<?php
// Simple footer include
?>
<footer class="gsw-footer">
  <div class="container-wide">
    <a href="privacy.php">Privacy</a> | <a href="tos.php">TOS</a> | <a href="server_status.php">Server Status</a> | <a href="https://worlddomination.dev" target="_blank" rel="noopener">Worlddomination.dev</a>
    <div class="last-updated" style="float:right;color:#999;font-size:0.9em;">
      <?php
      // Include the canonical billing timestamp text file (plain text).
      $billing_ts = __DIR__ . '/../timestamp.txt';
      if (file_exists($billing_ts)) {
          echo trim(file_get_contents($billing_ts));
      }
      ?>
    </div>
  </div>
</footer>

<!-- close site wrapper started in menu.php -->
</div>
