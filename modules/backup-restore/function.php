<?php

//	GET Functions	====================
function getButtonList($homeid){
// Get Avaliable back ups for $homeid
	$backupPaths = backupPaths($homeid);
	$latestDay = latestBackup($backupPaths);
	topRow();
	echo '<br><strong style="font-size:30px">Backups Available for game server '.$homeid.'</strong>';
	bottomRow($latestDay,$backupPaths);

}
function topRow(){
//	echo '<div>';
	echo '<button type="submit" name="dothis" value="goBack"><<&emsp;Go Back</button>';
	echo '&emsp;&emsp;&emsp;';
	echo '<button type="submit" name="dothis" value="backup">Create New Backup</button>';
//	echo '</div>';
}
function bottomRow($latestDay,$backupPaths){
	echo '<br><div>';
	$latest = customSearch($latestDay,$backupPaths);
	$length = count($backupPaths);
	for ($i = 0; $i < $length; $i++) {
		$path = explode("/",$backupPaths[$i]);
		$path1 = explode("-",$path[4]);
		if( $path1[0] == 0 ){
			//dont need this one
		}elseif ( "$path1[0]" == "$latest" ) {
			$day = $path1[1];
			$dayLatest = "$path1[1]"." -> Latest";
			echo '<button type="submit" name="dothis" value="'.$day.'">'.$dayLatest.'</button>&emsp;';
		}else{
			$day = $path1[1];
			echo '<button type="submit" name="dothis" value="'.$day.'">'.$day.'</button>&emsp;';
		}
	}
	echo '</div><br>';
}

//	POST Functions	================
function doButtons($homeid, $action){
	switch ($action) {
		case "goBack":
			goBack($homeid);
			break;
		case "backup":
			backup($homeid,$action);
			break;
		default:
			restore($homeid,$action);
	}
}
function goBack($homeid){
	$baseURL = "https://panel.iaregamer.com/home.php?m=gamemanager&p=game_monitor&home_id=$homeid";
	header('Location: '.$baseURL);
	exit;
}
function backup($homeid, $action){
	echo '<center><div><form id="backupNow" method="POST">';
	echo '<h3>You have chosen to create a new backup.';
	echo '<br>This will overwrite the latest backup and';
	echo '<br>create a new one for this day.</h3>';
	echo '<input type="hidden" name="home_id" value="'.$homeid.'"/>';
	echo '<button type="submit" name="dothis" value="cancel">Last Chance to Cancel</button>';
	echo '&emsp;&emsp;&emsp;';
	echo '<button type="submit" name="dothis" value="backupNow">
			Backup Home Dir '.$homeid.'</button>';
	echo '</form></div></center>';
}
function backupNow($homeid){
	$backupPaths = backupPaths($homeid);
	$latestDay = latestBackup($backupPaths);
	$backup = customSearch($latestDay, $backupPaths);
	$backupfolder = $backupPaths[$backup];
	$gameServer = gameServer($homeid);
	$serverINFO = serverINFO($gameServer);
	$servername = $serverINFO['servername'];
	$serverlogin = $serverINFO['serverlogin'];
	$serverpass = $serverINFO['serverpass'];
	$homedir = $serverINFO['homedir'];
	$backupdir = $serverINFO['backupdir'];
	$backuplink = "00-latest-$servername";
//	$pass = "******";
//	--dry-run 
	$sshpass = 'sshpass -p '.$serverpass;
	$rsync = 'rsync -avP --delete --dry-run -e "ssh -p 12322"';
	$softlink = '--link-dest '.$backupdir.'/'.$backuplink;
	$gameserver = $serverlogin.':'.$homedir.$homeid;
	$localdir = $backupfolder;
	$command = $sshpass.' '.$rsync.' '.$softlink.' '.$gameserver.' '.$localdir;
//	echo "<br>Command:<br>	$command<br>";
	$output = [];
	$returnVar = 0;
	exec($command, $output, $returnVar);
	echo '<center><div>';
		echo "<h3>Backing up: $homeid </h3>";
		echo '<form method="POST">';
		echo '<input type="hidden" name="home_id" value="'.$homeid.'"/>';
		echo '<button type="submit" name="dothis" value="cancel">Go Back Home</button>';
	if ($returnVar === 0) { 
		echo "<h4>Rsync completed successfully.</h4>";
	}else{
		echo "<h4>Rsync failed with status $returnVar.</h4>";
	}
	echo '</form>';
	echo '<div>';
		echo '<textarea readonly id="backup" name="backup" rows="20" cols="30">';
			foreach ($output as $line) { echo $line."\n";}
		echo '</textarea>';
	echo '</div></div></center>';

}
function info(){
//	Local to Local:  rsync [OPTION]... [SRC]... DEST
//	Local to Remote: rsync [OPTION]... [SRC]... [USER@]HOST:DEST
//	Remote to Local: rsync [OPTION]... [USER@]HOST:SRC... [DEST]

//Push	rsync -a 
//	localDir 
//	username@remote_host:destination_directory

//Pull	rsync -a 
//	username@remote_host:/home/username/dir1
//	localDir
}
function restore($homeid, $action){
	echo '<center><div><h3>';
	echo 'You Have chosen to Restore';
	echo "<br>Home: $homeid from $action's backup.";
	echo '</h3></div></center>';
	echo '<center><div><form id="restore" method="POST">';
	echo '<input type="hidden" name="home_id" value="'.$homeid.'"/>';
	echo '<input type="hidden" name="restore" value="'.$action.'"/>';
	echo '<button type="submit" name="dothis" value="cancel">Last Chance to Cancel</button>';
	echo '&emsp;&emsp;&emsp;';
	echo '<button type="submit" name="dothis" value="restoreNow">
			Restore Home Dir from '.$action.'\'s backup</button>';
	echo '</form></div></center>';
}
function restoreNow($homeid, $action){
	$restore = $_POST['restore'];	// day of week to restore
	$backupPaths = backupPaths($homeid);	//list of all backups for this home dir
	$pathNum = customSearch($restore, $backupPaths);	// key number of array of the backup
	$localPath = $backupPaths[$pathNum];	//local path of the backup file
	$gameServer = gameServer($homeid);	//gameserver name
	$serverINFO = serverINFO($gameServer);
//	gameserver INFO from xml file
	$gameserver = $serverINFO[servername];		//[servername] => kcwin.D.drive
	$serverlogin = $serverINFO[serverlogin];	//[serverlogin] => cyg_server@kcwin.iaregamer.com
	$serverpass = $serverINFO[serverpass];		//[serverpass] => S4wihr6q8rzc!
	$homedir = $serverINFO[homedir];			//[homedir] => /cygdrive/d/OGP64/home/gameserver/
	$backupdir = $serverINFO[backupdir];		//[backupdir] => /sdb1/backup/gameserver
//	build the $command
	$sshpass = 'sshpass -p '.$serverpass;
	$rsync = 'rsync -avP --delete --dry-run -e "ssh -p 12322"';
	$gameserver = $serverlogin.':'.$homedir;
	$localdir = $localPath;

	//	Local to Remote: rsync [OPTION]... [SRC]... [USER@]HOST:DEST
	$command = $sshpass.' '.$rsync.' '.$localdir.' '.$gameserver;
//echo $command;
	echo "<center><div>";
	echo '<form method="POST">';
	echo "<h3>Restoring: $homeid from $restore's</h3>";
	echo '<input type="hidden" name="home_id" value="'.$homeid.'"/>';
	echo '<button type="submit" name="dothis" value="cancel">Go Back Home</button>';
//	echo 'This is where the actual Restore will take place<br>';
	exec($command, $output, $returnVar);
	if ($returnVar === 0) { 
		echo "<h4>Rsync completed successfully.</h4>";
	}else{
		echo "<h4>Rsync failed with status $returnVar.</h4>";
	}
	echo '</form>';
	echo '<div>';
		echo '<textarea readonly id="backup" name="backup" rows="20" cols="30">';
			foreach ($output as $line) { echo $line."\n";}
		echo '</textarea>';
	echo "</div>";
	echo "</div></center>";
}

//	General Functions	=================
function gameServer($homeid){	// Gets serverName for $homeid
	$dir = "/sdb1/backup/gameserver";
	$servers = backupPaths($homeid);
	$name = $servers[0];
	$name = explode("-",$name);
	$gameServer = dirname($name[2]);

	return $gameServer;
}
function latestBackup($backupPaths){
	$latest = readlink(dirname($backupPaths[0]));
	$latestDay = explode("/",$latest);
	$latestDay = explode("-",$latestDay[4]);
	$latestDay = $latestDay[1];
	return $latestDay;
}
function backupPaths($homeid){
	$dir = "/sdb1/backup/gameserver";
	$backupPaths = glob("$dir/*/$homeid", GLOB_ONLYDIR);
	return $backupPaths;
}
function serverINFO($serverid) {	// returns serverINFO Array
	$xmlData = simplexml_load_file("/sdb1/backup/servers.xml");
	foreach ($xmlData->server as $server) {
		if ($server->servername == $serverid) {
			$serverINFO[servername] = "$server->servername";
			$serverINFO[serverlogin] = "$server->login";
			$serverINFO[serverpass] = "$server->pass";
			$serverINFO[homedir] = "$server->files";
			$serverINFO[backupdir] = "$server->backupdir";
		}
	}
	return $serverINFO;
}
function customSearch($keyword, $arrayToSearch){
    foreach($arrayToSearch as $key => $arrayItem){
        if( stristr($arrayItem, $keyword)){
            return $key;
        }
    }
}
// quick way to print the array, I got tired of typing this during testing
function printArray($array){
	echo "<br>PrintArray<pre>";
		print_r($array);
	echo "</pre><br>";
}


