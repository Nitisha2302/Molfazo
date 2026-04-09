<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VideoRequest;

class VideoRequestController extends Controller
{
    public function index()
    {
        $status = request()->get('status');

        $requests = VideoRequest::with(['vendor', 'store', 'plan'])
            ->when($status, function($query, $status) {
                $query->where('status', $status);
            })
            ->paginate(10)
            ->appends(request()->all()); // Keep filters in pagination links

        return view('admin.video_requests.index', compact('requests', 'status'));
    }

    public function approve($id)
    {
        $req = VideoRequest::findOrFail($id);
        $req->status = 'approved';
        $req->save();

        // 🔔 Notification
        $vendor = $req->vendor;

        if ($vendor && $vendor->fcm_token) {

            $tokens = [[
                'fcm_token' => $vendor->fcm_token,
                'device_type' => $vendor->device_type ?? 'android',
                'user_id' => $vendor->id,
            ]];

            $data = [
                'notification_type' => 5,
                'title' => "✅ Video Plan Approved",
                'body'  => "Your request has been approved",
                'video_request_id' => $req->id,
                'status' => 'approved'
            ];

            (new \App\Services\FCMService())->sendNotification($tokens, $data, true);
        }

        return back()->with('success','Request Approved Successfully');
    }

    public function reject($id)
    {
        $req = VideoRequest::findOrFail($id);
        $req->status = 'rejected';
        $req->save();

        // 🔔 Notification
        $vendor = $req->vendor;

        if ($vendor && $vendor->fcm_token) {

            $tokens = [[
                'fcm_token' => $vendor->fcm_token,
                'device_type' => $vendor->device_type ?? 'android',
                'user_id' => $vendor->id,
            ]];

            $data = [
                'notification_type' => 5,
                'title' => "❌ Video Plan Rejected",
                'body'  => "Your request has been rejected",
                'video_request_id' => $req->id,
                'status' => 'rejected'
            ];

            (new \App\Services\FCMService())->sendNotification($tokens, $data, true);
        }

        return back()->with('success','Request Rejected');
    }
}