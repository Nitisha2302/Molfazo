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
        Schema::create('child_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_category_id')
                ->constrained('sub_categories')
                ->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->tinyInteger('status_id')->default(1);
            $table->timestamps();

            $table->unique(['sub_category_id', 'name']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_categories');
    }
};
