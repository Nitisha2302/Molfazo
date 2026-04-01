<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminPaymentDetail extends Model
{
    protected $fillable = [
        'account_name',
        'account_number',
        'ifsc',
        'upi_id',
        'qr_code'
    ];
}