<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Bank;
use App\Models\VendorBank;

class VendorBankController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Save Vendor Payment
    |--------------------------------------------------------------------------
    */
    public function saveVendorPayment(Request $request)
    {
        $vendor = Auth::guard('api')->user();

        if (!$vendor || $vendor->role != 2 || $vendor->status_id != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized vendor.'
            ], 403);
        }

        // ✅ Validation
        $validator = Validator::make($request->all(), [
            'payment_modes' => 'required|array',
            'payment_modes.*' => 'in:cod,bank',
            'banks' => 'required_if:payment_modes.*,bank|array',
            'banks.*.bank_id' => 'required|exists:banks,id',
            'banks.*.account_holder_name' => 'required|string|max:255',
            'banks.*.account_number' => 'required|string|max:50',
        ], [
            'payment_modes.required' => 'Payment mode is required.',
            'payment_modes.array' => 'Payment mode must be an array.',
            'payment_modes.*.in' => 'Payment mode must be COD or Bank.',
            'banks.required_if' => 'Bank details are required when payment mode includes Bank.',
            'banks.*.bank_id.required' => 'Bank ID is required.',
            'banks.*.bank_id.exists' => 'Selected bank does not exist.',
            'banks.*.account_holder_name.required' => 'Account holder name is required.',
            'banks.*.account_number.required' => 'Account number is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        // ✅ Save payment modes
        $vendor->update([
            'payment_modes' => $request->payment_modes
        ]);

        // ✅ Remove all existing bank entries if 'bank' not in payment_modes
        if (!in_array('bank', $request->payment_modes)) {
            VendorBank::where('user_id', $vendor->id)->delete();
        }

        // ✅ Save/update multiple banks
        if (in_array('bank', $request->payment_modes)) {
            foreach ($request->banks as $bank) {
                VendorBank::updateOrCreate(
                    [
                        'user_id' => $vendor->id,
                        'bank_id' => $bank['bank_id'],
                    ],
                    [
                        'account_holder_name' => $bank['account_holder_name'],
                        'account_number' => $bank['account_number'],
                    ]
                );
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Payment modes updated successfully.'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Vendor Payment Details
    |--------------------------------------------------------------------------
    */
    public function getVendorPayment()
    {
        $vendor = Auth::guard('api')->user();

        if (!$vendor || $vendor->role != 2 || $vendor->status_id != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        $banks = VendorBank::with('bank')
            ->where('user_id', $vendor->id)
            ->get()
            ->map(function ($bank) {
                return [
                    'bank_id' => $bank->bank->id ?? null,
                    'bank_name' => $bank->bank->name ?? null,
                    'bank_logo' => $bank->bank->logo ?? null,
                    'account_holder_name' => $bank->account_holder_name,
                    'account_number' => $bank->account_number,
                ];
            });

        return response()->json([
            'status' => true,
            'payment_modes' => $vendor->payment_modes ?? [],
            'bank_details' => in_array('bank', $vendor->payment_modes ?? []) ? $banks : []
        ]);
    }
}