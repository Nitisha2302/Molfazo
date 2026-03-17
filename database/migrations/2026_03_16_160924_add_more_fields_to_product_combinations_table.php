<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_combinations', function (Blueprint $table) {

            $table->text('description')->nullable()->after('combination');

            $table->decimal('price_before_discount', 10, 2)
                  ->nullable()
                  ->after('price');

            $table->decimal('cost_price', 10, 2)
                  ->nullable()
                  ->after('price_before_discount');

        });
    }

    public function down()
    {
        Schema::table('product_combinations', function (Blueprint $table) {

            $table->dropColumn([
                'description',
                'price_before_discount',
                'cost_price'
            ]);

        });
    }
};
