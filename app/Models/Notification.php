<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Store;


class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'notification_type',
        'order_id',
        'sender_id',
        'notification_created_at',
        'product_id',
        'store_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
      public function store()
    {
        return $this->belongsTo(Product::class, 'store_id');
    }

    public function storeN()
    {
        return $this->belongsTo(\App\Models\Store::class, 'store_id');
    }
}
