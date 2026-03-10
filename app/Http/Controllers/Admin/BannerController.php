<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    // List banners
    public function index(Request $request)
    {
        $query = Banner::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        $banners = $query->latest()->paginate(10)->withQueryString();
       // Get unique cities for dropdown
      $cities = Banner::select('city')->distinct()->pluck('city');

        return view('admin.banners.index', compact('banners', 'cities'));
    }

    // Show create form
    public function create()
    {
        return view('admin.banners.create');
    }

    // Store banner
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
               'city' => 'required|string|max:100',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:0,1',
        ], [
            'image.required' => 'Banner image is required!',
            'image.image' => 'File must be an image!',
            'image.mimes' => 'Allowed image types: jpeg, png, jpg, gif, webp',
            'image.max' => 'Image cannot exceed 2MB',
             'city.required' => 'City is required.', 
        ]);

            // Save image in public/assets/banner_images
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('assets/banner_images'), $fileName);
        }

        Banner::create([
            'title' => $request->title,
            'city' => $request->city,
            'image' => $fileName,
            'status' => $request->status ?? 1,
        ]);

        return redirect()->route('dashboard.admin.banners.index')
                         ->with('success', 'Banner created successfully.');
    }

    // Show edit form
    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    // Update banner
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:0,1',
        ], [
            'image.image' => 'File must be an image!',
            'image.mimes' => 'Allowed image types: jpeg, png, jpg, gif, webp',
            'image.max' => 'Image cannot exceed 2MB',
             'city.required' => 'City is required.',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($banner->image && file_exists(public_path('assets/banner_images/'.$banner->image))) {
                unlink(public_path('assets/banner_images/'.$banner->image));
            }

            $file = $request->file('image');
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('assets/banner_images'), $fileName);
            $banner->image = $fileName;
        }

        $banner->title = $request->title;
        $banner->city = $request->city;
        $banner->status = $request->status ?? 1;
        $banner->save();

        return redirect()->route('dashboard.admin.banners.index')
                         ->with('success', 'Banner updated successfully.');
    }

    // Delete banner
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
