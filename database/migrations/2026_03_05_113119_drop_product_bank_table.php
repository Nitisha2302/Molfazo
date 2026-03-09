<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('product_bank');
    }

    public function down(): void
    {
        Schema::create('product_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_id')->constrained()->onDelete('cascade');
            $table->string('account_holder_name');
            $table->string('account_number');
            $table->string('ifsc_code');
            $table->string('phone_number');
            $table->timestamps();
        });
    }
};