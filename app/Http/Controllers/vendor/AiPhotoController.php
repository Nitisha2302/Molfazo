<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\AiPhotoCreditTransaction;
use App\Models\AiPhotoOrder;
use App\Models\AiPhotoPlan;
use App\Services\AiPhotoCreditService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AiPhotoController extends Controller
{
    protected AiPhotoCreditService $credits;

    public function __construct(AiPhotoCreditService $credits)
    {
        $this->credits = $credits;
    }

    /**
     * GET /api/vendor/ai-photo/plans
     * Lists active plans + the vendor's current balance.
     */
    public function plans(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $plans = AiPhotoPlan::active()
            ->orderBy('sort_order')
            ->orderBy('credits')
            ->get()
            ->map(function ($plan) {
                return [
                    'id'              => $plan->id,
                    'name'            => $plan->name,
                    'credits'         => $plan->credits,
                    'price'           => (float) $plan->price,
                    'currency'        => strtoupper($plan->currency),
                    'display_price'   => $this->formatMoney($plan->price, $plan->currency),
                    'price_per_photo' => round(((float) $plan->price) / max(1, $plan->credits), 3),
                ];
            });

        return response()->json([
            'status'  => true,
            'message' => 'AI photo plans fetched successfully',
            'data'    => [
                'balance' => $this->credits->getBalance($user->id),
                'plans'   => $plans,
            ],
        ]);
    }

    /**
     * GET /api/vendor/ai-photo/credits
     * Just the balance — call after every generation to refresh the UI.
     */
    public function credits(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $wallet = $this->credits->getOrCreateWallet($user->id);

        return response()->json([
            'status'  => true,
            'message' => 'Credits fetched successfully',
            'data'    => [
                'balance'         => $wallet->balance,
                'total_purchased' => $wallet->total_purchased,
                'total_used'      => $wallet->total_used,
            ],
        ]);
    }

    /**
     * POST /api/vendor/ai-photo/purchase
     * body: { plan_id }
     *
     * Creates a PENDING order + a Stripe PaymentIntent, returns client_secret.
     * The app then opens the Stripe Payment Sheet with that client_secret.
     *
     * IMPORTANT: credits are NOT granted here. They are granted only when
     * Stripe confirms payment (webhook, or the verify endpoint below).
     */
    public function purchase(Request $request, StripeService $stripe)
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer|exists:ai_photo_plans,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $plan = AiPhotoPlan::find($request->plan_id);

        if (! $plan || ! $plan->is_active) {
            return response()->json([
                'status'  => false,
                'message' => 'This plan is no longer available',
            ], 422);
        }

        // Reuse an existing unpaid order for the same plan instead of
        // spawning a new PaymentIntent every time the user taps Buy.
        $existing = AiPhotoOrder::where('vendor_id', $user->id)
            ->where('plan_id', $plan->id)
            ->where('status', AiPhotoOrder::STATUS_PENDING)
            ->whereNotNull('stripe_payment_intent_id')
            ->where('created_at', '>=', now()->subMinutes(30))
            ->latest()
            ->first();

        if ($existing) {
            try {
                $intent = $stripe->retrievePaymentIntent($existing->stripe_payment_intent_id);

                if (in_array($intent->status, ['requires_payment_method', 'requires_confirmation', 'requires_action'], true)) {
                    return response()->json([
                        'status'  => true,
                        'message' => 'Payment intent ready',
                        'data'    => $this->intentPayload($existing, $intent, $plan),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('AI photo: could not reuse intent', ['error' => $e->getMessage()]);
            }
        }

        // Snapshot the plan — later plan edits must not change this order.
        $order = AiPhotoOrder::create([
            'vendor_id' => $user->id,
            'plan_id'   => $plan->id,
            'plan_name' => $plan->name,
            'credits'   => $plan->credits,
            'amount'    => $plan->price,
            'currency'  => $plan->currency,
            'status'    => AiPhotoOrder::STATUS_PENDING,
        ]);

        try {
            $intent = $stripe->createPaymentIntent(
                $plan->amount_in_cents,
                $plan->currency,
                [
                    'type'      => 'ai_photo_credits',
                    'order_id'  => (string) $order->id,
                    'vendor_id' => (string) $user->id,
                    'plan_id'   => (string) $plan->id,
                    'credits'   => (string) $plan->credits,
                ],
                null,
                // idempotency key: same order never creates two intents
                'ai_photo_order_' . $order->id
            );
        } catch (\Stripe\Exception\CardException $e) {
            $order->update([
                'status'         => AiPhotoOrder::STATUS_FAILED,
                'failure_reason' => $e->getMessage(),
            ]);

            return response()->json(['status' => false, 'message' => $e->getMessage()], 402);
        } catch (\Throwable $e) {
            Log::error('AI photo: stripe intent failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);

            $order->update([
                'status'         => AiPhotoOrder::STATUS_FAILED,
                'failure_reason' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Could not start the payment. Please try again.',
            ], 500);
        }

        $order->update(['stripe_payment_intent_id' => $intent->id]);

        return response()->json([
            'status'  => true,
            'message' => 'Payment intent created successfully',
            'data'    => $this->intentPayload($order, $intent, $plan),
        ]);
    }

    /**
     * POST /api/vendor/ai-photo/verify-payment
     * body: { order_id }
     *
     * Called by the app right after the Payment Sheet reports success,
     * so the seller sees credits instantly instead of waiting for the webhook.
     *
     * This does NOT trust the app. It asks Stripe what really happened.
     * Safe to call many times — grantCreditsForOrder is idempotent.
     */
    public function verifyPayment(Request $request, StripeService $stripe)
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $order = AiPhotoOrder::where('id', $request->order_id)
            ->where('vendor_id', $user->id)   // vendor can only verify their OWN order
            ->first();

        if (! $order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        if ($order->isPaid() && $order->credits_granted) {
            return response()->json([
                'status'  => true,
                'message' => 'Payment already confirmed',
                'data'    => [
                    'order_id'         => $order->id,
                    'payment_status'   => 'paid',
                    'credits_added'    => $order->credits,
                    'balance'          => $this->credits->getBalance($user->id),
                ],
            ]);
        }

        if (! $order->stripe_payment_intent_id) {
            return response()->json(['status' => false, 'message' => 'No payment found for this order'], 422);
        }

        try {
            $intent = $stripe->retrievePaymentIntent($order->stripe_payment_intent_id);
        } catch (\Throwable $e) {
            Log::error('AI photo: verify failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);

            return response()->json(['status' => false, 'message' => 'Could not verify payment. Try again.'], 500);
        }

        // Guard against a tampered/mismatched intent
        if ((int) $intent->amount !== (int) round(((float) $order->amount) * 100)) {
            Log::critical('AI photo: amount mismatch', [
                'order_id'      => $order->id,
                'order_amount'  => $order->amount,
                'intent_amount' => $intent->amount,
            ]);

            return response()->json(['status' => false, 'message' => 'Payment amount mismatch'], 422);
        }

        if ($intent->status === 'succeeded') {
            $order->update([
                'status'           => AiPhotoOrder::STATUS_PAID,
                'paid_at'          => now(),
                'stripe_charge_id' => $intent->latest_charge ?? null,
            ]);

            $this->credits->grantCreditsForOrder($order->fresh());

            return response()->json([
                'status'  => true,
                'message' => "Payment successful. {$order->credits} credits added.",
                'data'    => [
                    'order_id'       => $order->id,
                    'payment_status' => 'paid',
                    'credits_added'  => $order->credits,
                    'balance'        => $this->credits->getBalance($user->id),
                ],
            ]);
        }

        if (in_array($intent->status, ['processing', 'requires_action', 'requires_confirmation'], true)) {
            return response()->json([
                'status'  => true,
                'message' => 'Payment is still processing. Credits will be added shortly.',
                'data'    => [
                    'order_id'       => $order->id,
                    'payment_status' => 'processing',
                    'balance'        => $this->credits->getBalance($user->id),
                ],
            ]);
        }

        // requires_payment_method / canceled => failed
        $order->update([
            'status'         => AiPhotoOrder::STATUS_FAILED,
            'failure_reason' => $intent->last_payment_error->message ?? "Intent status: {$intent->status}",
        ]);

        return response()->json([
            'status'  => false,
            'message' => $intent->last_payment_error->message ?? 'Payment was not completed',
            'data'    => [
                'order_id'       => $order->id,
                'payment_status' => 'failed',
                'balance'        => $this->credits->getBalance($user->id),
            ],
        ], 402);
    }

    /**
     * GET /api/vendor/ai-photo/orders
     * Purchase history.
     */
    public function orders(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $orders = AiPhotoOrder::where('vendor_id', $user->id)
            ->latest()
            ->paginate(20);

        $data = $orders->getCollection()->map(function ($o) {
            return [
                'id'            => $o->id,
                'plan_name'     => $o->plan_name,
                'credits'       => $o->credits,
                'amount'        => (float) $o->amount,
                'currency'      => strtoupper($o->currency),
                'display_price' => $this->formatMoney($o->amount, $o->currency),
                'status'        => $o->status,
                'paid_at'       => optional($o->paid_at)->toDateTimeString(),
                'created_at'    => $o->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Orders fetched successfully',
            'data'    => $data,
            'meta'    => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'total'        => $orders->total(),
            ],
        ]);
    }

    /**
     * GET /api/vendor/ai-photo/transactions
     * Full credit history (bought + used).
     */
    public function transactions(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $tx = AiPhotoCreditTransaction::where('vendor_id', $user->id)
            ->latest()
            ->paginate(30);

        $data = $tx->getCollection()->map(function ($t) {
            return [
                'id'            => $t->id,
                'type'          => $t->type,
                'amount'        => $t->amount,
                'balance_after' => $t->balance_after,
                'description'   => $t->description,
                'created_at'    => $t->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Transactions fetched successfully',
            'data'    => $data,
            'meta'    => [
                'current_page' => $tx->currentPage(),
                'last_page'    => $tx->lastPage(),
                'total'        => $tx->total(),
            ],
        ]);
    }

    // ---------- helpers ----------

    protected function intentPayload(AiPhotoOrder $order, $intent, AiPhotoPlan $plan): array
    {
        return [
            'order_id'          => $order->id,
            'plan_id'           => $plan->id,
            'plan_name'         => $plan->name,
            'credits'           => $plan->credits,
            'amount'            => (float) $plan->price,
            'currency'          => strtoupper($plan->currency),
            'client_secret'     => $intent->client_secret,
            'payment_intent_id' => $intent->id,
            'publishable_key'   => config('services.stripe.key'),
        ];
    }

    protected function formatMoney($amount, string $currency): string
    {
        $symbols = ['usd' => '$', 'eur' => '€', 'gbp' => '£'];
        $symbol  = $symbols[strtolower($currency)] ?? (strtoupper($currency) . ' ');

        return $symbol . number_format((float) $amount, 2);
    }
}
