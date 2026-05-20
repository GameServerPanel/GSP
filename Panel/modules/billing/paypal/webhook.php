<?php
/**
 * GSP PayPal Webhook Receiver
 *
 * Public URL: $SITE_BASE_URL + $paypal_webhook_path (e.g. https://gameservers.world/paypal/webhook.php)
 *
 * This endpoint:
 *   1. Reads raw POST JSON from PayPal
 *   2. Verifies the webhook signature using PayPal's verify-webhook-signature API
 *   3. Checks for duplicate events (idempotency) via billing_paypal_webhook_events table
 *   4. Processes supported event types and updates billing_orders / triggers provisioning
 *   5. Returns appropriate HTTP status codes
 *
 * HTTP status codes returned:
 *   200 — success (or duplicate event safely ignored)
 *   400 — missing / invalid JSON body
 *   401 — PayPal signature verification failed or OAuth failed
 *   500 — internal error (DB unavailable, etc.)
 */

ini_set('display_errors', '0');
error_reporting(E_ALL);

// ---------------------------------------------------------------------------
// Bootstrap: load config and DB
// ---------------------------------------------------------------------------
$_billing_dir = dirname(__DIR__);
require_once $_billing_dir . '/includes/config_loader.php';

// Log helper — writes to logs/paypal_webhook.log; never logs secrets.
$_wh_log_file = $_billing_dir . '/logs/paypal_webhook.log';
@mkdir(dirname($_wh_log_file), 0755, true);

function wh_log(string $level, string $message, array $context = []): void
{
    global $_wh_log_file;
    $ctx = empty($context) ? '' : ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $line = '[' . date('c') . '] [' . strtoupper($level) . '] ' . $message . $ctx . "\n";
    @file_put_contents($_wh_log_file, $line, FILE_APPEND | LOCK_EX);
}

// ---------------------------------------------------------------------------
// 1. Read raw input
// ---------------------------------------------------------------------------
$raw = (string)file_get_contents('php://input');
$headers = array_change_key_case((array)(getallheaders() ?: []), CASE_UPPER);

wh_log('info', 'webhook_received', ['ip' => $_SERVER['REMOTE_ADDR'] ?? '', 'bytes' => strlen($raw)]);

if ($raw === '') {
    wh_log('warn', 'empty_body');
    http_response_code(400);
    echo json_encode(['error' => 'empty_body']);
    exit;
}

$evt = json_decode($raw, true);
if (json_last_error() !== JSON_ERROR_NONE || !is_array($evt)) {
    wh_log('warn', 'invalid_json', ['json_error' => json_last_error_msg()]);
    http_response_code(400);
    echo json_encode(['error' => 'invalid_json']);
    exit;
}

// ---------------------------------------------------------------------------
// 2. DB connection (needed for idempotency log and order updates)
// ---------------------------------------------------------------------------
$db_port_int = intval($db_port ?? 3306) ?: 3306;
$wh_db = @mysqli_connect($db_host ?? 'localhost', $db_user ?? '', $db_pass ?? '', $db_name ?? '', $db_port_int);
if (!$wh_db) {
    wh_log('error', 'db_connect_failed', ['error' => mysqli_connect_error()]);
    http_response_code(500);
    echo json_encode(['error' => 'db_unavailable']);
    exit;
}
mysqli_set_charset($wh_db, 'utf8mb4');

$pfx = $table_prefix ?? 'gsp_';

// ---------------------------------------------------------------------------
// 2a. Ensure the webhook event log table exists (idempotent DDL)
// ---------------------------------------------------------------------------
wh_ensure_event_table($wh_db, $pfx);

// ---------------------------------------------------------------------------
// 3. PayPal OAuth token
// ---------------------------------------------------------------------------
$api_base     = gsp_paypal_get_api_base();
$client_id    = gsp_paypal_get_client_id();
$client_secret = gsp_paypal_get_client_secret();
$webhook_id   = gsp_paypal_get_webhook_id();

if (empty($client_id) || empty($client_secret)) {
    wh_log('error', 'paypal_not_configured');
    http_response_code(500);
    echo json_encode(['error' => 'paypal_not_configured']);
    mysqli_close($wh_db);
    exit;
}

$access_token = wh_get_access_token($api_base, $client_id, $client_secret);
if (!$access_token) {
    wh_log('warn', 'oauth_failed');
    http_response_code(401);
    echo json_encode(['error' => 'oauth_failed']);
    mysqli_close($wh_db);
    exit;
}

// ---------------------------------------------------------------------------
// 4. Verify webhook signature (skip only if webhook_id is empty)
// ---------------------------------------------------------------------------
if (!empty($webhook_id)) {
    $verified = wh_verify_signature($api_base, $access_token, $webhook_id, $headers, $evt);
    if (!$verified) {
        wh_log('warn', 'signature_invalid', [
            'transmission_id' => $headers['PAYPAL-TRANSMISSION-ID'] ?? '',
            'event_type'      => $evt['event_type'] ?? '',
        ]);
        http_response_code(401);
        echo json_encode(['error' => 'signature_invalid']);
        mysqli_close($wh_db);
        exit;
    }
    wh_log('info', 'signature_ok');
} else {
    wh_log('warn', 'signature_skipped_no_webhook_id');
}

// ---------------------------------------------------------------------------
// 5. Idempotency check
// ---------------------------------------------------------------------------
$paypal_event_id = $evt['id'] ?? '';
$event_type      = $evt['event_type'] ?? '';
$resource        = $evt['resource'] ?? [];

if ($paypal_event_id !== '') {
    $existing = wh_get_event($wh_db, $pfx, $paypal_event_id);
    if ($existing && $existing['processing_status'] === 'processed') {
        wh_log('info', 'duplicate_event_ignored', ['paypal_event_id' => $paypal_event_id, 'event_type' => $event_type]);
        http_response_code(200);
        echo json_encode(['status' => 'duplicate_ignored']);
        mysqli_close($wh_db);
        exit;
    }
}

// Log the event as received (upsert — so retries update the record)
$log_id = wh_log_event($wh_db, $pfx, [
    'paypal_event_id'   => $paypal_event_id,
    'event_type'        => $event_type,
    'resource_id'       => $resource['id'] ?? '',
    'order_id'          => '',
    'capture_id'        => '',
    'billing_order_id'  => 0,
    'processing_status' => 'received',
    'raw_json'          => $raw,
]);

// ---------------------------------------------------------------------------
// 6. Process event
// ---------------------------------------------------------------------------
$result = wh_process_event($wh_db, $pfx, $event_type, $resource, $evt, $access_token, $api_base, $raw, $_billing_dir);

// Update log entry with final status
if ($log_id > 0) {
    wh_update_event_status($wh_db, $pfx, $log_id, $result['status'], $result['billing_order_id'] ?? 0);
}

wh_log('info', 'event_processed', [
    'event_type' => $event_type,
    'status'     => $result['status'],
    'billing_order_id' => $result['billing_order_id'] ?? 0,
]);

http_response_code(200);
echo json_encode(['status' => $result['status']]);
mysqli_close($wh_db);
exit;

// ============================================================================
// Helper functions
// ============================================================================

/**
 * Get OAuth access token from PayPal.
 */
function wh_get_access_token(string $api_base, string $client_id, string $client_secret): ?string
{
    $ch = curl_init($api_base . '/v1/oauth2/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        CURLOPT_USERPWD        => $client_id . ':' . $client_secret,
        CURLOPT_TIMEOUT        => 15,
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code !== 200 || !$body) {
        return null;
    }
    $data = json_decode($body, true);
    return $data['access_token'] ?? null;
}

/**
 * Verify PayPal webhook signature.
 * Returns true only when PayPal confirms verification_status = SUCCESS.
 */
function wh_verify_signature(
    string $api_base,
    string $access_token,
    string $webhook_id,
    array  $headers,
    array  $evt
): bool {
    $payload = [
        'auth_algo'        => $headers['PAYPAL-AUTH-ALGO']        ?? '',
        'cert_url'         => $headers['PAYPAL-CERT-URL']         ?? '',
        'transmission_id'  => $headers['PAYPAL-TRANSMISSION-ID']  ?? '',
        'transmission_sig' => $headers['PAYPAL-TRANSMISSION-SIG'] ?? '',
        'transmission_time'=> $headers['PAYPAL-TRANSMISSION-TIME'] ?? '',
        'webhook_id'       => $webhook_id,
        'webhook_event'    => $evt,
    ];
    $ch = curl_init($api_base . '/v1/notifications/verify-webhook-signature');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token,
        ],
        CURLOPT_TIMEOUT => 15,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code !== 200 || !$resp) {
        return false;
    }
    $data = json_decode($resp, true);
    return ($data['verification_status'] ?? '') === 'SUCCESS';
}

/**
 * Process a single webhook event. Returns ['status' => string, 'billing_order_id' => int].
 */
function wh_process_event(
    mysqli $db,
    string $pfx,
    string $event_type,
    array  $resource,
    array  $evt,
    string $access_token,
    string $api_base,
    string $raw_json,
    string $billing_dir = ''
): array {
    switch ($event_type) {
        case 'CHECKOUT.ORDER.APPROVED':
            return wh_handle_order_approved($db, $pfx, $resource, $evt);

        case 'PAYMENT.CAPTURE.COMPLETED':
        case 'PAYMENT.SALE.COMPLETED':
            return wh_handle_capture_completed($db, $pfx, $resource, $evt, $access_token, $api_base, $billing_dir);

        case 'PAYMENT.CAPTURE.DENIED':
        case 'PAYMENT.SALE.DENIED':
            return wh_handle_capture_denied($db, $pfx, $resource, $evt);

        case 'PAYMENT.CAPTURE.REFUNDED':
        case 'PAYMENT.SALE.REFUNDED':
            return wh_handle_capture_refunded($db, $pfx, $resource, $evt);

        default:
            wh_log('info', 'unhandled_event_type', ['event_type' => $event_type]);
            return ['status' => 'ignored_unhandled', 'billing_order_id' => 0];
    }
}

/**
 * CHECKOUT.ORDER.APPROVED — buyer approved the order but capture not yet done.
 * We log this for auditing; the actual fulfillment happens on CAPTURE.COMPLETED.
 */
function wh_handle_order_approved(mysqli $db, string $pfx, array $resource, array $evt): array
{
    $paypal_order_id = $resource['id'] ?? ($evt['resource']['id'] ?? '');
    wh_log('info', 'order_approved', ['paypal_order_id' => $paypal_order_id]);
    return ['status' => 'approved_logged', 'billing_order_id' => 0];
}

/**
 * PAYMENT.CAPTURE.COMPLETED — payment fully captured; provision the server.
 */
function wh_handle_capture_completed(
    mysqli $db,
    string $pfx,
    array  $resource,
    array  $evt,
    string $access_token,
    string $api_base,
    string $billing_dir = ''
): array {
    $capture_id = $resource['id'] ?? '';
    $amount     = $resource['amount']['value'] ?? null;
    $currency   = $resource['amount']['currency_code'] ?? 'USD';

    // Extract PayPal order ID from supplementary_data or links
    $paypal_order_id = $resource['supplementary_data']['related_ids']['order_id'] ?? '';
    if (empty($paypal_order_id) && isset($resource['links']) && is_array($resource['links'])) {
        foreach ($resource['links'] as $lnk) {
            if (!empty($lnk['href']) && stripos($lnk['href'], '/v2/checkout/orders/') !== false) {
                $paypal_order_id = basename(parse_url($lnk['href'], PHP_URL_PATH));
                break;
            }
        }
    }

    // Extract invoice/custom from resource or fetch the full order
    $invoice_ref = $resource['invoice_id'] ?? ($resource['invoice_number'] ?? null);
    $custom_id   = $resource['custom_id']  ?? ($resource['custom'] ?? null);

    // If we have a PayPal order ID, fetch the order to get invoice/custom IDs
    if (!empty($paypal_order_id) && (empty($invoice_ref) || empty($custom_id))) {
        $order_data = wh_fetch_paypal_order($api_base, $access_token, $paypal_order_id);
        if ($order_data) {
            $pu = $order_data['purchase_units'][0] ?? [];
            if (empty($invoice_ref)) $invoice_ref = $pu['invoice_id'] ?? null;
            if (empty($custom_id))  $custom_id   = $pu['custom_id']  ?? null;
        }
    }

    wh_log('info', 'capture_completed', [
        'capture_id'      => $capture_id,
        'paypal_order_id' => $paypal_order_id,
        'invoice_ref'     => $invoice_ref,
        'custom_id'       => $custom_id,
        'amount'          => $amount,
    ]);

    // Find matching billing invoice(s) and process payment
    $billing_order_id = wh_fulfill_payment($db, $pfx, [
        'capture_id'      => $capture_id,
        'paypal_order_id' => $paypal_order_id,
        'invoice_ref'     => $invoice_ref,
        'custom_id'       => $custom_id,
        'amount'          => $amount,
        'currency'        => $currency,
    ], $billing_dir);

    return ['status' => 'processed', 'billing_order_id' => $billing_order_id];
}

/**
 * PAYMENT.CAPTURE.DENIED — capture was denied (e.g. failed fraud check).
 */
function wh_handle_capture_denied(mysqli $db, string $pfx, array $resource, array $evt): array
{
    $capture_id = $resource['id'] ?? '';
    wh_log('warn', 'capture_denied', ['capture_id' => $capture_id]);

    // Find the billing order for this capture and mark it denied, if still pending
    if ($capture_id !== '') {
        $esc = mysqli_real_escape_string($db, $capture_id);
        $sql = "UPDATE `{$pfx}billing_orders`
                SET status = 'payment_denied'
                WHERE payment_txid = '{$esc}'
                  AND status NOT IN ('Active','cancelled')
                LIMIT 1";
        mysqli_query($db, $sql);
    }

    return ['status' => 'denied_logged', 'billing_order_id' => 0];
}

/**
 * PAYMENT.CAPTURE.REFUNDED — payment was refunded.
 */
function wh_handle_capture_refunded(mysqli $db, string $pfx, array $resource, array $evt): array
{
    $refund_id  = $resource['id'] ?? '';
    $capture_id = $resource['links'] ? (function () use ($resource) {
        foreach ($resource['links'] as $l) {
            if (($l['rel'] ?? '') === 'up' && stripos($l['href'] ?? '', '/captures/') !== false) {
                return basename(parse_url($l['href'], PHP_URL_PATH));
            }
        }
        return '';
    })() : '';

    wh_log('info', 'capture_refunded', ['refund_id' => $refund_id, 'capture_id' => $capture_id]);

    // Log the refund; do not automatically cancel the server unless the billing lifecycle supports it.
    return ['status' => 'refunded_logged', 'billing_order_id' => 0];
}

/**
 * Fetch a PayPal order by ID. Returns decoded array or null.
 */
function wh_fetch_paypal_order(string $api_base, string $access_token, string $order_id): ?array
{
    $ch = curl_init($api_base . '/v2/checkout/orders/' . urlencode($order_id));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT => 15,
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code !== 200 || !$body) {
        wh_log('warn', 'order_fetch_failed', ['order_id' => $order_id, 'http' => $code]);
        return null;
    }
    $data = json_decode($body, true);
    return is_array($data) ? $data : null;
}

/**
 * Match the PayPal capture to a billing invoice, mark it paid, create/extend billing_orders,
 * and trigger server provisioning. Returns the billing_order_id or 0.
 */
function wh_invoice_ids_from_custom_id($custom_id): array
{
    if (!is_string($custom_id) || $custom_id === '') {
        return [];
    }
    if (ctype_digit($custom_id)) {
        return [intval($custom_id)];
    }
    if (stripos($custom_id, 'cart:') !== 0) {
        return [];
    }
    $invoice_ids = [];
    foreach (explode(',', substr($custom_id, 5)) as $part) {
        $part = trim($part);
        if ($part !== '' && ctype_digit($part)) {
            $invoice_ids[] = intval($part);
        }
    }
    return array_values(array_unique($invoice_ids));
}

function wh_fulfill_payment(mysqli $db, string $pfx, array $payment, string $billing_dir = ''): int
{
    $txid        = $payment['capture_id']      ?? '';
    $custom_id   = $payment['custom_id']       ?? null;
    $invoice_ref = $payment['invoice_ref']     ?? null;
    $amount      = isset($payment['amount'])   ? floatval($payment['amount']) : null;
    $now         = date('Y-m-d H:i:s');
    $esc_txid    = mysqli_real_escape_string($db, (string)$txid);

    // Find matching invoices
    $invoices = [];

    // 1) Match by numeric custom_id (which we set to invoice_id when creating the PayPal order)
    $custom_invoice_ids = wh_invoice_ids_from_custom_id($custom_id);
    if (!empty($custom_invoice_ids)) {
        $id_list = implode(',', array_map('intval', $custom_invoice_ids));
        $res = mysqli_query($db, "SELECT * FROM `{$pfx}billing_invoices` WHERE invoice_id IN ({$id_list}) AND status = 'due' ORDER BY invoice_id ASC");
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $invoices[] = $row;
            }
        }
    }
    elseif (!empty($custom_id) && ctype_digit((string)$custom_id)) {
        $inv_id = intval($custom_id);
        $res = mysqli_query($db, "SELECT * FROM `{$pfx}billing_invoices` WHERE invoice_id = {$inv_id} AND status = 'due' LIMIT 1");
        if ($res && $row = mysqli_fetch_assoc($res)) {
            $invoices[] = $row;
        }
    }

    // 2) Match by invoice reference in description
    if (empty($invoices) && !empty($invoice_ref)) {
        $esc_ref = mysqli_real_escape_string($db, (string)$invoice_ref);
        $res = mysqli_query($db, "SELECT * FROM `{$pfx}billing_invoices` WHERE status = 'due' AND description LIKE '%{$esc_ref}%'");
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $invoices[] = $row;
            }
        }
    }

    // 3) Fallback: match by exact amount
    if (empty($invoices) && $amount !== null) {
        $esc_amount = number_format($amount, 2, '.', '');
        $res = mysqli_query($db, "SELECT * FROM `{$pfx}billing_invoices` WHERE status = 'due' AND amount = {$esc_amount}");
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $invoices[] = $row;
            }
        }
    }

    if (empty($invoices)) {
        wh_log('warn', 'no_matching_invoices', ['custom_id' => $custom_id, 'invoice_ref' => $invoice_ref, 'amount' => $amount]);
        return 0;
    }

    $last_order_id = 0;
    $applied_coupon_id = 0;

    foreach ($invoices as $inv) {
        $invoice_id = intval($inv['invoice_id']);
        $order_id   = intval($inv['order_id'] ?? 0);
        $user_id    = intval($inv['user_id']);
        $service_id = intval($inv['service_id'] ?? 0);
        $duration   = $inv['invoice_duration'] ?? 'month';
        $qty        = max(1, intval($inv['qty'] ?? 1));

        // Mark invoice paid
        $stmt = mysqli_prepare($db, "UPDATE `{$pfx}billing_invoices` SET status='paid', payment_status='paid', paid_date=?, payment_txid=?, payment_method='paypal' WHERE invoice_id=? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssi', $now, $esc_txid, $invoice_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // Increment coupon usage if applicable
        $coupon_id = intval($inv['coupon_id'] ?? 0);
        if ($coupon_id > 0) {
            $applied_coupon_id = $coupon_id;
        }

        $duration_days = 31 * $qty;
        if (stripos($duration, 'day') !== false) {
            $duration_days = $qty;
        } elseif (stripos($duration, 'year') !== false) {
            $duration_days = 365 * $qty;
        }

        if ($order_id > 0) {
            // Renewal: extend existing order
            $res = mysqli_query($db, "SELECT end_date, home_id FROM `{$pfx}billing_orders` WHERE order_id = {$order_id} LIMIT 1");
            if ($res && $row = mysqli_fetch_assoc($res)) {
                $current_end  = $row['end_date'] ?? $now;
                $extend_from  = (strtotime($current_end) > time()) ? $current_end : $now;
                $dt           = new DateTime($extend_from);
                $dt->modify('+' . $duration_days . ' days');
                $new_end      = $dt->format('Y-m-d H:i:s');

                $stmt = mysqli_prepare($db, "UPDATE `{$pfx}billing_orders` SET end_date=?, status='Active', payment_txid=?, paid_ts=? WHERE order_id=? LIMIT 1");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'sssi', $new_end, $esc_txid, $now, $order_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
                $last_order_id = $order_id;
                $existing_home_id = intval($row['home_id'] ?? 0);
                wh_log('info', 'order_renewed', ['order_id' => $order_id, 'new_end' => $new_end, 'home_id' => $existing_home_id]);
                $dir = ($billing_dir !== '') ? $billing_dir : dirname(__DIR__);
                wh_try_provision($dir, $order_id, $user_id);
            }
        } else {
            // New order: create billing_orders row
            $dt      = new DateTime($now);
            $dt->modify('+' . $duration_days . ' days');
            $end_date = $dt->format('Y-m-d H:i:s');
            $invoice_amount = floatval($inv['amount'] ?? $inv['total_due'] ?? 0);
            $price   = number_format($invoice_amount, 2, '.', '');
            $esc_home = mysqli_real_escape_string($db, $inv['home_name'] ?? '');
            $esc_dur  = mysqli_real_escape_string($db, $duration);
            $esc_rcon = mysqli_real_escape_string($db, $inv['remote_control_password'] ?? '');
            $esc_ftp  = mysqli_real_escape_string($db, $inv['ftp_password'] ?? '');
            $ip_val   = intval($inv['ip'] ?? 0);
            $max_pl   = intval($inv['max_players'] ?? 0);

            $sql = sprintf(
                "INSERT INTO `%sbilling_orders` (user_id, service_id, home_name, ip, max_players, qty, invoice_duration, price, remote_control_password, ftp_password, status, order_date, end_date, payment_txid, paid_ts)
                 VALUES (%d, %d, '%s', %d, %d, %d, '%s', %s, '%s', '%s', 'Active', '%s', '%s', '%s', '%s')",
                $pfx,
                $user_id, $service_id, $esc_home, $ip_val, $max_pl, $qty,
                $esc_dur, $price, $esc_rcon, $esc_ftp, $now, $end_date, $esc_txid, $now
            );

            if (mysqli_query($db, $sql)) {
                $new_order_id = (int)mysqli_insert_id($db);

                // Link invoice → order
                $stmt = mysqli_prepare($db, "UPDATE `{$pfx}billing_invoices` SET order_id=? WHERE invoice_id=? LIMIT 1");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'ii', $new_order_id, $invoice_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }

                $last_order_id = $new_order_id;
                wh_log('info', 'order_created', ['order_id' => $new_order_id, 'invoice_id' => $invoice_id]);

                // Attempt provisioning via panel bridge
                $dir = ($billing_dir !== '') ? $billing_dir : dirname(__DIR__);
                wh_try_provision($dir, $new_order_id, $user_id);
            } else {
                wh_log('error', 'order_insert_failed', ['db_error' => mysqli_error($db), 'invoice_id' => $invoice_id]);
            }
        }
    }

    if ($applied_coupon_id > 0) {
        mysqli_query($db, "UPDATE `{$pfx}billing_coupons` SET current_uses = current_uses + 1 WHERE coupon_id = {$applied_coupon_id}");
    }

    return $last_order_id;
}

/**
 * Attempt to provision a newly created server via the panel bridge.
 * Non-fatal: logs warnings on failure.
 */
function wh_try_provision(string $billing_dir, int $order_id, int $user_id): void
{
    $bridge = $billing_dir . '/includes/panel_bridge.php';
    $create = $billing_dir . '/create_servers.php';
    if (!is_file($bridge) || !is_file($create)) {
        wh_log('info', 'provision_skipped_no_bridge', ['order_id' => $order_id]);
        return;
    }
    try {
        require_once $bridge;
        if (!function_exists('billing_panel_bootstrap')) {
            wh_log('warn', 'provision_no_bootstrap_fn', ['order_id' => $order_id]);
            return;
        }
        $ctx = billing_panel_bootstrap();
        if (!$ctx || empty($ctx['db'])) {
            wh_log('warn', 'provision_panel_bootstrap_failed', ['order_id' => $order_id]);
            return;
        }
        $GLOBALS['db']       = $ctx['db'];
        $GLOBALS['settings'] = $ctx['settings'] ?? [];
        require_once $create;
        if (function_exists('billing_invoke_provision')) {
            $r = billing_invoke_provision(['order_ids' => [$order_id], 'user_id' => $user_id, 'is_admin' => true]);
            wh_log('info', 'provision_result', ['order_id' => $order_id, 'result' => $r]);
        }
    } catch (Throwable $e) {
        wh_log('error', 'provision_exception', ['order_id' => $order_id, 'error' => $e->getMessage()]);
    }
}

// ============================================================================
// Webhook event log table helpers
// ============================================================================

/**
 * Ensure billing_paypal_webhook_events table exists (idempotent, no ALTER on existing tables).
 */
function wh_ensure_event_table(mysqli $db, string $pfx): void
{
    $table = $pfx . 'billing_paypal_webhook_events';
    $res   = mysqli_query($db, "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table}'");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        if (intval($row['cnt']) > 0) {
            return; // table exists
        }
    }
    $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
        `id`                INT(11)       NOT NULL AUTO_INCREMENT,
        `paypal_event_id`   VARCHAR(100)  NOT NULL DEFAULT '',
        `event_type`        VARCHAR(100)  NOT NULL DEFAULT '',
        `resource_id`       VARCHAR(100)  NOT NULL DEFAULT '',
        `order_id`          VARCHAR(100)  NOT NULL DEFAULT '',
        `capture_id`        VARCHAR(100)  NOT NULL DEFAULT '',
        `billing_order_id`  INT(11)       NOT NULL DEFAULT 0,
        `processing_status` VARCHAR(50)   NOT NULL DEFAULT 'received',
        `raw_json`          MEDIUMTEXT    NULL,
        `created_at`        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `processed_at`      DATETIME      NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uidx_paypal_event_id` (`paypal_event_id`),
        KEY `idx_event_type` (`event_type`),
        KEY `idx_billing_order_id` (`billing_order_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    mysqli_query($db, $sql);
}

/**
 * Retrieve an existing webhook event log row by paypal_event_id.
 */
function wh_get_event(mysqli $db, string $pfx, string $paypal_event_id): ?array
{
    if ($paypal_event_id === '') return null;
    $esc = mysqli_real_escape_string($db, $paypal_event_id);
    $res = mysqli_query($db, "SELECT * FROM `{$pfx}billing_paypal_webhook_events` WHERE paypal_event_id = '{$esc}' LIMIT 1");
    if (!$res) return null;
    $row = mysqli_fetch_assoc($res);
    return $row ?: null;
}

/**
 * Insert or update a webhook event log row. Returns the row id.
 */
function wh_log_event(mysqli $db, string $pfx, array $data): int
{
    $paypal_event_id   = mysqli_real_escape_string($db, $data['paypal_event_id']   ?? '');
    $event_type        = mysqli_real_escape_string($db, $data['event_type']        ?? '');
    $resource_id       = mysqli_real_escape_string($db, $data['resource_id']       ?? '');
    $order_id_str      = mysqli_real_escape_string($db, $data['order_id']          ?? '');
    $capture_id        = mysqli_real_escape_string($db, $data['capture_id']        ?? '');
    $billing_order_id  = intval($data['billing_order_id'] ?? 0);
    $processing_status = mysqli_real_escape_string($db, $data['processing_status'] ?? 'received');
    $raw_json          = mysqli_real_escape_string($db, $data['raw_json']          ?? '');
    $now               = date('Y-m-d H:i:s');

    if ($paypal_event_id === '') {
        // No stable event ID — always insert
        $sql = "INSERT INTO `{$pfx}billing_paypal_webhook_events`
                    (paypal_event_id, event_type, resource_id, order_id, capture_id, billing_order_id, processing_status, raw_json, created_at)
                VALUES ('{$paypal_event_id}', '{$event_type}', '{$resource_id}', '{$order_id_str}', '{$capture_id}', {$billing_order_id}, '{$processing_status}', '{$raw_json}', '{$now}')";
        mysqli_query($db, $sql);
        return (int)mysqli_insert_id($db);
    }

    // Upsert: insert or update existing row
    $sql = "INSERT INTO `{$pfx}billing_paypal_webhook_events`
                (paypal_event_id, event_type, resource_id, order_id, capture_id, billing_order_id, processing_status, raw_json, created_at)
            VALUES ('{$paypal_event_id}', '{$event_type}', '{$resource_id}', '{$order_id_str}', '{$capture_id}', {$billing_order_id}, '{$processing_status}', '{$raw_json}', '{$now}')
            ON DUPLICATE KEY UPDATE
                processing_status = VALUES(processing_status),
                billing_order_id  = VALUES(billing_order_id)";
    mysqli_query($db, $sql);
    $insert_id = (int)mysqli_insert_id($db);
    if ($insert_id > 0) {
        return $insert_id;
    }
    // Row already existed — fetch its id
    $existing = wh_get_event($db, $pfx, $data['paypal_event_id']);
    return $existing ? intval($existing['id']) : 0;
}

/**
 * Update processing_status and processed_at on an event log row.
 */
function wh_update_event_status(mysqli $db, string $pfx, int $log_id, string $status, int $billing_order_id): void
{
    $esc_status = mysqli_real_escape_string($db, $status);
    $now        = date('Y-m-d H:i:s');
    $bod        = intval($billing_order_id);
    mysqli_query($db, "UPDATE `{$pfx}billing_paypal_webhook_events`
                       SET processing_status = '{$esc_status}', billing_order_id = {$bod}, processed_at = '{$now}'
                       WHERE id = {$log_id} LIMIT 1");
}
