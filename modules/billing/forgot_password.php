<?php
// Start a separate session for the website
session_name("gameservers_website");
session_start();

// Include database configuration
require_once(__DIR__ . '/includes/config.inc.php');

// Create database connection
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Logger function
function logger($logtext){
    file_put_contents(__DIR__ . "/logfile.txt", $logtext . PHP_EOL, FILE_APPEND);
}

$message = '';
$error = '';

// Process password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_reset'])) {
    $identifier = trim($_POST['identifier'] ?? '');
    
    if (empty($identifier)) {
        $error = 'Please enter your username or email address.';
    } else {
        // Sanitize input
        $identifier = mysqli_real_escape_string($db, $identifier);
        
        // Check if it's an email or username
        $query = "SELECT user_id, users_login, users_email FROM {$table_prefix}users 
                  WHERE users_login = '$identifier' OR users_email = '$identifier' LIMIT 1";
        $result = mysqli_query($db, $query);
        
        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Check if password_reset_tokens table exists
            $table_check = mysqli_query($db, "SHOW TABLES LIKE '{$table_prefix}password_reset_tokens'");
            if (!$table_check || mysqli_num_rows($table_check) === 0) {
                // Create table if it doesn't exist
                $create_table = "CREATE TABLE IF NOT EXISTS {$table_prefix}password_reset_tokens (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    token VARCHAR(64) NOT NULL,
                    expires DATETIME NOT NULL,
                    used TINYINT(1) DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_token (token),
                    INDEX idx_user_id (user_id)
                )";
                mysqli_query($db, $create_table);
            }
            
            // Delete any existing tokens for this user
            $stmt = $db->prepare("DELETE FROM {$table_prefix}password_reset_tokens WHERE user_id = ?");
            $stmt->bind_param('i', $user['user_id']);
            $stmt->execute();
            $stmt->close();
            
            // Insert new token
            $stmt = $db->prepare("INSERT INTO {$table_prefix}password_reset_tokens (user_id, token, expires) VALUES (?, ?, ?)");
            $stmt->bind_param('iss', $user['user_id'], $token, $expires);
            $stmt->execute();
            $stmt->close();
            
            // Build reset link
            $reset_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") 
                        . "://{$_SERVER['HTTP_HOST']}"
                        . dirname($_SERVER['SCRIPT_NAME']) 
                        . "/reset_password.php?token=" . urlencode($token);
            
            // Send email (for now, just show the link - actual email sending requires mail configuration)
            $email_body = "Hello {$user['users_login']},\n\n"
                        . "You requested a password reset. Click the link below to reset your password:\n\n"
                        . "{$reset_link}\n\n"
                        . "This link will expire in 1 hour.\n\n"
                        . "If you did not request this reset, please ignore this email.";
            
            // Attempt to send email
            $headers = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n"
                     . "Reply-To: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n"
                     . "X-Mailer: PHP/" . phpversion();
            
            $email_sent = @mail($user['users_email'], "Password Reset Request", $email_body, $headers);
            
            logger("Password reset requested for user: {$user['users_login']} (email sent: " . ($email_sent ? 'yes' : 'no') . ")");
            
            if ($email_sent) {
                $message = "Password reset instructions have been sent to your email address.";
            } else {
                // If email fails, show the link directly (development mode)
                $message = "Password reset link generated. In production, this would be emailed to you.<br><br>"
                         . "For testing, use this link: <a href='$reset_link'>Reset Password</a>";
            }
        } else {
            // For security, don't reveal if user exists or not
            $message = "If an account exists with that username or email, password reset instructions have been sent.";
            logger("Password reset requested for unknown identifier: $identifier");
        }
    }
}

// Close database connection
mysqli_close($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - GameServers.World</title>
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
    </style>
</head>
<body>
    <?php include(__DIR__ . '/includes/top.php'); ?>
    <?php include(__DIR__ . '/includes/menu.php'); ?>
    <div class="content">
    <div class="reset-container">
        <div class="reset-header">
            <h1>Forgot Password</h1>
            <p>Enter your username or email to reset your password</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="forgot_password.php">
            <div class="form-group">
                <label for="identifier">Username or Email</label>
                <input type="text" id="identifier" name="identifier" required autofocus>
            </div>
            
            <button type="submit" name="request_reset" class="btn-submit">Request Password Reset</button>
        </form>
        
        <div class="footer-links">
            <a href="login.php">Back to Login</a> | 
            <a href="index.php">Home</a>
        </div>
    </div>
    </div>
</body>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</html>
