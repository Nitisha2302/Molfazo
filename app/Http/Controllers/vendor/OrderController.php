<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Auth;

class OrderController extends Controller
{
    public function list()
    {
        $orders = Order::whereHas('store', function ($q) {
            $q->where('user_id', Auth::id());
        })->get();

        return response()->json([
            'status' => true,
            'data' => $orders,
        ]);
    }

    public function accept($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        $order->status_id = 2;
        $order->save();

        return response()->json([
            'status' => true,
            'message' => 'Order accepted.',
        ]);
    }

    public function complete($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        $order->status_id = 3;
        $order->save();

        return response()->json([
            'status' => true,
            'message' => 'Order completed.',
        ]);
    }
}
