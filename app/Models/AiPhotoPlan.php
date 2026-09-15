<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiPhotoPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'credits',
        'price',
        'currency',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'credits'    => 'integer',
        'price'      => 'decimal:2',
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function orders()
    {
        return $this->hasMany(AiPhotoOrder::class, 'plan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Stripe works in the smallest currency unit (cents).
     * $3.50 -> 350
     */
    public function getAmountInCentsAttribute(): int
    {
        return (int) round(((float) $this->price) * 100);
    }
}
