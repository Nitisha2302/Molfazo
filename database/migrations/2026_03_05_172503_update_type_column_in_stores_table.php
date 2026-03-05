<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->enum('type_new', ['Retail','Online','Wholesale','Offline'])->nullable();
        });

        // Move existing data safely
        DB::statement("
            UPDATE stores SET type_new =
            CASE
                WHEN type = '1' THEN 'Retail'
                WHEN type = '2' THEN 'Online'
                WHEN type = '3' THEN 'Wholesale'
                WHEN type = '4' THEN 'Offline'
                ELSE 'Retail'
            END
        ");

        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->renameColumn('type_new', 'type');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->json('type_new')->nullable();
        });

        DB::statement("
            UPDATE stores SET type_new =
            CASE
                WHEN type = 'Retail' THEN '[1]'
                WHEN type = 'Online' THEN '[2]'
                WHEN type = 'Wholesale' THEN '[3]'
                WHEN type = 'Offline' THEN '[4]'
                ELSE '[1]'
            END
        ");

        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->renameColumn('type_new', 'type');
        });
    }
};