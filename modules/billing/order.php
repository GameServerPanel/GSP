<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Server - GameServers.World</title>
</head>
<body>
<?php

/*
This is the actual "order gameserver" page.  There is a page that displays all the possible game servers we can rent.  This page displays the options
for a single specific game server and has the "Add to Cart" button.  
The gameserver selected is passed from the gameserverss page by a Post of the ServiceID 
When the user clicks the "Add to Cart" button, the next page to load is "add_to_cart.php" which creates all the DB entries.
All the configuration info is passed to the add_to_cart.php in hidden fields 
 
In our website, we are setting "post" pages with a "Tag". The first tag in our post should be the service ID from the services table
There are other methods that might be better to get the info.  But all we need is the "service_ID" in the "{$table_prefix}billing_services" table
This method means we can use one code block in every game page and fill in the data dynamically.   
*/

// Require login for ordering
require_once(__DIR__ . '/includes/login_required.php');

// Include billing bootstrap (loads config and DB helper)
require_once(__DIR__ . '/bootstrap.php');

// Variables from config.inc.php (helps IDEs understand scope)
/** @var string $db_host Database host */
/** @var string $db_user Database user */
/** @var string $db_pass Database password */
/** @var string $db_name Database name */
/** @var string $table_prefix Table prefix for database tables */

// Create database connection
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name, isset($db_port) ? (int)$db_port : null);
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Include top bar and menu
include(__DIR__ . '/includes/top.php');
include(__DIR__ . '/includes/menu.php');

	
	if (isset($_POST['save']) AND !empty($_POST['description']))
	{
		$new_description = str_replace("\\r\\n", "<br>", $_POST['description']);
		$service = $_POST['service_id'];
		
		$change_description = "UPDATE {$table_prefix}billing_services
						       SET description ='".$new_description."'
						       WHERE service_id=".$service;
		$save = $db->query($change_description);
	}
	?>
	
	


	
	<!-- ------------------------------------------------------------------------------
THIS IS WHAT WE DISPLAY ON THE SHOP PAGE AT THE TOP
-->

	<?php 
	// Shop Form
	if(intval($_REQUEST['service_id']) !==0) $where_service_id = " WHERE enabled = 1 and service_id=".intval($_REQUEST['service_id']); else $where_service_id = " where enabled = 1";
	$qry_services = "SELECT * FROM {$table_prefix}billing_services ".$where_service_id ." ORDER BY service_name";
	$services = $db->query($qry_services);
	
	if (isset($_REQUEST['service_id']) && $services === false) {
		echo "<meta http-equiv='refresh' content='1'>";
		return;
	}
	
	foreach ((array)$services as $key => $row) {
		$service_ids[$key] = $row['service_id'];
		$home_cfg_id[$key] = $row['home_cfg_id'];
		$mod_cfg_id[$key] = $row['mod_cfg_id'];
	$service_name[$key] = $row['service_name'];
		$remote_server_id[$key] = $row['remote_server_id'];
		$slot_max_qty[$key] = $row['slot_max_qty'];
		$slot_min_qty[$key] = $row['slot_min_qty'];
		$price_daily[$key] = $row['price_daily'];
		$price_monthly[$key] = $row['price_monthly'];
		$price_year[$key] = $row['price_year'];
		$description[$key] = $row['description'];
		$img_url[$key] = $row['img_url'];
		$ftp[$key] = $row['ftp'];
		$install_method[$key] = $row['install_method'];
		$manual_url[$key] = $row['manual_url'];
	$access_rights_list[$key] = $row['access_rights'];
	}
	
?>	
<div class="clearfix">
	<?php
	foreach ((array)$services as $row)
	{ 
	if(!isset($_REQUEST['service_id']))
		{
	?>
		<div class="float-left p-30-20">
  
  
  
<img src="<?php echo htmlspecialchars(billing_image_url((string)($row['img_url'] ?? '')), ENT_QUOTES, 'UTF-8');?>" width="460" height="225" >
<br>
<?php echo htmlspecialchars((string)$row['service_name'], ENT_QUOTES, 'UTF-8');?>
<br>
<?php 
if ($row['price_monthly'] == 0.0) {
	echo "FREE";
} else {
	echo "$" .  number_format(floatval($row['price_monthly']),2). " Monthly";
}
?>
<br>
<form action="<?php echo $row['description'];?>" method="POST">
<input name="service_id" type="hidden" value="<?php echo $row['service_id'];?>" />

<input  name="order_server" type="submit" value="Server Info">
</form>
<form action="" method="POST">
<input name="service_id" type="hidden" value="<?php echo $row['service_id'];?>" />

<input  name="order_server" type="submit" value="Order Server">
</form>
</div>
</div>

	
	</>
	
	
			
			<?php 
		}		else
			//THIS IS THE SERVER WE WANT TO ORDER
		{	
			?>
			<div class="float-left decorative-bottom">
			
			<img src="<?php echo htmlspecialchars(billing_image_url((string)($row['img_url'] ?? '')), ENT_QUOTES, 'UTF-8');?>" width="230" height="112">
			<center><b>	<?php echo htmlspecialchars((string)$row['service_name'], ENT_QUOTES, 'UTF-8');?></b></center>
			<?php
			
			//$isAdmin = if( current_user_can('administrator')){
			//$isAdmin = true;
			//$isAdmin = $db->isAdmin($_SESSION['user_id'] );
			
			$isAdmin = false;
			if($isAdmin)
			{
				if(!isset($_POST['edit']))
				{
					echo "<p style='color:gray;width:230px;' >$row[description]<p>";
					echo "<form action='' method='post'>".
						 "<input type='hidden' name='service_id' value='$row[service_id]' />".
						 "<input type='submit' name='edit' value='Edit' />".
						 "</form>";
				}
				else
				{
					echo "<form action='' method='post'>".
						 "<textarea style='resize:none;width:230px;height:132px;' name='description' >".str_replace("<br>", "\r\n", $row['description'])."</textarea><br>".
						 "<input type='hidden' name='service_id' value='$row[service_id]' />".
						 "<input type='submit' name='save' value='Save' />".
						 "</form>";
				}
			}
			else
				echo "<p style='color:gray;width:280px;' >$row[description]<p>";
			?>
			</div>
			<table class="float-left">
			<form method="post" action="add_to_cart.php">
    		<input type="hidden" name="service_id" size="15" value="<?php if(isset($_POST['service_id'])) echo $_POST['service_id'];?>">
			<input type="hidden" name="remote_control_password" size="15" value="ChangeMe">
			<input type="hidden" name="ftp_password" size="15" value="ChangeMe">
			<tr>
			<td align="right"><b>Game Server Name</b> </td>
			<td align="left">
			<input type="text" name="home_name" size="40" value="<?php echo $row['service_name'];?>">
			</td>
			<tr>
			  <td align="right"><b>Location</b></td>
			  <td align="left">
						<?php
			// Fetch servers available for this game from billing_services.remote_server_id
			// (a comma-separated list of numeric remote server IDs, e.g. "1,3,7").
			$available_server = false;
			$remoteIdsCsv = (string)($row['remote_server_id'] ?? '');
			$allowedIds = [];
			foreach (explode(',', $remoteIdsCsv) as $part) {
				$part = trim($part);
				if ($part !== '' && ctype_digit($part)) {
					$allowedIds[] = (int)$part;
				}
			}
			if (!empty($allowedIds)) {
				$inList = implode(',', $allowedIds);
				$rsQuery = "SELECT remote_server_id, remote_server_name
				            FROM {$table_prefix}remote_servers
				            WHERE remote_server_id IN ({$inList})
				            ORDER BY remote_server_name";
				$rsResult = $db->query($rsQuery);
				if ($rsResult) {
					$firstServer = true;
					while ($rs = $rsResult->fetch_assoc()) {
						$rsID    = (int)$rs['remote_server_id'];
						$rsNAME  = htmlspecialchars((string)$rs['remote_server_name'], ENT_QUOTES, 'UTF-8');
						$checked = $firstServer ? ' checked' : '';
						$available_server = true;
						$firstServer = false;
						echo "<div>\n"
						   . "  <input type='radio' name='ip_id' id='rs_{$rsID}' value='{$rsID}' required{$checked}>\n"
						   . "  <label for='rs_{$rsID}'>{$rsNAME}</label>\n"
						   . "</div>\n";
					}
				}
			}
			?>



			  </td>
			</tr>
			<tr> 
			  <td align="right"><b>Configure</b></td>
			  <td  align="left">
			  <div class="slidecontainer">
			     <center><b>Player Slots</b> </center>
				<input type="range" name="max_players" min="<?php echo $row['slot_min_qty']?>" max="<?php echo $row['slot_max_qty']?>" value="<?php echo $row['slot_min_qty']?>" class="slider" id="playerRange">
				 <center><b>Months</b></center>
				 <input type="range" name="qty" min="1" max="24" value="1" class="slider" id="invoiceRange">

				<p>Player Slots: <span id="playerSlots"></span><br>
				<span>Price: $<?php echo number_format(floatval($row['price_monthly']),2 );?> USD</span><br>
				<span id="invoiceDuration"></span><br>
				<span id="totalPrice"></span></p>
				</div>
				
								
				<script>
				var slider = document.getElementById("playerRange");
				var invoiceslider = document.getElementById("invoiceRange");

				var output = document.getElementById("playerSlots");
				var price = document.getElementById("totalPrice");
				var invoiceDuration = document.getElementById("invoiceDuration");
				var totalvalue = 0;

				
				output.innerHTML = slider.value;
				invoiceDuration.innerHTML = "Duration: "+invoiceslider.value+" months";
                totalvalue =  slider.value * invoiceslider.value * <?php echo number_format($row['price_monthly'],2);?>;
				price.innerHTML = "Total Price: $"+totalvalue.toFixed(2) ;

				slider.oninput = function() {
				  output.innerHTML = this.value;
				  invoiceDuration.innerHTML = "Duration: "+invoiceslider.value+" months";
				   totalvalue =   invoiceslider.value * <?php echo number_format($row['price_monthly'],2);?>;
				  price.innerHTML = "Total Price: $"+totalvalue.toFixed(2) ;
				}
				invoiceslider.oninput = function() {
					 invoiceDuration.innerHTML = "Duration: "+invoiceslider.value+" months";
					  totalvalue =  slider.value * invoiceslider.value * <?php echo number_format($row['price_monthly'],2);?>;
					 price.innerHTML = "Total Price: $"+totalvalue.toFixed(2) ;
				}
				</script>			
			  
			 
			 	 <input type="hidden"  name="invoice_duration" value="month" />
			  </td>
			</tr>
			
						<tr>
							<td align="left" colspan="2">
								<input name="service_id" type="hidden" value="<?php echo $row['service_id'];?>"/>
								<?php
									// Only show Add to Cart when logged in
									$is_logged_in = (isset($_SESSION['website_user_id']) && !empty($_SESSION['website_user_id'])) || (isset($_SESSION['website_username']) && !empty($_SESSION['website_username']));
								?>
								<?php if ($available_server && $is_logged_in): ?>
									<button type="submit" name="add_to_cart" class="gsw-btn">Add to Cart</button>
								<?php else: ?>
									<div class="login-placeholder">Please <a href="login.php">login</a> to order</div>
								<?php endif; ?>
							</form>
							</td>
						</tr>
			<tr>
			<td align="left" colspan="2">
						<form action ="serverlist.php" method="POST">
							<button class="gsw-btn-secondary">Back to List</button>
						</form>
			</td>
			</tr>
			</table>
			<?php
		}
	}
	?>
	</div>
<?php
// Close database connection
	billing_maybe_close_db($db);
?>
</body>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</html>
