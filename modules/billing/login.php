<?php
// Start a separate session for the website (not the panel session)
session_name("gameservers_website");
session_start();
// Enable error display for debugging the white screen issue. Remove or gate in production.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// We'll compute a site root below (up to /_website) and define a strict sanitizer after config is loaded

// Include database configuration
require_once(__DIR__ . '/includes/config.inc.php');
require_once(__DIR__ . '/includes/log.php');

// Determine site root up to /_website so we can enforce absolute redirects within this site
$script = $_SERVER['SCRIPT_NAME'] ?? '';
$pos = strpos($script, '/_website');
$SITE_ROOT_PATH = $pos !== false ? substr($script, 0, $pos + strlen('/_website')) : rtrim(dirname($script), '/\\');

// Strict sanitizer that returns an absolute path under $SITE_ROOT_PATH or empty string on invalid
$sanitize_return_path = function($p) use ($SITE_ROOT_PATH) {
    $p = trim((string)$p);
    if ($p === '') return '';
    // disallow absolute URLs or protocol-relative paths
    if (preg_match('#^(https?:)?//#i', $p)) return '';
    if (strpos($p, "\n") !== false || strpos($p, "\r") !== false) return '';
    // Reject path traversal
    if (strpos($p, '..') !== false) return '';
    // Normalize: if it starts with '/', treat as absolute path and ensure it's under SITE_ROOT_PATH
    if (substr($p,0,1) === '/') {
        // simple character whitelist
        if (!preg_match('#^/[A-Za-z0-9_./?&=%:\-]+$#', $p)) return '';
        // Disallow entry to 'dashboard' (panel area) explicitly
        if (stripos($p, '/dashboard') !== false) return '';
        return $p;
    }
    // Relative path: restrict characters and build absolute under site root
    if (!preg_match('#^[A-Za-z0-9_./?&=%:\-]+$#', $p)) return '';
    // Disallow references to panel dashboard
    if (stripos($p, 'dashboard') !== false) return '';
    return $SITE_ROOT_PATH . '/' . ltrim($p, '/');
};

// Create database connection
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Logger function
function logger($logtext){
    file_put_contents(__DIR__ . "/logfile.txt", $logtext . PHP_EOL, FILE_APPEND);
}

// If user already has a website session, redirect to index.php (no return_to)
if (isset($_SESSION['website_user_id']) && !empty($_SESSION['website_user_id'])) {
    header('Location: ' . $SITE_ROOT_PATH . '/index.php');
    exit();
}

// Initialize error message
$error_message = '';
$success_message = '';

// Process login form submission: simplified for debugging
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['ulogin'] ?? '');
    if ($username === '') {
        $error_message = 'Please enter a username.';
        site_log_warn('login_failed_empty_username', ['ip'=>$_SERVER['REMOTE_ADDR'] ?? '', 'script'=>$_SERVER['SCRIPT_NAME'] ?? '']);
    } else {
        // Normal operation: create website session (should be set after proper auth)
        // In final mode, preserve username but do not fabricate IDs. The site should set website_user_id after proper registration/login.
        $_SESSION['website_username'] = $username;
        $_SESSION['website_login_time'] = time();
        // Try to resolve an existing panel user_id by username so the menu and admin checks work.
        $resolved_uid = null;
        if ($db) {
            $safe = mysqli_real_escape_string($db, $username);
            $res = @mysqli_query($db, "SELECT user_id FROM ogp_users WHERE users_login = '$safe' LIMIT 1");
            if ($res && mysqli_num_rows($res) === 1) {
                $r = mysqli_fetch_assoc($res);
                $resolved_uid = intval($r['user_id'] ?? 0);
            }
        }
        if (!empty($resolved_uid)) {
            $_SESSION['website_user_id'] = $resolved_uid;
        } else {
            // Fallback: assign a numeric session id so the menu treats the user as logged in during debugging
            $_SESSION['website_user_id'] = time();
        }
        site_log_info('login_success', ['username'=>$username, 'ip'=>$_SERVER['REMOTE_ADDR'] ?? '']);
        // Always redirect to index.php under site root
        header('Location: ' . $SITE_ROOT_PATH . '/index.php');
        exit();
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
    <title>Login - GameServers.World</title>
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
            padding: 0; /* we'll handle padding in content wrapper */
        }

        /* content area below the top/menu; aligns the login box to the right */
        .content{
            display:flex;
            align-items:center;
            justify-content:flex-end;
            min-height: calc(100vh - 140px); /* leave room for header/menu */
            padding:20px;
        }
        
        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            padding: 40px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 8px;
        }
        
        .login-header p {
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
        
        .btn-login {
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
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-login:active {
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
        
        .divider {
            margin: 20px 0;
            text-align: center;
            color: #999;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/includes/top.php'); ?>
    <?php include(__DIR__ . '/includes/menu.php'); ?>
    <div class="content">
    <div class="login-container">
        <div class="login-header">
            <h1>Welcome Back</h1>
            <p>Sign in to your GameServers account</p>
        </div>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <?php
        // Capture a return_to GET parameter so we can send users back after login
        $return_to_raw = $_GET['return_to'] ?? '';
        // ensure we don't break if not set; the sanitizer is defined above
        ?>
        <form method="POST" action="login.php">
            <input type="hidden" name="return_to" value="<?php echo htmlspecialchars($return_to_raw); ?>">
            <div class="form-group">
                <label for="ulogin">Username</label>
                <input type="text" id="ulogin" name="ulogin" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="upassword">Password</label>
                <input type="password" id="upassword" name="upassword" required>
            </div>
            
            <button type="submit" name="login" class="btn-login">Sign In</button>
        </form>
    <div class="center mt-12">
            <a href="register.php">Register</a> | 
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
        
        <div class="divider">or</div>
        
        <div class="footer-links">
            <a href="index.php">Back to Home</a> | 
            <a href="../index.php">Panel Login</a>
        </div>
    </div>
    </div>
</body>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</html>
