<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiPhotoOrder extends Model
{
    use HasFactory;

    public const STATUS_PENDING  = 'pending';
    public const STATUS_PAID     = 'paid';
    public const STATUS_FAILED   = 'failed';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_CANCELED = 'canceled';

    protected $fillable = [
        'vendor_id',
        'plan_id',
        'plan_name',
        'credits',
        'amount',
        'currency',
        'stripe_payment_intent_id',
        'stripe_checkout_session_id',
        'stripe_charge_id',
        'status',
        'credits_granted',
        'paid_at',
        'failure_reason',
        'meta',
    ];

    protected $casts = [
        'credits'         => 'integer',
        'amount'          => 'decimal:2',
        'credits_granted' => 'boolean',
        'paid_at'         => 'datetime',
        'meta'            => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function plan()
    {
        return $this->belongsTo(AiPhotoPlan::class, 'plan_id');
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }
}
