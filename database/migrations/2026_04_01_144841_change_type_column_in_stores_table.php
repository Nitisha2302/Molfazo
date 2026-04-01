<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeColumnInStoresTable extends Migration
{
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->json('type')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->tinyInteger('type')->default(1)->change();
        });
    }
}
