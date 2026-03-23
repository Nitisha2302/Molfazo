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
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('combination_id')->nullable()->after('product_id');

            $table->foreign('combination_id')
                ->references('id')
                ->on('product_combinations')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['combination_id']);
            $table->dropColumn('combination_id');
        });
    }
};
