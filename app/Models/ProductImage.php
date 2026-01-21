<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image',
        'color',
        'is_primary'
    ];

    // Relation back to product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
