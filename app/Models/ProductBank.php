<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBank extends Model
{
    protected $table = 'product_bank';

    protected $fillable = [
        'product_id',
        'bank_id',
        'account_holder_name',
        'account_number',
        'ifsc_code',
        'phone_number',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Product relation
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Bank relation
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}