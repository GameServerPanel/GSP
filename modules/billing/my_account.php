<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - GameServers.World</title>
    <style>
        .account-container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .account-section {
            background: rgba(0,0,0,0.25);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .account-section h2 {
            margin: 0 0 15px 0;
            font-size: 1.3rem;
            color: #fff;
            border-bottom: 2px solid rgba(255,255,255,0.1);
            padding-bottom: 10px;
        }
        
        .account-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .account-info-item {
            padding: 10px;
            background: rgba(255,255,255,0.03);
            border-radius: 6px;
        }
        
        .account-info-label {
            font-weight: 600;
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .account-info-value {
            color: #fff;
            font-size: 1rem;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #fff;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 6px;
            background: rgba(0,0,0,0.3);
            color: #fff;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        
        .alert-error {
            background-color: rgba(255,0,0,0.2);
            border: 1px solid rgba(255,0,0,0.3);
            color: #ffcccc;
        }
        
        .alert-success {
            background-color: rgba(0,255,0,0.2);
            border: 1px solid rgba(0,255,0,0.3);
            color: #ccffcc;
        }
        
        .server-item {
            background: rgba(255,255,255,0.03);
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 10px;
            border-left: 3px solid #667eea;
        }
        
        .server-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 8px;
        }
        
        .server-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .server-detail {
            font-size: 0.9rem;
        }
        
        .server-detail-label {
            color: rgba(255,255,255,0.6);
        }
        
        .server-detail-value {
            color: #fff;
            font-weight: 500;
        }
        
        .invoice-item {
            background: rgba(255,255,255,0.03);
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .invoice-id {
            font-weight: 600;
            color: #fff;
        }
        
        .invoice-amount {
            color: #10b981;
            font-weight: 600;
        }
        
        .invoice-status {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .invoice-status-paid {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }
        
        .invoice-status-pending {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }
        
        .invoice-date {
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: rgba(255,255,255,0.6);
        }
        
        @media (max-width: 768px) {
            .account-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<?php
// Require login for this page
require_once(__DIR__ . '/includes/login_required.php');

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

// Initialize messages
$error_message = '';
$success_message = '';

// Get user ID from session
$user_id = intval($_SESSION['website_user_id'] ?? 0);

// Fetch user information from database
$user_info = null;
if ($user_id > 0) {
    $query = "SELECT user_id, users_login, users_email, users_fname, users_lname FROM ogp_users WHERE user_id = $user_id LIMIT 1";
    $result = mysqli_query($db, $query);
    if ($result && mysqli_num_rows($result) === 1) {
        $user_info = mysqli_fetch_assoc($result);
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = 'All password fields are required.';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'New passwords do not match.';
    } elseif (strlen($new_password) < 6) {
        $error_message = 'New password must be at least 6 characters long.';
    } else {
        // Verify current password (using MD5 as per panel legacy)
        $current_hash = md5($current_password);
        $verify_query = "SELECT user_id FROM ogp_users WHERE user_id = $user_id AND users_passwd = '$current_hash' LIMIT 1";
        $verify_result = mysqli_query($db, $verify_query);
        
        if ($verify_result && mysqli_num_rows($verify_result) === 1) {
            // Update password
            $new_hash = md5($new_password);
            $update_query = "UPDATE ogp_users SET users_passwd = '$new_hash' WHERE user_id = $user_id LIMIT 1";
            if (mysqli_query($db, $update_query)) {
                $success_message = 'Password changed successfully!';
            } else {
                $error_message = 'Failed to update password. Please try again.';
            }
        } else {
            $error_message = 'Current password is incorrect.';
        }
    }
}

// Handle account info update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    $fname = mysqli_real_escape_string($db, trim($_POST['fname'] ?? ''));
    $lname = mysqli_real_escape_string($db, trim($_POST['lname'] ?? ''));
    $email = mysqli_real_escape_string($db, trim($_POST['email'] ?? ''));
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Invalid email address.';
    } else {
        $update_query = "UPDATE ogp_users SET users_fname = '$fname', users_lname = '$lname', users_email = '$email' WHERE user_id = $user_id LIMIT 1";
        if (mysqli_query($db, $update_query)) {
            $success_message = 'Account information updated successfully!';
            // Refresh user info
            $query = "SELECT user_id, users_login, users_email, users_fname, users_lname FROM ogp_users WHERE user_id = $user_id LIMIT 1";
            $result = mysqli_query($db, $query);
            if ($result && mysqli_num_rows($result) === 1) {
                $user_info = mysqli_fetch_assoc($result);
            }
        } else {
            $error_message = 'Failed to update account information. Please try again.';
        }
    }
}

// Fetch user's game servers from billing_orders
$servers_query = "SELECT 
                    o.order_id,
                    o.home_name,
                    o.status,
                    o.price,
                    o.invoice_duration,
                    o.created_at,
                    bs.service_name,
                    rs.remote_server_name
                  FROM ogp_billing_orders o
                  LEFT JOIN ogp_billing_services bs ON o.service_id = bs.service_id
                  LEFT JOIN ogp_remote_servers rs ON o.remote_server_id = rs.remote_server_id
                  WHERE o.user_id = $user_id
                  ORDER BY o.created_at DESC";
$servers_result = mysqli_query($db, $servers_query);

// Fetch invoices (from data directory JSON files)
$dataDir = (isset($SITE_DATA_DIR) && $SITE_DATA_DIR) ? $SITE_DATA_DIR : realpath(__DIR__ . '/') . DIRECTORY_SEPARATOR . 'data';
$invoices = [];
if (is_dir($dataDir)) {
    foreach (glob($dataDir . '/*.json') as $file) {
        $j = json_decode(file_get_contents($file), true);
        if (!$j || !is_array($j)) continue;
        
        // Try to match by user email or user_id in custom field
        $match = false;
        if ($user_info && !empty($user_info['users_email'])) {
            if (!empty($j['payer']) && stripos($j['payer'], $user_info['users_email']) !== false) $match = true;
            if (!$match && !empty($j['custom']) && stripos($j['custom'], $user_info['users_email']) !== false) $match = true;
        }
        
        if ($match) {
            $invoices[] = $j;
        }
    }
}

// Sort invoices by date (newest first)
usort($invoices, function($a, $b) {
    return strtotime($b['ts'] ?? 0) - strtotime($a['ts'] ?? 0);
});

// Separate current (pending) and previous (paid) invoices
$current_invoices = array_filter($invoices, function($inv) {
    return strtolower($inv['status'] ?? '') === 'pending' || empty($inv['status']);
});
$previous_invoices = array_filter($invoices, function($inv) {
    return strtolower($inv['status'] ?? '') === 'paid' || strtolower($inv['status'] ?? '') === 'completed';
});

?>

<div class="account-container">
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
    
    <!-- Account Information Section -->
    <div class="account-section">
        <h2>Account Information</h2>
        <?php if ($user_info): ?>
            <div class="account-info-grid">
                <div class="account-info-item">
                    <div class="account-info-label">Username</div>
                    <div class="account-info-value"><?php echo htmlspecialchars($user_info['users_login'] ?? 'N/A'); ?></div>
                </div>
                <div class="account-info-item">
                    <div class="account-info-label">Email</div>
                    <div class="account-info-value"><?php echo htmlspecialchars($user_info['users_email'] ?? 'N/A'); ?></div>
                </div>
                <div class="account-info-item">
                    <div class="account-info-label">First Name</div>
                    <div class="account-info-value"><?php echo htmlspecialchars($user_info['users_fname'] ?? 'N/A'); ?></div>
                </div>
                <div class="account-info-item">
                    <div class="account-info-label">Last Name</div>
                    <div class="account-info-value"><?php echo htmlspecialchars($user_info['users_lname'] ?? 'N/A'); ?></div>
                </div>
            </div>
            
            <!-- Edit Account Information Form -->
            <details>
                <summary style="cursor: pointer; color: #667eea; font-weight: 600; margin-top: 10px;">Edit Account Information</summary>
                <form method="POST" style="margin-top: 15px;">
                    <div class="form-group">
                        <label for="fname">First Name</label>
                        <input type="text" id="fname" name="fname" value="<?php echo htmlspecialchars($user_info['users_fname'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="lname">Last Name</label>
                        <input type="text" id="lname" name="lname" value="<?php echo htmlspecialchars($user_info['users_lname'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_info['users_email'] ?? ''); ?>" required>
                    </div>
                    <button type="submit" name="update_info" class="btn-primary">Update Information</button>
                </form>
            </details>
        <?php else: ?>
            <div class="no-data">Unable to load account information.</div>
        <?php endif; ?>
    </div>
    
    <!-- Change Password Section -->
    <div class="account-section">
        <h2>Change Password</h2>
        <form method="POST">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" name="change_password" class="btn-primary">Change Password</button>
        </form>
    </div>
    
    <!-- My Game Servers Section -->
    <div class="account-section">
        <h2>My Game Servers</h2>
        <?php if ($servers_result && mysqli_num_rows($servers_result) > 0): ?>
            <?php while ($server = mysqli_fetch_assoc($servers_result)): ?>
                <div class="server-item">
                    <div class="server-name"><?php echo htmlspecialchars($server['home_name'] ?? 'Unnamed Server'); ?></div>
                    <div class="server-details">
                        <div class="server-detail">
                            <span class="server-detail-label">Game:</span>
                            <span class="server-detail-value"><?php echo htmlspecialchars($server['service_name'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="server-detail">
                            <span class="server-detail-label">Location:</span>
                            <span class="server-detail-value"><?php echo htmlspecialchars($server['remote_server_name'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="server-detail">
                            <span class="server-detail-label">Status:</span>
                            <span class="server-detail-value"><?php echo htmlspecialchars(ucfirst($server['status'] ?? 'pending')); ?></span>
                        </div>
                        <div class="server-detail">
                            <span class="server-detail-label">Price:</span>
                            <span class="server-detail-value">$<?php echo number_format($server['price'] ?? 0, 2); ?>/<?php echo htmlspecialchars($server['invoice_duration'] ?? 'month'); ?></span>
                        </div>
                        <div class="server-detail">
                            <span class="server-detail-label">Created:</span>
                            <span class="server-detail-value"><?php echo $server['created_at'] ? date('M d, Y', strtotime($server['created_at'])) : 'N/A'; ?></span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-data">
                <p>You don't have any game servers yet.</p>
                <a href="serverlist.php" class="btn-primary" style="display: inline-block; margin-top: 10px;">Browse Game Servers</a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Current Invoices Due Section -->
    <?php if (!empty($current_invoices)): ?>
    <div class="account-section">
        <h2>Current Invoices Due</h2>
        <?php foreach ($current_invoices as $invoice): ?>
            <div class="invoice-item">
                <div>
                    <div class="invoice-id">Invoice #<?php echo htmlspecialchars($invoice['invoice'] ?? $invoice['custom'] ?? 'N/A'); ?></div>
                    <div class="invoice-date"><?php echo htmlspecialchars($invoice['ts'] ?? 'N/A'); ?></div>
                </div>
                <div>
                    <span class="invoice-amount"><?php echo htmlspecialchars(($invoice['currency'] ?? 'USD') . ' ' . number_format((float)($invoice['amount'] ?? 0), 2)); ?></span>
                    <span class="invoice-status invoice-status-pending">Pending</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Previous Invoices Section -->
    <div class="account-section">
        <h2>Previous Invoices</h2>
        <?php if (!empty($previous_invoices)): ?>
            <?php foreach ($previous_invoices as $invoice): ?>
                <div class="invoice-item">
                    <div>
                        <div class="invoice-id">Invoice #<?php echo htmlspecialchars($invoice['invoice'] ?? $invoice['custom'] ?? 'N/A'); ?></div>
                        <div class="invoice-date"><?php echo htmlspecialchars($invoice['ts'] ?? 'N/A'); ?></div>
                    </div>
                    <div>
                        <span class="invoice-amount"><?php echo htmlspecialchars(($invoice['currency'] ?? 'USD') . ' ' . number_format((float)($invoice['amount'] ?? 0), 2)); ?></span>
                        <span class="invoice-status invoice-status-paid">Paid</span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-data">No previous invoices found.</div>
        <?php endif; ?>
    </div>
</div>

<?php
// Close database connection
mysqli_close($db);
?>

</body>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</html>
