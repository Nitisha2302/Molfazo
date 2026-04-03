<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromotionRequest;

class PromotionRequestController extends Controller
{
    public function index()
    {
        $requests = PromotionRequest::with(['product','package','vendor'])
                        ->latest()
                        ->paginate(10);

        return view('admin.promotion_requests.index', compact('requests'));
    }

    public function approve($id)
    {
        $req = PromotionRequest::findOrFail($id);
        $req->status = 'approved';
        $req->save();

         // 🔔 SEND NOTIFICATION TO VENDOR
    $vendor = $req->vendor;

    if ($vendor && $vendor->fcm_token) {

        $tokens = [
            [
                'fcm_token' => $vendor->fcm_token,
                'device_type' => $vendor->device_type ?? 'android',
                'user_id' => $vendor->id,
            ]
        ];

        $notificationData = [
            'notification_type' => 4,
            'title' => "✅ Promotion Approved",
            'body'  => "Your promotion request has been approved by admin.",
            'promotion_request_id' => $req->id,
            'status' => 'approved'
        ];

        $fcmService = new \App\Services\FCMService();
        $fcmService->sendNotification($tokens, $notificationData, true);
    }


        return back()->with('success','Request Approved Successfully');
    }

    public function reject($id)
    {
        $req = PromotionRequest::findOrFail($id);
        $req->status = 'rejected';
        $req->save();

           // 🔔 SEND NOTIFICATION TO VENDOR
    $vendor = $req->vendor;

    if ($vendor && $vendor->fcm_token) {

        $tokens = [
            [
                'fcm_token' => $vendor->fcm_token,
                'device_type' => $vendor->device_type ?? 'android',
                'user_id' => $vendor->id,
            ]
        ];

        $notificationData = [
            'notification_type' => 4,
            'title' => "❌ Promotion Rejected",
            'body'  => "Your promotion request has been rejected by admin.",
            'promotion_request_id' => $req->id,
            'status' => 'rejected'
        ];

        $fcmService = new \App\Services\FCMService();
        $fcmService->sendNotification($tokens, $notificationData, true);
    }

        return back()->with('success','Request Rejected');
    }
}