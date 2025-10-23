<?php
// Helper to process a persisted payment record and mark orders paid in panel DB.
// Usage: require_once(__DIR__ . '/payment_success.php'); process_payment_record($record);

function process_payment_record(array $record) {
    // Minimal validation
    $invoice = $record['invoice'] ?? '';
    $custom  = $record['custom'] ?? '';
    $txid    = $record['resource_id'] ?? '';
    $ts      = $record['ts'] ?? date('c');

    // Attempt DB update using site DB config
    // This file lives in _website/, config is in includes/config.inc.php
    $cfg = __DIR__ . '/includes/config.inc.php';
    if (!is_file($cfg)) {
        error_log('[payment_success] missing config: ' . $cfg);
        return false;
    }
    require_once($cfg);
    // include site logging helper if available
    if (is_file(__DIR__ . '/includes/log.php')) require_once(__DIR__ . '/includes/log.php');

    // Use variables from config.inc.php: $db_host, $db_user, $db_pass, $db_name
    $db = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (!$db) {
        if (function_exists('site_log_error')) site_log_error('payment_success_db_connect_failed', ['error'=>mysqli_connect_error()]);
        else error_log('[payment_success] DB connect failed: ' . mysqli_connect_error());
        return false;
    }

    // Helper to run a prepared update
    $update_paid = function($where_sql, $bind_types, $bind_vals) use ($db, $txid, $ts) {
        // Ensure we only set paid when not already paid
        $sql = "UPDATE ogp_billing_orders SET status = 'paid'";
        // Optionally set txid/paid_ts if columns exist; also attempt finish_date
        $cols = [];
        $res = mysqli_query($db, "SHOW COLUMNS FROM ogp_billing_orders LIKE 'payment_txid'");
        if ($res && mysqli_num_rows($res) > 0) $cols[] = 'payment_txid';
        $res2 = mysqli_query($db, "SHOW COLUMNS FROM ogp_billing_orders LIKE 'paid_ts'");
        if ($res2 && mysqli_num_rows($res2) > 0) $cols[] = 'paid_ts';
        $res3 = mysqli_query($db, "SHOW COLUMNS FROM ogp_billing_orders LIKE 'finish_date'");
        $has_finish = ($res3 && mysqli_num_rows($res3) > 0);
        // We'll compute finish_date when possible by selecting qty/invoice_duration for the matched row later
        if ($cols) {
            $sql .= ', ' . implode(' = ?, ', $cols) . ' = ?';
        }
        // placeholder for finish_date; we'll append it if we can compute it
        $sql .= ' WHERE ' . $where_sql . ' AND status <> "paid" LIMIT 1';

        // If we need finish_date, attempt to compute it by selecting the row first
        $finish_date_val = null;
        if ($has_finish) {
            // Attempt to find the target order's qty/invoice_duration using the same where clause but without LIMIT
            $sel_sql = "SELECT qty, invoice_duration FROM ogp_billing_orders WHERE " . str_replace(' AND status <> \"paid\" LIMIT 1', '', $where_sql) . " LIMIT 1";
            // Note: this simple substitution assumes the where_sql is of the form 'col = ?' used earlier
            if ($sel_stmt = $db->prepare($sel_sql)) {
                // bind where params
                if ($bind_types) {
                    $refs = [];
                    $vals = $bind_vals;
                    foreach ($vals as $k => $v) $refs[$k] = &$vals[$k];
                    array_unshift($refs, $bind_types);
                    call_user_func_array([$sel_stmt, 'bind_param'], $refs);
                }
                $sel_stmt->execute();
                $sel_stmt->bind_result($sel_qty, $sel_invdur);
                if ($sel_stmt->fetch()) {
                    // compute months
                    $months = 0;
                    $q = intval($sel_qty ?? 0);
                    $invdur = strtolower(trim($sel_invdur ?? ''));
                    if (strpos($invdur, 'year') !== false) {
                        $months = $q * 12;
                    } else {
                        $months = $q;
                    }
                    if ($months <= 0) $months = 0;
                    $dt = new DateTime('now');
                    if ($months > 0) $dt->modify('+' . intval($months) . ' months');
                    $finish_date_val = $dt->format('Y-m-d H:i:s');
                }
                $sel_stmt->close();
            }
            if ($finish_date_val !== null) {
                $sql = str_replace(' WHERE ', ', finish_date = ? WHERE ', $sql);
            }
        }

        if ($stmt = $db->prepare($sql)) {
            // Build params: first any where params, then txid/ts values if present, then finish_date if present
            $types = $bind_types;
            $vals = $bind_vals;
            if ($cols) {
                foreach ($cols as $c) {
                    $types .= 's';
                    if ($c === 'payment_txid') $vals[] = $txid;
                    else $vals[] = $ts;
                }
            }
            if ($finish_date_val !== null) {
                $types .= 's';
                $vals[] = $finish_date_val;
            }
            // bind dynamically
            if ($types) {
                $refs = [];
                foreach ($vals as $k => $v) $refs[$k] = &$vals[$k];
                array_unshift($refs, $types);
                call_user_func_array([$stmt, 'bind_param'], $refs);
            }
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();
            return $affected;
        }
        return 0;
    };

    $affected = 0;
    // Try match by invoice column (if present)
    if ($invoice) {
        // some invoices may include paths or file names; use exact match
        $affected = $update_paid('invoice = ?', 's', [$invoice]);
    }

    // If not matched, try numeric custom (order_id)
    if (!$affected && $custom) {
        if (ctype_digit((string)$custom)) {
            $affected = $update_paid('order_id = ?', 'i', [(int)$custom]);
        }
    }

    // If still not matched, try matching the custom text field
    if (!$affected && $custom) {
        $affected = $update_paid('custom = ?', 's', [$custom]);
    }

    mysqli_close($db);

    if ($affected) {
        if (function_exists('site_log_info')) site_log_info('payment_success_marked_paid', ['affected'=>intval($affected),'invoice'=>$invoice,'custom'=>$custom]);
        else error_log('[payment_success] Marked order paid (affected=' . intval($affected) . ') invoice=' . $invoice . ' custom=' . $custom);
        return true;
    } else {
        if (function_exists('site_log_warn')) site_log_warn('payment_success_no_match', ['invoice'=>$invoice,'custom'=>$custom]);
        else error_log('[payment_success] No matching order found for invoice=' . $invoice . ' custom=' . $custom);
        return false;
    }
}

?>
