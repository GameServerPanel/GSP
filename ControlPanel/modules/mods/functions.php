<?php

function scrape_mod_url($url) {
	$html = file_get_html($url);
	$upDate = $html->find('div[class="changelog headline"]', 0)->innertext;
	return $upDate;
}

function parse_date($mod_date){
	$mod_update = array();
	foreach ($mod_date as $key => $value) {
		$mDate = $mod_date[$key];
		$mDate = explode(" ",$mDate);
		$month = $mDate[0];
		$day = rtrim($mDate[1], ',');
		if( "$mDate[2]" == "@"){
			$year = "2024";
			$time = $mDate[3];
			$time = explode(":",$time);
			if( substr($time[1], -2) == "am"){
				$time = "$time[0]:".substr($time[1], 0, strlen($time[1])-2);
			}else{
				$time = $time[0]+12 .":".substr($time[1], 0, strlen($time[1])-2);
			}
		}else{
			$year = $mDate[2];
			$time = $mDate[4];
			$time = explode(":",$time);
			if( substr($time[1], -2) == "am"){
				$time = "$time[0]:".substr($time[1], 0, strlen($time[1])-2);
			}else{
				$time = $time[0]+12 .":".substr($time[1], 0, strlen($time[1])-2);
			}
		}
		$datetime = "$month/$day/$year $time";
		$datetime = date_create_from_format('M/j/Y H:i', $datetime);
		$mod_update += [$key=>$datetime->format('Y-m-d H:i')];
	}
	return $mod_update;
}

function mods($dbname) {
	$mods = [];
	$conn = dbConnect($dbname);
	$sql = "SELECT mod_id FROM mods";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
	// output data of each row
		while($row = $result->fetch_assoc()) {$mods[] = $row["mod_id"];}
	}else{
		$mods[] += "0 results";
	}
	$conn->close(); 
	return $mods;
}

function dbConnect($dbname){
	$servername = "localhost";
	$username = "localuser";
	$password = "Pkloyn7yvpht!";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	if (!$conn) {die("Connection failed: " . mysqli_connect_error());}
	return $conn;
}

