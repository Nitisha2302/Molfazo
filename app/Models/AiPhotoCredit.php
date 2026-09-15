<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiPhotoCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'balance',
        'total_purchased',
        'total_used',
    ];

    protected $casts = [
        'balance'         => 'integer',
        'total_purchased' => 'integer',
        'total_used'      => 'integer',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
