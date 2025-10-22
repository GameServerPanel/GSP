<?php
// Start a separate session for the website (not the panel session)
session_name("gameservers_website");
session_start();

// Include database connection
require_once('db.php');

// Check if user is already logged in
if (isset($_SESSION['website_user_id']) && !empty($_SESSION['website_user_id'])) {
    // Already logged in, redirect to appropriate page
    header('Location: /');
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
        
        // Query the panel database for the user
        $query = "SELECT user_id, users_login, users_passwd, users_role, users_email FROM ogp_users WHERE users_login = '$username'";
        $result = mysqli_query($db, $query);
        
        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Verify password (panel uses MD5)
            if (md5($password) === $user['users_passwd']) {
                // Login successful - create website session
                $_SESSION['website_user_id'] = $user['user_id'];
                $_SESSION['website_username'] = $user['users_login'];
                $_SESSION['website_user_role'] = $user['users_role'];
                $_SESSION['website_user_email'] = $user['users_email'];
                $_SESSION['website_login_time'] = time();
                
                $success_message = 'Login successful! Redirecting...';
                
                // Log the login
                logger("Website login successful: " . $user['users_login']);
                
                // Redirect after 2 seconds
                header('Refresh: 2; URL=/');
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
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
        
        <form method="POST" action="login.php">
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
        
        <div class="divider">or</div>
        
        <div class="footer-links">
            <a href="/">Back to Home</a> | 
            <a href="../index.php">Panel Login</a>
        </div>
    </div>
</body>
</html>
