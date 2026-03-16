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
use App\Models\Bank;
use App\Models\ProductBank;
use App\Models\ProductCombination;
use Auth;
use Validator;
use Carbon\Carbon;
use App\Models\AttributeRequest;
use Illuminate\Support\Facades\DB;



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
                'message' => 'Vendor account is not approved or authenticated.',
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
            'images.*' => 'file|mimes:jpeg,jpg,png,gif',
            'attributes_json' => 'nullable|array',
            'variant_images' => 'nullable|array',
           'variant_images.*.*' => 'image|mimes:jpeg,jpg,png,gif',

            'cost_price' => 'nullable|numeric',
             'price_before_discount' => 'nullable|numeric',
            
            'article' => 'nullable|string',
            'weight' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
        ], [
            'store_id.required' => 'Please select a store.',
            'store_id.exists' => 'The selected store does not exist.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category does not exist.',
            'sub_category_id.required' => 'Please select a subcategory.',
            'sub_category_id.exists' => 'The selected subcategory does not exist.',
            'child_category_id.required' => 'Please select a child category.',
            'child_category_id.exists' => 'The selected child category does not exist.',
            'name.required' => 'Product name is required.',
            'price.required' => 'Product price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'discount_price.numeric' => 'Discount price must be a valid number.',
            'available_quantity.required' => 'Available quantity is required.',
            'available_quantity.integer' => 'Available quantity must be an integer.',
            'characteristics.array' => 'Characteristics must be sent as an array.',
            'tags.array' => 'Tags must be sent as an array.',
            'images.required' => 'At least one image is required.',
            'images.array' => 'Images must be sent as an array.',
            'images.*.mimes' => 'Each image must be jpeg, jpg, png, or gif.',
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
                'message' => 'Invalid or unapproved store.',
            ], 403);
        }

        if ($request->child_category_id) {
            $childCategory = ChildCategory::where('id', $request->child_category_id)
                ->where('sub_category_id', $request->sub_category_id)
                ->first();

            if (!$childCategory) {
                return response()->json([
                    'status' => false,
                    'message' => 'Child category does not belong to selected sub-category.',
                ], 422);
            }
        }

       $variantData = $request->variants ?? $request->attributes_json;

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
            // 'attributes_json' => $request->attributes_json ? $request->attributes_json : null,
            'status_id' => 1, // Active

            'cost_price' => $request->cost_price,
             'price_before_discount' => $request->price_before_discount,
            
            'article' => $request->article,
            'weight' => $request->weight,
            'length' => $request->length,
            'width' => $request->width,
            'height' => $request->height,
        ]);

       

        if($variantData){

            $categoryAttr = \DB::table('category_attributes')
                ->where('child_category_id',$request->child_category_id)
                ->first();

            $existingAttributes = [];

            if($categoryAttr){
                $existingAttributes = json_decode($categoryAttr->attributes_json ?? '{}', true);
            }

            foreach($variantData as $attrName=>$values){

                if(!isset($existingAttributes[$attrName])){

                    foreach($values as $val){

                        AttributeRequest::firstOrCreate([
                            'vendor_id'=>$user->id,
                            'child_category_id'=>$request->child_category_id,
                            'attribute_name'=>$attrName,
                            'attribute_value'=>$val
                        ]);

                    }

                }else{

                    foreach($values as $val){

                        if(!in_array($val,$existingAttributes[$attrName])){

                            AttributeRequest::firstOrCreate([
                                'vendor_id'=>$user->id,
                                'child_category_id'=>$request->child_category_id,
                                'attribute_name'=>$attrName,
                                'attribute_value'=>$val
                            ]);

                        }

                    }

                }

            }

             /* ===============================
            GENERATE COMBINATIONS
            =============================== */

            $combinations = $this->generateCombinations($variantData);

            $variantImages = $request->file('variant_images');

            foreach ($combinations as $index => $combo) {

                $images = [];

                if (isset($variantImages[$index])) {

                    foreach ($variantImages[$index] as $file) {

                        $filename = time() . '_' . $file->getClientOriginalName();

                        $file->move(public_path('assets/variant_images'), $filename);

                        $images[] =  $filename;
                    }
                }

                ProductCombination::create([
                    'product_id' => $product->id,
                    'combination' => json_encode($combo),
                    'description' => $request->description ?? null,
                    'price' => $request->price,
                    'price_before_discount' => $request->price_before_discount,
                    'cost_price' => $request->cost_price,
                    'stock' => $request->available_quantity,
                    'images' => json_encode($images)
                ]);
            }
        }


        /* ===============================
           UPLOAD PRODUCT IMAGES
        =============================== */
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/product_images'), $filename);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $filename,
                    'is_primary' => $index === 0 ? 1 : 0,
                ]);
            }
        }

        // Reload relationship so $product->images is a Collection
        $product->load('images');

        return response()->json([
            'status' => true,
            'message' => 'Product added successfully.',
            'data' => $this->formatProduct($product),
        ], 200);
    }

    /**
     * List all products for the vendor
     */
    public function list()
    {
        $user = Auth::guard('api')->user();
        if (!$user || $user->role != 2 || $user->status_id != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Vendor account is not approved or authenticated.',
            ], 403);
        }

        $products = Product::whereHas('store', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get();

        // $products = Product::with([
        //     'images',
        //     'combinations',
        //     'category',
        //     'subCategory',
        //     'childCategory'
        // ])->whereHas('store', function ($q) use ($user) {
        //     $q->where('user_id', $user->id);
        // })->get();

        $products = $products->map(function ($product) {
            return $this->formatProduct($product);
        });

        return response()->json([
            'status' => true,
              'message' => 'Products fetched successfully.',
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
                'message' => 'Vendor account is not approved or authenticated.',
            ], 403);
        }

        $product = Product::where('id', $id)
            ->whereHas('store', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->first();

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        return response()->json([
            'status' => true,
              'message' => 'Products details fetched successfully.',
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
        $products = Product::where('store_id', $store->id)->get();

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
              'message' => 'Products fetched successfully.',
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
            'message' => 'Bank list fetched successfully.',
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


    // Size = L,XL
    // Color = Red,Black

    //output

    // L + Red
    // L + Black
    // XL + Red
    // XL + Black



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

                $image->move(public_path('assets/variant_images'), $name);

                $images[] = $name;
            }

            // Save images as JSON
            $combo->images = json_encode($images);
        }

        $combo->save();

        return response()->json([
            'status' => true,
            'message' => 'Combination updated successfully',
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
            'message' => 'Combination deleted successfully'
        ]);
    }


    // public function copyProduct($id)
    // {

    //     $user = Auth::guard('api')->user();

    //     $product = Product::findOrFail($id);

    //     $newProduct = $product->replicate();

    //     $store = $user->stores()->first();

    //     if(!$store){
    //         return response()->json([
    //             'status'=>false,
    //             'message'=>'Vendor has no store'
    //         ]);
    //     }

    //     $newProduct->store_id = $store->id;

    //     $newProduct->save();

    //     foreach($product->images as $image){

    //     ProductImage::create([

    //     'product_id'=>$newProduct->id,

    //     'image'=>$image->image

    //     ]);

    //     }

    //     // foreach($product->combinations as $combo){

    //     // ProductCombination::create([

    //     // 'product_id'=>$newProduct->id,

    //     // 'combination'=>$combo->combination,

    //     // 'price'=>$combo->price,

    //     // 'stock'=>$combo->stock

    //     // ]);

    //     // }

    //     foreach($product->combinations as $combo){

    //         ProductCombination::create([

    //             'product_id'=>$newProduct->id,

    //             'combination'=>$combo->combination,

    //             'price'=>$combo->price,

    //             'stock'=>$combo->stock,

    //             'images'=>$combo->images

    //         ]);

    //     }

    //     return response()->json([

    //     'status'=>true,

    //     'message'=>'Product copied successfully'

    //     ]);

    // }

  public function copyProduct($id)
    {
        $user = Auth::guard('api')->user();

        // Check if vendor has at least one store
        $store = $user->stores()->first();
        if (!$store) {
            return response()->json([
                'status' => false,
                'message' => 'Vendor has no store'
            ]);
        }

        DB::beginTransaction();

        try {
            // Load product with images and combinations
            $product = Product::with(['images', 'combinations'])->findOrFail($id);

            // Replicate main product
            $newProduct = $product->replicate();
            $newProduct->store_id = $store->id;

            // Optional: create a new unique slug
            $newProduct->slug = $product->slug . '-' . time();

            $newProduct->save();

            // Copy product images
            foreach ($product->images as $image) {
                ProductImage::create([
                    'product_id' => $newProduct->id,
                    'image' => $image->image
                ]);
            }

            // Copy product combinations / variants
            foreach ($product->combinations as $combo) {
                ProductCombination::create([
                    'product_id' => $newProduct->id,
                    'combination' => $combo->combination,
                    'description' => $combo->description,
                    'price' => $combo->price,
                    'price_before_discount' => $combo->price_before_discount,
                    'cost_price' => $combo->cost_price,
                    'stock' => $combo->stock,
                    'images' => $combo->images
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product copied successfully',
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


}
