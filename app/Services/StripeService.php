<?php

namespace App\Services;

use Stripe\StripeClient;
use Stripe\Webhook;

/**
 * Thin wrapper around the Stripe PHP SDK so controllers stay clean
 * and so it can be mocked in tests.
 */
class StripeService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $secret = config('services.stripe.secret');

        if (empty($secret)) {
            throw new \RuntimeException('STRIPE_SECRET is not configured.');
        }

        $this->stripe = new StripeClient([
            'api_key'        => $secret,
            'stripe_version' => '2024-06-20',
        ]);
    }

    public function client(): StripeClient
    {
        return $this->stripe;
    }

    /**
     * PaymentIntent — used by the MOBILE APP (Stripe Payment Sheet).
     * The app gets client_secret and confirms the payment natively.
     *
     * $idempotencyKey stops a double-tap on "Pay" from creating two charges.
     */
    public function createPaymentIntent(
        int $amountInCents,
        string $currency,
        array $metadata = [],
        ?string $customerId = null,
        ?string $idempotencyKey = null
    ) {
        $params = [
            'amount'                    => $amountInCents,
            'currency'                  => strtolower($currency),
            'metadata'                  => $metadata,
            'automatic_payment_methods' => ['enabled' => true],
        ];

        if ($customerId) {
            $params['customer'] = $customerId;
        }

        $opts = [];
        if ($idempotencyKey) {
            $opts['idempotency_key'] = $idempotencyKey;
        }

        return $this->stripe->paymentIntents->create($params, $opts);
    }

    public function retrievePaymentIntent(string $id)
    {
        return $this->stripe->paymentIntents->retrieve($id, []);
    }

    /**
     * Ephemeral key — required by the Stripe mobile Payment Sheet
     * so it can show/save the customer's cards.
     */
    public function createEphemeralKey(string $customerId, string $apiVersion = '2024-06-20')
    {
        return $this->stripe->ephemeralKeys->create(
            ['customer' => $customerId],
            ['stripe_version' => $apiVersion]
        );
    }

    public function createCustomer(array $data)
    {
        return $this->stripe->customers->create($data);
    }

    /**
     * Checkout Session — used for a WEB fallback / testing in a browser.
     */
    public function createCheckoutSession(array $params, ?string $idempotencyKey = null)
    {
        $opts = [];
        if ($idempotencyKey) {
            $opts['idempotency_key'] = $idempotencyKey;
        }

        return $this->stripe->checkout->sessions->create($params, $opts);
    }

    public function retrieveCheckoutSession(string $id)
    {
        return $this->stripe->checkout->sessions->retrieve($id, []);
    }

    /**
     * Verify the webhook came from Stripe and was not tampered with.
     * Throws SignatureVerificationException on a bad signature.
     */
    public function constructWebhookEvent(string $payload, string $signatureHeader)
    {
        return Webhook::constructEvent(
            $payload,
            $signatureHeader,
            config('services.stripe.webhook_secret')
        );
    }
}
