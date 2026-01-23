<?php

// app/Http/Controllers/Vendor/CategoryController.php
namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;


class CategoryController extends Controller
{
    // List all active categories
    public function categories()
    {
        $categories = Category::where('status_id', 1)
            ->with([
                'subCategories' => function ($q) {
                    $q->where('status_id', 1)
                    ->with(['childCategories' => function ($q2) {
                        $q2->where('status_id', 1);
                    }]);
                }
            ])
            ->get();

        $data = $categories->map(function ($cat) {
            return [
                'id'   => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'sub_categories' => $cat->subCategories->map(function ($sub) {
                    return [
                        'id'   => $sub->id,
                        'name' => $sub->name,
                        'slug' => $sub->slug,
                        'child_categories' => $sub->childCategories->map(function ($child) {
                            return [
                                'id'   => $child->id,
                                'name' => $child->name,
                                'slug' => $child->slug,
                            ];
                        }),
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => true,
              'message' => 'Category successfully fetched.',
            'data'   => $data,
        ], 200);
    }


    // Get subcategories by category ID
    public function subcategories($category_id)
    {
        $subCategories = SubCategory::where('category_id', $category_id)
            ->where('status_id', 1)
            ->get();

        if ($subCategories->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No subcategories found for this category.',
            ], 404);
        }

        $data = $subCategories->map(function ($sub) {
            return [
                'id'   => $sub->id,
                'name' => $sub->name,
                'slug' => $sub->slug,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Category successfully fetched.',
            'data'   => $data,
        ], 200);
    }


    // Get child categories by sub-category ID
    public function childCategories($sub_category_id)
    {
        $subCategory = SubCategory::where('id', $sub_category_id)
            ->where('status_id', 1)
            ->first();

        if (!$subCategory) {
            return response()->json([
                'status' => false,
                'message' => 'Sub category not found.'
            ], 404);
        }

        $childCategories = $subCategory->childCategories()
            ->where('status_id', 1)
            ->get();

        $data = $childCategories->map(function ($child) {
            return [
                'id'   => $child->id,
                'name' => $child->name,
                'slug' => $child->slug,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Category successfully fetched.',
            'data'   => $data
        ], 200);
    }

}
