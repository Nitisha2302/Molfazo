<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DiditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class KycController extends Controller
{
    // ✅ Create Session
    public function createSession(DiditService $didit)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $response = $didit->createSession($user);

        if (!isset($response['session_id'])) {
            return response()->json([
                'status' => false,
                'message' => 'Session creation failed',
                'error' => $response
            ]);
        }

        $user->update([
            'kyc_session_id' => $response['session_id']
        ]);

        return response()->json([
            'status' => true,
            'session_id' => $response['session_id'],
            'sdk_token' => $response['session_token']
        ]);
    }

    // // ✅ Webhook correct
    // public function webhook(Request $request)
    // {
    //     \Log::info('DIDIT Webhook:', $request->all());

    //     $sessionId = $request->input('session_id');
    //     $status = $request->input('status');

    //     $user = User::where('kyc_session_id', $sessionId)->first();

    //     if (!$user) {
    //         return response()->json(['status' => false]);
    //     }

    //     if (strtolower($status) === 'approved') {
    //         $user->kyc_status = 'verified';
    //     } else {
    //         $user->kyc_status = 'failed';
    //     }
    //     // store full response
    //     $user->kyc_response = json_encode($request->all());

    //     // OPTIONAL extracted data
    //     $user->verified_name = $request->input('data.full_name');
    //     $user->verified_doc_type = $request->input('data.document_type');
    //     $user->verified_doc_number = $request->input('data.document_number');

    //     $user->save();

    //     return response()->json(['status' => true]);
    // }


    // with downlaod iage and save in db 

    // public function webhook(Request $request)
    // {
    //     \Log::info('DIDIT Webhook:', $request->all());

    //     $sessionId = $request->input('session_id');
    //     $status = $request->input('status');

    //     $user = User::where('kyc_session_id', $sessionId)->first();

    //     if (!$user) {
    //         return response()->json(['status' => false]);
    //     }

    //     if (strtolower($status) === 'approved') {
    //         $user->kyc_status = 'verified';
    //     } else {
    //         $user->kyc_status = 'failed';
    //     }
    //     // store full response
    //     $user->kyc_response = json_encode($request->all());

    //     // ✅ Extract images from response
    //     // $verification = $request->input('decision.id_verifications.0');

    //     $verification = $request->input('decision.id_verifications');

    //     $verification = is_array($verification) ? $verification[0] : null;

    //     if ($verification) {

    //     $frontUrl = $verification['full_front_image'] ?? $verification['front_image'] ?? null;
    //     $backUrl  = $verification['full_back_image'] ?? $verification['back_image'] ?? null;

    //     $savedFiles = [];

    //     // 📥 Download FRONT IMAGE
    //     if ($frontUrl) {
    //         $frontName = 'front_' . time() . '.jpg';
    //         $frontPath = public_path('assets/gov_id_document/' . $frontName);

    //         $image = Http::get($frontUrl)->body();
    //         file_put_contents($frontPath, $image);

    //         $savedFiles[] = $frontName;
    //     }

    //     // 📥 Download BACK IMAGE
    //     if ($backUrl) {
    //         $backName = 'back_' . time() . '.jpg';
    //         $backPath = public_path('assets/gov_id_document/' . $backName);

    //         $image = Http::get($backUrl)->body();
    //         file_put_contents($backPath, $image);

    //         $savedFiles[] = $backName;
    //     }

    //     // ✅ Save in same DB format as manual upload
    //     if (!empty($savedFiles)) {
    //         $user->government_id = json_encode($savedFiles);
    //     }

    //     // OPTIONAL extracted data
    //     $user->verified_name = $request->input('data.full_name');
    //     $user->verified_doc_type = $request->input('data.document_type');
    //     $user->verified_doc_number = $request->input('data.document_number');

    //     }

    //     $user->save();

    //     return response()->json(['status' => true]);
    // }

    // with anme and selfie 

    
    public function webhook(Request $request)
    {
        \Log::info('DIDIT Webhook:', $request->all());

        $sessionId = $request->input('session_id');
        $status = $request->input('status');

        $user = User::where('kyc_session_id', $sessionId)->first();

        if (!$user) {
            return response()->json(['status' => false]);
        }

        $status = strtolower(trim($status));

        if ($status === 'approved') {
            $user->kyc_status = 'verified';
        } elseif ($status === 'in review') {
            $user->kyc_status = 'pending';
        } else {
            $user->kyc_status = 'failed';
        }
        // store full response
        $user->kyc_response = json_encode($request->all());

        // ✅ Extract images from response
        // $verification = $request->input('decision.id_verifications.0');

        $verification = $request->input('decision.id_verifications');

        $verification = is_array($verification) ? $verification[0] : null;

        $selfieData = $request->input('decision.selfie_verification');

    

        if ($verification) {

            $frontUrl = $verification['full_front_image'] ?? $verification['front_image'] ?? null;
            $backUrl  = $verification['full_back_image'] ?? $verification['back_image'] ?? null;

            $savedFiles = [];

            // 📥 Download FRONT IMAGE
            if ($frontUrl) {
                $frontName = 'front_' . time() . '.jpg';
                $frontPath = public_path('assets/gov_id_document/' . $frontName);

                $image = Http::get($frontUrl)->body();
                file_put_contents($frontPath, $image);

                $savedFiles[] = $frontName;
            }

            // 📥 Download BACK IMAGE
            if ($backUrl) {
                $backName = 'back_' . time() . '.jpg';
                $backPath = public_path('assets/gov_id_document/' . $backName);

                $image = Http::get($backUrl)->body();
                file_put_contents($backPath, $image);

                $savedFiles[] = $backName;
            }

            // ✅ Save in same DB format as manual upload
            if (!empty($savedFiles)) {
                $user->government_id = json_encode($savedFiles);
            }

            // OPTIONAL extracted data
            $user->verified_name = $verification['full_name'] ?? null;

            // ALSO update main name (like profile)
            if (!empty($verification['full_name'])) {
                $user->name = $verification['full_name'];
            }
            // $user->verified_doc_type = $request->input('data.document_type');
            // $user->verified_doc_number = $request->input('data.document_number');

        }

        // =======================
        // ✅ SELFIE (ALWAYS TRY)
        // =======================
        $selfieUrl = $selfieData['selfie_image']
            ?? $selfieData['face_image']
            ?? $selfieData['image']
            ?? null;

        if ($selfieUrl) {
            $selfieName = 'selfie_' . time() . '.jpg';
            file_put_contents(public_path('assets/profile_image/' . $selfieName), Http::get($selfieUrl)->body());

            $user->profile_photo = $selfieName;
        }

        $user->save();

        return response()->json(['status' => true]);
    }




}
