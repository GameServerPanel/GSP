<?php
/**
 * Standalone Payment Processing Helper (module-local)
 *
 * This file is intentionally self-contained and does NOT include or rely on
 * panel-wide files. Configure the DB connection information below (edit
 * the $DB_* variables) or provide a module-local `config.local.php` that defines
 * the same variables.
 *
 * Usage:
 *   require_once __DIR__ . '/payment_processor.php';
 *   process_payment_record($record);
 */

// Load panel config first, falling back to a module-local copy if needed.
require_once __DIR__ . '/config_loader.php';

// Variables from config.inc.php (helps IDEs understand scope)
/** @var string $db_host Database host */
/** @var string $db_user Database user */
/** @var string $db_pass Database password */
/** @var string $db_name Database name */
/** @var string $table_prefix Table prefix for database tables */

// Normalize table prefix variable: many files use $table_prefix (lowercase)
if (!isset($TABLE_PREFIX) && isset($table_prefix)) {
    $TABLE_PREFIX = $table_prefix;
}

/**
 * Create and return a mysqli connection using the site's config values.
 * Returns mysqli or false on failure.
 */
function payment_db_connect() {
    global $db_host, $db_user, $db_pass, $db_name, $db_port;

    if (empty($db_host) || empty($db_user) || empty($db_name)) {
        error_log('[payment_processor] DB globals not available from includes/config.inc.php');
        return false;
    }

    $port = intval($db_port) ?: 3306;
    $db = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, $port);
    if (!$db) {
        error_log('[payment_processor] mysqli_connect failed: ' . mysqli_connect_error());
        return false;
    }
    mysqli_set_charset($db, 'utf8mb4');
    return $db;
}

/**
 * Process a payment record. Self-contained: uses module-local DB config.
 * @param array $record { invoice, custom, resource_id, amount }
 * @return bool true if at least one invoice processed
 */
function process_payment_record(array $record) {
    global $TABLE_PREFIX;

    // Normalize inputs
    $invoice_ref = $record['invoice'] ?? null;     // e.g. PayPal invoice id or custom label
    $custom = $record['custom'] ?? null;           // our custom_id (often invoice_id)
    $txid = $record['resource_id'] ?? ($record['txid'] ?? null);
    $amount = isset($record['amount']) ? floatval($record['amount']) : null;

    $db = payment_db_connect();
    if (!$db) return false;

    $now = date('Y-m-d H:i:s');
    $esc_txid = mysqli_real_escape_string($db, (string)$txid);

    $invoices_to_process = [];

    // 1) If custom is a single numeric invoice id, try to fetch that exact invoice
    if ($custom && ctype_digit((string)$custom)) {
        $invoice_id = intval($custom);
        $sql = "SELECT * FROM `" . $TABLE_PREFIX . "billing_invoices` WHERE invoice_id = ? AND status = 'due' LIMIT 1";
        if ($stmt = mysqli_prepare($db, $sql)) {
            mysqli_stmt_bind_param($stmt, 'i', $invoice_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res && $row = mysqli_fetch_assoc($res)) $invoices_to_process[] = $row;
            mysqli_stmt_close($stmt);
        }
    }

    // 2) If we didn't find by custom and invoice_ref is provided, match by description containing invoice_ref
    if (empty($invoices_to_process) && $invoice_ref) {
        $like = '%' . mysqli_real_escape_string($db, $invoice_ref) . '%';
        $sql = "SELECT * FROM `" . $TABLE_PREFIX . "billing_invoices` WHERE status = 'due' AND description LIKE ?";
        if ($stmt = mysqli_prepare($db, $sql)) {
            mysqli_stmt_bind_param($stmt, 's', $like);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res) {
                while ($row = mysqli_fetch_assoc($res)) $invoices_to_process[] = $row;
            }
            mysqli_stmt_close($stmt);
        }
    }

    // 3) As a fallback, match unpaid invoices by amount (exact match). Useful for simple carts.
    if (empty($invoices_to_process) && $amount !== null) {
        $sql = "SELECT * FROM `" . $TABLE_PREFIX . "billing_invoices` WHERE status = 'due' AND amount = ?";
        if ($stmt = mysqli_prepare($db, $sql)) {
            mysqli_stmt_bind_param($stmt, 'd', $amount);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res) {
                while ($row = mysqli_fetch_assoc($res)) $invoices_to_process[] = $row;
            }
            mysqli_stmt_close($stmt);
        }
    }

    $processed_count = 0;

    foreach ((array)$invoices_to_process as $inv) {
        $invoice_id = intval($inv['invoice_id']);
        $order_id = intval($inv['order_id'] ?? 0);
        $user_id = intval($inv['user_id']);
        $service_id = intval($inv['service_id'] ?? 0);
        $home_name = mysqli_real_escape_string($db, $inv['home_name'] ?? '');
        $ip = intval($inv['ip'] ?? 0);
        $max_players = intval($inv['max_players'] ?? 0);
        $qty = intval($inv['qty'] ?? 1);
        $duration = $inv['invoice_duration'] ?? 'month';
        $invoice_amount = floatval($inv['amount'] ?? 0);
        $rcon_pw = mysqli_real_escape_string($db, $inv['remote_control_password'] ?? '');
        $ftp_pw = mysqli_real_escape_string($db, $inv['ftp_password'] ?? '');

        // Mark invoice as paid
        $upd = "UPDATE `" . $TABLE_PREFIX . "billing_invoices` SET status='paid', paid_date=?, payment_txid=?, payment_method='paypal' WHERE invoice_id = ? LIMIT 1";
        if ($stmt = mysqli_prepare($db, $upd)) {
            mysqli_stmt_bind_param($stmt, 'ssi', $now, $esc_txid, $invoice_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // If invoice has a coupon, increment usage count
        $coupon_id = intval($inv['coupon_id'] ?? 0);
        if ($coupon_id > 0) {
            $upd_coupon = "UPDATE `" . $TABLE_PREFIX . "billing_coupons` SET current_uses = current_uses + 1 WHERE coupon_id = ?";
            if ($stmt = mysqli_prepare($db, $upd_coupon)) {
                mysqli_stmt_bind_param($stmt, 'i', $coupon_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }

        // If this invoice already has an order -> treat as renewal
        if ($order_id > 0) {
            // compute months
            $months = (stripos($duration, 'year') !== false) ? ($qty * 12) : $qty;
            // fetch current end_date
            $get = "SELECT end_date FROM `" . $TABLE_PREFIX . "billing_orders` WHERE order_id = ? LIMIT 1";
            if ($stmt = mysqli_prepare($db, $get)) {
                mysqli_stmt_bind_param($stmt, 'i', $order_id);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                if ($res && $row = mysqli_fetch_assoc($res)) {
                    $current_end = $row['end_date'] ?? date('Y-m-d H:i:s');
                    $extend_from = (strtotime($current_end) > time()) ? $current_end : date('Y-m-d H:i:s');
                    $dt = new DateTime($extend_from);
                    if ($months > 0) $dt->modify('+' . intval($months) . ' months');
                    $new_end = $dt->format('Y-m-d H:i:s');
                    $update = "UPDATE `" . $TABLE_PREFIX . "billing_orders` SET end_date = ?, status='Active', payment_txid = ?, paid_ts = ? WHERE order_id = ?";
                    if ($u = mysqli_prepare($db, $update)) {
                        mysqli_stmt_bind_param($u, 'sssi', $new_end, $esc_txid, $now, $order_id);
                        mysqli_stmt_execute($u);
                        mysqli_stmt_close($u);
                        $processed_count++;
                    }
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            // Create new order
            $months = (stripos($duration, 'year') !== false) ? ($qty * 12) : $qty;
            $dt = new DateTime('now');
            if ($months > 0) $dt->modify('+' . intval($months) . ' months');
            $end_date = $dt->format('Y-m-d H:i:s');

            // Simpler insert using properly escaped values
            $esc_home = mysqli_real_escape_string($db, $home_name);
            $esc_rcon = mysqli_real_escape_string($db, $rcon_pw);
            $esc_ftp = mysqli_real_escape_string($db, $ftp_pw);
            $esc_duration = mysqli_real_escape_string($db, $duration);
            $price = number_format($invoice_amount, 2, '.', '');

            $insert2 = sprintf(
                "INSERT INTO `%s` (user_id, service_id, home_name, ip, max_players, qty, invoice_duration, price, remote_control_password, ftp_password, status, order_date, end_date, payment_txid, paid_ts) VALUES (%d, %d, '%s', %d, %d, %d, '%s', %s, '%s', '%s', 'Active', '%s', '%s', '%s', '%s')",
                $TABLE_PREFIX . 'billing_orders',
                $user_id, $service_id, $esc_home, $ip, $max_players, $qty, $esc_duration, $price, $esc_rcon, $esc_ftp, $now, $end_date, $esc_txid, $now
            );
            if (mysqli_query($db, $insert2)) {
                $new_order_id = mysqli_insert_id($db);
                $link = "UPDATE `" . $TABLE_PREFIX . "billing_invoices` SET order_id = ? WHERE invoice_id = ?";
                if ($u = mysqli_prepare($db, $link)) {
                    mysqli_stmt_bind_param($u, 'ii', $new_order_id, $invoice_id);
                    mysqli_stmt_execute($u);
                    mysqli_stmt_close($u);
                }
                $processed_count++;
            } else {
                error_log('[payment_processor] Failed to insert order: ' . mysqli_error($db));
            }
        }
    }

    mysqli_close($db);

    if ($processed_count > 0) {
        error_log('[payment_processor] Processed ' . $processed_count . ' invoice(s)');
        return true;
    }

    error_log('[payment_processor] No matching invoices processed for record: ' . json_encode($record));
    return false;
}

?>

