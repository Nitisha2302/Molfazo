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
        Schema::create('attribute_requests', function (Blueprint $table) {

        $table->id();

        $table->unsignedBigInteger('vendor_id');

        $table->unsignedBigInteger('child_category_id');

        $table->string('attribute_name');

        $table->string('attribute_value');

        $table->enum('status',['pending','approved','rejected'])->default('pending');

        $table->timestamps();

    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_requests');
    }
};
