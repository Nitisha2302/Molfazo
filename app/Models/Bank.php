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

    

}