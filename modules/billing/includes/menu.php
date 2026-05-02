<?php
/**
 * Navigation Menu for GameServers.World Website
 * This file provides a consistent navigation menu across all website pages
 */

require_once(__DIR__ . '/session_bridge.php');

if (!function_exists('billing_nav_escape')) {
  function billing_nav_escape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
  }
}

$nav_prefix = '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
if (is_string($scriptName) && $scriptName !== '') {
  if (preg_match('#/modules/billing/(.*)$#', $scriptName, $match)) {
    $subPath = $match[1];
    if ($subPath !== '') {
      $depth = substr_count($subPath, '/');
      if ($depth > 0) {
        $nav_prefix = str_repeat('../', $depth);
      }
    }
  }
}
$nav_prefix = $nav_prefix ?: '';

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
  require_once(__DIR__ . '/config_loader.php');
  
  // Variables from config.inc.php (helps IDEs understand scope)
  /** @var string $db_host Database host */
  /** @var string $db_user Database user */
  /** @var string $db_pass Database password */
  /** @var string $db_name Database name */
  /** @var string $table_prefix Table prefix for database tables */
  
  // Prefer reusing an existing $db if present, otherwise open a local connection
  $menu_db = null;
  $menu_db_opened = false;
  // Only reuse $db if it is still an open (non-closed) connection.
  // mysqli_thread_id() returns 0 on a closed handle; no @ needed since instanceof
  // already guarantees $db is a mysqli object.
  if (isset($db) && $db instanceof mysqli && mysqli_thread_id($db)) {
    $menu_db = $db;
  } else {
    $menu_db_port = isset($db_port) ? (int)$db_port : null;
    $menu_db = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, $menu_db_port);
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
<link rel="stylesheet" href="<?php echo billing_nav_escape($nav_prefix . 'css/header.css'); ?>">

<!-- site wrapper for scoping styles -->
<div id="gsw-site">

<div class="gsw-header">
  <div class="gsw-header-top">
    <div class="gsw-header-left">
      <a href="<?php echo billing_nav_escape($nav_prefix . 'index.php'); ?>" class="gsw-logo-link">
        <img src="<?php echo billing_nav_escape($nav_prefix . 'images/logo-sm.png'); ?>" alt="GameServers.World" class="gsw-logo">
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
        <a href="<?php echo billing_nav_escape($nav_prefix . 'my_account.php'); ?>" class="gsw-user-info">Welcome, <?php echo $username; ?></a>
        <a href="<?php echo billing_nav_escape($nav_prefix . 'logout.php?return_to=' . urlencode($return_to_param)); ?>" class="gsw-header-btn">Logout</a>
      <?php else: ?>
        <a href="<?php echo billing_nav_escape($nav_prefix . 'login.php?return_to=' . urlencode($return_to_param)); ?>" class="gsw-header-btn">Login</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="gsw-header-bottom">
    <nav class="gsw-header-nav">
      <a href="<?php echo billing_nav_escape($nav_prefix . 'index.php'); ?>" class="gsw-nav-link">Home</a>
      <a href="<?php echo billing_nav_escape($nav_prefix . 'serverlist.php'); ?>" class="gsw-nav-link">Game Servers</a>
      <a href="<?php echo billing_nav_escape($nav_prefix . 'docs.php'); ?>" class="gsw-nav-link">Documentation</a>
      <?php if ($is_logged_in): ?>
        <!-- My Account as a regular nav link, not a prominent button -->
        <a href="<?php echo billing_nav_escape($nav_prefix . 'my_account.php'); ?>" class="gsw-nav-link">My Account</a>
        <a href="<?php echo billing_nav_escape($nav_prefix . 'cart.php'); ?>" class="gsw-nav-link">Cart
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
        <a href="<?php echo billing_nav_escape($nav_prefix . 'admin.php'); ?>" class="gsw-nav-link">Admin</a>
      <?php endif; ?>
      <a href="http://panel.iaregamer.com" class="gsw-nav-link" target="_blank">Control Panel</a>
    </nav>
  </div>
</div>

