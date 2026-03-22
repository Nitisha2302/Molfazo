<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

// App\Http\Controllers\Admin\ProductController.php

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with([
            'store',
            'category',
            'subCategory',
            'images',
            'primaryImage',
            'store.vendorBanks.bank' 
        ]);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->latest()->paginate(10)->withQueryString();

        return view('admin.products.index', compact('products'));
    }


    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete(); // or $product->update(['status_id'=>3]) if soft delete

        return redirect()
            ->route('dashboard.admin.products')
            ->with('success', 'Product deleted successfully.');
    }


    public function approve($id)
    {
        $product = Product::findOrFail($id);

        if ($product->approval_status == 'approved') {
            return back()->with('success', 'Already approved');
        }

        // 🔥 GENERATE UNIQUE ARTICLE NUMBER
        $articleNumber = 'ART-' . strtoupper(uniqid());

        $product->update([
            'approval_status' => 'approved',
            'article_number' => $articleNumber,
            'is_original' => 1
        ]);

        return back()->with('success', 'Product approved successfully.');
    }

    public function reject($id)
    {
        $product = Product::findOrFail($id);

        $product->update([
            'approval_status' => 'rejected'
        ]);

        return back()->with('success', 'Product rejected successfully.');
    }

}
