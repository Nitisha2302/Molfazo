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

class OrderController extends Controller
{
    /* =========================
       PLACE ORDER
    ========================= */
    public function placeOrder(Request $request)
    {
        $user = Auth::guard('api')->user();

        $request->validate([
            'address_id' => 'required|exists:user_addresses,id',
            'payment_type' => 'required|in:cod,online', // Payment type validation
            'delivery_method' => 'nullable|string'
        ]);

        $cartItems = Cart::with('product')
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
        // Check stock availability
    foreach ($cartItems as $item) {
        if ($item->quantity > $item->product->available_quantity) {
            return response()->json([
                'status' => false,
                'message' => "Product {$item->product->name} does not have enough stock."
            ], 400);
        }
    }


        DB::beginTransaction();

        try {
            $total = 0;

            foreach ($cartItems as $item) {
                $total += $item->product->price * $item->quantity;
            }

            // Create order
            $order = Order::create([
                'user_id'          => $user->id,
                'store_id'         => $storeId,
                'total_amount'     => $total,
                'status_id'        => 1, // New
                'delivery_method'  => $request->delivery_method ?? 'home_delivery',
                'delivery_address' => $address->full_name.', '.$address->address.', '.$address->city.' - '.$address->pincode,
                'payment_type'     => $request->payment_type
            ]);

            // Create order items & decrement stock
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->product->price,
                ]);

                // Reduce stock
                $item->product->decrement('available_quantity', $item->quantity);
            }

            // Clear cart
            Cart::where('user_id', $user->id)->delete();

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
    public function myOrders()
    {
        $user = Auth::guard('api')->user();

        $orders = Order::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'order_id'     => $order->id,
                    'total_amount' => $order->total_amount,
                    'status'       => $this->getStatusText($order->status_id),
                    'payment_type' => $order->payment_type,
                    'created_at'   => $order->created_at->format('d M Y'),
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
    public function orderDetails($id)
    {
        $user = Auth::guard('api')->user();

        $order = Order::with('items.product')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'order_id' => $order->id,
                'total_amount' => $order->total_amount,
                'status' => $this->getStatusText($order->status_id),
                'payment_type' => $order->payment_type,
                'delivery_address' => $order->delivery_address,
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? '',
                        'quantity' => $item->quantity,
                        'price' => $item->price,
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
