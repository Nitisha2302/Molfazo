<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\SubCategory;
use Str;
use App\Models\ChildCategory;



class CategoryController extends Controller
{
   public function categoryListing(Request $request)
    {
        $categories = Category::query()
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->status_filter, function ($q) use ($request) {
                $q->where('status_id', $request->status_filter);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // keeps search/status filters on pagination links

        return view('admin.categories.categoriesListing', compact('categories'));
    }


    public function createCategory()
    {
        return view('admin.categories.addCategories');
    }

    public function storeCategory(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string|max:255|unique:categories,name',
            ],
            [
                'name.required' => 'Category name is required.',
                'name.string'   => 'Category name must be a valid text.',
                'name.max'      => 'Category name cannot exceed 255 characters.',
                'name.unique'   => 'This category already exists.',
            ]
        );

        Category::create([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
        ]);

        return redirect()
            ->route('dashboard.admin.categories')
            ->with('success', 'Category added successfully.');
    }


    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.editCategory', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate(
            [
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
                'status_id' => 'required|in:1,2',
            ],
            [
                'name.required' => 'Category name is required.',
                'name.string'   => 'Category name must be text.',
                'name.max'      => 'Category name cannot exceed 255 characters.',
                'name.unique'   => 'This category already exists.',
                'status_id.required' => 'Status is required.',
                'status_id.in'       => 'Invalid status selected.',
            ]
        );

        $category->update([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'status_id' => $request->status_id,
        ]);
         //  IMPORTANT LOGIC
            // If category is inactive → make all sub-categories inactive
            if ($request->status_id == 2) {
                $category->subCategories()->each(function ($sub) {
                    $sub->update(['status_id' => 2]);
                    $sub->childCategories()->update(['status_id' => 2]);
                });
            }


        return redirect()
            ->route('dashboard.admin.categories')
            ->with('success', 'Category updated successfully.');
    }

    public function destroyCategory($id)
    {
        Category::findOrFail($id)->delete();
        return back()->with('success','Category deleted');
    }

     // Listing sub-categories
    public function subCategoryListing(Request $request)
    {
        $subCategories = SubCategory::with('category')
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->category_filter, function ($q) use ($request) {
                $q->where('category_id', $request->category_filter);
            })
            ->when($request->status_filter, function ($q) use ($request) {
                $q->where('status_id', $request->status_filter);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // keep filters on pagination

        $categories = Category::where('status_id', 1)->pluck('name', 'id'); // for dropdown

        return view('admin.subcategories.subcategoriesListing', compact('subCategories','categories'));
    }

    // Create page
    public function createSubCategory()
    {
        $categories = Category::where('status_id', 1)->pluck('name', 'id');
        return view('admin.subcategories.addSubCategory', compact('categories'));
    }

    /**
     * Generate a unique slug for SubCategory
     */
    private function generateUniqueSubCategorySlug($name, $id = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        // Keep checking if slug exists (excluding current id for updates)
        while (SubCategory::where('slug', $slug)->when($id, fn($q) => $q->where('id', '!=', $id))->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }


    // Store sub-category
    public function storeSubCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:sub_categories,name,NULL,id,category_id,' . $request->category_id,
        ], [
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'Selected category is invalid.',
            'name.required' => 'Sub-category name is required.',
            'name.unique' => 'This sub-category already exists in the selected category.',
        ]);

        SubCategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
           'slug' => $this->generateUniqueSubCategorySlug($request->name),
            'status_id' => 1,
        ]);

        return redirect()->route('dashboard.admin.subcategories')->with('success','Sub-category added successfully.');
    }

    // Edit sub-category page
    public function editSubCategory($id)
    {
        $subCategory = SubCategory::findOrFail($id);
        $categories = Category::where('status_id', 1)->pluck('name', 'id');
        return view('admin.subcategories.editSubCategory', compact('subCategory','categories'));
    }

    // Update sub-category
    public function updateSubCategory(Request $request, $id)
    {
        $subCategory = SubCategory::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:sub_categories,name,' . $id . ',id,category_id,' . $request->category_id,
        ], [
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'Selected category is invalid.',
            'name.required' => 'Sub-category name is required.',
            'name.unique' => 'This sub-category already exists in the selected category.',
        ]);

        $subCategory->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
           'slug' => $this->generateUniqueSubCategorySlug($request->name, $subCategory->id),

        ]);

        if ($request->status_id == 2) {
            $subCategory->childCategories()->update([
                'status_id' => 2
            ]);
        }


        return redirect()->route('dashboard.admin.subcategories')->with('success','Sub-category updated successfully.');
    }

    // Delete sub-category
    public function destroySubCategory($id)
    {
        SubCategory::findOrFail($id)->delete();
        return back()->with('success','Sub-category deleted successfully.');
    }


    
    //////////////////////////////
    // CHILD CATEGORY SLUG
    //////////////////////////////
    private function generateUniqueChildCategorySlug($name, $id = null)
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (
            ChildCategory::where('slug', $slug)
                ->when($id, fn ($q) => $q->where('id', '!=', $id))
                ->exists()
        ) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }

    //////////////////////////////
    // LIST
    //////////////////////////////
    public function childCategoryListing(Request $request)
    {
        $childCategories = ChildCategory::with('subCategory.category')
            ->when($request->search, fn ($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
            )
            ->when($request->sub_category_filter, fn ($q) =>
                $q->where('sub_category_id', $request->sub_category_filter)
            )
            ->when($request->status_filter, fn ($q) =>
                $q->where('status_id', $request->status_filter)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $subCategories = SubCategory::where('status_id', 1)->pluck('name', 'id');

        return view(
            'admin.childcategories.childCategoriesListing',
            compact('childCategories', 'subCategories')
        );
    }

    //////////////////////////////
    // CREATE
    //////////////////////////////
    public function createChildCategory()
    {
        $subCategories = SubCategory::where('status_id', 1)->pluck('name', 'id');
        return view('admin.childcategories.addChildCategory', compact('subCategories'));
    }

    //////////////////////////////
    // STORE
    //////////////////////////////
    public function storeChildCategory(Request $request)
    {
        $rules = [
            'sub_category_id' => 'required|exists:sub_categories,id',
            'name' => 'required|string|max:255|unique:child_categories,name,NULL,id,sub_category_id,' . $request->sub_category_id,
            'status_id' => 'required|in:1,2',
        ];

        $messages = [
            'sub_category_id.required' => 'Please select a sub category.',
            'name.required' => 'Child category name is required.',
            'name.unique' => 'This child category already exists for selected sub category.',
            'status_id.required' => 'Please select status.',
        ];

        $request->validate($rules, $messages);

        ChildCategory::create([
            'sub_category_id' => $request->sub_category_id,
            'name' => $request->name,
            'slug' => $this->generateUniqueChildCategorySlug($request->name),
            'status_id' => $request->status_id,
        ]);

        return redirect()
            ->route('dashboard.admin.childcategories')
            ->with('success', 'Child category added successfully.');
    }

    //////////////////////////////
    // EDIT
    //////////////////////////////
    public function editChildCategory($id)
    {
        $childCategory = ChildCategory::findOrFail($id);
        $subCategories = SubCategory::where('status_id', 1)->pluck('name', 'id');

        return view(
            'admin.childcategories.editChildCategory',
            compact('childCategory', 'subCategories')
        );
    }

    //////////////////////////////
    // UPDATE
    //////////////////////////////
    public function updateChildCategory(Request $request, $id)
    {
        $childCategory = ChildCategory::findOrFail($id);

        $request->validate([
            'sub_category_id' => 'required|exists:sub_categories,id',
            'name' => 'required|string|max:255|unique:child_categories,name,' . $id . ',id,sub_category_id,' . $request->sub_category_id,
            'status_id' => 'required|in:1,2',
        ]);

        $childCategory->update([
            'sub_category_id' => $request->sub_category_id,
            'name' => $request->name,
            'slug' => $this->generateUniqueChildCategorySlug($request->name, $id),
            'status_id' => $request->status_id,
        ]);

        return redirect()
            ->route('dashboard.admin.childcategories')
            ->with('success', 'Child category updated successfully.');
    }

    //////////////////////////////
    // DELETE
    //////////////////////////////
    public function destroyChildCategory($id)
    {
        ChildCategory::findOrFail($id)->delete();
        return back()->with('success', 'Child category deleted successfully.');
    }







}
