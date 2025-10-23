<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Status - GameServers.World</title>
</head>
<body>
<?php
// Include database configuration
require_once(__DIR__ . '/includes/config.inc.php');

// Create database connection
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Include top bar and menu
include(__DIR__ . '/includes/top.php');
include(__DIR__ . '/includes/menu.php');

// Check if server status table exists, if not create it
$table_check = mysqli_query($db, "SHOW TABLES LIKE 'ogp_server_status'");
if (!$table_check || mysqli_num_rows($table_check) === 0) {
    // Create table for server status updates
    $create_table = "CREATE TABLE IF NOT EXISTS ogp_server_status (
        status_id INT AUTO_INCREMENT PRIMARY KEY,
        remote_server_id INT NOT NULL,
        server_name VARCHAR(255) NOT NULL,
        ip_address VARCHAR(45),
        status ENUM('online', 'offline', 'maintenance') DEFAULT 'offline',
        cpu_usage DECIMAL(5,2),
        memory_usage DECIMAL(5,2),
        disk_usage DECIMAL(5,2),
        uptime VARCHAR(50),
        last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        notes TEXT,
        INDEX idx_remote_server (remote_server_id),
        UNIQUE KEY unique_server (remote_server_id)
    )";
    mysqli_query($db, $create_table);
}

// Fetch all remote servers and their status
$query = "SELECT 
            rs.remote_server_id,
            rs.remote_server_name,
            rs.agent_ip,
            rs.enabled,
            ss.status,
            ss.cpu_usage,
            ss.memory_usage,
            ss.disk_usage,
            ss.uptime,
            ss.last_updated,
            ss.notes
          FROM ogp_remote_servers rs
          LEFT JOIN ogp_server_status ss ON rs.remote_server_id = ss.remote_server_id
          ORDER BY rs.remote_server_name";

$result = mysqli_query($db, $query);

?>

<div class="site-panel">
    <div class="site-panel-title">Server Status</div>
    
    <div style="margin-bottom:20px;text-align:center;">
        <p style="color:rgba(255,255,255,0.7);">Real-time status of our game server infrastructure</p>
    </div>
    
    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Server Name</th>
                    <th>Location/IP</th>
                    <th>Status</th>
                    <th>CPU Usage</th>
                    <th>Memory Usage</th>
                    <th>Disk Usage</th>
                    <th>Uptime</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($server = mysqli_fetch_assoc($result)): ?>
                    <?php
                    // Determine status
                    $status = $server['status'] ?? 'unknown';
                    if ($server['enabled'] == 0) {
                        $status = 'maintenance';
                    }
                    
                    // Status styling
                    $status_class = '';
                    $status_display = ucfirst($status);
                    switch ($status) {
                        case 'online':
                            $status_class = 'status-online';
                            break;
                        case 'offline':
                            $status_class = 'status-offline';
                            break;
                        case 'maintenance':
                            $status_class = 'status-maintenance';
                            break;
                        default:
                            $status_class = 'status-unknown';
                            $status_display = 'Unknown';
                    }
                    
                    // Format last updated
                    $last_updated = 'Never';
                    if ($server['last_updated']) {
                        $timestamp = strtotime($server['last_updated']);
                        $diff = time() - $timestamp;
                        if ($diff < 60) {
                            $last_updated = 'Just now';
                        } elseif ($diff < 3600) {
                            $last_updated = floor($diff / 60) . ' min ago';
                        } elseif ($diff < 86400) {
                            $last_updated = floor($diff / 3600) . ' hours ago';
                        } else {
                            $last_updated = date('M d, Y', $timestamp);
                        }
                    }
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($server['remote_server_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($server['agent_ip'] ?? 'N/A'); ?></td>
                        <td>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo $status_display; ?>
                            </span>
                        </td>
                        <td><?php echo $server['cpu_usage'] ? number_format($server['cpu_usage'], 1) . '%' : 'N/A'; ?></td>
                        <td><?php echo $server['memory_usage'] ? number_format($server['memory_usage'], 1) . '%' : 'N/A'; ?></td>
                        <td><?php echo $server['disk_usage'] ? number_format($server['disk_usage'], 1) . '%' : 'N/A'; ?></td>
                        <td><?php echo htmlspecialchars($server['uptime'] ?? 'N/A'); ?></td>
                        <td><?php echo $last_updated; ?></td>
                    </tr>
                    <?php if (!empty($server['notes'])): ?>
                    <tr>
                        <td colspan="8" style="padding-left:40px;font-size:0.9rem;color:rgba(255,255,255,0.7);">
                            <em><?php echo htmlspecialchars($server['notes']); ?></em>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="panel" style="text-align:center;padding:40px;">
            <p style="font-size:1.2rem;">No server status information available.</p>
        </div>
    <?php endif; ?>
    
    <div style="margin-top:30px;text-align:center;color:rgba(255,255,255,0.6);font-size:0.9rem;">
        <p>Server status is updated automatically every 5 minutes.</p>
        <p style="margin-top:10px;">If you experience any issues, please contact support.</p>
    </div>
</div>

<?php
// Close database connection
mysqli_close($db);
?>

<style>
.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-online {
    background-color: #10b981;
    color: white;
}

.status-offline {
    background-color: #ef4444;
    color: white;
}

.status-maintenance {
    background-color: #f59e0b;
    color: white;
}

.status-unknown {
    background-color: #6b7280;
    color: white;
}
</style>

</body>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</html>
