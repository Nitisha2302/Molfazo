<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable(); // receiver user (vendor/store owner)
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->integer('notification_type')->default(1);

            // optional useful ids
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('sender_id')->nullable(); // customer id

            $table->timestamp('notification_created_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
