<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ CASE 1: Agar type already exist hai → kuch mat karo
        if (Schema::hasColumn('stores', 'type') && !Schema::hasColumn('stores', 'type_new')) {
            return;
        }

        // ✅ CASE 2: Agar type_new hai → usko type me convert karo
        if (Schema::hasColumn('stores', 'type_new')) {

            // Agar type bhi exist karta hai → drop first
            if (Schema::hasColumn('stores', 'type')) {
                Schema::table('stores', function (Blueprint $table) {
                    $table->dropColumn('type');
                });
            }

            // Rename using CHANGE (MariaDB safe)
            DB::statement("
                ALTER TABLE stores 
                CHANGE type_new type 
                ENUM('Retail','Online','Wholesale','Offline')
            ");
        }
    }

    public function down(): void
    {
        // optional (ignore)
    }
};