<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // append-only ledger — every credit movement is recorded here.
        // balance in ai_photo_credits must always equal SUM(amount) here.
        Schema::create('ai_photo_credit_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');

            // purchase | usage | refund | admin_adjustment | rollback
            $table->string('type', 30);

            // positive = credit added, negative = credit deducted
            $table->integer('amount');
            $table->unsignedInteger('balance_after');

            // polymorphic-ish source pointer
            $table->string('source_type')->nullable();   // App\Models\AiPhotoOrder
            $table->unsignedBigInteger('source_id')->nullable();

            $table->string('description')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['vendor_id', 'created_at']);
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_photo_credit_transactions');
    }
};
