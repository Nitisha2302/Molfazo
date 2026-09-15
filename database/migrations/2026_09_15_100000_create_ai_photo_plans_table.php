<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_photo_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                       // "10 Photos"
            $table->unsignedInteger('credits');           // 10 / 20 / 50
            $table->decimal('price', 8, 2);               // 2.00 / 3.50 / 7.00
            $table->string('currency', 3)->default('usd');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_photo_plans');
    }
};
