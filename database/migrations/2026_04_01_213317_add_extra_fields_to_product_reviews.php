<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('promotion_request_id')->nullable()->after('product_id');

            $table->string('username')->nullable()->after('review');
            $table->string('profile_image')->nullable()->after('username');

        });
    }

    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropColumn([
                'vendor_id',
                'promotion_request_id',
                'username',
                'profile_image',
            ]);
        });
    }
};