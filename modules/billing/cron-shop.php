<?php
/*
 *
 * OGP / GSP - Open Game Panel / Game Server Panel
 * Copyright (C) 2008 - 2017 The OGP Development Team
 *
 * http://www.opengamepanel.org/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 *
 * BILLING CRON - Three-Status Lifecycle
 * ========================================
 *
 * Operates on server_homes.billing_status (separate from game-server runtime state).
 *
 * Status values:
 *   Active   - Server is current; no unpaid renewal invoice.
 *   Invoiced - Renewal invoice generated; payment due.
 *   Expired  - Invoice not paid by due date; server awaiting deletion.
 *
 * Steps run each night:
 *   A. Active   -> Invoiced  : next_invoice_date has arrived -> create {prefix}invoices record.
 *   B. Invoiced -> Expired   : server_expiration_date passed and invoice unpaid.
 *   C. Expired  -> Deleted   : past delete_after_expired_days grace window -> remove server.
 *   D. Paid invoices (safety net): set server and invoice back to Active.
 *
 * Prerequisites (run once):
 *   sql/update_billing_status_active_invoiced_expired.sql
 */

chdir(realpath(dirname(__FILE__))); /* Change to the billing module directory */
chdir("../.."); /* Step back to the OGP/GSP web root */

error_reporting(E_ALL);
ini_set('display_errors', '1');

define("CONFIG_FILE", "includes/config.inc.php");
require_once("includes/functions.php");
require_once("includes/helpers.php");
require_once("includes/html_functions.php");
require_once("modules/config_games/server_config_parser.php");
require_once("includes/lib_remote.php");
require_once(CONFIG_FILE);

// Connect using the panel's DB helper (provides $db with logger(), resultQuery(), etc.)
$db = createDatabaseConnection(
    $db_type, $db_host, $db_user, $db_pass, $db_name, $table_prefix,
    isset($db_port) ? $db_port : null
);

$panel_settings = $db->getSettings();
if (!empty($panel_settings['time_zone'])) {
    date_default_timezone_set($panel_settings['time_zone']);
}

$rundate = date('Y-m-d H:i:s');
$db->logger("BILLING-CRON: ===== Lifecycle automation started at {$rundate} =====");

// ----------------------------------------------------------------
// Load global billing config (grace_days, delete_after_expired_days)
// Falls back to safe defaults when {prefix}billing_config is empty.
// ----------------------------------------------------------------
$cfg_rows = $db->resultQuery(
    "SELECT * FROM {$table_prefix}billing_config WHERE game_key IS NULL AND enabled = 1 ORDER BY config_id ASC LIMIT 1"
);
$global_cfg           = is_array($cfg_rows) && !empty($cfg_rows) ? $cfg_rows[0] : [];
$grace_days           = intval($global_cfg['grace_days']                ?? 0);
$delete_after_days    = intval($global_cfg['delete_after_expired_days'] ?? 7);
$default_rate_type    = $global_cfg['rate_type']                        ?? 'monthly';
$default_price_player = floatval($global_cfg['price_per_player']        ?? 0.00);

$db->logger("BILLING-CRON: Config => grace_days={$grace_days}, delete_after={$delete_after_days}, rate={$default_rate_type}");

// ======================================================================
// STEP A - Active -> Invoiced
//   Find billing-enabled servers whose next_invoice_date has arrived
//   and that do not already have an open 'Invoiced' renewal invoice.
// ======================================================================
$db->logger("BILLING-CRON: --- Step A: Active -> Invoiced ---");

$due_for_invoice = $db->resultQuery("
    SELECT sh.home_id, sh.home_name, sh.user_id_main AS user_id,
           sh.next_invoice_date, sh.server_expiration_date,
           bo.price, bo.invoice_duration, bo.qty, bo.order_id,
           COALESCE(bs.price_monthly, 0) AS svc_price_monthly,
           u.users_email,
           CONCAT(COALESCE(u.users_fname,''), ' ', COALESCE(u.users_lname,'')) AS customer_name
    FROM {$table_prefix}server_homes sh
    LEFT JOIN {$table_prefix}users u ON u.user_id = sh.user_id_main
    LEFT JOIN {$table_prefix}billing_orders bo
           ON bo.home_id = sh.home_id AND bo.status = 'Active'
    LEFT JOIN {$table_prefix}billing_services bs ON bs.service_id = bo.service_id
    WHERE sh.billing_enabled = 1
      AND sh.billing_status  = 'Active'
      AND sh.next_invoice_date IS NOT NULL
      AND sh.next_invoice_date <= NOW()
      AND NOT EXISTS (
          SELECT 1 FROM {$table_prefix}invoices inv
          WHERE inv.home_id = sh.home_id AND inv.billing_status = 'Invoiced'
      )
    ORDER BY sh.home_id ASC
");

if (is_array($due_for_invoice)) {
    foreach ($due_for_invoice as $srv) {
        $home_id   = intval($srv['home_id']);
        $user_id   = intval($srv['user_id']);
        $home_name = $srv['home_name'] ?? 'Server #' . $home_id;
        $qty       = max(1, intval($srv['qty'] ?? 1));

        // Normalise rate_type to the ENUM values used in {prefix}invoices
        $raw_rate = strtolower($srv['invoice_duration'] ?? $default_rate_type);
        $rate_map = ['day' => 'daily', 'month' => 'monthly', 'year' => 'yearly'];
        $rate_type = $rate_map[$raw_rate] ?? $raw_rate;

        // Pricing: billing_config > billing_orders flat price
        $price_per_player = $default_price_player;
        $player_slots     = max(0, intval($srv['qty'] ?? 0));
        $subtotal         = $price_per_player * max(1, $player_slots);
        if ($subtotal == 0.00 && floatval($srv['price'] ?? 0) > 0) {
            $subtotal = floatval($srv['price']);
        }
        $total_due = $subtotal;

        // Calculate due_date: now + 1 billing period
        $period_map = ['daily' => '+1 day', 'monthly' => '+1 month', 'yearly' => '+1 year'];
        $due_date_ts = strtotime($period_map[$rate_type], time());
        $due_date    = date('Y-m-d H:i:s', $due_date_ts);

        // Guard: skip if an invoice for this exact period already exists
        $exists = $db->resultQuery("
            SELECT invoice_id FROM {$table_prefix}invoices
            WHERE home_id = {$home_id}
              AND due_date = '" . $db->realEscapeSingle($due_date) . "'
            LIMIT 1
        ");
        if (is_array($exists) && !empty($exists)) {
            $db->logger("BILLING-CRON: Step A - SKIP home {$home_id}: invoice for this period already exists");
            continue;
        }

        // Create renewal invoice in {prefix}invoices
        $db->query("
            INSERT INTO {$table_prefix}invoices
                (home_id, user_id, due_date, billing_status, rate_type,
                 price_per_player, player_slots, quantity, subtotal, total_due)
            VALUES (
                {$home_id}, {$user_id},
                '" . $db->realEscapeSingle($due_date) . "',
                'Invoiced',
                '" . $db->realEscapeSingle($rate_type) . "',
                " . number_format($price_per_player, 2, '.', '') . ",
                {$player_slots},
                {$qty},
                " . number_format($subtotal, 2, '.', '') . ",
                " . number_format($total_due, 2, '.', '') . "
            )
        ");
        $new_invoice_id = $db->lastInsertId();

        // Update server_homes: set Invoiced, store invoice id and expiration date
        $db->query("
            UPDATE {$table_prefix}server_homes
            SET billing_status        = 'Invoiced',
                server_expiration_date = '" . $db->realEscapeSingle($due_date) . "',
                last_invoice_id        = " . intval($new_invoice_id) . "
            WHERE home_id = {$home_id}
        ");

        $db->logger("BILLING-CRON: Step A - INVOICED home {$home_id} (invoice #{$new_invoice_id}, due {$due_date})");

        // Send renewal notice
        if (!empty($srv['users_email'])) {
            $settings = $db->getSettings();
            $subject  = "Renewal Invoice for {$home_name} - " . ($panel_settings['panel_name'] ?? 'Game Server Panel');
            $message  = "Your server '{$home_name}' (ID: {$home_id}) has a renewal invoice due on "
                      . date('F j, Y', $due_date_ts) . "."
                      . "<br><br>Amount Due: \$" . number_format($total_due, 2)
                      . "<br>Due Date: " . date('F j, Y', $due_date_ts)
                      . "<br><br>Please log in to pay your invoice and keep your server active."
                      . "<br><br>Thank you!";
            if (!mymail($srv['users_email'], $subject, $message, $settings)) {
                $db->logger("BILLING-CRON: Step A - Email FAILED for home {$home_id}");
            }
        }
    }
}

// ======================================================================
// STEP B - Invoiced -> Expired
//   Servers whose expiration date has passed and whose last invoice
//   is still unpaid.
// ======================================================================
$db->logger("BILLING-CRON: --- Step B: Invoiced -> Expired (grace_days={$grace_days}) ---");

$past_due = $db->resultQuery("
    SELECT sh.home_id, sh.home_name, sh.user_id_main AS user_id,
           sh.last_invoice_id, sh.server_expiration_date,
           u.users_email
    FROM {$table_prefix}server_homes sh
    LEFT JOIN {$table_prefix}users u ON u.user_id = sh.user_id_main
    WHERE sh.billing_enabled      = 1
      AND sh.billing_status       = 'Invoiced'
      AND sh.server_expiration_date IS NOT NULL
      AND DATE(sh.server_expiration_date) < DATE_SUB(CURDATE(), INTERVAL {$grace_days} DAY)
      AND (
          sh.last_invoice_id IS NULL
          OR EXISTS (
              SELECT 1 FROM {$table_prefix}invoices inv
              WHERE inv.invoice_id    = sh.last_invoice_id
                AND inv.billing_status = 'Invoiced'
                AND inv.paid_at       IS NULL
          )
      )
    ORDER BY sh.home_id ASC
");

if (is_array($past_due)) {
    foreach ($past_due as $srv) {
        $home_id         = intval($srv['home_id']);
        $last_invoice_id = intval($srv['last_invoice_id'] ?? 0);

        // Mark server Expired
        $db->query("
            UPDATE {$table_prefix}server_homes
            SET billing_status = 'Expired'
            WHERE home_id = {$home_id}
        ");

        // Mark matching invoice Expired (if still unpaid)
        if ($last_invoice_id > 0) {
            $db->query("
                UPDATE {$table_prefix}invoices
                SET billing_status = 'Expired'
                WHERE invoice_id    = {$last_invoice_id}
                  AND billing_status = 'Invoiced'
                  AND paid_at       IS NULL
            ");
        }

        $db->logger("BILLING-CRON: Step B - EXPIRED home {$home_id}");

        // Notify user
        if (!empty($srv['users_email'])) {
            $settings  = $db->getSettings();
            $home_name = $srv['home_name'] ?? 'Server #' . $home_id;
            $subject   = "Server Expired - {$home_name} - " . ($panel_settings['panel_name'] ?? 'Game Server Panel');
            $message   = "Your server '{$home_name}' (ID: {$home_id}) has expired due to non-payment."
                       . "<br><br>The server will be permanently deleted in {$delete_after_days} day(s) if payment is not received."
                       . "<br><br>Please log in and pay your outstanding invoice to restore service."
                       . "<br><br>Thank you.";
            if (!mymail($srv['users_email'], $subject, $message, $settings)) {
                $db->logger("BILLING-CRON: Step B - Email FAILED for home {$home_id}");
            }
        }
    }
}

// ======================================================================
// STEP C - Expired -> Deleted
//   Servers that have been Expired longer than delete_after_expired_days.
// ======================================================================
$db->logger("BILLING-CRON: --- Step C: Expired -> Deleted (window={$delete_after_days}d) ---");

$to_delete = $db->resultQuery("
    SELECT sh.home_id, sh.home_name, sh.user_id_main AS user_id,
           sh.server_expiration_date,
           u.users_email
    FROM {$table_prefix}server_homes sh
    LEFT JOIN {$table_prefix}users u ON u.user_id = sh.user_id_main
    WHERE sh.billing_enabled       = 1
      AND sh.billing_status        = 'Expired'
      AND sh.server_expiration_date IS NOT NULL
      AND DATE(sh.server_expiration_date) < DATE_SUB(CURDATE(), INTERVAL {$delete_after_days} DAY)
    ORDER BY sh.home_id ASC
");

if (is_array($to_delete)) {
    foreach ($to_delete as $srv) {
        $home_id   = intval($srv['home_id']);
        $user_id   = intval($srv['user_id']);
        $home_name = $srv['home_name'] ?? 'Server #' . $home_id;

        // Fetch home info for remote deletion
        $home_info = $db->getGameHomeWithoutMods($home_id);
        if ($home_info) {
            $server_info = $db->getRemoteServerById($home_info['remote_server_id']);
            if ($server_info) {
                $remote = new OGPRemoteLibrary(
                    $server_info['agent_ip'],
                    $server_info['agent_port'],
                    $server_info['encryption_key'],
                    $server_info['timeout']
                );

                // Stop the running server process
                $server_xml   = read_server_config(SERVER_CONFIG_LOCATION . "/" . $home_info['home_cfg_file']);
                $control_type = isset($server_xml->control_protocol_type)
                              ? (string)$server_xml->control_protocol_type : "";
                $addresses    = $db->getHomeIpPorts($home_id);
                foreach ((array)$addresses as $addr) {
                    $remote->remote_stop_server(
                        $home_id, $addr['ip'], $addr['port'],
                        $server_xml->control_protocol,
                        $home_info['control_password'],
                        $control_type,
                        $home_info['home_path']
                    );
                }

                // Disable FTP
                $ftp_login = !empty($home_info['ftp_login']) ? $home_info['ftp_login'] : $home_id;
                $remote->ftp_mgr("userdel", $ftp_login);
                $db->changeFtpStatus('disabled', $home_id);

                // Unassign from user
                $db->unassignHomeFrom("user", $user_id, $home_id);

                // Delete home record from panel DB
                $db->deleteGameHome($home_id);

                // Remove server files on remote agent
                $remote->remove_home($home_info['home_path']);

                // Drop any per-server database/user accounts
                @$db->query("DROP USER 'user_{$home_id}'@'%'");
                @$db->query("DROP USER 'user_{$home_id}'@'localhost'");
                @$db->query("DROP USER 'server_{$home_id}'@'%'");
                @$db->query("DROP USER 'server_{$home_id}'@'localhost'");
                @$db->query("DROP DATABASE IF EXISTS user_{$home_id}");
                @$db->query("DROP DATABASE IF EXISTS server_{$home_id}");
            } else {
                $db->logger("BILLING-CRON: Step C - WARNING: no remote server info for home {$home_id}; removing panel record only");
                $db->deleteGameHome($home_id);
            }
        } else {
            $db->logger("BILLING-CRON: Step C - WARNING: home {$home_id} not found in panel DB (already removed)");
        }

        // Mark billing_orders record as Expired and clear home_id reference
        $db->query("
            UPDATE {$table_prefix}billing_orders
            SET status  = 'Expired',
                home_id = '0'
            WHERE home_id = '{$home_id}'
        ");

        // Mark any open gsp_invoices for this home as Expired
        $db->query("
            UPDATE {$table_prefix}invoices
            SET billing_status = 'Expired'
            WHERE home_id      = {$home_id}
              AND billing_status = 'Invoiced'
        ");

        $db->logger("BILLING-CRON: Step C - DELETED home {$home_id}");

        // Notify user
        if (!empty($srv['users_email'])) {
            $settings = $db->getSettings();
            $subject  = "Server Permanently Deleted - {$home_name} - " . ($panel_settings['panel_name'] ?? 'Game Server Panel');
            $message  = "Your server '{$home_name}' (ID: {$home_id}) has been permanently deleted."
                      . "<br><br>The server expired and was removed after the grace period."
                      . "<br><br>If this was an error, contact us immediately - we may be able to restore from backup."
                      . "<br><br>Thank you for being a customer. We hope to serve you again.";
            if (!mymail($srv['users_email'], $subject, $message, $settings)) {
                $db->logger("BILLING-CRON: Step C - Email FAILED for home {$home_id}");
            }
        }
    }
}

// ======================================================================
// STEP D - Paid invoice safety net
//   If a payment was recorded on a {prefix}invoices row but the
//   server_home was not updated (e.g. race condition at capture time),
//   correct it here so the server is restored to Active.
// ======================================================================
$db->logger("BILLING-CRON: --- Step D: Paid invoice safety-net ---");

$paid_invoices = $db->resultQuery("
    SELECT inv.invoice_id, inv.home_id, inv.rate_type,
           sh.billing_status
    FROM {$table_prefix}invoices inv
    INNER JOIN {$table_prefix}server_homes sh ON sh.home_id = inv.home_id
    WHERE inv.billing_status  = 'Invoiced'
      AND sh.billing_status   = 'Invoiced'
      AND (inv.paid_at IS NOT NULL OR inv.payment_id IS NOT NULL)
    ORDER BY inv.invoice_id ASC
");

if (is_array($paid_invoices)) {
    foreach ($paid_invoices as $inv) {
        $home_id    = intval($inv['home_id']);
        $invoice_id = intval($inv['invoice_id']);
        $rate_type  = $inv['rate_type'] ?? 'monthly';

        // Calculate next_invoice_date based on rate_type
        $period_map        = ['daily' => '+1 day', 'monthly' => '+1 month', 'yearly' => '+1 year'];
        $next_invoice_date = date('Y-m-d H:i:s', strtotime($period_map[$rate_type] ?? '+1 month'));

        $db->query("
            UPDATE {$table_prefix}invoices
            SET billing_status = 'Active'
            WHERE invoice_id = {$invoice_id}
        ");

        $db->query("
            UPDATE {$table_prefix}server_homes
            SET billing_status        = 'Active',
                next_invoice_date     = '" . $db->realEscapeSingle($next_invoice_date) . "',
                server_expiration_date = NULL
            WHERE home_id = {$home_id}
        ");

        $db->logger("BILLING-CRON: Step D - RESTORED home {$home_id} to Active via paid invoice #{$invoice_id}");
    }
}

$db->logger("BILLING-CRON: ===== Lifecycle automation completed at " . date('Y-m-d H:i:s') . " =====");
