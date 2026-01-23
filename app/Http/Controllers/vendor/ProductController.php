<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Store;
use App\Models\ChildCategory;
use App\Models\ProductImage;
use Auth;
use Validator;

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
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'child_category_id' => 'required|exists:child_categories,id',
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

        $childCategory = ChildCategory::where('id', $request->child_category_id)
            ->where('sub_category_id', $request->sub_category_id)
            ->first();

        if (!$childCategory) {
            return response()->json([
                'status' => false,
                'message' => 'Child category does not belong to selected sub-category.',
            ], 422);
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
            'status_id' => 1, // Active
        ]);

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
            'status_name' => $product->status_id,
            'images' => $product->images->map(function ($img) {
                return [
                    'id' => $img->id,
                    'image' =>  $img->image,
                    'color' => $img->color,
                    'is_primary' => (bool)$img->is_primary,
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


}
