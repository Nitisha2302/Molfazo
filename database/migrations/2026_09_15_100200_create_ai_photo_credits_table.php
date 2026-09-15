<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // one row per vendor = current balance (fast read, row-locked on write)
        Schema::create('ai_photo_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->unique()->constrained('users')->onDelete('cascade');
            $table->unsignedInteger('balance')->default(0);
            $table->unsignedInteger('total_purchased')->default(0);
            $table->unsignedInteger('total_used')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_photo_credits');
    }
};
