<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    // ✅ Allow mass assignment
    protected $fillable = [
        'name',
        'logo',
        'status'
    ];

    // ✅ Many-to-Many Relation with Products
    // public function products()
    // {
    //     return $this->belongsToMany(Product::class, 'product_bank')
    //                 ->withTimestamps();
    // }

    public function productBanks()
{
    return $this->hasMany(ProductBank::class);
}

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_bank')
            ->withPivot([
                'account_holder_name',
                'account_number',
                'ifsc_code',
                'phone_number'
            ]);
    }
}