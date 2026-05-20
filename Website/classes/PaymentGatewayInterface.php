<?php
/**
 * Payment Gateway Interface
 * All payment providers must implement this contract.
 */
interface PaymentGatewayInterface
{
    /**
     * Create a payment/order on the provider side.
     * @param array $params { amount, currency, invoice_id, description, return_url, cancel_url, items? }
     * @return array { success: bool, provider_order_id: string, redirect_url?: string, error?: string }
     */
    public function createPayment(array $params): array;

    /**
     * Handle a provider callback/capture (webhook or return).
     * @param array $params Provider-specific parameters (e.g. { order_id } for PayPal)
     * @return array { success: bool, transaction_id: string, amount: float, status: string, raw_response: array, error?: string }
     */
    public function handleCallback(array $params): array;

    /**
     * Verify that a payment/webhook is authentic.
     * @param array $payload  Raw request body / headers
     * @return bool
     */
    public function verifyPayment(array $payload): bool;

    /**
     * Get the provider's external transaction ID from a capture result.
     * @param array $captureResult Result from handleCallback()
     * @return string|null
     */
    public function getTransactionId(array $captureResult): ?string;

    /**
     * Return a short machine name for this gateway (e.g. 'paypal', 'stripe', 'manual').
     */
    public function getName(): string;
}
