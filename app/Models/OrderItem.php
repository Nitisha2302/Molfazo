<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price'
    ];

    /* =========================
       PRODUCT RELATION
    ========================= */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /* =========================
       ORDER RELATION (optional but good)
    ========================= */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

      public function combination()
    {
        return $this->belongsTo(ProductCombination::class, 'combination_id');
    }

    
}
