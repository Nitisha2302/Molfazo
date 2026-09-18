<?php
namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\PromotionPackage;
use App\Models\AdminPaymentDetail;
use App\Models\ProductReview;
use App\Models\PromotionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PromotionController extends Controller
{
    //  GET PACKAGES

    public function packages(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // ✅ GET ALL PACKAGES
        $packages = PromotionPackage::select('id','title','review_count','price')->get();

        // ✅ Agar product_id nahi aaya → simple return
        if (!$request->filled('product_id')) {

            return response()->json([
                'status' => true,
               'message' => __('messages.vendor.promotion.packages.success'),
                'data' => $packages
            ]);
        }

        // ✅ Agar product_id aaya → status bhi attach karo
        $request->validate([
            'product_id' => 'exists:products,id'
        ]);

        $requests = PromotionRequest::where('vendor_id', $user->id)
            ->where('product_id', $request->product_id)
            ->get()
            ->keyBy('package_id');

        $data = $packages->map(function ($package) use ($requests) {

            $promotion = $requests[$package->id] ?? null;
            // ✅ COUNT USED REVIEWS
           $usedReviews = 0;
            if ($promotion) {
                $usedReviews = ProductReview::where('product_id', $promotion->product_id)
                ->where('vendor_id', $promotion->vendor_id)
                ->count();
            }

            return [
                'id' => $package->id,
                'title' => $package->title,
                'review_count' => $package->review_count,
                'price' => $package->price,
                 // ✅ NEW FIELDS
                'used_reviews' => $usedReviews,
                'remaining_reviews' => $promotion ? ($package->review_count - $usedReviews) : $package->review_count,

                'status' => $promotion->status ?? null,
                'is_applied' => $promotion ? true : false,
                  'promotion_request_id' => $promotion->id ?? null
            ];
        });

        return response()->json([
            'status' => true,
           'message' => __('messages.vendor.promotion.packages.with_status_success'),
            'data' => $data
        ]);
    }

    //  GET PAYMENT DETAILS
    public function paymentDetails()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $data = AdminPaymentDetail::first();

        return response()->json([
            'status' => true,
             'message' => __('messages.vendor.promotion.payment.success'),
            'data' => $data
        ]);
    }

    //  STORE PROMOTION REQUEST
    // public function store(Request $request)
    // {
    //     $user = Auth::guard('api')->user();

    //     if (!$user) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Unauthorized'
    //         ], 401);
    //     }

    //     // ✅ VALIDATION
    //     $validator = Validator::make($request->all(), [
    //         'product_id' => 'required|exists:products,id',
    //         'package_id' => 'required|exists:promotion_packages,id',
    //         'image' => 'required|image|max:2048'
    //     ],[
    //         'product_id.required' => __('messages.vendor.promotion.validation.product_required'),
    //         'package_id.required' => __('messages.vendor.promotion.validation.package_required'),
    //         'image.required' => __('messages.vendor.promotion.validation.image_required'),
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $validator->errors()->first() // ✅ ONLY FIRST ERROR
    //         ], 422);
    //     }

    //     // ✅ DUPLICATE CHECK
    //     $exists = PromotionRequest::where('vendor_id',$user->id)
    //                 ->where('product_id',$request->product_id)
    //                 ->where('status','pending')
    //                 ->exists();

                    


    //     if($exists){
    //         return response()->json([
    //             'status'=>false,
    //            'message' => __('messages.vendor.promotion.store.duplicate')
    //         ]);
    //     }

    //     // ✅ STORE IMAGE
    //    $imageName = null;

    //     if ($request->hasFile('image')) {
    //         $file = $request->file('image');
    //         $imageName = time().'_'.$file->getClientOriginalName();
    //         $file->move(public_path('assets/payment_screenshots'), $imageName);
    //     }

    //     //  CREATE REQUEST
    //     PromotionRequest::create([
    //         'vendor_id' => $user->id,
    //         'product_id' => $request->product_id,
    //         'package_id' => $request->package_id,
    //         'payment_screenshot' => $imageName, 
    //         'status' => 'pending'
    //     ]);


    //     return response()->json([
    //         'status' => true,
    //          'message' => __('messages.vendor.promotion.store.success')
    //     ]);
    // }


    /**
     * ==================================================================
     *  NEW FLOW — PAYMENT DONE IN THE APP, NO ADMIN APPROVAL
     * ==================================================================
     *  POST /api/vendor/promotion-request/paid
     *
     *  The app takes the Stripe payment itself, then calls this with the
     *  payment id and status. If the payment succeeded the promotion is
     *  APPROVED IMMEDIATELY — admin does nothing.
     *
     *  body (JSON or form-data):
     *    product_id     : required
     *    package_id     : required
     *    payment_id     : required — Stripe payment intent / charge id
     *    payment_status : required — succeeded | paid | pending | failed
     *    payment_method : optional — defaults to "stripe"
     *
     *  The server cannot verify the payment really happened. It trusts
     *  what the app sends. Every row created here is marked
     *  auto_approved = true so it can be audited:
     *
     *      SELECT * FROM promotion_requests WHERE auto_approved = 1;
     * ==================================================================
     */
    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'product_id'     => 'required|exists:products,id',
            'package_id'     => 'required|exists:promotion_packages,id',
            'payment_id'     => 'required|string|max:191',
            'payment_status' => 'required|string|in:succeeded,paid,pending,failed',
            'payment_method' => 'nullable|string|max:30',
        ],[
            'product_id.required'     => __('messages.vendor.promotion.validation.product_required'),
            'package_id.required'     => __('messages.vendor.promotion.validation.package_required'),
            'payment_id.required'     => 'Payment id is required.',
            'payment_status.required' => 'Payment status is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $paymentId     = $request->payment_id;
        $paymentStatus = $request->payment_status;

        // ✅ SAME PAYMENT TWICE — one payment, one promotion
        $already = PromotionRequest::where('payment_id', $paymentId)->first();

        if ($already) {
            return response()->json([
                'status'  => true,
                'message' => 'This payment was already processed',
                'data'    => [
                    'promotion_request_id' => $already->id,
                    'status'               => $already->status,
                    'duplicate'            => true,
                ]
            ]);
        }

        // ✅ PAYMENT MUST HAVE SUCCEEDED
        if (! in_array($paymentStatus, ['succeeded', 'paid'], true)) {
            return response()->json([
                'status'  => false,
                'message' => 'Payment was not completed. Please try again.',
                'data'    => ['payment_status' => $paymentStatus],
            ], 402);
        }

        // ✅ ALREADY HAS A PENDING OR ACTIVE PROMOTION FOR THIS PRODUCT?
        // $exists = PromotionRequest::where('vendor_id', $user->id)
        //     ->where('product_id', $request->product_id)
        //     ->whereIn('status', ['pending', 'approved'])
        //     ->exists();

        // if ($exists) {
        //     return response()->json([
        //         'status'  => false,
        //         'message' => __('messages.vendor.promotion.store.duplicate')
        //     ]);
        // }

        $package = PromotionPackage::find($request->package_id);

        // ✅ CREATE — APPROVED STRAIGHT AWAY
        $promotion = PromotionRequest::create([
            'vendor_id'          => $user->id,
            'product_id'         => $request->product_id,
            'package_id'         => $request->package_id,
            'payment_screenshot' => null,          // no screenshot in this flow
            'status'             => 'approved',    // no admin step

            'payment_id'         => $paymentId,
            'payment_status'     => $paymentStatus,
            'payment_method'     => $request->input('payment_method', 'stripe'),
            'amount_paid'        => $package->price ?? null,
            'paid_at'            => now(),
            'auto_approved'      => true,
        ]);

        Log::info('Promotion auto-approved (payment reported by app)', [
            'promotion_request_id' => $promotion->id,
            'vendor_id'            => $user->id,
            'product_id'           => $request->product_id,
            'package_id'           => $request->package_id,
            'payment_id'           => $paymentId,
            'amount'               => $package->price ?? null,
            'ip'                   => $request->ip(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Payment successful. Your promotion is active.',
            'data'    => [
                'promotion_request_id' => $promotion->id,
                'status'               => $promotion->status,
                'package_title'        => $package->title ?? null,
                'review_count'         => $package->review_count ?? null,
                'amount_paid'          => (float) ($package->price ?? 0),
                'needs_admin_approval' => false,
            ]
        ]);
    }
}