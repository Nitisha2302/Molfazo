<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /* =========================
       GET ALL CATEGORIES
    ========================= */
   public function categories()
    {
        $categories = Category::where('status_id', 1)
            ->with([
                'subCategories' => function ($q) {
                    $q->where('status_id', 1)
                    ->orderBy('id', 'desc')
                    ->with([
                        'childCategories' => function ($q2) {
                            $q2->where('status_id', 1)
                                ->orderBy('id', 'desc');
                        }
                    ]);
                }
            ])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Categories retrieved successfully.',
            'data'    => $categories
        ], 200);
    }



    /* =========================
       GET SUBCATEGORIES BY CATEGORY
    ========================= */
   public function subCategories($categoryId)
    {
        $subCategories = SubCategory::where('category_id', $categoryId)
            ->where('status_id', 1)
            ->with(['childCategories' => function ($q) {
                $q->where('status_id', 1)
                ->orderBy('id', 'desc');
            }])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Subcategories retrieved successfully.',
            'data'    => $subCategories
        ], 200);
    }


    /* =========================
       GET CHILD CATEGORIES BY SUBCATEGORY
    ========================= */
    public function childCategories($subCategoryId)
    {
        $childCategories = ChildCategory::where('sub_category_id', $subCategoryId)
            ->where('status_id', 1)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Child categories retrieved successfully.',
            'data'    => $childCategories
        ], 200);
    }

    
}
