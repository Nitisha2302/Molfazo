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
        Schema::table('banners', function (Blueprint $table) {
            // Drop the old single city column if it exists
            if (Schema::hasColumn('banners', 'city')) {
                $table->dropColumn('city');
            }

            // Add a JSON column to store multiple cities
            $table->json('cities')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('cities');
        });
    }
};
