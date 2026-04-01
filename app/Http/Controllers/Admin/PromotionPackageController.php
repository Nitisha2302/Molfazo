<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromotionPackage;
use Illuminate\Http\Request;

class PromotionPackageController extends Controller
{
    public function index(Request $request)
    {
        $query = PromotionPackage::query();

        if ($request->search) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        $packages = $query->latest()->paginate(10);

        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'review_count' => 'required|integer|min:1',
            'price' => 'required|numeric|min:1',
        ],[
            'title.required' => 'Package title is required',
            'review_count.required' => 'Review count is required',
            'review_count.integer' => 'Review count must be a number',
            'review_count.min' => 'Minimum 1 review required',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
        ]);

        PromotionPackage::create($request->all());

        return redirect()->route('dashboard.admin.packages.index')
            ->with('success','Package Added Successfully');
    }

    public function edit($id)
    {
        $package = PromotionPackage::findOrFail($id);
        return view('admin.packages.edit', compact('package'));
    }

   public function update(Request $request,$id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'review_count' => 'required|integer|min:1',
            'price' => 'required|numeric|min:1',
        ],[
            'title.required' => 'Package title is required',
            'review_count.required' => 'Review count is required',
            'price.required' => 'Price is required',
        ]);

        PromotionPackage::findOrFail($id)->update($request->all());

       return redirect()->route('dashboard.admin.packages.index')
            ->with('success','Package Updated Successfully');
    }

    public function destroy($id)
    {
        PromotionPackage::findOrFail($id)->delete();
        return back()->with('success','Deleted');
    }
}