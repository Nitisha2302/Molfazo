<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocFieldsToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {

            $table->string('article')->nullable()->after('name');

            $table->decimal('price_before_discount',10,2)->nullable()->after('price');

            $table->decimal('cost_price',10,2)->nullable()->after('price_before_discount');

            $table->decimal('weight',10,2)->nullable()->after('cost_price');

            $table->string('dimensions')->nullable()->after('weight');

        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {

            $table->dropColumn([
                'article',
                'price_before_discount',
                'cost_price',
                'weight',
                'dimensions'
            ]);

        });
    }
}