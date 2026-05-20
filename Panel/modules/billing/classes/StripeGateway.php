<?php
require_once __DIR__ . '/../classes/PaymentGatewayInterface.php';

/**
 * Stripe payment gateway stub.
 * Implement this class when Stripe support is needed.
 */
class StripeGateway implements PaymentGatewayInterface
{
    public function getName(): string { return 'stripe'; }

    public function createPayment(array $params): array
    {
        return ['success' => false, 'error' => 'stripe_not_implemented'];
    }

    public function handleCallback(array $params): array
    {
        return ['success' => false, 'error' => 'stripe_not_implemented'];
    }

    public function verifyPayment(array $payload): bool { return false; }

    public function getTransactionId(array $captureResult): ?string { return null; }
}
