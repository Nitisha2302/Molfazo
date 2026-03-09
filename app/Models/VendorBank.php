<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorBank extends Model
{
    protected $fillable = [
        'user_id',
        'bank_id',
        'account_holder_name',
        'account_number',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    


}