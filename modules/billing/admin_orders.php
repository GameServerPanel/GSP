<?php
/*
 * Admin page to manage all billing orders
 * Allows admins to view, provision, suspend, and delete orders
 */

function exec_ogp_module()
{
	global $db, $view, $table_prefix;
	$db_prefix = isset($table_prefix) ? $table_prefix : '';
	$user_id = $_SESSION['user_id'];
	$isAdmin = $db->isAdmin($user_id);
	
	if (!$isAdmin) {
		echo "<div class='failure'><p>Access Denied: Admin privileges required.</p></div>";
		return;
	}

	// -------------------------------------------------------------------
	// Handle "Retry Provisioning" for a single order
	// -------------------------------------------------------------------
	if (isset($_POST['retry_provision_order']) && !empty($_POST['retry_order_id'])) {
		$retry_id = intval($_POST['retry_order_id']);
		require_once __DIR__ . '/create_servers.php';
		$retryResult = billing_invoke_provision([
			'order_ids' => [$retry_id],
			'user_id'   => $user_id,
			'is_admin'  => true,
		]);
		if (!empty($retryResult['provisioned_count'])) {
			echo "<div class='success'><p>Retry provisioning succeeded for order #{$retry_id}.</p></div>";
		} elseif (!empty($retryResult['output'])) {
			echo "<div class='failure'><p>Retry provisioning for order #{$retry_id} did not succeed. See details below.</p>"
			   . "<pre>" . htmlspecialchars($retryResult['output']) . "</pre></div>";
		} else {
			echo "<div class='failure'><p>Retry provisioning for order #{$retry_id}: no result returned. Check server logs.</p></div>";
		}
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
					$db->query("UPDATE `{$db_prefix}billing_orders` SET status='Expired' WHERE order_id=".$order_id);
					break;
				case 'activate':
					$db->query("UPDATE `{$db_prefix}billing_orders` SET status='Active' WHERE order_id=".$order_id);
					break;
				case 'invoice':
					$db->query("UPDATE `{$db_prefix}billing_orders` SET status='Invoiced' WHERE order_id=".$order_id);
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
			  FROM `{$db_prefix}billing_orders` o
			  LEFT JOIN `{$db_prefix}billing_services` s ON o.service_id = s.service_id
			  LEFT JOIN `{$db_prefix}users` u ON o.user_id = u.user_id
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

	// Pre-fetch provisioning error counts per order for display
	$errorCounts = [];
	$errCountRows = $db->resultQuery(
		"SELECT billing_order_id, COUNT(*) AS cnt
		   FROM `{$db_prefix}billing_provisioning_errors`
		  GROUP BY billing_order_id"
	);
	foreach ((array)$errCountRows as $ecr) {
		$errorCounts[intval($ecr['billing_order_id'])] = intval($ecr['cnt']);
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

		$oid = intval($order['order_id']);
		$errCount = $errorCounts[$oid] ?? 0;
		
		echo "<tr>";
		echo "<td><input type='checkbox' name='selected_orders[]' value='".$oid."'></td>";
		echo "<td>".$oid."</td>";
		echo "<td>".htmlspecialchars($order['users_login'] ?? '')."<br><small>".htmlspecialchars($order['users_email'] ?? '')."</small></td>";
		echo "<td>".htmlspecialchars($order['home_name'] ?? '')."</td>";
		echo "<td>".htmlspecialchars($order['service_name'] ?? '')."</td>";
		echo "<td>".$order['max_players']."</td>";
		echo "<td>$".number_format($order['price'], 2)."</td>";
		echo "<td>".$order['qty']." ".$order['invoice_duration']."(s)</td>";
		echo "<td><span class='label ".$status_class."'>".$order['status']."</span>";
		if ($errCount > 0) {
			echo " <span class='label label-warning' title='Provisioning errors'>" . $errCount . " error(s)</span>";
		}
		echo "</td>";
		echo "<td>".date('Y-m-d H:i', strtotime($order['order_date']))."</td>";
		echo "<td>".($order['end_date'] ? date('Y-m-d', strtotime($order['end_date'])) : 'N/A')."</td>";
		echo "<td>".($order['home_id'] ? $order['home_id'] : 'N/A')."</td>";
		echo "<td>";
		
		if ($order['status'] == 'Active' && !$order['home_id']) {
			echo "<a href='home.php?m=billing&p=provision_servers&order_id=".$oid."' class='btn btn-sm'>Provision</a> ";
			// Retry provisioning button (inline POST form)
			echo "<form method='post' action='home.php?m=billing&p=admin_orders' style='display:inline;'>";
			echo "<input type='hidden' name='retry_provision_order' value='1'>";
			echo "<input type='hidden' name='retry_order_id' value='".$oid."'>";
			echo "<button type='submit' class='btn btn-sm btn-warning'>Retry Provisioning</button>";
			echo "</form> ";
		}
		
		if ($order['status'] == 'Active' && $order['home_id']) {
			echo "<a href='home.php?m=gamemanager&p=game_monitor&home_id-mod_id-ip=".$order['home_id']."' class='btn btn-sm'>View Server</a> ";
		}

		if ($errCount > 0) {
			echo "<a href='#' onclick='toggleErrors(".$oid.")' class='btn btn-sm btn-danger'>Errors</a> ";
		}

		echo "<a href='#' onclick='viewOrder(".$oid.")' class='btn btn-sm'>Details</a>";
		echo "</td>";
		echo "</tr>";

		// Collapsible provisioning error rows
		if ($errCount > 0) {
			echo "<tr id='errors_".$oid."' style='display:none;background:#fff8f8;'>";
			echo "<td colspan='13'>";
			$errRows = $db->resultQuery(
				"SELECT * FROM `{$db_prefix}billing_provisioning_errors`
				  WHERE billing_order_id=" . $oid . "
				  ORDER BY created_at DESC LIMIT 20"
			);
			if (!empty($errRows)) {
				echo "<table style='width:100%;font-size:0.9em;'>";
				echo "<thead><tr><th>Time</th><th>Remote Srv</th><th>IP ID</th><th>Port</th><th>Mod</th><th>Message</th></tr></thead><tbody>";
				foreach ($errRows as $er) {
					echo "<tr>";
					echo "<td>".htmlspecialchars($er['created_at'])."</td>";
					echo "<td>".intval($er['remote_server_id'])."</td>";
					echo "<td>".intval($er['ip_id'])."</td>";
					echo "<td>".intval($er['attempted_port'])."</td>";
					echo "<td>".intval($er['mod_cfg_id'])."</td>";
					echo "<td>".htmlspecialchars($er['failure_message'])."</td>";
					echo "</tr>";
				}
				echo "</tbody></table>";
			}
			echo "</td></tr>";
		}
	}
	
	echo "</tbody></table>";
	echo "</form>";
	
	// JavaScript for checkbox toggle and error panel
	echo "<script>
	function toggleAll(checkbox) {
		var checkboxes = document.getElementsByName('selected_orders[]');
		for (var i = 0; i < checkboxes.length; i++) {
			checkboxes[i].checked = checkbox.checked;
		}
	}

	function toggleErrors(orderId) {
		var row = document.getElementById('errors_' + orderId);
		if (row) {
			row.style.display = (row.style.display === 'none') ? 'table-row' : 'none';
		}
		return false;
	}
	
	function viewOrder(orderId) {
		alert('Order details for #' + orderId + '\\n\\nFull order details feature coming soon.');
		return false;
	}
	</script>";
	
	// Summary stats
	$stats = $db->resultQuery(
		"SELECT status, COUNT(*) as count, SUM(price) as total 
		   FROM `{$db_prefix}billing_orders` 
		  GROUP BY status"
	);
	
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
		   FROM `{$db_prefix}billing_orders` o
		   LEFT JOIN `{$db_prefix}server_homes` sh ON sh.home_id = o.home_id
		  WHERE o.home_id != '0'
		    AND o.home_id != ''
		    AND sh.home_id IS NULL
		  ORDER BY o.order_id ASC"
	);

	echo "<div style='margin-top: 30px;'>";
	echo "<h3>Orphaned home_id Diagnostics</h3>";
	echo "<p style='color:#666;'>Billing orders that reference a <code>home_id</code> which no longer exists in <code>server_homes</code>. ";
	echo "These orders will not show an expiration date on the game monitor. ";
	echo "Reset <code>home_id</code> to <code>0</code> and use the Retry Provisioning button to re-provision them. ";
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

	// Recent provisioning errors (all orders) ————————————————————————————
	$recentErrors = $db->resultQuery(
		"SELECT e.*, o.home_name, u.users_login
		   FROM `{$db_prefix}billing_provisioning_errors` e
		   LEFT JOIN `{$db_prefix}billing_orders` o ON o.order_id = e.billing_order_id
		   LEFT JOIN `{$db_prefix}users` u ON u.user_id = e.user_id
		  ORDER BY e.created_at DESC
		  LIMIT 50"
	);

	echo "<div style='margin-top: 30px;'>";
	echo "<h3>Recent Provisioning Errors</h3>";
	if (empty($recentErrors)) {
		echo "<p style='color:green;'>&#10003; No provisioning errors recorded.</p>";
	} else {
		echo "<table class='tablesorter' style='width:100%;'>";
		echo "<thead><tr><th>Time</th><th>Order ID</th><th>User</th><th>Server Name</th><th>Remote Srv</th><th>IP ID</th><th>Port</th><th>Mod</th><th>Message</th></tr></thead><tbody>";
		foreach ($recentErrors as $er) {
			echo "<tr>";
			echo "<td>".htmlspecialchars($er['created_at'])."</td>";
			echo "<td>".intval($er['billing_order_id'])."</td>";
			echo "<td>".htmlspecialchars($er['users_login'] ?? ('uid:'.intval($er['user_id'])))."</td>";
			echo "<td>".htmlspecialchars($er['home_name'] ?? '')."</td>";
			echo "<td>".intval($er['remote_server_id'])."</td>";
			echo "<td>".intval($er['ip_id'])."</td>";
			echo "<td>".intval($er['attempted_port'])."</td>";
			echo "<td>".intval($er['mod_cfg_id'])."</td>";
			echo "<td>".htmlspecialchars($er['failure_message'])."</td>";
			echo "</tr>";
		}
		echo "</tbody></table>";
	}
	echo "</div>";
}
?>
