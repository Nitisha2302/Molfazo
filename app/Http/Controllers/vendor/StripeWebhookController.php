<?php

namespace App\Http\Controllers;

use App\Models\AiPhotoOrder;
use App\Services\AiPhotoCreditService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Stripe → our server. This is the ONLY trustworthy source of payment truth.
 *
 * Route must be:
 *   - POST, no auth middleware
 *   - excluded from CSRF (api.php routes already are)
 *   - reachable publicly over HTTPS
 */
class StripeWebhookController extends Controller
{
    protected AiPhotoCreditService $credits;

    public function __construct(AiPhotoCreditService $credits)
    {
        $this->credits = $credits;
    }

    public function handle(Request $request, StripeService $stripe)
    {
        $payload   = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        if (! $signature) {
            Log::warning('Stripe webhook: missing signature header');

            return response()->json(['error' => 'Missing signature'], 400);
        }

        try {
            $event = $stripe->constructWebhookEvent($payload, $signature);
        } catch (\UnexpectedValueException $e) {
            Log::warning('Stripe webhook: invalid payload', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Someone is POSTing fake events at us. Never process these.
            Log::warning('Stripe webhook: bad signature', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Stripe webhook received', ['type' => $event->type, 'id' => $event->id]);

        try {
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->onPaymentSucceeded($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->onPaymentFailed($event->data->object);
                    break;

                case 'payment_intent.canceled':
                    $this->onPaymentCanceled($event->data->object);
                    break;

                case 'charge.refunded':
                    $this->onChargeRefunded($event->data->object);
                    break;

                case 'charge.dispute.created':
                    $this->onDisputeCreated($event->data->object);
                    break;

                default:
                    Log::info('Stripe webhook: unhandled type', ['type' => $event->type]);
            }
        } catch (\Throwable $e) {
            Log::error('Stripe webhook: handler crashed', [
                'type'  => $event->type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return 500 so Stripe retries this event later.
            return response()->json(['error' => 'Handler failed'], 500);
        }

        // 200 tells Stripe "received, stop retrying".
        return response()->json(['received' => true], 200);
    }

    protected function onPaymentSucceeded($intent): void
    {
        $order = $this->resolveOrder($intent);

        if (! $order) {
            Log::warning('Stripe webhook: no order for intent', ['intent' => $intent->id]);

            return;
        }

        // Verify the amount actually charged matches what we asked for.
        $expected = (int) round(((float) $order->amount) * 100);

        if ((int) $intent->amount_received !== $expected) {
            Log::critical('Stripe webhook: amount mismatch — credits NOT granted', [
                'order_id' => $order->id,
                'expected' => $expected,
                'received' => $intent->amount_received,
            ]);

            return;
        }

        if ($order->status !== AiPhotoOrder::STATUS_PAID) {
            $order->update([
                'status'           => AiPhotoOrder::STATUS_PAID,
                'paid_at'          => now(),
                'stripe_charge_id' => $intent->latest_charge ?? null,
            ]);
        }

        // Idempotent — safe if verify-payment already granted them.
        $granted = $this->credits->grantCreditsForOrder($order->fresh());

        Log::info('Stripe webhook: payment succeeded', [
            'order_id'    => $order->id,
            'vendor_id'   => $order->vendor_id,
            'credits'     => $order->credits,
            'granted_now' => $granted,
        ]);

        // Optional: push notification to the seller
        $this->notifyVendor($order);
    }

    protected function onPaymentFailed($intent): void
    {
        $order = $this->resolveOrder($intent);

        if (! $order || $order->isPaid()) {
            return;
        }

        $order->update([
            'status'         => AiPhotoOrder::STATUS_FAILED,
            'failure_reason' => $intent->last_payment_error->message ?? 'Payment failed',
        ]);

        Log::info('Stripe webhook: payment failed', ['order_id' => $order->id]);
    }

    protected function onPaymentCanceled($intent): void
    {
        $order = $this->resolveOrder($intent);

        if (! $order || $order->isPaid()) {
            return;
        }

        $order->update(['status' => AiPhotoOrder::STATUS_CANCELED]);
    }

    protected function onChargeRefunded($charge): void
    {
        $order = AiPhotoOrder::where('stripe_charge_id', $charge->id)
            ->orWhere('stripe_payment_intent_id', $charge->payment_intent)
            ->first();

        if (! $order) {
            return;
        }

        // Full refund only. Partial refunds need a business decision —
        // logged here so finance can handle manually.
        if ((int) $charge->amount_refunded >= (int) $charge->amount) {
            $this->credits->revokeForOrder($order, 'Stripe refund issued');
            Log::info('Stripe webhook: refunded, credits revoked', ['order_id' => $order->id]);
        } else {
            Log::warning('Stripe webhook: PARTIAL refund — manual review needed', [
                'order_id' => $order->id,
                'refunded' => $charge->amount_refunded,
                'total'    => $charge->amount,
            ]);
        }
    }

    protected function onDisputeCreated($dispute): void
    {
        $order = AiPhotoOrder::where('stripe_charge_id', $dispute->charge)->first();

        if (! $order) {
            return;
        }

        $this->credits->revokeForOrder($order, 'Chargeback opened');

        Log::critical('Stripe webhook: CHARGEBACK', [
            'order_id'  => $order->id,
            'vendor_id' => $order->vendor_id,
            'amount'    => $dispute->amount,
        ]);
    }

    /**
     * Find the order from metadata first, then fall back to the intent id.
     */
    protected function resolveOrder($intent): ?AiPhotoOrder
    {
        $orderId = $intent->metadata->order_id ?? null;

        if ($orderId) {
            $order = AiPhotoOrder::find($orderId);
            if ($order) {
                return $order;
            }
        }

        return AiPhotoOrder::where('stripe_payment_intent_id', $intent->id)->first();
    }

    protected function notifyVendor(AiPhotoOrder $order): void
    {
        try {
            $vendor = $order->vendor;

            if (! $vendor || ! $vendor->fcm_token) {
                return;
            }

            $tokens = [[
                'fcm_token'   => $vendor->fcm_token,
                'device_type' => $vendor->device_type ?? 'android',
                'user_id'     => $vendor->id,
            ]];

            (new \App\Services\FCMService())->sendNotification($tokens, [
                'notification_type' => 6,
                'title'             => 'Credits added',
                'body'              => "{$order->credits} AI photo credits have been added to your account.",
                'order_id'          => $order->id,
            ], true);
        } catch (\Throwable $e) {
            Log::warning('AI photo: vendor notification failed', ['error' => $e->getMessage()]);
        }
    }
}
