<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminPaymentDetail;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function edit()
    {
        $data = AdminPaymentDetail::first();
        return view('admin.adminPayment.edit', compact('data'));
    }

    public function update(Request $request)
    {
        // ✅ VALIDATION
        $request->validate([
            'account_name'   => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'ifsc'           => 'nullable|string|max:50',
            'upi_id'         => 'nullable|string|max:100',
            'qr_code'        => 'nullable|image|max:2048',
        ]);

        // ✅ DEFAULT OLD IMAGE
        $qr = $request->old_qr ?? null;

        // ✅ CUSTOM IMAGE UPLOAD (LIKE YOUR FORMAT)
        if ($request->hasFile('qr_code')) {
            $file = $request->file('qr_code');
            $imageName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('assets/qr_codes'), $imageName);

            $qr = $imageName;
        }

        // ✅ SAVE DATA
        AdminPaymentDetail::updateOrCreate(
            ['id'=>1],
            [
                'account_name'   => $request->account_name,
                'account_number' => $request->account_number,
                'ifsc'           => $request->ifsc,
                'upi_id'         => $request->upi_id,
                'qr_code'        => $qr
            ]
        );

        return back()->with('success','Payment details updated successfully');
    }
}