<?php
// Simple footer include
require_once(dirname(__DIR__) . '/billing_bootstrap.php');
?>
<footer class="gsw-footer">
  <div class="container-wide">
    <a href="<?php echo htmlspecialchars(function_exists('billing_url') ? billing_url('privacy.php') : 'privacy.php', ENT_QUOTES, 'UTF-8'); ?>">Privacy</a> |
    <a href="<?php echo htmlspecialchars(function_exists('billing_url') ? billing_url('tos.php') : 'tos.php', ENT_QUOTES, 'UTF-8'); ?>">TOS</a> |
    <a href="<?php echo htmlspecialchars(function_exists('billing_url') ? billing_url('server_status.php') : 'server_status.php', ENT_QUOTES, 'UTF-8'); ?>">Server Status</a> |
    <a href="https://worlddomination.dev" target="_blank" rel="noopener">Worlddomination.dev</a>
    
  </div>
  <div class="last-updated" style="color:#999;font-size:0.9em;">
      <?php
      // Runtime reads modules/billing timestamp; sync from Website/timestamp.txt is handled by billing_bootstrap.php.
      $billing_ts = __DIR__ . '/../timestamp.txt';
      if (file_exists($billing_ts)) {
          echo trim(file_get_contents($billing_ts));
      }
      ?>
    </div>
</footer>

<!-- close site wrapper started in menu.php -->
</div>
