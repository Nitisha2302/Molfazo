<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    //  Get All Notifications
    public function getAllNotifications()
    {
        $userId = auth()->id();

        $notifications = DB::table('notifications')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $notifications
        ]);
    }

    //  Get Rejections (Store + Product)
    // public function getRejections()
    // {
    //     // ✅ Use only ONE auth method (important)
    //     $user = Auth::guard('api')->user();

    //     if (!$user) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Unauthorized'
    //         ], 401);
    //     }

    //     $notifications = DB::table('notifications')
    //         ->leftJoin('stores', 'notifications.store_id', '=', 'stores.id')
    //         ->leftJoin('products', 'notifications.product_id', '=', 'products.id')
    //         ->where('notifications.user_id', $user->id)
    //         ->whereIn('notifications.notification_type', [10, 22])
    //         ->select(
    //             'notifications.id',
    //             'notifications.title',
    //             'notifications.description',
    //             'notifications.notification_type',
    //             'notifications.created_at',
    //             'notifications.store_id',
    //             'notifications.product_id',

    //             'stores.name as store_name',
    //             'stores.logo as store_image',
    //             'stores.reject_reason as store_reason',


    //             'products.name as product_name',
    //              'products.reject_reason as product_reason'
    //         )
    //         ->orderBy('notifications.created_at', 'desc')
    //         ->get();

    //     $data = [];

    //     foreach ($notifications as $noti) {

    //         // ✅ Extract reason (if exists)
    //         $reason = null;
    //         if (strpos($noti->description, 'Reason:') !== false) {
    //             $parts = explode('Reason:', $noti->description);
    //             $reason = trim($parts[1]);
    //         }

    //         if ($noti->notification_type == 10) {
    //             $data[] = [
    //                 'type' => 'store',
    //                 'notification_id' => $noti->id,
    //                 'store_id' => $noti->store_id,
    //                 'store_name' => $noti->store_name,
    //                 'message' => $noti->description,
    //                  'reason' => $noti->store_reason,
    //                 'created_at' => $noti->created_at
    //             ];
    //         }

    //         if ($noti->notification_type == 22) {
    //             $data[] = [
    //                 'type' => 'product',
    //                 'notification_id' => $noti->id,
    //                 'product_id' => $noti->product_id,
    //                 'product_name' => $noti->product_name,
    //                 'message' => $noti->description,
    //                  'reason' => $noti->store_reason,
    //                 'created_at' => $noti->created_at
    //             ];
    //         }
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'data' => $data
    //     ]);
    // }

   public function getRejections()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // ✅ USE MODEL INSTEAD OF DB
        $notifications = Notification::with([
                'storeN',
                'product.primaryImage' // 🔥 IMPORTANT
            ])
            ->where('user_id', $user->id)
            ->whereIn('notification_type', [10, 22])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [];

        foreach ($notifications as $noti) {

            // ✅ STORE REJECTION
            if ($noti->notification_type == 10) {

                $store = $noti->storeN;

                $data[] = [
                    'type' => 'store',
                    'notification_id' => $noti->id,
                    'store_id' => $store->id ?? null,
                    'store_name' => $store->name ?? null,

                    // ✅ STORE IMAGE
                    'store_image' => $store && $store->logo
                        ?  $store->logo
                        : null,

                    'message' => $noti->description,
                    'reason' => $store->reject_reason ?? null,
                    'created_at' => $noti->created_at
                ];
            }

            // ✅ PRODUCT REJECTION
            if ($noti->notification_type == 22) {

                $product = $noti->product;

                $data[] = [
                    'type' => 'product',
                    'notification_id' => $noti->id,
                    'product_id' => $product->id ?? null,
                    'product_name' => $product->name ?? null,

                    // ✅ PRIMARY IMAGE FROM RELATION (FIXED)
                    'product_image' => $product && $product->primaryImage
                        ?  $product->primaryImage->image
                        : null,

                    'message' => $noti->description,
                    'reason' => $product->reject_reason ?? null, // 🔥 FIXED
                    'created_at' => $noti->created_at
                ];
            }
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    //  Mark as Read
    public function markAsRead($id)
    {
        $userId = auth()->id();

        DB::table('notifications')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->update(['is_read' => 1]);

        return response()->json([
            'status' => true,
            'message' => 'Marked as read'
        ]);
    }

    //  Delete Notification
    public function deleteNotification($id)
    {
        $userId = auth()->id();

        DB::table('notifications')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete();

        return response()->json([
            'status' => true,
             'message' => __('messages.vendor.notification.delete.success')
        ]);
    }

    // Unread Count
    public function unreadCount()
    {
        $userId = auth()->id();

        $count = DB::table('notifications')
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->count();

        return response()->json([
            'status' => true,
            'unread_count' => $count
        ]);
    }
}