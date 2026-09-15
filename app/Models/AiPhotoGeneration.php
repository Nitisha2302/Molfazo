<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiPhotoGeneration extends Model
{
    use HasFactory;

    public const MODE_GENERATE = 'generate';
    public const MODE_EDIT     = 'edit';

    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SUCCESS    = 'success';
    public const STATUS_FAILED     = 'failed';

    protected $fillable = [
        'vendor_id',
        'product_id',
        'mode',
        'prompt',
        'negative_prompt',
        'source_image',
        'output_image',
        'status',
        'credit_deducted',
        'error_message',
        'meta',
    ];

    protected $casts = [
        'credit_deducted' => 'boolean',
        'meta'            => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
