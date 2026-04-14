<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'description',
        'answer',
        'status',
        'answered_at'
    ];

    /**
     * Relation with user (customer or vendor)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper: Check if enquiry is from vendor
     */
    public function isVendor()
    {
        return $this->type === 'vendor';
    }

    /**
     * Helper: Check if enquiry is from customer
     */
    public function isCustomer()
    {
        return $this->type === 'customer';
    }
}