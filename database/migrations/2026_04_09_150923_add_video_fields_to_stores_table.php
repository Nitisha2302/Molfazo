<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVideoFieldsToStoresTable extends Migration
{
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {

            $table->string('background_video')->nullable()->after('store_background_image');

            $table->timestamp('video_expires_at')->nullable();

            $table->foreignId('video_plan_id')
                  ->nullable()
                  ->constrained('video_plans')
                  ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('stores', function (Blueprint $table) {

            $table->dropForeign(['video_plan_id']);

            $table->dropColumn([
                'background_video',
                'video_expires_at',
                'video_plan_id'
            ]);
        });
    }
}