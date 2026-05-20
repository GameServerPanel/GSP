<?php
require_once __DIR__ . '/../classes/PaymentGatewayInterface.php';

/**
 * Manual / offline payment gateway.
 * Used when an admin marks a payment as paid directly.
 */
class ManualGateway implements PaymentGatewayInterface
{
    public function getName(): string { return 'manual'; }

    public function createPayment(array $params): array
    {
        return ['success' => true, 'provider_order_id' => 'MANUAL-' . uniqid(), 'raw_response' => []];
    }

    public function handleCallback(array $params): array
    {
        $txid = $params['admin_txid'] ?? ('MANUAL-' . uniqid());
        return [
            'success'        => true,
            'transaction_id' => $txid,
            'amount'         => (float)($params['amount'] ?? 0),
            'currency'       => $params['currency'] ?? 'USD',
            'status'         => 'completed',
            'raw_response'   => $params,
        ];
    }

    public function verifyPayment(array $payload): bool { return true; }

    public function getTransactionId(array $captureResult): ?string
    {
        return $captureResult['transaction_id'] ?? null;
    }
}
