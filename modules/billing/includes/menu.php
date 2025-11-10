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
// Primary check uses website_user_id, but some remote deployments may only set website_username.
// Treat presence of website_username as a fallback to consider the user logged in for UI purposes.
$is_logged_in = (isset($_SESSION['website_user_id']) && !empty($_SESSION['website_user_id'])) || (isset($_SESSION['website_username']) && !empty($_SESSION['website_username']));
$username = '';
if (isset($_SESSION['website_username']) && !empty($_SESSION['website_username'])) {
  $username = htmlspecialchars($_SESSION['website_username']);
} elseif (isset($_SESSION['website_user_id']) && !empty($_SESSION['website_user_id'])) {
  // fetch username lazily if only user_id is present
  $username = htmlspecialchars((string)($_SESSION['website_user_id']));
}

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
    $uid = null;
    if (isset($_SESSION['website_user_id']) && !empty($_SESSION['website_user_id'])) {
      $uid = intval($_SESSION['website_user_id']);
    }
    if (!empty($uid)) {
      $res = mysqli_query($menu_db, "SELECT users_role FROM {$table_prefix}users WHERE user_id = $uid LIMIT 1");
      if ($res && mysqli_num_rows($res) === 1) {
        $row = mysqli_fetch_assoc($res);
        if (strtolower((string)($row['users_role'] ?? '')) === 'admin') $is_admin = true;
      }
    }
    if ($menu_db_opened) {
      if (function_exists('billing_maybe_close_db')) {
        billing_maybe_close_db($menu_db);
      } else {
        @mysqli_close($menu_db);
      }
    }
  }
}
?>
<link rel="stylesheet" href="css/header.css">

<!-- site wrapper for scoping styles -->
<div id="gsw-site">

<div class="gsw-header">
  <div class="gsw-header-top">
    <div class="gsw-header-left">
      <a href="index.php" class="gsw-logo-link">
        <img src="images/logo-sm.png" alt="GameServers.World" class="gsw-logo">
        <span class="gsw-site-name">GameServers.World</span>
      </a>
    </div>
    <div class="gsw-header-right">
      <!-- Always show the user-info area (may be empty for guests) and an auth button -->
      <?php
        // Build a safe absolute return_to under this site so auth redirects stay within this module
        $current = $_SERVER['REQUEST_URI'] ?? '/';
        $return_to_param = $current;
      ?>
      <?php if ($is_logged_in): ?>
        <a href="my_account.php" class="gsw-user-info">Welcome, <?php echo $username; ?></a>
        <a href="logout.php?return_to=<?php echo urlencode($return_to_param); ?>" class="gsw-header-btn">Logout</a>
      <?php else: ?>
        <a href="login.php?return_to=<?php echo urlencode($return_to_param); ?>" class="gsw-header-btn">Login</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="gsw-header-bottom">
    <nav class="gsw-header-nav">
      <a href="index.php" class="gsw-nav-link">Home</a>
      <a href="serverlist.php" class="gsw-nav-link">Game Servers</a>
      <a href="docs.php" class="gsw-nav-link">Documentation</a>
      <?php if ($is_logged_in): ?>
        <!-- My Account as a regular nav link, not a prominent button -->
        <a href="my_account.php" class="gsw-nav-link">My Account</a>
        <a href="cart.php" class="gsw-nav-link">Cart
          <?php
            $cart_count = 0;
            if (file_exists(__DIR__ . '/cart_helper.php')) {
              include_once __DIR__ . '/cart_helper.php';
              if (function_exists('get_cart_count')) $cart_count = (int) get_cart_count();
            }
            if ($cart_count > 0) echo ' <span class="cart-badge">' . intval($cart_count) . '</span>';
          ?>
        </a>
      <?php endif; ?>
      <?php if ($is_logged_in && $is_admin): ?>
        <a href="admin.php" class="gsw-nav-link">Admin</a>
      <?php endif; ?>
      <a href="http://panel.iaregamer.com" class="gsw-nav-link" target="_blank">Control Panel</a>
    </nav>
  </div>
</div>
