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
        Schema::create('orders', function(Blueprint $table){
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // buyer
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->decimal('total_amount',10,2)->default(0);
            $table->tinyInteger('status_id')->default(1)->comment('1=New,2=Accepted,3=Completed,4=Cancelled');
            $table->string('delivery_method')->nullable();
            $table->text('delivery_address')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
