<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_photo_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->constrained('ai_photo_plans')->nullOnDelete();

            // snapshot of the plan at purchase time (plan may be edited/deleted later)
            $table->string('plan_name');
            $table->unsignedInteger('credits');
            $table->decimal('amount', 8, 2);
            $table->string('currency', 3)->default('usd');

            // stripe
            $table->string('stripe_payment_intent_id')->nullable()->unique();
            $table->string('stripe_checkout_session_id')->nullable()->unique();
            $table->string('stripe_charge_id')->nullable();

            // pending | paid | failed | refunded | canceled
            $table->string('status', 20)->default('pending');

            $table->boolean('credits_granted')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['vendor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_photo_orders');
    }
};
