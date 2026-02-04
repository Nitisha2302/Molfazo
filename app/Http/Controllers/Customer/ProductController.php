<?php

namespace App\Http\Controllers\Customer; // <--- IMPORTANT

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // List products with filters and sorting
    public function list(Request $request)
    {
        $query = Product::with(['store', 'category', 'subCategory', 'childCategory', 'primaryImage']);

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

        // Search by product name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Only active products
        $query->where('status_id', 1);

        // Sorting
        if ($request->has('sort')) {
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
            // Default: latest first
            $query->orderBy('id', 'desc');
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

        $products = $products->map(function ($product) {

            $product->primaryimage = optional($product->primaryImage)->image;

            // remove relation object
            unset($product->primaryImage);

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
        // Get main product
        $product = Product::with(['store', 'category', 'subCategory', 'childCategory', 'images'])
                    ->where('id', $id)
                    ->where('status_id', 1)
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

        // 🔥 Related products (NO PAGINATION)
        $relatedProducts = Product::with('primaryImage')
            ->where('status_id', 1)
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->get();

        // 🔥 Transform related products primary image
        $relatedProducts = $relatedProducts->map(function ($item) {
            $item->primaryimage = optional($item->primaryImage)->image;
            unset($item->primaryImage);
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
        $query = Product::with(['store', 'category', 'subCategory', 'childCategory', 'primaryImage'])
            ->where('status_id', 1);

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
        $products = $products->map(function ($product) {
            $product->primaryimage = optional($product->primaryImage)->image;
            unset($product->primaryImage);
            return $product;
        });

        return response()->json([
            'status' => true,
            'message' => 'Search results fetched successfully.',
            'data' => $products
        ], 200);
    }


}
