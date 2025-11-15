<?php
/*
 * Panel-side page to view user's orders and provision paid servers
 * This page displays orders that have been paid but not yet installed
 */

function exec_ogp_module()
{
	global $db, $view;
	$user_id = $_SESSION['user_id'];
	$isAdmin = $db->isAdmin($user_id);
	
	echo "<h2>My Server Orders</h2>";
	
	// Get paid but not installed orders for this user
	if ($isAdmin) {
		$orders = $db->resultQuery("SELECT o.*, s.service_name, u.users_login 
									FROM OGP_DB_PREFIXbilling_orders o
									LEFT JOIN OGP_DB_PREFIXbilling_services s ON o.service_id = s.service_id
									LEFT JOIN OGP_DB_PREFIXusers u ON o.user_id = u.user_id
									WHERE o.status IN ('paid')
									ORDER BY o.order_date DESC");
	} else {
		$orders = $db->resultQuery("SELECT o.*, s.service_name 
									FROM OGP_DB_PREFIXbilling_orders o
									LEFT JOIN OGP_DB_PREFIXbilling_services s ON o.service_id = s.service_id
									WHERE o.user_id = ".$db->realEscapeSingle($user_id)."
									AND o.status IN ('paid')
									ORDER BY o.order_date DESC");
	}
	
	if (empty($orders)) {
		echo "<div class='info'><p>".get_lang('no_servers_to_provision')."</p></div>";
		echo "<p><a href='home.php?m=gamemanager&p=game_monitor' class='btn'>".get_lang('view_my_servers')."</a></p>";
		return;
	}
	
	echo "<div class='info'>";
	echo "<p>The following servers have been paid for and are ready to be provisioned. Click 'Provision Server' to install them.</p>";
	echo "</div>";
	
	echo "<table class='tablesorter'>";
	echo "<thead><tr>";
	echo "<th>Order ID</th>";
	echo "<th>Server Name</th>";
	echo "<th>Game Service</th>";
	echo "<th>Max Players</th>";
	echo "<th>Price</th>";
	echo "<th>Duration</th>";
	echo "<th>Order Date</th>";
	if ($isAdmin) echo "<th>Username</th>";
	echo "<th>Action</th>";
	echo "</tr></thead><tbody>";
	
	foreach ($orders as $order) {
		echo "<tr>";
		echo "<td>".$order['order_id']."</td>";
		echo "<td>".$order['home_name']."</td>";
		echo "<td>".$order['service_name']."</td>";
		echo "<td>".$order['max_players']."</td>";
		echo "<td>$".number_format($order['price'], 2)."</td>";
		echo "<td>".$order['qty']." ".$order['invoice_duration']."(s)</td>";
		echo "<td>".date('Y-m-d H:i', strtotime($order['order_date']))."</td>";
		if ($isAdmin) echo "<td>".$order['users_login']."</td>";
		echo "<td>";
		echo "<form method='post' action='home.php?m=billing&p=provision_servers'>";
		echo "<input type='hidden' name='order_id' value='".$order['order_id']."'>";
		echo "<input type='hidden' name='provision_single' value='1'>";
		echo "<button type='submit' class='btn'>Provision Server</button>";
		echo "</form>";
		echo "</td>";
		echo "</tr>";
	}
	
	echo "</tbody></table>";
	
	// Provision all button
	if (count($orders) > 1) {
		echo "<div style='margin-top: 20px;'>";
		echo "<form method='post' action='home.php?m=billing&p=provision_servers'>";
		echo "<input type='hidden' name='provision_all' value='1'>";
		echo "<button type='submit' class='btn btn-primary'>Provision All My Servers (".count($orders).")</button>";
		echo "</form>";
		echo "</div>";
	}
}
?>
