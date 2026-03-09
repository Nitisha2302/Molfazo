<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorBanksTable extends Migration
{
    public function up()
    {
        Schema::create('vendor_banks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('bank_id');
            $table->string('account_holder_name')->nullable();
            $table->string('account_number');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade');

            $table->unique(['user_id', 'bank_id']); // Prevent duplicate bank
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_banks');
    }
}
