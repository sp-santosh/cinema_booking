<?php
/**
 * Wrapper for processing payments via Stripe API.
 */
class StripeGatewayService
{
    private string $secretKey;

    public function __construct()
    {
        $config = require APP_PATH . '/config/stripe.php';
        $this->secretKey = $config['secret_key'];
        
        // In a real app, you would initialize the Stripe SDK here:
        // \Stripe\Stripe::setApiKey($this->secretKey);
    }

    public function createCheckoutSession(array $booking, float $amount): string
    {
        // Mock Stripe Checkout URL generation.
        // In real use, creates a Stripe Session and redirects.
        return APP_URL . '/payments/mock-stripe-checkout?session=mock_' . bin2hex(random_bytes(16));
    }

    public function verifyWebhookSignature(string $payload, string $sigHeader): bool
    {
        // Mock signature verification
        return true;
    }
}
