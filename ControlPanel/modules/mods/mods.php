<?php
$dbname = "server_1434";
//$dbname = "server_1437";

include_once('functions.php');
include_once('assets/simple_html_dom.php');

$mod_date = array();

echo "$dbname<br>";
$mods = mods($dbname);

for($i=0; $i<count($mods); $i++){
	$url = "https://steamcommunity.com/sharedfiles/filedetails/changelog/$mods[$i]";
	$text = scrape_mod_url($url);
	$mod_date += ["$mods[$i]"=>substr(trim($text),8)];
}

$mod_update = parse_date($mod_date);
printvar($mod_update);

function printvar($var){
	echo "<pre>";
		print_r($var);
	echo "</pre>";
}

