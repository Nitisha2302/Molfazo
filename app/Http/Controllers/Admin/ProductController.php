<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Services\FCMService;

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

    public function show($id)
    {
        $product = Product::with(['store',
            'category',
            'subCategory',
            'images',
            'primaryImage',
            'store.vendorBanks.bank' ])->findOrFail($id);
        return view('admin.notifications.show_product', compact('product'));
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
                'parent_product_id' => null,
                'reject_reason'   => null,
            ]);

            // 🔥 STEP 4: UPDATE ALL CHILD PRODUCTS
            Product::where('parent_product_id', $original->id)
                ->update([
                    'approval_status' => 'approved',
                    'article_number' => $articleNumber,
                    'is_original' => 0,
                    'reject_reason'   => null,
                ]);

            DB::commit();

            return back()->with('success', 'Product approved successfully.');

        } catch (\Exception $e) {

            DB::rollback();

            return back()->with('error', 'Something went wrong');
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:500'
        ], [
            'reject_reason.required' => 'Please provide a reason for rejecting this product.',
        ]);

        $product = Product::with('store')->findOrFail($id);

        DB::beginTransaction();

        try {

            $affectedProducts = collect();

            /* ===============================
            🔥 CASE 1: ORIGINAL PRODUCT → REJECT ALL
            =============================== */
            if ($product->parent_product_id == null) {

                $affectedProducts = Product::where('parent_product_id', $product->id)
                    ->orWhere('id', $product->id)
                    ->get();

                Product::where('parent_product_id', $product->id)
                    ->orWhere('id', $product->id)
                    ->update([
                        'approval_status' => 'rejected',
                        'reject_reason'   => $request->reject_reason
                    ]);
            }

            /* ===============================
            🔥 CASE 2: CHILD PRODUCT → ONLY THAT ONE
            =============================== */
            else {

                $product->update([
                    'approval_status' => 'rejected',
                    'reject_reason'   => $request->reject_reason
                ]);

                $affectedProducts = collect([$product]);
            }

            /* ===============================
            🔔 SEND NOTIFICATIONS
            =============================== */
            foreach ($affectedProducts as $p) {

                $vendor = User::find($p->store->user_id);

                if (!$vendor) continue;

                // 🔔 Save in DB (bell)
                // Notification::create([
                //     'user_id' => $vendor->id,
                //     'title' => '❌ Product Rejected',
                //     'body' => 'Your product "' . $p->name . '" was rejected. Reason: ' . $request->reject_reason,
                //     'notification_type' => 22,
                //     'is_read' => 0
                // ]);

                // 🔔 FCM Push
                if (!empty($vendor->fcm_token)) {

                    $tokens = [[
                        'fcm_token' => $vendor->fcm_token,
                        'user_id' => $vendor->id,
                    ]];

                    $notificationData = [
                        'notification_type' => 22,
                        'title' => '❌ Product Rejected',
                        'body' => 'Your product "' . $p->name . '" was rejected.',
                        'product_id' => $p->id,
                        // ✅ Full product details
                        'product' => json_encode($p->toArray()),

                        // ✅ Include store id and store name
                        'store_id' => $p->store->id,
                        'store_name' => $p->store->name,
                    ];

                    (new \App\Services\FCMService())
                        ->sendNotification($tokens, $notificationData, true);
                }
            }

            DB::commit();

            return back()->with('success', 'Product rejected successfully.');

        } catch (\Exception $e) {

            DB::rollback();

            \Log::error('Product Reject Error: ' . $e->getMessage());

            return back()->with('error', 'Something went wrong');
        }
    }

}
