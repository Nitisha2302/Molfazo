<?php 

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Store;
use App\Models\User;
use App\Services\FCMService;

class OrderController extends Controller
{
   
    // public function placeOrder(Request $request)
    // {
    //     $user = Auth::guard('api')->user();

    //    if (!$user) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Unauthorized'
    //         ], 401);
    //     }

    //     // 🔥 Custom validation
    //     $validator = Validator::make(
    //         $request->all(),
    //         [
    //             'address_id'      => 'required|exists:user_addresses,id',
    //             'payment_type'    => 'required|in:cod,online',
    //             'delivery_method' => 'nullable|string',
    //              'bank_id'      => 'required_if:payment_type,online|exists:banks,id'
    //         ],
    //         [
    //             'address_id.required'   => 'Delivery address is required',
    //             'address_id.exists'     => 'Selected address is invalid',
    //             'payment_type.required' => 'Payment type is required',
    //             'payment_type.in'       => 'Payment type must be COD or Online',
    //              'bank_id.required_if' => 'Please select a bank for online payment.',
    //             'bank_id.exists'      => 'Selected bank is invalid.',
    //         ]
    //     );
        

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $validator->errors()->first()
    //         ], 201);
    //     }


    //     $cartItems = Cart::with('product')
    //         ->where('user_id', $user->id)
    //         ->get();

    //     if ($cartItems->isEmpty()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Your cart is empty.'
    //         ], 400);
    //     }

    //     // Ensure single store
    //     $storeIds = $cartItems->pluck('product.store_id')->unique();
    //     if ($storeIds->count() > 1) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Multiple store products not allowed in one order.'
    //         ], 400);
    //     }

    //     $storeId = $storeIds->first();

    //     $store = Store::with('user')->find($storeId);

    //     $paymentModes = $store->user->payment_modes ?? [];

    //     if ($request->payment_type == 'online' && !in_array('bank', $paymentModes)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'This vendor does not support bank payment.'
    //         ], 400);
    //     }

    //     $address = UserAddress::where('id', $request->address_id)
    //         ->where('user_id', $user->id)
    //         ->first();

    //     if (!$address) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid address selected.'
    //         ], 400);
    //     }

    //     // Check stock availability
    //     // Check stock availability
    //     foreach ($cartItems as $item) {
    //         if ($item->quantity > $item->product->available_quantity) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => "Product {$item->product->name} does not have enough stock."
    //             ], 400);
    //         }
    //     }


    //     DB::beginTransaction();

    //     try {
    //         $total = 0;

    //         foreach ($cartItems as $item) {
    //             $total += $item->product->price * $item->quantity;
    //         }

    //          // 🔐 Secure Bank Account Fetch
    //         $accountNumber = null;

    //         if ($request->payment_type === 'online') {

    //             // get vendor id from store
    //             $store = Store::find($storeId);

    //             $vendorBank = DB::table('vendor_banks')
    //                 ->where('user_id', $store->user_id)
    //                 ->where('bank_id', $request->bank_id)
    //                 ->first();

    //             if (!$vendorBank) {
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'Selected bank is not available for this vendor.'
    //                 ], 400);
    //             }

    //             $accountNumber = $vendorBank->account_number;
    //         }
            


    //         // Create order
    //         $order = Order::create([
    //             'user_id'          => $user->id,
    //             'store_id'         => $storeId,
    //             'total_amount'     => $total,
    //             'status_id'        => 1, // New
    //             'delivery_method'  => $request->delivery_method ?? 'home_delivery',
    //             'delivery_address' => $address->full_name.', '.$address->address.', '.$address->city.' - '.$address->pincode,
    //             'payment_type'     => $request->payment_type,
    //             'bank_id' => $request->payment_type == 'online'
    //             ? $request->bank_id
    //             : null,
    //             'account_number'   => $accountNumber,
    //         ]);

    //         // Create order items & decrement stock
    //         foreach ($cartItems as $item) {
    //             OrderItem::create([
    //                 'order_id'   => $order->id,
    //                 'product_id' => $item->product_id,
    //                 'quantity'   => $item->quantity,
    //                 'price'      => $item->product->price,
    //             ]);

    //             // Reduce stock
    //             $item->product->decrement('available_quantity', $item->quantity);
    //         }

    //         // Clear cart
    //         Cart::where('user_id', $user->id)->delete();

    //          // ✅ SEND NOTIFICATION TO STORE OWNER / VENDOR
    //         $store = Store::find($storeId);

    //         if ($store) {

    //             $vendor = User::find($store->user_id);

    //             if ($vendor && $vendor->fcm_token) {

    //                 // product name message
    //                 $firstProduct = $cartItems->first()->product->name ?? 'Product';
    //                 $productCount = $cartItems->count();

    //                 $productText = $productCount > 1
    //                     ? $firstProduct . " + " . ($productCount - 1) . " more"
    //                     : $firstProduct;

    //                 $title = "🛒 New Order";
    //                 $body  = $user->name . " placed a new order for " . $productText;

    //                 $tokens = [
    //                     [
    //                         'fcm_token' => $vendor->fcm_token,
    //                         'device_type'  => $vendor->device_type ?? 'android',
    //                         'user_id'      => $vendor->id,
    //                     ]
    //                 ];

    //                 $notificationData = [
    //                     'notification_type' => 3,
    //                     'title' => $title,
    //                     'body'  => $body,
    //                       'order_id' => $order->id, 
    //                 ];

    //                 $fcmService = new FCMService();
    //                $fcmService->sendNotification($tokens, $notificationData, true);

    //             }
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Order placed successfully.',
    //             'order_id' => $order->id
    //         ]);

    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Failed to place order.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }


    // with combination price

    public function placeOrder(Request $request)
    {
        $user = Auth::guard('api')->user();

       if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // 🔥 Custom validation
        $validator = Validator::make(
            $request->all(),
            [
                'address_id'      => 'required|exists:user_addresses,id',
                'payment_type'    => 'required|in:cod,online',
                'delivery_method' => 'nullable|string',
                 'bank_id'      => 'required_if:payment_type,online|exists:banks,id'
            ],
            [
                'address_id.required'   => 'Delivery address is required',
                'address_id.exists'     => 'Selected address is invalid',
                'payment_type.required' => 'Payment type is required',
                'payment_type.in'       => 'Payment type must be COD or Online',
                 'bank_id.required_if' => 'Please select a bank for online payment.',
                'bank_id.exists'      => 'Selected bank is invalid.',
            ]
        );
        

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 201);
        }


        $cartItems = Cart::with(['product', 'combination'])
        ->where('user_id', $user->id)
        ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Your cart is empty.'
            ], 400);
        }

        // Ensure single store
        $storeIds = $cartItems->pluck('product.store_id')->unique();
        if ($storeIds->count() > 1) {
            return response()->json([
                'status' => false,
                'message' => 'Multiple store products not allowed in one order.'
            ], 400);
        }

        $storeId = $storeIds->first();

        $store = Store::with('user')->find($storeId);

        $paymentModes = $store->user->payment_modes ?? [];

        if ($request->payment_type == 'online' && !in_array('bank', $paymentModes)) {
            return response()->json([
                'status' => false,
                'message' => 'This vendor does not support bank payment.'
            ], 400);
        }

        $address = UserAddress::where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid address selected.'
            ], 400);
        }

        // Check stock availability
        foreach ($cartItems as $item) {

            if ($item->combination) {

                if ($item->quantity > $item->combination->stock) {
                    return response()->json([
                        'status' => false,
                        'message' => "Variant of {$item->product->name} is out of stock."
                    ], 400);
                }

            } else {

                if ($item->quantity > $item->product->available_quantity) {
                    return response()->json([
                        'status' => false,
                        'message' => "Product {$item->product->name} does not have enough stock."
                    ], 400);
                }

            }
        }


        DB::beginTransaction();

        try {
            $total = 0;

            foreach ($cartItems as $item) {
                $price = $item->combination->price 
                    ?? $item->product->discount_price 
                    ?? $item->product->price;

                $total += $price * $item->quantity;
            }

             // 🔐 Secure Bank Account Fetch
            $accountNumber = null;

            if ($request->payment_type === 'online') {

                // get vendor id from store
                $store = Store::find($storeId);

                $vendorBank = DB::table('vendor_banks')
                    ->where('user_id', $store->user_id)
                    ->where('bank_id', $request->bank_id)
                    ->first();

                if (!$vendorBank) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Selected bank is not available for this vendor.'
                    ], 400);
                }

                $accountNumber = $vendorBank->account_number;
            }
            


            // Create order
            $order = Order::create([
                'user_id'          => $user->id,
                'store_id'         => $storeId,
                'total_amount'     => $total,
                'status_id'        => 1, // New
                'delivery_method'  => $request->delivery_method ?? 'home_delivery',
                'delivery_address' => $address->full_name.', '.$address->address.', '.$address->city.' - '.$address->pincode,
                'payment_type'     => $request->payment_type,
                'bank_id' => $request->payment_type == 'online'
                ? $request->bank_id
                : null,
                'account_number'   => $accountNumber,
            ]);

            // Create order items & decrement stock
            foreach ($cartItems as $item) {

                $price = $item->combination->price 
                    ?? $item->product->discount_price 
                    ?? $item->product->price;

                OrderItem::create([
                    'order_id'       => $order->id,
                    'product_id'     => $item->product_id,
                    'combination_id' => $item->combination_id, // ✅ ADD THIS
                    'quantity'       => $item->quantity,
                    'price'          => $price,
                ]);

                // ✅ STOCK REDUCTION
                if ($item->combination) {
                    $item->combination->decrement('stock', $item->quantity);
                } else {
                    $item->product->decrement('available_quantity', $item->quantity);
                }
            }

            // Clear cart
            Cart::where('user_id', $user->id)->delete();

             // ✅ SEND NOTIFICATION TO STORE OWNER / VENDOR
            $store = Store::find($storeId);

            if ($store) {

                $vendor = User::find($store->user_id);

                // if ($vendor && $vendor->fcm_token) {

                //     // product name message
                //     $firstProduct = $cartItems->first()->product->name ?? 'Product';
                //     $productCount = $cartItems->count();

                //     $productText = $productCount > 1
                //         ? $firstProduct . " + " . ($productCount - 1) . " more"
                //         : $firstProduct;

                //     $title = "🛒 New Order";
                //     $body  = $user->name . " placed a new order for " . $productText;

                //     $tokens = [
                //         [
                //             'fcm_token' => $vendor->fcm_token,
                //             'device_type'  => $vendor->device_type ?? 'android',
                //             'user_id'      => $vendor->id,
                //         ]
                //     ];

                //     $notificationData = [
                //         'notification_type' => 3,
                //         'title' => $title,
                //         'body'  => $body,
                //           'order_id' => $order->id, 
                //     ];

                //     $fcmService = new FCMService();
                //    $fcmService->sendNotification($tokens, $notificationData, true);

                // }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order placed successfully.',
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to place order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /* =========================
       MY ORDERS
    ========================= */
    // public function myOrders()
    // {
    //     $user = Auth::guard('api')->user();

    //      $orders = Order::with('items.product.primaryImage','bank')
    //         ->where('user_id', $user->id)
    //         ->orderBy('id', 'desc')
    //         ->get()
    //         ->map(function ($order) {
    //             return [
    //                 'order_id'     => $order->id,
    //                 'total_amount' => $order->total_amount,
    //                 'status'       => $order->status_id,
    //                 'payment_type' => $order->payment_type,
    //                 // ✅ BANK DETAILS
    //                 'bank' => $order->bank ? [
    //                     'bank_id'   => $order->bank->id,
    //                     'bank_name' => $order->bank->name,
    //                     'bank_logo' => $order->bank->logo,
    //                 ] : null,
    //                 'created_at'   => $order->created_at->format('d M Y'),

    //                 // 🔥 PRODUCT INFO
    //                 'products' => $order->items->map(function ($item) {
    //                     return [
    //                         'product_id'   => $item->product_id,
    //                         'product_name' => $item->product->name ?? '',
    //                         'quantity'     => $item->quantity,
    //                         'price'        => $item->price,
    //                         'image' => optional($item->product->primaryImage)->image
    //                             ? $item->product->primaryImage->image
    //                             : null,
    //                     ];
    //                 })
    //             ];
    //         });

    //     return response()->json([
    //         'status' => true,
    //         'data' => $orders
    //     ]);
    // }


    // with combination price 

    public function myOrders()
    {
        $user = Auth::guard('api')->user();

         $orders = Order::with('items.product.primaryImage', 'items.combination', 'bank')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'order_id'     => $order->id,
                    'total_amount' => $order->total_amount,
                    'status'       => $order->status_id,
                    'payment_type' => $order->payment_type,
                    // ✅ BANK DETAILS
                    'bank' => $order->bank ? [
                        'bank_id'   => $order->bank->id,
                        'bank_name' => $order->bank->name,
                        'bank_logo' => $order->bank->logo,
                    ] : null,
                    'created_at'   => $order->created_at->format('d M Y'),

                    // 🔥 PRODUCT INFO
                    'products' => $order->items->map(function ($item) {
                        return [
                            'product_id'   => $item->product_id,
                            'product_name' => $item->product->name ?? '',
                            'quantity'     => $item->quantity,
                            'price'        => $item->price,

                            // ✅ ADD THIS
                            'variant' => $item->combination 
                                ? json_decode($item->combination->combination, true)
                                : null,

                            'image' => optional($item->product->primaryImage)->image
                                ? $item->product->primaryImage->image
                                : null,
                        ];
                    })
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }
    

    /* =========================
       ORDER DETAILS
    ========================= */
    // public function orderDetails($id)
    // {
    //     $user = Auth::guard('api')->user();

    //     $order = Order::with('items.product.primaryImage','bank')
    //         ->where('id', $id)
    //         ->where('user_id', $user->id)
    //         ->first();

    //     if (!$order) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Order not found.'
    //         ], 404);
    //     }
    //       $vendor = $order->store->vendor ?? null;

    //     return response()->json([
    //         'status' => true,
    //         'data' => [
    //             'order_id' => $order->id,
    //             'total_amount' => $order->total_amount,
    //             'status' => $order->status_id,
    //             'payment_type' => $order->payment_type,
    //             'bank' => $order->bank ? [
    //                 'bank_id' => $order->bank->id,
    //                 'bank_name' => $order->bank->name,
    //                 'bank_logo' => $order->bank->logo,
    //             ] : null,
    //             'delivery_address' => $order->delivery_address,
    //              // ✅ Vendor Details
    //             'vendor' => $vendor ? [
    //                 'vendor_id' => $vendor->id,
    //                 'vendor_name' => $vendor->name,
    //                 'vendor_email' => $vendor->email,
    //                 'vendor_mobile' => $vendor->mobile ?? null,
    //                 'vendor_image' => $vendor->profile_photo ?? null,
    //                 'store_id' => $order->store->id ?? null,
    //                 'store_name' => $order->store->name ?? null,
    //                 'store_address' => $order->store->address ?? null,
    //             ] : null,
    //             'items' => $order->items->map(function ($item) {
    //                   $product = $item->product; 
    //                 return [
    //                     'product_id' => $item->product_id,
    //                     'product_name' => $item->product->name ?? '',
    //                     'quantity' => $item->quantity,
    //                     'price' => $item->price,
    //                     'image' => $product && $product->primaryImage
    //                         ? $product->primaryImage->image
    //                         : null,
    //                 ];
    //             })
    //         ]
    //     ]);
    // }

    // with combination 

     public function orderDetails($id)
    {
        $user = Auth::guard('api')->user();

         $order = Order::with('items.product.primaryImage', 'items.combination', 'bank', 'store.user')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();
            

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found.'
            ], 404);
        }
          $vendor = $order->store->vendor ?? null;

        return response()->json([
            'status' => true,
            'data' => [
                'order_id' => $order->id,
                'total_amount' => $order->total_amount,
                'status' => $order->status_id,
                'payment_type' => $order->payment_type,
                'bank' => $order->bank ? [
                    'bank_id' => $order->bank->id,
                    'bank_name' => $order->bank->name,
                    'bank_logo' => $order->bank->logo,
                ] : null,
                'delivery_address' => $order->delivery_address,
                 // ✅ Vendor Details
                'vendor' => $vendor ? [
                    'vendor_id' => $vendor->id,
                    'vendor_name' => $vendor->name,
                    'vendor_email' => $vendor->email,
                    'vendor_mobile' => $vendor->mobile ?? null,
                    'vendor_image' => $vendor->profile_photo ?? null,
                    'store_id' => $order->store->id ?? null,
                    'store_name' => $order->store->name ?? null,
                    'store_address' => $order->store->address ?? null,
                ] : null,
                'items' => $order->items->map(function ($item) {
                      $product = $item->product; 
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? '',
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                           // ✅ ADD THIS
                        'variant' => $item->combination
                            ? json_decode($item->combination->combination, true)
                            : null,
                        'image' => $product && $product->primaryImage
                            ? $product->primaryImage->image
                            : null,
                    ];
                })
            ]
        ]);
    }

    private function getStatusText($status)
    {
        return match ($status) {
            1 => 'New',
            2 => 'Accepted',
            3 => 'Completed',
            4 => 'Cancelled',
            default => 'Unknown',
        };
    }


}
