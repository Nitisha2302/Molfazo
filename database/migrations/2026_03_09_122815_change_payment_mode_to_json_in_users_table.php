<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Step 1: Add new JSON column
        Schema::table('users', function (Blueprint $table) {
            $table->json('payment_modes')->nullable()->after('payment_mode');
        });

        // Step 2: Convert old values to JSON
        DB::statement("
            UPDATE users
            SET payment_modes = JSON_ARRAY(payment_mode)
            WHERE payment_mode IS NOT NULL
        ");

        // Step 3: Drop old column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('payment_mode');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('payment_mode',['cod','bank'])->default('cod');
        });

        DB::statement("
            UPDATE users
            SET payment_mode = JSON_UNQUOTE(JSON_EXTRACT(payment_modes,'$[0]'))
        ");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('payment_modes');
        });
    }
};
