<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;


class User extends Authenticatable
{
    protected $fillable = [
        'role',
        'status_id',
        'name',
        'email',
        'email_verified',
        'email_verified_at',
        'otp',
        'otp_sent_at',
        'password',
        'forgot_password_new',
        'forgot_password_sent_at',
        'mobile',
        'alt_mobile',
         'mobile_otp',
        'mobile_otp_sent_at',
        'is_mobile_verified',
        'mobile_verified_at',
        'country',
        'city',
        'profile_photo',
        'gov_id_type',
        'gov_id_number',
        'government_id',
        'terms_accepted',
        'approved_at',
        'apple_token',
        'facebook_token',
        'google_token',
        'is_social',
        'device_type',
        'device_token',
        'api_token',
        'fcm_token',
    ];

    // Role helpers
    public function isAdmin()
    {
        return $this->role === 1;
    }

    public function isVendor()
    {
        return $this->role === 2 && $this->status_id === 1;
    }

    public function isCustomer()
    {
        return $this->role === 3;
    }


    // Relationship to stores (Vendor only)
    public function stores()
    {
        return $this->hasMany(Store::class);
    }
    public function statusText()
    {
        return match($this->status_id) {
            1 => 'Approved',
            2 => 'Pending',
            3 => 'Rejected',
            4 => 'Blocked',
            default => 'Unknown'
        };
    }
    
}

