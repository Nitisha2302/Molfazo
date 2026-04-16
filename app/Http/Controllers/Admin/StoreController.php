<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\User;
use App\Services\FCMService;
use Illuminate\Support\Facades\Log;



class StoreController extends Controller
{
    // List all stores
    public function index(Request $request)
    {
        $query = Store::query();

        // Search by name or email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status_filter')) {
            $query->where('status_id', $request->status_filter);
        }

        $stores = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.stores.allListing', compact('stores'));
    }

    public function show($id)
    {
        $store = Store::findOrFail($id);
        return view('admin.notifications.show_store', compact('store'));
    }

    // Approve store
    public function approve(Store $store)
    {
        $store->status_id = 1; // Active
        $store->approved_at = now();
         $store->reject_reason = null;
        $store->save();

        return back()->with('success', 'Store approved successfully.');
    }


    public function reject(Request $request, Store $store)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:500'
        ], [
            'reject_reason.required' => 'Please provide a reason for rejection.',
        ]);

        $store->status_id = 3; // Rejected
        $store->reject_reason = $request->reject_reason;
        $store->save();

        Log::info('Store rejected', [
            'store_id' => $store->id,
            'vendor_id' => $store->user_id
        ]);

        /* ===============================
        SEND NOTIFICATION TO VENDOR
        =============================== */

        $vendor = User::find($store->user_id);

        // ❌ Vendor not found
        if (!$vendor) {
            Log::error('FCM Failed: Vendor not found', [
                'store_id' => $store->id,
                'vendor_id' => $store->user_id
            ]);
            return back()->with('success', 'Store rejected but vendor not found.');
        }

        // ❌ Token missing
        if (!$vendor->fcm_token) {
            Log::warning('FCM Skipped: Missing FCM token', [
                'vendor_id' => $vendor->id
            ]);
            return back()->with('success', 'Store rejected but no FCM token.');
        }

        try {

            $tokens = [
                [
                    'fcm_token' => $vendor->fcm_token,
                    'user_id'   => $vendor->id,
                ]
            ];

            $notificationData = [
                'notification_type' => 10,
                'title' => '❌ Store Rejected',
                'body'  => 'Your store "' . $store->name . '" was rejected. Reason: ' . $request->reject_reason,
                'store_id' => $store->id,
                'store' => [
                    'id' => $store->id,
                    'name' => $store->name,
                    'status_id' => $store->status_id,
                    'reject_reason' => $store->reject_reason,
                    'created_at' => $store->created_at,
                    'updated_at' => $store->updated_at,
                ],
            ];

            Log::info('FCM Sending...', [
                'tokens' => $tokens,
                'data' => $notificationData
            ]);

            $fcmService = new \App\Services\FCMService();
            $response = $fcmService->sendNotification($tokens, $notificationData, true);

            Log::info('FCM Response', [
                'response' => $response
            ]);

        } catch (\Exception $e) {

            Log::error('FCM Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return back()->with('success', 'Store rejected successfully.');
    }

}
