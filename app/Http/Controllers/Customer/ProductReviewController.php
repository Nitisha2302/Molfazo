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
                'message' => 'unauthorized.'
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
                'product_id.required' => 'Product ID is required.',
                'product_id.exists'   => 'The selected product does not exist.',
                'rating.required'     => 'Rating is required.',
                'rating.integer'      => 'Rating must be a number.',
                'rating.min'          => 'Rating must be at least 1 star.',
                'rating.max'          => 'Rating cannot be more than 5 stars.',
                'review.string'       => 'Review must be valid text.',
                'review.max'          => 'Review cannot exceed 1000 characters.',
                'images.*.image'      => 'Each file must be an image.',
                'images.*.mimes'      => 'Images must be jpg, jpeg or png.',
                'images.*.max'        => 'Each image must not exceed 2MB.'
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
                'message' => 'Product not found.'
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
            'message' => 'Review submitted successfully.',
            'data'    => $review
        ], 200);
    }


    // ⭐ Get Product Reviews
    // public function list($productId)
    // {
    //     $product = Product::with(['reviews.user', 'reviews.images'])
    //     ->find($productId);

    //     if (!$product) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Product not found.'
    //         ], 404);
    //     }

    //     $average = $product->reviews()->avg('rating');
    //     $count   = $product->reviews()->count();

    //     return response()->json([
    //         'status'         => true,
    //         'average_rating' => $average ? round($average, 1) : 0,
    //         'total_reviews'  => $count,
    //         'reviews'        => $product->reviews
    //     ], 200);
    // }

    public function list($productId)
    {
        $product = Product::with(['reviews.user', 'reviews.images'])
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

            return [
                'id'        => $review->id,
                'rating'    => $review->rating,
                'review'    => $review->review,
                'user'      => [
                    'id'   => $review->user->id,
                    'name' => $review->user->name,
                    'profile_photo' => $review->user->profile_photo
                        ? $review->user->profile_photo
                        : null
                ],
                'images' => $review->images->map(function ($img) {
                    return  $img->image;
                })
            ];
        });

        return response()->json([
            'status'         => true,
            'average_rating' => $average ? round($average, 1) : 0,
            'total_reviews'  => $count,
            'reviews'        => $reviews
        ], 200);
    }

}