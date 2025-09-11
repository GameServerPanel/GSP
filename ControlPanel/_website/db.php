
text/x-generic _db.php ( PHP script, ASCII text, with CRLF line terminators )
<?php
$servername = "panel.iaregamer.com";
$username = "remoteuser";
$password = "Pkloyn7yvpht!";
$dbname = "panel";

// Create connection
$db = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$db) {
	echo "failed";
	die("Connection failed: " . mysqli_connect_error());
}

//This gets the current users role , Admin, User or other
//returning  true/false
//$isAdmin = isAdmin(186);
function isAdmin($userID){
    $adminField = $db->query("SELECT 'users_role' FROM ogp_users WHERE userID = $userID");
    if($adminField == "admin"){
        $adminStatus = true;
        }else{
        $adminStatus = false;
    }
    return $adminStatus;
}

function logger($logtext){
    file_put_contents("logfile.txt",$logtext . PHP_EOL,FILE_APPEND);

}
?>
