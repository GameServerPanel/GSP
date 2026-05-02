<?php
// Start a separate session for the website
session_name("opengamepanel_web");
session_start();

// Include database configuration
require_once(__DIR__ . '/bootstrap.php');

// Variables from config.inc.php (helps IDEs understand scope)
/** @var string $db_host Database host */
/** @var string $db_user Database user */
/** @var string $db_pass Database password */
/** @var string $db_name Database name */
/** @var string $table_prefix Table prefix for database tables */

// Create database connection
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name, isset($db_port) ? (int)$db_port : null);
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Logger function
function logger($logtext){
    file_put_contents(__DIR__ . "/logfile.txt", $logtext . PHP_EOL, FILE_APPEND);
}

$message = '';
$error = '';
$token_valid = false;
$user_id = null;

// Get token from URL
$token = isset($_GET['token']) ? trim($_GET['token']) : '';

if (empty($token)) {
    $error = 'Invalid or missing reset token.';
} else {
    // Sanitize token
    $token = mysqli_real_escape_string($db, $token);
    
    // Verify token
    $query = "SELECT user_id, expires, used FROM {$table_prefix}password_reset_tokens 
              WHERE token = '$token' LIMIT 1";
    $result = mysqli_query($db, $query);
    
    if ($result && mysqli_num_rows($result) === 1) {
        $token_data = mysqli_fetch_assoc($result);
        
        // Check if token is expired or used
        if ($token_data['used'] == 1) {
            $error = 'This password reset link has already been used.';
        } elseif (strtotime($token_data['expires']) < time()) {
            $error = 'This password reset link has expired. Please request a new one.';
        } else {
            $token_valid = true;
            $user_id = $token_data['user_id'];
        }
    } else {
        $error = 'Invalid password reset token.';
    }
}

// Process password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password']) && $token_valid) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($new_password) || empty($confirm_password)) {
        $error = 'Please enter and confirm your new password.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($new_password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } else {
        // Hash the password (MD5 for panel compatibility, with modern hash shadow if column exists)
        $md5_password = md5($new_password);
        
        // Check if shadow column exists
        $has_shadow = false;
        $res_cols = mysqli_query($db, "SHOW COLUMNS FROM {$table_prefix}users LIKE 'users_pass_hash'");
        if ($res_cols && mysqli_num_rows($res_cols) > 0) {
            $has_shadow = true;
        }
        
        // Update password
        if ($has_shadow) {
            $modern_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE {$table_prefix}users SET users_passwd = ?, users_pass_hash = ? WHERE user_id = ?");
            $stmt->bind_param('ssi', $md5_password, $modern_hash, $user_id);
        } else {
            $stmt = $db->prepare("UPDATE {$table_prefix}users SET users_passwd = ? WHERE user_id = ?");
            $stmt->bind_param('si', $md5_password, $user_id);
        }
        
        if ($stmt->execute()) {
            // Mark token as used
            $stmt2 = $db->prepare("UPDATE {$table_prefix}password_reset_tokens SET used = 1 WHERE token = ?");
            $stmt2->bind_param('s', $token);
            $stmt2->execute();
            $stmt2->close();
            
            logger("Password reset completed for user_id: $user_id");
            
            $message = 'Password has been reset successfully. You can now login with your new password.';
            $token_valid = false; // Prevent form from showing again
        } else {
            $error = 'Failed to reset password. Please try again.';
        }
        
        $stmt->close();
    }
}

// Close database connection
            billing_maybe_close_db($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - GameServers.World</title>
<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: block;
            padding: 0;
        }

        .content{
            display:flex;
            align-items:center;
            justify-content:center;
            min-height: calc(100vh - 140px);
            padding:20px;
        }
        
        .reset-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            padding: 40px;
        }
        
        .reset-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .reset-header h1 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 8px;
        }
        
        .reset-header p {
            color: #666;
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        
        .alert-error {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }
        
        .alert-success {
            background-color: #efe;
            border: 1px solid #cfc;
            color: #3c3;
        }
        
        .footer-links {
            margin-top: 24px;
            text-align: center;
        }
        
        .footer-links a {
            color: #667eea;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        .password-requirements {
            font-size: 0.85rem;
            color: #666;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/includes/top.php'); ?>
    <?php include(__DIR__ . '/includes/menu.php'); ?>
    <div class="content">
    <div class="reset-container">
        <div class="reset-header">
            <h1>Reset Password</h1>
            <p>Enter your new password</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($token_valid): ?>
        <form method="POST" action="reset_password.php?token=<?php echo urlencode($token); ?>">
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required autofocus>
                <div class="password-requirements">Must be at least 8 characters long</div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" name="reset_password" class="btn-submit">Reset Password</button>
        </form>
        <?php endif; ?>
        
        <div class="footer-links">
            <a href="login.php">Back to Login</a> | 
            <a href="index.php">Home</a>
        </div>
    </div>
    </div>
</body>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</html>

