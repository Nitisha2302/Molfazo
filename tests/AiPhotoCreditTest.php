<?php

namespace Tests\Feature;

use App\Models\AiPhotoCredit;
use App\Models\AiPhotoCreditTransaction;
use App\Models\AiPhotoOrder;
use App\Models\AiPhotoPlan;
use App\Models\User;
use App\Services\AiPhotoCreditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Payment / credit safety tests.
 *
 * Run with:
 *   php artisan test --filter=AiPhotoCreditTest
 *
 * These DO NOT call Stripe. They test the part that actually protects
 * your money: the credit ledger, idempotency and race conditions.
 * Stripe itself is tested manually with the test cards + `stripe trigger`.
 */
class AiPhotoCreditTest extends TestCase
{
    use RefreshDatabase;

    protected AiPhotoCreditService $service;
    protected User $vendor;
    protected AiPhotoPlan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(AiPhotoCreditService::class);

        $this->vendor = User::factory()->create([
            'role'   => 2,       // vendor role in this project
            'name'   => 'Test Seller',
            'email'  => 'seller@test.com',
        ]);

        $this->plan = AiPhotoPlan::create([
            'name'      => '10 Photos',
            'credits'   => 10,
            'price'     => 2.00,
            'currency'  => 'usd',
            'is_active' => true,
        ]);
    }

    protected function makeOrder(string $status = AiPhotoOrder::STATUS_PAID): AiPhotoOrder
    {
        return AiPhotoOrder::create([
            'vendor_id'                => $this->vendor->id,
            'plan_id'                  => $this->plan->id,
            'plan_name'                => $this->plan->name,
            'credits'                  => $this->plan->credits,
            'amount'                   => $this->plan->price,
            'currency'                 => 'usd',
            'status'                   => $status,
            'stripe_payment_intent_id' => 'pi_test_' . uniqid(),
        ]);
    }

    // ---------------------------------------------------------------
    // GRANTING
    // ---------------------------------------------------------------

    /** @test */
    public function paid_order_grants_credits_once()
    {
        $order = $this->makeOrder();

        $granted = $this->service->grantCreditsForOrder($order);

        $this->assertTrue($granted);
        $this->assertEquals(10, $this->service->getBalance($this->vendor->id));
        $this->assertTrue($order->fresh()->credits_granted);
    }

    /** @test */
    public function granting_twice_does_not_double_credit()
    {
        // This is the single most important test in the module.
        // Simulates: verify-payment endpoint AND the Stripe webhook
        // both confirming the same order.
        $order = $this->makeOrder();

        $first  = $this->service->grantCreditsForOrder($order);
        $second = $this->service->grantCreditsForOrder($order->fresh());
        $third  = $this->service->grantCreditsForOrder($order->fresh());

        $this->assertTrue($first);
        $this->assertFalse($second);
        $this->assertFalse($third);

        $this->assertEquals(10, $this->service->getBalance($this->vendor->id));
        $this->assertEquals(1, AiPhotoCreditTransaction::where('vendor_id', $this->vendor->id)
            ->where('type', AiPhotoCreditTransaction::TYPE_PURCHASE)->count());
    }

    /** @test */
    public function unpaid_order_grants_nothing()
    {
        foreach (['pending', 'failed', 'canceled'] as $status) {
            $order = $this->makeOrder($status);
            $this->assertFalse($this->service->grantCreditsForOrder($order));
        }

        $this->assertEquals(0, $this->service->getBalance($this->vendor->id));
    }

    /** @test */
    public function multiple_purchases_accumulate()
    {
        $this->service->grantCreditsForOrder($this->makeOrder());
        $this->service->grantCreditsForOrder($this->makeOrder());

        $this->assertEquals(20, $this->service->getBalance($this->vendor->id));

        $wallet = AiPhotoCredit::where('vendor_id', $this->vendor->id)->first();
        $this->assertEquals(20, $wallet->total_purchased);
    }

    // ---------------------------------------------------------------
    // SPENDING
    // ---------------------------------------------------------------

    /** @test */
    public function deducting_reduces_balance_and_logs_it()
    {
        $this->service->grantCreditsForOrder($this->makeOrder());

        $this->service->deduct($this->vendor->id, 1);

        $this->assertEquals(9, $this->service->getBalance($this->vendor->id));

        $tx = AiPhotoCreditTransaction::where('vendor_id', $this->vendor->id)
            ->where('type', AiPhotoCreditTransaction::TYPE_USAGE)->first();

        $this->assertEquals(-1, $tx->amount);
        $this->assertEquals(9, $tx->balance_after);
    }

    /** @test */
    public function cannot_spend_more_than_balance()
    {
        $this->service->grantCreditsForOrder($this->makeOrder()); // 10 credits

        // burn all 10
        for ($i = 0; $i < 10; $i++) {
            $this->service->deduct($this->vendor->id, 1);
        }

        $this->assertEquals(0, $this->service->getBalance($this->vendor->id));

        // the 11th must fail
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('INSUFFICIENT_CREDITS');

        $this->service->deduct($this->vendor->id, 1);
    }

    /** @test */
    public function vendor_with_no_wallet_cannot_spend()
    {
        $this->expectException(\RuntimeException::class);
        $this->service->deduct($this->vendor->id, 1);
    }

    /** @test */
    public function balance_never_goes_negative()
    {
        $this->service->grantCreditsForOrder($this->makeOrder());

        try {
            $this->service->deduct($this->vendor->id, 50); // way more than 10
        } catch (\RuntimeException $e) {
            // expected
        }

        $this->assertEquals(10, $this->service->getBalance($this->vendor->id));
        $this->assertGreaterThanOrEqual(0, $this->service->getBalance($this->vendor->id));
    }

    // ---------------------------------------------------------------
    // REFUND / ROLLBACK
    // ---------------------------------------------------------------

    /** @test */
    public function failed_generation_returns_the_credit()
    {
        $this->service->grantCreditsForOrder($this->makeOrder());
        $this->service->deduct($this->vendor->id, 1);

        $this->assertEquals(9, $this->service->getBalance($this->vendor->id));

        // Stability AI threw an error → give it back
        $this->service->refund($this->vendor->id, 1);

        $this->assertEquals(10, $this->service->getBalance($this->vendor->id));

        $wallet = AiPhotoCredit::where('vendor_id', $this->vendor->id)->first();
        $this->assertEquals(0, $wallet->total_used, 'total_used should roll back too');
    }

    /** @test */
    public function stripe_refund_revokes_credits()
    {
        $order = $this->makeOrder();
        $this->service->grantCreditsForOrder($order);

        $this->service->revokeForOrder($order->fresh(), 'Stripe refund issued');

        $this->assertEquals(0, $this->service->getBalance($this->vendor->id));
        $this->assertEquals(AiPhotoOrder::STATUS_REFUNDED, $order->fresh()->status);
        $this->assertFalse($order->fresh()->credits_granted);
    }

    /** @test */
    public function refund_after_credits_already_spent_floors_at_zero()
    {
        // Seller buys 10, uses 8, then charges back.
        // We can only claw back the 2 they still have.
        $order = $this->makeOrder();
        $this->service->grantCreditsForOrder($order);

        for ($i = 0; $i < 8; $i++) {
            $this->service->deduct($this->vendor->id, 1);
        }

        $this->assertEquals(2, $this->service->getBalance($this->vendor->id));

        $this->service->revokeForOrder($order->fresh(), 'Chargeback opened');

        $this->assertEquals(0, $this->service->getBalance($this->vendor->id));
    }

    /** @test */
    public function revoking_twice_is_safe()
    {
        $order = $this->makeOrder();
        $this->service->grantCreditsForOrder($order);

        $this->service->revokeForOrder($order->fresh());
        $this->service->revokeForOrder($order->fresh());

        $this->assertEquals(0, $this->service->getBalance($this->vendor->id));
    }

    // ---------------------------------------------------------------
    // LEDGER INTEGRITY
    // ---------------------------------------------------------------

    /** @test */
    public function ledger_sum_always_equals_stored_balance()
    {
        $this->service->grantCreditsForOrder($this->makeOrder());
        $this->service->deduct($this->vendor->id, 3);
        $this->service->refund($this->vendor->id, 1);
        $this->service->grantCreditsForOrder($this->makeOrder());
        $this->service->deduct($this->vendor->id, 5);

        $check = $this->service->verifyIntegrity($this->vendor->id);

        $this->assertTrue($check['ok'], 'Ledger and balance drifted apart!');
        $this->assertEquals($check['stored_balance'], $check['ledger_sum']);
    }

    /** @test */
    public function editing_a_plan_does_not_change_existing_orders()
    {
        $order = $this->makeOrder();
        $this->service->grantCreditsForOrder($order);

        // admin changes the plan afterwards
        $this->plan->update(['credits' => 999, 'price' => 99.00, 'name' => 'Changed']);

        $order->refresh();

        $this->assertEquals(10, $order->credits);
        $this->assertEquals('10 Photos', $order->plan_name);
        $this->assertEquals('2.00', (string) $order->amount);
        $this->assertEquals(10, $this->service->getBalance($this->vendor->id));
    }

    // ---------------------------------------------------------------
    // API ENDPOINTS
    // ---------------------------------------------------------------

    /** @test */
    public function endpoints_reject_unauthenticated_requests()
    {
        $this->getJson('/api/vendor/ai-photo/plans')->assertStatus(401);
        $this->getJson('/api/vendor/ai-photo/credits')->assertStatus(401);
        $this->postJson('/api/vendor/ai-photo/purchase', ['plan_id' => 1])->assertStatus(401);
        $this->postJson('/api/vendor/ai-photo/verify-payment', ['order_id' => 1])->assertStatus(401);
    }

    /** @test */
    public function vendor_cannot_verify_another_vendors_order()
    {
        $other = User::factory()->create(['role' => 2]);

        $order = AiPhotoOrder::create([
            'vendor_id' => $other->id,
            'plan_id'   => $this->plan->id,
            'plan_name' => $this->plan->name,
            'credits'   => 10,
            'amount'    => 2.00,
            'currency'  => 'usd',
            'status'    => AiPhotoOrder::STATUS_PENDING,
            'stripe_payment_intent_id' => 'pi_someone_else',
        ]);

        $this->actingAs($this->vendor, 'api')
            ->postJson('/api/vendor/ai-photo/verify-payment', ['order_id' => $order->id])
            ->assertStatus(404);
    }

    /** @test */
    public function purchase_rejects_unknown_or_inactive_plan()
    {
        $this->actingAs($this->vendor, 'api')
            ->postJson('/api/vendor/ai-photo/purchase', ['plan_id' => 99999])
            ->assertStatus(422);

        $this->plan->update(['is_active' => false]);

        $this->actingAs($this->vendor, 'api')
            ->postJson('/api/vendor/ai-photo/purchase', ['plan_id' => $this->plan->id])
            ->assertStatus(422);
    }

    /** @test */
    public function credits_endpoint_returns_correct_balance()
    {
        $this->service->grantCreditsForOrder($this->makeOrder());
        $this->service->deduct($this->vendor->id, 4);

        $this->actingAs($this->vendor, 'api')
            ->getJson('/api/vendor/ai-photo/credits')
            ->assertStatus(200)
            ->assertJsonPath('data.balance', 6)
            ->assertJsonPath('data.total_purchased', 10)
            ->assertJsonPath('data.total_used', 4);
    }

    // ---------------------------------------------------------------
    // WEBHOOK
    // ---------------------------------------------------------------

    /** @test */
    public function webhook_rejects_request_without_signature()
    {
        $this->postJson('/api/stripe/webhook', ['type' => 'payment_intent.succeeded'])
            ->assertStatus(400);
    }

    /** @test */
    public function webhook_rejects_forged_signature()
    {
        $this->call(
            'POST',
            '/api/stripe/webhook',
            [],
            [],
            [],
            ['HTTP_STRIPE_SIGNATURE' => 't=123,v1=totally_fake_signature'],
            json_encode(['type' => 'payment_intent.succeeded'])
        )->assertStatus(400);
    }
}
