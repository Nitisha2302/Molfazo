<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
     public function index(Request $request)
    {
        $orders = Order::with(['user', 'store', 'items.product'])
            ->when($request->store_name, function ($q) use ($request) {
                $q->whereHas('store', function ($storeQuery) use ($request) {
                    $storeQuery->where('name', 'LIKE', '%' . $request->store_name . '%');
                });
            })
            ->when($request->status_id, function ($q) use ($request) {
                $q->where('status_id', $request->status_id);
            })
            ->latest()
            ->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }
}
