<?php

namespace App\Console\Commands;

use App\Models\AiPhotoCredit;
use App\Models\AiPhotoOrder;
use App\Services\AiPhotoCreditService;
use Illuminate\Console\Command;

/**
 * Safety net. Run daily on a schedule.
 *
 *   php artisan ai-photo:audit
 *   php artisan ai-photo:audit --fix-stuck
 *
 * Finds:
 *  1. Wallets whose balance does not match their ledger (should never happen)
 *  2. Orders marked PAID but with credits_granted = false
 *     (happens if a webhook was missed while the server was down)
 */
class AuditAiPhotoCredits extends Command
{
    protected $signature = 'ai-photo:audit {--fix-stuck : Grant credits for paid orders that never received them}';

    protected $description = 'Verify AI photo credit ledger integrity and recover missed payments';

    public function handle(AiPhotoCreditService $service): int
    {
        $this->info('=== AI Photo credit audit ===');

        // ---- 1. ledger vs balance -------------------------------
        $broken = 0;

        AiPhotoCredit::chunk(200, function ($wallets) use ($service, &$broken) {
            foreach ($wallets as $wallet) {
                $check = $service->verifyIntegrity($wallet->vendor_id);

                if (! $check['ok']) {
                    $broken++;
                    $this->error(sprintf(
                        'MISMATCH vendor #%d — stored: %d, ledger: %d',
                        $check['vendor_id'],
                        $check['stored_balance'],
                        $check['ledger_sum']
                    ));
                }
            }
        });

        if ($broken === 0) {
            $this->info('✔ All wallets match their ledger.');
        } else {
            $this->error("✘ {$broken} wallet(s) out of sync — investigate before touching anything.");
        }

        // ---- 2. paid orders missing credits ---------------------
        $stuck = AiPhotoOrder::where('status', AiPhotoOrder::STATUS_PAID)
            ->where('credits_granted', false)
            ->get();

        if ($stuck->isEmpty()) {
            $this->info('✔ No paid orders are missing credits.');

            return self::SUCCESS;
        }

        $this->warn("Found {$stuck->count()} PAID order(s) with no credits granted:");

        foreach ($stuck as $order) {
            $this->line("  order #{$order->id} — vendor #{$order->vendor_id} — {$order->credits} credits — {$order->stripe_payment_intent_id}");
        }

        if (! $this->option('fix-stuck')) {
            $this->comment('Re-run with --fix-stuck to grant them.');

            return self::SUCCESS;
        }

        $fixed = 0;

        foreach ($stuck as $order) {
            if ($service->grantCreditsForOrder($order)) {
                $fixed++;
                $this->info("  ✔ granted {$order->credits} credits for order #{$order->id}");
            }
        }

        $this->info("Done. {$fixed} order(s) recovered.");

        return self::SUCCESS;
    }
}
