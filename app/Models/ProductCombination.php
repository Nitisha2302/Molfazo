<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCombination extends Model
{

    protected $fillable = [
        'product_id',
        'combination',
        'price',
        'stock',
        'images'
    ];

    protected $casts = [
        'combination' => 'array',
        'images' => 'array'
    ];

    

}