<?php

namespace App\Http\Controllers\Customer; // <--- IMPORTANT

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\FavoriteProducts;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{
    // accordiong to location of store 

    public function list(Request $request)
    {
      $user = Auth::guard('api')->user();

        // Get favorite product ids
        $favIds = [];

        if ($user) {
            $favIds = FavoriteProducts::where('user_id', $user->id)
                        ->pluck('product_id')
                        ->toArray();
        }
        $query = Product::with(['store', 'category', 'subCategory', 'childCategory', 'primaryImage', 'reviews.images','store.user','store.vendorBanks.bank','combinations',  ])->withAvg('reviews', 'rating')
        ->withCount('reviews')->where('approval_status', 'approved');

        // Filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('subcategory_id')) {
            $query->where('sub_category_id', $request->subcategory_id);
        }

        if ($request->has('child_category_id')) {
            $query->where('child_category_id', $request->child_category_id);
        }

        // ✅ Store Location Filter (NEW CODE)
       // Store Location Filter
        if ($request->filled('city') || $request->filled('country')) {

            $query->whereHas('store', function ($q) use ($request) {

                if ($request->filled('city')) {
                    $q->where('city', 'like', '%' . $request->city . '%');
                }

                if ($request->filled('country')) {
                    $q->where('country', 'like', '%' . $request->country . '%');
                }

            });
        }

        // Search by product name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Type Based (Trending / Latest)
        if ($request->has('type') && $request->type != '') {

            if ($request->type == 'trending') {

                // ✅ Trending = Most sold products (Top 10)
                $query->withSum('orderItems as total_sold', 'quantity')
                    ->orderByDesc('total_sold')
                    ->limit(10);

            } elseif ($request->type == 'latest') {

                // ✅ Latest = Last 10 added products
                $query->orderBy('id', 'desc')
                    ->limit(10);
            }

        } else {

            //  Sorting Normal
            if ($request->has('sort') && $request->sort != '') {
                switch ($request->sort) {
                    case 'latest':
                        $query->orderBy('id', 'desc');
                        break;

                    case 'price_low':
                        $query->orderBy('price', 'asc');
                        break;

                    case 'price_high':
                        $query->orderBy('price', 'desc');
                        break;

                    default:
                        $query->orderBy('id', 'desc');
                }
            } else {
                $query->orderBy('id', 'desc');
            }
        }
        //  NO PAGINATION
        $products = $query->get();

        // If no products found → return 201
        if ($products->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No products available'
            ], 201);
        }

         $products = $products->map(function ($product) use ($favIds) {

            $product->primaryimage = optional($product->primaryImage)->image;

            // remove relation object
            unset($product->primaryImage);

             //  Favorite status
            $product->is_favorite = in_array($product->id, $favIds);

            // ✅ FORMAT COMBINATIONS
            $product->combinations = $product->combinations->map(function ($combo) {
                return [
                    'id' => $combo->id,
                    'variant' => json_decode($combo->combination, true),
                    'price' => $combo->price,
                    'stock' => $combo->stock,
                    'images' => $combo->images ? json_decode($combo->images, true) : []
                ];
            });
             // ✅ Banks List
            $paymentModes = $product->store->user->payment_modes ?? [];

                if (in_array('bank', $paymentModes)) {

                    $product->banks = $product->store->vendorBanks->map(function ($vendorBank) {
                        return [
                            'bank_id' => $vendorBank->bank->id ?? null,
                            'name' => $vendorBank->bank->name ?? null,
                            'logo' => $vendorBank->bank->logo ?? null,
                            'account_holder_name' => $vendorBank->account_holder_name,
                            'account_number' => $vendorBank->account_number,
                        ];
                    });

                } else {
                    $product->banks = [];
                }

            return $product;
        });

        return response()->json([
            'status' => true,
            'message' => 'Products fetched successfully',
            'data' => $products
        ]);
    }

    // Product details by ID
    public function details(Request $request, $id)
    {
        $user = Auth::guard('api')->user();

        $favIds = [];

        if ($user) {
            $favIds = FavoriteProducts::where('user_id', $user->id)
                        ->pluck('product_id')
                        ->toArray();
        }
        // Get main product
       $product = Product::with([
            'store',
            'category',
            'subCategory',
            'childCategory',
            'images',
            'primaryImage',
            'reviews.user',
            'reviews.images',
            'store.user','store.vendorBanks.bank'
            ,'combinations',
        ])
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->where('id', $id)
        ->where('status_id', 1)
        ->where('approval_status', 'approved')
        ->first();

      
        

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 201);
        }

         // 🔥 Convert primaryImage object → value
        $product->primaryimage = optional($product->primaryImage)->image;
        unset($product->primaryImage);
        // $product->attributes = $product->attributes_json ?? [];

        $product->combinations = $product->combinations->map(function ($combo) {
            return [
                'id' => $combo->id,
                'variant' => json_decode($combo->combination, true),
                'price' => $combo->price,
                'stock' => $combo->stock,
                'images' => $combo->images ? json_decode($combo->images, true) : []
            ];
        });
        $product->is_favorite = in_array($product->id, $favIds);
        $paymentModes = $product->store->user->payment_modes ?? [];

        if (in_array('bank', $paymentModes)) {

            $product->banks = $product->store->vendorBanks->map(function ($vendorBank) {
                return [
                    'bank_id' => $vendorBank->bank->id ?? null,
                    'name' => $vendorBank->bank->name ?? null,
                    'logo' => $vendorBank->bank->logo ?? null,
                    'account_holder_name' => $vendorBank->account_holder_name,
                    'account_number' => $vendorBank->account_number,
                ];
            });

        } else {
            $product->banks = [];
        }

        // 🔥 Related products (NO PAGINATION)
        $relatedProducts = Product::with(['primaryImage','store.user','store.vendorBanks.bank', 'combinations'])
        ->where('status_id', 1)
        ->where('id', '!=', $product->id)
        ->where('category_id', $product->category_id)
        ->where('approval_status', 'approved')
        ->get();

       $relatedProducts = $relatedProducts->map(function ($item) use ($favIds) {

            $item->primaryimage = optional($item->primaryImage)->image;
            unset($item->primaryImage);
             // Favorite status
           $item->is_favorite = in_array($item->id, $favIds);
            $item->combinations = $item->combinations->map(function ($combo) {
                    return [
                        'id' => $combo->id,
                        'variant' => json_decode($combo->combination, true),
                        'price' => $combo->price,
                        'stock' => $combo->stock,
                        'images' => $combo->images ? json_decode($combo->images, true) : []
                    ];
                });

            $paymentModes = $item->store->user->payment_modes ?? [];

                if (in_array('bank', $paymentModes)) {

                    $item->banks = $item->store->vendorBanks->map(function ($vendorBank) {
                        return [
                            'bank_id' => $vendorBank->bank->id ?? null,
                            'name' => $vendorBank->bank->name ?? null,
                            'logo' => $vendorBank->bank->logo ?? null,
                        ];
                    });

                } else {
                    $item->banks = [];
                }

            return $item;
        });


        return response()->json([
            'status' => true,
            'message' => 'Product details fetched successfully',
            'data' => $product,
            'related_products' => $relatedProducts
        ]);
    }

    public function search(Request $request)
    {

        // 🔥 Get logged-in user (optional)
        $user = Auth::guard('api')->user();

        $favIds = [];

        if ($user) {
            $favIds = FavoriteProducts::where('user_id', $user->id)
                        ->pluck('product_id')
                        ->toArray();
        }
        $query = Product::with(['store', 'category', 'subCategory', 'childCategory', 'primaryImage','store.user','store.vendorBanks.bank','combinations', ])
            ->where('status_id', 1)->where('approval_status', 'approved');

        //  Search Keyword
        if ($request->has('search') && $request->search != '') {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                // Product Name
                $q->where('name', 'like', '%' . $search . '%')

                    // Store Name
                    ->orWhereHas('store', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%' . $search . '%');
                    })

                    // Category Name
                    ->orWhereHas('category', function ($q3) use ($search) {
                        $q3->where('name', 'like', '%' . $search . '%');
                    })

                    // SubCategory Name
                    ->orWhereHas('subCategory', function ($q4) use ($search) {
                        $q4->where('name', 'like', '%' . $search . '%');
                    })

                    // ChildCategory Name
                    ->orWhereHas('childCategory', function ($q5) use ($search) {
                        $q5->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

         // 🔥 City Filter
        if ($request->filled('city')) {
            $query->whereHas('store', function ($q) use ($request) {
                $q->where('city', 'like', '%' . $request->city . '%');
            });
        }

        // 🔥 Country Filter
        if ($request->filled('country')) {
            $query->whereHas('store', function ($q) use ($request) {
                $q->where('country', 'like', '%' . $request->country . '%');
            });
        }
        // 🔥 Filters (Optional)
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('sub_category_id') && $request->sub_category_id != '') {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        if ($request->has('child_category_id') && $request->child_category_id != '') {
            $query->where('child_category_id', $request->child_category_id);
        }

        if ($request->has('store_id') && $request->store_id != '') {
            $query->where('store_id', $request->store_id);
        }

        // 🔥 Sorting
        if ($request->has('sort') && $request->sort != '') {
            switch ($request->sort) {
                case 'latest':
                    $query->orderBy('id', 'desc');
                    break;

                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;

                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;

                default:
                    $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        // 🔥 No Pagination
        $products = $query->get();

        if ($products->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No products found.',
                'data' => []
            ], 201);
        }

        // 🔥 Convert primaryImage object -> primaryimage key
       $products = $products->map(function ($product) use ($favIds) {
            $product->primaryimage = optional($product->primaryImage)->image;
            unset($product->primaryImage);
               $product->is_favorite = in_array($product->id, $favIds);
               $product->combinations = $product->combinations->map(function ($combo) {
                return [
                    'id' => $combo->id,
                    'variant' => json_decode($combo->combination, true),
                    'price' => $combo->price,
                    'stock' => $combo->stock,
                    'images' => $combo->images ? json_decode($combo->images, true) : []
                ];
            });
             // ✅ Product Banks
           $paymentModes = $product->store->user->payment_modes ?? [];

                if (in_array('bank', $paymentModes)) {

                    $product->banks = $product->store->vendorBanks->map(function ($vendorBank) {
                        return [
                            'bank_id' => $vendorBank->bank->id ?? null,
                            'name' => $vendorBank->bank->name ?? null,
                            'logo' => $vendorBank->bank->logo ?? null,
                            'account_holder_name' => $vendorBank->account_holder_name,
                            'account_number' => $vendorBank->account_number,
                        ];
                    });

                } else {
                    $product->banks = [];
                }
            return $product;
        });

        return response()->json([
            'status' => true,
            'message' => 'Search results fetched successfully.',
            'data' => $products
        ], 200);
    }


    
    // Add / Remove Favorite
    public function toggleFavorite(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized user'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id'
        ],[
            'product_id.required' => 'Product ID is required',
            'product_id.exists' => 'Product not found'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->first(),
            ], 422);
        }

        

        $favorite = FavoriteProducts::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($favorite) {

            $favorite->delete();

            return response()->json([
                'status' => true,
                'message' => 'Product removed from favorites'
            ]);

        } else {

            FavoriteProducts::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Product added to favorites'
            ]);
        }

        
    }

    public function favoriteList()
    {
        try {

            $user = Auth::guard('api')->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized user'
                ], 401);
            }

            $favorites = FavoriteProducts::with([
                'product.store',
                'product.category',
                'product.subCategory',
                'product.childCategory',
                'product.images',
                'product.primaryImage',
                'product.reviews.user',
                'product.reviews.images',
                'product.store.user',
                'product.store.vendorBanks.bank'
                ,'product.combinations',
            ])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

            if ($favorites->isEmpty()) {

                return response()->json([
                    'status' => true,
                    'message' => 'No favorite products found',
                    'data' => []
                ]);
            }

            // 🔥 Format response like product APIs
            $favorites->transform(function ($item) {

                $product = $item->product;

                // primary image
                $product->primaryimage = optional($product->primaryImage)->image;
                unset($product->primaryImage);

                // favorite status
                $product->is_favorite = true;

                $product->combinations = $product->combinations->map(function ($combo) {
                    return [
                        'id' => $combo->id,
                        'variant' => json_decode($combo->combination, true),
                        'price' => $combo->price,
                        'stock' => $combo->stock,
                        'images' => $combo->images ? json_decode($combo->images, true) : []
                    ];
                });

                // bank details
                $paymentModes = $product->store->user->payment_modes ?? [];

                if (in_array('bank', $paymentModes)) {

                    $product->banks = $product->store->vendorBanks->map(function ($vendorBank) {
                        return [
                            'bank_id' => $vendorBank->bank->id ?? null,
                            'name' => $vendorBank->bank->name ?? null,
                            'logo' => $vendorBank->bank->logo ?? null,
                            'account_holder_name' => $vendorBank->account_holder_name,
                            'account_number' => $vendorBank->account_number,
                        ];
                    });

                } else {
                    $product->banks = [];
                }

                return $product; // return only product
            });

            return response()->json([
                'status' => true,
                'message' => 'Favorite products fetched successfully',
                'data' => $favorites
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }



}
