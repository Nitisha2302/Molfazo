<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\AiPhotoCreditTransaction;
use App\Models\AiPhotoOrder;
use App\Models\AiPhotoPlan;
use App\Services\AiPhotoCreditService;
use App\Services\StripeService;   // STRIPE ON HOLD — re-enable later
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * =====================================================================
 *  MODE: DIRECT PURCHASE (no Stripe)
 * =====================================================================
 *
 *  The frontend collects the payment itself and then tells this API
 *  "the seller bought plan X". Credits are granted immediately.
 *
 *  SECURITY WARNING — READ THIS
 *  In this mode the server CANNOT verify that money was actually paid.
 *  Anyone holding a valid seller token can call /purchase repeatedly and
 *  receive unlimited free credits. The backend cannot prevent this,
 *  because the backend is not part of the payment.
 *
 *  This is acceptable ONLY for development and internal testing.
 *  Before going live, switch to the Stripe flow (kept commented below)
 *  or to whichever gateway you choose.
 *
 *  Every order created in this mode is tagged in `meta` as
 *  'verified' => false so you can find and audit them later.
 * =====================================================================
 */
class AiPhotoController extends Controller
{
    protected AiPhotoCreditService $credits;

    public function __construct(AiPhotoCreditService $credits)
    {
        $this->credits = $credits;
    }

    /**
     * GET /api/vendor/ai-photo/plans
     * Same in both modes.
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
                $perPhoto = ((float) $plan->price) / max(1, $plan->credits);

                return [
                    'id'                      => $plan->id,
                    'name'                    => $plan->name,
                    'credits'                 => $plan->credits,
                    'price'                   => (float) $plan->price,
                    // 'display_price'           => $this->formatMoney($plan->price),
                    // 'price_per_photo'         => round($perPhoto, 2),
                    // 'display_price_per_photo' => $this->formatMoney($perPhoto),
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
     *
     * DIRECT MODE — one call does everything.
     * The app calls this AFTER it has taken the payment.
     *
     * body: {
     *   plan_id           : required
     *   payment_reference : optional — transaction id from whatever the app used
     *   payment_method    : optional — free text, e.g. "manual", "test"
     * }
     *
     * There is no verify-payment step in this mode.
     */
    public function purchase(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'plan_id'           => 'required|integer|exists:ai_photo_plans,id',
            'payment_reference' => 'nullable|string|max:191',
            'payment_method'    => 'nullable|string|max:50',
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

        $reference = $request->input('payment_reference');

        // If the app sends a reference, refuse to process the same one twice.
        // This is the only duplicate protection available in this mode.
        if ($reference) {
            $existing = AiPhotoOrder::where('vendor_id', $user->id)
                ->where('meta->payment_reference', $reference)
                ->first();

            if ($existing) {
                return response()->json([
                    'status'  => true,
                    'message' => 'This payment was already processed',
                    'data'    => [
                        'order_id'      => $existing->id,
                        'credits_added' => $existing->credits,
                        'balance'       => $this->credits->getBalance($user->id),
                        'duplicate'     => true,
                    ],
                ]);
            }
        }

        // Snapshot the plan — later edits must not change this order.
        $order = AiPhotoOrder::create([
            'vendor_id' => $user->id,
            'plan_id'   => $plan->id,
            'plan_name' => $plan->name,
            'credits'   => $plan->credits,
            'amount'    => $plan->price,
            'currency'  => $plan->currency,
            'status'    => AiPhotoOrder::STATUS_PAID,
            'paid_at'   => now(),
            'meta'      => [
                'mode'              => 'direct',
                'verified'          => false,   // server did not confirm the money
                'payment_method'    => $request->input('payment_method', 'frontend'),
                'payment_reference' => $reference,
                'ip'                => $request->ip(),
            ],
        ]);

        $this->credits->grantCreditsForOrder($order);

        Log::info('AI photo: direct purchase (UNVERIFIED)', [
            'order_id'  => $order->id,
            'vendor_id' => $user->id,
            'credits'   => $plan->credits,
            'amount'    => $plan->price,
            'reference' => $reference,
        ]);

        return response()->json([
            'status'  => true,
            'message' => "{$plan->credits} credits added successfully",
            'data'    => [
                'order_id'      => $order->id,
                'plan_name'     => $plan->name,
                'credits_added' => $plan->credits,
                'amount'        => (float) $plan->price,
                'display_price' => $this->formatMoney($plan->price),
                'balance'       => $this->credits->getBalance($user->id),
            ],
        ]);
    }

    /**
     * GET /api/vendor/ai-photo/orders
     */
    public function orders(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $orders = AiPhotoOrder::where('vendor_id', $user->id)->latest()->paginate(20);

        $data = $orders->getCollection()->map(function ($o) {
            return [
                'id'            => $o->id,
                'plan_name'     => $o->plan_name,
                'credits'       => $o->credits,
                'amount'        => (float) $o->amount,
                'display_price' => $this->formatMoney($o->amount),
                'status'        => $o->status,
                'paid_at'       => optional($o->paid_at)->toDateTimeString(),
                'created_at'    => $o->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $data,
            'meta'   => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'total'        => $orders->total(),
            ],
        ]);
    }

    /**
     * GET /api/vendor/ai-photo/transactions
     */
    public function transactions(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $tx = AiPhotoCreditTransaction::where('vendor_id', $user->id)->latest()->paginate(30);

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
            'status' => true,
            'data'   => $data,
            'meta'   => [
                'current_page' => $tx->currentPage(),
                'last_page'    => $tx->lastPage(),
                'total'        => $tx->total(),
            ],
        ]);
    }

    protected function formatMoney($amount): string
    {
        return 'c. ' . number_format((float) $amount, 2);
    }


    /* =================================================================
     |  STRIPE FLOW — ON HOLD
     |=================================================================
     |  To switch back on:
     |    1. composer require stripe/stripe-php
     |    2. add stripe keys to .env + config/services.php
     |    3. uncomment `use App\Services\StripeService;` at the top
     |    4. uncomment stripePurchase() and verifyPayment() below
     |    5. in routes/api.php point /purchase at stripePurchase
     |       and re-enable /verify-payment + /stripe/webhook
     |    6. rename the direct purchase() above to legacyDirectPurchase()
     |       so it is no longer reachable
     |
     |  Nothing in the database has to change. The tables, the ledger and
     |  the credit service already work with both flows.
     |=================================================================

    public function stripePurchase(Request $request, StripeService $stripe)
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer|exists:ai_photo_plans,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $plan = AiPhotoPlan::find($request->plan_id);

        if (! $plan || ! $plan->is_active) {
            return response()->json(['status' => false, 'message' => 'This plan is no longer available'], 422);
        }

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
                'ai_photo_order_' . $order->id
            );
        } catch (\Throwable $e) {
            Log::error('AI photo: stripe intent failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);

            $order->update(['status' => AiPhotoOrder::STATUS_FAILED, 'failure_reason' => $e->getMessage()]);

            return response()->json(['status' => false, 'message' => 'Could not start the payment. Please try again.'], 500);
        }

        $order->update(['stripe_payment_intent_id' => $intent->id]);

        return response()->json([
            'status'  => true,
            'message' => 'Payment intent created successfully',
            'data'    => $this->intentPayload($order, $intent, $plan),
        ]);
    }

    public function verifyPayment(Request $request, StripeService $stripe)
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), ['order_id' => 'required|integer']);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $order = AiPhotoOrder::where('id', $request->order_id)
            ->where('vendor_id', $user->id)
            ->first();

        if (! $order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        if ($order->isPaid() && $order->credits_granted) {
            return response()->json([
                'status'  => true,
                'message' => 'Payment already confirmed',
                'data'    => [
                    'order_id'       => $order->id,
                    'payment_status' => 'paid',
                    'credits_added'  => $order->credits,
                    'balance'        => $this->credits->getBalance($user->id),
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

        if ((int) $intent->amount !== (int) round(((float) $order->amount) * 100)) {
            Log::critical('AI photo: amount mismatch', ['order_id' => $order->id]);

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

    protected function intentPayload(AiPhotoOrder $order, $intent, AiPhotoPlan $plan): array
    {
        return [
            'order_id'          => $order->id,
            'plan_id'           => $plan->id,
            'plan_name'         => $plan->name,
            'credits'           => $plan->credits,
            'amount'            => (float) $plan->price,
            'client_secret'     => $intent->client_secret,
            'payment_intent_id' => $intent->id,
            'publishable_key'   => config('services.stripe.key'),
        ];
    }

    ================================================================= */
}
