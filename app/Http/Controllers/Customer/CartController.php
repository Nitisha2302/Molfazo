<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
     use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * ADD TO CART
     */
    public function add(Request $request)
    {

        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1'
        ]);

       

        $product = Product::where('id', $request->product_id)
                    ->where('status_id', 1)
                    ->first();

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not available'
            ], 201);
        }

        $cartItem = Cart::where('user_id', $user->id)
                    ->where('product_id', $request->product_id)
                    ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id'    => $user->id,
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Product added to cart'
        ]);
    }

    /**
     * CART LIST
     */
    public function list()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $cartItems = Cart::where('user_id', $user->id)
            ->with([
                'product:id,name,price,discount_price,store_id',
                'product.primaryImage',
                 'product.banks' 
            ])
            ->get();

        $total = 0;

        $cartItems->transform(function ($item) use (&$total) {
            $price = $item->product->discount_price ?? $item->product->price;
            $item->item_total = $price * $item->quantity;
            $total += $item->item_total;
            // 🔥 primary image as value
            $item->product->primaryimage = optional($item->product->primaryImage)->image;
            unset($item->product->primaryImage);
            // ✅ BANK DETAILS (Same as details API)
        $item->product->banks =
            $item->product->payment_mode == 'bank'
            ? $item->product->banks->map(function ($bank) {
                return [
                    'id' => $bank->id,
                    'name' => $bank->name,
                    'logo' => $bank->logo ? $bank->logo : null,
                ];
            })
            : [];

            return $item;
        });

         // Optional: empty cart case
        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Cart is empty'
            ], 201);
        }

        return response()->json([
            'status' => true,
            'message' => 'Cart fetched successfully',
            'data' => [
                'items' => $cartItems,
                'cart_total_amount' => $total
            ]
        ]);
    }

    /**
     * UPDATE QUANTITY
     */
   

    public function update(Request $request)
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
                'cart_id'  => 'required|exists:carts,id',
                'quantity' => 'required|integer|min:1'
            ],
            [
                'cart_id.required'  => 'Cart ID is required',
                'cart_id.exists'    => 'Invalid cart item',
                'quantity.required' => 'Quantity is required',
                'quantity.integer'  => 'Quantity must be a number',
                'quantity.min'      => 'Quantity must be at least 1'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 201);
        }

        // 🔥 Fetch cart item safely
        $cartItem = Cart::where('id', $request->cart_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'status' => false,
                'message' => 'Cart item not found'
            ], 201);
        }

        // 🔥 Update quantity
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'status' => true,
            'message' => 'Cart updated successfully'
        ], 200);
    }


    /**
     * REMOVE ITEM
     */
    public function remove($id)
    {
        $user = Auth::guard('api')->user();

        $cartItem = cart::where('id', $id)
                    ->where('user_id', $user->id)
                    ->first();

        if (!$cartItem) {
            return response()->json([
                'status' => false,
                'message' => 'Cart item not found'
            ], 201);
        }

        $cartItem->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item removed from cart'
        ]);
    }
}
