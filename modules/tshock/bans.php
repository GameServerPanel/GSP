<?php
/*
 * Component of the tshock module
 */
function exec_ogp_module()
{
	include "modules/tshock/shared.php";
	if($continue)
	{
		?>
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item">
				<a href="?m=tshock&p=bans&show=create&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">
				Create
				</a>
			</li>
			<li class="nav-item">
				<a href="?m=tshock&p=bans&show=list&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">
				List
				</a>
			</li>
			<li class="nav-item">
				<a href="?m=tshock&p=bans&show=read&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>">
				Read
				</a>
			</li>
		</ul>				
		<?php
		if($_GET['show'] == 'create')//Create a new ban entry.
		{
			?>
			<div id="tab4" class="tab-pane" role="tabpanel">
				<div class="row">
					<div class="col-md-12">
						<p class="mbr-text py-5 mbr-fonts-style display-7"></p>
							<form action="?m=tshock&p=bans&show=create&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>" method="POST">
								IP: <input type="text" name="ip" placeholder="The IP to ban" size="34"><br><br>
								Name: <input type="text" name="name" placeholder="The name to ban" size="30"><br><br>
								Reason: <input type="text" name="reason" placeholder="Reason to assign the ban" size="29"><br>
								<br><input type="submit" name="create" value="Ban User">
							</form>
					
					</div>
				</div>
			</div>	
		<?php
			If(isset($_POST['create'])){
				if($token){
					$params = array('token' => $token, 
									'ip' => $_POST['ip'],
									'name' => $_POST['name'],
									'reason' => $_POST['reason']);
					$response = getResponse($ip, $port, '/bans/create/', $params);
					print_success($response['response']);
				}
				else
				{
					print_failure("No Token Found!");
				}
			}
		}
		
		if($_GET['show'] == 'list') //View all bans in the tshock database.
		{
			if($token){
				$params = array('token' => $token);
				$response = getResponse($ip, $port, '/v2/bans/list/', $params);
				$bans_table = "<table>";
				foreach($response['bans'] as $ban)
				{
					$bans_table .= "<tr>";
					foreach($ban as $ban_key => $ban_value)
						$bans_table .=  "<td><b>".strtoupper($ban_key)."</b>: ".$ban_value."</td>";
					$bans_table .= "</tr>";
				}
				$bans_table .= "</table>";
				echo $bans_table;
			}
			else
			{
				print_failure("No Token Found!");
			}
		}
		
		if($_GET['show'] == 'read') //View the details of a specific ban.
		{
			?>
			<div id="tab4" class="tab-pane" role="tabpanel">
				<div class="row">
					<div class="col-md-12">
						<p class="mbr-text py-5 mbr-fonts-style display-7"></p>
							<form action="?m=tshock&p=bans&show=read&home_id-mod_id-ip-port=<?=$_GET['home_id-mod_id-ip-port']?>" method="POST">
								Username or IP: <input type="text" name="ban" placeholder="IP or name to look up" size="35"><br><br>
								Type: <input type="radio" name="type" value="ip" checked> IP
									  <input type="radio" name="type" value="name"> Name<br><br>
								<input type="checkbox" name="caseinsensitive" value="caseinsensitive">Case Sensitive (Name lookups should be case sensitive.)<br>
								<br><input type="submit" name="read" value="Show Details">
							</form>
					</div>
				</div>
			</div><?php
			if(isset($_POST['read'])){
				if($token){
					$caseinsensitive = isset($_POST['caseinsensitive'])?'true':'false';
					$params = array('token' => $token,
									'ban' => $_POST['ban'],
									'type' => $_POST['type'],
									'caseinsensitive' => $caseinsensitive);
					$response = getResponse($ip, $port, '/v2/bans/read/', $params);
					if($response['status'] == '200')
					{
						$bans_table = "<table><tr>";
						foreach($response as $ban_key => $ban_value)
							if($ban_key != 'status')
								$bans_table .=  "<td><b>".strtoupper($ban_key)."</b>: ".$ban_value."</td>";
						$bans_table .= "</tr></table>";
						echo $bans_table;
					}
					else
					{
						print_failure($response['error']);
					}
				}
				else
				{
					print_failure("No Token Found!");
				}
			}
		}
	}
}