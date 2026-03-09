<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
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

        $messages = [
            'payment_modes.required' => 'Payment mode is required.',
            'payment_modes.array' => 'Payment mode must be an array.',
              'payment_modes.*.in' => 'Payment mode must be COD or Bank.',
            'bank_id.required_if' => 'Bank is required when payment mode is Bank.',
            'bank_id.exists' => 'Selected bank does not exist.',
            'account_number.required_if' => 'Account number is required when payment mode is Bank.'
        ];

        $validator = Validator::make($request->all(), [
            'payment_modes' => 'required|array',
            'payment_modes.*' => 'in:cod,bank',
            'bank_id' => 'required_if:payment_modes.*,bank|exists:banks,id',
            'account_holder_name' => 'nullable|string|max:255',
            'account_number' => 'required_if:payment_modes.*,bank|string|max:50',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->first()
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Update Payment Mode
        |--------------------------------------------------------------------------
        */

        $vendor->update([
            'payment_modes' => $request->payment_modes
        ]);

        /*
        |--------------------------------------------------------------------------
        | If COD → Delete Bank
        |--------------------------------------------------------------------------
        */

        if ($request->payment_mode === 'cod') {

            VendorBank::where('user_id', $vendor->id)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Payment mode set to COD successfully.'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | If BANK → Save / Update Bank
        |--------------------------------------------------------------------------
        */

        VendorBank::updateOrCreate(
            [
                'user_id' => $vendor->id,
                'bank_id' => $request->bank_id
            ],
            [
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Payment mode set to Bank and bank details saved successfully.'
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