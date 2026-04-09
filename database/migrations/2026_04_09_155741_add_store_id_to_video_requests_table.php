<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('video_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('vendor_id');

            // Foreign key (recommended)
            $table->foreign('store_id')
                  ->references('id')
                  ->on('stores')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('video_requests', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn('store_id');
        });
    }
};
