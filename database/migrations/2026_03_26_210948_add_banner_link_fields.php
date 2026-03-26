<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->enum('link_type', ['store', 'product'])->nullable()->after('title');
            $table->json('link_ids')->nullable()->after('link_type');
        });
    }

    public function down()
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['link_type', 'link_ids']);
        });
    }
};
