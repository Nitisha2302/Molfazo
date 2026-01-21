<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'email', 'mobile', 'country', 'city', 'address',
        'type', 'delivery_by_seller', 'self_pickup', 'logo', 'description',
        'working_hours', 'status_id', 'approved_at'
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
