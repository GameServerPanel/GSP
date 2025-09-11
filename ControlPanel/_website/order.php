<?php

/*
This is the actual "order gameserver" page.  There is a page that displays all the possible game servers we can rent.  This page displays the options
for a single specific game server and has the "Add to Cart" button.  
The gameserver selected is passed from the gameserverss page by a Post of the ServiceID 
When the user clicks the "Add to Cart" button, the next page to load is "add_to_cart.php" which creates all the DB entries.
All the configuration info is passed to the add_to_cart.php in hidden fields 
 
In our website, we are setting "post" pages with a "Tag". The first tag in our post should be the service ID from the services table
There are other methods that might be better to get the info.  But all we need is the "service_ID" in the "ogp_billing_services" table
This method means we can use one code block in every game page and fill in the data dynamically.   
*/
include "panel/_db.php";

	
	if (isset($_POST['save']) AND !empty($_POST['description']))
	{
		$new_description = str_replace("\\r\\n", "<br>", $_POST['description']);
		$service = $_POST['service_id'];
		
		$change_description = "UPDATE opg_billing_services
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
	$qry_services = "SELECT * FROM ogp_billing_services ".$where_service_id ." ORDER BY service_name";
	$services = $db->query($qry_services);
	
	if (isset($_REQUEST['service_id']) && $services === false) {
		echo "<meta http-equiv='refresh' content='1'>";
		return;
	}
	
	foreach ($services as $key => $row) {
		$service_id[$key] = $row['service_id'];
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
		$access_rights[$key] = $row['access_rights'];
	}
	
?>	
<div style="border-left:10px solid transparent;"> 
	<?php
	foreach($services as $row)
	{ 
	if(!isset($_REQUEST['service_id']))
		{
	?>
	<div style="
	float:left; 	
	padding-top: 30px;
  padding-right: 20px;
  padding-bottom: 30px;
  padding-left: 20px;">
  
  
  
<img src="<?php echo $row['img_url'] ;?>" width="460" height="225" >
<br>
<?php echo $row['service_name'];?>
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
			<div style="float:left; border: 4px solid transparent;border-bottom: 25px solid transparent;">
			
			<img src="<?php echo $row['img_url'];?>" width=230 height=112 border=0 ">
			<center><b>	<?php echo $row['service_name'];?></b></center>
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
			<table style="float:left;">
			<form method="post" action="panel/_add_to_cart.php">
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
			//loop through multiple remote server ID stored in services 'remote_server_ip' as text
			//change WHERE clause to IS IN clause
  			$rsiArray = explode(" ", $row['remote_server_id']);
            $rsi = implode(",",$rsiArray);
			//get the out of stock into an array and see if the rsID is in that array
			$available_server = false;
			//loop through each of the assigned servers and see if its disabled
			foreach($rsiArray as $rsi)
			{
				$query = "SELECT * FROM ogp_remote_servers WHERE remote_server_id = ".$rsi;
				$result = $db->query($query);
				foreach($result as $rs)
				{
							
					$rsID =$rs['remote_server_id'];
					$rsNAME = $rs['remote_server_name'];
					//echo  "<option  value='$rsID'>$rsNAME</option>";
					// add disabled to lable and input if $rsID is in out_of_stock
					$is_unavailable = "";
					$service_text_color = "";
					
											
					if($rs['enabled']==0)
					{
						$is_unavailable = "disabled";
						$service_text_color = "red";
					}
					if($is_unavailable == "")
					{
						$available_server = true;
					}
					
					
					//default radio button 
					// //<input type='radio' $is_unavailable  name='ip_id' id='$rsID' value='$rsID' >
					echo "<div>
				  <input type='radio' $is_unavailable  name='ip_id' id='$rsID' value='$rsID' required>
  				  <label for '$rsID' $is_unavailable ><span  style='color:$service_text_color'>$rsNAME </span></label>
				</div>";
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
					if ($available_server)
					{
				?>
				<input  type="submit" name="add_to_cart" value="Add to Cart"/>
					<?php
					}
					?>
			  </form>
			  </td>
			</tr>
			<tr>
			<td align="left" colspan="2">
			<form action ="https://gameservers.world/server-list/" method="POST">
			  <button >Back to List</button>
			</form>
			</td>
			</tr>
			</table>
			<?php
		}
	}
	?>
	</div>
