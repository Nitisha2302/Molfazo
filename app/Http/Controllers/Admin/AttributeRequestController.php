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

        $attributes = $categoryAttr->attributes_json ?? [];

        if(!isset($attributes[$requestAttr->attribute_name])){
            $attributes[$requestAttr->attribute_name] = [];
        }

        if(!in_array($requestAttr->attribute_value,$attributes[$requestAttr->attribute_name])){
            $attributes[$requestAttr->attribute_name][] = $requestAttr->attribute_value;
        }

        $categoryAttr->update([
            'attributes_json' => $attributes
        ]);

        $requestAttr->update([
            'status' => 'approved'
        ]);

        return back()->with('success','Attribute approved successfully.');
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

            if(isset($attributes[$requestAttr->attribute_name])){

                $attributes[$requestAttr->attribute_name] = array_filter(
                    $attributes[$requestAttr->attribute_name],
                    function($val) use ($requestAttr){
                        return $val != $requestAttr->attribute_value;
                    }
                );

                $attributes[$requestAttr->attribute_name] = array_values($attributes[$requestAttr->attribute_name]);

                $categoryAttr->update([
                    'attributes_json' => $attributes
                ]);
            }
        }

        $requestAttr->update([
            'status' => 'rejected'
        ]);

        return back()->with('success','Attribute rejected and removed.');
    }

}