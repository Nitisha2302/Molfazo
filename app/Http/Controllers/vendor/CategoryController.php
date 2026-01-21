<?php

// app/Http/Controllers/Vendor/CategoryController.php
namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;

class CategoryController extends Controller
{
    // List all active categories
    public function categories()
    {
        $categories = Category::where('status_id', 1)
                        ->with(['subCategories' => function($q){
                            $q->where('status_id', 1);
                        }])
                        ->get();

        $data = $categories->map(function($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'sub_categories' => $cat->subCategories->map(function($sub){
                    return [
                        'id' => $sub->id,
                        'name' => $sub->name,
                        'slug' => $sub->slug
                    ];
                })
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    // Get subcategories by category ID
    public function subcategories($category_id)
    {
        $category = Category::where('id', $category_id)->where('status_id', 1)->first();
        if(!$category){
            return response()->json([
                'status' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        $subCategories = $category->subCategories()->where('status_id', 1)->get();

        $data = $subCategories->map(function($sub){
            return [
                'id' => $sub->id,
                'name' => $sub->name,
                'slug' => $sub->slug
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }
}
