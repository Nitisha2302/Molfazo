<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 3); // ✅ Customers

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Status filter
        if ($request->filled('status_filter')) {
            $query->where('status_id', $request->status_filter);
        }

        $customers = $query
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.customers.allListing', compact('customers'));
    }

    // Block
    public function block(User $customer)
    {
        if ($customer->role != 3) return back()->with('error', 'Invalid customer');

        $customer->status_id = 4;
        $customer->save();

        return back()->with('success', 'Customer blocked successfully.');
    }

    // Unblock
    public function unblock(User $customer)
    {
        if ($customer->role != 3) return back()->with('error', 'Invalid customer');

        $customer->status_id = 1;
        $customer->save();

        return back()->with('success', 'Customer unblocked successfully.');
    }


    
}