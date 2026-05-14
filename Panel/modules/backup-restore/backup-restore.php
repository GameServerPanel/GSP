<?php

include "function.php";

function exec_ogp_module(){

	if($_SERVER['REQUEST_METHOD'] === 'GET'){
		$homeid = $_GET['home_id'];
		if($homeid == ""){
			$home = "https://panel.iaregamer.com/home.php?m=dashboard&p=dashboard";
			echo '<center>';
			echo '<h1>WE HAVE A PROBLEM:</h1>';
			echo '<h3>Theres NO homeid</h3>';
//			echo '<button onclick="window.location.href="'.$home.';">&emsp; OK &emsp;</button>';
			echo '</center>';
		}else{
			echo "<center><div>";
			echo '<h3 style="text-transform: none;">
				This Module is Currently not Working - Testing phase - if you need to 
				restore from a backup see &emsp; @shootingBlanks &emsp; or &emsp; @dimrod
				&emsp; on discord</h3>';
			echo '<p><b>You can push buttons to see what it does,
				you wont hurt anything right now!</b></p>';
			echo '<div><form id="mianForm" method="POST">';
			echo '<input type="hidden" name="home_id" value="'.$homeid.'"/>';
				getButtonList($homeid);
			echo "	</form></div></center>";
		}
	}elseif($_SERVER['REQUEST_METHOD'] === 'POST'){
		$homeid = $_GET['home_id'];
		$action = $_POST['dothis'];
		if($action == "backupNow"){
			$homeid = $_POST['home_id'];
			$action = $_POST['dothis'];
			BackupNow($homeid, $action);
		}elseif($action == "restoreNow"){
			$homeid = $_POST['home_id'];
			$action = $_POST['dothis'];
			restoreNow($homeid, $action);
		}elseif($action == cancel){
			$homeid = $_POST['home_id'];
			goBack($homeid);
		}else{
			$action = $_POST['dothis'];
			$homeid = $_POST['home_id'];
			doButtons($homeid, $action);
		}
	}
}