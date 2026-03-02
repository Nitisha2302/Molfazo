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
        Schema::table('orders', function (Blueprint $table) {

            // Add bank_id column
            $table->unsignedBigInteger('bank_id')
                  ->nullable()
                  ->after('payment_type');

            // Foreign key relation
            $table->foreign('bank_id')
                  ->references('id')
                  ->on('banks')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // Drop foreign key first
            $table->dropForeign(['bank_id']);

            // Drop column
            $table->dropColumn('bank_id');
        });
    }
};