<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','store_id','total_amount','status_id','delivery_method','delivery_address','payment_type','bank_id','account_number'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ✅ Order belongs to Store
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    // ✅ Order belongs to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

      public function combination()
    {
        return $this->belongsTo(ProductCombination::class, 'combination_id');
    }


}


