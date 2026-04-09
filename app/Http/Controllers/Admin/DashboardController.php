<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    public function Dashboard()
    {
        // ================= USER COUNTS =================
        $totalUsers = User::count();
        $vendorCount = User::where('role', 2)->count();
        $customerCount = User::where('role', 3)->count();

        // ================= VENDOR STATUS =================
        $approvedVendors = User::where('role', 2)
                                ->where('status_id', 1)
                                ->count();

        $rejectedVendors = User::where('role', 2)
                                ->where('status_id', 3)
                                ->count();

        $pendingVendors = User::where('role', 2)
                                ->where('status_id', 2)
                                ->count();

        // ================= OPTIONAL (ACTIVE USERS) =================
        $activeUsers = User::where('status_id', 1)->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'vendorCount',
            'customerCount',
            'approvedVendors',
            'rejectedVendors',
            'pendingVendors',
            'activeUsers'
        ));
    }
}