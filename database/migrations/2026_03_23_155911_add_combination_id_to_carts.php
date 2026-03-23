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
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('combination_id')
                  ->nullable()
                  ->after('product_id');

            // ✅ Add foreign key (recommended)
            $table->foreign('combination_id')
                  ->references('id')
                  ->on('product_combinations')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {

            // ✅ First drop foreign key
            $table->dropForeign(['combination_id']);

            // ✅ Then drop column
            $table->dropColumn('combination_id');
        });
    }
};