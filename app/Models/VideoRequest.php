<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'vendor_id',
        'plan_id',
        'payment_screenshot',
        'status',
    ];

    // 🔗 Relationships

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function plan()
    {
        return $this->belongsTo(VideoPlan::class, 'plan_id');
    }

    // 🔥 Helper scopes

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }



    public function store()
    {
        return $this->belongsTo(\App\Models\Store::class, 'store_id');
    }

}