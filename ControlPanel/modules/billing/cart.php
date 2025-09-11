<?php
function saveOrderToDb($user_id,$service_id,$home_name,$ip,$max_players,$qty,$invoice_duration,$price,$remote_control_password,$ftp_password,$cart_id,$home_id = "0",$status,$finish_date,$extended = "0"){
	global $db;
	if(isset($_SESSION['coupon_id'])){
		$coupon_id = $_SESSION['coupon_id'];
		} else {
		$coupon_id = 0;
	}
	$fields['user_id'] = $user_id;
	$fields['service_id'] = $service_id;
	$fields['home_name'] = $home_name;
	$fields['ip'] = $ip;
	$fields['max_players'] = $max_players;
	$fields['qty'] = $qty;
	$fields['invoice_duration'] = $invoice_duration;
	$fields['price'] = $price;
	$fields['remote_control_password'] = $remote_control_password;
	$fields['ftp_password'] = $ftp_password;
	$fields['cart_id'] = $cart_id;
	$fields['home_id'] = $home_id;
	$fields['status'] = $status;
	$fields['finish_date'] = $finish_date;
	$fields['extended'] = $extended;
	$fields['coupon_id'] = $coupon_id;
	return $db->resultInsertId( 'billing_orders', $fields );
}



function assignOrdersToCart($user_id,$tax_amount,$currency,$coupon_id){
	global $db;
	$fields['user_id'] = $user_id;
	$fields['paid'] = '0';
	$fields['tax_amount'] = $tax_amount;
	$fields['currency'] = $currency;
	//discount coupon
	if (!isset($coupon_id)) $coupon_id = "0";
	$fields['coupon_id'] = $coupon_id;
	$check_expired = $db->resultquery("SELECT id from OGP_DB_PREFIXbilling_coupons WHERE id = $fields[coupon_id] AND count > 0 AND expires >= NOW()");
	if ($check_expired <= 0) $fields['coupon_id'] = 0;
	return $db->resultInsertId( 'billing_carts', $fields );
}

function exec_ogp_module()
{
	error_reporting(E_ALL);
	
	global $db,$view,$settings;
	 $discounted_price = 0;
	
	$user_id = $_SESSION['user_id'];
 if( isset( $_POST["buy"] ) or isset( $_POST["pay_paypal"] ) )
	{
		
			$cart_id = $_POST['cart_id'];
			echo '<meta http-equiv="refresh" content="0;url=home.php?m=billing&p=create_servers&cart_id='.$cart_id.'" >';

 
	}
	
	if( isset( $_POST["extend"] ) or isset( $_POST["extend_and_pay_paypal"] ))
	{

	}
	
	if(isset($_POST['remove']))
	{
		

	}
	
		?>
	<style>
	h4 {
		width:250px;
		height:25px;
		background:#f5f5f5;
		border-top-style:solid;
		border-top-color:#afafaf;
		border-top-width:1px;
		border-style: solid;
		border-color: #CFCFCF;
		border-width: 1px;
		padding-top:8px;
		text-align: center;
		font-family:"Trebuchet MS";
	}
	</style>
	<h2>Cart</h2>
	<!--
	SHOW ALL THE INVOICES FOR USER
	
	<form method="post" action="?m=billing&p=orders">
	<input type="hidden" name="cart_id" value="<?php echo $order['cart_id'];?>">
	<input type="submit" value="All Orders">
	</form>
	-->
	<?php
	if( isset($_SESSION['CART']) and !empty($_SESSION['CART']) )
	{
		$carts[0] = $_SESSION['CART'];
	}

	$user_carts = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXbilling_carts WHERE user_id=".$db->realEscapeSingle($user_id) ." order by cart_id desc" );
	

	if( $user_carts >=1 )
	{

	// SELECT WHAT KIND OF OLD INVOICES TO DISPLAY. WE NEED A BUTTON?	
		foreach ( $user_carts as $user_cart )
		{
			$cart_id = $user_cart['cart_id'];

			$carts[$cart_id] = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXbilling_carts AS cart JOIN
																OGP_DB_PREFIXbilling_orders AS orders  
																ON orders.cart_id=cart.cart_id
																WHERE orders.status IN (0, -1 , -2) AND (cart.cart_id=".$db->realEscapeSingle($cart_id). ") order by order_id asc");
		}
	}
	
	if( empty( $carts ) )
	{
		print_failure( get_lang('there_are_no_orders_in_cart') );
		?>		
		<a href="?m=billing&p=shop"><?php print_lang('back'); ?></a>
		<?php
		return;
	}
	foreach ( $carts as $orders )
	{
		if( !empty( $orders ) )
		{
			?>
	<center>
		<table style="width:95%;text-align:left;" class="center">
			<tr>
			<hr />
                        
			
			 <th>
			<?php print_lang("order_desc");?></th>
			 <th>
			<?php print_lang("price");?>
			 </th>
			 <?php
			 if(isset($orders[0]['paid']) and $orders[0]['paid'] == 3)
			 {
			 ?>
			 <th>
			 <?php print_lang('expiration_date');?>
			 </th>
	 
			 <th>Status
			 </th>
			 <?php
			 }
			 ?>
			 <th>
			 </th>
			</tr>
			<?php 
			$subtotal = 0;
			$total_orders = count($orders);
			$order_counter = 0;
			foreach($orders as $order)
			{
				$order_counter++;
				if ( $order['qty'] > 1 ) 
					$order['invoice_duration'] = $order['invoice_duration']."s";

				$subtotal += ($order['price']* $order['max_players'] * $order['qty']);
				
				?>
			<tr class="tr">
			
			 <td>
				<?php 
				$rserver = $db->getRemoteServer($order['ip']);
				if($order['home_id'] == 0)
				{
				echo "Order# ".$order['order_id'] . " <b>".$order['home_name']."</b>";
//**************************************************
				?>
				<form method="post" action="home.php?m=billing&p=create_servers" >
			    <input type="hidden" name="cart_id" value="<?php echo $order['cart_id'];?>">
			    <input name="buy" type="submit" value="Create Server" ><br>
				</form>
				<?php	
					

//*************************************************
				}
				else{
				
				echo "Order# ".$order['order_id'] . " <b>".$order['home_name']."</b> Server ID ".$order['home_id'] ;
				}
				?>
			 </td>
			 <td>
				<?php 
				echo "$" . number_format( $order['price'], 2 ). "  " .$order['currency'] . " per slot<br>"
				
				. $order['max_players'] . " Slots<br>"
				. $order['qty'] . " " . $order['invoice_duration'] ;
				?>
			 </td>
				<?php
				if($order['paid'] == 0 and ($order['extended'] == 0))
				{
					?>
			 <td align="center">
			  <form method="post" action="">
			   <input type="hidden" name="cart_id" value="<?php echo $order['cart_id'];?>">
			   <input type="hidden" name="order_id" value="<?php echo @$order['order_id'];?>">
	 
			  </form>
			  <?php if ($total_orders == $order_counter) { 
			  ?>
			 <!--checkbox -->
			   <form method="post" action="" >
			    <input type="hidden" name="cart_id" value="<?php echo $order['cart_id'];?>">
			   <?php     				
				//check number of orders they have had or if user is an admin (to be able to create server)
				$isAdmin = $db->isAdmin( $_SESSION['user_id'] );
				$server_price =  number_format( $order['price'], 2 );
				if(isset($settings['display_free'])) {
						$display_free = $settings['display_free'];
				}else {
					$display_free = false;
				}
			   if($isAdmin)
				//if($display_free)   
				{
					if($isAdmin)
					{
						//echo '<input name="buy" type="submit" value="Create Server" ><br>';
					}					
				}				
				?>								     
             </form>
             <?php } ?>
             </td><?php
                }
	 
				
				if($order['paid'] == 3)
				{
					$today=time();
                    $formated_finish_date = date('d/M/Y H:i A',$order['finish_date']);

					//status has a date for invoice
					if($order['status'] > 0)
					{
					$status = "<b style='color:green;'>Active</b>" ;
					}
															 
	  
														  
	  
					//status is -1, invoice has been created
					elseif($order['status'] == -1)
					{
					$status = "<b style='color:yellow;'>Invoice Due</b>";
					}
					//invoice was not paid, server is expired and suspended
					elseif($order['status'] == -2)
					{
					$status = "<b style='color:red;'>Suspended</b>";
					}
										
					//display the expiration date and invoice button.
					if($order['status'] > 0){$warning_status = "<b style='color:green;'>". $formated_finish_date ."</b>";}
					if($order['status'] == -1){$warning_status ="<b style='color:yellow;'>". $formated_finish_date ."</b>";}
					if($order['status'] == -2){$warning_status ="<b style='color:red;'>". $formated_finish_date ."</b>" ;}

				?>
			 <td>
				<?php echo "$warning_status";?>
			 </td>
			 <td>
				<?php echo "$status";


?>
			 </td>
			<?php
				}
				
				if( isset( $order['status'] ) and $order['status'] == "0" or $order['status'] == "-1" or $order['status'] == "-2")
				{
					?>
			 			 <td></td></tr><tr><td>

			  
			 </td><?php
				}
				?>
			</tr><?php
			}
			?>
		</table>
		<table style="width:95%;text-align:left;" class="center">
			<tr>
			 <td>Amount</td>
									   
			 <td>
			<?php
			echo "$" . number_format( $subtotal , 2 ). " " .$order['currency'];?>
			 </td>
			</tr>
			<tr>
				<td><b><?php echo $coupon_name;?></b></td>
				<td>
				<?php
			//APPLY COUPON CODE HERE 
			$coupon_discount_amt = $subtotal * ($coupon_discount / 100);
			echo "-$" . number_format($coupon_discount_amt,2);
			?></td><td>
			 <table><tr>
			 <form method="post" action="">
						<td class="child">
						<input type="text" name="coupon_code"size="5" value="<?php echo $coupon_code ?>"></input>
					</td>
					<td>
					<!--<input type="submit" name="Apply Code" value="Apply Code"></input>-->
					</td>
				   </tr></table>
			</form>
			</td>
			</tr>
			
			<tr>
				<td>Discounted Subtotal</td>
				<td><?php $subtotal = $subtotal-$coupon_discount_amt;echo "$" . number_format( $subtotal , 2 ). " " .$order['currency'];?></td>
			</tr>
			
			<tr>
			 <td>
			Tax Amount</td>
			 <td>
			<?php echo "$" . number_format($order['tax_amount']/100 * $subtotal,2);?>
			 </td>
			</tr>
			<tr>
			 <td>
			<?php print_lang("total");?>
			 </td>
			 <td>
			<?php 
			  $total = $subtotal+($order['tax_amount']/100*$subtotal);
			  echo "$" . number_format( $total , 2 ). " " .$order['currency'];
			?>
			 </td>
			 <td>
			  <?php
		if($order['paid'] == 1)
			  {
			  ?>
			 <form method="post" action="home.php?m=billing&p=create_servers">
			  <input type="hidden" name="cart_id" value="<?php echo $order['cart_id'];?>">
			  <?php
			 if($order['extended'] == "1")
			 {
			 ?>
			  <input name="enable_server" type="submit" value="<?php print_lang("enable_server");?>">
			 <?php 
			 }
			 else
			 {
			 ?>
			 <!-- <input name="create_server" type="submit" value="<?php print_lang("create_server");?>">-->
			 <?php 
			 }
			?>
			 </form>
			  <?php
			  }
			  elseif($order['paid'] == 2)
			  {
			  echo get_lang_f("payment_is_pending_of_approval");
			  }
			  elseif($order['paid'] == 3)
			  {
			  ?>
			 <form method="post" action="?m=billing&p=bill">
			  <input type="hidden" name="cart_id" value="<?php echo $order['cart_id'];?>">
			  <input name="paid" type="submit" value="<?php print_lang("see_invoice");?>">
			 </form>
			  <?php
			  }
			  else
			  {
			   }
			  ?>
			  </form>
			 </td>
			</tr>
		</table>

	</center>
			<?php
		}
	}
	?>		
											  
	<a href="?m=billing&p=shop"><?php print_lang('back'); ?></a>
	<?php
}
?>


















