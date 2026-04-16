<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Models\ProductReviewImage;
use App\Models\PromotionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();

        // ✅ AUTH CHECK
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // ✅ VALIDATION
        $validator = Validator::make($request->all(), [
            'promotion_request_id' => 'required|exists:promotion_requests,id',
            'title' => 'required',
            'review' => 'required',
            'rating' => 'required|integer|min:1|max:5',
            'username' => 'required',
            'profile_image' => 'nullable|image|max:2048',
            'images.*' => 'image|max:2048'
        ], [
            'title.required' => __('messages.vendor.review.validation.title_required'),
            'review.required' => __('messages.vendor.review.validation.review_required'),
            'rating.required' => __('messages.vendor.review.validation.rating_required'),
            'username.required' => __('messages.vendor.review.validation.username_required'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        // ✅ CHECK APPROVED PROMOTION
        $promo = PromotionRequest::where('id', $request->promotion_request_id)
            ->where('vendor_id', $user->id)
            ->where('status', 'approved')
            ->first();

        if (!$promo) {
            return response()->json([
                'status' => false,
                'message' => __('messages.vendor.review.promotion.not_approved')
            ]);
        }

        // ✅ CHECK REVIEW LIMIT
        $used = ProductReview::where('promotion_request_id', $promo->id)->count();

        if ($used >= $promo->package->review_count) {
            return response()->json([
                'status' => false,
                 'message' => __('messages.vendor.review.limit.reached')
            ]);
        }

        // ✅ PROFILE IMAGE UPLOAD
        $profileImage = null;
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $profileImage = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('assets/review_profiles'), $profileImage);
        }

        // ✅ CREATE REVIEW
        $review = ProductReview::create([
            'vendor_id' => $user->id,
            'user_id' => null,
            'product_id' => $promo->product_id,
            'promotion_request_id' => $promo->id,
            'rating' => $request->rating,
            'review' => $request->review,
            'username' => $request->username,
            'title' => $request->title,
            'profile_image' => $profileImage,
            'status' => 'approved' // ya 'pending' agar admin approval chahiye
        ]);

        // ✅ MULTIPLE IMAGES
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {

                $imageName = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('assets/review_images'), $imageName);

                ProductReviewImage::create([
                    'review_id' => $review->id,
                    'image' => $imageName
                ]);
            }
        }

        return response()->json([
            'status' => true,
           'message' => __('messages.vendor.review.store.success')
        ]);
    }
}