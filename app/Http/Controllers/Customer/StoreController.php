<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Product;

class StoreController extends Controller
{
    // Get all stores paginated
    // public function list(Request $request)
    // {
    //     // $query = Store::query();
    //     $query = Store::where('status_id', 1); // ✅ only active stores

    //     // Optional filters
    //     if ($request->has('city')) {
    //         $query->where('city', $request->city);
    //     }

    //     if ($request->has('type')) {
    //         $query->where('type', $request->type);
    //     }

        
    //      // 🔥 NO PAGINATION
    //     // $stores = $query->get();
        
    //     //  $stores = $query->with(['products' => function ($q) {
    //     //     $q->latest()->limit(6)->with('primaryImage'); 
    //     // }])->get();

    //     $stores = $query->with(['products' => function ($q) {
    //         $q->where('approval_status', 'approved') // ✅ correct filter
    //         ->latest()
    //         ->limit(6)
    //         ->with('primaryImage');
    //     }])->get();

    //     // Optional: handle no data case
    //     if ($stores->isEmpty()) {
    //         return response()->json([
    //             'status' => false,
    //           'message' => __('messages.customer.store.list.empty')
    //         ], 201);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => __('messages.customer.store.list.success'),
    //         'data' => $stores
    //     ]);
    // }


    // with new days search 


    //  public function list(Request $request)
    // {
    //     $query = Store::where('status_id', 1);

    //     // 🔥 Store city filter (existing)
    //     if ($request->filled('city')) {
    //         $query->where('city', 'like', '%' . $request->city . '%');
    //     }

    //     // 🔥 Store type filter (existing)
    //     if ($request->filled('type')) {
    //         $query->where('type', $request->type);
    //     }

        

        
    //      // 🔥 NO PAGINATION
    //     // $stores = $query->get();
        
    //     //  $stores = $query->with(['products' => function ($q) {
    //     //     $q->latest()->limit(6)->with('primaryImage'); 
    //     // }])->get();

    //     $stores = $query->with(['products' => function ($q) {
    //         $q->where('approval_status', 'approved') // ✅ correct filter
    //         ->latest()
    //         ->limit(6)
    //         ->with('primaryImage');
    //     }])->get();

    //     // 🔥 DELIVERY FILTER (CITY + TYPE + TIME)
    //     if (
    //         $request->filled('delivery_city') ||
    //         $request->filled('delivery_type') ||
    //         ($request->filled('delivery_time_value') && $request->filled('delivery_time_unit'))
    //     ) {

    //         $stores = $stores->filter(function ($store) use ($request) {

    //             $configs = $store->delivery_config ?? [];

    //             // decode if string
    //             if (is_string($configs)) {
    //                 $configs = json_decode($configs, true);
    //             }

    //             if (!$configs || !is_array($configs)) {
    //                 return false;
    //             }

    //             foreach ($configs as $config) {

    //                 // ❌ skip disabled
    //                 if (($config['enabled'] ?? 0) != 1) continue;

    //                 // ✅ DELIVERY CITY
    //                 if ($request->filled('delivery_city')) {
    //                     if (strtolower($config['city']) != strtolower($request->delivery_city)) {
    //                         continue;
    //                     }
    //                 }

    //                 // ✅ DELIVERY TYPE
    //                 if ($request->filled('delivery_type')) {
    //                     if (($config['delivery_type'] ?? '') != $request->delivery_type) {
    //                         continue;
    //                     }
    //                 }

    //                 // ✅ DELIVERY TIME
    //                 if ($request->filled('delivery_time_value') && $request->filled('delivery_time_unit')) {

    //                     if (($config['delivery_time_unit'] ?? '') != $request->delivery_time_unit) {
    //                         continue;
    //                     }

    //                     if (($config['delivery_time_value'] ?? 9999) > $request->delivery_time_value) {
    //                         continue;
    //                     }
    //                 }

    //                 // ✅ MATCH FOUND
    //                 return true;
    //             }

    //             return false;

    //         })->values();
    //     }

    //     // Optional: handle no data case
    //     if ($stores->isEmpty()) {
    //         return response()->json([
    //             'status' => false,
    //           'message' => __('messages.customer.store.list.empty')
    //         ], 201);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => __('messages.customer.store.list.success'),
    //         'data' => $stores
    //     ]);
    // }


    // search with multiple delivery city 

       public function list(Request $request)
    {
        $query = Store::where('status_id', 1);

        // 🔥 Store city filter (existing)
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // 🔥 Store type filter (existing)
        if ($request->filled('type')) {
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

        // =========================
        // 🔥 DELIVERY FILTER (UPDATED)
        // =========================

        if (
            $request->filled('delivery_city') ||
            $request->filled('delivery_type') ||
            ($request->filled('delivery_time_value') && $request->filled('delivery_time_unit'))
        ) {

            // Normalize inputs
            $cities = $request->delivery_city;
            $types  = $request->delivery_type;

            if (!is_array($cities) && $cities) {
                $cities = [$cities];
            }

            if (!is_array($types) && $types) {
                $types = [$types];
            }

            $stores = $stores->filter(function ($store) use ($request, $cities, $types) {

                $configs = $store->delivery_config ?? [];

                // decode JSON
                if (is_string($configs)) {
                    $configs = json_decode($configs, true);
                }

                if (!$configs || !is_array($configs)) {
                    return false;
                }

                foreach ($configs as $config) {

                    // skip disabled
                    if (($config['enabled'] ?? 0) != 1) continue;

                    // ✅ MULTIPLE CITY MATCH
                    if (!empty($cities)) {
                        $configCity = strtolower(trim($config['city'] ?? ''));

                        $cityMatch = collect($cities)->contains(function ($city) use ($configCity) {
                            return strtolower(trim($city)) === $configCity;
                        });

                        if (!$cityMatch) continue;
                    }

                    // ✅ MULTIPLE TYPE MATCH
                    if (!empty($types)) {
                        $typeMatch = collect($types)->contains(function ($type) use ($config) {
                            return strtolower(trim($type)) === strtolower(trim($config['delivery_type'] ?? ''));
                        });

                        if (!$typeMatch) continue;
                    }

                    // ✅ DELIVERY TIME
                    if ($request->filled('delivery_time_value') && $request->filled('delivery_time_unit')) {

                        $reqUnit = strtolower(trim($request->delivery_time_unit));
                        $configUnit = strtolower(trim($config['delivery_time_unit'] ?? ''));

                        if ($configUnit !== $reqUnit) {
                            continue;
                        }

                        if ((int)$config['delivery_time_value'] > (int)$request->delivery_time_value) {
                            continue;
                        }
                    }

                    return true;
                }

                return false;

            })->values();
        }


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
            ->with('primaryImage',
             'category:id,name',
            'subCategory:id,name',
            'childCategory:id,name')
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
