<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttributeRequest;
use App\Models\CategoryAttribute;

class AttributeRequestController extends Controller
{

   public function index(Request $request)
    {
        $query = AttributeRequest::with(['vendor','childCategory']);

        // Filter by child category name
        if ($request->filled('category')) {
            $query->whereHas('childCategory', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->category . '%');
            });
        }

        // Filter by attribute name
        if ($request->filled('attribute')) {
            $query->where('attribute_name', 'like', '%' . $request->attribute . '%');
        }

        $requests = $query->latest()->paginate(10)->withQueryString();

        return view('admin.attribute_requests.index', compact('requests'));
    }

    public function approve($id)
    {
        $requestAttr = AttributeRequest::findOrFail($id);

        $categoryAttr = CategoryAttribute::where(
            'child_category_id',
            $requestAttr->child_category_id
        )->first();

        // ✅ handle null
        $attributes = $categoryAttr->attributes_json ?? [];

        $attrName = $requestAttr->attribute_name;
        $attrValue = $requestAttr->attribute_value;

        // ✅ create attribute if not exist
        if (!isset($attributes[$attrName])) {
            $attributes[$attrName] = [];
        }

        // ✅ avoid duplicate (case-insensitive)
        $existingValues = array_map('strtolower', $attributes[$attrName]);

        if (!in_array(strtolower($attrValue), $existingValues)) {
            $attributes[$attrName][] = $attrValue;
        }

        // ✅ create if not exist
        if (!$categoryAttr) {
            CategoryAttribute::create([
                'child_category_id' => $requestAttr->child_category_id,
                'attributes_json' => $attributes
            ]);
        } else {
            $categoryAttr->update([
                'attributes_json' => $attributes
            ]);
        }

        $requestAttr->update(['status' => 'approved']);

        return back()->with('success', 'Attribute approved successfully.');
    }

    public function reject($id)
    {
        $requestAttr = AttributeRequest::findOrFail($id);

        $categoryAttr = CategoryAttribute::where(
            'child_category_id',
            $requestAttr->child_category_id
        )->first();

        if ($categoryAttr) {

            $attributes = $categoryAttr->attributes_json ?? [];

            $attrName = $requestAttr->attribute_name;
            $attrValue = $requestAttr->attribute_value;

            if (isset($attributes[$attrName])) {

                // ✅ remove value
                $attributes[$attrName] = array_filter(
                    $attributes[$attrName],
                    function ($val) use ($attrValue) {
                        return strtolower($val) != strtolower($attrValue);
                    }
                );

                $attributes[$attrName] = array_values($attributes[$attrName]);

                // 🔥 remove attribute if empty
                if (empty($attributes[$attrName])) {
                    unset($attributes[$attrName]);
                }

                $categoryAttr->update([
                    'attributes_json' => $attributes
                ]);
            }
        }

        $requestAttr->update(['status' => 'rejected']);

        return back()->with('success', 'Attribute rejected and removed.');
    }

}