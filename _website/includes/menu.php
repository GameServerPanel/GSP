<?php
/**
 * Navigation Menu for GameServers.World Website
 * This file provides a consistent navigation menu across all website pages
 */

// Start the website session to check if user is logged in (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_name("gameservers_website");
    session_start();
}

// Check login status
$is_logged_in = isset($_SESSION['website_user_id']) && !empty($_SESSION['website_user_id']);
$username = $is_logged_in ? htmlspecialchars($_SESSION['website_username']) : '';

// Determine if the logged-in user is an admin by checking the panel DB
$is_admin = false;
if ($is_logged_in) {
  // load DB credentials
  require_once(__DIR__ . '/config.inc.php');
  // Prefer reusing an existing $db if present, otherwise open a local connection
  $menu_db = null;
  $menu_db_opened = false;
  if (isset($db) && $db instanceof mysqli) {
    $menu_db = $db;
  } else {
    $menu_db = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    $menu_db_opened = true;
  }

  if ($menu_db) {
    $uid = intval($_SESSION['website_user_id']);
    $res = mysqli_query($menu_db, "SELECT users_role FROM ogp_users WHERE user_id = $uid LIMIT 1");
    if ($res && mysqli_num_rows($res) === 1) {
      $row = mysqli_fetch_assoc($res);
      if (strtolower((string)($row['users_role'] ?? '')) === 'admin') $is_admin = true;
    }
    if ($menu_db_opened) {
      mysqli_close($menu_db);
    }
  }
}
?>
<link rel="stylesheet" href="css/header.css">

<div class="gsw-header">
  <div class="gsw-header-left">
    <a href="index.php">GameServers.World</a>
  </div>
  <nav class="gsw-header-nav">
    <a href="index.php" class="gsw-nav-link">Home</a>
    <a href="serverlist.php" class="gsw-nav-link">Game Servers</a>
    <li>
      <a href="cart.php">Cart
        <?php
        // show cart badge if helper available
        $cart_count = 0;
        if (file_exists(__DIR__ . '/cart_helper.php')) {
          include_once __DIR__ . '/cart_helper.php';
          if (function_exists('get_cart_count')) {
            $cart_count = (int) get_cart_count();
          }
        }
        if ($cart_count > 0) {
          echo ' <span class="cart-badge">' . intval($cart_count) . '</span>';
        }
        ?>
      </a>
    </li>
    <?php if (basename($_SERVER['PHP_SELF']) === 'login.php'): ?>
      <a href="register.php" class="gsw-nav-link">Register</a>
    <?php endif; ?>
    <?php if ($is_logged_in && $is_admin): ?>
  <a href="admin.php" class="gsw-nav-link">Admin</a>
    <?php endif; ?>
    <a href="http://panel.iaregamer.com" class="gsw-nav-link" target="_blank">Panel Login</a>
  </nav>
  <div class="gsw-header-right">
    <?php if ($is_logged_in): ?>
      <span class="gsw-user-info">Welcome, <?php echo $username; ?>!</span>
  <?php
    // Build a safe absolute return_to under this site so logout redirects stay in _website
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $pos = strpos($script, '/_website');
    $siteRoot = $pos !== false ? substr($script, 0, $pos + strlen('/_website')) : rtrim(dirname($script), '/\\');
    $current = $_SERVER['REQUEST_URI'] ?? $siteRoot . '/index.php';
    // Ensure current is absolute and under site root; urlencode only when embedding in URL
    $return_to_param = $current;
  ?>
  <a href="logout.php?return_to=<?php echo urlencode($return_to_param); ?>" class="gsw-header-btn">Logout</a>
    <?php else: ?>
  <a href="login.php" class="gsw-header-btn">Login</a>
    <?php endif; ?>
  </div>
</div>
