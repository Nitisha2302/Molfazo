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
    Schema::table('users', function (Blueprint $table) {

        // DIDIT KYC
        $table->string('kyc_status')->default('pending'); 
        // pending, verified, failed

        $table->string('kyc_session_id')->nullable();
        $table->json('kyc_response')->nullable();

        // OPTIONAL extracted data
        $table->string('verified_name')->nullable();
        $table->string('verified_doc_type')->nullable();
        $table->string('verified_doc_number')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
