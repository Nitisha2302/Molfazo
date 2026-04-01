<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromotionRequest;

class PromotionRequestController extends Controller
{
    public function index()
    {
        $requests = PromotionRequest::with(['product','package','vendor'])
                        ->latest()
                        ->paginate(10);

        return view('admin.promotion_requests.index', compact('requests'));
    }

    public function approve($id)
    {
        $req = PromotionRequest::findOrFail($id);
        $req->status = 'approved';
        $req->save();

        return back()->with('success','Request Approved Successfully');
    }

    public function reject($id)
    {
        $req = PromotionRequest::findOrFail($id);
        $req->status = 'rejected';
        $req->save();

        return back()->with('success','Request Rejected');
    }
}