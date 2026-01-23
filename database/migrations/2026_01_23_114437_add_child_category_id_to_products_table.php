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
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('child_category_id')
                ->nullable()
                ->after('sub_category_id');

            $table->foreign('child_category_id')
                ->references('id')
                ->on('child_categories')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['child_category_id']);
            $table->dropColumn('child_category_id');
        });
    }

};
