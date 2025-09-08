<?php
/*
 * Component of the dashboard module
 */

function exec_ogp_module()
{
    global $db;

	if(!$_POST["data"]){  
		echo "Invalid data";  
		exit;  
	}  
	  
	//decode JSON data received from AJAX POST request  
	$data=$_POST["data"];  
	$data = str_replace('\\','',$data);
	$data = json_decode($data);
	  
	foreach($data->items as $item)  
	{  
		//Extract column number for panel  
		$col_id = preg_replace('/[^\d\s]/', '', $item->column);
		//Extract id of the panel  
		$widget_id = preg_replace('/[^\d\s]/', '', $item->id);

		if (is_numeric($col_id) && is_numeric($widget_id)) {
			$db->query("UPDATE ".OGP_DB_PREFIX."widgets_users SET column_id='$col_id', sort_no='".(int)$item->order."', collapsed='".(int)$item->collapsed."' WHERE widget_id='".$widget_id."' AND user_id='".$_SESSION['user_id']."'");
		}
	}

	echo "success";
}
?>