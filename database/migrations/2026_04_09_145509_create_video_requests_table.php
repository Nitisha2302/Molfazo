<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('video_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('video_plans')->onDelete('cascade');

            $table->string('payment_screenshot');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_requests');
    }
};
