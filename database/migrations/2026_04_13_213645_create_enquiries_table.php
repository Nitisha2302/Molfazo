<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up()
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // who raised query
            $table->enum('type', ['customer', 'vendor']);

            $table->string('title');
            $table->text('description');

            $table->text('answer')->nullable();
            $table->timestamp('answered_at')->nullable();

            $table->enum('status', ['pending', 'answered'])->default('pending');

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
