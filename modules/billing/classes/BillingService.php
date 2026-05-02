<?php
require_once __DIR__ . '/../classes/BillingRepository.php';
require_once __DIR__ . '/../classes/PaymentGatewayInterface.php';

/**
 * BillingService — core business logic for the billing module.
 *
 * Responsibilities:
 *  - Calculate pricing
 *  - Create invoices
 *  - Process payment results (log transaction, mark invoice paid, update server home)
 *  - Extend / reset server billing expiration
 */
class BillingService
{
    private BillingRepository $repo;

    public function __construct(BillingRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Calculate pricing for a new order.
     *
     * @param array  $service     Row from gsp_billing_services
     * @param string $rateType    'daily' | 'monthly' | 'yearly'
     * @param int    $players     Number of player slots
     * @param int    $qty         Duration quantity (e.g. 2 = 2 months)
     * @return array { rate_per_player, subtotal, total_due, period_days }
     */
    public function calculatePrice(array $service, string $rateType, int $players, int $qty = 1): array
    {
        $qty    = max(1, $qty);
        $players = max(1, $players);

        switch ($rateType) {
            case 'daily':
                $basePrice  = (float)($service['price_daily'] ?? 0);
                $periodDays = $qty;
                break;
            case 'yearly':
                $basePrice  = (float)($service['price_year'] ?? 0);
                $periodDays = $qty * 365;
                break;
            case 'monthly':
            default:
                $rateType   = 'monthly';
                $basePrice  = (float)($service['price_monthly'] ?? 0);
                $periodDays = $qty * 31;
                break;
        }

        // price_monthly etc is the per-player per-period rate
        $ratePerPlayer = $basePrice;
        $subtotal      = round($ratePerPlayer * $players * $qty, 2);
        $totalDue      = $subtotal;

        return [
            'rate_type'       => $rateType,
            'rate_per_player' => $ratePerPlayer,
            'players'         => $players,
            'qty'             => $qty,
            'subtotal'        => $subtotal,
            'total_due'       => $totalDue,
            'period_days'     => $periodDays,
        ];
    }

    /**
     * Create a billing invoice row.
     *
     * @param array $pricing  Result from calculatePrice()
     * @param array $context  { user_id, service_id, home_id, home_name, customer_name, customer_email, description }
     * @return int  New invoice_id (0 on failure)
     */
    public function createInvoice(array $pricing, array $context): int
    {
        $now         = date('Y-m-d H:i:s');
        $periodStart = $now;
        $periodEnd   = date('Y-m-d H:i:s', strtotime('+' . $pricing['period_days'] . ' days'));

        return $this->repo->createInvoice([
            'user_id'         => intval($context['user_id'] ?? 0),
            'service_id'      => intval($context['service_id'] ?? 0),
            'home_id'         => intval($context['home_id'] ?? 0),
            'home_name'       => $context['home_name'] ?? '',
            'customer_name'   => $context['customer_name'] ?? '',
            'customer_email'  => $context['customer_email'] ?? '',
            'rate_type'       => $pricing['rate_type'],
            'rate_per_player' => $pricing['rate_per_player'],
            'players'         => $pricing['players'],
            'period_start'    => $periodStart,
            'period_end'      => $periodEnd,
            'subtotal'        => $pricing['subtotal'],
            'total_due'       => $pricing['total_due'],
            'currency'        => $context['currency'] ?? 'USD',
            'payment_status'  => 'unpaid',
            'payment_method'  => '',
            'description'     => $context['description'] ?? '',
        ]);
    }

    /**
     * Process a successful payment result from a gateway.
     *
     * 1. Log the transaction
     * 2. Mark invoice paid
     * 3. Update server home billing state (extend or reset expiration)
     *
     * @param array $captureResult  Result from PaymentGatewayInterface::handleCallback()
     * @param int   $invoiceId
     * @param int   $userId
     * @param int   $homeId
     * @param array $invoiceRow     The invoice row (from DB) — needed for period/pricing
     * @return array { success: bool, transaction_id: string, error?: string }
     */
    public function processPaymentSuccess(
        array $captureResult,
        int   $invoiceId,
        int   $userId,
        int   $homeId,
        array $invoiceRow
    ): array {
        $txid     = $captureResult['transaction_id'] ?? null;
        $method   = $captureResult['payment_method'] ?? 'paypal';
        $amount   = (float)($captureResult['amount'] ?? $invoiceRow['total_due'] ?? 0);
        $currency = $captureResult['currency'] ?? $invoiceRow['currency'] ?? 'USD';
        $now      = date('Y-m-d H:i:s');

        // 1. Log transaction
        $this->repo->logTransaction([
            'invoice_id'              => $invoiceId,
            'user_id'                 => $userId,
            'home_id'                 => $homeId,
            'payment_method'          => $method,
            'transaction_external_id' => $txid ?? '',
            'amount'                  => $amount,
            'currency'                => $currency,
            'status'                  => 'completed',
            'raw_response'            => $captureResult['raw_response'] ?? [],
        ]);

        // 2. Mark invoice paid
        if ($invoiceId > 0) {
            $this->repo->markInvoicePaid($invoiceId, $txid ?? '', $method, $now);
        }

        // 3. Update server home billing state
        if ($homeId > 0) {
            $this->extendServerBilling($homeId, $invoiceRow, $now);
        }

        return ['success' => true, 'transaction_id' => $txid];
    }

    /**
     * Extend or reset a server's billing expiration based on the invoice period.
     */
    public function extendServerBilling(int $homeId, array $invoiceRow, string $now): void
    {
        $home      = $this->repo->getServerHomeBilling($homeId);
        $periodEnd = $invoiceRow['period_end'] ?? null;

        if (!$periodEnd) {
            $rateType  = $invoiceRow['rate_type'] ?? 'monthly';
            $periodMap = ['daily' => '+1 day', 'monthly' => '+31 days', 'yearly' => '+365 days'];
            $periodEnd = date('Y-m-d H:i:s', strtotime($periodMap[$rateType] ?? '+31 days'));
        }

        // If current expiry is in the future, extend from it; otherwise reset from period_end
        $currentExpiry = $home['billing_expires_at'] ?? null;
        if ($currentExpiry && strtotime($currentExpiry) > time()) {
            // Calculate the period length from the invoice; fall back to rate_type if dates are missing
            $periodStart = $invoiceRow['period_start'] ?? null;
            $periodEndVal = $invoiceRow['period_end'] ?? null;
            if ($periodStart && $periodEndVal) {
                $currentPeriodSecs = strtotime($periodEndVal) - strtotime($periodStart);
            } else {
                $rateType2   = $invoiceRow['rate_type'] ?? 'monthly';
                $periodSecMap = ['daily' => 86400, 'monthly' => 31 * 86400, 'yearly' => 365 * 86400];
                $currentPeriodSecs = $periodSecMap[$rateType2] ?? (31 * 86400);
            }
            $newExpiry = date('Y-m-d H:i:s', strtotime($currentExpiry) + max(86400, $currentPeriodSecs));
        } else {
            $newExpiry = $periodEnd;
        }

        $this->repo->updateServerHomeBilling($homeId, [
            'billing_status'          => 'active',
            'billing_expires_at'      => $newExpiry,
            'next_invoice_date'       => $newExpiry,
            'server_expiration_date'  => null,
            'billing_invoice_sent_at' => null,
        ]);
    }
}
