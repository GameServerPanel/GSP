<?php
session_name("gameservers_website");
session_start();
require_once(__DIR__ . '/includes/config.inc.php');

// Simple registration form (creates a user in {table_prefix}users with MD5 password)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['username']) && !empty($_POST['password'])) {
    $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if ($db) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $email = trim($_POST['email']);

        // basic validation
        if ($username === '' || $password === '' || $email === '') {
            $error = 'All fields are required.';
        } else {
            // Store legacy MD5 for panel compatibility, and also store a modern hash
            $md5pw = md5($password);
            $modern = password_hash($password, PASSWORD_DEFAULT);

            // Try to insert with shadow column if it exists
            $has_shadow = false;
            $res = $db->query("SHOW COLUMNS FROM {$table_prefix}users LIKE 'users_pass_hash'");
            if ($res && $res->num_rows > 0) {
                $has_shadow = true;
            }

            if ($has_shadow) {
                $stmt = $db->prepare("INSERT INTO {$table_prefix}users (users_login, users_passwd, users_pass_hash, users_email, users_role) VALUES (?, ?, ?, ?, 'user')");
                $stmt->bind_param('ssss', $username, $md5pw, $modern, $email);
            } else {
                $stmt = $db->prepare("INSERT INTO {$table_prefix}users (users_login, users_passwd, users_email, users_role) VALUES (?, ?, ?, 'user')");
                $stmt->bind_param('sss', $username, $md5pw, $email);
            }

            if ($stmt->execute()) {
                // Redirect to absolute login URL
                $script = $_SERVER['SCRIPT_NAME'] ?? '';
                $pos = strpos($script, '/_website');
                $siteRoot = $pos !== false ? substr($script, 0, $pos + strlen('/_website')) : rtrim(dirname($script), '/\\');
                header('Location: ' . $siteRoot . '/login.php?registered=1');
                exit;
            } else {
                $error = 'Could not create user. Maybe the name is taken.';
            }
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Register - GameServers.World</title></head>
<body>
<?php include(__DIR__ . '/includes/top.php'); include(__DIR__ . '/includes/menu.php'); ?>
<h2>Register</h2>
<?php if (!empty($error)) echo '<div class="muted text-danger">'.htmlspecialchars($error).'</div>'; ?>
<form method="post" action="register.php">
  <label>Username<br><input type="text" name="username" required></label><br>
  <label>Email<br><input type="email" name="email"></label><br>
  <label>Password<br><input type="password" name="password" required></label><br>
  <button type="submit">Register</button>
</form>
</body>
</html>
