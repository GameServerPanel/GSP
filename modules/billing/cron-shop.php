<?php
/*
 *
 * OGP - Open Game Panel
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
 * INVOICE-BASED BILLING SYSTEM
 * =============================
 * 
 * Status Flow for billing_orders:
 * - in-cart: User added to cart, not yet paid
 * - paid: Payment received, awaiting server provisioning
 * - installed: ✅ Active/Running (server provisioned and operational)
 * - suspended: Server stopped, payment overdue (has unpaid invoice)
 * - deleted: Server permanently removed
 * - expired: Order has expired
 * 
 * Invoice Status (billing_invoices):
 * - unpaid: Invoice created, awaiting payment
 * - paid: Invoice paid, service extended
 */

chdir(realpath(dirname(__FILE__))); /* Change to the current file path */
chdir("../.."); /* Base path to ogp web files */
// Report all PHP errors
error_reporting(E_ALL);
// Path definitions
define("CONFIG_FILE","includes/config.inc.php");
//Require
require_once("includes/functions.php");
require_once("includes/helpers.php");
require_once("includes/html_functions.php");
require_once("modules/config_games/server_config_parser.php");
require_once("includes/lib_remote.php");
require_once CONFIG_FILE;
// Connect to the database server and select database.
$db = createDatabaseConnection($db_type, $db_host, $db_user, $db_pass, $db_name, $table_prefix, isset($db_port) ? $db_port : NULL);

$panel_settings = $db->getSettings();
if( isset($panel_settings['time_zone']) && $panel_settings['time_zone'] != "" )
        date_default_timezone_set($panel_settings['time_zone']);

// Date calculations
$today = time();
$invoice_date = strtotime('+ 7 days'); // Create invoice 7 days before expiration
$suspend_date = $today; // Suspend immediately when overdue
$removal_date = strtotime('- 7 days'); // Remove 7 days after suspension
$rundate = date('Y-m-d H:i:s', is_numeric($today) ? (int)$today : strtotime($today));

$db->logger("BILLING-CRON: Server lifecycle automation running at " . $rundate);

// ==================================================================================
// STEP 1: CREATE RENEWAL INVOICES FOR SERVERS EXPIRING IN 7 DAYS
// ==================================================================================
// Find all ACTIVE servers (installed) that expire within 7 days and don't have an unpaid invoice
$upcoming_expirations = $db->resultQuery("
    SELECT o.*, u.users_email, u.users_fname, u.users_lname
    FROM " . $table_prefix . "billing_orders o
    LEFT JOIN " . $table_prefix . "users u ON o.user_id = u.user_id
    WHERE o.status = 'installed'
        AND o.end_date IS NOT NULL
        AND UNIX_TIMESTAMP(o.end_date) < {$invoice_date}
        AND UNIX_TIMESTAMP(o.end_date) > {$today}
        AND NOT EXISTS (
            SELECT 1 FROM " . $table_prefix . "billing_invoices i
            WHERE i.order_id = o.order_id AND i.status = 'unpaid'
        )
");

if (is_array($upcoming_expirations)) {
    foreach ((array)$upcoming_expirations as $order) {
        $user_id = $order['user_id'];
        $order_id = $order['order_id'];
        $home_id = $order['home_id'];
        $customer_name = trim(($order['users_fname'] ?? '') . ' ' . ($order['users_lname'] ?? ''));
        $customer_email = $order['users_email'] ?? '';
        
        // Create renewal invoice
        $invoice_desc = "Renewal for " . $order['home_name'];
        $due_date = date('Y-m-d H:i:s', strtotime($order['end_date']));
        
        $db->query("INSERT INTO " . $table_prefix . "billing_invoices 
            (order_id, user_id, customer_name, customer_email, amount, currency, status, 
             invoice_date, due_date, description, invoice_duration, qty)
            VALUES (
                {$order_id}, 
                {$user_id}, 
                '" . $db->realEscapeSingle($customer_name) . "',
                '" . $db->realEscapeSingle($customer_email) . "',
                " . floatval($order['price']) . ",
                'USD',
                'unpaid',
                NOW(),
                '" . $db->realEscapeSingle($due_date) . "',
                '" . $db->realEscapeSingle($invoice_desc) . "',
                '" . $db->realEscapeSingle($order['invoice_duration']) . "',
                " . intval($order['qty']) . "
            )");
        
        // Mark order status as 'renew' to indicate renewal invoice was created
        $db->query("UPDATE " . $table_prefix . "billing_orders 
                    SET status='renew' 
                    WHERE order_id={$order_id}");
        
        // Send renewal notice email
        $settings = $db->getSettings();
        $subject = "Renewal Invoice for " . $order['home_name'] . " - " . $panel_settings['panel_name'];
        $message = "Your server '" . $order['home_name'] . "' (ID: {$home_id}) will expire on " . 
                   date('F j, Y', strtotime($order['end_date'])) . 
                   ".<br><br>A renewal invoice has been created. Please log in to your account and pay the invoice to continue your service." .
                   "<br><br>Amount Due: $" . number_format($order['price'], 2) .
                   "<br>Due Date: " . date('F j, Y', strtotime($order['end_date'])) .
                   "<br><br>Thank you for your business!<br>";
        
        $mail = mymail($customer_email, $subject, $message, $settings);
        
        $db->logger("BILLING-CRON: Created renewal invoice for order {$order_id}, home {$home_id}");
        
        if (!$mail) {
            $db->logger("BILLING-CRON: Email FAILED - Renewal invoice for order {$order_id}");
        }
    }
}

// ==================================================================================
// STEP 2: SUSPEND SERVERS THAT ARE EXPIRED AND HAVE UNPAID INVOICES
// ==================================================================================
// Find servers that:
// - Are currently installed or renew (active)
// - Have passed their end_date
// - Have at least one unpaid invoice
$servers_to_suspend = $db->resultQuery("
    SELECT DISTINCT o.*, u.users_email
    FROM " . $table_prefix . "billing_orders o
    LEFT JOIN " . $table_prefix . "users u ON o.user_id = u.user_id
    INNER JOIN " . $table_prefix . "billing_invoices i ON o.order_id = i.order_id
    WHERE o.status IN ('installed', 'renew')
        AND o.end_date IS NOT NULL
        AND UNIX_TIMESTAMP(o.end_date) < {$suspend_date}
        AND i.status = 'unpaid'
");

if (is_array($servers_to_suspend)) {
    foreach ((array)$servers_to_suspend as $order) {
        $user_id = $order['user_id'];
        $home_id = $order['home_id'];
        $order_id = $order['order_id'];
        
        // Get home and server info
        $home_info = $db->getGameHomeWithoutMods($home_id);
        if (!$home_info) {
            $db->logger("BILLING-CRON: WARNING - Home {$home_id} not found for order {$order_id}, marking suspended anyway");
            $db->query("UPDATE " . $table_prefix . "billing_orders SET status='suspended' WHERE order_id={$order_id}");
            continue;
        }
        
        $server_info = $db->getRemoteServerById($home_info['remote_server_id']);
        $remote = new OGPRemoteLibrary($server_info['agent_ip'], $server_info['agent_port'], 
                                       $server_info['encryption_key'], $server_info['timeout']);
        
        // Disable FTP
        $ftp_login = isset($home_info['ftp_login']) ? $home_info['ftp_login'] : $home_id;
        $remote->ftp_mgr("userdel", $ftp_login);
        $db->changeFtpStatus('disabled', $home_id);
        
        // Stop the server
        $server_xml = read_server_config(SERVER_CONFIG_LOCATION . "/" . $home_info['home_cfg_file']);
        $control_type = isset($server_xml->control_protocol_type) ? $server_xml->control_protocol_type : "";
        $addresses = $db->getHomeIpPorts($home_id);
        
        foreach ((array)$addresses as $address) {
            $remote->remote_stop_server($home_id, $address['ip'], $address['port'], 
                                       $server_xml->control_protocol, $home_info['control_password'], 
                                       $control_type, $home_info['home_path']);
        }
        
        // Unassign from user
        $db->unassignHomeFrom("user", $user_id, $home_id);
        
        // Update order status
        $db->query("UPDATE " . $table_prefix . "billing_orders SET status='suspended' WHERE order_id={$order_id}");
        
        $db->logger("BILLING-CRON: SUSPENDED server {$home_id} for order {$order_id} due to unpaid invoice");
        
        // Send suspension email
        $settings = $db->getSettings();
        $subject = "Server Suspended - " . $order['home_name'] . " - " . $panel_settings['panel_name'];
        $message = "Your server '" . $order['home_name'] . "' (ID: {$home_id}) has been suspended due to non-payment." .
                   "<br><br>Your server has been stopped and will be permanently deleted in 7 days if payment is not received." .
                   "<br><br>Please log in to your account and pay your outstanding invoice to restore your server." .
                   "<br><br>Thank you.";
        
        $mail = mymail($order['users_email'], $subject, $message, $settings);
        
        if (!$mail) {
            $db->logger("BILLING-CRON: Email FAILED - Suspension notice for order {$order_id}");
        }
    }
}

// ==================================================================================
// STEP 3: DELETE SERVERS THAT HAVE BEEN SUSPENDED FOR 7+ DAYS
// ==================================================================================
// Find servers that:
// - Are currently suspended
// - Have been suspended for at least 7 days (end_date + 7 days has passed)
// - Still have unpaid invoices
$servers_to_delete = $db->resultQuery("
    SELECT DISTINCT o.*, u.users_email
    FROM " . $table_prefix . "billing_orders o
    LEFT JOIN " . $table_prefix . "users u ON o.user_id = u.user_id
    INNER JOIN " . $table_prefix . "billing_invoices i ON o.order_id = i.order_id
    WHERE o.status = 'suspended'
        AND o.end_date IS NOT NULL
        AND UNIX_TIMESTAMP(o.end_date) < {$removal_date}
        AND i.status = 'unpaid'
");

if (is_array($servers_to_delete)) {
    foreach ((array)$servers_to_delete as $order) {
        $user_id = $order['user_id'];
        $home_id = $order['home_id'];
        $order_id = $order['order_id'];
        
        // Get home and server info
        $home_info = $db->getGameHomeWithoutMods($home_id);
        if ($home_info) {
            $server_info = $db->getRemoteServerById($home_info['remote_server_id']);
            $remote = new OGPRemoteLibrary($server_info['agent_ip'], $server_info['agent_port'], 
                                          $server_info['encryption_key'], $server_info['timeout']);
            
            // Remove the game home from db
            $db->deleteGameHome($home_id);
            
            // Remove the game home files from remote server
            $remote->remove_home($home_info['home_path']);
            
            // Drop database and user if they exist (both user_#### and server_#### formats)
            @$db->query("DROP USER 'user_" . $home_id . "'@'%'");
            @$db->query("DROP USER 'user_" . $home_id . "'@'localhost'");
            @$db->query("DROP USER 'server_" . $home_id . "'@'%'");
            @$db->query("DROP USER 'server_" . $home_id . "'@'localhost'");
            @$db->query("DROP DATABASE IF EXISTS user_" . $home_id);
            @$db->query("DROP DATABASE IF EXISTS server_" . $home_id);
        }
        
        // Update order status and clear home_id
        $db->query("UPDATE " . $table_prefix . "billing_orders 
                    SET status='deleted', home_id='0' 
                    WHERE order_id={$order_id}");
        
        // Mark all unpaid invoices for this order as deleted
        $db->query("UPDATE " . $table_prefix . "billing_invoices 
                    SET status='deleted' 
                    WHERE order_id={$order_id} AND status='unpaid'");
        
        $db->logger("BILLING-CRON: DELETED server {$home_id} for order {$order_id} after 7 days suspended");
        
        // Send deletion email
        $settings = $db->getSettings();
        $subject = "Server Permanently Deleted - " . $order['home_name'] . " - " . $panel_settings['panel_name'];
        $message = "Your server '" . $order['home_name'] . "' (ID: {$home_id}) has been permanently deleted." .
                   "<br><br>The server was suspended 7 days ago due to non-payment and has now been removed." .
                   "<br><br>If this was an error and you contact us immediately, we may be able to restore your server from backups." .
                   "<br><br>Thank you for being a customer. We hope to serve you again in the future.";
        
        $mail = mymail($order['users_email'], $subject, $message, $settings);
        
        if (!$mail) {
            $db->logger("BILLING-CRON: Email FAILED - Deletion notice for order {$order_id}");
        }
    }
}

$db->logger("BILLING-CRON: Server lifecycle automation completed");
?>


//THESE SERVERS HAVE REACHED THE DATE FOR INVOICE, END_DATE - 7 (OR WHAT IS IN SETTINGS)
//SET STATUS 'invoiced' MEANING INVOICE SHOULD BE CREATED
//LOOP THROUGH ALL SERVERS WITH STATUS = 'paid' OR 'installed' (ACTIVE) -----------------------------------------------------------
$user_homes = $db->resultQuery( "SELECT *
                                                                 FROM " . $table_prefix .  "billing_orders
                                                                 WHERE status IN ('paid', 'installed') AND end_date <" . $invoice_date); 

if (!is_array($user_homes))
{
}
else
{
        foreach ((array)$user_homes as $user_home)
        {

                // Developer note:
                // In future we may want to change the renewal/invoice strategy so that a
                // new order record is created for the renewal (leaving the original order
                // intact) instead of mutating the existing order's status/end_date.
                // Creating a separate renewal order gives a clearer, immutable purchase
                // history and simplifies auditing. For now this cron job continues to
                // update the existing order (change status/end_date) as implemented
                // below.

                $user_id = $user_home['user_id'];
                $home_id = $user_home['home_id'];
				
               
                // Reset the STATUS 'invoiced' so cart.php will create an invoice
				$db->query( "UPDATE " . $table_prefix . "billing_orders
                                         SET status='invoiced'
                                         WHERE order_id=".$db->realEscapeSingle($user_home['order_id']));

				// SEND EMAIL
					$settings = $db->getSettings();
					$subject = "You have an INVOICE at ". $panel_settings['panel_name'];
				    $email = $db->resultQuery("   SELECT DISTINCT users_email
									   FROM " . $table_prefix .  "users, " . $table_prefix .  "billing_orders
									   WHERE " . $table_prefix .  "users.user_id = $user_id")[0]["users_email"];
				    $message = "Your server with ID ". $home_id . " will expire soon. Please log in and VIEW INVOICES on the Dashboard to renew your server.<br><br><br>~<br>Thanks!<br>";
				    $mail = mymail($email, $subject, $message, $settings);
					//logger
					$db->logger( "AUTO-CLEAN: INVOICE created for server " . $home_id);

				 if (!$mail)
                                                  $db->logger( "AUTO-CLEAN: Email FAILED - Server Invoiced " . $home_id);

				// END EMAIL 
				
				
        }
}

//THESE ARE THE SERVERS THAT HAVE NOT BEEN PAID AND THE END_DATE IS TODAY
//THESE SERVERS GET SUSPENDED
//LOOP THROUGH ALL ORDERS WITH STATUS 'invoiced' OR 'in-cart' OR 'unknown' (INACTIVE OR INVOICED)
$user_homes = $db->resultQuery( "SELECT *
                                                                 FROM " . $table_prefix .  "billing_orders
                                                                 WHERE status IN ('invoiced', 'in-cart', 'unknown') AND end_date < ".$today);

if (!is_array($user_homes))
{
}
else
{
        foreach ((array)$user_homes as $user_home)
        {
                $user_id = $user_home['user_id'];
                $home_id = $user_home['home_id'];
                $home_info = $db->getGameHomeWithoutMods($home_id);
                $server_info = $db->getRemoteServerById($home_info['remote_server_id']);
                $remote = new OGPRemoteLibrary($server_info['agent_ip'], $server_info['agent_port'], $server_info['encryption_key'],$server_info['timeout']);
                $ftp_login = isset($home_info['ftp_login']) ? $home_info['ftp_login'] : $home_id;
                $remote->ftp_mgr("userdel", $ftp_login);
                $db->changeFtpStatus('disabled',$home_id);
                $server_xml = read_server_config(SERVER_CONFIG_LOCATION."/".$home_info['home_cfg_file']);
                if(isset($server_xml->control_protocol_type))$control_type = $server_xml->control_protocol_type; else $control_type = "";
                $addresses = $db->getHomeIpPorts($home_id);
                foreach ((array)$addresses as $address)
                {
                        $remote->remote_stop_server($home_id,$address['ip'],$address['port'],$server_xml->control_protocol,$home_info['control_password'],$control_type,$home_info['home_path']);
                }
                $db->unassignHomeFrom("user", $user_id, $home_id);

                // Reset the invoice end date to 'suspended'
				// User can still RENEW server
                $db->query( "UPDATE " . $table_prefix . "billing_orders
                                         SET status='suspended'
                                         WHERE order_id=".$db->realEscapeSingle($user_home['order_id']));

			//logger
				$db->logger( "AUTO-CLEAN: SUSPENDED server " . $home_id);

 				// SEND EMAIL
					$settings = $db->getSettings();
					$subject = "GameServer Suspended at ". $panel_settings['panel_name'];
				    $email = $db->resultQuery("   SELECT DISTINCT users_email
									   FROM " . $table_prefix .  "users, " . $table_prefix .  "billing_orders
									   WHERE " . $table_prefix .  "users.user_id = $user_id")[0]["users_email"];
				    $message = "Your server with ID ". $home_id . " has expired and has been suspended. Please log in and VIEW INVOICES on the Dashboard to renew your server.<br>~<br>Thanks!<br>";
				    $mail = mymail($email, $subject, $message, $settings);
					if (!$mail)
                                                  $db->logger( "AUTO-CLEAN: Email FAILED - Server Suspended " . $home_id);
				// END EMAIL 

        }
}

// end date = 'suspended' (suspended) and its been suspended for $removal_date days
//set removed servers as 'deleted'
$user_homes = $db->resultQuery( "SELECT *
                                                                 FROM " . $table_prefix .  "billing_orders
                                                                 WHERE status = 'suspended' AND end_date < ".$removal_date );

if (!is_array($user_homes))
{
}
else
{
        foreach ((array)$user_homes as $user_home)
        {
                $user_id = $user_home['user_id'];
                $home_id = $user_home['home_id'];
                $home_info = $db->getGameHomeWithoutMods($home_id);
                $server_info = $db->getRemoteServerById($home_info['remote_server_id']);
                $remote = new OGPRemoteLibrary($server_info['agent_ip'], $server_info['agent_port'], $server_info['encryption_key'],$server_info['timeout']);

                // Remove the game home from db
                $db->deleteGameHome($home_id);

                // Remove the game home files from remote server
                $remote->remove_home($home_info['home_path']);

                

                // Reset the invoice end date
                $db->query( "UPDATE " . $table_prefix . "billing_orders
                                         SET status='deleted'
                                         WHERE order_id=".$db->realEscapeSingle($user_home['order_id']));

                
				// Set order as not installed
                $db->query( "UPDATE " . $table_prefix . "billing_orders
                                         SET home_id=0
                                         WHERE order_id=".$db->realEscapeSingle($user_home['order_id']));
			    
				// Mark all unpaid invoices for this order as deleted
				$db->query("UPDATE " . $table_prefix . "billing_invoices 
							SET status='deleted' 
							WHERE order_id=".$db->realEscapeSingle($user_home['order_id'])." AND status='unpaid'");
			    
				// remove userid and table from database (both user_#### and server_#### formats)
				@$db->query( "DROP USER 'user_" .$home_id ."'@'%'");
				@$db->query( "DROP USER 'user_" .$home_id ."'@'localhost'");
				@$db->query( "DROP USER 'server_" .$home_id ."'@'%'");
				@$db->query( "DROP USER 'server_" .$home_id ."'@'localhost'");
				@$db->query( "DROP DATABASE IF EXISTS user_" .$home_id); 
				@$db->query( "DROP DATABASE IF EXISTS server_" .$home_id); 
										 
				//logger
				$db->logger( "AUTO-CLEAN: DELETED server " . $home_id);

				
				// SEND EMAIL
				    					$settings = $db->getSettings();
					$settings = $db->getSettings();
					$subject = "GameServer DELETED at ". $panel_settings['panel_name'];
				    $email = $db->resultQuery("   SELECT DISTINCT users_email
									   FROM " . $table_prefix .  "users, " . $table_prefix .  "billing_orders
									   WHERE " . $table_prefix .  "users.user_id = $user_id")[0]["users_email"];
				    $message = "Your server with ID ". $home_id . " has been deleted<br><br>You did not renew the service and it was PERMANENTLY REMOVED today. If this was an error, if you contact us immediately we may be able to restore your server.<br>Thanks for being a customer and we hope we can provide a server for you again.<br><br>";
				    $mail = mymail($email, $subject, $message, $settings);
					if (!$mail)
                                                  $db->logger( "AUTO-CLEAN: Email FAILED - Server Deleted " . $home_id);
				// END EMAIL 


        }
}
?>






