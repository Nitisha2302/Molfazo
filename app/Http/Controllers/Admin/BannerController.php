<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\DB;
use App\Models\Store;
use App\Models\Product;

class BannerController extends Controller
{
    // List banners
    // public function index(Request $request)
    // {
    //     $query = Banner::query();

    //     // Search by title
    //     if ($request->filled('search')) {
    //         $query->where('title', 'like', '%' . $request->search . '%');
    //     }

    //     // Filter by city
    //     if ($request->filled('city')) {
    //         $query->whereJsonContains('cities', (string)$request->city);
    //     }

    //     $banners = $query->latest()->paginate(10)->withQueryString();

    //     // Get all active cities for dropdown
    //     $cities = DB::table('cities')->where('status', 1)->get();

    //     return view('admin.banners.index', compact('banners', 'cities'));
    // }


    // with store/product search

    public function index(Request $request)
    {
        $query = Banner::query();

        // 🔍 Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // 🌆 Filter by city
        if ($request->filled('city')) {
            $query->whereJsonContains('cities', (string)$request->city);
        }

        // 🔥 Filter by type (store/product)
        if ($request->filled('type')) {
            $query->where('link_type', $request->type);
        }

        // 🔥 Filter by specific store/product
        if ($request->filled('link_id')) {

            $value = $request->link_id;

            if (str_contains($value, 'store_')) {
                $id = str_replace('store_', '', $value);

                $query->where('link_type', 'store')
                    ->whereJsonContains('link_ids', (string)$id);
            }

            if (str_contains($value, 'product_')) {
                $id = str_replace('product_', '', $value);

                $query->where('link_type', 'product')
                    ->whereJsonContains('link_ids', (string)$id);
            }
        }

        $banners = $query->latest()->paginate(10)->withQueryString();

        $cities = DB::table('cities')->where('status', 1)->get();
        $stores = DB::table('stores')->where('status_id', 1)->get();
        $products = DB::table('products')->where('approval_status', "approved")->get();

        return view('admin.banners.index', compact('banners', 'cities', 'stores', 'products'));
    }

    // Show create form
    // public function create()
    // {
    //     $cities = DB::table('cities')->where('status', 1)->get();
    //     return view('admin.banners.create', compact('cities'));
    // }

    // with slect 

    public function create()
    {
        $cities = DB::table('cities')->where('status', 1)->get();
         // ✅ Only ACTIVE stores
        $stores = Store::where('status_id', 1)->get();

        // ✅ Only APPROVED products
        $products = Product::where('approval_status', 'approved')
                            ->get();
        return view('admin.banners.create', compact('cities', 'stores', 'products'));
    }

    // Store banner
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'title' => 'nullable|string|max:255',
    //         'cities' => 'required|array|min:1',
    //         'cities.*' => 'exists:cities,id', // validate each city ID
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    //         'status' => 'required|in:0,1',
    //     ]);

    //     // Handle "All Cities"
    //     if (in_array('all', $request->cities)) {
    //         $allCityIds = DB::table('cities')->where('status', 1)->pluck('id')->toArray();
    //         $citiesToStore = $allCityIds;
    //     } else {
    //         $citiesToStore = $request->cities;
    //     }

    //     // Upload image
    //     if ($request->hasFile('image')) {
    //         $file = $request->file('image');
    //         $fileName = time().'_'.$file->getClientOriginalName();
    //         $file->move(public_path('assets/banner_images'), $fileName);
    //     }

    //     Banner::create([
    //         'title' => $request->title,
    //         'cities' => $citiesToStore, // store as JSON
    //         'image' => $fileName ?? null,
    //         'status' => $request->status ?? 1,
    //     ]);

    //     return redirect()->route('dashboard.admin.banners.index')
    //                     ->with('success', 'Banner created successfully.');
    // }

    // with select 

    public function store(Request $request)
    {


        $request->validate([
            'title' => 'nullable|string|max:255',
            'cities' => 'required|array|min:1',
            'cities.*' => 'exists:cities,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:0,1',

            'link_type' => 'nullable|in:store,product',
            'link_ids' => 'nullable|array',
        ], [
            // 🔥 Cities
            'cities.required' => 'Please select at least one city',
            'cities.array' => 'Cities must be a valid array',
            'cities.min' => 'Select at least one city',
            'cities.*.exists' => 'Selected city is invalid',

            // 🔥 Image
            'image.required' => 'Banner image is required',
            'image.image' => 'File must be an image',
            'image.mimes' => 'Image must be jpeg, png, jpg, gif or webp',
            'image.max' => 'Image size must be less than 2MB',

            // 🔥 Status
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status selected',

            // 🔥 Link Type
            'link_type.in' => 'Link type must be store or product',

            // 🔥 Link IDs
            'link_ids.array' => 'Link selection must be valid',
        ]);
        // $request->validate([
        //     'title' => 'nullable|string|max:255',
        //     'cities' => 'required|array|min:1',
        //     'cities.*' => 'exists:cities,id', // validate each city ID
        //     'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        //     'status' => 'required|in:0,1',

        //      // NEW
        //     'link_type' => 'nullable|in:store,product',
        //     'link_ids' => 'nullable|array',
        // ]);

        // Handle "All Cities"
        if (in_array('all', $request->cities)) {
            $allCityIds = DB::table('cities')->where('status', 1)->pluck('id')->toArray();
            $citiesToStore = $allCityIds;
        } else {
            $citiesToStore = $request->cities;
        }

        // Upload image
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('assets/banner_images'), $fileName);
        }

        Banner::create([
            'title' => $request->title,
            'cities' => $citiesToStore, // store as JSON
            'image' => $fileName ?? null,
            'status' => $request->status ?? 1,
            'link_type' => $request->link_type,
            'link_ids' => $request->link_ids,
        ]);

        return redirect()->route('dashboard.admin.banners.index')
                        ->with('success', 'Banner created successfully.');
    }

    // Show edit form
    // public function edit(Banner $banner)
    // {
    //     $cities = DB::table('cities')->where('status', 1)->get();
    //     return view('admin.banners.edit', compact('banner', 'cities'));
    // }

    // with seletc 

     public function edit(Banner $banner)
    {
        $cities = DB::table('cities')->where('status', 1)->get();
        $stores = Store::where('status_id', 1)->get();

        $products = Product::where('approval_status', 'approved')
                        ->where('status_id', 1)
                        ->get();

       return view('admin.banners.edit', compact('banner', 'cities', 'stores', 'products'));
    }

    // Update banner
    // public function update(Request $request, Banner $banner)
    // {
    //     $request->validate([
    //         'title' => 'nullable|string|max:255',
    //         'cities' => 'required|array|min:1',
    //         'cities.*' => 'exists:cities,id',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    //         'status' => 'required|in:0,1',
    //     ]);

    //     // Handle "All Cities"
    //     if (in_array('all', $request->cities)) {
    //         $allCityIds = DB::table('cities')->where('status', 1)->pluck('id')->toArray();
    //         $citiesToStore = $allCityIds;
    //     } else {
    //         $citiesToStore = $request->cities;
    //     }

    //     // Upload image if changed
    //     if ($request->hasFile('image')) {
    //         if ($banner->image && file_exists(public_path('assets/banner_images/'.$banner->image))) {
    //             unlink(public_path('assets/banner_images/'.$banner->image));
    //         }

    //         $file = $request->file('image');
    //         $fileName = time().'_'.$file->getClientOriginalName();
    //         $file->move(public_path('assets/banner_images'), $fileName);
    //         $banner->image = $fileName;
    //     }

    //     $banner->title = $request->title;
    //     $banner->cities = $citiesToStore; // store as JSON
    //     $banner->status = $request->status ?? 1;
    //     $banner->save();

    //     return redirect()->route('dashboard.admin.banners.index')
    //                     ->with('success', 'Banner updated successfully.');
    // }

    // with selct

    public function update(Request $request, Banner $banner)
    {
        // $request->validate([
        //     'title' => 'nullable|string|max:255',
        //     'cities' => 'required|array|min:1',
        //     'cities.*' => 'exists:cities,id',
        //     'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        //     'status' => 'required|in:0,1',

        //     // NEW
        //     'link_type' => 'nullable|in:store,product',
        //     'link_ids' => 'nullable|array',
        // ]);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'cities' => 'required|array|min:1',
            'cities.*' => 'exists:cities,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:0,1',

            'link_type' => 'nullable|in:store,product',
            'link_ids' => 'nullable|array',
        ], [
            // 🔥 Cities
            'cities.required' => 'Please select at least one city',
            'cities.array' => 'Cities must be a valid array',
            'cities.min' => 'Select at least one city',
            'cities.*.exists' => 'Selected city is invalid',

            // 🔥 Image (optional in update)
            'image.image' => 'File must be an image',
            'image.mimes' => 'Image must be jpeg, png, jpg, gif or webp',
            'image.max' => 'Image size must be less than 2MB',

            // 🔥 Status
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status selected',

            // 🔥 Link Type
            'link_type.in' => 'Link type must be store or product',

            // 🔥 Link IDs
            'link_ids.array' => 'Link selection must be valid',
        ]);


        // Handle "All Cities"
        if (in_array('all', $request->cities)) {
            $allCityIds = DB::table('cities')->where('status', 1)->pluck('id')->toArray();
            $citiesToStore = $allCityIds;
        } else {
            $citiesToStore = $request->cities;
        }

        // Upload image if changed
        if ($request->hasFile('image')) {
            if ($banner->image && file_exists(public_path('assets/banner_images/'.$banner->image))) {
                unlink(public_path('assets/banner_images/'.$banner->image));
            }

            $file = $request->file('image');
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('assets/banner_images'), $fileName);
            $banner->image = $fileName;
        }

        $banner->title = $request->title;
        $banner->cities = $citiesToStore; // store as JSON
        $banner->status = $request->status ?? 1;
        $banner->link_type = $request->link_type;
        if ($request->link_type == 'store') {
            $banner->link_ids = $request->link_ids ?? [];
        } elseif ($request->link_type == 'product') {
            $banner->link_ids = $request->link_ids ?? [];
        } else {
            $banner->link_ids = null;
        }
        $banner->save();

        return redirect()->route('dashboard.admin.banners.index')
                        ->with('success', 'Banner updated successfully.');
    }


    // Delete banner
    public function destroy(Banner $banner)
    {
        if ($banner->image && file_exists(public_path('assets/banner_images/'.$banner->image))) {
            unlink(public_path('assets/banner_images/'.$banner->image));
        }

        $banner->delete();

        return redirect()->route('dashboard.admin.banners.index')
                        ->with('success', 'Banner deleted successfully.');
    }
}