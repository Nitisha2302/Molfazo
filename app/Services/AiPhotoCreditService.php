<?php

namespace App\Services;

use App\Models\AiPhotoCredit;
use App\Models\AiPhotoCreditTransaction;
use App\Models\AiPhotoOrder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Single source of truth for AI-photo credit movement.
 *
 * RULES (do not break these):
 *  1. NOTHING else in the app may write to ai_photo_credits.balance directly.
 *  2. Every balance change writes a matching row in ai_photo_credit_transactions.
 *  3. Every change happens inside a DB transaction with a row-level lock
 *     (lockForUpdate) so two concurrent requests cannot double-spend.
 */
class AiPhotoCreditService
{
    /**
     * Get (or lazily create) the balance row for a vendor.
     */
    public function getOrCreateWallet(int $vendorId): AiPhotoCredit
    {
        return AiPhotoCredit::firstOrCreate(
            ['vendor_id' => $vendorId],
            ['balance' => 0, 'total_purchased' => 0, 'total_used' => 0]
        );
    }

    public function getBalance(int $vendorId): int
    {
        return (int) (AiPhotoCredit::where('vendor_id', $vendorId)->value('balance') ?? 0);
    }

    /**
     * Grant credits from a PAID order. IDEMPOTENT.
     *
     * Stripe can deliver the same webhook many times, and the success-redirect
     * can fire at the same moment as the webhook. Both call this method.
     * The credits_granted flag + row lock guarantees credits are added ONCE.
     *
     * @return bool true if credits were granted now, false if already granted before
     */
    public function grantCreditsForOrder(AiPhotoOrder $order): bool
    {
        return DB::transaction(function () use ($order) {

            // Re-read the order WITH a lock so a parallel webhook waits here.
            $order = AiPhotoOrder::whereKey($order->id)->lockForUpdate()->first();

            if (! $order) {
                return false;
            }

            // Already granted → do nothing. This is the idempotency guard.
            if ($order->credits_granted) {
                return false;
            }

            if ($order->status !== AiPhotoOrder::STATUS_PAID) {
                return false;
            }

            $wallet = AiPhotoCredit::where('vendor_id', $order->vendor_id)
                ->lockForUpdate()
                ->first();

            if (! $wallet) {
                $wallet = AiPhotoCredit::create([
                    'vendor_id'       => $order->vendor_id,
                    'balance'         => 0,
                    'total_purchased' => 0,
                    'total_used'      => 0,
                ]);
                $wallet = AiPhotoCredit::whereKey($wallet->id)->lockForUpdate()->first();
            }

            $wallet->balance         += $order->credits;
            $wallet->total_purchased += $order->credits;
            $wallet->save();

            AiPhotoCreditTransaction::create([
                'vendor_id'     => $order->vendor_id,
                'type'          => AiPhotoCreditTransaction::TYPE_PURCHASE,
                'amount'        => $order->credits,
                'balance_after' => $wallet->balance,
                'source_type'   => AiPhotoOrder::class,
                'source_id'     => $order->id,
                'description'   => "Purchased {$order->credits} credits ({$order->plan_name})",
                'meta'          => [
                    'amount'    => (string) $order->amount,
                    'currency'  => $order->currency,
                    'intent_id' => $order->stripe_payment_intent_id,
                ],
            ]);

            $order->credits_granted = true;
            $order->save();

            return true;
        });
    }

    /**
     * Deduct credits (phase 2: one per generated photo).
     * Throws if the vendor cannot afford it — never lets balance go negative.
     */
    public function deduct(int $vendorId, int $amount = 1, $source = null, ?string $description = null): AiPhotoCreditTransaction
    {
        if ($amount < 1) {
            throw new RuntimeException('Deduction amount must be at least 1.');
        }

        return DB::transaction(function () use ($vendorId, $amount, $source, $description) {

            $wallet = AiPhotoCredit::where('vendor_id', $vendorId)
                ->lockForUpdate()
                ->first();

            if (! $wallet || $wallet->balance < $amount) {
                throw new RuntimeException('INSUFFICIENT_CREDITS');
            }

            $wallet->balance    -= $amount;
            $wallet->total_used += $amount;
            $wallet->save();

            return AiPhotoCreditTransaction::create([
                'vendor_id'     => $vendorId,
                'type'          => AiPhotoCreditTransaction::TYPE_USAGE,
                'amount'        => -$amount,
                'balance_after' => $wallet->balance,
                'source_type'   => $source ? get_class($source) : null,
                'source_id'     => $source->id ?? null,
                'description'   => $description ?? "Used {$amount} credit(s) for AI photo generation",
            ]);
        });
    }

    /**
     * Give credits back (Stability AI call failed after we already deducted).
     */
    public function refund(int $vendorId, int $amount, $source = null, ?string $description = null): AiPhotoCreditTransaction
    {
        return DB::transaction(function () use ($vendorId, $amount, $source, $description) {

            $wallet = AiPhotoCredit::where('vendor_id', $vendorId)->lockForUpdate()->first();

            if (! $wallet) {
                $wallet = AiPhotoCredit::create(['vendor_id' => $vendorId, 'balance' => 0]);
                $wallet = AiPhotoCredit::whereKey($wallet->id)->lockForUpdate()->first();
            }

            $wallet->balance += $amount;
            // it was never really "used", so roll that counter back too
            $wallet->total_used = max(0, $wallet->total_used - $amount);
            $wallet->save();

            return AiPhotoCreditTransaction::create([
                'vendor_id'     => $vendorId,
                'type'          => AiPhotoCreditTransaction::TYPE_ROLLBACK,
                'amount'        => $amount,
                'balance_after' => $wallet->balance,
                'source_type'   => $source ? get_class($source) : null,
                'source_id'     => $source->id ?? null,
                'description'   => $description ?? "Refunded {$amount} credit(s) — generation failed",
            ]);
        });
    }

    /**
     * Remove credits after a Stripe refund / chargeback.
     * Balance is allowed to floor at 0 (vendor may already have spent them).
     */
    public function revokeForOrder(AiPhotoOrder $order, string $reason = 'Payment refunded'): void
    {
        DB::transaction(function () use ($order, $reason) {

            $order = AiPhotoOrder::whereKey($order->id)->lockForUpdate()->first();

            if (! $order || ! $order->credits_granted) {
                return;
            }

            $wallet = AiPhotoCredit::where('vendor_id', $order->vendor_id)->lockForUpdate()->first();
            if (! $wallet) {
                return;
            }

            $take = min($wallet->balance, $order->credits);

            $wallet->balance         -= $take;
            $wallet->total_purchased = max(0, $wallet->total_purchased - $order->credits);
            $wallet->save();

            AiPhotoCreditTransaction::create([
                'vendor_id'     => $order->vendor_id,
                'type'          => AiPhotoCreditTransaction::TYPE_REFUND,
                'amount'        => -$take,
                'balance_after' => $wallet->balance,
                'source_type'   => AiPhotoOrder::class,
                'source_id'     => $order->id,
                'description'   => $reason,
                'meta'          => ['requested' => $order->credits, 'actually_removed' => $take],
            ]);

            $order->credits_granted = false;
            $order->status          = AiPhotoOrder::STATUS_REFUNDED;
            $order->save();
        });
    }

    /**
     * Health check — ledger sum must equal stored balance.
     * Call from a console command / test.
     */
    public function verifyIntegrity(int $vendorId): array
    {
        $stored = $this->getBalance($vendorId);
        $ledger = (int) AiPhotoCreditTransaction::where('vendor_id', $vendorId)->sum('amount');

        return [
            'vendor_id'      => $vendorId,
            'stored_balance' => $stored,
            'ledger_sum'     => $ledger,
            'ok'             => $stored === $ledger,
        ];
    }
}
