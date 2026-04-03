<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE product_review_images 
            CHANGE product_review_id review_id BIGINT UNSIGNED
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE product_review_images 
            CHANGE review_id product_review_id BIGINT UNSIGNED
        ");
    }
};