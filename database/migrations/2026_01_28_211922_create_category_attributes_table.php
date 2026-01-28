<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('category_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_category_id')
                  ->constrained('child_categories')
                  ->cascadeOnDelete();
            $table->json('attributes_json');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('category_attributes');
    }
};
