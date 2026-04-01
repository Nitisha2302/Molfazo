<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionRequest extends Model
{
    protected $fillable = [
        'vendor_id',
        'product_id',
        'package_id',
        'payment_screenshot',
        'status'
    ];


    public function reviews()
    {
        return $this->hasMany(FakeReview::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    public function package()
    {
        return $this->belongsTo(\App\Models\PromotionPackage::class, 'package_id');
    }

    public function vendor()
    {
        return $this->belongsTo(\App\Models\User::class, 'vendor_id');
    }
}
