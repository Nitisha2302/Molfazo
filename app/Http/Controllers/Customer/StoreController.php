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
        $query = Store::query();

        // Optional filters
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $perPage = $request->get('per_page', 10);
        $stores = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Stores fetched successfully',
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

        // Paginate products for this store
        $perPage = $request->get('per_page', 10);

        $products = Product::where('store_id', $store->id)
                    ->with('primaryImage') // Make sure Product model has primaryImage relation
                    ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Store details fetched successfully',
            'data' => [
                'store' => $store,
                'products' => $products
            ]
        ]);
    }
}
