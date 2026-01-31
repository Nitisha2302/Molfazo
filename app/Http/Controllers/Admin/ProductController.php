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
        Product::where('id', $id)->update(['status_id' => 3]);

        return back()->with('success', 'Product deleted successfully.');
    }
}
