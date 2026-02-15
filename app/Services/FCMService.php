<?php

namespace App\Services;

use Google\Client;
use Illuminate\Support\Facades\Http;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class FCMService
{
    public function sendNotification(array $tokens, array $data, $saveToDB = true)
    {
        Log::info("🔔 FCMService: sendNotification called", [
            'tokens_count' => count($tokens),
            'notification_type' => $data['notification_type'],
            'title' => $data['title'],
            'body' => $data['body'],
            'save_to_db' => $saveToDB
        ]);

        $client = new Client();
        $client->setAuthConfig(storage_path('app/service-account.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->fetchAccessTokenWithAssertion();
        $accessToken = $client->getAccessToken()['access_token'];

        foreach ($tokens as $tokenInfo) {

            $token = $tokenInfo['fcm_token'];
            $userId = $tokenInfo['user_id'];

            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => (string) $data['title'],
                        'body'  => (string) $data['body'],
                    ],
                    'data' => [
                        'title' => (string) $data['title'],
                        'body'  => (string) $data['body'],
                        'notification_type' => (string) $data['notification_type'],

                        // optional extra fields
                        'conversation_id' => (string) ($data['conversation_id'] ?? ''),
                        'sender_id'       => (string) ($data['sender_id'] ?? ''),
                        'order_id'        => (string) ($data['order_id'] ?? ''),
                    ]
                ]
            ];

            try {

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])->post(
                    'https://fcm.googleapis.com/v1/projects/' . env('FIREBASE_PROJECT_ID') . '/messages:send',
                    $payload
                );

                if ($response->successful()) {

                    Log::info("✅ FCM notification sent successfully", [
                        'user_id' => $userId,
                        'token' => $token,
                        'response' => $response->json()
                    ]);

                    if ($saveToDB) {

                        $notification = Notification::create([
                            'user_id' => $userId,
                            'title' => $data['title'],
                            'description' => $data['body'],
                            'notification_type' => $data['notification_type'],
                            'notification_created_at' => now(),
                        ]);

                        Log::info("💾 Notification saved in database", [
                            'notification_id' => $notification->id
                        ]);
                    }

                } else {
                    Log::error("❌ FCM notification failed", [
                        'user_id' => $userId,
                        'token' => $token,
                        'response' => $response->json()
                    ]);
                }

            } catch (\Exception $e) {
                Log::error("🔥 Exception while sending FCM notification", [
                    'user_id' => $userId,
                    'token' => $token,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info("✅ FCMService: sendNotification finished");
    }
}
