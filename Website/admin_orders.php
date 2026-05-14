<?php
/*
 * Admin page to manage all billing orders
 * Allows admins to view, provision, suspend, and delete orders
 */

function exec_ogp_module()
{
	global $db, $view;
	$user_id = $_SESSION['user_id'];
	$isAdmin = $db->isAdmin($user_id);
	
	if (!$isAdmin) {
		echo "<div class='failure'><p>Access Denied: Admin privileges required.</p></div>";
		return;
	}
	
	// Handle bulk actions
	if (isset($_POST['bulk_action']) && isset($_POST['selected_orders'])) {
		$action = $_POST['bulk_action'];
		$selected = $_POST['selected_orders'];
		
		foreach ((array)$selected as $order_id) {
			$order_id = $db->realEscapeSingle($order_id);
			
			switch ($action) {
				case 'provision':
					// Redirect to provision page for each order
					header("Location: home.php?m=billing&p=provision_servers&order_id=".$order_id);
					exit;
					break;
				case 'expire':
					$db->query("UPDATE OGP_DB_PREFIXbilling_orders SET status='Expired' WHERE order_id=".$order_id);
					break;
				case 'activate':
					$db->query("UPDATE OGP_DB_PREFIXbilling_orders SET status='Active' WHERE order_id=".$order_id);
					break;
				case 'invoice':
					$db->query("UPDATE OGP_DB_PREFIXbilling_orders SET status='Invoiced' WHERE order_id=".$order_id);
					break;
			}
		}
		
		echo "<div class='success'><p>Bulk action completed for ".count((array)$selected)." order(s).</p></div>";
	}
	
	// Get filter parameters
	$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
	$search = isset($_GET['search']) ? $_GET['search'] : '';
	
	echo "<h2>Manage All Orders (Admin)</h2>";
	
	// Filter form
	echo "<form method='get' action='home.php' style='margin-bottom: 20px;'>";
	echo "<input type='hidden' name='m' value='billing'>";
	echo "<input type='hidden' name='p' value='admin_orders'>";
	echo "Status: <select name='status' onchange='this.form.submit()'>";
	echo "<option value='all' ".($status_filter == 'all' ? 'selected' : '').">All Orders</option>";
	echo "<option value='Active' ".($status_filter == 'Active' ? 'selected' : '').">Active</option>";
	echo "<option value='Invoiced' ".($status_filter == 'Invoiced' ? 'selected' : '').">Invoiced</option>";
	echo "<option value='Expired' ".($status_filter == 'Expired' ? 'selected' : '').">Expired</option>";
	echo "</select> ";
	echo "Search: <input type='text' name='search' value='".$search."' placeholder='Order ID, username, server name...'> ";
	echo "<button type='submit' class='btn'>Filter</button>";
	echo "</form>";
	
	// Build query
	$query = "SELECT o.*, s.service_name, u.users_login, u.users_email 
			  FROM OGP_DB_PREFIXbilling_orders o
			  LEFT JOIN OGP_DB_PREFIXbilling_services s ON o.service_id = s.service_id
			  LEFT JOIN OGP_DB_PREFIXusers u ON o.user_id = u.user_id
			  WHERE 1=1";
	
	if ($status_filter != 'all') {
		$query .= " AND o.status = '".$db->realEscapeSingle($status_filter)."'";
	}
	
	if (!empty($search)) {
		$search_escaped = $db->realEscapeSingle($search);
		$query .= " AND (o.order_id LIKE '%".$search_escaped."%' 
					OR o.home_name LIKE '%".$search_escaped."%'
					OR u.users_login LIKE '%".$search_escaped."%'
					OR u.users_email LIKE '%".$search_escaped."%')";
	}
	
	$query .= " ORDER BY o.order_date DESC";
	
	$orders = $db->resultQuery($query);
	
	if (empty($orders)) {
		echo "<div class='info'><p>No orders found matching your filters.</p></div>";
		return;
	}
	
	echo "<form method='post' action='home.php?m=billing&p=admin_orders'>";
	echo "<div style='margin-bottom: 10px;'>";
	echo "With selected: ";
	echo "<select name='bulk_action'>";
	echo "<option value=''>-- Choose Action --</option>";
	echo "<option value='provision'>Provision Servers</option>";
	echo "<option value='activate'>Set Active</option>";
	echo "<option value='invoice'>Set Invoiced</option>";
	echo "<option value='expire'>Set Expired</option>";
	echo "</select> ";
	echo "<button type='submit' class='btn'>Apply</button>";
	echo "</div>";
	
	echo "<table class='tablesorter'>";
	echo "<thead><tr>";
	echo "<th><input type='checkbox' id='select_all' onclick='toggleAll(this)'></th>";
	echo "<th>Order ID</th>";
	echo "<th>Username</th>";
	echo "<th>Server Name</th>";
	echo "<th>Game Service</th>";
	echo "<th>Players</th>";
	echo "<th>Price</th>";
	echo "<th>Duration</th>";
	echo "<th>Status</th>";
	echo "<th>Order Date</th>";
	echo "<th>End Date</th>";
	echo "<th>Home ID</th>";
	echo "<th>Actions</th>";
	echo "</tr></thead><tbody>";
	
	foreach ((array)$orders as $order) {
		$status_class = '';
		switch ($order['status']) {
			case 'Active':   $status_class = 'label-success'; break;
			case 'Invoiced': $status_class = 'label-warning'; break;
			case 'Expired':  $status_class = 'label-danger';  break;
			default:         $status_class = 'label-info';
		}
		
		echo "<tr>";
		echo "<td><input type='checkbox' name='selected_orders[]' value='".$order['order_id']."'></td>";
		echo "<td>".$order['order_id']."</td>";
		echo "<td>".$order['users_login']."<br><small>".$order['users_email']."</small></td>";
		echo "<td>".$order['home_name']."</td>";
		echo "<td>".$order['service_name']."</td>";
		echo "<td>".$order['max_players']."</td>";
		echo "<td>$".number_format($order['price'], 2)."</td>";
		echo "<td>".$order['qty']." ".$order['invoice_duration']."(s)</td>";
		echo "<td><span class='label ".$status_class."'>".$order['status']."</span></td>";
		echo "<td>".date('Y-m-d H:i', strtotime($order['order_date']))."</td>";
		echo "<td>".($order['end_date'] ? date('Y-m-d', strtotime($order['end_date'])) : 'N/A')."</td>";
		echo "<td>".($order['home_id'] ? $order['home_id'] : 'N/A')."</td>";
		echo "<td>";
		
		if ($order['status'] == 'Active' && !$order['home_id']) {
			echo "<a href='home.php?m=billing&p=provision_servers&order_id=".$order['order_id']."' class='btn btn-sm'>Provision</a> ";
		}
		
		if ($order['status'] == 'Active' && $order['home_id']) {
			echo "<a href='home.php?m=gamemanager&p=game_monitor&home_id-mod_id-ip=".$order['home_id']."' class='btn btn-sm'>View Server</a> ";
		}
		
		echo "<a href='#' onclick='viewOrder(".$order['order_id'].")' class='btn btn-sm'>Details</a>";
		echo "</td>";
		echo "</tr>";
	}
	
	echo "</tbody></table>";
	echo "</form>";
	
	// JavaScript for checkbox toggle
	echo "<script>
	function toggleAll(checkbox) {
		var checkboxes = document.getElementsByName('selected_orders[]');
		for (var i = 0; i < checkboxes.length; i++) {
			checkboxes[i].checked = checkbox.checked;
		}
	}
	
	function viewOrder(orderId) {
		alert('Order details for #' + orderId + '\\n\\nFull order details feature coming soon.');
		return false;
	}
	</script>";
	
	// Summary stats
	$stats = $db->resultQuery("SELECT status, COUNT(*) as count, SUM(price) as total 
							   FROM OGP_DB_PREFIXbilling_orders 
							   GROUP BY status");
	
	echo "<div style='margin-top: 30px;'>";
	echo "<h3>Order Statistics</h3>";
	echo "<table class='tablesorter' style='width: auto;'>";
	echo "<thead><tr><th>Status</th><th>Count</th><th>Total Value</th></tr></thead><tbody>";
	
	foreach ((array)$stats as $stat) {
		echo "<tr>";
		echo "<td>".$stat['status']."</td>";
		echo "<td>".$stat['count']."</td>";
		echo "<td>$".number_format($stat['total'], 2)."</td>";
		echo "</tr>";
	}
	
	echo "</tbody></table>";
	echo "</div>";

	// Orphaned home_id diagnostics —————————————————————————————————————————
	// Find billing_orders rows where home_id != 0 but no matching gsp_server_homes
	// record exists. These indicate provisioning failures or stale data, and they
	// are the reason the game monitor may show "No expiration date found".
	$orphans = $db->resultQuery(
		"SELECT o.order_id, o.user_id, o.home_name, o.home_id, o.status, o.end_date
		   FROM OGP_DB_PREFIXbilling_orders o
		   LEFT JOIN OGP_DB_PREFIXserver_homes sh ON sh.home_id = o.home_id
		  WHERE o.home_id != '0'
		    AND o.home_id != ''
		    AND sh.home_id IS NULL
		  ORDER BY o.order_id ASC"
	);

	echo "<div style='margin-top: 30px;'>";
	echo "<h3>Orphaned home_id Diagnostics</h3>";
	echo "<p style='color:#666;'>Billing orders that reference a <code>home_id</code> which no longer exists in <code>gsp_server_homes</code>. ";
	echo "These orders will not show an expiration date on the game monitor. ";
	echo "Reset <code>home_id</code> to <code>0</code> or re-provision these orders to fix them. ";
	echo "Run <code>normalize_billing_order_status.sql</code> to standardize any legacy status values.</p>";

	if (empty($orphans)) {
		echo "<p style='color:green;'>&#10003; No orphaned billing orders found.</p>";
	} else {
		echo "<table class='tablesorter' style='width:100%;'>";
		echo "<thead><tr><th>Order ID</th><th>User ID</th><th>Server Name</th><th>home_id (missing)</th><th>Status</th><th>End Date</th></tr></thead><tbody>";
		foreach ($orphans as $row) {
			echo "<tr>";
			echo "<td>".intval($row['order_id'])."</td>";
			echo "<td>".intval($row['user_id'])."</td>";
			echo "<td>".htmlspecialchars($row['home_name'] ?? '')."</td>";
			echo "<td style='color:red;'>".htmlspecialchars($row['home_id'] ?? '')."</td>";
			echo "<td>".htmlspecialchars($row['status'] ?? '')."</td>";
			echo "<td>".htmlspecialchars($row['end_date'] ?? 'NULL')."</td>";
			echo "</tr>";
		}
		echo "</tbody></table>";
	}
	echo "</div>";
}
?>
