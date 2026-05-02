<?php
// Admin config editor — lightweight editor for _website/includes/config.inc.php
require_once(__DIR__ . '/includes/admin_auth.php');
require_once(__DIR__ . '/includes/config_loader.php');
include(__DIR__ . '/includes/top.php');
include(__DIR__ . '/includes/menu.php');

if (session_status() === PHP_SESSION_NONE) {
    session_name('opengamepanel_web');
    session_start();
}
if (empty($_SESSION['admin_csrf'])) $_SESSION['admin_csrf'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['admin_csrf'];

$cfgPath = __DIR__ . '/includes/config.inc.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$status = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $token = $_POST['csrf'] ?? '';
  if (!hash_equals($csrf, (string)$token)) {
    $status = 'Invalid CSRF token.';
  } else {
    if (!is_writable($cfgPath)) {
      $status = 'Config file not writable: ' . h($cfgPath);
    } else {
      // Backup
      $bakDir = dirname($cfgPath) . '/backups';
      @mkdir($bakDir, 0775, true);
      $bakName = $bakDir . '/config.inc.php.' . date('Ymd-His') . '.' . bin2hex(random_bytes(4)) . '.bak';
      if (!copy($cfgPath, $bakName)) {
        $status = 'Failed to create backup. Aborting.';
      } else {
        $new = $_POST['config_text'] ?? '';
        // Basic safety: ensure the file still starts with <?php
        if (strpos(trim($new), '<?php') !== 0) {
          $status = 'Config must start with <?php';
        } else {
          if (file_put_contents($cfgPath, $new) === false) {
            $status = 'Failed to write config file.';
          } else {
            // Post-save syntax check: try to run php -l using a sensible PHP executable.
            $phpExec = PHP_BINARY ?: (file_exists('C:\\xampp\\php\\php.exe') ? 'C:\\xampp\\php\\php.exe' : null);
            $lintOk = true;
            $lintOutput = '';
            if ($phpExec) {
              $cmd = escapeshellarg($phpExec) . ' -l ' . escapeshellarg($cfgPath);
              // execute and capture output
              $out = null; $rc = null;
              @exec($cmd . ' 2>&1', $out, $rc);
              $lintOutput = is_array($out) ? implode("\n", $out) : (string)$out;
              if ($rc !== 0) {
                $lintOk = false;
              }
            } else {
              $lintOutput = 'PHP executable not found for linting; skipping post-save syntax check.';
            }

            if (!$lintOk) {
              // rollback
              @copy($bakName, $cfgPath);
              $status = 'Syntax error detected in saved config. Changes rolled back. Lint output: ' . h($lintOutput);
            } else {
              $status = 'Config saved successfully. Backup: ' . basename($bakName) . (strlen($lintOutput) ? ' (lint: '.h($lintOutput).')' : '');
              // reload values
              require_once($cfgPath);
            }
          }
        }
      }
    }
  }
}

$currentText = '';
if (is_readable($cfgPath)) {
  $currentText = file_get_contents($cfgPath);
}

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin — Edit Config</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/header.css">
</head>
<body>
<div class="container-wide panel">
  <h1>Edit Site Config</h1>
  <?php if ($status): ?><div class="panel"><strong><?php echo h($status); ?></strong></div><?php endif; ?>

  <form method="post" action="">
    <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
    <div style="margin-bottom:8px;"><button type="submit">Save Config</button></div>
    <textarea name="config_text" rows="24" style="width:100%;font-family:monospace;"><?php echo h($currentText); ?></textarea>
    <div style="margin-top:8px;"><button type="submit">Save Config</button></div>
  </form>

  <p><small>Backups are stored in <code><?php echo h(dirname($cfgPath) . '/backups'); ?></code></small></p>
</div>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</body>
</html>
