<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the 'banners' table
        Schema::create('banners', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('title')->nullable(); // Optional banner title
            $table->string('image'); // Banner image path
            $table->tinyInteger('status')->default(1)
                  ->comment('1 = Active, 0 = Inactive'); // Status: 1 = Active, 0 = Inactive
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the 'banners' table if it exists
        Schema::dropIfExists('banners');
    }
};
