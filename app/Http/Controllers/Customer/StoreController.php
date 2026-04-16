<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Product;

class StoreController extends Controller
{
    // Get all stores paginated
    public function list(Request $request)
    {
        // $query = Store::query();
        $query = Store::where('status_id', 1); // ✅ only active stores

        // Optional filters
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        
         // 🔥 NO PAGINATION
        // $stores = $query->get();
        
        //  $stores = $query->with(['products' => function ($q) {
        //     $q->latest()->limit(6)->with('primaryImage'); 
        // }])->get();

        $stores = $query->with(['products' => function ($q) {
            $q->where('approval_status', 'approved') // ✅ correct filter
            ->latest()
            ->limit(6)
            ->with('primaryImage');
        }])->get();

        // Optional: handle no data case
        if ($stores->isEmpty()) {
            return response()->json([
                'status' => false,
              'message' => __('messages.customer.store.list.empty')
            ], 201);
        }

        return response()->json([
            'status' => true,
            'message' => __('messages.customer.store.list.success'),
            'data' => $stores
        ]);
    }

    // Get single store + paginated products
    public function details(Request $request, $id)
    {
        $store = Store::find($id);

        if (!$store) {
            return response()->json([
                'status' => false,
                'message' => 'Store not found'
            ], 404);
        }

         // 🔥 Get all products (NO PAGINATION)
        $products = Product::where('store_id', $store->id)
         ->where('approval_status', 'approved')
            ->with('primaryImage')
            ->get();

        // 🔥 Convert primaryImage object → value
        $products = $products->map(function ($product) {
            $product->primaryimage = optional($product->primaryImage)->image;
            unset($product->primaryImage);
            return $product;
        });

        // Optional: no products case
        // if ($products->isEmpty()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'No products found for this store'
        //     ], 201);
        // }

        return response()->json([
            'status' => true,
            'message' => __('messages.customer.store.details.success'),
            'data' => [
                'store' => $store,
                'products' => $products
            ]
        ]);
    }
}
