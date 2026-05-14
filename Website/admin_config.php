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

    $mode      = (strtolower($vals['paypal_mode'] ?? 'sandbox') === 'live') ? 'live' : 'sandbox';
    $retention = max(1, min(10, (int)($vals['backup_retention'] ?? 5)));
    $baseUrl   = rtrim(trim($vals['SITE_BASE_URL'] ?? ''), '/');
    $bg        = trim($vals['SITE_BACKGROUND'] ?? 'images/dark.jpg');
    $dataDir   = trim($vals['SITE_DATA_DIR'] ?? '');
    $wh_path   = '/' . ltrim(trim($vals['paypal_webhook_path'] ?? '/paypal/webhook.php'), '/');

    // Sandbox credentials — never erase existing secret if field was left blank
    $sb_id     = trim($vals['paypal_sandbox_client_id']     ?? '');
    $sb_sec    = trim($vals['paypal_sandbox_client_secret'] ?? '');
    $sb_wh     = trim($vals['paypal_sandbox_webhook_id']    ?? '');

    // Live credentials — never erase existing secret if field was left blank
    $lv_id     = trim($vals['paypal_live_client_id']        ?? '');
    $lv_sec    = trim($vals['paypal_live_client_secret']    ?? '');
    $lv_wh     = trim($vals['paypal_live_webhook_id']       ?? '');

    $dbBlock = '';
    foreach (['db_host', 'db_port', 'db_user', 'db_pass', 'db_name', 'table_prefix', 'db_type'] as $var) {
        if (isset($dbLines[$var])) {
            $dbBlock .= $dbLines[$var] . "\n";
        }
    }

    $dataDirLine = ($dataDir !== '' && $dataDir !== 'auto')
        ? '$SITE_DATA_DIR = ' . $q($dataDir) . ';'
        : "\$SITE_DATA_DIR = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'data';";

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
        . '// Optional: base URL without trailing slash (e.g. https://gameservers.world).' . "\n"
        . '// Leave empty to use relative paths.' . "\n"
        . '$SITE_BASE_URL = ' . $q($baseUrl) . ';' . "\n"
        . '$SITE_BASE_URL = rtrim(trim((string)$SITE_BASE_URL), \'/\');' . "\n"
        . "\n"
        . '// Site-wide background image (relative to site root).' . "\n"
        . '$SITE_BACKGROUND = ' . $q($bg) . ';' . "\n"
        . '$SITE_BACKGROUND = trim((string)$SITE_BACKGROUND);' . "\n"
        . "\n"
        . '// Data directory for persisted payment webhook JSON files.' . "\n"
        . $dataDirLine . "\n"
        . "\n"
        . '// ---------------------------------------------------------------------------' . "\n"
        . '// PayPal configuration' . "\n"
        . '// ---------------------------------------------------------------------------' . "\n"
        . '$paypal_mode = ' . $q($mode) . ';  // \'sandbox\' or \'live\'' . "\n"
        . "\n"
        . '// Sandbox credentials (PayPal Developer Dashboard → sandbox app)' . "\n"
        . '$paypal_sandbox_client_id     = ' . $q($sb_id) . ';' . "\n"
        . '$paypal_sandbox_client_secret = ' . $q($sb_sec) . ';' . "\n"
        . '$paypal_sandbox_webhook_id    = ' . $q($sb_wh) . ';' . "\n"
        . "\n"
        . '// Live credentials (leave blank until ready for production)' . "\n"
        . '$paypal_live_client_id     = ' . $q($lv_id) . ';' . "\n"
        . '$paypal_live_client_secret = ' . $q($lv_sec) . ';' . "\n"
        . '$paypal_live_webhook_id    = ' . $q($lv_wh) . ';' . "\n"
        . "\n"
        . '// Webhook path (relative to billing site root, must start with /)' . "\n"
        . '// Full public URL = $SITE_BASE_URL + $paypal_webhook_path' . "\n"
        . '$paypal_webhook_path = ' . $q($wh_path) . ';' . "\n"
        . "\n"
        . '// Admin config backup retention: how many backups to keep (1–10). Default 5.' . "\n"
        . '$SITE_CONFIG_BACKUP_RETENTION = ' . $retention . ';' . "\n"
        . '?>' . "\n";
}

// ---------------------------------------------------------------------------
// Read current values from config (already loaded by config_loader above).
// ---------------------------------------------------------------------------
$cfgVals = [
    'SITE_BASE_URL'                  => $SITE_BASE_URL          ?? '',
    'SITE_BACKGROUND'                => $SITE_BACKGROUND         ?? 'images/dark.jpg',
    'SITE_DATA_DIR'                  => $SITE_DATA_DIR           ?? '',
    'paypal_mode'                    => $paypal_mode             ?? 'sandbox',
    'paypal_sandbox_client_id'       => $paypal_sandbox_client_id       ?? '',
    'paypal_sandbox_client_secret'   => $paypal_sandbox_client_secret   ?? '',
    'paypal_sandbox_webhook_id'      => $paypal_sandbox_webhook_id      ?? '',
    'paypal_live_client_id'          => $paypal_live_client_id          ?? '',
    'paypal_live_client_secret'      => $paypal_live_client_secret      ?? '',
    'paypal_live_webhook_id'         => $paypal_live_webhook_id         ?? '',
    'paypal_webhook_path'            => $paypal_webhook_path            ?? '/paypal/webhook.php',
    'backup_retention'               => $SITE_CONFIG_BACKUP_RETENTION   ?? 5,
];

// Computed full webhook URL for display
$computedWebhookUrl = function_exists('gsp_paypal_get_full_webhook_url')
    ? gsp_paypal_get_full_webhook_url()
    : rtrim($cfgVals['SITE_BASE_URL'], '/') . $cfgVals['paypal_webhook_path'];

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
            'SITE_BASE_URL'               => trim($_POST['SITE_BASE_URL']               ?? ''),
            'SITE_BACKGROUND'             => trim($_POST['SITE_BACKGROUND']             ?? 'images/dark.jpg'),
            'SITE_DATA_DIR'               => trim($_POST['SITE_DATA_DIR']               ?? ''),
            'paypal_mode'                 => (strtolower(trim($_POST['paypal_mode'] ?? 'sandbox')) === 'live') ? 'live' : 'sandbox',
            'paypal_sandbox_client_id'    => trim($_POST['paypal_sandbox_client_id']    ?? ''),
            'paypal_live_client_id'       => trim($_POST['paypal_live_client_id']       ?? ''),
            'paypal_sandbox_webhook_id'   => trim($_POST['paypal_sandbox_webhook_id']   ?? ''),
            'paypal_live_webhook_id'      => trim($_POST['paypal_live_webhook_id']      ?? ''),
            'paypal_webhook_path'         => trim($_POST['paypal_webhook_path']         ?? '/paypal/webhook.php'),
            'backup_retention'            => (int)($_POST['backup_retention']           ?? 5),
        ];

        // Client secrets: only update if a non-blank value was submitted (never erase existing).
        $sbSecPost = trim($_POST['paypal_sandbox_client_secret'] ?? '');
        $formVals['paypal_sandbox_client_secret'] = ($sbSecPost !== '') ? $sbSecPost : ($cfgVals['paypal_sandbox_client_secret'] ?? '');

        $lvSecPost = trim($_POST['paypal_live_client_secret'] ?? '');
        $formVals['paypal_live_client_secret'] = ($lvSecPost !== '') ? $lvSecPost : ($cfgVals['paypal_live_client_secret'] ?? '');

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

            // Backup before write.
            // Note: the backup copy and subsequent file_put_contents are not covered by a
            // single atomic lock.  This is acceptable for an admin-only operation where
            // concurrent writes are not expected.
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

                        $cfgVals         = $formVals; // update displayed values
                        $computedWebhookUrl = rtrim($formVals['SITE_BASE_URL'], '/') . ('/' . ltrim($formVals['paypal_webhook_path'] ?? '/paypal/webhook.php', '/'));
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
            // Backup then write (admin-only operation; concurrent writes are not expected).
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
          Full base URL <strong>without trailing slash</strong> (e.g. <code>https://gameservers.world</code>).
          Leave empty to use relative paths. Used to compute the full public PayPal webhook URL.
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

      <?php
      $isSandboxMode = ($cfgVals['paypal_mode'] ?? 'sandbox') !== 'live';
      $modeLabel     = $isSandboxMode ? '🟡 Sandbox (test mode)' : '🟢 Live (real payments)';
      $modeBadgeClass = $isSandboxMode ? 'status-info' : 'status-success';
      ?>
      <div class="status-box <?php echo h($modeBadgeClass); ?>" style="margin-bottom:14px;font-size:0.95em;">
        Currently active PayPal mode: <strong><?php echo h($modeLabel); ?></strong>
      </div>

      <!-- PayPal Mode -->
      <div class="field-group">
        <label for="cfg_mode">PayPal Mode</label>
        <div class="field-help">
          <strong>Sandbox</strong> uses test credentials and the PayPal sandbox API — safe for development.
          <strong>Live</strong> processes real payments. Switch only after configuring live credentials.
        </div>
        <select id="cfg_mode" name="paypal_mode">
          <option value="sandbox" <?php echo $isSandboxMode ? 'selected' : ''; ?>>Sandbox (test mode)</option>
          <option value="live"    <?php echo !$isSandboxMode ? 'selected' : ''; ?>>Live (real payments)</option>
        </select>
      </div>

      <!-- Sandbox credentials -->
      <h4 style="color:#555;margin:20px 0 8px;">Sandbox Credentials</h4>
      <div class="field-group">
        <label for="cfg_sb_id">Sandbox Client ID</label>
        <div class="field-help">Found in PayPal Developer Dashboard → sandbox app. Safe to expose in browser JS.</div>
        <input type="text" id="cfg_sb_id" name="paypal_sandbox_client_id"
               value="<?php echo h((string)$cfgVals['paypal_sandbox_client_id']); ?>"
               placeholder="AfvY_... or sandbox client ID">
      </div>
      <div class="field-group">
        <label for="cfg_sb_sec">Sandbox Client Secret</label>
        <div class="field-help"><strong>Server-side only</strong> — never sent to the browser. Leave blank to keep existing value.</div>
        <div class="pw-wrap">
          <input type="password" id="cfg_sb_sec" name="paypal_sandbox_client_secret"
                 placeholder="<?php echo $cfgVals['paypal_sandbox_client_secret'] !== '' ? '(set — leave blank to keep)' : '(not set)'; ?>"
                 autocomplete="new-password">
          <button type="button" class="btn-show"
                  onclick="var f=document.getElementById('cfg_sb_sec');f.type=f.type==='password'?'text':'password';this.textContent=f.type==='password'?'Show':'Hide';">Show</button>
        </div>
      </div>
      <div class="field-group">
        <label for="cfg_sb_wh">Sandbox Webhook ID</label>
        <div class="field-help">
          Webhook ID from your PayPal sandbox app (for signature verification).
          Leave empty to skip verification in sandbox mode (OK for initial setup).
        </div>
        <input type="text" id="cfg_sb_wh" name="paypal_sandbox_webhook_id"
               value="<?php echo h((string)$cfgVals['paypal_sandbox_webhook_id']); ?>"
               placeholder="Sandbox Webhook ID">
      </div>

      <!-- Live credentials -->
      <h4 style="color:#555;margin:20px 0 8px;">Live Credentials</h4>
      <div class="field-group">
        <label for="cfg_lv_id">Live Client ID</label>
        <div class="field-help">From your PayPal live app. Leave blank until ready for production.</div>
        <input type="text" id="cfg_lv_id" name="paypal_live_client_id"
               value="<?php echo h((string)$cfgVals['paypal_live_client_id']); ?>"
               placeholder="Live Client ID">
      </div>
      <div class="field-group">
        <label for="cfg_lv_sec">Live Client Secret</label>
        <div class="field-help"><strong>Server-side only.</strong> Leave blank to keep existing value.</div>
        <div class="pw-wrap">
          <input type="password" id="cfg_lv_sec" name="paypal_live_client_secret"
                 placeholder="<?php echo $cfgVals['paypal_live_client_secret'] !== '' ? '(set — leave blank to keep)' : '(not set)'; ?>"
                 autocomplete="new-password">
          <button type="button" class="btn-show"
                  onclick="var f=document.getElementById('cfg_lv_sec');f.type=f.type==='password'?'text':'password';this.textContent=f.type==='password'?'Show':'Hide';">Show</button>
        </div>
      </div>
      <div class="field-group">
        <label for="cfg_lv_wh">Live Webhook ID</label>
        <div class="field-help">Webhook ID from your PayPal live app (for signature verification).</div>
        <input type="text" id="cfg_lv_wh" name="paypal_live_webhook_id"
               value="<?php echo h((string)$cfgVals['paypal_live_webhook_id']); ?>"
               placeholder="Live Webhook ID">
      </div>

      <!-- Webhook path + computed URL -->
      <h4 style="color:#555;margin:20px 0 8px;">Webhook Endpoint</h4>
      <div class="field-help" style="margin-bottom:10px;">
        PayPal requires a <strong>full public HTTPS URL</strong> to deliver webhook events.
        Set your Site Base URL above, then copy the computed URL below into your PayPal app's webhook configuration.
      </div>
      <div class="field-group">
        <label for="cfg_wh_path">Webhook Path</label>
        <div class="field-help">Path relative to the billing site root (must start with <code>/</code>). Default: <code>/paypal/webhook.php</code></div>
        <input type="text" id="cfg_wh_path" name="paypal_webhook_path"
               value="<?php echo h((string)$cfgVals['paypal_webhook_path']); ?>"
               placeholder="/paypal/webhook.php"
               oninput="updateWebhookUrl()">
      </div>
      <div class="field-group">
        <label>Computed Full Webhook URL <small style="font-weight:normal;color:#888;">(read-only — paste this into PayPal)</small></label>
        <div class="field-help">
          This is the URL PayPal will POST webhook events to.
          It must be publicly accessible over HTTPS before enabling live mode.
        </div>
        <input type="text" id="computed_webhook_url"
               class="readonly-field"
               value="<?php echo h($computedWebhookUrl); ?>"
               readonly
               style="font-family:monospace;color:#333;background:#f0f4ff;">
        <button type="button" id="copy_webhook_url_btn" class="btn-show" style="margin-top:4px;"
                onclick="var u=document.getElementById('computed_webhook_url');if(u){navigator.clipboard.writeText(u.value).then(function(){var b=document.getElementById('copy_webhook_url_btn');b.textContent='Copied!';setTimeout(function(){b.textContent='Copy';},2000);});}">Copy</button>
      </div>
      <script>
        function updateWebhookUrl() {
          var base = document.getElementById('cfg_base_url');
          var path = document.getElementById('cfg_wh_path');
          var out  = document.getElementById('computed_webhook_url');
          if (!base || !path || !out) return;
          var b = base.value.replace(/\/+$/, '');
          var p = path.value.replace(/^([^\/])/, '/$1');
          out.value = b + p;
        }
        document.addEventListener('DOMContentLoaded', function() {
          var base = document.getElementById('cfg_base_url');
          if (base) base.addEventListener('input', updateWebhookUrl);
        });
      </script>

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
       SECTION B: PayPal Diagnostics
  ==================================================================== -->
  <?php
  // Gather diagnostics data
  $diag_mode         = $cfgVals['paypal_mode'] ?? 'sandbox';
  $diag_is_sandbox   = $diag_mode !== 'live';
  $diag_sb_id_set    = ($cfgVals['paypal_sandbox_client_id']     ?? '') !== '';
  $diag_sb_sec_set   = ($cfgVals['paypal_sandbox_client_secret'] ?? '') !== '';
  $diag_sb_wh_set    = ($cfgVals['paypal_sandbox_webhook_id']    ?? '') !== '';
  $diag_lv_id_set    = ($cfgVals['paypal_live_client_id']        ?? '') !== '';
  $diag_lv_sec_set   = ($cfgVals['paypal_live_client_secret']    ?? '') !== '';
  $diag_lv_wh_set    = ($cfgVals['paypal_live_webhook_id']       ?? '') !== '';
  $diag_wh_path      = '/' . ltrim((string)($cfgVals['paypal_webhook_path'] ?? '/paypal/webhook.php'), '/');
  $diag_wh_full_url  = $computedWebhookUrl;
  $diag_wh_file      = rtrim(__DIR__, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($diag_wh_path, '/');
  $diag_wh_exists    = file_exists($diag_wh_file);

  // Active mode credential check
  $diag_active_id_set  = $diag_is_sandbox ? $diag_sb_id_set  : $diag_lv_id_set;
  $diag_active_sec_set = $diag_is_sandbox ? $diag_sb_sec_set : $diag_lv_sec_set;
  $diag_active_wh_set  = $diag_is_sandbox ? $diag_sb_wh_set  : $diag_lv_wh_set;

  function diag_badge(bool $ok, string $yes = 'Yes', string $no = 'No'): string {
      $cls   = $ok ? 'background:#d4edda;color:#155724;border:1px solid #c3e6cb;' : 'background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;';
      $label = $ok ? $yes : $no;
      return '<span style="' . $cls . 'padding:2px 8px;border-radius:3px;font-size:0.85em;font-weight:600;display:inline-block;word-break:break-word;">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
  }

  // Last webhook events + recent PayPal errors
  $diag_recent_events = [];
  $diag_recent_errors = [];
  $diag_errors_warning = '';
  try {
      $port_int = intval($db_port ?? 3306) ?: 3306;
      $diag_db  = @mysqli_connect($db_host ?? 'localhost', $db_user ?? '', $db_pass ?? '', $db_name ?? '', $port_int);
      if ($diag_db) {
          $pfx_diag = $table_prefix ?? 'gsp_';
          mysqli_set_charset($diag_db, 'utf8mb4');

          $res = @mysqli_query($diag_db, "SELECT paypal_event_id, event_type, processing_status, created_at FROM `{$pfx_diag}billing_paypal_webhook_events` ORDER BY id DESC LIMIT 5");
          if ($res) {
              while ($row = mysqli_fetch_assoc($res)) {
                  $diag_recent_events[] = $row;
              }
          }

          // Recent PayPal errors — use BillingRepository for safe table creation
          require_once __DIR__ . '/classes/BillingRepository.php';
          $diag_repo = new BillingRepository($diag_db, $pfx_diag);
          if ($diag_repo->ensureBillingPaypalErrorsTable()) {
              $diag_recent_errors = $diag_repo->getRecentPaypalErrors(10);
          } else {
              $diag_errors_warning = 'Could not create billing_paypal_errors table. Check DB permissions.';
          }

          mysqli_close($diag_db);
      }
  } catch (Throwable $e) {
      $diag_errors_warning = 'Diagnostics DB query failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
  }
  ?>
  <style>
    .diag-grid { display:grid; grid-template-columns:1fr; gap:8px; font-size:0.9em; }
    @media (min-width:600px) { .diag-grid { grid-template-columns:220px 1fr; } }
    .diag-row { display:contents; }
    .diag-label { color:#555; font-weight:600; padding:6px 0; border-bottom:1px solid #f0f0f0; word-break:break-word; }
    .diag-value { padding:6px 0; border-bottom:1px solid #f0f0f0; word-break:break-all; }
    .diag-sub { font-size:0.85em; color:#888; margin-top:4px; }
    .diag-sep { grid-column:1/-1; border-top:2px solid #e9ecef; margin:6px 0 2px; }
    .recent-errors-table { width:100%; border-collapse:collapse; font-size:0.85em; overflow-x:auto; display:block; }
    .recent-errors-table th { background:#f8f9fa; padding:6px 8px; text-align:left; border-bottom:2px solid #dee2e6; white-space:nowrap; }
    .recent-errors-table td { padding:5px 8px; border-bottom:1px solid #eee; word-break:break-word; }
  </style>
  <div class="cfg-section">
    <h2>PayPal Diagnostics</h2>

    <!-- Self-check button -->
    <form method="post" style="margin-bottom:16px;">
      <input type="hidden" name="csrf"   value="<?php echo h($csrf); ?>">
      <input type="hidden" name="action" value="self_check">
      <button type="submit" class="btn-show" style="padding:9px 18px;font-size:0.95em;">🔍 Run Billing Self-Check</button>
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'self_check') {
        $token = $_POST['csrf'] ?? '';
        if (hash_equals($csrf, (string)$token)):
    ?>
    <div class="status-box status-info" style="font-size:0.9em;">
      <strong>Self-Check Results:</strong><br>
      &bull; Mode: <strong><?php echo h($diag_mode ?: '(unknown)'); ?></strong><br>
      &bull; Active Client ID: <?php echo $diag_active_id_set  ? '✅ configured' : '❌ missing'; ?><br>
      &bull; Active Client Secret: <?php echo $diag_active_sec_set ? '✅ configured' : '❌ missing'; ?><br>
      &bull; Active Webhook ID: <?php echo $diag_active_wh_set  ? '✅ configured' : '⚠️ missing (signature verification skipped)'; ?><br>
      &bull; Webhook file: <?php echo $diag_wh_exists ? '✅ exists' : '❌ not found'; ?> — <code style="word-break:break-all"><?php echo h($diag_wh_file); ?></code><br>
      &bull; Logs directory: <?php $logDir = __DIR__ . '/logs'; echo (is_dir($logDir) && is_writable($logDir)) ? '✅ writable' : '⚠️ ' . (is_dir($logDir) ? 'not writable' : 'missing'); ?><br>
      &bull; Data directory: <?php echo (is_dir($SITE_DATA_DIR ?? '') && is_writable($SITE_DATA_DIR ?? '')) ? '✅ writable' : '⚠️ check path'; ?><br>
      &bull; Config file: <?php echo is_writable($cfgPath) ? '✅ writable' : '⚠️ read-only'; ?><br>
    </div>
    <?php endif; } ?>

    <div class="diag-grid">
      <div class="diag-row">
        <div class="diag-label">Current mode</div>
        <div class="diag-value">
          <strong><?php echo h($diag_mode !== '' ? $diag_mode : '(not set)'); ?></strong>
          <?php if ($diag_mode === 'sandbox'): ?>
            <span style="background:#fff3cd;color:#856404;border:1px solid #ffc107;padding:1px 7px;border-radius:3px;font-size:0.8em;margin-left:6px;">test</span>
          <?php elseif ($diag_mode === 'live'): ?>
            <span style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;padding:1px 7px;border-radius:3px;font-size:0.8em;margin-left:6px;">live</span>
          <?php endif; ?>
        </div>
      </div>

      <div class="diag-sep"></div>

      <div class="diag-row">
        <div class="diag-label">Active Client ID</div>
        <div class="diag-value"><?php echo diag_badge($diag_active_id_set); ?></div>
      </div>
      <div class="diag-row">
        <div class="diag-label">Active Client Secret</div>
        <div class="diag-value"><?php echo diag_badge($diag_active_sec_set); ?></div>
      </div>
      <div class="diag-row">
        <div class="diag-label">Active Webhook ID</div>
        <div class="diag-value"><?php echo diag_badge($diag_active_wh_set, 'Yes', 'No — signature verification skipped'); ?></div>
      </div>

      <div class="diag-sep"></div>

      <div class="diag-row">
        <div class="diag-label">Sandbox Client ID</div>
        <div class="diag-value"><?php echo diag_badge($diag_sb_id_set); ?></div>
      </div>
      <div class="diag-row">
        <div class="diag-label">Sandbox Client Secret</div>
        <div class="diag-value"><?php echo diag_badge($diag_sb_sec_set); ?></div>
      </div>
      <div class="diag-row">
        <div class="diag-label">Sandbox Webhook ID</div>
        <div class="diag-value"><?php echo diag_badge($diag_sb_wh_set); ?></div>
      </div>

      <div class="diag-sep"></div>

      <div class="diag-row">
        <div class="diag-label">Live Client ID</div>
        <div class="diag-value"><?php echo diag_badge($diag_lv_id_set); ?></div>
      </div>
      <div class="diag-row">
        <div class="diag-label">Live Client Secret</div>
        <div class="diag-value"><?php echo diag_badge($diag_lv_sec_set); ?></div>
      </div>
      <div class="diag-row">
        <div class="diag-label">Live Webhook ID</div>
        <div class="diag-value"><?php echo diag_badge($diag_lv_wh_set); ?></div>
      </div>

      <div class="diag-sep"></div>

      <div class="diag-row">
        <div class="diag-label">Webhook path</div>
        <div class="diag-value"><code><?php echo h($diag_wh_path); ?></code></div>
      </div>
      <div class="diag-row">
        <div class="diag-label">Full public webhook URL</div>
        <div class="diag-value">
          <code><?php echo h($diag_wh_full_url !== '' ? $diag_wh_full_url : '(Site Base URL not configured)'); ?></code>
        </div>
      </div>
      <div class="diag-row">
        <div class="diag-label">Webhook file on disk</div>
        <div class="diag-value">
          <?php echo diag_badge($diag_wh_exists, 'Found', 'Not found'); ?>
          <div class="diag-sub"><code><?php echo h($diag_wh_file); ?></code></div>
        </div>
      </div>
    </div>

    <?php if (!empty($diag_recent_events)): ?>
    <h4 style="margin-top:22px;color:#555;">Recent Webhook Events</h4>
    <div style="overflow-x:auto;">
    <table class="recent-errors-table">
      <thead><tr>
        <th>PayPal Event ID</th>
        <th>Type</th>
        <th>Status</th>
        <th>Received</th>
      </tr></thead>
      <tbody>
      <?php foreach ($diag_recent_events as $ev): ?>
        <tr>
          <td><code><?php echo h($ev['paypal_event_id'] ?: '—'); ?></code></td>
          <td><?php echo h($ev['event_type']); ?></td>
          <td><?php echo diag_badge($ev['processing_status'] === 'processed', $ev['processing_status'], $ev['processing_status']); ?></td>
          <td><?php echo h($ev['created_at']); ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <?php else: ?>
    <p style="color:#888;font-size:0.9em;margin-top:12px;">No webhook events recorded yet. Events will appear here after PayPal delivers the first webhook to <code><?php echo h($diag_wh_full_url ?: $diag_wh_path); ?></code>.</p>
    <?php endif; ?>

    <h4 style="margin-top:22px;color:#555;">Recent PayPal Errors</h4>
    <?php if ($diag_errors_warning): ?>
    <div class="warn-box"><?php echo h($diag_errors_warning); ?></div>
    <?php elseif (empty($diag_recent_errors)): ?>
    <p style="color:#888;font-size:0.9em;">No PayPal errors logged yet.</p>
    <?php else: ?>
    <div style="overflow-x:auto;">
    <table class="recent-errors-table">
      <thead><tr>
        <th>Time</th><th>Context</th><th>Error Code</th><th>Message</th>
        <th>Debug ID</th><th>Order ID</th><th>User</th>
      </tr></thead>
      <tbody>
      <?php foreach ($diag_recent_errors as $er): ?>
        <tr>
          <td style="white-space:nowrap"><?php echo h($er['created_at']); ?></td>
          <td><?php echo h($er['context']); ?></td>
          <td><code><?php echo h($er['error_code']); ?></code></td>
          <td><?php echo h($er['message']); ?></td>
          <td><code><?php echo h($er['paypal_debug_id'] ?? '—'); ?></code></td>
          <td><code><?php echo h($er['order_id'] ?? '—'); ?></code></td>
          <td><?php echo h($er['user_id'] ?? '—'); ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- ===================================================================
       SECTION C: Raw PHP editor
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
