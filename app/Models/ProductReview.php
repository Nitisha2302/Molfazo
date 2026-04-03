<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'vendor_id',
        'promotion_request_id',
        'review',
        'username',
        'title',
        'profile_image',
        'status',
        'is_verified_purchase',
        'rating'
    ];

    // ✅ Product relation
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ✅ Real user (customer)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ✅ Images
   public function images()
    {
        return $this->hasMany(ProductReviewImage::class, 'review_id');
    }
    
}