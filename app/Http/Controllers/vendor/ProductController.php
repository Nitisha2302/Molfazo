<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Store;
use App\Models\ChildCategory;
use App\Models\ProductImage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Bank;
use App\Models\ProductBank;
use App\Models\ProductCombination;
use Auth;
use Validator;
use Carbon\Carbon;
use App\Models\AttributeRequest;
use Illuminate\Support\Facades\DB;
use App\Services\FCMService;
use Illuminate\Support\Facades\Http;



class ProductController extends Controller
{
    /**
     * Add a new product
     */

    public function create(Request $request)
    {
        /* ===============================
        AUTHENTICATED USER
        =============================== */
        $user = Auth::guard('api')->user();
        if (!$user || $user->role != 2 || $user->status_id != 1) {
            return response()->json([
                'status' => false,
                 'message' => __('messages.vendor.product.create.unauthorized'),
            ], 403);
        }

        /* ===============================
        VALIDATION
        =============================== */
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'category_id' => 'nullable|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'available_quantity' => 'required|integer|min:0',
            'delivery_available' => 'nullable|boolean',
            'delivery_price' => 'nullable|numeric',
            'delivery_time' => 'nullable|string',
            'characteristics' => 'nullable|array',
            'tags' => 'nullable|array',
            'images' => 'required|array|min:1',
          'images.*' => 'file|mimes:jpeg,jpg,png,gif|max:2048', // 2MB per image
        
            'images_meta' => 'required|array',
            'images_meta.*.id' => 'required|string',
            'attributes_json' => 'nullable|array',

             'combinations' => 'nullable|array',
            'combinations.*.combination' => 'required|array',
            'combinations.*.price' => 'required|numeric',
            'combinations.*.stock' => 'required|integer',
            // ONLY IDS (NO FILES)
            'combinations.*.image_ids' => 'nullable|array',
            'combinations.*.image_ids.*' => 'string',

            'cost_price' => 'nullable|numeric',
            'price_before_discount' => 'nullable|numeric',
            'article' => 'nullable|string',
            'weight' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
        ], [
            'store_id.required' => __('messages.vendor.product.create.validation.store_required'),
            'store_id.exists'   => __('messages.vendor.product.create.validation.store_exists'),

            'name.required'     => __('messages.vendor.product.create.validation.name_required'),

            'price.required'    => __('messages.vendor.product.create.validation.price_required'),
            'price.numeric'     => __('messages.vendor.product.create.validation.price_numeric'),

            'discount_price.numeric' => __('messages.vendor.product.create.validation.discount_numeric'),

            'available_quantity.required' => __('messages.vendor.product.create.validation.quantity_required'),
            'available_quantity.integer'  => __('messages.vendor.product.create.validation.quantity_integer'),

            'images.required' => __('messages.vendor.product.create.validation.images_required'),
            'images.array'    => __('messages.vendor.product.create.validation.images_array'),
            'images.*.mimes'  => __('messages.vendor.product.create.validation.images_mimes'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        /* ===============================
        CHECK STORE OWNERSHIP
        =============================== */
        $store = $user->stores()->where('id', $request->store_id)->first();
        if (!$store || $store->status_id != 1) {
            return response()->json([
                'status' => false,
               'message' => __('messages.vendor.product.create.invalid_store'),
            ], 403);
        }

        if ($request->child_category_id) {
            $childCategory = ChildCategory::where('id', $request->child_category_id)
                ->where('sub_category_id', $request->sub_category_id)
                ->first();

            if (!$childCategory) {
                return response()->json([
                    'status' => false,
                         'message' => __('messages.vendor.product.create.child_category_invalid'),
                ], 422);
            }
        }

        $variantData = $request->variants ?? $request->attributes_json;

        /* ===============================
        CHECK EXISTING PRODUCT BY NAME
        =============================== */
        $existingProduct = Product::where('name', $request->name)
            ->where('approval_status', 'approved')
            ->first();

        $isOriginal = 0;
        $approvalStatus = 'pending';
        $articleNumber = null;

        if ($existingProduct) {
            // ✅ COPY CASE
            $approvalStatus = 'approved';
            $articleNumber = $existingProduct->article_number;
            $isOriginal = 0;
        } else {
            // ✅ NEW PRODUCT → ADMIN APPROVAL
            $approvalStatus = 'pending';
            $isOriginal = 1;
        }

        $parentProductId = null;

        if ($existingProduct) {
            // copy case
            $parentProductId = $existingProduct->is_original == 1
                ? $existingProduct->id
                : $existingProduct->parent_product_id;
        }

        /* ===============================
        CREATE PRODUCT
        =============================== */
        $product = Product::create([
            'store_id' => $request->store_id,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'child_category_id' => $request->child_category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'available_quantity' => $request->available_quantity,
            'delivery_available' => $request->delivery_available ?? 1,
            'delivery_price' => $request->delivery_price,
            'delivery_time' => $request->delivery_time,
            'characteristics' => $request->characteristics ? json_encode($request->characteristics) : null,
            'tags' => $request->tags ? json_encode($request->tags) : null,
            'attributes_json' => $variantData ?? null,
            'status_id' => 1,
            'cost_price' => $request->cost_price,
            'price_before_discount' => $request->price_before_discount,
            'article' => $request->article,
            'weight' => $request->weight,
            'length' => $request->length,
            'width' => $request->width,
            'height' => $request->height,
            'article_number' => $articleNumber,
            'approval_status' => $approvalStatus,
            'is_original' => $isOriginal,
            'parent_product_id' => $parentProductId,
        ]);

        /* ===============================
        UPLOAD IMAGES + MAP ID
        =============================== */
        $imageMap = [];       // img_1 => filename.jpg
        $allImages = [];      // fallback images

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {

                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('assets/product_images'), $filename);

                // 🔥 GET TEMP ID FROM FRONTEND
                $imageId = $request->images_meta[$index]['id'] ?? null;

                if ($imageId) {
                    $imageMap[$imageId] = $filename;
                }

                $allImages[] = $filename;

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $filename,
                    'is_primary' => $index === 0 ? 1 : 0,
                ]);
            }
        }

        /* ===============================
        HANDLE ATTRIBUTES (UNCHANGED)
        =============================== */
        if ($request->attributes_json) {

            $categoryAttr = DB::table('category_attributes')
                ->where('child_category_id', $request->child_category_id)
                ->first();

            $existing = $categoryAttr
                ? json_decode($categoryAttr->attributes_json, true)
                : [];

            foreach ($request->attributes_json as $attr => $values) {

                if (!isset($existing[$attr])) {
                    foreach ($values as $val) {
                        AttributeRequest::firstOrCreate([
                            'vendor_id' => $user->id,
                            'child_category_id' => $request->child_category_id,
                            'attribute_name' => $attr,
                            'attribute_value' => $val
                        ]);
                    }
                } else {
                    foreach ($values as $val) {
                        if (!in_array($val, $existing[$attr])) {
                            AttributeRequest::firstOrCreate([
                                'vendor_id' => $user->id,
                                'child_category_id' => $request->child_category_id,
                                'attribute_name' => $attr,
                                'attribute_value' => $val
                            ]);
                        }
                    }
                }
            }
        }

        /* ===============================
        SAVE COMBINATIONS (🔥 UPDATED)
        =============================== */
        if ($request->combinations) {

            foreach ($request->combinations as $combo) {

                $comboImages = [];

                // 🔥 USE IMAGE IDS
                if (!empty($combo['image_ids'])) {
                    foreach ($combo['image_ids'] as $imgId) {
                        if (isset($imageMap[$imgId])) {
                            $comboImages[] = $imageMap[$imgId];
                        }
                    }
                }

                ProductCombination::create([
                    'product_id' => $product->id,
                    'combination' => json_encode($combo['combination']),
                    'price' => $combo['price'],
                    'stock' => $combo['stock'],
                    'images' => json_encode(!empty($comboImages) ? $comboImages : $allImages),
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => __('messages.vendor.product.create.success'),
            'data' => $this->formatProduct($product),
        ], 200);
    }


    public function list(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user || $user->role != 2 || $user->status_id != 1) {
            return response()->json([
                'status' => false,
                'message' => __('messages.vendor.product.list.unauthorized'),
            ], 403);
        }

        // ✅ Global catalog
        if ($request->has('type') && $request->type == 'all') {

            $products = Product::where('status_id', 1)
                ->where('approval_status', 'approved') // 🔥 FIX
                ->with(['reviews.images'])
                ->latest()
                ->get();

        } else {

            // ✅ Vendor own products
            $products = Product::whereHas('store', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                 ->with(['reviews.images'])
                ->latest()
                ->get();
        }

        $products = $products->map(function ($product) {
            return $this->formatProduct($product);
        });
        

        return response()->json([
            'status' => true,
            'message' => __('messages.vendor.product.list.success'),
            'data' => $products,
        ], 200);
    }

    /**
     * Get product details
     */
        public function details($id)
        {
            $user = Auth::guard('api')->user();
            if (!$user || $user->role != 2 || $user->status_id != 1) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.vendor.product.details.unauthorized'),
                ], 403);
            }

            $product = Product::where('id', $id)
                ->whereHas('store', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                  ->with(['reviews.images']) 
                ->first();

            if (!$product) {
                return response()->json([
                    'status' => false,
                     'message' => __('messages.vendor.product.details.not_found'),
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => __('messages.vendor.product.details.success'),
                'data' => $this->formatProduct($product),
            ], 200);
        }

        /**
         * Format product for API response
         */
        private function formatProduct($product)
        {
            $banks = [];

            if ($product->store && $product->store->user_id) {

                $vendorBanks = \App\Models\VendorBank::with('bank')
                    ->where('user_id', $product->store->user_id)
                    ->get();

                foreach ($vendorBanks as $vendorBank) {
                    $banks[] = [
                        'id' => $vendorBank->id,
                        'bank_id' => $vendorBank->bank_id,
                        'bank_name' => $vendorBank->bank->name ?? null,
                        'logo' => $vendorBank->bank->logo ?? null,
                        'account_holder_name' => $vendorBank->account_holder_name,
                        'account_number' => $vendorBank->account_number,
                        'ifsc_code' => $vendorBank->ifsc_code,
                        'phone_number' => $vendorBank->phone_number,
                    ];
                }
                }
            return [
                'id' => $product->id,
                'store_id' => $product->store_id,
                
                'article_number' => $product->article_number,
                'approval_status' => $product->approval_status,
                'is_original' => (bool)$product->is_original,

                'parent_product_id' => $product->parent_product_id,

                // 🔥 OPTIONAL BUT VERY USEFUL (frontend friendly)
                // 'is_child' => $product->parent_product_id ? true : false,
                // 'parent_id' => $product->parent_product_id,

                'category' => $product->category ? [
                'id' => $product->category->id,
                    'name' => $product->category->name,
                ] : null,

                'sub_category' => $product->subCategory ? [
                    'id' => $product->subCategory->id,
                    'name' => $product->subCategory->name,
                ] : null,

                'child_category' => $product->childCategory ? [
                    'id' => $product->childCategory->id,
                    'name' => $product->childCategory->name,
                ] : null,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'discount_price' => $product->discount_price,
                'available_quantity' => $product->available_quantity,
                'delivery_available' => (bool)$product->delivery_available,
                'delivery_price' => $product->delivery_price,
                'delivery_time' => $product->delivery_time,
                'characteristics' => $product->characteristics ? json_decode($product->characteristics, true) : null,
                'tags' => $product->tags ? json_decode($product->tags, true) : null,
                'status_id' => $product->status_id,
                // 'status_name' => $this->getStatusName($product->status_id),
                'attributes_json' => $product->attributes_json,
                'status_name' => $product->status_id,
            'payment_modes' => $product->payment_modes,
                // ✅ BANK DATA ADDED HERE
                /* ===============================
            ✅ BANKS WITH ACCOUNT DETAILS
                =============================== */
                'banks' => $banks,
                'images' => $product->images->map(function ($img) {
                    return [
                        'id' => $img->id,
                        'image' =>  $img->image,
                        'color' => $img->color,
                        'is_primary' => (bool)$img->is_primary,
                    ];
                }),
            'combinations' => $product->combinations->map(function ($combo) {
                    return [
                        'id' => $combo->id,
                        'variant' => json_decode($combo->combination, true),
                        'price' => $combo->price,
                        'price_before_discount' => $combo->price_before_discount,
                        'cost_price' => $combo->cost_price,
                        'description' => $combo->description,
                    
                        'stock' => $combo->stock,
                        'images' => $combo->images ? json_decode($combo->images, true) : [],
                    ];
                }),
                'reviews' => $product->reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'title' => $review->title,
                    'review' => $review->review,
                    'username' => $review->username,
                    'profile_image' => $review->profile_image,
                    'created_at' => $review->created_at,

                    'images' => $review->images->map(function ($img) {
                        return [
                            'id' => $img->id,
                            'image' => $img->image,
                        ];
                    }),
                ];
            }),
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        }

        private function getStatusName($status)
        {
            return match ($status) {
                1 => 'Active',
                2 => 'Blocked',
                3 => 'Deleted',
                default => 'Unknown',
            };
        }

        public function getstoreAllProducts($store_id)
        {
            /* ===============================
            AUTHENTICATION (OPTIONAL)
            =============================== */
            $user = Auth::guard('api')->user();

            // If this API should be public → remove this block
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            /* ===============================
            STORE VALIDATION
            =============================== */
            $store = Store::where('id', $store_id)
                ->where('status_id', 1) // 1 = Active
                ->first();

            if (!$store) {
                return response()->json([
                    'status' => false,
                    'message' => 'Store not found or not active.',
                ], 404);
            }

            /* ===============================
            FETCH STORE PRODUCTS
            =============================== */
            $products = Product::where('store_id', $store->id)  ->with(['reviews.images']) ->get();

            if ($products->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'data' => [],
                    'message' => 'No products found for this store.'
                ], 200);
            }

            /* ===============================
            FORMAT PRODUCTS
            =============================== */
            $products = $products->map(function ($product) {
                return $this->formatProduct($product);
            });

            return response()->json([
                'status' => true,
                 'message' => __('messages.vendor.product.list.success'),
                'data' => $products,
            ], 200);
        }

    public function getBankList(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized user.'
            ], 401);
        }

        $banks = Bank::select('id','name','logo')->get();

        $data = $banks->map(function ($bank) {
            return [
                'id' => $bank->id,
                'name' => $bank->name,
                'logo' => $bank->logo
                    ? $bank->logo
                    : null,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => __('messages.vendor.bank.list_success'),
            'data' => $data
        ]);
    }


    public function dashboard(Request $request)
    {
        $vendor = Auth::guard('api')->user();

        if (!$vendor || $vendor->role != 2) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | DATE FILTER LOGIC
        |--------------------------------------------------------------------------
        */

        if ($request->today == 1) {
            $startDate = Carbon::today();
            $endDate   = Carbon::today()->endOfDay();
        } else {
            $startDate = $request->start_date
                ? Carbon::parse($request->start_date)->startOfDay()
                : null;

            $endDate = $request->end_date
                ? Carbon::parse($request->end_date)->endOfDay()
                : null;
        }

        /*
        |--------------------------------------------------------------------------
        | Vendor Stores
        |--------------------------------------------------------------------------
        */

        $storeIds = $vendor->stores()->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | ORDER ITEMS QUERY (BASE)
        |--------------------------------------------------------------------------
        */

        $orderItemsQuery = OrderItem::whereHas('product', function ($q) use ($storeIds) {
            $q->whereIn('store_id', $storeIds);
        });

        if ($startDate && $endDate) {
            $orderItemsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $orderItems = $orderItemsQuery->get();

        /*
        |--------------------------------------------------------------------------
        | ORDERS DATA
        |--------------------------------------------------------------------------
        */

        $orderIds = $orderItems->pluck('order_id')->unique();

        $orders = Order::whereIn('id', $orderIds);

        if ($startDate && $endDate) {
            $orders->whereBetween('created_at', [$startDate, $endDate]);
        }

        $orders = $orders->get();

        $totalOrders = $orders->count();

        /*
        |--------------------------------------------------------------------------
        | REVENUE
        |--------------------------------------------------------------------------
        */

        $totalRevenue = $orderItems->sum(fn($i) =>
            $i->price * $i->quantity
        );

        $dailyRevenue = $orderItems
            ->groupBy(fn($i) => Carbon::parse($i->created_at)->format('Y-m-d'))
            ->map(fn($items) => $items->sum(fn($i)=>$i->price*$i->quantity))
            ->values();

        $weeklyRevenue = $orderItems
            ->where('created_at','>=',Carbon::now()->subDays(7))
            ->sum(fn($i)=>$i->price*$i->quantity);

        $monthlyRevenue = $orderItems
            ->where('created_at','>=',Carbon::now()->startOfMonth())
            ->sum(fn($i)=>$i->price*$i->quantity);

        $yearlyRevenue = $orderItems
            ->where('created_at','>=',Carbon::now()->startOfYear())
            ->sum(fn($i)=>$i->price*$i->quantity);

        /*
        |--------------------------------------------------------------------------
        | PRODUCTS
        |--------------------------------------------------------------------------
        */

        // $productQuery = Product::whereIn('store_id', $storeIds);
        $productQuery = Product::with('primaryImage')
         ->whereIn('store_id', $storeIds);

        if ($request->stock == 'out') {
            $productQuery->where('available_quantity', 0);
        }

        if ($request->stock == 'in') {
            $productQuery->where('available_quantity', '>', 0);
        }

        $products = $productQuery->get();

        $outOfStock = Product::whereIn('store_id', $storeIds)
            ->where('available_quantity', 0)
            ->count();

        $mostPurchased = OrderItem::selectRaw(
                'product_id, SUM(quantity) as total_sold'
            )
            ->whereHas('product', fn($q)=>$q->whereIn('store_id',$storeIds))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
           ->with('product.primaryImage')
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | CUSTOMERS
        |--------------------------------------------------------------------------
        */

        $totalCustomers = Order::whereIn('id', $orderIds)
            ->distinct('user_id')
            ->count('user_id');

        /*
        |--------------------------------------------------------------------------
        | TODAY ORDERS (ALL STATUS)
        |--------------------------------------------------------------------------
        */

        $todayOrders = Order::whereDate('created_at', Carbon::today())
            ->whereHas('items.product', fn($q)=>$q->whereIn('store_id',$storeIds))
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'status' => true,

            'orders' => [
                'total_orders' => $totalOrders,
                'recent_orders' => $orders->take(10),
            ],

            'revenue' => [
                'total_revenue' => $totalRevenue,
                'daily_revenue' => $dailyRevenue,
                'weekly_revenue' => $weeklyRevenue,
                'monthly_revenue' => $monthlyRevenue,
                'yearly_revenue' => $yearlyRevenue,
            ],

            'products' => [
                'total_products' => $products->count(),
                'out_of_stock' => $outOfStock,
                'all_products' => $products,
                'most_purchased' => $mostPurchased,
            ],

            'customers' => [
                'total_customers' => $totalCustomers
            ],

            'today_orders' => $todayOrders
        ]);
    }

    // new flow 


    private function generateCombinations($arrays)
    {
        $result = [[]];

        foreach ($arrays as $property => $property_values) {

            $tmp = [];

            foreach ($result as $result_item) {

                foreach ($property_values as $property_value) {

                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }

            $result = $tmp;
        }

        return $result;
    }

    public function updateCombination(Request $request, $id)
    {
        $combo = ProductCombination::findOrFail($id);

        // Update price and stock
        $combo->price = $request->price ?? $combo->price;
        $combo->stock = $request->stock ?? $combo->stock;

        $images = [];

        // If new images uploaded
        if ($request->hasFile('images')) {


            foreach ($request->file('images') as $image) {

                $name = time().'_'.$image->getClientOriginalName();

                $image->move(public_path('assets/product_images'), $name);

                $images[] = $name;
            }

            // Save images as JSON
            $combo->images = json_encode($images);
        }

        $combo->save();

        return response()->json([
            'status' => true,
           'message' => __('messages.vendor.combination.combination_upadted'),
            'data' => $combo
        ]);
    }

    public function deleteCombination($id)
    {
        $combo = ProductCombination::findOrFail($id);

        // Delete images from storage
        if ($combo->images) {

            $images = json_decode($combo->images, true);

            foreach ($images as $image) {

                $path = public_path($image);

                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }

        $combo->delete();

        return response()->json([
            'status' => true,
            'message' => __('messages.vendor.combination.combination_delete'),
        ]);
    }

    // with requested new request flow

    public function copyProduct(Request $request, $id)
    {
        $user = Auth::guard('api')->user();

        if (!$user || $user->role != 2) {
            return response()->json([
                'status' => false,
                'message' => __('messages.vendor.product.create.unauthorized')
            ], 401);
        }

        /* ===============================
        VALIDATION (SAME AS CREATE)
        =============================== */
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'discount_price' => 'nullable|numeric',
            'available_quantity' => 'nullable|integer|min:0',

            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,jpg,png,gif|max:2048',

            'images_meta' => 'nullable|array',

            'attributes_json' => 'nullable|array',

            'combinations' => 'nullable|array',
            'combinations.*.combination' => 'required|array',
            'combinations.*.price' => 'required|numeric',
            'combinations.*.stock' => 'required|integer',
            'combinations.*.image_ids' => 'nullable|array',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        /* ===============================
        CHECK STORE
        =============================== */
        $store = $user->stores()->where('id', $request->store_id)->first();
        if (!$store) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid store'
            ]);
        }

        DB::beginTransaction();

        try {

            $product = Product::with(['images', 'combinations'])->findOrFail($id);

            if ($product->approval_status != 'approved') {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.vendor.product.copy.only_approved')
                ], 403);
            }

            $parentProductId = $product->is_original == 1
            ? $product->id
            : $product->parent_product_id;

            /* ===============================
            CREATE NEW PRODUCT (MERGE LOGIC)
            =============================== */
            $newProduct = Product::create([

                'store_id' => $store->id,

                // 🔥 override OR fallback
                'name' => $request->name ?? $product->name,
                'description' => $request->description ?? $product->description,
                'price' => $request->price ?? $product->price,
                'discount_price' => $request->discount_price ?? $product->discount_price,
                'available_quantity' => $request->available_quantity ?? $product->available_quantity,

                'category_id' => $request->category_id ?? $product->category_id,
                'sub_category_id' => $request->sub_category_id ?? $product->sub_category_id,
                'child_category_id' => $request->child_category_id ?? $product->child_category_id,

                'attributes_json' => $request->attributes_json ?? $product->attributes_json,

                'delivery_available' => $request->delivery_available ?? $product->delivery_available,
                'delivery_price' => $request->delivery_price ?? $product->delivery_price,
                'delivery_time' => $request->delivery_time ?? $product->delivery_time,

                'cost_price' => $request->cost_price ?? $product->cost_price,
                'price_before_discount' => $request->price_before_discount ?? $product->price_before_discount,

                'weight' => $request->weight ?? $product->weight,
                'length' => $request->length ?? $product->length,
                'width' => $request->width ?? $product->width,
                'height' => $request->height ?? $product->height,

                // 🔥 IMPORTANT FLAGS
                'article_number' => $product->article_number,
                'article' => $request->article ?? $product->article,
                'approval_status' => 'approved',
                'is_original' => 0,
                'status_id' => 1,
                'parent_product_id' => $parentProductId,
            ]);

            /* ===============================
            HANDLE IMAGES
            =============================== */
            $imageMap = [];
            $allImages = [];

            // ✅ If new images uploaded
            if ($request->hasFile('images')) {

                foreach ($request->file('images') as $index => $file) {

                    $filename = time().'_'.$file->getClientOriginalName();
                    $file->move(public_path('assets/product_images'), $filename);

                    $imageId = $request->images_meta[$index]['id'] ?? null;

                    if ($imageId) {
                        $imageMap[$imageId] = $filename;
                    }

                    $allImages[] = $filename;

                    ProductImage::create([
                        'product_id' => $newProduct->id,
                        'image' => $filename,
                        'is_primary' => $index === 0 ? 1 : 0,
                    ]);
                }

            } else {
                // ✅ fallback old images
                foreach ($product->images as $img) {
                    $allImages[] = $img->image;

                    ProductImage::create([
                        'product_id' => $newProduct->id,
                        'image' => $img->image,
                        'is_primary' => $img->is_primary,
                    ]);
                }
            }

            /* ===============================
            HANDLE COMBINATIONS
            =============================== */
            if ($request->combinations) {

                foreach ($request->combinations as $combo) {

                    $comboImages = [];

                    if (!empty($combo['image_ids'])) {
                        foreach ($combo['image_ids'] as $imgId) {
                            if (isset($imageMap[$imgId])) {
                                $comboImages[] = $imageMap[$imgId];
                            }
                        }
                    }

                    ProductCombination::create([
                        'product_id' => $newProduct->id,
                        'combination' => json_encode($combo['combination']),
                        'price' => $combo['price'],
                        'stock' => $combo['stock'],
                        'images' => json_encode(!empty($comboImages) ? $comboImages : $allImages),
                    ]);
                }

            } else {
                // ✅ fallback old combinations
                foreach ($product->combinations as $combo) {
                    ProductCombination::create([
                        'product_id' => $newProduct->id,
                        'combination' => $combo->combination,
                        'price' => $combo->price,
                        'stock' => $combo->stock,
                        'images' => $combo->images
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                 'message' => __('messages.vendor.product.copy.success'),
                'data' => $newProduct
            ]);

        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ]);
        }
    }


    public function update(Request $request, $id)
    {
        /* ===============================
        AUTH USER
        =============================== */
        $user = Auth::guard('api')->user();
        if (!$user || $user->role != 2 || $user->status_id != 1) {
            return response()->json([
                'status' => false,
                'message' => __('messages.vendor.product.update.unauthorized'),
            ], 403);
        }

        /* ===============================
        FIND PRODUCT
        =============================== */
       $product = Product::where('id', $id)
        ->whereIn('store_id', $user->stores()->pluck('id'))
        ->first();

        if (!$product) {
            return response()->json([
                'status' => false,
                 'message' => __('messages.vendor.product.update.not_found'),
            ], 404);
        }

        /* ===============================
        VALIDATION
        =============================== */
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
            'discount_price' => 'nullable|numeric',
            'available_quantity' => 'sometimes|required|integer|min:0',
            'delivery_available' => 'nullable|boolean',
            'delivery_price' => 'nullable|numeric',
            'delivery_time' => 'nullable|string',
            'characteristics' => 'nullable|array',
            'tags' => 'nullable|array',

            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,jpg,png,gif|max:2048',

            // image meta (IDs)
            'images_meta' => 'nullable|array',
            'images_meta.*.id' => 'required_with:images|string',

            'attributes_json' => 'nullable|array',
            
            'combinations' => 'nullable|array',
            'combinations.*.combination' => 'required|array',
            'combinations.*.price' => 'required|numeric',
            'combinations.*.stock' => 'required|integer',
             'combinations.*.image_ids' => 'nullable|array',
             'combinations.*.image_ids.*' => 'string',
            'cost_price' => 'nullable|numeric',
            'price_before_discount' => 'nullable|numeric',
            'article' => 'nullable|string',
            'weight' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }


        /* ===============================
        CHECK NAME CHANGE + ARTICLE LOGIC
        =============================== */
        $articleNumber = $product->article_number;
        $approvalStatus = $product->approval_status;
        $isOriginal = $product->is_original;
        $parentProductId = $product->parent_product_id; // default (no change)

        if ($request->has('name') && strtolower($request->name) != strtolower($product->name)) {

            $existingProduct = Product::whereRaw('LOWER(name) = ?', [strtolower($request->name)])
                ->where('approval_status', 'approved')
                ->where('id', '!=', $product->id)
                ->first();

            // if ($existingProduct) {
            //     // ✅ MATCH FOUND → LINK WITH EXISTING ARTICLE
            //     $articleNumber = $existingProduct->article_number;
            //     $approvalStatus = 'approved';
            //     $isOriginal = 0;
            // } else {
            //     // ❗ NEW NAME → NEED ADMIN APPROVAL AGAIN
            //     $articleNumber = null;
            //     $approvalStatus = 'pending';
            //     $isOriginal = 1;
            // }

            if ($existingProduct) {

                // ✅ MATCH FOUND → LINK WITH EXISTING ARTICLE
                $articleNumber = $existingProduct->article_number;
                $approvalStatus = 'approved';
                $isOriginal = 0;

                // 🔥 ADD THIS (MOST IMPORTANT)
                $parentProductId = $existingProduct->is_original == 1
                    ? $existingProduct->id
                    : $existingProduct->parent_product_id;

            } else {

                // ❗ NEW NAME → NEED ADMIN APPROVAL AGAIN
                $articleNumber = null;
                $approvalStatus = 'pending';
                $isOriginal = 1;

                // 🔥 ADD THIS
                $parentProductId = null;
            }
        }


        /* ===============================
        🔥 DETECT IMPORTANT CHANGES
        =============================== */
        $needsApproval = false;

        if ($request->has('name') && strtolower($request->name) != strtolower($product->name)) {
            $needsApproval = true;
        }

        if ($request->has('description') && trim($request->description ?? '') !== trim($product->description ?? '')) {
            $needsApproval = true;
        }

        if ($request->hasFile('images')) {
            $needsApproval = true;
        }

        /* ===============================
        APPROVAL STATUS
        =============================== */
        // $approvalStatus = $product->approval_status;

        if ($needsApproval) {
            $approvalStatus = 'pending';
        }
        /* ===============================
        UPDATE PRODUCT DATA
        =============================== */
        $product->update([
            'name' => $request->name ?? $product->name,
            'description' => $request->description ?? $product->description,
            'price' => $request->price ?? $product->price,
            'discount_price' => $request->discount_price,
            'available_quantity' => $request->available_quantity ?? $product->available_quantity,
            'delivery_available' => $request->delivery_available ?? $product->delivery_available,
            'delivery_price' => $request->delivery_price,
            'delivery_time' => $request->delivery_time,
            'characteristics' => $request->characteristics ? json_encode($request->characteristics) : $product->characteristics,
            'tags' => $request->tags ? json_encode($request->tags) : $product->tags,
            'attributes_json' => $request->attributes_json ?? $product->attributes_json,
            'cost_price' => $request->cost_price,
            'price_before_discount' => $request->price_before_discount,
            'article' => $request->article,
            'weight' => $request->weight,
            'length' => $request->length,
            'width' => $request->width,
            'height' => $request->height,
            'article_number' => $articleNumber,
            'approval_status' => $approvalStatus,
            'is_original' => $isOriginal,
            'parent_product_id' => $parentProductId,
        ]);

         /* ===============================
        UPDATE IMAGES + MAP
        =============================== */
        $imageMap = [];
        $allImages = [];

        if ($request->hasFile('images')) {

            // delete old
            foreach ($product->images as $img) {
                $path = public_path('assets/product_images/' . $img->image);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            ProductImage::where('product_id', $product->id)->delete();

            // upload new
            foreach ($request->file('images') as $index => $file) {

                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('assets/product_images'), $filename);

                $imageId = $request->images_meta[$index]['id'] ?? null;

                if ($imageId) {
                    $imageMap[$imageId] = $filename;
                }

                $allImages[] = $filename;

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $filename,
                    'is_primary' => $index === 0 ? 1 : 0,
                ]);
            }

        } else {
            $allImages = $product->images->pluck('image')->toArray();
        }

        /* ===============================
        UPDATE ATTRIBUTES
        =============================== */
        if ($request->attributes_json) {

            $categoryAttr = DB::table('category_attributes')
                ->where('child_category_id', $product->child_category_id)
                ->first();

            $existing = $categoryAttr
                ? json_decode($categoryAttr->attributes_json, true)
                : [];

            foreach ($request->attributes_json as $attr => $values) {

                foreach ($values as $val) {

                    if (!isset($existing[$attr]) || !in_array($val, $existing[$attr])) {

                        AttributeRequest::firstOrCreate([
                            'vendor_id' => $user->id,
                            'child_category_id' => $product->child_category_id,
                            'attribute_name' => $attr,
                            'attribute_value' => $val
                        ]);
                    }
                }
            }
        }

        /* ===============================
        UPDATE COMBINATIONS (FINAL FIX)
        =============================== */

        // 🔥 Case 1: Only attributes changed → reset combinations
        if ($request->attributes_json && !$request->combinations) {

            ProductCombination::where('product_id', $product->id)->delete();

        }

        // 🔥 Case 2: combinations sent
        elseif ($request->combinations) {

            ProductCombination::where('product_id', $product->id)->delete();

            foreach ($request->combinations as $combo) {

                $comboImages = [];

                if (!empty($combo['image_ids'])) {
                    foreach ($combo['image_ids'] as $imgId) {

                        if (isset($imageMap[$imgId])) {
                            $comboImages[] = $imageMap[$imgId];
                        } else {
                            // fallback from DB
                            $existing = ProductImage::where('product_id', $product->id)
                                ->skip((int) str_replace('img_', '', $imgId) - 1)
                                ->first();

                            if ($existing) {
                                $comboImages[] = $existing->image;
                            }
                        }
                    }
                }

                ProductCombination::create([
                    'product_id' => $product->id,
                    'combination' => json_encode($combo['combination']),
                    'price' => $combo['price'],
                    'stock' => $combo['stock'],
                    'images' => json_encode(!empty($comboImages) ? $comboImages : $allImages),
                ]);
            }
        }

        $product->load('images');

         /* ===============================
        🔔 NOTIFICATIONS
        =============================== */
        if ($needsApproval) {

            $admins = User::where('role', 1)->where('status_id', 1)->get();

            foreach ($admins as $admin) {
                // Save notification in DB
                // Notification::create([
                //     'user_id' => $admin->id,
                //     'title' => '⏳ Product Pending Approval',
                //     'body' => 'Product "' . $product->name . '" requires your review.',
                //     'notification_type' => 21,
                //     'is_read' => 0
                // ]);

                // FCM
                if ($admin->fcm_token) {
                    $tokens = [[
                        'fcm_token' => $admin->fcm_token,
                        'user_id' => $admin->id,
                    ]];

                    $notificationData = [
                        'notification_type' => 21,
                        'title' => '⏳ Product Pending Approval',
                        'body' => 'Product "' . $product->name . '" requires your review.',
                        'product_id' => $product->id,
                    ];

                    (new \App\Services\FCMService())
                        ->sendNotification($tokens, $notificationData, true);
                }
            }
        }
        

        return response()->json([
            'status' => true,
            'message' => __('messages.vendor.product.update.success'),
            'data' => $this->formatProduct($product),
        ], 200);
    }


    public function checkProductName(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user || $user->role != 2 || $user->status_id != 1) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Get all store IDs of this vendor
        $vendorStoreIds = $user->stores()->pluck('id')->toArray();

        // Check if same vendor already has this product name
        $exists = Product::where('name', $request->name)
            ->whereIn('store_id', $vendorStoreIds)
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => false,
                'message' => 'You have already added a product with this name. Please use a different name.',
            ], 422);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Product name is not availabe available.',
        ], 200);
    }


    
    public function analyzeImage(Request $request)
    {
        /* ===============================
        AUTHENTICATED USER
        =============================== */
        $user = Auth::guard('api')->user();
        if (!$user || $user->role != 2 || $user->status_id != 1) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized. Only active vendors can use this feature.',
            ], 403);
        }

        /* ===============================
        VALIDATION
        =============================== */
        $validator = Validator::make($request->all(), [
            'image' => 'required|file|mimes:jpeg,jpg,png,gif|max:5120',
        ], [
            'image.required' => 'Please upload an image.',
            'image.mimes'    => 'Image must be jpeg, jpg, png, or gif.',
            'image.max'      => 'Image must not exceed 5MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        /* ===============================
        PREPARE IMAGE FOR GEMINI
        =============================== */
        $file      = $request->file('image');
        $mimeType  = $file->getMimeType();
        $imageData = base64_encode(file_get_contents($file->getRealPath()));

        /* ===============================
        CALL GEMINI VISION API
        =============================== */
        $apiKey      = config('services.gemini.api_key');
        $modelsToTry = ['gemini-2.5-flash','gemini-2.5-flash-lite'];

        $prompt = <<<PROMPT
        You are an expert ecommerce product copywriter.

        Analyze this product image carefully and respond ONLY in the following JSON format (no extra text).
        Write the name and description in Russian language (Русский).
        Description must be maximum 100 words only.

        {
        "name": "<короткое название товара, максимум 10 слов, для интернет-магазина>",
        "description": "<описание товара, максимум 100 слов>"
        }
    PROMPT;

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data'      => $imageData,
                            ],
                        ],
                        [
                            'text' => $prompt,
                        ],
                    ],
                ],
            ],
           'generationConfig' => [
                'temperature'     => 0.4,
                'maxOutputTokens' => 2048,
            ],
        ];

        try {
            $response  = null;
            $lastError = null;

            /* ===============================
            TRY EACH MODEL (FALLBACK)
            =============================== */
            foreach ($modelsToTry as $model) {
                $res = Http::timeout(30)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                        $payload
                    );

                if ($res->successful()) {
                    $response = $res;
                    break; // ✅ success, stop trying
                }

                $lastError = $res->body(); // save error, try next model
            }

            /* ===============================
            ALL MODELS FAILED
            =============================== */
            if (!$response) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Gemini API error: ' . $lastError,
                ], 500);
            }

            /* ===============================
            PARSE RESPONSE
            =============================== */
            $responseBody = $response->json();

            $rawText = $responseBody['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$rawText) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Could not extract response from Gemini.',
                ], 500);
            }

            // Strip markdown code fences if present (```json ... ```)
            $cleanText = preg_replace('/^```(?:json)?\s*/i', '', trim($rawText));
            $cleanText = preg_replace('/\s*```$/', '', $cleanText);

            $parsed = json_decode($cleanText, true);

            if (!$parsed || !isset($parsed['name'], $parsed['description'])) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Gemini returned unexpected format.',
                    'raw'     => $rawText,
                ], 500);
            }

            /* ===============================
            SUCCESS RESPONSE
            =============================== */
            return response()->json([
                'status'  => true,
                'message' => 'Image analyzed successfully.',
                'data'    => [
                    'name'        => $parsed['name'],
                    'description' => $parsed['description'],
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

}
