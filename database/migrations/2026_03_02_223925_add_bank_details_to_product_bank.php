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
        Schema::table('product_bank', function (Blueprint $table) {

            $table->string('account_holder_name')->nullable()->after('bank_id');
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('phone_number')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_bank', function (Blueprint $table) {

            $table->dropColumn([
                'account_holder_name',
                'account_number',
                'ifsc_code',
                'phone_number'
            ]);

        });
    }
};