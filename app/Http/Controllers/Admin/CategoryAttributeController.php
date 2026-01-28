<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryAttribute;
use App\Models\ChildCategory;
use Illuminate\Http\Request;

class CategoryAttributeController extends Controller
{
    /* ===============================
       LIST ATTRIBUTES
    =============================== */
    public function listing(Request $request)
    {
        $attributes = CategoryAttribute::with('childCategory.subCategory.category')
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('childCategory', function ($qq) use ($request) {
                    $qq->where('name', 'like', '%' . $request->search . '%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.attributes.attributesListing', compact('attributes'));
    }

    /* ===============================
       CREATE PAGE
    =============================== */
    public function create()
    {
        $childCategories = ChildCategory::where('status_id', 1)
            ->pluck('name', 'id');

        return view('admin.attributes.addAttribute', compact('childCategories'));
    }

    /* ===============================
       STORE
    =============================== */
    public function store(Request $request)
    {
        $request->validate([
            'child_category_id' => 'required|exists:child_categories,id|unique:category_attributes,child_category_id',
            'attributes_json'   => 'required|json',
        ], [
            'child_category_id.required' => 'Please select a child category.',
            'child_category_id.exists'   => 'Selected child category is invalid.',
            'child_category_id.unique'   => 'Attributes are already defined for this child category.',

            'attributes_json.required'   => 'Please add at least one attribute.',
            'attributes_json.json'       => 'Invalid JSON format. Example: {"color":["Red","Blue"]}',
        ]);

        // 🔥 Decode JSON ONCE
        $attributes = json_decode($request->attributes_json, true);

        CategoryAttribute::create([
            'child_category_id' => $request->child_category_id,
            'attributes_json'   => $attributes,
        ]);

        return redirect()
            ->route('dashboard.admin.attributes')
            ->with('success', 'Attributes added successfully.');
    }

    /* ===============================
       EDIT PAGE
    =============================== */
    public function edit($id)
    {
        $attribute = CategoryAttribute::findOrFail($id);

        $childCategories = ChildCategory::where('status_id', 1)
            ->pluck('name', 'id');

        return view(
            'admin.attributes.editAttribute',
            compact('attribute', 'childCategories')
        );
    }

    /* ===============================
       UPDATE
    =============================== */
    public function update(Request $request, $id)
    {
        $attribute = CategoryAttribute::findOrFail($id);

        $request->validate([
            'child_category_id' =>
                'required|exists:child_categories,id|unique:category_attributes,child_category_id,' . $id,
            'attributes_json' => 'required|json',
        ], [
            'child_category_id.required' => 'Please select a child category.',
            'child_category_id.exists'   => 'Selected child category is invalid.',
            'child_category_id.unique'   => 'Another child category already has attributes assigned.',

            'attributes_json.required'   => 'Attribute list cannot be empty.',
            'attributes_json.json'       => 'Invalid JSON structure detected.',
        ]);

        // 🔥 Decode JSON ONCE
        $attributes = json_decode($request->attributes_json, true);

        $attribute->update([
            'child_category_id' => $request->child_category_id,
            'attributes_json'   => $attributes,
        ]);

        return redirect()
            ->route('dashboard.admin.attributes')
            ->with('success', 'Attributes updated successfully.');
    }
}
