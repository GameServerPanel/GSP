<?php
/*
 * billing_integration.php
 *
 * Shared helper for recording admin-created game servers in the billing tables,
 * so they are treated identically to FREE website orders:
 *   billing_invoices (status='paid', amount=0)
 *   billing_orders   (status='installed', price=0)
 *
 * This does NOT re-provision the server — the caller (add_home.php) already
 * created the server via the panel DB layer. We only write the billing ledger
 * entries so admins can track every server in one place and cron-shop.php can
 * manage renewals/suspensions uniformly.
 *
 * Usage (inside exec_ogp_module after $new_home_id is confirmed):
 *   require_once 'billing_integration.php';
 *   admin_register_server_in_billing($db, $user_id, $home_cfg_id,
 *       $rserver_id, $home_name, $max_players, $access_rights, $ftp, $new_home_id);
 */

if (!function_exists('admin_register_server_in_billing')) {

    /**
     * Create billing_invoice + billing_order entries for an admin-provisioned
     * game server so it participates in the normal billing lifecycle.
     *
     * @param OGPDatabase $db           Panel DB object (OGP_DB_PREFIX substitution works)
     * @param int         $user_id      Owner of the server
     * @param int         $home_cfg_id  config_homes primary key (game type)
     * @param int         $rserver_id   remote_server id (stored in billing as "ip")
     * @param string      $home_name    Human-readable server name
     * @param int         $max_players  Slot count
     * @param string      $access_rights Access-rights flags string (e.g. "rgset")
     * @param bool        $ftp          Whether FTP was enabled for this server
     * @param int         $home_id      server_homes.home_id of the already-created server
     *
     * @return int|false  New billing_orders.order_id on success, FALSE on error
     */
    function admin_register_server_in_billing(
        $db,
        $user_id,
        $home_cfg_id,
        $rserver_id,
        $home_name,
        $max_players,
        $access_rights,
        $ftp,
        $home_id
    ) {
        // ------------------------------------------------------------------ //
        // 1. Resolve service_id: find an existing billing_service matching   //
        //    this game type.  Fall back to 0 (no catalogue entry) if none.   //
        // ------------------------------------------------------------------ //
        $service_id = 0;
        $services = $db->resultQuery(
            "SELECT service_id FROM OGP_DB_PREFIXbilling_services
             WHERE home_cfg_id = " . intval($home_cfg_id) . "
             AND enabled = 1
             LIMIT 1"
        );
        if (!empty($services[0]['service_id'])) {
            $service_id = intval($services[0]['service_id']);
        }

        // ------------------------------------------------------------------ //
        // 2. Resolve owner's name & email for the invoice record.            //
        // ------------------------------------------------------------------ //
        $customer_name  = '';
        $customer_email = '';
        $user_row = $db->getUserById(intval($user_id));
        if (!empty($user_row)) {
            $customer_name  = trim(
                ($user_row['users_fname'] ?? '') . ' ' . ($user_row['users_lname'] ?? '')
            );
            $customer_email = $user_row['users_email'] ?? '';
        }

        $now       = date('Y-m-d H:i:s');
        $end_date  = date('Y-m-d H:i:s', strtotime('+1 year'));
        $ftp_flag  = $ftp ? 'enabled' : 'disabled';

        // ------------------------------------------------------------------ //
        // 3. Insert billing_invoice (amount=0, already "paid").              //
        // ------------------------------------------------------------------ //
        $invoice_fields = array(
            'order_id'                 => 0,
            'user_id'                  => intval($user_id),
            'service_id'               => $service_id,
            'home_name'                => $home_name,
            'ip'                       => intval($rserver_id),
            'max_players'              => intval($max_players),
            'remote_control_password'  => '',
            'ftp_password'             => '',
            'customer_name'            => $customer_name,
            'customer_email'           => $customer_email,
            'amount'                   => '0.00',
            'discount_amount'          => '0.00',
            'currency'                 => 'USD',
            'status'                   => 'paid',
            'invoice_date'             => $now,
            'due_date'                 => $now,
            'paid_date'                => $now,
            'payment_txid'             => 'admin-created',
            'payment_method'           => 'admin',
            'description'              => 'Admin-created server: ' . $home_name,
            'invoice_duration'         => 'year',
            'qty'                      => 1,
        );

        $invoice_id = $db->resultInsertId('billing_invoices', $invoice_fields);
        if ($invoice_id === FALSE) {
            return FALSE;
        }

        // ------------------------------------------------------------------ //
        // 4. Insert billing_order (status='installed', already provisioned). //
        // ------------------------------------------------------------------ //
        $order_fields = array(
            'user_id'                  => intval($user_id),
            'service_id'               => $service_id,
            'home_name'                => $home_name,
            'ip'                       => intval($rserver_id),
            'qty'                      => 1,
            'invoice_duration'         => 'year',
            'max_players'              => intval($max_players),
            'price'                    => '0.00',
            'discount_amount'          => '0.00',
            'remote_control_password'  => '',
            'ftp_password'             => '',
            'home_id'                  => intval($home_id),
            'status'                   => 'installed',
            'order_date'               => $now,
            'end_date'                 => $end_date,
            'payment_txid'             => 'admin-created',
            'paid_ts'                  => $now,
            'coupon_id'                => 0,
        );

        $order_id = $db->resultInsertId('billing_orders', $order_fields);
        if ($order_id === FALSE) {
            return FALSE;
        }

        // ------------------------------------------------------------------ //
        // 5. Link the invoice back to the new order.                         //
        // ------------------------------------------------------------------ //
        $db->query(
            "UPDATE OGP_DB_PREFIXbilling_invoices
             SET order_id = " . intval($order_id) . "
             WHERE invoice_id = " . intval($invoice_id)
        );

        return $order_id;
    }
}
