<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewImage;
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

        // ✅ VALIDATION (single error only)
        $validator = Validator::make($request->all(), [
            'promotion_request_id' => 'required|exists:promotion_requests,id',
            'title' => 'required',
            'review' => 'required',
            'rating' => 'required|integer|min:1|max:5',
            'username' => 'required',
            'images.*' => 'image|max:2048'
        ], [
            'title.required' => 'Title is required',
            'review.required' => 'Review is required',
            'rating.required' => 'Rating is required',
            'username.required' => 'Username is required'
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
                'message' => 'Promotion not approved'
            ]);
        }

        // ✅ CHECK REVIEW LIMIT
        $used = Review::where('promotion_request_id', $promo->id)->count();

        if ($used >= $promo->package->review_count) {
            return response()->json([
                'status' => false,
                'message' => 'Review limit reached'
            ]);
        }

        // ✅ CREATE REVIEW (IMPORTANT 🔥)
        $review = Review::create([
            'vendor_id' => $user->id,
            'user_id' => null, // fake review
            'product_id' => $promo->product_id,
            'promotion_request_id' => $promo->id,
            'product_rating' => $request->rating,
            'seller_rating' => $request->rating,
            'review' => $request->review,
            'username' => $request->username,
            'title' => $request->title
        ]);

        // ✅ MULTIPLE IMAGE UPLOAD (your format)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {

                $imageName = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('assets/review_images'), $imageName);

                ReviewImage::create([
                    'review_id' => $review->id,
                    'image' => $imageName
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Review submitted successfully'
        ]);
    }
}