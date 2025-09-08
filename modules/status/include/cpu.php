<?php
/*
 * Component of the status module
 */

if ($os == "windows")
{
	if (!extension_loaded('com_dotnet'))
	{
		echo '<div style="width:855px;position:absolute;z-index:21;margin-top:90px;
						  margin-left:20px;background-color: rgba(0, 0, 0, 0.6);
						  color:white;padding:10px;border:2px solid red; border-radius:9px;">'.
			   '<div onClick="this.parentNode.remove();" 
					 style="width:10px;display:block;float:right;background-color: black;
							color:gray;padding:0px 1px 0px 4px;border:2px solid gray;
							border-radius:9px;cursor:pointer;position:relative;top:-8px;right:-8px;">X</div>'.
			   '<div style="display:block:float:left;" >To enable CPU & RAM status you must to add <b style="color:blue;">extension=php_com_dotnet.dll</b>, '.
			   "just after the other extensions in php.ini and restart apache.<br>".
			   "If the problem persists maybe the extension is not installed, ".
			   "so you need to find out how to install this extension for your distribution of PHP.</div>".
			  '</div>';
		
		$nocpushow = "1";
	}
	else
	{
		$wmi = new \COM("Winmgmts://");
		$cpus = $wmi->execquery("SELECT * FROM Win32_Processor");
		
		$cpu_num = '0';
		
		foreach ($cpus as $cpu)
		{
			$cpu_num = $cpu->NumberOfLogicalProcessors;
		}
		
		$cpus_info = $wmi->execquery("select * from Win32_PerfFormattedData_PerfOS_Processor");

		$cores = array();
		
		$cpu_loop = 1;
		
		foreach($cpus_info as $cpu_info)
		{
			$cores[$cpu_loop] = 100 - $cpu_info->PercentIdleTime;
			
			$cpu_loop++;

			if($cpu_loop > $cpu_num)
				break;
		}
		$nocpushow = "0";
	}
} 
elseif ($os == "linux")
{
	if( isset($_GET['remote_server_id']) ) 
	{
		require_once('includes/lib_remote.php');
		global $db;
		$rhost_id = $_GET['remote_server_id'];
		$remote_server = $db->getRemoteServer($rhost_id);
		$remote = new OGPRemoteLibrary($remote_server['agent_ip'], $remote_server['agent_port'], $remote_server['encryption_key'], $remote_server['timeout']);
		$cores = $remote->shell_action('get_cpu_usage' , 'logical');
	}
	else
	{
		function getStat()
		{
			$_statPath = '/proc/stat';
			ob_start();
			passthru('cat ' . $_statPath);
			$stat = ob_get_contents();
			ob_end_clean();
			
			$lb = explode("\n", $stat);

			$first = 1;

			$cores = array();
			foreach($lb as $line)
			{
				if($first == 0)
				{
					if(preg_match('/^cpu[0-9]/', $line))
					{
						$info = explode(' ', $line );
						$cores[] = array(
							'user' => $info[1],
							'nice' => $info[2],
							'sys' => $info[3],
							'idle' => $info[4],
							'total' => $info[1] + $info[2] + $info[3] + $info[4]
							);
						
					}
				}
				else
				{
					$first = 0;
				}
			}
			return $cores;
		}

		/* compares two information snapshots and returns the cpu percentage */
		function getCpuUsage($stat1, $stat2)
		{
			if( count($stat1) !== count($stat2) )
			{
				return False;
			}

			for( $i = 0; $i < count($stat1) ; $i++ )
			{
				$diff_idle[$i] = $stat2[$i]['idle'] - $stat1[$i]['idle'];
				$diff_total[$i] = $stat2[$i]['total'] - $stat1[$i]['total'];
				$DU = round(( 1000 * ( $diff_total[$i] - $diff_idle[$i] ) / $diff_total[$i] ) / 10, 2);
				$diff_usage[$i] = $DU > 100 ? 100 : $DU;
			}

			return $diff_usage;
		}
		
		do{
			$stat1 = getStat();
			sleep(1);
			$stat2 = getStat();
			$cores = getCpuUsage($stat1, $stat2);
		} while($cores == False);
	}
	$nocpushow = "0";
} 
elseif ($os == "nocpu")
{
	$nocpushow = "1";
} 
else
{
	$nocpushow = "1";
}

?>