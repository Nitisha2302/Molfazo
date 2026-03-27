<?php

// app/Http/Controllers/Vendor/CategoryController.php
namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\CategoryAttribute;
use App\Models\Banner;
use App\Models\City;


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
              'image' => $cat->image 
                ? asset($cat->image)
                : null,

                'sub_categories' => $cat->subCategories->map(function ($sub) {
                    return [
                        'id'   => $sub->id,
                        'name' => $sub->name,
                        'slug' => $sub->slug,
                         'image' => $sub->image,
                        'child_categories' => $sub->childCategories->map(function ($child) {
                            return [
                                'id'   => $child->id,
                                'name' => $child->name,
                                'slug' => $child->slug,
                                'image' => $child->image,
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


    public function subcategories($category_id)
    {
        $subCategories = SubCategory::where('category_id', $category_id)
            ->where('status_id', 1)
            ->with(['childCategories' => function ($q) {
                $q->where('status_id', 1);
            }])
            ->get();

        if ($subCategories->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No subcategories found.',
            ], 404);
        }

        $data = $subCategories->map(function ($sub) {
            return [
                'id'   => $sub->id,
                'name' => $sub->name,
                'slug' => $sub->slug,
                 'image' => $sub->image,
                'child_categories' => $sub->childCategories->map(function ($child) {
                    return [
                        'id'   => $child->id,
                        'name' => $child->name,
                        'slug' => $child->slug,
                         'image' => $child->image,
                    ];
                }),
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Sub categories fetched successfully.',
            'data'    => $data,
        ], 200);
    }


    public function childCategories($sub_category_id)
    {
        $childCategories = ChildCategory::where('sub_category_id', $sub_category_id)
            ->where('status_id', 1)
            ->get();

        if ($childCategories->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No child categories found.',
            ], 404);
        }

        $data = $childCategories->map(function ($child) {
            return [
                'id'   => $child->id,
                'name' => $child->name,
                'slug' => $child->slug,
                'image' => $child->image,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Child categories fetched successfully.',
            'data'    => $data,
        ], 200);
    }


    public function getAttributeByChildCategory($child_category_id)
    {
        $record = CategoryAttribute::where('child_category_id', $child_category_id)
            ->first();

        if (!$record) {
            return response()->json([
                'status' => true,
                'data' => [],
                'message' => 'No attributes found for this category.'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Attributes fetched successfully.',
            'data' => $this->formatAttributes($record->attributes_json),
        ]);
    }

    private function formatAttributes(array $attributes)
    {
        $result = [];

        foreach ($attributes as $name => $values) {
            $result[] = [
                'name' => $name,
                'values' => array_values($values)
            ];
        }

        return $result;
    }


    public function getBanners(Request $request)
    {
        $query = Banner::where('status', 1);

        if ($request->filled('city')) {

            $city = City::where('name', $request->city)->first();

            // If city not found return empty
            if (!$city) {
                return response()->json([
                    'status' => true,
                    'message' => 'No banners found for this city',
                    'data' => []
                ]);
            }

            $query->whereJsonContains('cities', (string)$city->id);
        }

        // Filter by link_type
        if ($request->filled('link_type')) {
            $query->where('link_type', $request->link_type);
        }

        // Filter by link_id (JSON column)
        if ($request->filled('link_id')) {
            $query->whereJsonContains('link_ids', (string)$request->link_id);
        }

        $cities = City::pluck('name','id');

        $banners = $query->latest()->get()->map(function ($banner) use ($cities) {

            $cityNames = collect($banner->cities)
                ->map(fn($id) => $cities[$id] ?? null)
                ->filter()
                ->values();

            return [
                'id' => $banner->id,
                'title' => $banner->title,
                'image' => $banner->image ? $banner->image : null,
                'cities' => $cityNames,
                 'type' => $banner->link_type,
            'linked_data' => $banner->link_ids,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Banners fetched successfully',
            'data' => $banners
        ]);
    }

     public function getCities()
    {
        $cities = City::where('status', 1)
                    ->orderBy('name')
                    ->get(['id','name']);

        return response()->json([
            'status' => true,
            'message' => 'Cities fetched successfully',
            'data' => $cities
        ]);
    }

    

}
