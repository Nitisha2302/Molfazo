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

    // with downlaod document  image and save in db correct 

    // public function webhook(Request $request)
    // {
    //     \Log::info('✅ DIDIT Webhook HIT', $request->all());

    //     try {

    //         // =========================
    //         // 🔍 STATUS + USER
    //         // =========================
    //         $status = strtolower(trim($request->input('decision.status')));
    //         $userId = $request->input('decision.vendor_data');

    //         \Log::info('📌 Extracted Data', [
    //             'status' => $status,
    //             'userId' => $userId
    //         ]);

    //         $user = User::find($userId);

    //         if (!$user) {
    //             \Log::error('❌ User NOT FOUND', ['userId' => $userId]);
    //             return response()->json(['status' => false]);
    //         }

    //         \Log::info('✅ User Found', ['id' => $user->id]);

    //         // =========================
    //         // 🔍 STATUS UPDATE
    //         // =========================
    //         if ($status === 'approved') {
    //             $user->kyc_status = 'verified';
    //         } elseif ($status === 'in review') {
    //             $user->kyc_status = 'pending';
    //         } else {
    //             $user->kyc_status = 'failed';
    //         }

    //         \Log::info('📌 KYC Status Set', ['kyc_status' => $user->kyc_status]);

    //         // =========================
    //         // 🔍 SAVE FULL RESPONSE
    //         // =========================
    //         $user->kyc_response = json_encode($request->all());

    //         // =========================
    //         // 🔍 VERIFICATION DATA
    //         // =========================
    //         $verifications = $request->input('decision.id_verifications');

    //         \Log::info('📌 Raw Verification', ['data' => $verifications]);

    //         $verification = is_array($verifications) ? $verifications[0] : null;

    //         if (!$verification) {
    //             \Log::warning('⚠️ No verification data found');
    //         }

    //         $savedFiles = [];

    //         if ($verification) {

    //             $frontUrl = $verification['full_front_image'] ?? $verification['front_image'] ?? null;
    //             $backUrl  = $verification['full_back_image'] ?? $verification['back_image'] ?? null;

    //             \Log::info('📌 Image URLs', [
    //                 'front' => $frontUrl,
    //                 'back' => $backUrl
    //             ]);

    //             // =========================
    //             // 📥 FRONT IMAGE
    //             // =========================
    //             if ($frontUrl) {
    //                 try {
    //                     $frontName = 'front_' . time() . '.jpg';
    //                     $frontPath = public_path('assets/gov_id_document/' . $frontName);

    //                     $response = Http::timeout(10)->get($frontUrl);

    //                     if ($response->successful()) {
    //                         file_put_contents($frontPath, $response->body());
    //                         $savedFiles[] = $frontName;

    //                         \Log::info('✅ Front image saved', ['file' => $frontName]);
    //                     } else {
    //                         \Log::error('❌ Front image download failed', ['url' => $frontUrl]);
    //                     }

    //                 } catch (\Exception $e) {
    //                     \Log::error('❌ Front image error', ['error' => $e->getMessage()]);
    //                 }
    //             }

    //             // =========================
    //             // 📥 BACK IMAGE
    //             // =========================
    //             if ($backUrl) {
    //                 try {
    //                     $backName = 'back_' . time() . '.jpg';
    //                     $backPath = public_path('assets/gov_id_document/' . $backName);

    //                     $response = Http::timeout(10)->get($backUrl);

    //                     if ($response->successful()) {
    //                         file_put_contents($backPath, $response->body());
    //                         $savedFiles[] = $backName;

    //                         \Log::info('✅ Back image saved', ['file' => $backName]);
    //                     } else {
    //                         \Log::error('❌ Back image download failed', ['url' => $backUrl]);
    //                     }

    //                 } catch (\Exception $e) {
    //                     \Log::error('❌ Back image error', ['error' => $e->getMessage()]);
    //                 }
    //             }

    //             // =========================
    //             // 💾 SAVE FILES
    //             // =========================
    //             if (!empty($savedFiles)) {
    //                 $user->government_id = json_encode($savedFiles);

    //                 \Log::info('✅ Government ID saved', ['files' => $savedFiles]);
    //             } else {
    //                 \Log::warning('⚠️ No files saved');
    //             }
    //         }

    //         // =========================
    //         // 💾 FINAL SAVE
    //         // =========================
    //         $user->save();

    //         \Log::info('✅ USER SAVED SUCCESSFULLY', [
    //             'user_id' => $user->id,
    //             'kyc_status' => $user->kyc_status
    //         ]);

    //         return response()->json(['status' => true]);

    //     } catch (\Exception $e) {

    //         \Log::error('🔥 WEBHOOK CRASHED', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return response()->json(['status' => false]);
    //     }
    // }

    // with downlaod the profile from document and uplaod and alos name 


     public function webhook(Request $request)
    {
        \Log::info('✅ DIDIT Webhook HIT', $request->all());

        try {

            // =========================
            // 🔍 STATUS + USER
            // =========================
            $status = strtolower(trim($request->input('decision.status')));
            $userId = $request->input('decision.vendor_data');

            \Log::info('📌 Extracted Data', [
                'status' => $status,
                'userId' => $userId
            ]);

            $user = User::find($userId);

            if (!$user) {
                \Log::error('❌ User NOT FOUND', ['userId' => $userId]);
                return response()->json(['status' => false]);
            }

            \Log::info('✅ User Found', ['id' => $user->id]);

            // =========================
            // 🔍 STATUS UPDATE
            // =========================
            if ($status === 'approved') {
                $user->kyc_status = 'verified';
            } elseif ($status === 'in review') {
                $user->kyc_status = 'pending';
            } else {
                $user->kyc_status = 'failed';
            }

            \Log::info('📌 KYC Status Set', ['kyc_status' => $user->kyc_status]);

            // =========================
            // 🔍 SAVE FULL RESPONSE
            // =========================
            $user->kyc_response = json_encode($request->all());

            // =========================
            // 🔍 VERIFICATION DATA
            // =========================
            $verifications = $request->input('decision.id_verifications');

            \Log::info('📌 Raw Verification', ['data' => $verifications]);

            $verification = is_array($verifications) ? $verifications[0] : null;

            if (!$verification) {
                \Log::warning('⚠️ No verification data found');
            }

            if (!empty($verification['full_name'])) {
                $user->verified_name = $verification['full_name'];
                $user->name = $verification['full_name'];

                \Log::info('✅ Name updated', [
                    'name' => $verification['full_name']
                ]);
            }

            // =========================
            // 📸 SELFIE (LIVENESS)
            // =========================
            $liveness = $request->input('decision.liveness_checks');
            $liveness = is_array($liveness) ? $liveness[0] : null;

            $selfieUrl = $liveness['reference_image'] ?? null;

            \Log::info('📌 Selfie URL', ['selfie' => $selfieUrl]);

            if ($selfieUrl) {
                try {
                    $selfieName = 'selfie_' . time() . '.jpg';
                    $selfiePath = public_path('assets/profile_image/' . $selfieName);

                    $response = Http::timeout(10)->get($selfieUrl);

                    if ($response->successful()) {
                        file_put_contents($selfiePath, $response->body());

                        $user->profile_photo = $selfieName;

                        \Log::info('✅ Selfie saved', ['file' => $selfieName]);
                    } else {
                        \Log::error('❌ Selfie download failed', ['url' => $selfieUrl]);
                    }

                } catch (\Exception $e) {
                    \Log::error('❌ Selfie error', ['error' => $e->getMessage()]);
                }
            }

            $savedFiles = [];

            if ($verification) {

                $frontUrl = $verification['full_front_image'] ?? $verification['front_image'] ?? null;
                $backUrl  = $verification['full_back_image'] ?? $verification['back_image'] ?? null;

                \Log::info('📌 Image URLs', [
                    'front' => $frontUrl,
                    'back' => $backUrl
                ]);

                // =========================
                // 📥 FRONT IMAGE
                // =========================
                if ($frontUrl) {
                    try {
                        $frontName = 'front_' . time() . '.jpg';
                        $frontPath = public_path('assets/gov_id_document/' . $frontName);

                        $response = Http::timeout(10)->get($frontUrl);

                        if ($response->successful()) {
                            file_put_contents($frontPath, $response->body());
                            $savedFiles[] = $frontName;

                            \Log::info('✅ Front image saved', ['file' => $frontName]);
                        } else {
                            \Log::error('❌ Front image download failed', ['url' => $frontUrl]);
                        }

                    } catch (\Exception $e) {
                        \Log::error('❌ Front image error', ['error' => $e->getMessage()]);
                    }
                }

                // =========================
                // 📥 BACK IMAGE
                // =========================
                if ($backUrl) {
                    try {
                        $backName = 'back_' . time() . '.jpg';
                        $backPath = public_path('assets/gov_id_document/' . $backName);

                        $response = Http::timeout(10)->get($backUrl);

                        if ($response->successful()) {
                            file_put_contents($backPath, $response->body());
                            $savedFiles[] = $backName;

                            \Log::info('✅ Back image saved', ['file' => $backName]);
                        } else {
                            \Log::error('❌ Back image download failed', ['url' => $backUrl]);
                        }

                    } catch (\Exception $e) {
                        \Log::error('❌ Back image error', ['error' => $e->getMessage()]);
                    }
                }

                // =========================
                // 💾 SAVE FILES
                // =========================
                if (!empty($savedFiles)) {
                    $user->government_id = json_encode($savedFiles);

                    \Log::info('✅ Government ID saved', ['files' => $savedFiles]);
                } else {
                    \Log::warning('⚠️ No files saved');
                }
            }

            // =========================
            // 💾 FINAL SAVE
            // =========================
            $user->save();

            \Log::info('✅ USER SAVED SUCCESSFULLY', [
                'user_id' => $user->id,
                'kyc_status' => $user->kyc_status
            ]);

            return response()->json(['status' => true]);

        } catch (\Exception $e) {

            \Log::error('🔥 WEBHOOK CRASHED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => false]);
        }
    }



}
