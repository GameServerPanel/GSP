<?php
/*
 * Component of the status module
 */

if( isset($_GET['remote_server_id']) AND $os != "nocpu" )
{
	require_once('includes/lib_remote.php');
	$rhost_id = $_GET['remote_server_id'];
	$remote_server = $db->getRemoteServer($rhost_id);
	$remote = new OGPRemoteLibrary($remote_server['agent_ip'], $remote_server['agent_port'], $remote_server['encryption_key'], $remote_server['timeout']);
	$disk_info = $remote->shell_action('get_disk_usage' , 'sum');
	$free = $disk_info['free'];
	$used = $disk_info['used'];
	$total = $disk_info['total'];
	$percent = $disk_info['percent'];
}
else
{
	if($os == "windows")
	{
		$total = disk_total_space("./");
		$free = disk_free_space("./");
		$used = $total - $free;
	}
	else
	{
		if($cygwin)
			$disk_info = shell_exec('df -lP|grep "^.:\s"|awk \'{size+=$2}{used+=$3}{avail+=$4} END {print size, used, avail}\'');
		else
			$disk_info = shell_exec('df -lP|grep "^/dev/.*"|awk \'{size+=$2}{used+=$3}{avail+=$4} END {print size, used, avail}\'');
		list($totalKB, $usedKB, $freeKB) = explode(" ", $disk_info);
		$free = 1024*$freeKB;
		$used = 1024*$usedKB;
		$total = 1024*$totalKB;
	}
	$percent = 100 * $used / $total;
}

$diskspace = numbersFormatting($total);
$diskinuse = numbersFormatting($used) . ' (' . round($percent, "2") . '%)';
$hddbarusage = round($percent, "2");
$hddfreespace = numbersFormatting($free);
?>