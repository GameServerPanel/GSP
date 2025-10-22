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
?>
<style>
  .gsw-header{display:flex;justify-content:space-between;align-items:center;padding:16px 24px;background:rgba(102, 126, 234, 0.95);backdrop-filter:blur(10px);margin-bottom:20px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}
  .gsw-header-left{font-weight:700;font-size:1.2rem;color:#fff;}
  .gsw-header-left a{color:#fff;text-decoration:none;}
  .gsw-header-nav{display:flex;gap:20px;align-items:center;}
  .gsw-nav-link{color:#fff;text-decoration:none;font-size:0.95rem;transition:opacity 0.2s;}
  .gsw-nav-link:hover{opacity:0.8;text-decoration:underline;}
  .gsw-header-right{display:flex;gap:12px;align-items:center;}
  .gsw-user-info{color:#fff;font-size:0.95rem;}
  .gsw-header-btn{padding:8px 16px;background:#fff;color:#667eea;border-radius:6px;text-decoration:none;font-weight:600;transition:transform 0.2s;}
  .gsw-header-btn:hover{transform:translateY(-2px);}
  @media(max-width:768px){
    .gsw-header{flex-direction:column;gap:12px;}
    .gsw-header-nav{flex-wrap:wrap;justify-content:center;}
  }
</style>

<div class="gsw-header">
  <div class="gsw-header-left">
    <a href="/">GameServers.World</a>
  </div>
  <nav class="gsw-header-nav">
    <a href="/" class="gsw-nav-link">Home</a>
    <a href="/serverlist.php" class="gsw-nav-link">Game Servers</a>
    <a href="/cart.php" class="gsw-nav-link">Cart</a>
    <?php if ($is_logged_in): ?>
      <a href="/adminserverlist.php" class="gsw-nav-link">Admin</a>
    <?php endif; ?>
    <a href="http://panel.iaregamer.com" class="gsw-nav-link" target="_blank">Panel Login</a>
  </nav>
  <div class="gsw-header-right">
    <?php if ($is_logged_in): ?>
      <span class="gsw-user-info">Welcome, <?php echo $username; ?>!</span>
      <a href="/logout.php" class="gsw-header-btn">Logout</a>
    <?php else: ?>
      <a href="/login.php" class="gsw-header-btn">Login</a>
    <?php endif; ?>
  </div>
</div>
