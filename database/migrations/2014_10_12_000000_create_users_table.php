<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Role & Status
            $table->tinyInteger('role_id')->default(3)->comment('1=Admin,2=Vendor,3=Customer');
            $table->tinyInteger('status_id')->default(1)->comment('1=Active,2=Pending,3=Rejected,4=Blocked');

            // Basic profile
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('email_verified')->default(false);
            $table->string('otp')->nullable();
            $table->timestamp('otp_sent_at')->nullable();
            $table->string('password')->nullable();
            $table->string('mobile')->unique()->nullable();
            $table->string('alt_mobile')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('profile_photo')->nullable();

            // Vendor KYC (consider moving to vendor_profiles table)
            $table->string('gov_id_type')->nullable();
            $table->string('gov_id_number')->nullable();
            $table->string('gov_id_document')->nullable();

            // Vendor approval
            $table->boolean('terms_accepted')->default(false);
            $table->timestamp('approved_at')->nullable();

            // Social login & device info
            $table->string('apple_token')->nullable();
            $table->string('facebook_token')->nullable();
            $table->string('google_token')->nullable();
            $table->boolean('is_social')->default(false);
            $table->string('device_type')->nullable();
            $table->string('device_token')->nullable();
            $table->string('auth_token')->nullable();
            $table->string('fcm_token')->nullable();

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
