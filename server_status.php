<?php
/*
 * Server status monitoring page
 */

// Standalone Server Status Page - Available to all users
require_once("includes/functions.php");
require_once("includes/helpers.php");
require_once("includes/html_functions.php");
startSession();

// Report all PHP errors
error_reporting(E_ERROR);

// Path definitions
define("IMAGES", "images/");
define("INCLUDES", "includes/");
define("MODULES", "modules/");
define("CONFIG_FILE","includes/config.inc.php");

require_once CONFIG_FILE;
require_once('includes/lib_remote.php');

// Connect to the database server and select database.
$db = createDatabaseConnection($db_type, $db_host, $db_user, $db_pass, $db_name, $table_prefix);

// Load languages.
include_once("includes/lang.php");

if (!$db instanceof OGPDatabase) {
    ogpLang();
    die(get_lang('no_db_connection'));
}

// Check if user is logged in
if (!isset($_SESSION['users_login'])) {
    header('Location: index.php');
    exit();
}

// Get user info
$loggedInUserInfo = $db->getUserById($_SESSION['user_id']);

// Get settings
$settings = $db->getSettings();
@$GLOBALS['panel_language'] = $settings['panel_language'];
ogpLang();

function ping_host($host, $timeout = 5) {
    if (function_exists('exec')) {
        $output = array();
        $result = 0;
        
        // Use ping command based on OS
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec("ping -n 1 -w " . ($timeout * 1000) . " " . escapeshellarg($host), $output, $result);
        } else {
            exec("ping -c 1 -W " . $timeout . " " . escapeshellarg($host), $output, $result);
        }
        
        if ($result === 0) {
            // Extract ping time from output
            foreach ($output as $line) {
                if (preg_match('/time[<=]([0-9.]+)\s*ms/i', $line, $matches)) {
                    return floatval($matches[1]);
                }
            }
            return 0; // Host is up but couldn't extract time
        }
    }
    return false; // Host is down or ping unavailable
}

function get_hostname($ip) {
    $hostname = gethostbyaddr($ip);
    return ($hostname && $hostname !== $ip) ? $hostname : false;
}

// Get all remote servers
$servers = $db->getRemoteServers();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Status - OGP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .status-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .status-table th {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .status-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .status-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-table tr:hover {
            background-color: #f5f5f5;
        }
        .status-up {
            color: #4CAF50;
            font-weight: bold;
            font-size: 18px;
        }
        .status-down {
            color: #f44336;
            font-weight: bold;
            font-size: 18px;
        }
        .ping-good {
            color: #4CAF50;
            font-weight: bold;
        }
        .ping-medium {
            color: #ff9800;
            font-weight: bold;
        }
        .ping-bad {
            color: #f44336;
            font-weight: bold;
        }
        .refresh-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .refresh-btn:hover {
            background-color: #45a049;
        }
        .last-updated {
            text-align: center;
            color: #666;
            margin-top: 20px;
            font-style: italic;
        }
        .server-name {
            font-weight: bold;
            text-align: left;
        }
        .no-servers {
            text-align: center;
            color: #666;
            padding: 40px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖥️ Server Status Dashboard</h1>
        
        <button class="refresh-btn" onclick="window.location.reload();">🔄 Refresh Status</button>
        
        <?php if (empty($servers)): ?>
            <div class="no-servers">
                No servers configured in the system.
            </div>
        <?php else: ?>
            <table class="status-table">
                <thead>
                    <tr>
                        <th>Server Name</th>
                        <th>Location/IP</th>
                        <th>Hostname</th>
                        <th>Status</th>
                        <th>Ping (ms)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($servers as $server): 
                        $server_ip = gethostbyname($server['agent_ip']);
                        $hostname = get_hostname($server_ip);
                        
                        // Check server status
                        $remote = new OGPRemoteLibrary($server['agent_ip'], $server['agent_port'], 
                                                    $server['encryption_key'], $server['timeout']);
                        $status = $remote->status_chk();
                        $is_online = ($status === 1);
                        
                        // Get ping time
                        $ping_time = ping_host($server_ip, 3);
                        
                        // Determine ping color class
                        $ping_class = '';
                        if ($ping_time !== false) {
                            if ($ping_time <= 50) {
                                $ping_class = 'ping-good';
                            } elseif ($ping_time <= 150) {
                                $ping_class = 'ping-medium';
                            } else {
                                $ping_class = 'ping-bad';
                            }
                        }
                    ?>
                        <tr>
                            <td class="server-name"><?php echo htmlspecialchars($server['remote_server_name']); ?></td>
                            <td><?php echo htmlspecialchars($server['agent_ip']); ?>
                                <?php if ($server_ip !== $server['agent_ip']): ?>
                                    <br><small>(<?php echo htmlspecialchars($server_ip); ?>)</small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $hostname ? htmlspecialchars($hostname) : '<em>N/A</em>'; ?></td>
                            <td>
                                <?php if ($is_online): ?>
                                    <span class="status-up">🟢 UP</span>
                                <?php else: ?>
                                    <span class="status-down">🔴 DOWN</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($ping_time !== false): ?>
                                    <span class="<?php echo $ping_class; ?>">
                                        <?php echo number_format($ping_time, 1); ?> ms
                                    </span>
                                <?php else: ?>
                                    <span style="color: #999;">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <div class="last-updated">
            Last updated: <?php echo date('Y-m-d H:i:s'); ?>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="home.php" style="color: #4CAF50; text-decoration: none; font-weight: bold;">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>