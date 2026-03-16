<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{

    protected $fillable=[

    'product_id',
    'variant_name',
    'variant_values'

    ];

    protected $casts=[

    'variant_values'=>'array'

    ];

}