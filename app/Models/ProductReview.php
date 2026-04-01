<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'vendor_id',
        'promotion_request_id',
        'product_rating',
        'seller_rating',
        'review',
        'username',
        'title'
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
        return $this->hasMany(ReviewImage::class);
    }
}