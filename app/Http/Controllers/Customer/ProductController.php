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

        // Pagination
        $perPage = $request->get('per_page', 10);
        $products = $query->paginate($perPage);

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
            ], 404);
        }

        // Related products pagination
        $perPage = $request->get('related_per_page', 5); // default 5
        $page = $request->get('related_page', 1); // default first page

        $relatedQuery = Product::with(['primaryImage'])
            ->where('status_id', 1)
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id);

        $relatedProducts = $relatedQuery->paginate($perPage, ['*'], 'related_page', $page);

        // Optional: full URL for primary image
        $relatedProducts->getCollection()->transform(function ($item) {
            if ($item->primaryImage) {
                $item->primaryImage->image =  $item->primaryImage->image;
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

}
