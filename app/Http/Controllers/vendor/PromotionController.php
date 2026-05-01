<?php
namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\PromotionPackage;
use App\Models\AdminPaymentDetail;
use App\Models\ProductReview;
use App\Models\PromotionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // ✅ VALIDATION
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'package_id' => 'required|exists:promotion_packages,id',
            'image' => 'required|image|max:2048'
        ],[
            'product_id.required' => __('messages.vendor.promotion.validation.product_required'),
            'package_id.required' => __('messages.vendor.promotion.validation.package_required'),
            'image.required' => __('messages.vendor.promotion.validation.image_required'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first() // ✅ ONLY FIRST ERROR
            ], 422);
        }

        // ✅ DUPLICATE CHECK
        $exists = PromotionRequest::where('vendor_id',$user->id)
                    ->where('product_id',$request->product_id)
                    ->where('status','pending')
                    ->exists();

                    


        if($exists){
            return response()->json([
                'status'=>false,
               'message' => __('messages.vendor.promotion.store.duplicate')
            ]);
        }

        // ✅ STORE IMAGE
       $imageName = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('assets/payment_screenshots'), $imageName);
        }

        //  CREATE REQUEST
        PromotionRequest::create([
            'vendor_id' => $user->id,
            'product_id' => $request->product_id,
            'package_id' => $request->package_id,
            'payment_screenshot' => $imageName, 
            'status' => 'pending'
        ]);


        return response()->json([
            'status' => true,
             'message' => __('messages.vendor.promotion.store.success')
        ]);
    }
}