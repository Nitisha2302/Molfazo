<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Mail\OTPVerificationMail;
use Illuminate\Validation\ValidationException;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Str;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\UserLang;
use Illuminate\Support\Facades\DB;




class AuthController extends Controller
{

    public function sendMobileOtp(Request $request)
    {

           $userLang = DB::table('user_langs')
            ->where('device_token', $request->device_token)
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
            'device_token' => 'nullable|string|max:255',
            'fcm_token'    => 'nullable|string|max:255',
        ], [
           'phone_number.required' => __('messages.vendor.send_otp.validation.mobile_required'),
           'phone_number.digits_between' => __('messages.vendor.send_otp.validation.mobile_invalid'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 201);
        }

        $phone = $request->phone_number;

        /** 🔴 CHECK IF MOBILE ALREADY EXISTS */
        $existingUser = User::where('mobile', $phone)->first();

        if ($existingUser) {
            return response()->json([
                'status' => false,
                  'message' => __('messages.vendor.send_otp.mobile_exists'),
            ], 201);
        }

        /** ✅ GENERATE OTP */
        $otp = rand(100000, 999999);

        /** ✅ CREATE NEW USER */
        $user = User::create([
            'mobile' => $phone,
            'is_mobile_verified' => 0,
            'device_type' => $request->device_type,
            'device_token' => $request->device_token,
            'fcm_token' => $request->fcm_token,
            'mobile_otp' => $otp,
            'role' => 2,
            'mobile_otp_sent_at' => now(),
        ]);
        $smsMessage = __('messages.vendor.send_otp.sms.otp_message', ['otp' => $otp]);
         $responseMessage = __('messages.vendor.send_otp.otp_sent');
        $responseOtp = null;

        

        /** 🌍 OSON SMS (9-digit numbers) */
        if (strlen($phone) === 9) {
            $txnId = 'otp_' . time();
            $login  = config('services.oson.login');
            $from   = config('services.oson.sender');
            $apiKey = config('services.oson.api_key');

            $hash = $this->generateSha256Hex(
                "$txnId;$login;$from;$phone;$apiKey"
            );
            // $hash = $this->generateSha256Hex(
            //     "borafzo;BORAFZO;{$phone};c3cdbb3f1171320d49f2bf1da20f53fc;{$txnId}"
            // );

            Http::get('https://api.osonsms.com/sendsms_v1.php', [
                'login' => $login,
                'from'  => $from,
                'phone_number' => $phone,
                'msg'   => $smsMessage,
                'txn_id' => $txnId,
                'str_hash' => $hash,
            ]);
        }

        /** 🇮🇳 INDIA TEST MODE */
        if (strlen($phone) === 10) {
              $responseMessage = __('messages.vendor.send_otp.otp_test');
            $responseOtp = $otp;
        }

        return response()->json([
            'status' => true,
            'message' => $responseMessage,
            'data' => [
                'phone_number' => $phone,
                'otp' => $responseOtp,
            ]
        ], 200);
    }


    public function sendEmailOtp(Request $request)
    {
         /* ===============================
        AUTHENTICATED USER
        =============================== */
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User is not authenticated.',
            ], 401);
        }
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
             'device_type'  => 'nullable|string|max:255',
            'device_token'    => 'nullable|string|max:255',
            'fcm_token'    => 'nullable|string|max:255',
        ], [
            'email.required' => 'Email is required.',
            'email.unique' => 'Email already exists.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }


        $otp = rand(100000, 999999);

        $user->email = $request->email;
        $user->device_type = $request->device_type;
        $user->device_token = $request->device_token;
        $user->fcm_token = $request->fcm_token;
        $user->otp = $otp;
        $user->otp_sent_at = now();
        $user->save();

      /* ===============================
        SEND EMAIL
        =============================== */
        $logoPath = url('/') . '/assets/email-logo/logo_molfazo.png';

        Mail::to($user->email)->send(
            new OTPVerificationMail(
                $user->name ?? '',
                $otp,
                $logoPath
            )
        );

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to your email.',
        ], 200);
    }


    public function verifyOtp(Request $request)
    {


            $userLang = DB::table('user_langs')
            ->where('device_token', $request->device_token)
            ->where('device_type', $request->device_type)
            ->value('language');

            $lang = $request->header('lang')
                ?? $request->lang
                ?? $request->query('lang')
                ?? $userLang
                ?? 'ru';

            app()->setLocale($lang);

        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6',
            'phone_number' => 'nullable|digits_between:8,15',
            'email' => 'nullable|email',
            'device_token'    => 'nullable|string|max:255',
            'device_type'  => 'nullable|string|max:255',
            'fcm_token'    => 'nullable|string|max:255',
        ], [
            'otp.required' => __('messages.vendor.verify_otp.validation.otp_required'),
            'otp.digits'   => __('messages.vendor.verify_otp.validation.otp_digits'),
            'phone_number.digits_between' => __('messages.vendor.vendor.verify_otp.validation.mobile_invalid'),
            'email.email'  => __('messages.verify_otp.validation.email_invalid'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if ($request->phone_number) {
            $user = User::where('mobile', $request->phone_number)->first();
            $otpCol = 'mobile_otp';
            $timeCol = 'mobile_otp_sent_at';
            $verifyCol = 'is_mobile_verified';
            $verifyAt = 'mobile_verified_at';
        } else {
            $user = User::where('email', $request->email)->first();
            $otpCol = 'otp';
            $timeCol = 'otp_sent_at';
            $verifyCol = 'email_verified';
            $verifyAt = 'email_verified_at';
        }

        if (!$user || $user->$otpCol != $request->otp) {
            return response()->json([
                'status' => false,
                'message' => __('messages.vendor.verify_otp.invalid_otp'),
            ], 401);
        }

        if (Carbon::parse($user->$timeCol)->addMinutes(5)->isPast()) {
            return response()->json([
                'status' => false,
                'message' => __('messages.vendor.verify_otp.otp_expired'),
            ], 401);
        }

        if (!$user->api_token) {
            $user->api_token = bin2hex(random_bytes(40));
        }

        $user->$verifyCol = 1;
        $user->$verifyAt = now();
        $user->$otpCol = null;
        $user->$timeCol = null;
        $user->device_token = $request->device_token;
        $user->device_type  = $request->device_type;
        $user->fcm_token    = $request->fcm_token;
        $user->role = 2;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => __('messages.vendor.verify_otp.otp_verified'),
            'api_token' => $user->api_token,
        ], 200);
        
    }
   
    //  Generate SHA-256 hash for OsonSMS

    private function generateSha256Hex(string $input): string
    {
        $utf8String = mb_convert_encoding($input, 'UTF-8');
        return hash('sha256', $utf8String);
    }

    public function vendorCompleteProfile(Request $request)
    {
        /* ===============================
        AUTHENTICATED USER
        =============================== */
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.vendor.completeprofile.user_not_authenticated'),
            ], 401);
        }


         // 🌐 Language detect
        // $userLang = UserLang::where('user_id', $user->id)
        //     ->where('device_token', $user->device_token)
        //     ->where('device_type', $user->device_type)
        //     ->first();

        // $lang = $userLang->language ?? 'ru';
        // app()->setLocale($lang);

         /* ===============================
        VERIFICATION CHECK
        =============================== */
        if (!$user->is_mobile_verified ) {
            return response()->json([
                'status'  => false,
               'message' => __('messages.vendor.completeprofile.mobile_not_verified'),
            ], 403);
        }

        /* ===============================
        VALIDATION
        =============================== */
        $rules = [
            'name'            => 'nullable|string|max:255',
            'email'           => 'nullable|email|unique:users,email',
            'password'        => 'nullable|min:6|confirmed',

            'city'            => 'nullable|string',
            'country'         => 'nullable|string',
            'terms_accepted'  => 'nullable|in:1',

            'profile_photo'   => 'nullable|image|mimes:jpg,png',
            'alt_mobile'      => 'nullable|digits_between:8,15',

            // Government ID fields (OPTIONAL)
            'gov_id_type'     => 'nullable|string',
            'gov_id_number'   => 'nullable|string',
            'gov_id_document' => 'nullable|array',
            'gov_id_document.*' => 'file|mimes:jpg,png,pdf',

            'device_id'       => 'nullable|string',
            'device_type'     => 'nullable|string',
            'fcm_token'       => 'nullable|string',
        ];

        $messages = [
            'name.required'      => __('messages.vendor.completeprofile.validation.name_required'),
            'email.required'     => __('messages.vendor.completeprofile.validation.email_required'),
            'email.unique'       => __('messages.vendor.completeprofile.validation.email_unique'),
            'mobile.required'    => __('messages.vendor.completeprofile.validation.mobile_required'),
            'mobile.unique'      => __('messages.vendor.completeprofile.validation.mobile_unique'),
            'password.confirmed' => __('messages.vendor.completeprofile.validation.password_confirmed'),
            'terms_accepted.in'  => __('messages.vendor.completeprofile.validation.terms_required'),
        ];
        

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 201);
        }

        /* ===============================
        HANDLE FILE UPLOADS
        =============================== */
        
        // ---- Multiple Government IDs ----
       $uploadedGovIds = [];
        if ($request->hasFile('gov_id_document')) {
            foreach ($request->file('gov_id_document') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/gov_id_document'), $filename);
                $uploadedGovIds[] = $filename;
            }
        }
        $govDocJson = json_encode($uploadedGovIds);



        // ---- Profile Photo (Optional) ----
        $profilePhotoName = null;
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $profilePhotoName = time() . '_' . $file->getClientOriginalName();
            
            // Move file to folder
            $file->move(public_path('assets/profile_image'), $profilePhotoName);
            
            // Store only filename in DB
        }


        /* ===============================
        CREATE USER
        =============================== */
        $user->update([
            // 'name'             => $request->name,
            'email'            => $request->email,
            'alt_mobile'       => $request->alt_mobile,
            'password'         => Hash::make($request->password),

            'role'          => 2, // Vendor
            'status_id'        => 2, // Pending admin approval

            'gov_id_type'      => $request->gov_id_type,
            'gov_id_number'    => $request->gov_id_number,
            // 'government_id'    => $govDocJson, // multiple files stored

            // NEW
            //    'kyc_status' => 'pending',

            'city'             => $request->city,
            'country'          => $request->country,
            // 'profile_photo'    => $profilePhotoName,
            'terms_accepted'   => true,

            'device_id'        => $request->device_id,
            'device_type'      => $request->device_type,
            'fcm_token'        => $request->fcm_token,
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.vendor.completeprofile.register_success'),
            'data'    => $user,
        ], 200);
    }

    public function vendorLogin(Request $request)
    {
           $userLang = DB::table('user_langs')
            ->where('device_token', $request->device_token)
            ->where('device_type', $request->device_type)
            ->value('language');

            $lang = $request->header('lang')
                ?? $request->lang
                ?? $request->query('lang')
                ?? $userLang
                ?? 'ru';

            app()->setLocale($lang);
        /* ===============================
        VALIDATION
        =============================== */
        $validator = Validator::make($request->all(), [
            'login'        => 'required|string',
            'password'     => 'required|string',
            'device_token' => 'nullable|string',
            'device_type'  => 'nullable|string',
            'fcm_token'    => 'nullable|string',
        ], [
            'login.required'    => __('messages.vendor.login.validation.login_required'),
            'password.required' => __('messages.vendor.login.validation.password_required'),
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        /* ===============================
        USER CHECK (EMAIL OR MOBILE)
        =============================== */
        $loginValue = $request->login;

        $user = User::where('email', $loginValue)
                    ->orWhere('mobile', $loginValue)
                    ->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.vendor.login.account_not_found'),
            ], 404);
        }

        /* ===============================
        PASSWORD CHECK
        =============================== */
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                 'message' => __('messages.vendor.login.invalid_credentials'),
            ], 401);
        }

        /* ===============================
        VENDOR ROLE CHECK
        =============================== */
        if ($user->role != 2) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.vendor.login.only_vendor'),
            ], 403);
        }

        /* ===============================
        VENDOR APPROVAL CHECK
        =============================== */
        /*if ($user->status_id == 2) {
            return response()->json([
                'status'  => false,
                'message' => 'Your vendor account is pending admin approval.',
            ], 403);
        }*/

        if ($user->status_id == 3) {
            return response()->json([
                'status'  => false,
               'message' => __('messages.vendor.login.rejected'),
            ], 403);
        }

        if ($user->status_id == 4) {
            return response()->json([
                'status'  => false,
                 'message' => __('messages.vendor.login.blocked'),
            ], 403);
        }

        /* ===============================
        LOGIN SUCCESS
        =============================== */
        $apiToken = bin2hex(random_bytes(40));

        $user->update([
            'api_token'    => $apiToken,
            'device_token' => $request->device_token,
            'device_type'  => $request->device_type,
            'fcm_token'    => $request->fcm_token,
        ]);

        return response()->json([
            'status'  => true,
             'message' => __('messages.vendor.login.login_success'),
            'data'    => [
                'id'        => $user->id,
                'role'      => $user->role,
                'email'     => $user->email,
                'mobile'    => $user->mobile,
                'api_token' => $user->api_token,
            ],
        ], 200);
    }

    public function sendVendorLoginOtp(Request $request)
    {

            $userLang = DB::table('user_langs')
            ->where('device_token', $request->device_token)
            ->where('device_type', $request->device_type)
            ->value('language');

            $lang = $request->header('lang')
                ?? $request->lang
                ?? $request->query('lang')
                ?? $userLang
                ?? 'ru';

            app()->setLocale($lang);
       /* ===============================
        VALIDATION
        =============================== */
          $validator = Validator::make($request->all(), [
            'phone_number' => 'required|digits_between:8,15',
            'device_type'  => 'nullable|string|max:255',
            'device_token' => 'nullable|string|max:255',
            'fcm_token'    => 'nullable|string|max:255',
        ], [
            'phone_number.required'       => __('messages.vendor.login_otp.validation.mobile_required'),
            'phone_number.digits_between' => __('messages.vendor.login_otp.validation.mobile_invalid'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(), // Return first error
                'errors'  => $validator->errors(),         // Optional: full list of errors
            ], 201);
        }


        /* ===============================
        USER CHECK
        =============================== */
        $user = User::where('mobile', $request->phone_number)->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                   'message' => __('messages.vendor.login_otp.account_not_found'),
            ], 404);
        }

        if ($user->role != 2) {
            return response()->json([
                'status'  => false,
                 'message' => __('messages.vendor.login_otp.only_vendor'),
            ], 403);
        }

        if ($user->status_id != 1) {
            return response()->json([
                'status'  => false,
              'message' => __('messages.vendor.login_otp.not_active'),
            ], 403);
        }

        /* ===============================
        GENERATE OTP
        =============================== */
        $otp = rand(100000, 999999);

        $user->mobile_otp = $otp;
        $user->mobile_otp_sent_at = now();
        $user->save();

        /* ===============================
        SEND OTP
        =============================== */
        $phone = $request->phone_number;
        // ✅ Dynamic SMS
       $smsMessage = __('messages.vendor.login_otp.sms.otp_message', ['otp' => $otp]);
       $responseMessage = __('messages.vendor.login_otp.otp_sent');
        $responseOtp = null;

        if (strlen($phone) === 9) {
            // OSON SMS
            $txnId = 'login_' . time();
            $login  = config('services.oson.login');
            $from   = config('services.oson.sender');
            $apiKey = config('services.oson.api_key');

            $hash = $this->generateSha256Hex(
                "$txnId;$login;$from;$phone;$apiKey"
            );
            // $hash = $this->generateSha256Hex(
            //     "borafzo;BORAFZO;{$phone};c3cdbb3f1171320d49f2bf1da20f53fc;{$txnId}"
            // );

            Http::get('https://api.osonsms.com/sendsms_v1.php', [
                'login'        =>  $login,
                'from'         => $from,
                'phone_number' => $phone,
                'msg'          => $smsMessage,
                'txn_id'       => $txnId,
                'str_hash'     => $hash,
            ]);
        }

        if (strlen($phone) === 10) {
            // India testing
           $responseMessage = __('messages.vendor.login_otp.otp_test');
            $responseOtp = $otp;
        }

        return response()->json([
            'status'  => true,
            'message' => $responseMessage,
            'data'    => [
                'phone_number' => $phone,
                'otp' => $responseOtp,
            ],
        ], 200);
    }

    public function verifyLoginOtp(Request $request)
    {

            $userLang = DB::table('user_langs')
            ->where('device_token', $request->device_token)
            ->where('device_type', $request->device_type)
            ->value('language');

            $lang = $request->header('lang')
                ?? $request->lang
                ?? $request->query('lang')
                ?? $userLang
                ?? 'ru';

            app()->setLocale($lang);
        /* ===============================
        VALIDATION
        =============================== */
          $validator = Validator::make($request->all(), [
            'phone_number' => 'required|digits_between:8,15',
            'otp'          => 'required|digits:6',
            'device_type'  => 'nullable|string|max:255',
            'device_token' => 'nullable|string|max:255',
            'fcm_token'    => 'nullable|string|max:255',
        ], [
            'phone_number.required'       => __('messages.vendor.verify_login_otp.validation.mobile_required'),
            'phone_number.digits_between' => __('messages.vendor.verify_login_otp.validation.mobile_invalid'),
            'otp.required'                => __('messages.vendor.verify_login_otp.validation.otp_required'),
            'otp.digits'                  => __('messages.vendor.verify_login_otp.validation.otp_digits'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        /* ===============================
        FIND USER
        =============================== */
        $user = User::where('mobile', $request->phone_number)->first();

        if (!$user || $user->mobile_otp != $request->otp) {
            return response()->json([
                'status'  => false,
              'message' => __('messages.vendor.verify_login_otp.invalid'),
            ], 401);
        }

        /* ===============================
        OTP EXPIRY (5 MIN)
        =============================== */
        if (Carbon::parse($user->mobile_otp_sent_at)->addMinutes(5)->isPast()) {
            return response()->json([
                'status'  => false,
                 'message' => __('messages.vendor.verify_login_otp.expired'),
            ], 401);
        }

        /* ===============================
        LOGIN SUCCESS
        =============================== */
        $apiToken = bin2hex(random_bytes(40));

        $user->update([
            'api_token'           => $apiToken,
            'is_mobile_verified'  => 1,
            'mobile_verified_at'  => now(),
            'mobile_otp'          => null,
            'mobile_otp_sent_at'  => null,
            'device_type'         => $request->device_type,
            'device_token'        => $request->device_token,
            'fcm_token'           => $request->fcm_token,
            'last_login_at'       => now(),
        ]);

        return response()->json([
            'status'  => true,
          'message' => __('messages.vendor.verify_login_otp.login_success'),
            'data'    => [
                'id'        => $user->id,
                'role'      => $user->role,
                'mobile'    => $user->mobile,
                'api_token' => $user->api_token,
            ],
        ], 200);
    }


    public function getProfile(Request $request)
    {
        /* ===============================
        AUTHENTICATED USER
        =============================== */
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status'  => false,
              'message' => __('messages.getProfile.user_not_authenticated'),
            ], 401);
        }

        // 🌐 Language detect from DB (logged-in user)
        // $userLang = UserLang::where('user_id', $user->id)->first();
        // $lang = $userLang->language ?? 'ru';
        // app()->setLocale($lang);

        /* ===============================
        FORMAT OPTIONAL DATA
        =============================== */
        $profilePhoto = $user->profile_photo
            ?  $user->profile_photo
            : null;

        $govDocuments = $user->government_id
            ? json_decode($user->government_id, true)
            : [];

        /* ===============================
        RESPONSE
        =============================== */
        return response()->json([
            'status'  => true,
            'message' =>  __('messages.getProfile.success'),
            'data'    => [
                'id' => $user->id,

                /* Role & Status */
                'role'       => $user->role,        // 1=Admin,2=Vendor,3=Customer
                'status_id'  => $user->status_id,   // 1=Active,2=Pending,3=Rejected,4=Blocked

                /* Basic Info */
                'name'       => $user->name,
                'email'      => $user->email,
                'mobile'     => $user->mobile,
                'alt_mobile' => $user->alt_mobile,

                /* Location */
                'country' => $user->country,
                'city'    => $user->city,

                /* Profile */
                'profile_photo' => $profilePhoto,

                /* Government Documents */
                'gov_id_type'   => $user->gov_id_type,
                'gov_id_number' => $user->gov_id_number,
                'government_id_documents' => $govDocuments,

                /* Approval */
                'approved_at' => $user->approved_at,

                /* Verification Flags (no OTP logic) */
                'email_verified'        => $user->email_verified,
                'email_verified_at'     => $user->email_verified_at,
                'is_mobile_verified'    => $user->is_mobile_verified,
                'mobile_verified_at'    => $user->mobile_verified_at,

                /* Flags */
                'terms_accepted' => $user->terms_accepted,
                'is_social'      => $user->is_social,

                'kyc_status'      => $user->kyc_status,

                /* Device */
                'device_type' => $user->device_type,
                'fcm_token'   => $user->fcm_token,

                /* Meta */
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ], 200);
    }


    public function logout(Request $request)
    {
        /* ===============================
        AUTHENTICATED USER
        =============================== */
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                 'message' => __('messages.logout.user_not_authenticated'),
            ], 401);
        }


        //  Language detect from DB (logged-in user)
        // $userLang = UserLang::where('user_id', $user->id)->first();
        // $lang = $userLang->language ?? 'ru';
        // app()->setLocale($lang);
        /* ===============================
        LOGOUT
        =============================== */
        $user->update([
            'api_token'    => null,
            'device_token' => null,
            'device_type'  => null,
            'fcm_token'    => null,
        ]);

        return response()->json([
            'status'  => true,
           'message' =>  __('messages.logout.logout_success'),
        ], 200);
    }


    public function forgotPassword(Request $request)
    {
        $userLang = DB::table('user_langs')
        ->where('device_token', $request->device_token)
        ->where('device_type', $request->device_type)
        ->value('language');

        $lang = $request->header('lang')
            ?? $request->lang
            ?? $request->query('lang')
            ?? $userLang
            ?? 'ru';

        app()->setLocale($lang);

        /* ===============================
        VALIDATION
        =============================== */
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
                'device_token'    => 'nullable|string|max:255',
            'device_type'  => 'nullable|string|max:255',
            'fcm_token'    => 'nullable|string|max:255',
        ], [
            'email.required' => __('messages.vendor.forgot_password.validation.email_required'),
            'email.email'    => __('messages.vendor.forgot_password.validation.email_invalid'),
            'email.exists'   => __('messages.vendor.forgot_password.validation.email_exists'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 201); // ✅ SAME AS vendorRegister
        }

        $user = User::where('email', $request->email)->first();

        $newPassword = rand(100000, 999999);

        $user->forgot_password_new = Hash::make($newPassword);
        $user->forgot_password_sent_at = now();
       $user->device_token = $request->device_token;
        $user->device_type  = $request->device_type;
        $user->fcm_token    = $request->fcm_token;
        $user->save();

        try {
            $logoPath = url('/') . "/assets/email-logo/logo_molfazo.png";

            Mail::to($user->email)->send(
                new ForgotPasswordMail(
                    $user->name ?? '',
                    $newPassword,
                    $logoPath
                )
            );

            return response()->json([
                'status'  => true,
                 'message' => __('messages.vendor.forgot_password.success'),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.vendor.forgot_password.email_failed'),
            ], 500);
        }
    }


    public function resetForgotPassword(Request $request)
    {

        $userLang = DB::table('user_langs')
        ->where('device_token', $request->device_token)
        ->where('device_type', $request->device_type)
        ->value('language');

        $lang = $request->header('lang')
            ?? $request->lang
            ?? $request->query('lang')
            ?? $userLang
            ?? 'ru';

        app()->setLocale($lang);
        $rules = [
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|digits:6|confirmed',
            'device_token'    => 'nullable|string|max:255',
            'device_type'  => 'nullable|string|max:255',
            'fcm_token'    => 'nullable|string|max:255',
        ];

        $messages = [
            'email.required'     => __('messages.vendor.reset_password.validation.email_required'),
            'email.email'        => __('messages.vendor.reset_password.validation.email_invalid'),
            'email.exists'       => __('messages.vendor.reset_password.validation.email_exists'),

            'password.required'  => __('messages.vendor.reset_password.validation.password_required'),
            'password.digits'    => __('messages.vendor.reset_password.validation.password_digits'),
            'password.confirmed' => __('messages.vendor.reset_password.validation.password_confirmed'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 201); // ✅ SAME STYLE
        }

        $user = User::where('email', $request->email)->first();

        if (
            !$user->forgot_password_new ||
            !Hash::check($request->password, $user->forgot_password_new)
        ) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.vendor.reset_password.invalid_password'),
            ], 201);
        }

        if (Carbon::parse($user->forgot_password_sent_at)->addMinutes(10)->isPast()) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.vendor.reset_password.expired'),
            ], 201);
        }

        $user->password = Hash::make($request->password);
        $user->forgot_password_new = null;
        $user->forgot_password_sent_at = null;
         $user->device_token = $request->device_token;
        $user->device_type  = $request->device_type;
        $user->fcm_token    = $request->fcm_token;
        $user->save();

        return response()->json([
            'status'  => true,
             'message' => __('messages.vendor.reset_password.success'),
        ], 200);
    }

  
    public function VendorloginWithApple(Request $request)
    {

        // 🌐 Language detect (default = ru)
        // $lang = $request->header('lang') 
        //     ?? $request->lang 
        //     ?? $request->query('lang') 
        //     ?? 'ru';

        // app()->setLocale($lang);
        /* ===============================
           VALIDATION
        =============================== */
        $validator = Validator::make($request->all(), [
            'identity_token' => 'required',
            'apple_id'       => 'required',
            'email'          => 'nullable|email',
            'name'           => 'nullable|string',
        ], [
            'identity_token.required' => __('messages.vendor.apple_login.validation.identity_token_required'),
            'apple_id.required'       => __('messages.vendor.apple_login.validation.apple_id_required'),
            'email.email'             => __('messages.vendor.apple_login.validation.email_invalid'),
            'name.string'             => __('messages.vendor.apple_login.validation.name_string'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {

            /* ===============================
               VERIFY APPLE TOKEN
            =============================== */
            $appleKeys = Http::get('https://appleid.apple.com/auth/keys')->json();

            $decoded = JWT::decode(
                $request->identity_token,
                JWK::parseKeySet($appleKeys),
                ['RS256']
            );

            if ($decoded->iss !== 'https://appleid.apple.com') {
                return response()->json([
                    'status' => false,
                 'message' => __('messages.vendor.apple_login.invalid_token'),
                ], 401);
            }

            /* ===============================
               FIND OR CREATE USER
            =============================== */
            $user = User::where('apple_token', $request->apple_id)->first();

            if (!$user) {

                // match by email (first login only)
                if ($request->email) {
                    $user = User::where('email', $request->email)->first();
                }

                if (!$user) {
                    $user = User::create([
                        'name'        => $request->name ?? 'Apple User',
                        'email'       => $request->email,
                        'password'    => Hash::make(Str::random(32)),
                        'apple_token' => $request->apple_id,
                        'is_social'   => 1,
                        'status_id'   => 1,
                        'role'        => 3,
                    ]);
                } else {
                    $user->update([
                        'apple_token' => $request->apple_id,
                        'is_social'   => 1,
                    ]);
                }
            }

            /* ===============================
               STATUS CHECK
            =============================== */
            if ($user->status_id == 4) {
                return response()->json([
                    'status' => false,
                   'message' => __('messages.vendor.apple_login.blocked'),
                ], 403);
            }

            /* ===============================
               ISSUE BEARER TOKEN
            =============================== */
            $token = $user->createToken('apple-login')->plainTextToken;

            return response()->json([
                'status' => true,
                'message'    => __('messages.vendor.apple_login.success'),
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('messages.vendor.apple_login.failed'),
            ], 401);
        }
    }


    public function updateLanguage(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $request->validate([
            'language'     => 'required',
            'device_token' => 'required|string',
            'device_type'  => 'required|string',
        ]);

        // ✅ Save in users table (your structure)
        $user->update([
            'device_token' => $request->device_token,
            'device_type'  => $request->device_type,
        ]);

        // ✅ Save language
        UserLang::updateOrCreate(
            [
                'user_id'      => $user->id,
                'device_token' => $request->device_token,
                'device_type'  => $request->device_type,
            ],
            [
                'language' => $request->language,
            ]
        );

        app()->setLocale($request->language);

        return response()->json([
            'status' => true,
            'message' => __('messages.language.updated'),
            'data' => ['language' => $request->language],
        ]);
    }





    public function getLanguage()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $userLang = UserLang::where('user_id', $user->id)
            ->where('device_token', $user->device_token)
            ->where('device_type', $user->device_type)
            ->first();

        return response()->json([
            'status' => true,
            'message' => 'Language fetched successfully',
            'data' => [
                'language' => $userLang->language ?? 'ru'
            ]
        ]);
    }














}
