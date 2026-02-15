<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('user_one_id');
            $table->unsignedBigInteger('user_two_id');

            $table->unsignedBigInteger('last_message_id')->nullable();
            $table->text('last_message_preview')->nullable();
            $table->timestamp('last_message_at')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('user_one_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_two_id')->references('id')->on('users')->onDelete('cascade');

            // Index
            $table->index(['user_one_id', 'user_two_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
