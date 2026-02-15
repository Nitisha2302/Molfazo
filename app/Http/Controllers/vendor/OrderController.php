<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Auth;
use Validator;

class OrderController extends Controller
{
    /* ===============================
       LIST ALL ORDERS (VENDOR)
    =============================== */
    public function list(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user || $user->role != 2 || $user->status_id != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Vendor account not approved or unauthenticated.'
            ], 403);
        }

        $query = Order::whereHas('store', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with([
            'user:id,name,email,mobile',
            'items.product.primaryImage'
        ]);

        // ✅ Filter by status_id (Optional)
        if ($request->has('status_id') && $request->status_id != '') {
            $query->where('status_id', $request->status_id);
        }

        $orders = $query->orderBy('id', 'desc')->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No orders found.',
                'data' => []
            ], 404);
        }

        $orders = $orders->map(function ($order) {

            return [
                'id' => $order->id,
                'user_id' => $order->user_id,
                'store_id' => $order->store_id,
                'total_amount' => $order->total_amount,
                'status_id' => $order->status_id,
                'delivery_method' => $order->delivery_method,
                'payment_type' => $order->payment_type,
                'delivery_address' => $order->delivery_address,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,

                // ✅ Customer Info
                'customer' => $order->user ? [
                    'id' => $order->user->id,
                    'name' => $order->user->name,
                    'email' => $order->user->email,
                    'mobile' => $order->user->mobile,
                ] : null,

                // ✅ Products / Items
                'items' => $order->items->map(function ($item) {

                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'total' => $item->price * $item->quantity,

                        'product' => $item->product ? [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'price' => $item->product->price,
                            'discount_price' => $item->product->discount_price,
                            'primary_image' => $item->product->primaryImage
                                ? $item->product->primaryImage->image
                                : null,
                        ] : null
                    ];
                })
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Orders fetched successfully.',
            'data' => $orders
        ], 200);
    }




    /* ===============================
       UPDATE ORDER STATUS (ONE API)
       1=New,2=Accepted,3=Completed,4=Cancelled
    =============================== */
    // public function updateStatus(Request $request, $id)
    // {
    //     $user = Auth::guard('api')->user();

    //     if (!$user || $user->role != 2 || $user->status_id != 1) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Vendor account not approved or unauthenticated.'
    //         ], 403);
    //     }

    //     /* ===============================
    //        VALIDATION WITH CUSTOM MESSAGE
    //     =============================== */
    //     $validator = Validator::make($request->all(), [
    //         'status_id' => 'required|in:2,3,4'
    //     ], [
    //         'status_id.required' => 'Status is required.',
    //         'status_id.in' => 'Invalid status. Allowed values: 2=Accepted, 3=Completed, 4=Cancelled.'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $validator->errors()->first()
    //         ], 422);
    //     }

    //     /* ===============================
    //        FETCH ORDER WITH VENDOR CHECK
    //     =============================== */
    //     $order = Order::where('id', $id)
    //         ->whereHas('store', function ($q) use ($user) {
    //             $q->where('user_id', $user->id);
    //         })
    //         ->first();

    //     if (!$order) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Order not found.'
    //         ], 404);
    //     }

    //     /* ===============================
    //        ORDER STATUS LOGIC
    //     =============================== */

    //     // If already completed or cancelled, block update
    //     if ($order->status_id == 3) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Order is already completed. Status cannot be changed.'
    //         ], 400);
    //     }

    //     if ($order->status_id == 4) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Order is already cancelled. Status cannot be changed.'
    //         ], 400);
    //     }

    //     // If order is new and vendor wants to complete directly (not allowed)
    //     if ($order->status_id == 1 && $request->status_id == 3) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Order must be accepted before completing.'
    //         ], 400);
    //     }

    //     $order->status_id = $request->status_id;
    //     $order->save();

    //     $statusName = match ((int)$request->status_id) {
    //         2 => 'Accepted',
    //         3 => 'Completed',
    //         4 => 'Cancelled',
    //         default => 'Unknown'
    //     };

    //     return response()->json([
    //         'status' => true,
    //         'message' => "Order status updated successfully to {$statusName}.",
    //         'data' => $order
    //     ], 200);
    // }


    // with notification 

     public function updateStatus(Request $request, $id)
    {
        $user = Auth::guard('api')->user();

        if (!$user || $user->role != 2 || $user->status_id != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Vendor account not approved or unauthenticated.'
            ], 403);
        }

        /* ===============================
           VALIDATION WITH CUSTOM MESSAGE
        =============================== */
        $validator = Validator::make($request->all(), [
            'status_id' => 'required|in:2,3,4'
        ], [
            'status_id.required' => 'Status is required.',
            'status_id.in' => 'Invalid status. Allowed values: 2=Accepted, 3=Completed, 4=Cancelled.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        /* ===============================
           FETCH ORDER WITH VENDOR CHECK
        =============================== */
        $order = Order::where('id', $id)
            ->whereHas('store', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found.'
            ], 404);
        }

        /* ===============================
           ORDER STATUS LOGIC
        =============================== */

        // If already completed or cancelled, block update
        if ($order->status_id == 3) {
            return response()->json([
                'status' => false,
                'message' => 'Order is already completed. Status cannot be changed.'
            ], 400);
        }

        if ($order->status_id == 4) {
            return response()->json([
                'status' => false,
                'message' => 'Order is already cancelled. Status cannot be changed.'
            ], 400);
        }

        // If order is new and vendor wants to complete directly (not allowed)
        if ($order->status_id == 1 && $request->status_id == 3) {
            return response()->json([
                'status' => false,
                'message' => 'Order must be accepted before completing.'
            ], 400);
        }

        $order->status_id = $request->status_id;
        $order->save();

        $statusName = match ((int)$request->status_id) {
            2 => 'Accepted',
            3 => 'Completed',
            4 => 'Cancelled',
            default => 'Unknown'
        };


        /* ===============================
        GET ALL PRODUCT NAMES
        =============================== */

        $orderItems = OrderItem::with('product')
            ->where('order_id', $order->id)
            ->get();

        $productNames = $orderItems->pluck('product.name')->toArray();

        $productText = !empty($productNames)
            ? implode(', ', $productNames)
            : 'your products';

        /* ===============================
        SEND NOTIFICATION TO CUSTOMER
        =============================== */

        $customer = User::find($order->user_id);

        if ($customer && $customer->fcm_token) {

            $title = "📦 Order Status Update";

            $body = match ((int)$request->status_id) {
                2 => "✅ Your order for {$productText} has been accepted.",
                3 => "🎉 Your order for {$productText} has been completed successfully.",
                4 => "❌ Your order for {$productText} has been cancelled.",
                default => "Your order for {$productText} status updated."
            };

            $tokens = [
                [
                    'fcm_token' => $customer->fcm_token,
                    'device_type'  => $customer->device_type ?? 'android',
                    'user_id'      => $customer->id,
                ]
            ];

            $notificationData = [
                'notification_type' => 5,
                'title' => $title,
                'body'  => $body,
            ];

            $fcmService = new \App\Services\FCMService();
            $fcmService->sendNotification($tokens, $notificationData, true);

        }

        return response()->json([
            'status' => true,
            'message' => "Order status updated successfully to {$statusName}.",
            'data' => $order
        ], 200);
    }

}
