<?php
/**
 * Admin Config Editor
 *
 * Provides two ways to edit modules/billing/includes/config.inc.php:
 *   A. Interactive form (top) — fields for each billing-specific setting.
 *   B. Raw PHP editor  (bottom) — direct file content textarea (advanced).
 *
 * Both methods create a timestamped backup before saving and apply the
 * $SITE_CONFIG_BACKUP_RETENTION limit (default 5) after writing.
 * A post-save php -l syntax check rolls back the file on parse errors.
 *
 * Database settings (db_host, db_port, db_user, db_pass, db_name, table_prefix)
 * are shown as read-only when the module is installed inside a GSP panel tree.
 * They are managed via the panel and synced automatically by config_loader.php.
 */

require_once(__DIR__ . '/includes/admin_auth.php');
require_once(__DIR__ . '/includes/config_loader.php');
include(__DIR__ . '/includes/top.php');
include(__DIR__ . '/includes/menu.php');

if (session_status() === PHP_SESSION_NONE) {
    session_name('opengamepanel_web');
    session_start();
}
if (empty($_SESSION['admin_csrf'])) {
    $_SESSION['admin_csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['admin_csrf'];

$cfgPath = __DIR__ . '/includes/config.inc.php';
$bakDir  = dirname($cfgPath) . '/backups';

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// ---------------------------------------------------------------------------
// Helper: apply backup retention — delete oldest .bak files beyond the limit.
// Only touches files with names matching *.bak inside the expected backup dir.
// ---------------------------------------------------------------------------
function billing_admin_apply_retention(string $dir, int $retention): void
{
    $retention = max(1, min(10, $retention));
    if (!is_dir($dir)) {
        return;
    }
    $files = glob($dir . '/*.bak');
    if (!is_array($files) || count($files) <= $retention) {
        return;
    }
    // Sort oldest first (by file modification time)
    usort($files, static function (string $a, string $b): int {
        return filemtime($a) <=> filemtime($b);
    });
    $toDelete = count($files) - $retention;
    for ($i = 0; $i < $toDelete; $i++) {
        @unlink($files[$i]);
    }
}

// ---------------------------------------------------------------------------
// Helper: create a backup of the config file; returns backup filename or ''.
// ---------------------------------------------------------------------------
function billing_admin_create_backup(string $cfgPath, string $bakDir): string
{
    @mkdir($bakDir, 0775, true);
    $bakName = $bakDir . '/config.inc.php.' . date('Ymd-His') . '.' . bin2hex(random_bytes(4)) . '.bak';
    if (!copy($cfgPath, $bakName)) {
        return '';
    }
    return $bakName;
}

// ---------------------------------------------------------------------------
// Helper: run php -l on a file and return [ok, output].
// ---------------------------------------------------------------------------
function billing_admin_lint(string $filePath): array
{
    $phpExec = PHP_BINARY ?: null;
    if (!$phpExec) {
        return [true, 'PHP executable not found; skipping syntax check.'];
    }
    $cmd = escapeshellarg($phpExec) . ' -l ' . escapeshellarg($filePath);
    $out = [];
    $rc  = 0;
    @exec($cmd . ' 2>&1', $out, $rc);
    return [$rc === 0, implode("\n", $out)];
}

// ---------------------------------------------------------------------------
// Helper: generate canonical config.inc.php content from an array of values.
// DB settings are preserved from the existing file; only billing fields change.
// ---------------------------------------------------------------------------
function billing_admin_build_config(string $existingContent, array $vals): string
{
    // Extract current DB settings from existing file content so we never lose them.
    $dbLines = [];
    foreach (['db_host', 'db_port', 'db_user', 'db_pass', 'db_name', 'table_prefix', 'db_type'] as $var) {
        if (preg_match('/^\s*\$' . preg_quote($var, '/') . '\s*=.*$/m', $existingContent, $m)) {
            $dbLines[$var] = rtrim($m[0]);
        }
    }

    $q = static function (string $v): string {
        return '"' . addslashes($v) . '"';
    };

    $sandbox    = (bool)$vals['paypal_sandbox'];
    $retention  = max(1, min(10, (int)($vals['backup_retention'] ?? 5)));
    $baseUrl    = trim($vals['SITE_BASE_URL'] ?? '');
    $bg         = trim($vals['SITE_BACKGROUND'] ?? 'images/dark.jpg');
    $dataDir    = trim($vals['SITE_DATA_DIR'] ?? '');

    $dbBlock = '';
    foreach (['db_host', 'db_port', 'db_user', 'db_pass', 'db_name', 'table_prefix', 'db_type'] as $var) {
        if (isset($dbLines[$var])) {
            $dbBlock .= $dbLines[$var] . "\n";
        }
    }

    $dataDirLine = ($dataDir !== '' && $dataDir !== 'auto')
        ? '$SITE_DATA_DIR = ' . $q($dataDir) . ';'
        : '$SITE_DATA_DIR = realpath(__DIR__ . \'/..\')'
          . ' . DIRECTORY_SEPARATOR . \'data\';';

    return '<?php' . "\n"
        . '###############################################' . "\n"
        . '# Website Database Configuration' . "\n"
        . '# This file contains the database connection' . "\n"
        . '# settings for the billing website.' . "\n"
        . '#' . "\n"
        . '# Managed via Admin > Edit Config.' . "\n"
        . '###############################################' . "\n"
        . $dbBlock
        . "\n"
        . '// Optional: base URL used by admin pages to build absolute image previews.' . "\n"
        . '// Leave empty to prefer relative paths (local folder).' . "\n"
        . '$SITE_BASE_URL = ' . $q($baseUrl) . ';' . "\n"
        . "\n"
        . '// Normalize: ensure either empty or ends without trailing slash' . "\n"
        . '$SITE_BASE_URL = trim((string)$SITE_BASE_URL);' . "\n"
        . "\n"
        . '// Site-wide background image (relative to site root).' . "\n"
        . '$SITE_BACKGROUND = ' . $q($bg) . ';' . "\n"
        . '// Normalize' . "\n"
        . '$SITE_BACKGROUND = trim((string)$SITE_BACKGROUND);' . "\n"
        . "\n"
        . '// Data directory for persisted payment webhook JSON files (relative to repo root)' . "\n"
        . $dataDirLine . "\n"
        . "\n"
        . '// PayPal configuration — set credentials here, never in API files' . "\n"
        . '$paypal_sandbox       = ' . ($sandbox ? 'true' : 'false') . ';   // Set to false for live payments' . "\n"
        . '$paypal_client_id     = ' . $q($vals['paypal_client_id'] ?? '') . ';     // Your PayPal Client ID' . "\n"
        . '$paypal_client_secret = ' . $q($vals['paypal_client_secret'] ?? '') . ';     // Your PayPal Client Secret' . "\n"
        . '$paypal_webhook_id    = ' . $q($vals['paypal_webhook_id'] ?? '') . ';     // Your PayPal Webhook ID' . "\n"
        . "\n"
        . '// Admin config backup retention: how many backups to keep (1–10). Default 5.' . "\n"
        . '$SITE_CONFIG_BACKUP_RETENTION = ' . $retention . ';' . "\n"
        . '?>' . "\n";
}

// ---------------------------------------------------------------------------
// Read current values from config (already loaded by config_loader above).
// ---------------------------------------------------------------------------
$cfgVals = [
    'SITE_BASE_URL'           => $SITE_BASE_URL          ?? '',
    'SITE_BACKGROUND'         => $SITE_BACKGROUND         ?? 'images/dark.jpg',
    'SITE_DATA_DIR'           => isset($SITE_DATA_DIR)    ? $SITE_DATA_DIR : '',
    'paypal_sandbox'          => $paypal_sandbox          ?? true,
    'paypal_client_id'        => $paypal_client_id        ?? '',
    'paypal_client_secret'    => $paypal_client_secret    ?? '',
    'paypal_webhook_id'       => $paypal_webhook_id       ?? '',
    'backup_retention'        => $SITE_CONFIG_BACKUP_RETENTION ?? 5,
];

// Detect panel-mode (DB settings are managed by the panel)
$panelMode     = defined('BILLING_PANEL_CONFIG_PATH');
$panelCfgPath  = $panelMode ? BILLING_PANEL_CONFIG_PATH : null;

$status = '';
$statusType = 'info';   // 'success' | 'error' | 'info'

// ---------------------------------------------------------------------------
// POST: Save interactive form
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_form') {
    $token = $_POST['csrf'] ?? '';
    if (!hash_equals($csrf, (string)$token)) {
        $status     = 'Invalid CSRF token.';
        $statusType = 'error';
    } elseif (!is_writable($cfgPath)) {
        $status     = 'Config file is not writable: ' . h($cfgPath);
        $statusType = 'error';
    } else {
        // Collect and validate form values
        $formVals = [
            'SITE_BASE_URL'        => trim($_POST['SITE_BASE_URL']        ?? ''),
            'SITE_BACKGROUND'      => trim($_POST['SITE_BACKGROUND']      ?? 'images/dark.jpg'),
            'SITE_DATA_DIR'        => trim($_POST['SITE_DATA_DIR']        ?? ''),
            'paypal_sandbox'       => (($_POST['paypal_sandbox'] ?? 'true') === 'true'),
            'paypal_client_id'     => trim($_POST['paypal_client_id']     ?? ''),
            'paypal_client_secret' => trim($_POST['paypal_client_secret'] ?? ''),
            'paypal_webhook_id'    => trim($_POST['paypal_webhook_id']    ?? ''),
            'backup_retention'     => (int)($_POST['backup_retention']    ?? 5),
        ];

        // Validate
        $validationError = '';
        if ($formVals['backup_retention'] < 1 || $formVals['backup_retention'] > 10) {
            $validationError = 'Backup retention must be a number between 1 and 10.';
        }

        if ($validationError) {
            $status     = $validationError;
            $statusType = 'error';
        } else {
            $existingContent = (string)file_get_contents($cfgPath);
            $newContent      = billing_admin_build_config($existingContent, $formVals);

            // Backup before write
            $bakName = billing_admin_create_backup($cfgPath, $bakDir);
            if (!$bakName) {
                $status     = 'Failed to create backup. Aborting save.';
                $statusType = 'error';
            } else {
                if (file_put_contents($cfgPath, $newContent, LOCK_EX) === false) {
                    $status     = 'Failed to write config file.';
                    $statusType = 'error';
                } else {
                    // Syntax check
                    [$lintOk, $lintOut] = billing_admin_lint($cfgPath);
                    if (!$lintOk) {
                        @copy($bakName, $cfgPath); // rollback
                        $status     = 'Syntax error in generated config; rolled back. Lint: ' . h($lintOut);
                        $statusType = 'error';
                    } else {
                        // Apply backup retention
                        $retention = max(1, min(10, $formVals['backup_retention']));
                        billing_admin_apply_retention($bakDir, $retention);

                        $cfgVals    = $formVals; // update displayed values
                        $status     = 'Config saved successfully. Backup: ' . basename($bakName);
                        $statusType = 'success';
                    }
                }
            }
        }
    }
}

// ---------------------------------------------------------------------------
// POST: Save raw editor
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_raw') {
    $token = $_POST['csrf'] ?? '';
    if (!hash_equals($csrf, (string)$token)) {
        $status     = 'Invalid CSRF token.';
        $statusType = 'error';
    } elseif (!is_writable($cfgPath)) {
        $status     = 'Config file is not writable: ' . h($cfgPath);
        $statusType = 'error';
    } else {
        $newRaw = $_POST['config_text'] ?? '';
        if (strpos(trim($newRaw), '<?php') !== 0) {
            $status     = 'Config must start with <?php';
            $statusType = 'error';
        } else {
            $bakName = billing_admin_create_backup($cfgPath, $bakDir);
            if (!$bakName) {
                $status     = 'Failed to create backup. Aborting save.';
                $statusType = 'error';
            } else {
                if (file_put_contents($cfgPath, $newRaw, LOCK_EX) === false) {
                    $status     = 'Failed to write config file.';
                    $statusType = 'error';
                } else {
                    [$lintOk, $lintOut] = billing_admin_lint($cfgPath);
                    if (!$lintOk) {
                        @copy($bakName, $cfgPath); // rollback
                        $status     = 'Syntax error detected; changes rolled back. Lint: ' . h($lintOut);
                        $statusType = 'error';
                    } else {
                        // Apply backup retention from config
                        $retentionNow = max(1, min(10, (int)($SITE_CONFIG_BACKUP_RETENTION ?? 5)));
                        billing_admin_apply_retention($bakDir, $retentionNow);

                        $status     = 'Config saved successfully. Backup: ' . basename($bakName);
                        $statusType = 'success';
                    }
                }
            }
        }
    }
}

// Always read current raw content from disk for the raw editor
$currentText = '';
if (is_readable($cfgPath)) {
    $currentText = file_get_contents($cfgPath);
}

// List current backups for display
$bakFiles = is_dir($bakDir) ? (array)glob($bakDir . '/*.bak') : [];
rsort($bakFiles); // newest first

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin — Edit Config</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/header.css">
  <style>
    .cfg-section { background:#fff; border:1px solid #ddd; border-radius:6px; padding:20px 24px; margin-bottom:28px; }
    .cfg-section h2 { margin-top:0; color:#333; border-bottom:2px solid #eee; padding-bottom:8px; }
    .field-group { margin-bottom:18px; }
    .field-group label { display:block; font-weight:600; color:#333; margin-bottom:4px; }
    .field-help { font-size:0.85em; color:#666; margin-bottom:6px; }
    .field-group input[type=text],
    .field-group input[type=password],
    .field-group input[type=number],
    .field-group select { width:100%; max-width:520px; padding:8px 10px; border:1px solid #ccc;
                           border-radius:4px; font-size:1em; box-sizing:border-box; }
    .field-group .pw-wrap { display:flex; gap:6px; align-items:center; max-width:520px; }
    .field-group .pw-wrap input { flex:1; }
    .btn-show { padding:8px 14px; font-size:0.9em; border:1px solid #aaa; border-radius:4px;
                background:#f5f5f5; cursor:pointer; white-space:nowrap; }
    .status-box { padding:12px 16px; border-radius:4px; margin-bottom:18px; font-weight:600; }
    .status-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
    .status-error   { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
    .status-info    { background:#d1ecf1; color:#0c5460; border:1px solid #bee5eb; }
    .panel-badge { background:#e8f4fd; border:1px solid #9ec8f0; color:#1a5276; padding:10px 14px;
                   border-radius:4px; margin-bottom:18px; font-size:0.9em; }
    .readonly-field { background:#f4f4f4; color:#555; cursor:not-allowed; }
    .warn-box { background:#fff3cd; border:1px solid #ffc107; color:#856404; padding:10px 14px;
                border-radius:4px; margin-bottom:14px; font-size:0.9em; }
    .save-row { margin:14px 0; }
    .save-row button { padding:10px 24px; font-size:1em; font-weight:600; }
    .bak-list { font-size:0.85em; color:#555; margin-top:4px; }
  </style>
</head>
<body>
<div class="container-wide panel">
  <h1>Edit Site Config</h1>

  <?php if ($status): ?>
  <div class="status-box status-<?php echo h($statusType); ?>"><?php echo h($status); ?></div>
  <?php endif; ?>

  <?php if (!empty($billing_config_warning)): ?>
  <div class="warn-box">⚠️ <?php echo h($billing_config_warning); ?></div>
  <?php endif; ?>

  <!-- ===================================================================
       SECTION A: Interactive form
  ==================================================================== -->
  <div class="cfg-section">
    <h2>Site Settings</h2>

    <?php if ($panelMode): ?>
    <div class="panel-badge">
      ℹ️ <strong>Panel-integrated mode.</strong>
      Database settings are managed by the panel and synced automatically from
      <code><?php echo h($panelCfgPath); ?></code>.
      They are shown below for reference only.
    </div>
    <?php endif; ?>

    <form method="post" action="">
      <input type="hidden" name="csrf"   value="<?php echo h($csrf); ?>">
      <input type="hidden" name="action" value="save_form">

      <!-- DB read-only info (panel mode) -->
      <?php if ($panelMode): ?>
      <div class="field-group">
        <label>Database Host</label>
        <div class="field-help">Managed by the panel config. Edit the panel's <code>includes/config.inc.php</code> to change.</div>
        <input type="text" class="readonly-field" value="<?php echo h((string)($db_host ?? '')); ?>" readonly>
      </div>
      <div class="field-group">
        <label>Database Name</label>
        <input type="text" class="readonly-field" value="<?php echo h((string)($db_name ?? '')); ?>" readonly>
      </div>
      <div class="field-group">
        <label>Table Prefix</label>
        <input type="text" class="readonly-field" value="<?php echo h((string)($table_prefix ?? '')); ?>" readonly>
      </div>
      <?php endif; ?>

      <!-- Site Base URL -->
      <div class="field-group">
        <label for="cfg_base_url">Site Base URL</label>
        <div class="field-help">
          Full base URL without trailing slash (e.g. <code>https://gameservers.world</code>).
          Leave empty to use relative paths.
        </div>
        <input type="text" id="cfg_base_url" name="SITE_BASE_URL"
               value="<?php echo h((string)$cfgVals['SITE_BASE_URL']); ?>"
               placeholder="https://example.com">
      </div>

      <!-- Site Background -->
      <div class="field-group">
        <label for="cfg_bg">Site Background Image</label>
        <div class="field-help">
          Path to background image relative to the billing site root (e.g. <code>images/dark.jpg</code>).
        </div>
        <input type="text" id="cfg_bg" name="SITE_BACKGROUND"
               value="<?php echo h((string)$cfgVals['SITE_BACKGROUND']); ?>"
               placeholder="images/dark.jpg">
      </div>

      <!-- Data Directory -->
      <div class="field-group">
        <label for="cfg_datadir">Site Data Directory</label>
        <div class="field-help">
          Absolute path where payment webhook JSON files are stored.
          Leave empty to use the default: <code>modules/billing/data/</code>.
        </div>
        <input type="text" id="cfg_datadir" name="SITE_DATA_DIR"
               value="<?php echo h((string)$cfgVals['SITE_DATA_DIR']); ?>"
               placeholder="(default: billing/data/)">
      </div>

      <hr style="border:none;border-top:1px solid #eee;margin:24px 0;">
      <h3 style="margin-top:0;color:#333;">PayPal Configuration</h3>

      <!-- PayPal Sandbox -->
      <div class="field-group">
        <label for="cfg_sandbox">PayPal Mode</label>
        <div class="field-help">
          Use <strong>Sandbox</strong> for testing, <strong>Live</strong> for real payments.
          Make sure you use the matching Client ID and Secret for the selected mode.
        </div>
        <select id="cfg_sandbox" name="paypal_sandbox">
          <option value="true"  <?php echo $cfgVals['paypal_sandbox'] ? 'selected' : ''; ?>>Sandbox (test mode)</option>
          <option value="false" <?php echo !$cfgVals['paypal_sandbox'] ? 'selected' : ''; ?>>Live (real payments)</option>
        </select>
      </div>

      <!-- PayPal Client ID -->
      <div class="field-group">
        <label for="cfg_cid">PayPal Client ID</label>
        <div class="field-help">
          Your PayPal app Client ID. Safe to expose in browser JS.
          Found in your PayPal Developer Dashboard under your app credentials.
        </div>
        <input type="text" id="cfg_cid" name="paypal_client_id"
               value="<?php echo h((string)$cfgVals['paypal_client_id']); ?>"
               placeholder="AY... or AZ...">
      </div>

      <!-- PayPal Client Secret -->
      <div class="field-group">
        <label for="cfg_csecret">PayPal Client Secret</label>
        <div class="field-help">
          Your PayPal app Client Secret. <strong>Server-side only</strong> — never sent to the browser.
        </div>
        <div class="pw-wrap">
          <input type="password" id="cfg_csecret" name="paypal_client_secret"
                 value="<?php echo h((string)$cfgVals['paypal_client_secret']); ?>"
                 autocomplete="new-password">
          <button type="button" class="btn-show"
                  onclick="var f=document.getElementById('cfg_csecret');f.type=f.type==='password'?'text':'password';this.textContent=f.type==='password'?'Show':'Hide';">
            Show
          </button>
        </div>
      </div>

      <!-- PayPal Webhook ID -->
      <div class="field-group">
        <label for="cfg_wh">PayPal Webhook ID</label>
        <div class="field-help">
          Webhook ID from your PayPal app (used for webhook signature verification).
          Leave empty to skip signature verification (not recommended for production).
        </div>
        <input type="text" id="cfg_wh" name="paypal_webhook_id"
               value="<?php echo h((string)$cfgVals['paypal_webhook_id']); ?>"
               placeholder="Webhook ID">
      </div>

      <hr style="border:none;border-top:1px solid #eee;margin:24px 0;">
      <h3 style="margin-top:0;color:#333;">Backup Settings</h3>

      <!-- Backup Retention -->
      <div class="field-group">
        <label for="cfg_retention">Config Backup Retention</label>
        <div class="field-help">
          Number of config backups to keep (1–10). The oldest backup beyond this limit is
          deleted after each save. Backups are stored in
          <code><?php echo h($bakDir); ?></code>.
        </div>
        <input type="number" id="cfg_retention" name="backup_retention"
               value="<?php echo (int)$cfgVals['backup_retention']; ?>"
               min="1" max="10" style="max-width:100px;">
      </div>

      <div class="save-row">
        <button type="submit">💾 Save Settings</button>
      </div>
    </form>
  </div>

  <!-- ===================================================================
       SECTION B: Raw PHP editor
  ==================================================================== -->
  <div class="cfg-section">
    <h2>Advanced: Raw Config Editor</h2>
    <div class="warn-box">
      ⚠️ <strong>Warning:</strong> Manually editing the raw PHP file can break the billing
      website if you introduce a syntax error or remove required variables.
      A backup is created automatically before saving, and a syntax check runs after.
      The file is rolled back if a parse error is detected.
    </div>

    <form method="post" action="">
      <input type="hidden" name="csrf"   value="<?php echo h($csrf); ?>">
      <input type="hidden" name="action" value="save_raw">
      <div class="save-row"><button type="submit">💾 Save Raw Config</button></div>
      <textarea name="config_text" rows="28"
                style="width:100%;font-family:monospace;font-size:0.9em;border:1px solid #ccc;border-radius:4px;padding:10px;box-sizing:border-box;"
      ><?php echo h((string)$currentText); ?></textarea>
      <div class="save-row"><button type="submit">💾 Save Raw Config</button></div>
    </form>

    <p style="margin-top:16px;">
      <strong>Backup directory:</strong> <code><?php echo h($bakDir); ?></code>
      <?php if ($bakFiles): ?>
        <br><span class="bak-list">
          <?php echo count($bakFiles); ?> backup(s) stored.
          Most recent: <code><?php echo h(basename($bakFiles[0])); ?></code>
        </span>
      <?php else: ?>
        <br><span class="bak-list">No backups yet.</span>
      <?php endif; ?>
    </p>
  </div>

</div>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</body>
</html>
