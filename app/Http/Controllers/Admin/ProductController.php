<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

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


    // public function approve($id)
    // {
    //     $product = Product::findOrFail($id);

    //     if ($product->approval_status == 'approved') {
    //         return back()->with('success', 'Already approved');
    //     }

    //     // 🔥 GENERATE UNIQUE ARTICLE NUMBER
    //     $articleNumber = 'ART-' . strtoupper(uniqid());

    //     $product->update([
    //         'approval_status' => 'approved',
    //         'article_number' => $articleNumber,
    //         'is_original' => 1
    //     ]);

    //     return back()->with('success', 'Product approved successfully.');
    // }

    // public function reject($id)
    // {
    //     $product = Product::findOrFail($id);

    //     $product->update([
    //         'approval_status' => 'rejected'
    //     ]);

    //     return back()->with('success', 'Product rejected successfully.');
    // }

    public function approve($id)
    {
        $product = Product::findOrFail($id);

        DB::beginTransaction();

        try {

            // 🔥 STEP 1: FIND ORIGINAL PRODUCT
            $original = $product->parent_product_id
                ? Product::find($product->parent_product_id)
                : $product;

            // 🔥 STEP 2: GENERATE ARTICLE NUMBER (ONLY IF NOT EXISTS)
          $articleNumber = $original->article_number 
            ? $original->article_number 
            : strtoupper(uniqid());

            // 🔥 STEP 3: UPDATE ORIGINAL
            $original->update([
                'approval_status' => 'approved',
                'article_number' => $articleNumber,
                'is_original' => 1,
                'parent_product_id' => null
            ]);

            // 🔥 STEP 4: UPDATE ALL CHILD PRODUCTS
            Product::where('parent_product_id', $original->id)
                ->update([
                    'approval_status' => 'approved',
                    'article_number' => $articleNumber,
                    'is_original' => 0
                ]);

            DB::commit();

            return back()->with('success', 'Product approved successfully.');

        } catch (\Exception $e) {

            DB::rollback();

            return back()->with('error', 'Something went wrong');
        }
    }

    // public function reject($id)
    // {
    //     $product = Product::findOrFail($id);

    //     DB::beginTransaction();

    //     try {

    //         // 🔥 CASE 1: IF ORIGINAL → REJECT ALL
    //         if ($product->parent_product_id == null) {

    //             Product::where('parent_product_id', $product->id)
    //                 ->orWhere('id', $product->id)
    //                 ->update([
    //                     'approval_status' => 'rejected'
    //                 ]);
    //         }

    //         // 🔥 CASE 2: IF CHILD → ONLY REJECT THAT PRODUCT
    //         else {

    //             $product->update([
    //                 'approval_status' => 'rejected'
    //             ]);
    //         }

    //         DB::commit();

    //         return back()->with('success', 'Product rejected successfully.');

    //     } catch (\Exception $e) {

    //         DB::rollback();

    //         return back()->with('error', 'Something went wrong');
    //     }
    // }


    // with reject reason

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:500'
        ], [
            'reject_reason.required' => 'Please provide a reason for rejecting this product.',
        ]);

        $product = Product::findOrFail($id);

        DB::beginTransaction();

        try {

            // 🔥 CASE 1: ORIGINAL PRODUCT → REJECT ALL
            if ($product->parent_product_id == null) {

                Product::where('parent_product_id', $product->id)
                    ->orWhere('id', $product->id)
                    ->update([
                        'approval_status' => 'rejected',
                        'reject_reason'   => $request->reject_reason
                    ]);
            }

            // 🔥 CASE 2: CHILD PRODUCT → ONLY THAT ONE
            else {

                $product->update([
                    'approval_status' => 'rejected',
                    'reject_reason'   => $request->reject_reason
                ]);
            }

            DB::commit();

            return back()->with('success', 'Product rejected successfully.');

        } catch (\Exception $e) {

            DB::rollback();

            return back()->with('error', 'Something went wrong');
        }
    }

}
