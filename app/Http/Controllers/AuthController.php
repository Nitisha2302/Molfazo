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




class AuthController extends Controller
{

    public function sendMobileOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|digits_between:8,15',
            'device_type'  => 'nullable|string|max:255',
            'device_token'    => 'nullable|string|max:255',
            'fcm_token'    => 'nullable|string|max:255',
        ], [
            'phone_number.required' => 'Mobile number is required.',
            'phone_number.digits_between' => 'Invalid mobile number.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 201);
        }

        $otp = rand(100000, 999999);

        $user = User::updateOrCreate(
            ['mobile' => $request->phone_number],
            [
                'is_mobile_verified' => 0,
                'device_type' => $request->device_type,
                'device_token'   => $request->device_token,
                'fcm_token'   => $request->fcm_token,
            ]
        );

        $user->mobile_otp = $otp;
        $user->mobile_otp_sent_at = now();
        $user->save();

        $phone = $request->phone_number;
        $responseMessage = 'OTP sent to your mobile number.';
        $responseOtp = null;

        if (strlen($phone) === 9) {
            // OSON SMS
            $txnId = 'otp_' . time();
            $hash = $this->generateSha256Hex(
                "borafzo;BORAFZO;{$phone};c3cdbb3f1171320d49f2bf1da20f53fc;{$txnId}"
            );

            Http::get('https://api.osonsms.com/sendsms_v1.php', [
                'login' => 'borafzo',
                'from'  => 'BORAFZO',
                'phone_number' => $phone,
                'msg'   => "Your verification code: {$otp}",
                'txn_id' => $txnId,
                'str_hash' => $hash,
            ]);
        }

        if (strlen($phone) === 10) {
            // India testing
            $responseMessage = 'Auto OTP generated (testing mode)';
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
        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6',
            'phone_number' => 'nullable|digits_between:8,15',
            'email' => 'nullable|email',
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
                'message' => 'Invalid OTP.',
            ], 401);
        }

        if (Carbon::parse($user->$timeCol)->addMinutes(5)->isPast()) {
            return response()->json([
                'status' => false,
                'message' => 'OTP expired.',
            ], 401);
        }

        if (!$user->api_token) {
            $user->api_token = bin2hex(random_bytes(40));
        }

        $user->$verifyCol = 1;
        $user->$verifyAt = now();
        $user->$otpCol = null;
        $user->$timeCol = null;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully.',
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
                'message' => 'User is not authenticated.',
            ], 401);
        }

         /* ===============================
        VERIFICATION CHECK
        =============================== */
        if (!$user->is_mobile_verified ) {
            return response()->json([
                'status'  => false,
                'message' => 'Please verify mobile number.',
            ], 403);
        }

        /* ===============================
        VALIDATION
        =============================== */
        $rules = [
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            // 'mobile'          => 'required|digits_between:8,15|unique:users,mobile',
            'password'        => 'required|min:6|confirmed',
            'gov_id_type'     => 'required|string',
            'gov_id_number'   => 'required|string',
            'gov_id_document' => 'required|array', // multiple files
            'gov_id_document.*' => 'file|mimes:jpg,png,pdf',
            'city'            => 'required|string',
            'country'         => 'required|string',
            'terms_accepted'  => 'required|in:1',

            'profile_photo'   => 'nullable|image|mimes:jpg,png',
            'alt_mobile'      => 'nullable|digits_between:8,15',

            'device_id'       => 'nullable|string',
            'device_type'     => 'nullable|string',
            'fcm_token'       => 'nullable|string',
        ];

        $messages = [
            'name.required'            => 'Full name is required.',
            'email.required'           => 'Email address is required.',
            'email.unique'             => 'This email is already registered.',
            'mobile.required'          => 'Mobile number is required.',
            'mobile.unique'            => 'This mobile number is already registered.',
            'password.confirmed'       => 'Password and confirm password do not match.',
            'gov_id_document.required' => 'At least one Government ID document is required.',
            'gov_id_document.*.mimes'  => 'Government ID must be a file of type: jpg, png, pdf.',
            'terms_accepted.in'        => 'You must accept terms & conditions.',
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
            'name'             => $request->name,
            'email'            => $request->email,
            // 'mobile'           => $request->mobile,
            'alt_mobile'       => $request->alt_mobile,
            'password'         => Hash::make($request->password),

            'role'          => 2, // Vendor
            'status_id'        => 2, // Pending admin approval

            'gov_id_type'      => $request->gov_id_type,
            'gov_id_number'    => $request->gov_id_number,
            'government_id'    => $govDocJson, // multiple files stored

            'city'             => $request->city,
            'country'          => $request->country,
            'profile_photo'    => $profilePhotoName,
            'terms_accepted'   => true,

            'device_id'        => $request->device_id,
            'device_type'      => $request->device_type,
            'fcm_token'        => $request->fcm_token,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Vendor registered successfully. Waiting for admin approval.',
            'data'    => $user,
        ], 200);
    }

    public function vendorLogin(Request $request)
    {
        /* ===============================
        VALIDATION
        =============================== */
        $validator = Validator::make($request->all(), [
            'login'        => 'required|string', // email or mobile
            'password'     => 'required|string',
            'device_token' => 'nullable|string',
            'device_type'  => 'nullable|string',
            'fcm_token'    => 'nullable|string',
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
                'message' => 'Account not found. Please register first.',
            ], 404);
        }

        /* ===============================
        PASSWORD CHECK
        =============================== */
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid login credentials.',
            ], 401);
        }

        /* ===============================
        VENDOR ROLE CHECK
        =============================== */
        if ($user->role != 2) {
            return response()->json([
                'status'  => false,
                'message' => 'Only vendor accounts are allowed to login here.',
            ], 403);
        }

        /* ===============================
        VENDOR APPROVAL CHECK
        =============================== */
        if ($user->status_id == 2) {
            return response()->json([
                'status'  => false,
                'message' => 'Your vendor account is pending admin approval.',
            ], 403);
        }

        if ($user->status_id == 3) {
            return response()->json([
                'status'  => false,
                'message' => 'Your vendor account has been rejected.',
            ], 403);
        }

        if ($user->status_id == 4) {
            return response()->json([
                'status'  => false,
                'message' => 'Your vendor account has been blocked. Contact support.',
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
            'message' => 'Login successful.',
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
       /* ===============================
        VALIDATION
        =============================== */
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|digits_between:8,15',
            'device_type'  => 'nullable|string|max:255',
            'device_token' => 'nullable|string|max:255',
            'fcm_token'    => 'nullable|string|max:255',
        ], [
            'phone_number.required'       => 'Please enter your mobile number.',
            'phone_number.digits_between' => 'Mobile number must be between 8 and 15 digits.',
            'device_type.string'          => 'Device type must be a string.',
            'device_type.max'             => 'Device type cannot exceed 255 characters.',
            'device_token.string'         => 'Device token must be a string.',
            'device_token.max'            => 'Device token cannot exceed 255 characters.',
            'fcm_token.string'            => 'FCM token must be a string.',
            'fcm_token.max'               => 'FCM token cannot exceed 255 characters.',
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
                'message' => 'Account not found. Please register first.',
            ], 404);
        }

        if ($user->role != 2) {
            return response()->json([
                'status'  => false,
                'message' => 'Only vendor accounts can login here.',
            ], 403);
        }

        if ($user->status_id != 1) {
            return response()->json([
                'status'  => false,
                'message' => 'Your account is not active.',
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
        $responseMessage = 'OTP sent to your mobile number.';
        $responseOtp = null;

        if (strlen($phone) === 9) {
            // OSON SMS
            $txnId = 'login_' . time();
            $hash = $this->generateSha256Hex(
                "borafzo;BORAFZO;{$phone};c3cdbb3f1171320d49f2bf1da20f53fc;{$txnId}"
            );

            Http::get('https://api.osonsms.com/sendsms_v1.php', [
                'login'        => 'borafzo',
                'from'         => 'BORAFZO',
                'phone_number' => $phone,
                'msg'          => "Your login OTP is {$otp}",
                'txn_id'       => $txnId,
                'str_hash'     => $hash,
            ]);
        }

        if (strlen($phone) === 10) {
            // India testing
            $responseMessage = 'Auto OTP generated (testing mode).';
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
        /* ===============================
        VALIDATION
        =============================== */
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|digits_between:8,15',
            'otp'          => 'required|digits:6',
            'device_type'  => 'nullable|string|max:255',
            'device_token' => 'nullable|string|max:255',
            'fcm_token'    => 'nullable|string|max:255',
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
                'message' => 'Invalid OTP or phone number.',
            ], 401);
        }

        /* ===============================
        OTP EXPIRY (5 MIN)
        =============================== */
        if (Carbon::parse($user->mobile_otp_sent_at)->addMinutes(5)->isPast()) {
            return response()->json([
                'status'  => false,
                'message' => 'OTP expired. Please request a new one.',
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
            'message' => 'Login successful.',
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
                'message' => 'User is not authenticated.',
            ], 401);
        }

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
            'message' => 'User profile fetched successfully.',
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
                'message' => 'User is not authenticated.',
            ], 401);
        }

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
            'message' => 'Logout successful.',
        ], 200);
    }
    

    public function forgotPassword(Request $request)
    {
        $rules = [
            'email' => 'required|email|exists:users,email',
        ];

        $messages = [
            'email.required' => 'Email is required',
            'email.email'    => 'Enter valid email address',
            'email.exists'   => 'Email not registered',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

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
        $user->save();

        try {
            $logoPath = url('/') . "/assets/email-logo/logo_hewie.png";

            Mail::to($user->email)->send(
                new ForgotPasswordMail(
                    $user->name ?? '',
                    $newPassword,
                    $logoPath
                )
            );

            return response()->json([
                'status'  => true,
                'message' => 'New password has been sent to your email address.',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to send email. Please try again later.',
            ], 500);
        }
    }


    public function resetForgotPassword(Request $request)
    {
        $rules = [
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|digits:6|confirmed',
        ];

        $messages = [
            'email.required'     => 'Email is required',
            'email.email'        => 'Enter valid email',
            'email.exists'       => 'Email not found',

            'password.required'  => 'Password is required',
            'password.digits'    => 'Password must be 6 digits',
            'password.confirmed' => 'Password and confirm password must match',
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
                'message' => 'Password does not match the sent password',
            ], 201);
        }

        if (Carbon::parse($user->forgot_password_sent_at)->addMinutes(10)->isPast()) {
            return response()->json([
                'status'  => false,
                'message' => 'Password expired, request again',
            ], 201);
        }

        $user->password = Hash::make($request->password);
        $user->forgot_password_new = null;
        $user->forgot_password_sent_at = null;
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Password updated successfully',
        ], 200);
    }



  
    public function VendorloginWithApple(Request $request)
    {
        /* ===============================
           VALIDATION
        =============================== */
        $request->validate([
            'identity_token' => 'required',
            'apple_id'       => 'required', // sub from apple
            'email'          => 'nullable|email',
            'name'           => 'nullable|string',
        ]);

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
                    'message' => 'Invalid Apple token',
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
                    'message' => 'Account blocked by admin',
                ], 403);
            }

            /* ===============================
               ISSUE BEARER TOKEN
            =============================== */
            $token = $user->createToken('apple-login')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Apple login successful',
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Apple authentication failed',
            ], 401);
        }
    }
















}
