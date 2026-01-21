<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class VendorController extends Controller
{
    // List all vendors
    public function index(Request $request)
    {
        $query = User::where('role', 2); // Only vendors

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status_filter')) {
            $query->where('status_id', $request->status_filter);
        }

        $vendors = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.vendors.allListing', compact('vendors'));
    }

    // Approve vendor (Pending / Rejected -> Active)
    public function approve(User $vendor)
    {
        if ($vendor->role != 2) return back()->with('error', 'Invalid vendor');

        $vendor->status_id = 1; // Active
        $vendor->save();

        return back()->with('success', 'Vendor approved successfully.');
    }

    // Reject vendor (Pending / Active -> Rejected)
    public function reject(User $vendor)
    {
        if ($vendor->role != 2) return back()->with('error', 'Invalid vendor');

        $vendor->status_id = 3; // Rejected
        $vendor->save();

        return back()->with('success', 'Vendor rejected successfully.');
    }

    // Block vendor (Pending / Active / Rejected -> Blocked)
    public function block(User $vendor)
    {
        if ($vendor->role != 2) return back()->with('error', 'Invalid vendor');

        $vendor->status_id = 4; // Blocked
        $vendor->save();

        return back()->with('success', 'Vendor blocked successfully.');
    }

    // Unblock vendor (Blocked -> Pending)
    public function unblock(User $vendor)
    {
        if ($vendor->role != 2) return back()->with('error', 'Invalid vendor');

        $vendor->status_id = 2; // Pending
        $vendor->save();

        return back()->with('success', 'Vendor unblocked successfully.');
    }
    
}
