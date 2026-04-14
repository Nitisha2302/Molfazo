<?php


namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\UserAddress;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\UserLang;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
   /**
     * REGISTER OR LOGIN + SEND OTP
     */
    public function login(Request $request)
    {

        $userLang = DB::table('user_langs')
        ->where('device_token', $request->device_id)
        ->where('device_type', $request->device_type)
        ->value('language');

        $lang = $request->header('lang')
            ?? $request->lang
            ?? $request->query('lang')
            ?? $userLang
            ?? 'ru';

        app()->setLocale($lang);
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|digits_between:8,15',
            'device_type'  => 'nullable|string|max:255',
            'device_id'    => 'nullable|string|max:255',
            'fcm_token'    => 'nullable|string|max:255',
        ], [
            'phone_number.required' => __('messages.customer.login.validation.phone_required'),
             'phone_number.digits_between' => __('messages.customer.login.validation.phone_invalid'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 201);
        }

        $phone = $request->phone_number;

        // ✅ Always random OTP
        $otp = rand(100000, 999999);

        // ✅ Optional: fixed OTP only in local env
        if (app()->environment('local') && env('TEST_OTP')) {
            $otp = env('TEST_OTP');
        }

        // 👤 Check user by mobile
        $user = User::where('mobile', $phone)->first();

        //  If user exists but NOT customer
        if ($user && $user->role != 3) {
            return response()->json([
                'status'  => false,
                 'message' => __('messages.customer.login.vendor_exists'),
            ], 403);
        }

        // 👤 Find or Create Customer (role = 3)
        $user = User::firstOrCreate(
            ['mobile' => $phone],
            [
                'role' => 3,
                'is_mobile_verified' => 0,
            ]
        );

        // 🚫 Block / Delete check
        if (!empty($user->is_blocked)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.customer.login.blocked'),
            ], 403);
        }

        if (!empty($user->is_deleted)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.customer.login.deleted'),

            ], 403);
        }

        // 🔄 Update OTP & device info
        $user->update([
            'mobile_otp'         => $otp,
            'mobile_otp_sent_at' => now(),
            'device_type'        => $request->device_type,
            'device_token'          => $request->device_id,
            'fcm_token'          => $request->fcm_token,
        ]);

        // 📩 Send SMS (real numbers only)
        // if (app()->environment('production')) {
            // $this->sendOsonSms($phone, $otp);
            $this->sendOsonSms($phone, $otp, app()->getLocale());
        // }

        return response()->json([
            'status' => true,
            'message' => __('messages.customer.login.otp_sent'),
            'data'   => [
                'user_id' => $user->id,
                'mobile'  => $user->mobile,
                'otp'     => app()->environment('local') ? $otp : null, // show only in local
            ],
        ], 200);
    }


       /**
     * OSON SMS
     */
    private function sendOsonSms($phone, $otp,$lang)
    {
        /* 🌐 Set language */
        app()->setLocale($lang);

        /* 🔐 ENV CONFIG */
        $login  = config('services.oson.login');
        $from   = config('services.oson.sender');
        $apiKey = config('services.oson.api_key');
        $txnId  = 'otp_' . time();

          $message = __('messages.customer.sms.otp', ['otp' => $otp]);

        $hashInput = "$txnId;$login;$from;$phone;$apiKey";
        $hash = hash('sha256', mb_convert_encoding($hashInput, 'UTF-8'));

        Http::get('https://api.osonsms.com/sendsms_v1.php', [
            'login'        => $login,
            'from'         => $from,
            'phone_number' => $phone,
            'msg'          =>  $message,
            'txn_id'       => $txnId,
            'str_hash'     => $hash,
        ]);

        Log::info('OTP sent via OSON SMS', ['phone' => $phone]);
    }


    /**
     * VERIFY OTP
     */
    public function verifyOtp(Request $request)
    {

         $userLang = DB::table('user_langs')
        ->where('device_token', $request->device_id)
        ->where('device_type', $request->device_type)
        ->value('language');

        $lang = $request->header('lang')
            ?? $request->lang
            ?? $request->query('lang')
            ?? $userLang
            ?? 'ru';

        app()->setLocale($lang);
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|digits_between:8,15',
            'otp'          => 'required|digits:6',
             'device_id'    => 'nullable|string|max:255',
            'device_type'  => 'nullable|string|max:255',
            'fcm_token'    => 'nullable|string|max:255',
        ], [
            'phone_number.required' => __('messages.customer.verify_otp.validation.phone_required'),
            'otp.required'          => __('messages.customer.verify_otp.validation.otp_required'),
            'otp.digits'            => __('messages.customer.verify_otp.validation.otp_digits'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=> false,
                'message'=> $validator->errors()->first(),
            ], 201);
        }

        $user = User::where('mobile', $request->phone_number)->first();

        if (!$user || $user->mobile_otp != $request->otp) {
            return response()->json([
                'status'=> false,
               'message' => __('messages.customer.verify_otp.invalid'),
            ], 401);
        }

        if (Carbon::parse($user->mobile_otp_sent_at)->addMinutes(5)->isPast()) {
            return response()->json([
                'status'=> false,
                'message' => __('messages.customer.verify_otp.expired'),
            ], 401);
        }

        // 🔐 Generate token
        $apiToken = bin2hex(random_bytes(40));

        $user->update([
            'api_token'          => $apiToken,
            'is_mobile_verified' => 1,
            'mobile_verified_at' => now(),
            'mobile_otp'         => null,
            'mobile_otp_sent_at' => null,
                'device_token'      => $request->device_id,
            'device_type'       => $request->device_type,
            'fcm_token'         => $request->fcm_token,
        ]);

        return response()->json([
            'status'=> true,
          'message' => __('messages.customer.verify_otp.success'),
            'data'=> [
                'id'        => $user->id,
                'role'      => $user->role,
                'mobile'    => $user->mobile,
                'api_token' => $user->api_token,
            ],
        ], 200);
    }

 

    public function updateProfile(Request $request)
    {
        // Get authenticated user
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }


         // 🌐 Language detect
        $userLang = UserLang::where('user_id', $user->id)
            ->where('device_token', $user->device_token)
            ->where('device_type', $user->device_type)
            ->first();

        $lang = $userLang ? $userLang->language : 'ru';
        app()->setLocale($lang);

        // Custom validation messages
       $messages = [
            'name.string' => __('messages.customer.update_profile.validation.name_string'),
            'name.max' => __('messages.customer.update_profile.validation.name_max'),
            'email.email' => __('messages.customer.update_profile.validation.email_invalid'),
            'email.unique' => __('messages.customer.update_profile.validation.email_unique'),
            'mobile.unique' => __('messages.customer.update_profile.validation.mobile_unique'),
            'alt_mobile.string' => __('messages.customer.update_profile.validation.alt_mobile_string'),
            'country.string' => __('messages.customer.update_profile.validation.country_string'),
            'city.string' => __('messages.customer.update_profile.validation.city_string'),
            'profile_photo.image' => __('messages.customer.update_profile.validation.photo_image'),
            'profile_photo.mimes' => __('messages.customer.update_profile.validation.photo_mimes'),
            'profile_photo.max' => __('messages.customer.update_profile.validation.photo_max'),
        ];

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|unique:users,mobile,' . $user->id,
            'alt_mobile' => 'nullable|string',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 201);
        }

        // Update user fields
        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->has('mobile')) $user->mobile = $request->mobile;
        if ($request->has('alt_mobile')) $user->alt_mobile = $request->alt_mobile;
        if ($request->has('country')) $user->country = $request->country;
        if ($request->has('city')) $user->city = $request->city;

        // Handle profile photo manually
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $profilePhotoName = time() . '_' . $file->getClientOriginalName();

            // Delete old photo if exists
            $oldPhotoPath = public_path('assets/profile_image/' . $user->profile_photo);
            if ($user->profile_photo && file_exists($oldPhotoPath)) {
                unlink($oldPhotoPath);
            }

            // Move new photo
            $file->move(public_path('assets/profile_image'), $profilePhotoName);

            // Store filename in DB
            $user->profile_photo = $profilePhotoName;
        }

        $user->save();

        return response()->json([
            'status' => true,
           'message' => __('messages.customer.update_profile.success'),
            'data' => $user
        ],200);
    }


    public function storeAddress(Request $request)
    {
        $user = Auth::guard('api')->user();

        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:50',
            'full_name' => 'required|string|max:100',
            'mobile'    => 'required|digits:10',
            'address'   => 'required|string',
            'city'      => 'required|string',
            'state'     => 'nullable|string',
            'pincode'   => 'nullable|digits:6',
        ], [
            'name.required'      => 'Address type is required (Home / Office).',
            'full_name.required' => 'Full name is required.',
            'mobile.required'    => 'Mobile number is required.',
            'mobile.digits'      => 'Mobile number must be 10 digits.',
            'address.required'   => 'Address field cannot be empty.',
            'city.required'      => 'City is required.',
            'state.required'     => 'State is required.',
            'pincode.required'   => 'Pincode is required.',
            'pincode.digits'     => 'Pincode must be 6 digits.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->first(),
            ], 201);
        }

        // Default address logic
        if ($request->is_default == 1) {
            UserAddress::where('user_id', $user->id)->update(['is_default' => 0]);
        }

        $address = UserAddress::create([
            'user_id'    => $user->id,
            'name'       => $request->name,
            'full_name'  => $request->full_name,
            'mobile'     => $request->mobile,
            'address'    => $request->address,
            'city'       => $request->city,
             'state'      => $request->state ?? null, // save null if not provided
            'pincode'    => $request->pincode ?? null, // save null if not provided
            'is_default' => $request->is_default ?? 0,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Address saved successfully.',
            'data'    => $address,
        ]);
    }


    public function addressList()
    {
        $user = Auth::guard('api')->user();

        $addresses = UserAddress::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $addresses,
        ]);
    }


    public function destroyAddress($id)
    {
        $user = Auth::guard('api')->user();

        $address = UserAddress::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json([
                'status'  => false,
                'message' => 'Address not found.',
            ], 404);
        }

        // If deleting default address → set another as default
        if ($address->is_default == 1) {
            $newDefault = UserAddress::where('user_id', $user->id)
                ->where('id', '!=', $id)
                ->first();

            if ($newDefault) {
                $newDefault->update(['is_default' => 1]);
            }
        }

        $address->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Address removed successfully.',
        ]);
    }


    public function setDefaultAddress(Request $request)
    {
        $user = Auth::guard('api')->user();

        $validator = Validator::make(
            $request->all(),
            [
                'address_id' => 'required|exists:user_addresses,id',
            ],
            [
                'address_id.required' => 'Please select an address.',
                'address_id.exists'   => 'Selected address does not exist.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 201);
        }

        // Check address ownership
        $address = UserAddress::where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json([
                'status'  => false,
                'message' => 'This address does not belong to your account.',
            ], 403);
        }

        // Already default check
        if ($address->is_default == 1) {
            return response()->json([
                'status'  => false,
                'message' => 'This address is already set as default.',
            ], 200);
        }

        // Update default address (safe way)
        \DB::transaction(function () use ($user, $address) {
            UserAddress::where('user_id', $user->id)
                ->update(['is_default' => 0]);

            $address->update(['is_default' => 1]);
        });

        return response()->json([
            'status'  => true,
            'message' => 'Default address updated successfully.',
            'data'    => $address,
        ], 200);
    }





}
