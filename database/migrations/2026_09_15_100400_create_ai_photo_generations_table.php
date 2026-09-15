<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Phase 2 (Stability AI). Created now so the credit ledger can point at it.
        Schema::create('ai_photo_generations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();

            $table->string('mode', 20);              // generate | edit
            $table->text('prompt');
            $table->text('negative_prompt')->nullable();

            $table->string('source_image')->nullable();   // input image for "edit" mode
            $table->string('output_image')->nullable();   // generated result

            // pending | processing | success | failed
            $table->string('status', 20)->default('pending');

            $table->boolean('credit_deducted')->default(false);
            $table->text('error_message')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['vendor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_photo_generations');
    }
};
