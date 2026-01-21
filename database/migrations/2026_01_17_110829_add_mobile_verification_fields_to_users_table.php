<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // 🔹 Mobile verification columns
            if (!Schema::hasColumn('users', 'is_mobile_verified')) {
                $table->boolean('is_mobile_verified')->default(false)->after('mobile');
            }

            if (!Schema::hasColumn('users', 'mobile_verified_at')) {
                $table->timestamp('mobile_verified_at')->nullable()->after('is_mobile_verified');
            }

            if (!Schema::hasColumn('users', 'mobile_otp')) {
                $table->string('mobile_otp', 6)->nullable()->after('mobile_verified_at');
            }

            if (!Schema::hasColumn('users', 'mobile_otp_sent_at')) {
                $table->timestamp('mobile_otp_sent_at')->nullable()->after('mobile_otp');
            }

            // 🔹 Replace single gov_id_document with multiple files
            if (Schema::hasColumn('users', 'gov_id_document')) {
                $table->dropColumn('gov_id_document');
            }

            if (!Schema::hasColumn('users', 'government_id')) {
                $table->longText('government_id')->nullable()->after('gov_id_number')
                    ->comment('Store multiple government ID files as JSON');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn([
                'is_mobile_verified',
                'mobile_verified_at',
                'mobile_otp',
                'mobile_otp_sent_at',
                'government_id'
            ]);

            // Re-add old single gov_id_document column
            if (!Schema::hasColumn('users', 'gov_id_document')) {
                $table->string('gov_id_document')->nullable()->after('gov_id_number');
            }
        });
    }
};
