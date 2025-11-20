<?php
// Start a separate session for the website (not the panel session)
session_name("opengamepanel_web");
session_start();
// Enable error display for debugging the white screen issue. Remove or gate in production.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// We'll compute a site root below (up to /_website) and define a strict sanitizer after config is loaded

// Include billing bootstrap (loads database configuration)
require_once(__DIR__ . '/bootstrap.php');
require_once(__DIR__ . '/includes/log.php');

// Variables from config.inc.php (helps IDEs understand scope)
/** @var string $db_host Database host */
/** @var string $db_user Database user */
/** @var string $db_pass Database password */
/** @var string $db_name Database name */
/** @var string $table_prefix Table prefix for database tables */

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
    $password = $_POST['upassword'] ?? '';
    if ($username === '' || $password === '') {
        $error_message = 'Please enter both a username and password.';
        site_log_warn('login_failed_missing_fields', ['ip'=>$_SERVER['REMOTE_ADDR'] ?? '', 'script'=>$_SERVER['SCRIPT_NAME'] ?? '']);
    } else {
        $safe = mysqli_real_escape_string($db, $username);
        $sql = "SELECT user_id, users_login, users_passwd, users_pass_hash, users_role, users_lang, users_theme FROM {$table_prefix}users WHERE users_login = '$safe' LIMIT 1";
        $res = mysqli_query($db, $sql);
        if ($res && mysqli_num_rows($res) === 1) {
            $row = mysqli_fetch_assoc($res);
            $userId = intval($row['user_id']);
            $legacyHash = $row['users_passwd'] ?? '';
            $modernHash = $row['users_pass_hash'] ?? '';
            $authOk = false;
            if (!empty($modernHash) && function_exists('password_verify')) {
                $authOk = password_verify($password, $modernHash);
            }
            if (!$authOk && !empty($legacyHash)) {
                $authOk = (md5($password) === $legacyHash);
                if ($authOk && function_exists('password_hash')) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $escapedHash = mysqli_real_escape_string($db, $newHash);
                    mysqli_query($db, "UPDATE {$table_prefix}users SET users_pass_hash = '$escapedHash' WHERE user_id = $userId LIMIT 1");
                }
            }
            if ($authOk) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $userId;
                $_SESSION['users_login'] = $row['users_login'] ?? $username;
                $_SESSION['users_passwd'] = $legacyHash;
                $_SESSION['users_group'] = $row['users_role'] ?? 'user';
                $_SESSION['users_lang'] = $row['users_lang'] ?? '';
                $_SESSION['users_theme'] = $row['users_theme'] ?? '';
                $_SESSION['website_user_id'] = $userId;
                $_SESSION['website_username'] = $row['users_login'] ?? $username;
                $_SESSION['website_user_role'] = $row['users_role'] ?? '';
                $_SESSION['website_login_time'] = time();
                require_once(__DIR__ . '/includes/panel_bridge.php');
                $panelCtx = billing_panel_bootstrap();
                if ($panelCtx && isset($panelCtx['db']) && $panelCtx['db'] instanceof OGPDatabase) {
                    $_SESSION['users_api_key'] = $panelCtx['db']->getApiToken($userId);
                } else {
                    $_SESSION['users_api_key'] = $_SESSION['users_api_key'] ?? '';
                }
                site_log_info('login_success', ['username'=>$username, 'ip'=>$_SERVER['REMOTE_ADDR'] ?? '']);
                $returnToParam = $_POST['return_to'] ?? '';
                $destination = $sanitize_return_path($returnToParam);
                if ($destination === '') {
                    $destination = $SITE_ROOT_PATH . '/index.php';
                }
                header('Location: ' . $destination);
                exit();
            }
        }
        $error_message = 'Invalid username or password.';
        site_log_warn('login_failed_invalid_credentials', ['username'=>$username, 'ip'=>$_SERVER['REMOTE_ADDR'] ?? '']);
    }
}

// Keep DB connection open for includes (menu.php may query the DB). The
// connection lifecycle is handled centrally; avoid closing here.
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
            background: #ffffff; /* explicit white */
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.28);
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border: 1px solid rgba(0,0,0,0.06);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            font-size: 1.8rem;
            color: #111111; /* high contrast */
            margin-bottom: 8px;
        }

        .login-header p {
            color: #4b5563; /* neutral dark gray */
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #111111 !important; /* ensure labels are dark and readable (override external styles) */
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.15s, box-shadow 0.15s;
            color: #111111;
            background-color: #ffffff;
            -webkit-appearance: none;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 6px 20px rgba(79,70,229,0.12);
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
                <label>Username</label>
                <input type="text" id="ulogin" name="ulogin" required autofocus>
            </div>
            
            <div class="form-group">
                <label>Password</label>
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

