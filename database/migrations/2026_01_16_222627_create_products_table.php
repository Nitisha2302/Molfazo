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
       Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('sub_category_id')->constrained('sub_categories')->onDelete('cascade');

            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->integer('available_quantity')->default(0);

            // Delivery info
            $table->boolean('delivery_available')->default(true);
            $table->decimal('delivery_price',10,2)->nullable();
            $table->string('delivery_time')->nullable();

            $table->json('characteristics')->nullable(); // age, size, etc.
            $table->json('tags')->nullable();
            $table->tinyInteger('status_id')->default(1)->comment('1=Active,2=Blocked,3=Deleted');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
