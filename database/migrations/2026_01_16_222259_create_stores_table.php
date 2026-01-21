<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->tinyInteger('type')->default(1)->comment('1=Retail,2=Online,3=Wholesale');
            $table->boolean('delivery_by_seller')->default(false);
            $table->boolean('self_pickup')->default(false);
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->string('working_hours')->nullable();
            $table->tinyInteger('status_id')->default(2)->comment('1=Active,2=Pending,3=Rejected');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stores');
    }
};
