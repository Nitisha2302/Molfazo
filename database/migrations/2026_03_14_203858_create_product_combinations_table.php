<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_combinations', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('product_id');

            $table->json('combination');

            $table->decimal('price',10,2)->nullable();

            $table->integer('stock')->default(0);

            $table->json('images')->nullable(); // multiple images

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_combinations');
    }
};