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
		
		foreach ($selected as $order_id) {
			$order_id = $db->realEscapeSingle($order_id);
			
			switch ($action) {
				case 'provision':
					// Redirect to provision page for each order
					header("Location: home.php?m=billing&p=provision_servers&order_id=".$order_id);
					exit;
					break;
				case 'suspend':
					$db->query("UPDATE OGP_DB_PREFIXbilling_orders SET status='suspended' WHERE order_id=".$order_id);
					break;
				case 'activate':
					$db->query("UPDATE OGP_DB_PREFIXbilling_orders SET status='paid' WHERE order_id=".$order_id);
					break;
				case 'delete':
					$db->query("UPDATE OGP_DB_PREFIXbilling_orders SET status='deleted' WHERE order_id=".$order_id);
					break;
			}
		}
		
		echo "<div class='success'><p>Bulk action completed for ".count($selected)." order(s).</p></div>";
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
	echo "<option value='in-cart' ".($status_filter == 'in-cart' ? 'selected' : '').">In Cart</option>";
	echo "<option value='paid' ".($status_filter == 'paid' ? 'selected' : '').">Paid (Awaiting Provision)</option>";
	echo "<option value='installed' ".($status_filter == 'installed' ? 'selected' : '').">Installed (Active)</option>";
	echo "<option value='invoiced' ".($status_filter == 'invoiced' ? 'selected' : '').">Renewal Invoiced</option>";
	echo "<option value='suspended' ".($status_filter == 'suspended' ? 'selected' : '').">Suspended</option>";
	echo "<option value='deleted' ".($status_filter == 'deleted' ? 'selected' : '').">Deleted</option>";
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
	echo "<option value='activate'>Set to Paid (Activate)</option>";
	echo "<option value='suspend'>Suspend</option>";
	echo "<option value='delete'>Delete</option>";
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
	
	foreach ($orders as $order) {
		$status_class = '';
		switch ($order['status']) {
			case 'paid': $status_class = 'label-warning'; break;
			case 'installed': $status_class = 'label-success'; break;
			case 'suspended': $status_class = 'label-danger'; break;
			case 'deleted': $status_class = 'label-default'; break;
			default: $status_class = 'label-info';
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
		
		if ($order['status'] == 'paid') {
			echo "<a href='home.php?m=billing&p=provision_servers&order_id=".$order['order_id']."' class='btn btn-sm'>Provision</a> ";
		}
		
		if ($order['status'] == 'installed' && $order['home_id']) {
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
	
	foreach ($stats as $stat) {
		echo "<tr>";
		echo "<td>".$stat['status']."</td>";
		echo "<td>".$stat['count']."</td>";
		echo "<td>$".number_format($stat['total'], 2)."</td>";
		echo "</tr>";
	}
	
	echo "</tbody></table>";
	echo "</div>";
}
?>
