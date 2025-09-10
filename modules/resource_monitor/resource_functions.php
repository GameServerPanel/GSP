<?php
/*
 * Resource Monitoring Functions for OGP
 * Handles resource data collection, storage, and alerting
 */

require_once('includes/lib_remote.php');

/**
 * Collect resource data from all agents
 */
function collect_all_resources()
{
    global $db;
    
    // Get all active remote servers
    $remote_servers = $db->getRemoteServers();
    
    foreach ($remote_servers as $server) {
        collect_server_resources($server);
    }
}

/**
 * Collect resources from a specific server
 */
function collect_server_resources($server)
{
    global $db;
    
    try {
        $remote = new OGPRemoteLibrary(
            $server['agent_ip'], 
            $server['agent_port'],
            $server['encryption_key'], 
            $server['timeout']
        );
        
        // Collect system-wide resources
        $system_stats = $remote->get_system_resource_usage();
        if ($system_stats && strpos($system_stats, '1;') === 0) {
            $data = parse_resource_string(substr($system_stats, 2));
            store_resource_data($server['remote_server_id'], null, $data);
            check_system_alerts($server, $data);
        }
        
        // Get all game servers on this remote server
        $game_servers = $db->getHomesFor("remote_server_id", $server['remote_server_id']);
        
        foreach ($game_servers as $game_server) {
            $game_stats = $remote->get_gameserver_resource_usage($game_server['home_id']);
            if ($game_stats && strpos($game_stats, '1;') === 0) {
                $data = parse_resource_string(substr($game_stats, 2));
                store_resource_data($server['remote_server_id'], $game_server['home_id'], $data);
                check_gameserver_alerts($server, $game_server, $data);
            }
        }
        
    } catch (Exception $e) {
        error_log("Resource monitoring error for server {$server['remote_server_id']}: " . $e->getMessage());
    }
}

/**
 * Parse resource data string from agent
 */
function parse_resource_string($resource_string)
{
    $data = array();
    $pairs = explode(';', $resource_string);
    
    foreach ($pairs as $pair) {
        if (strpos($pair, '=') !== false) {
            list($key, $value) = explode('=', $pair, 2);
            $data[trim($key)] = trim($value);
        }
    }
    
    return $data;
}

/**
 * Store resource data in database
 */
function store_resource_data($remote_server_id, $home_id, $data)
{
    global $db;
    
    $query = "INSERT INTO ogp_resource_monitoring 
              (remote_server_id, home_id, cpu_usage, memory_usage, memory_used_mb, memory_total_mb, 
               disk_usage, disk_used_mb, disk_total_mb, process_count, network_rx_mb, network_tx_mb) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($query);
    $stmt->execute([
        $remote_server_id,
        $home_id,
        isset($data['cpu_usage']) ? floatval($data['cpu_usage']) : null,
        isset($data['memory_usage']) ? floatval($data['memory_usage']) : null,
        isset($data['memory_used_mb']) ? intval($data['memory_used_mb']) : null,
        isset($data['memory_total_mb']) ? intval($data['memory_total_mb']) : null,
        isset($data['disk_usage']) ? floatval($data['disk_usage']) : null,
        isset($data['disk_used_mb']) ? intval($data['disk_used_mb']) : null,
        isset($data['disk_total_mb']) ? intval($data['disk_total_mb']) : null,
        isset($data['process_count']) ? intval($data['process_count']) : null,
        isset($data['network_rx_mb']) ? intval($data['network_rx_mb']) : null,
        isset($data['network_tx_mb']) ? intval($data['network_tx_mb']) : null
    ]);
}

/**
 * Check for system alerts
 */
function check_system_alerts($server, $data)
{
    global $db;
    
    // Get active alerts for this server (system-wide)
    $query = "SELECT * FROM ogp_resource_alerts 
              WHERE remote_server_id = ? AND home_id IS NULL AND is_active = 1";
    $stmt = $db->prepare($query);
    $stmt->execute([$server['remote_server_id']]);
    $alerts = $stmt->fetchAll();
    
    foreach ($alerts as $alert) {
        $resource_value = null;
        
        switch ($alert['alert_type']) {
            case 'cpu':
                $resource_value = isset($data['cpu_usage']) ? floatval($data['cpu_usage']) : null;
                break;
            case 'memory':
                $resource_value = isset($data['memory_usage']) ? floatval($data['memory_usage']) : null;
                break;
            case 'disk':
                $resource_value = isset($data['disk_usage']) ? floatval($data['disk_usage']) : null;
                break;
        }
        
        if ($resource_value !== null && $resource_value >= $alert['threshold_percentage']) {
            check_alert_duration($alert, $server, null, $resource_value);
        }
    }
}

/**
 * Check for game server alerts
 */
function check_gameserver_alerts($server, $game_server, $data)
{
    global $db;
    
    // Get active alerts for this game server
    $query = "SELECT * FROM ogp_resource_alerts 
              WHERE remote_server_id = ? AND home_id = ? AND is_active = 1";
    $stmt = $db->prepare($query);
    $stmt->execute([$server['remote_server_id'], $game_server['home_id']]);
    $alerts = $stmt->fetchAll();
    
    foreach ($alerts as $alert) {
        $resource_value = null;
        
        switch ($alert['alert_type']) {
            case 'cpu':
                $resource_value = isset($data['cpu_usage']) ? floatval($data['cpu_usage']) : null;
                break;
            case 'memory':
                // For game servers, we calculate percentage based on system total memory
                if (isset($data['memory_used_mb'])) {
                    // Get system total memory for percentage calculation
                    $system_query = "SELECT memory_total_mb FROM ogp_resource_monitoring 
                                   WHERE remote_server_id = ? AND home_id IS NULL 
                                   ORDER BY timestamp DESC LIMIT 1";
                    $system_stmt = $db->prepare($system_query);
                    $system_stmt->execute([$server['remote_server_id']]);
                    $system_memory = $system_stmt->fetch();
                    
                    if ($system_memory && $system_memory['memory_total_mb'] > 0) {
                        $resource_value = (floatval($data['memory_used_mb']) / floatval($system_memory['memory_total_mb'])) * 100;
                    }
                }
                break;
        }
        
        if ($resource_value !== null && $resource_value >= $alert['threshold_percentage']) {
            check_alert_duration($alert, $server, $game_server, $resource_value);
        }
    }
}

/**
 * Check if alert should be triggered based on duration
 */
function check_alert_duration($alert, $server, $game_server, $resource_value)
{
    global $db;
    
    // Get recent resource data to check if threshold has been exceeded for the required duration
    $home_condition = $game_server ? "AND home_id = ?" : "AND home_id IS NULL";
    $params = [$server['remote_server_id']];
    if ($game_server) {
        $params[] = $game_server['home_id'];
    }
    
    $resource_field = '';
    switch ($alert['alert_type']) {
        case 'cpu': $resource_field = 'cpu_usage'; break;
        case 'memory': $resource_field = 'memory_usage'; break;
        case 'disk': $resource_field = 'disk_usage'; break;
    }
    
    $query = "SELECT timestamp, $resource_field 
              FROM ogp_resource_monitoring 
              WHERE remote_server_id = ? $home_condition 
                AND timestamp >= DATE_SUB(NOW(), INTERVAL ? MINUTE)
                AND $resource_field >= ?
              ORDER BY timestamp DESC";
    
    $params[] = $alert['duration_minutes'];
    $params[] = $alert['threshold_percentage'];
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $exceeded_records = $stmt->fetchAll();
    
    // Check if we have enough consecutive records over the threshold
    $duration_exceeded = count($exceeded_records) * 5; // 5-minute intervals
    
    if ($duration_exceeded >= $alert['duration_minutes']) {
        trigger_discord_alert($alert, $server, $game_server, $resource_value, $duration_exceeded);
    }
}

/**
 * Trigger Discord alert
 */
function trigger_discord_alert($alert, $server, $game_server, $resource_value, $duration_exceeded)
{
    global $db;
    
    // Check cooldown to prevent spam
    $cooldown_query = "SELECT setting_value FROM ogp_discord_settings WHERE setting_name = 'alert_cooldown_minutes'";
    $cooldown_result = $db->query($cooldown_query)->fetch();
    $cooldown_minutes = $cooldown_result ? intval($cooldown_result['setting_value']) : 60;
    
    if ($alert['last_triggered']) {
        $last_triggered_time = strtotime($alert['last_triggered']);
        $cooldown_time = $last_triggered_time + ($cooldown_minutes * 60);
        if (time() < $cooldown_time) {
            return; // Still in cooldown
        }
    }
    
    // Get Discord settings
    $webhook_url = $alert['discord_webhook_url'];
    if (!$webhook_url) {
        $default_webhook = $db->query("SELECT setting_value FROM ogp_discord_settings WHERE setting_name = 'default_webhook_url'")->fetch();
        $webhook_url = $default_webhook ? $default_webhook['setting_value'] : '';
    }
    
    if (!$webhook_url) {
        error_log("No Discord webhook URL configured for alert ID {$alert['id']}");
        return;
    }
    
    // Create alert message
    $server_name = $server['agent_name'] ?: $server['agent_ip'];
    $target = $game_server ? "Game Server (Home ID: {$game_server['home_id']})" : "System";
    
    $message = [
        'username' => 'OGP Resource Monitor',
        'embeds' => [
            [
                'title' => '🚨 Resource Alert Triggered',
                'color' => 15158332, // Red color
                'fields' => [
                    [
                        'name' => 'Server',
                        'value' => $server_name,
                        'inline' => true
                    ],
                    [
                        'name' => 'Target',
                        'value' => $target,
                        'inline' => true
                    ],
                    [
                        'name' => 'Resource Type',
                        'value' => strtoupper($alert['alert_type']),
                        'inline' => true
                    ],
                    [
                        'name' => 'Current Usage',
                        'value' => sprintf('%.2f%%', $resource_value),
                        'inline' => true
                    ],
                    [
                        'name' => 'Threshold',
                        'value' => sprintf('%.2f%%', $alert['threshold_percentage']),
                        'inline' => true
                    ],
                    [
                        'name' => 'Duration Exceeded',
                        'value' => sprintf('%d minutes', $duration_exceeded),
                        'inline' => true
                    ]
                ],
                'timestamp' => date('c'),
                'footer' => [
                    'text' => 'OGP Resource Monitoring System'
                ]
            ]
        ]
    ];
    
    // Send Discord message
    $discord_response = send_discord_webhook($webhook_url, $message);
    
    // Update alert last_triggered time
    $update_query = "UPDATE ogp_resource_alerts SET last_triggered = NOW() WHERE id = ?";
    $db->prepare($update_query)->execute([$alert['id']]);
    
    // Log alert in history
    $history_query = "INSERT INTO ogp_resource_alert_history 
                      (alert_id, remote_server_id, home_id, alert_type, triggered_value, 
                       threshold_value, duration_exceeded, message_sent, discord_response) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $db->prepare($history_query)->execute([
        $alert['id'],
        $server['remote_server_id'],
        $game_server ? $game_server['home_id'] : null,
        $alert['alert_type'],
        $resource_value,
        $alert['threshold_percentage'],
        $duration_exceeded,
        $discord_response ? 1 : 0,
        $discord_response
    ]);
}

/**
 * Send Discord webhook message
 */
function send_discord_webhook($webhook_url, $message)
{
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $webhook_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code >= 200 && $http_code < 300) {
        return "Success: HTTP $http_code";
    } else {
        error_log("Discord webhook failed: HTTP $http_code, Response: $response");
        return "Error: HTTP $http_code";
    }
}

/**
 * Clean up old monitoring data
 */
function cleanup_old_data()
{
    global $db;
    
    // Delete monitoring data older than 30 days
    $monitoring_query = "DELETE FROM ogp_resource_monitoring WHERE timestamp < DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $db->query($monitoring_query);
    
    // Delete alert history older than 90 days
    $history_query = "DELETE FROM ogp_resource_alert_history WHERE triggered_at < DATE_SUB(NOW(), INTERVAL 90 DAY)";
    $db->query($history_query);
}

/**
 * API endpoint for collecting resources (called by cron)
 */
function collect_resources_api()
{
    // Verify this is being called appropriately (could add API key check here)
    collect_all_resources();
    cleanup_old_data();
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Resource collection completed']);
    exit;
}

/**
 * Show dashboard with current resource status
 */
function show_dashboard()
{
    global $db, $view;
    
    echo "<h2>Resource Monitoring Dashboard</h2>";
    
    // Get recent resource data for all servers
    $query = "SELECT r.*, s.agent_name, s.agent_ip 
              FROM ogp_resource_monitoring r 
              LEFT JOIN ogp_remote_servers s ON r.remote_server_id = s.remote_server_id 
              WHERE r.timestamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
                AND r.home_id IS NULL
              ORDER BY r.timestamp DESC";
    
    $result = $db->query($query);
    $servers_data = $result->fetchAll();
    
    if (empty($servers_data)) {
        echo "<p>No recent resource data available. Make sure the monitoring system is configured and running.</p>";
        echo "<a href='?m=resource_monitor&type=configure' class='btn btn-primary'>Configure Monitoring</a>";
        return;
    }
    
    echo "<div class='row'>";
    
    // Group by server
    $servers = [];
    foreach ($servers_data as $data) {
        $server_id = $data['remote_server_id'];
        if (!isset($servers[$server_id])) {
            $servers[$server_id] = [
                'info' => $data,
                'latest' => null
            ];
        }
        if (!$servers[$server_id]['latest'] || $data['timestamp'] > $servers[$server_id]['latest']['timestamp']) {
            $servers[$server_id]['latest'] = $data;
        }
    }
    
    foreach ($servers as $server_id => $server) {
        $data = $server['latest'];
        $server_name = $data['agent_name'] ?: $data['agent_ip'];
        
        echo "<div class='col-md-4'>";
        echo "<div class='card'>";
        echo "<div class='card-header'><h5>{$server_name}</h5></div>";
        echo "<div class='card-body'>";
        
        // CPU Usage
        $cpu_class = $data['cpu_usage'] >= 80 ? 'danger' : ($data['cpu_usage'] >= 60 ? 'warning' : 'success');
        echo "<p><strong>CPU:</strong> <span class='badge badge-{$cpu_class}'>" . number_format($data['cpu_usage'], 1) . "%</span></p>";
        
        // Memory Usage  
        $mem_class = $data['memory_usage'] >= 80 ? 'danger' : ($data['memory_usage'] >= 60 ? 'warning' : 'success');
        echo "<p><strong>Memory:</strong> <span class='badge badge-{$mem_class}'>" . number_format($data['memory_usage'], 1) . "%</span>";
        echo " (" . number_format($data['memory_used_mb']/1024, 1) . "GB / " . number_format($data['memory_total_mb']/1024, 1) . "GB)</p>";
        
        // Disk Usage
        $disk_class = $data['disk_usage'] >= 80 ? 'danger' : ($data['disk_usage'] >= 60 ? 'warning' : 'success');
        echo "<p><strong>Disk:</strong> <span class='badge badge-{$disk_class}'>" . number_format($data['disk_usage'], 1) . "%</span>";
        echo " (" . number_format($data['disk_used_mb']/1024, 1) . "GB / " . number_format($data['disk_total_mb']/1024, 1) . "GB)</p>";
        
        echo "<p><small>Last Updated: " . date('Y-m-d H:i:s', strtotime($data['timestamp'])) . "</small></p>";
        
        echo "</div></div></div>";
    }
    
    echo "</div>";
    
    echo "<div class='mt-4'>";
    echo "<a href='?m=resource_monitor&type=history' class='btn btn-info'>View History</a> ";
    echo "<a href='?m=resource_monitor&type=alerts' class='btn btn-warning'>Manage Alerts</a> ";
    echo "<a href='?m=resource_monitor&type=configure' class='btn btn-secondary'>Configuration</a>";
    echo "</div>";
}

/**
 * Show configuration page
 */
function show_configuration()
{
    echo "<h2>Resource Monitoring Configuration</h2>";
    
    echo "<div class='alert alert-info'>";
    echo "<h5>Setup Instructions:</h5>";
    echo "<ol>";
    echo "<li>Add this to your server's crontab to collect data every 5 minutes:<br>";
    echo "<code>*/5 * * * * curl -s " . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "?m=resource_monitor&type=api_collect</code></li>";
    echo "<li>Configure Discord webhook URLs in the alerts section</li>";
    echo "<li>Set up alert thresholds for your servers and game instances</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<h4>Monitoring Status</h4>";
    
    // Check if we have recent data
    global $db;
    $recent_data = $db->query("SELECT COUNT(*) as count FROM ogp_resource_monitoring WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)")->fetch();
    
    if ($recent_data['count'] > 0) {
        echo "<div class='alert alert-success'>✅ Monitoring is active - " . $recent_data['count'] . " records in the last 10 minutes</div>";
    } else {
        echo "<div class='alert alert-warning'>⚠️ No recent monitoring data found. Please set up the cron job.</div>";
    }
}

/**
 * Show resource history with charts
 */
function show_history()
{
    global $db;
    
    echo "<h2>Resource History</h2>";
    
    // Simple table view for now - could be enhanced with charts later
    $query = "SELECT r.*, s.agent_name, s.agent_ip, h.home_name
              FROM ogp_resource_monitoring r 
              LEFT JOIN ogp_remote_servers s ON r.remote_server_id = s.remote_server_id 
              LEFT JOIN ogp_server_homes h ON r.home_id = h.home_id
              WHERE r.timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
              ORDER BY r.timestamp DESC
              LIMIT 100";
    
    $result = $db->query($query);
    $history_data = $result->fetchAll();
    
    echo "<table class='table table-striped'>";
    echo "<thead><tr><th>Time</th><th>Server</th><th>Target</th><th>CPU %</th><th>Memory %</th><th>Disk %</th></tr></thead>";
    echo "<tbody>";
    
    foreach ($history_data as $data) {
        $server_name = $data['agent_name'] ?: $data['agent_ip'];
        $target = $data['home_id'] ? "Game Server ({$data['home_name']})" : "System";
        
        echo "<tr>";
        echo "<td>" . date('m-d H:i', strtotime($data['timestamp'])) . "</td>";
        echo "<td>{$server_name}</td>";
        echo "<td>{$target}</td>";
        echo "<td>" . ($data['cpu_usage'] ? number_format($data['cpu_usage'], 1) . "%" : "-") . "</td>";
        echo "<td>" . ($data['memory_usage'] ? number_format($data['memory_usage'], 1) . "%" : "-") . "</td>";
        echo "<td>" . ($data['disk_usage'] ? number_format($data['disk_usage'], 1) . "%" : "-") . "</td>";
        echo "</tr>";
    }
    
    echo "</tbody></table>";
}

/**
 * Manage alerts configuration
 */
function manage_alerts()
{
    global $db;
    
    if (isset($_POST['add_alert'])) {
        add_alert();
    }
    
    if (isset($_POST['update_discord_settings'])) {
        update_discord_settings();
    }
    
    echo "<h2>Alert Management</h2>";
    
    // Discord settings form
    echo "<h4>Discord Settings</h4>";
    echo "<form method='post'>";
    
    $discord_settings = get_discord_settings();
    
    echo "<div class='form-group'>";
    echo "<label>Default Webhook URL:</label>";
    echo "<input type='url' name='webhook_url' class='form-control' value='" . htmlspecialchars($discord_settings['default_webhook_url']) . "' placeholder='https://discord.com/api/webhooks/...'>";
    echo "</div>";
    
    echo "<div class='form-group'>";
    echo "<label>Bot Username:</label>";
    echo "<input type='text' name='bot_username' class='form-control' value='" . htmlspecialchars($discord_settings['bot_username']) . "'>";
    echo "</div>";
    
    echo "<button type='submit' name='update_discord_settings' class='btn btn-primary'>Update Discord Settings</button>";
    echo "</form>";
    
    echo "<hr>";
    
    // Show existing alerts
    echo "<h4>Current Alerts</h4>";
    $alerts = $db->query("SELECT a.*, s.agent_name, s.agent_ip FROM ogp_resource_alerts a LEFT JOIN ogp_remote_servers s ON a.remote_server_id = s.remote_server_id ORDER BY a.created_at DESC")->fetchAll();
    
    if (empty($alerts)) {
        echo "<p>No alerts configured yet.</p>";
    } else {
        echo "<table class='table'>";
        echo "<thead><tr><th>Server</th><th>Type</th><th>Threshold</th><th>Duration</th><th>Active</th><th>Last Triggered</th></tr></thead>";
        echo "<tbody>";
        
        foreach ($alerts as $alert) {
            $server_name = $alert['agent_name'] ?: $alert['agent_ip'];
            $target = $alert['home_id'] ? "Game Server (ID: {$alert['home_id']})" : "System";
            
            echo "<tr>";
            echo "<td>{$server_name}<br><small>{$target}</small></td>";
            echo "<td>" . strtoupper($alert['alert_type']) . "</td>";
            echo "<td>{$alert['threshold_percentage']}%</td>";
            echo "<td>{$alert['duration_minutes']} min</td>";
            echo "<td>" . ($alert['is_active'] ? "✅" : "❌") . "</td>";
            echo "<td>" . ($alert['last_triggered'] ? date('Y-m-d H:i', strtotime($alert['last_triggered'])) : "Never") . "</td>";
            echo "</tr>";
        }
        
        echo "</tbody></table>";
    }
    
    echo "<hr>";
    
    // Add new alert form
    echo "<h4>Add New Alert</h4>";
    echo "<form method='post'>";
    
    echo "<div class='row'>";
    echo "<div class='col-md-4'>";
    echo "<label>Server:</label>";
    echo "<select name='remote_server_id' class='form-control' required>";
    $servers = $db->query("SELECT remote_server_id, agent_name, agent_ip FROM ogp_remote_servers ORDER BY agent_name, agent_ip")->fetchAll();
    foreach ($servers as $server) {
        $name = $server['agent_name'] ?: $server['agent_ip'];
        echo "<option value='{$server['remote_server_id']}'>{$name}</option>";
    }
    echo "</select>";
    echo "</div>";
    
    echo "<div class='col-md-4'>";
    echo "<label>Alert Type:</label>";
    echo "<select name='alert_type' class='form-control' required>";
    echo "<option value='cpu'>CPU Usage</option>";
    echo "<option value='memory'>Memory Usage</option>";
    echo "<option value='disk'>Disk Usage</option>";
    echo "</select>";
    echo "</div>";
    
    echo "<div class='col-md-4'>";
    echo "<label>Threshold (%):</label>";
    echo "<input type='number' name='threshold' class='form-control' value='80' min='1' max='100' required>";
    echo "</div>";
    echo "</div>";
    
    echo "<div class='row mt-2'>";
    echo "<div class='col-md-4'>";
    echo "<label>Duration (minutes):</label>";
    echo "<input type='number' name='duration' class='form-control' value='30' min='5' required>";
    echo "</div>";
    
    echo "<div class='col-md-8'>";
    echo "<label>Webhook URL (optional - uses default if empty):</label>";
    echo "<input type='url' name='webhook_url' class='form-control' placeholder='https://discord.com/api/webhooks/...'>";
    echo "</div>";
    echo "</div>";
    
    echo "<div class='mt-3'>";
    echo "<button type='submit' name='add_alert' class='btn btn-success'>Add Alert</button>";
    echo "</div>";
    
    echo "</form>";
}

function get_discord_settings()
{
    global $db;
    
    $settings = [];
    $result = $db->query("SELECT setting_name, setting_value FROM ogp_discord_settings");
    
    while ($row = $result->fetch()) {
        $settings[$row['setting_name']] = $row['setting_value'];
    }
    
    return $settings;
}

function update_discord_settings()
{
    global $db;
    
    $settings = [
        'default_webhook_url' => $_POST['webhook_url'],
        'bot_username' => $_POST['bot_username']
    ];
    
    foreach ($settings as $name => $value) {
        $stmt = $db->prepare("UPDATE ogp_discord_settings SET setting_value = ? WHERE setting_name = ?");
        $stmt->execute([$value, $name]);
    }
    
    echo "<div class='alert alert-success'>Discord settings updated successfully!</div>";
}

function add_alert()
{
    global $db;
    
    $stmt = $db->prepare("INSERT INTO ogp_resource_alerts (remote_server_id, alert_type, threshold_percentage, duration_minutes, discord_webhook_url) VALUES (?, ?, ?, ?, ?)");
    
    $webhook_url = !empty($_POST['webhook_url']) ? $_POST['webhook_url'] : null;
    
    $stmt->execute([
        $_POST['remote_server_id'],
        $_POST['alert_type'],
        $_POST['threshold'],
        $_POST['duration'],
        $webhook_url
    ]);
    
    echo "<div class='alert alert-success'>Alert added successfully!</div>";
}

?>