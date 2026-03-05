<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add new JSON column
        Schema::table('stores', function (Blueprint $table) {
            $table->json('type_new')->nullable()->after('type');
        });

        // Step 2: Convert existing ENUM values to JSON array
        DB::statement("
            UPDATE stores SET type_new =
            CASE
                WHEN type = 'Retail' THEN '[1]'
                WHEN type = 'Online' THEN '[2]'
                WHEN type = 'Wholesale' THEN '[3]'
                WHEN type = 'Offline' THEN '[4]'
                ELSE NULL
            END
        ");

        // Step 3: Drop old column
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        // Step 4: Rename new column
        Schema::table('stores', function (Blueprint $table) {
            $table->renameColumn('type_new', 'type');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->enum('type_old', ['Retail','Online','Wholesale','Offline'])->nullable();
        });

        DB::statement("
            UPDATE stores SET type_old =
            CASE
                WHEN JSON_CONTAINS(type, '1') THEN 'Retail'
                WHEN JSON_CONTAINS(type, '2') THEN 'Online'
                WHEN JSON_CONTAINS(type, '3') THEN 'Wholesale'
                WHEN JSON_CONTAINS(type, '4') THEN 'Offline'
                ELSE NULL
            END
        ");

        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->renameColumn('type_old', 'type');
        });
    }
};