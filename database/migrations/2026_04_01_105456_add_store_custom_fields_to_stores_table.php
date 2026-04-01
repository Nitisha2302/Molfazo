<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('background_color')->nullable(); // Add instead of modify
            $table->json('return_policy')->nullable();
            $table->json('delivery_policy')->nullable();
            $table->string('delivery_days')->nullable();
            $table->json('social_links')->nullable();
        });
    }

    public function down()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['background_color', 'return_policy', 'delivery_policy', 'delivery_days', 'social_links']);
        });
    }
};
