<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'duration_days',
        'video_limit',
    ];

    // 🔗 Relationships
    public function videoRequests()
    {
        return $this->hasMany(VideoRequest::class, 'plan_id');
    }
}