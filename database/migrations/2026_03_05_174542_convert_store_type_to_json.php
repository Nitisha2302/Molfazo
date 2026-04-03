<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Step 1: Add new column if not exists
        if (!Schema::hasColumn('stores', 'type_new')) {
            Schema::table('stores', function (Blueprint $table) {
                $table->json('type_new')->nullable()->after('type');
            });
        }

        // ✅ Step 2: Convert data ONLY if old type exists
        if (Schema::hasColumn('stores', 'type')) {
            DB::statement("
                UPDATE stores SET type_new =
                CASE
                    WHEN type = 'Retail' THEN '[1]'
                    WHEN type = 'Online' THEN '[2]'
                    WHEN type = 'Wholesale' THEN '[3]'
                    WHEN type = 'Offline' THEN '[4]'
                    ELSE type_new
                END
            ");

            // ✅ Drop old column
            Schema::table('stores', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        // ✅ Step 3: Rename using CHANGE (MariaDB fix 🔥)
        if (Schema::hasColumn('stores', 'type_new')) {

            DB::statement("
                ALTER TABLE stores 
                CHANGE type_new type LONGTEXT
            ");
        }
    }

    public function down(): void
    {
        // reverse safely
        if (Schema::hasColumn('stores', 'type')) {

            Schema::table('stores', function (Blueprint $table) {
                $table->enum('type_old', ['Retail','Online','Wholesale','Offline'])->nullable();
            });

            DB::statement("
                UPDATE stores SET type_old =
                CASE
                    WHEN type LIKE '%1%' THEN 'Retail'
                    WHEN type LIKE '%2%' THEN 'Online'
                    WHEN type LIKE '%3%' THEN 'Wholesale'
                    WHEN type LIKE '%4%' THEN 'Offline'
                    ELSE NULL
                END
            ");

            Schema::table('stores', function (Blueprint $table) {
                $table->dropColumn('type');
            });

            DB::statement("
                ALTER TABLE stores 
                CHANGE type_old type 
                ENUM('Retail','Online','Wholesale','Offline')
            ");
        }
    }
};