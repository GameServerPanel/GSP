<?php
// Start a separate session for the website (not the panel session)
session_name("gameservers_website");
session_start();

// We'll compute a site root below (up to /_website) and define a strict sanitizer after config is loaded

// Include database configuration
require_once(__DIR__ . '/includes/config.inc.php');

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

// Check if user is already logged in
if (isset($_SESSION['website_user_id']) && !empty($_SESSION['website_user_id'])) {
    // Determine return path and sanitize it strictly to stay under _website
    // Request values may be url-encoded (from previous urlencode calls); decode first
    $return_to_raw = isset($_REQUEST['return_to']) ? urldecode($_REQUEST['return_to']) : '';
    $sanitized_return = $sanitize_return_path($return_to_raw);
    if ($sanitized_return === '') $sanitized_return = $SITE_ROOT_PATH . '/index.php';

    // Already logged in, redirect to appropriate page
    header('Location: ' . $sanitized_return);
    exit();
}

// Initialize error message
$error_message = '';
$success_message = '';

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['ulogin'] ?? '');
    $password = $_POST['upassword'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } else {
        // Sanitize username to prevent SQL injection
        $username = mysqli_real_escape_string($db, $username);
        
    // Detect if shadow column for modern hash exists, and build a safe SELECT
    $has_shadow = false;
    $res_cols = mysqli_query($db, "SHOW COLUMNS FROM ogp_users LIKE 'users_pass_hash'");
    if ($res_cols && mysqli_num_rows($res_cols) > 0) {
        $has_shadow = true;
    }

    $select_fields = 'user_id, users_login, users_passwd, users_role, users_email';
    if ($has_shadow) $select_fields .= ', users_pass_hash';

    // Query the panel database for the user
    $query = "SELECT $select_fields FROM ogp_users WHERE users_login = '$username'";
    $result = mysqli_query($db, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
            
            // Prefer modern password hash if present (shadow column), otherwise fall back to MD5 and migrate
            $verified = false;
            if (!empty($user['users_pass_hash'])) {
                // verify against modern hash
                if (password_verify($password, $user['users_pass_hash'])) {
                    $verified = true;
                }
            } else {
                // legacy MD5
                if (md5($password) === $user['users_passwd']) {
                    $verified = true;
                    // attempt to migrate: store modern hash if column exists
                    $res = mysqli_query($db, "SHOW COLUMNS FROM ogp_users LIKE 'users_pass_hash'");
                    if ($res && mysqli_num_rows($res) > 0) {
                        $newhash = password_hash($password, PASSWORD_DEFAULT);
                        $safe_user_id = (int)$user['user_id'];
                        $stmt_m = $db->prepare("UPDATE ogp_users SET users_pass_hash = ? WHERE user_id = ?");
                        if ($stmt_m) {
                            $stmt_m->bind_param('si', $newhash, $safe_user_id);
                            $stmt_m->execute();
                            $stmt_m->close();
                        }
                    }
                }
            }

            if ($verified) {
                // Login successful - create website session
                $_SESSION['website_user_id'] = $user['user_id'];
                $_SESSION['website_username'] = $user['users_login'];
                $_SESSION['website_user_role'] = $user['users_role'];
                $_SESSION['website_user_email'] = $user['users_email'];
                $_SESSION['website_login_time'] = time();
                
                $success_message = 'Login successful! Redirecting...';
                
                // Log the login
                logger("Website login successful: " . $user['users_login']);
                
                // Redirect after 2 seconds to the requested return path or index.php, using strict sanitizer
                // POST may contain a raw (not URL-encoded) return_to from the hidden form; decode defensively
                $post_return = isset($_POST['return_to']) ? urldecode($_POST['return_to']) : '';
                $return_candidate = $post_return !== '' ? $post_return : ($return_to_raw ?? '');
                $sanitized_after = $sanitize_return_path($return_candidate ?? '');
                if ($sanitized_after === '') $sanitized_after = $SITE_ROOT_PATH . '/index.php';
                // Use immediate server-side redirect to avoid client-side relative resolution or delays
                header('Location: ' . $sanitized_after);
                exit();
            } else {
                $error_message = 'Invalid username or password.';
                logger("Website login failed - wrong password: $username");
            }
        } else {
            $error_message = 'Invalid username or password.';
            logger("Website login failed - user not found: $username");
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
            <a href="register.php">Register</a>
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
