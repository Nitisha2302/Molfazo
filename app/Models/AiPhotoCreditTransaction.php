<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiPhotoCreditTransaction extends Model
{
    use HasFactory;

    public const TYPE_PURCHASE   = 'purchase';
    public const TYPE_USAGE      = 'usage';
    public const TYPE_REFUND     = 'refund';
    public const TYPE_ADJUSTMENT = 'admin_adjustment';
    public const TYPE_ROLLBACK   = 'rollback';

    protected $fillable = [
        'vendor_id',
        'type',
        'amount',
        'balance_after',
        'source_type',
        'source_id',
        'description',
        'meta',
    ];

    protected $casts = [
        'amount'        => 'integer',
        'balance_after' => 'integer',
        'meta'          => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function source()
    {
        return $this->morphTo(null, 'source_type', 'source_id');
    }
}
