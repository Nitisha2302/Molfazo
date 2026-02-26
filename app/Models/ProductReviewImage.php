<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReviewImage extends Model
{
    protected $fillable = [
        'product_review_id',
        'image'
    ];

    public function review()
    {
        return $this->belongsTo(ProductReview::class, 'product_review_id');
    }
}