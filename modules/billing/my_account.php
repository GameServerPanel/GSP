<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - GameServers.World</title>
</head>
<body>
<?php
// Start session to check login status
if (session_status() === PHP_SESSION_NONE) {
    session_name("gameservers_website");
    session_start();
}

// Enable error display during debugging so runtime errors show in the page
@ini_set('display_errors', 1);
@ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
$is_logged_in = (isset($_SESSION['website_user_id']) && !empty($_SESSION['website_user_id'])) || (isset($_SESSION['website_username']) && !empty($_SESSION['website_username']));

// If not logged in, show login page instead
if (!$is_logged_in) {
    include(__DIR__ . '/login.php');
    exit;
}

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

// (debug markers removed)

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

// Fetch user's orders from billing_orders. Keep this simple: select orders for the user and join service name.
// Avoid joins to remote server fields that do not exist on the orders table.
$servers_query = "SELECT
                                        o.order_id,
                                        o.home_name,
                                        o.status,
                                        o.price,
                                        o.invoice_duration,
                                        o.home_id,
                                        o.end_date,
                                        bs.service_name
                                    FROM ogp_billing_orders o
                                    LEFT JOIN ogp_billing_services bs ON o.service_id = bs.service_id
                                    WHERE o.user_id = $user_id
                                    ORDER BY o.order_id DESC";
$servers_result = mysqli_query($db, $servers_query);

// Debug: Log query execution and errors
if (!$servers_result) {
        error_log("My Account Error - User ID: $user_id, Query failed: " . mysqli_error($db));
} else {
        error_log("My Account Debug - User ID: $user_id, Servers Found: " . mysqli_num_rows($servers_result));
}

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

// Sort invoices by invoice/order id (newest order id first) when available,
// otherwise fall back to timestamp (newest first).
usort($invoices, function($a, $b) {
    $getOrderId = function($inv) {
        if (!empty($inv['invoice']) && is_numeric($inv['invoice'])) return intval($inv['invoice']);
        if (!empty($inv['custom']) && is_numeric($inv['custom'])) return intval($inv['custom']);
        return null;
    };

    $aId = $getOrderId($a);
    $bId = $getOrderId($b);

    if ($aId !== null || $bId !== null) {
        // If either has a numeric order id, prefer numeric comparison (desc)
        if ($aId === $bId) {
            return strtotime($b['ts'] ?? 0) - strtotime($a['ts'] ?? 0);
        }
        if ($aId === null) return 1; // b has id -> b before a
        if ($bId === null) return -1; // a has id -> a before b
        return $bId - $aId; // numeric desc
    }

    // Fallback: newest timestamp first
    return strtotime($b['ts'] ?? 0) - strtotime($a['ts'] ?? 0);
});

// Organize invoices by status
$invoices_by_status = [];
foreach ($invoices as $inv) {
    $status = strtolower($inv['status'] ?? 'pending');
    if (!isset($invoices_by_status[$status])) {
        $invoices_by_status[$status] = [];
    }
    $invoices_by_status[$status][] = $inv;
}

// Define status display order and labels
$status_config = [
    'pending' => ['label' => 'Pending Invoices', 'class' => 'pending'],
    'paid' => ['label' => 'Paid Invoices', 'class' => 'paid'],
    'completed' => ['label' => 'Completed Invoices', 'class' => 'paid'],
    'in-cart' => ['label' => 'In Cart', 'class' => 'pending'],
    'installed' => ['label' => 'Installed/Active', 'class' => 'paid'],
    'expired' => ['label' => 'Expired Invoices', 'class' => 'expired'],
    'cancelled' => ['label' => 'Cancelled Invoices', 'class' => 'expired'],
];

?>

<!-- Debug marker: Page rendering started -->
<div class="site-panel">
    <div class="site-panel-title">
        My Account
        <a href="logout.php" class="gsw-btn" style="float:right;">Logout</a>
    </div>
    
    <!-- Debug: User ID = <?php echo $user_id; ?>, User Info: <?php echo $user_info ? 'Loaded' : 'NULL'; ?> -->
    
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
                <summary class="account-edit-summary">Edit Account Information</summary>
                <form method="POST" class="mt-12">
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
                            <span class="server-detail-label">Location / Home ID:</span>
                            <span class="server-detail-value"><?php echo htmlspecialchars($server['remote_server_name'] ?? $server['home_id'] ?? 'N/A'); ?></span>
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
                            <span class="server-detail-label">Order ID:</span>
                            <span class="server-detail-value">#<?php echo htmlspecialchars($server['order_id'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="server-detail">
                            <span class="server-detail-label">Expires:</span>
                            <span class="server-detail-value"><?php echo !empty($server['end_date']) && $server['end_date'] != '0' ? date('M d, Y', strtotime($server['end_date'])) : 'N/A'; ?></span>
                        </div>
                    </div>
                    <div class="server-actions">
                        <?php
                        // Show Renew action for servers that can be renewed
                        $renewable_statuses = array('paid','installed','invoiced','suspended');
                        if (!empty($server['status']) && in_array(strtolower($server['status']), $renewable_statuses)): ?>
                            <a href="renew_server.php?order_id=<?php echo intval($server['order_id']); ?>" class="gsw-btn renew-btn">Renew</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-data">
                <p>You don't have any game servers yet.</p>
                <a href="serverlist.php" class="gsw-btn mt-10">Browse Game Servers</a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Invoices Section - Organized by Status -->
    <?php if (!empty($invoices_by_status)): ?>
        <?php foreach ($status_config as $status_key => $status_info): ?>
            <?php if (isset($invoices_by_status[$status_key]) && !empty($invoices_by_status[$status_key])): ?>
                <div class="account-section">
                    <h2><?php echo htmlspecialchars($status_info['label']); ?></h2>
                    <?php foreach ($invoices_by_status[$status_key] as $invoice): ?>
                        <div class="invoice-item">
                            <div>
                                <div class="invoice-id">Invoice #<?php echo htmlspecialchars($invoice['invoice'] ?? $invoice['custom'] ?? 'N/A'); ?></div>
                                <div class="invoice-date"><?php echo htmlspecialchars($invoice['ts'] ?? 'N/A'); ?></div>
                            </div>
                            <div>
                                <span class="invoice-amount"><?php echo htmlspecialchars(($invoice['currency'] ?? 'USD') . ' ' . number_format((float)($invoice['amount'] ?? 0), 2)); ?></span>
                                <span class="invoice-status invoice-status-<?php echo htmlspecialchars($status_info['class']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($status_key)); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="account-section">
            <h2>Invoices</h2>
            <div class="no-data">No invoices found.</div>
        </div>
    <?php endif; ?>
</div>

<?php
// Close database connection
mysqli_close($db);
?>

</body>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</html>
