<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;

class StoreController extends Controller
{
    // List all stores
    public function index(Request $request)
    {
        $query = Store::query();

        // Search by name or email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status_filter')) {
            $query->where('status_id', $request->status_filter);
        }

        $stores = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.stores.allListing', compact('stores'));
    }

    // Approve store
    public function approve(Store $store)
    {
        $store->status_id = 1; // Active
        $store->approved_at = now();
        $store->save();

        return back()->with('success', 'Store approved successfully.');
    }

    // Reject store
    public function reject(Store $store)
    {
        $store->status_id = 3; // Rejected
        $store->save();

        return back()->with('success', 'Store rejected successfully.');
    }
}
