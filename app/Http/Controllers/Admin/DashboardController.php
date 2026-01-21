<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\City;


class DashboardController extends Controller
{
    public function Dashboard() {
        return view('admin.dashboard');
    }


    

    
}
