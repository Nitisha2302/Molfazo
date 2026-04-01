<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionPackage extends Model
{
    protected $fillable = [
        'title',
        'review_count',
        'price'
    ];

    public function requests()
    {
        return $this->hasMany(PromotionRequest::class, 'package_id');
    }
}