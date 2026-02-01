<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'full_name',
        'mobile',
        'address',
        'city',
        'state',
        'pincode',
        'is_default',
    ];
}
