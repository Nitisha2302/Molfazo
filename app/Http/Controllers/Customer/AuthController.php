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

class AuthController extends Controller
{
   /**
     * REGISTER OR LOGIN + SEND OTP
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|digits_between:8,15',
            'device_type'  => 'nullable|string|max:255',
            'device_id'    => 'nullable|string|max:255',
            'fcm_token'    => 'nullable|string|max:255',
        ], [
            'phone_number.required' => 'Mobile number is required.',
            'phone_number.digits_between' => 'Invalid mobile number.',
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
                'message' => 'This number is already register with us as a Vendor.',
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
                'message'=> 'Your account is blocked.',
            ], 403);
        }

        if (!empty($user->is_deleted)) {
            return response()->json([
                'status' => false,
                'message'=> 'Your account is deleted.',
            ], 403);
        }

        // 🔄 Update OTP & device info
        $user->update([
            'mobile_otp'         => $otp,
            'mobile_otp_sent_at' => now(),
            'device_type'        => $request->device_type,
            'device_id'          => $request->device_id,
            'fcm_token'          => $request->fcm_token,
        ]);

        // 📩 Send SMS (real numbers only)
        if (app()->environment('production')) {
            $this->sendOsonSms($phone, $otp);
        }

        return response()->json([
            'status' => true,
            'message'=> 'OTP sent successfully.',
            'data'   => [
                'user_id' => $user->id,
                'mobile'  => $user->mobile,
                'otp'     => app()->environment('local') ? $otp : null, // show only in local
            ],
        ], 200);
    }


    /**
     * VERIFY OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|digits_between:8,15',
            'otp'          => 'required|digits:6',
        ], [
            'phone_number.required' => 'Mobile number is required.',
            'otp.required'          => 'OTP is required.',
            'otp.digits'            => 'Invalid OTP.',
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
                'message'=> 'Invalid OTP.',
            ], 401);
        }

        if (Carbon::parse($user->mobile_otp_sent_at)->addMinutes(5)->isPast()) {
            return response()->json([
                'status'=> false,
                'message'=> 'OTP expired.',
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
        ]);

        return response()->json([
            'status'=> true,
            'message'=> 'OTP verified successfully.',
            'data'=> [
                'id'        => $user->id,
                'role'      => $user->role,
                'mobile'    => $user->mobile,
                'api_token' => $user->api_token,
            ],
        ], 200);
    }

    /**
     * OSON SMS
     */
    private function sendOsonSms($phone, $otp)
    {
        $login  = 'borafzo';
        $from   = 'BORAFZO';
        $apiKey = 'c3cdbb3f1171320d49f2bf1da20f53fc';
        $txnId  = 'otp_' . time();

        $hashInput = "$txnId;$login;$from;$phone;$apiKey";
        $hash = hash('sha256', mb_convert_encoding($hashInput, 'UTF-8'));

        Http::get('https://api.osonsms.com/sendsms_v1.php', [
            'login'        => $login,
            'from'         => $from,
            'phone_number' => $phone,
            'msg'          => "Your OTP is {$otp}",
            'txn_id'       => $txnId,
            'str_hash'     => $hash,
        ]);

        Log::info('OTP sent via OSON SMS', ['phone' => $phone]);
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

        // Custom validation messages
        $messages = [
            'name.string' => 'Name must be a valid string.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already taken.',
            'alt_mobile.string' => 'Alternate mobile must be a valid string.',
            'country.string' => 'Country must be a valid string.',
            'city.string' => 'City must be a valid string.',
            'profile_photo.image' => 'Profile photo must be an image file.',
            'profile_photo.mimes' => 'Profile photo must be jpeg, png, jpg, or gif.',
            'profile_photo.max' => 'Profile photo cannot exceed 2MB.',
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
            'message' => 'Profile updated successfully',
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
            'state'     => 'required|string',
            'pincode'   => 'required|digits:6',
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
            'state'      => $request->state,
            'pincode'    => $request->pincode,
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
