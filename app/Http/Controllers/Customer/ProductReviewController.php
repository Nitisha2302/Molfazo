<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductReviewImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductReviewController extends Controller
{
    // ⭐ Store / Update Review
    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();

        // 🔐 Unauthorized
        if (!$user) {
            return response()->json([
                'status'  => false,
             'message' => __('messages.customer.review.unauthorized')
            ], 401);
        }

        // ✅ Validation Rules
        $validator = Validator::make(
            $request->all(),
            [
                'product_id' => 'required|exists:products,id',
                'rating'     => 'required|integer|min:1|max:5',
                'review'     => 'nullable|string|max:1000' ,
                'images.*'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
                
            ],
            // ⭐ Custom Messages
            [
                'product_id.required' => __('messages.customer.review.validation.product_required'),
                'product_id.exists'   => __('messages.customer.review.validation.product_exists'),
                'rating.required'     => __('messages.customer.review.validation.rating_required'),
                'rating.integer'      => __('messages.customer.review.validation.rating_integer'),
                'rating.min'          => __('messages.customer.review.validation.rating_min'),
                'rating.max'          => __('messages.customer.review.validation.rating_max'),
                'review.string'       => __('messages.customer.review.validation.review_string'),
                'review.max'          => __('messages.customer.review.validation.review_max'),
                'images.*.image'      => __('messages.customer.review.validation.image_invalid'),
                'images.*.mimes'      => __('messages.customer.review.validation.image_mimes'),
                'images.*.max'        => __('messages.customer.review.validation.image_max'),
            ]
        );

        // ❌ Validation Failed
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation Error',
                'errors'  => $validator->errors()->first()
            ], 201);
        }

        // 🔎 Check Product
        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json([
                'status'  => false,
               'message' => __('messages.customer.review.not_found')
            ], 404);
        }

        // 💾 Create or Update Review
        $review = ProductReview::updateOrCreate(
            [
                'product_id' => $request->product_id,
                'user_id'    => $user->id
            ],
            [
                'rating' => $request->rating,
                'review' => $request->review
            ]
        );

        // 📸 Upload Multiple Images
        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $index => $file) {

                $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();

                $file->move(public_path('assets/review_images'), $filename);

                ProductReviewImage::create([
                    'review_id' => $review->id,
                    'image' => $filename
                ]);
            }
        }

        return response()->json([
            'status'  => true,
           'message' => __('messages.customer.review.submitted'),
            'data'    => $review
        ], 200);
    }


    // public function list($productId)
    // {
    //     $product = Product::with(['reviews.user', 'reviews.images'])
    //         ->find($productId);

    //     if (!$product) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Product not found.'
    //         ], 404);
    //     }

    //     $average = $product->reviews()->avg('rating');
    //     $count   = $product->reviews()->count();

    //     // ✅ Format reviews
    //     $reviews = $product->reviews->map(function ($review) {

    //         return [
    //             'id'        => $review->id,
    //             'rating'    => $review->rating,
    //             'review'    => $review->review,
    //             'user'      => [
    //                 'id'   => $review->user->id,
    //                 'name' => $review->user->name,
    //                 'profile_photo' => $review->user->profile_photo
    //                     ? $review->user->profile_photo
    //                     : null
    //             ],
    //             'images' => $review->images->map(function ($img) {
    //                 return  $img->image;
    //             })
    //         ];
    //     });

    //     return response()->json([
    //         'status'         => true,
    //         'message' => __('messages.customer.review.list_success'),
    //         'average_rating' => $average ? round($average, 1) : 0,
    //         'total_reviews'  => $count,
    //         'reviews'        => $reviews
    //     ], 200);
    // }

     public function list($productId)
    {
        $product = Product::with(['reviews.user', 'reviews.vendor', 'reviews.images'])
         ->find($productId);

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found.'
            ], 404);
        }

        $average = $product->reviews()->avg('rating');
        $count   = $product->reviews()->count();

        // ✅ Format reviews
        $reviews = $product->reviews->map(function ($review) {

    // 👇 Case 1: Real User
    if ($review->user_id && $review->user) {
        $userData = [
            'id'   => $review->user->id,
            'name' => $review->user->name,
            'profile_photo' => $review->user->profile_photo ?? null,
            'type' => 'user'
        ];
    }

    // 👇 Case 2: Vendor review
    elseif ($review->vendor_id && $review->vendor) {
        $userData = [
            'id'   => $review->vendor->id,
            'name' => $review->vendor->name,
            'profile_photo' => $review->vendor->profile_photo ?? null,
            'type' => 'vendor'
        ];
    }

    // 👇 Case 3: Manual / Fake review
    else {
        $userData = [
            'id'   => null,
            'name' => $review->username ?? 'Anonymous',
            'profile_photo' => $review->profile_image ?? null,
            'type' => 'manual'
        ];
    }

    return [
        'id'        => $review->id,
        'rating'    => $review->rating,
        'review'    => $review->review,
        'user'      => $userData,

        'images' => $review->images->map(function ($img) {
            return $img->image;
        })
    ];
});

        return response()->json([
            'status'         => true,
            'message' => __('messages.customer.review.list_success'),
            'average_rating' => $average ? round($average, 1) : 0,
            'total_reviews'  => $count,
            'reviews'        => $reviews
        ], 200);
    }

}