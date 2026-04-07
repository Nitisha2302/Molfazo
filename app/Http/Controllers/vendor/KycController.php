<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DiditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    // // ✅ Webhook
    public function webhook(Request $request)
    {
        \Log::info('DIDIT Webhook:', $request->all());

        $sessionId = $request->input('session_id');
        $status = $request->input('status');

        $user = User::where('kyc_session_id', $sessionId)->first();

        if (!$user) {
            return response()->json(['status' => false]);
        }

        if (strtolower($status) === 'approved') {
            $user->kyc_status = 'verified';
        } else {
            $user->kyc_status = 'failed';
        }
        // store full response
        $user->kyc_response = json_encode($request->all());

        // OPTIONAL extracted data
        $user->verified_name = $request->input('data.full_name');
        $user->verified_doc_type = $request->input('data.document_type');
        $user->verified_doc_number = $request->input('data.document_number');

        $user->save();

        return response()->json(['status' => true]);
    }

    
    // public function webhook(Request $request)
    // {
    //     \Log::info('DIDIT Webhook:', $request->all());

    //     // ✅ STEP 1: Get user ID
    //     $externalId = $request->input('external_id');

    //     // ✅ STEP 2: Get status
    //     $status = $request->input('status');

    //     // ✅ STEP 3: Find user
    //     $user = User::find($externalId);

    //     if (!$user) {
    //         \Log::error('User not found for DIDIT', ['external_id' => $externalId]);
    //         return response()->json(['status' => false]);
    //     }

    //     // ✅ STEP 4: Update status
    //     if ($status === 'approved') {
    //         $user->kyc_status = 'verified';
    //     } else {
    //         $user->kyc_status = 'failed';
    //     }

    //     // ✅ STEP 5: Save full response
    //     $user->kyc_response = json_encode($request->all());

    //     // ✅ OPTIONAL: Save extracted data
    //     $user->verified_name = $request->input('data.full_name');
    //     $user->verified_doc_number = $request->input('data.document_number');

    //     $user->save();

    //     return response()->json(['status' => true]);
    // }


}
