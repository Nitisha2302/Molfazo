<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('sender_id');

            $table->text('message')->nullable();

            $table->string('type')->default('text'); // text,image,file,system
            $table->longText('meta')->nullable(); // store json meta like image path, file url etc

            $table->timestamp('send_at')->nullable();
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');

            // Index
            $table->index(['conversation_id', 'sender_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
