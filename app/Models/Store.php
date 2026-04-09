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
        'working_hours', 'status_id', 'approved_at','government_id','store_background_image','reject_reason','background_color','return_policy',
        'delivery_policy','delivery_days','social_links',
        'background_video',
    'video_expires_at',
    'video_plan_id'
    ];

    protected $casts = [
        'type' => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function vendorBanks()
    {
        return $this->hasMany(\App\Models\VendorBank::class, 'user_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
