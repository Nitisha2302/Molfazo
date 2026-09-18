<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ADDS columns only. Nothing is removed.
     *
     * `payment_screenshot` and the old manual-approval flow still work —
     * existing rows are untouched and old requests keep their status.
     */
    public function up(): void
    {
        Schema::table('promotion_requests', function (Blueprint $table) {

            // Stripe payment id sent by the frontend (pi_xxx / ch_xxx / txn id)
            $table->string('payment_id')->nullable()->after('payment_screenshot');

            // what the frontend reported: succeeded | pending | failed
            $table->string('payment_status', 20)->nullable()->after('payment_id');

            // stripe | manual | cash — how it was paid
            $table->string('payment_method', 30)->default('manual')->after('payment_status');

            // amount charged, snapshot of the package price at purchase time
            $table->decimal('amount_paid', 10, 2)->nullable()->after('payment_method');

            $table->timestamp('paid_at')->nullable()->after('amount_paid');

            // true  = approved automatically because payment succeeded
            // false = old flow, admin approved it by hand
            $table->boolean('auto_approved')->default(false)->after('paid_at');

            $table->index('payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('promotion_requests', function (Blueprint $table) {
            $table->dropIndex(['payment_id']);
            $table->dropColumn([
                'payment_id',
                'payment_status',
                'payment_method',
                'amount_paid',
                'paid_at',
                'auto_approved',
            ]);
        });
    }
};
