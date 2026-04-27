<?php
/*
 * GSP - Game Server Panel (WDS)
 * Copyright (C) 2008 - 2018 The OGP Development Team
 * GSP customizations (C) WDS / GameServerPanel
 *
 * GSP is a heavily customized fork of OGP maintained by WDS.
 * https://github.com/GameServerPanel/GSP
 *
 * Dependency Check Page — check.php
 * Safe to run before install or at any time. Never blocks installation.
 */

// Never leak PHP errors in output (log them server-side instead).
ini_set('display_errors', '0');
error_reporting(E_ALL);

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/**
 * Safely escape a value for HTML output.
 *
 * @param mixed $v
 * @return string
 */
function h($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Try to run a shell command via shell_exec.
 * Returns the trimmed output, or NULL when shell_exec is not available / the
 * command produced no output.
 *
 * @param string $cmd The command to run (must already be safe / validated).
 * @return string|null
 */
function safe_shell(?string $cmd): ?string {
    if (!function_exists('shell_exec')) return null;
    $out = @shell_exec($cmd);
    return ($out !== null && $out !== '') ? trim($out) : null;
}

/**
 * Check whether a Linux command exists via "command -v".
 * Returns "ok", "missing", or "unknown".
 *
 * @param string $cmd Binary name, alphanumeric + dashes only.
 * @return string
 */
function check_command(string $cmd): string {
    $safe = preg_replace('/[^a-zA-Z0-9_\-]/', '', $cmd);
    $result = safe_shell('command -v ' . escapeshellarg($safe) . ' 2>/dev/null');
    if ($result === null) return 'unknown';
    return ($result !== '') ? 'ok' : 'missing';
}

// ---------------------------------------------------------------------------
// Constants
// ---------------------------------------------------------------------------

define('CHECK_BASE', __DIR__);
define('PANEL_VERSION_MIN', '8.3.0');

// ---------------------------------------------------------------------------
// Run all checks and collect rows
// ---------------------------------------------------------------------------

$rows = [];

// ── PHP version ─────────────────────────────────────────────────────────────
$php_ver = PHP_VERSION;
$php_ok  = version_compare($php_ver, PANEL_VERSION_MIN, '>=');
$rows[] = [
    'section' => 'PHP Runtime',
    'name'    => 'PHP Version',
    'status'  => $php_ok ? 'ok' : 'warning',
    'current' => $php_ver,
    'fix'     => $php_ok ? '' : 'sudo apt install php8.3 libapache2-mod-php8.3 -y',
    'notes'   => 'Minimum recommended: PHP ' . PANEL_VERSION_MIN,
];

// ── Required PHP extensions ──────────────────────────────────────────────────
$required_exts = [
    'mysqli'   => 'Database connectivity',
    'curl'     => 'Remote HTTP requests',
    'gd'       => 'Image processing',
    'mbstring' => 'Multi-byte string handling',
    'zip'      => 'Archive extraction',
    'xml'      => 'XML parsing (game configs)',
    'json'     => 'JSON encoding/decoding',
    'openssl'  => 'Encrypted connections',
    'fileinfo' => 'MIME type detection',
    'session'  => 'Session management',
];

foreach ($required_exts as $ext => $desc) {
    $loaded = extension_loaded($ext);
    $rows[] = [
        'section' => 'PHP Extensions',
        'name'    => 'ext/' . $ext,
        'status'  => $loaded ? 'ok' : 'missing',
        'current' => $loaded ? 'Loaded' : 'Not loaded',
        'fix'     => $loaded ? '' : 'sudo apt install php8.3-' . $ext . ' -y',
        'notes'   => $desc,
    ];
}

// xmlrpc is packaged separately on modern Debian/Ubuntu so check it alone.
$xmlrpc_loaded = extension_loaded('xmlrpc');
$rows[] = [
    'section' => 'PHP Extensions',
    'name'    => 'ext/xmlrpc',
    'status'  => $xmlrpc_loaded ? 'ok' : 'warning',
    'current' => $xmlrpc_loaded ? 'Loaded' : 'Not loaded',
    'fix'     => $xmlrpc_loaded ? '' : 'sudo apt install php8.3-xmlrpc -y',
    'notes'   => 'Required for agent communication. May need separate package on PHP 8+.',
];

// ── PEAR ─────────────────────────────────────────────────────────────────────
$pear_path = stream_resolve_include_path('PEAR.php');
$rows[] = [
    'section' => 'PHP Libraries',
    'name'    => 'PEAR',
    'status'  => $pear_path !== false ? 'ok' : 'warning',
    'current' => $pear_path !== false ? $pear_path : 'Not found',
    'fix'     => $pear_path !== false ? '' : 'sudo apt install php-pear -y',
    'notes'   => 'Used by some legacy OGP/GSP modules.',
];

// ── Writable / readable paths ────────────────────────────────────────────────
$paths_to_check = [
    'includes/'                  => 'Config directory (must be writable at install time)',
    'modules/'                   => 'Modules directory',
    'upload/'                    => 'Upload directory (optional)',
    'cache/'                     => 'Cache directory (optional)',
    'log/'                       => 'Log directory (optional)',
    'temp/'                      => 'Temp directory (optional)',
    'includes/config.inc.php'    => 'Panel config file (writable at install time)',
];

foreach ($paths_to_check as $rel => $note) {
    $abs      = CHECK_BASE . '/' . $rel;
    $optional = in_array($rel, ['upload/', 'cache/', 'log/', 'temp/'], true);

    if (!file_exists($abs)) {
        $rows[] = [
            'section' => 'Filesystem',
            'name'    => $rel,
            'status'  => $optional ? 'warning' : 'warning',
            'current' => 'Does not exist',
            'fix'     => 'mkdir -p ' . escapeshellarg($rel),
            'notes'   => $note . ($optional ? ' (optional)' : ''),
        ];
        continue;
    }

    $is_dir  = is_dir($abs);
    $readable = is_readable($abs);
    $writable = is_writable($abs);

    if ($is_dir) {
        $status = ($readable && $writable) ? 'ok' : 'warning';
        $cur    = 'Exists — readable: ' . ($readable ? 'yes' : 'no') . ', writable: ' . ($writable ? 'yes' : 'no');
        $fix    = (!$writable) ? 'sudo chmod -R 775 ' . escapeshellarg($rel) . ' && sudo chown -R www-data:www-data ' . escapeshellarg($rel) : '';
    } else {
        // It's a file
        $status = ($readable && $writable) ? 'ok' : 'warning';
        $cur    = 'Exists — readable: ' . ($readable ? 'yes' : 'no') . ', writable: ' . ($writable ? 'yes' : 'no');
        $fix    = (!$writable) ? 'sudo chmod 664 ' . escapeshellarg($rel) : '';
    }

    $rows[] = [
        'section' => 'Filesystem',
        'name'    => $rel,
        'status'  => $status,
        'current' => $cur,
        'fix'     => $fix,
        'notes'   => $note,
    ];
}

// ── Linux commands ───────────────────────────────────────────────────────────
$commands = ['unzip', 'tar', 'screen', 'sudo', 'subversion', 'git', 'rsync', 'mysql'];
$shell_available = function_exists('shell_exec');

foreach ($commands as $cmd) {
    $status_str = check_command($cmd);
    if (!$shell_available) {
        $status_str = 'unknown';
        $cur        = 'shell_exec disabled';
        $fix        = 'Enable shell_exec in php.ini';
    } else {
        $cur = $status_str === 'ok' ? safe_shell('command -v ' . escapeshellarg($cmd) . ' 2>/dev/null') ?? $cmd : 'Not found in PATH';
        $fix = ($status_str === 'missing') ? 'sudo apt install ' . escapeshellarg($cmd) . ' -y' : '';
    }

    $rows[] = [
        'section' => 'Linux Commands',
        'name'    => $cmd,
        'status'  => $status_str,
        'current' => $cur,
        'fix'     => $fix,
        'notes'   => 'Required for game server management',
    ];
}

// ── Apache modules ───────────────────────────────────────────────────────────
if (function_exists('apache_get_modules')) {
    $apache_mods   = apache_get_modules();
    $rewrite_loaded = in_array('mod_rewrite', $apache_mods, true);
    $rows[] = [
        'section' => 'Apache',
        'name'    => 'mod_rewrite',
        'status'  => $rewrite_loaded ? 'ok' : 'warning',
        'current' => $rewrite_loaded ? 'Enabled' : 'Not enabled',
        'fix'     => $rewrite_loaded ? '' : "sudo a2enmod rewrite\nsudo systemctl restart apache2",
        'notes'   => 'Required for clean panel URLs',
    ];
} else {
    $rows[] = [
        'section' => 'Apache',
        'name'    => 'mod_rewrite',
        'status'  => 'unknown',
        'current' => 'apache_get_modules() unavailable (CGI/FPM mode or non-Apache?)',
        'fix'     => "sudo a2enmod rewrite\nsudo systemctl restart apache2",
        'notes'   => 'Required for clean panel URLs. Verify manually if not using mod_php.',
    ];
}

// ── Optional DB connectivity test ────────────────────────────────────────────
$config_path = CHECK_BASE . '/includes/config.inc.php';
if (is_readable($config_path)) {
    // Extract credentials using regex instead of executing the config file,
    // to avoid running arbitrary PHP code from the config.
    $raw = file_get_contents($config_path);
    $cfg = [];
    foreach (['db_host', 'db_port', 'db_user', 'db_pass', 'db_name'] as $var) {
        if ($raw !== false && preg_match('/\$' . $var . '\s*=\s*"([^"]*)"/', $raw, $m)) {
            $cfg[$var] = $m[1];
        }
    }

    $db_host_cfg = $cfg['db_host'] ?? null;
    $db_user_cfg = $cfg['db_user'] ?? null;
    $db_pass_cfg = $cfg['db_pass'] ?? null;
    $db_name_cfg = $cfg['db_name'] ?? null;
    $db_port_cfg = isset($cfg['db_port']) ? (int)$cfg['db_port'] : 3306;

    if ($db_host_cfg !== null && $db_user_cfg !== null && $db_name_cfg !== null) {
        $conn = @mysqli_connect($db_host_cfg, $db_user_cfg, $db_pass_cfg ?? '', $db_name_cfg, $db_port_cfg);
        if ($conn) {
            $db_status  = 'ok';
            $db_current = 'Connected to ' . $db_host_cfg . ':' . $db_port_cfg . ' / ' . $db_name_cfg;
            $db_fix     = '';
            mysqli_close($conn);
        } else {
            $db_status  = 'warning';
            $db_current = 'Connection failed — ' . (mysqli_connect_error() ?? 'unknown error');
            $db_fix     = 'Check credentials in includes/config.inc.php';
        }

        $rows[] = [
            'section' => 'Database',
            'name'    => 'MySQL connection',
            'status'  => $db_status,
            'current' => $db_current,
            'fix'     => $db_fix,
            'notes'   => 'Host: ' . $db_host_cfg . ' | Port: ' . $db_port_cfg . ' | DB: ' . $db_name_cfg . ' | User: ' . $db_user_cfg,
        ];
    } else {
        $rows[] = [
            'section' => 'Database',
            'name'    => 'MySQL connection',
            'status'  => 'warning',
            'current' => 'config.inc.php present but incomplete (missing host/user/db)',
            'fix'     => 'Run the installer at install.php',
            'notes'   => 'Cannot test connection without full credentials',
        ];
    }
} else {
    $rows[] = [
        'section' => 'Database',
        'name'    => 'MySQL connection',
        'status'  => 'warning',
        'current' => 'config.inc.php not found or not readable — not yet installed',
        'fix'     => 'Run the installer at install.php',
        'notes'   => 'This is normal before first install',
    ];
}

// ---------------------------------------------------------------------------
// Summary counts
// ---------------------------------------------------------------------------
$count_ok      = 0;
$count_warning = 0;
$count_missing = 0;
$count_unknown = 0;

foreach ($rows as $r) {
    switch ($r['status']) {
        case 'ok':      $count_ok++;      break;
        case 'warning': $count_warning++; break;
        case 'missing': $count_missing++; break;
        default:        $count_unknown++; break;
    }
}

// ---------------------------------------------------------------------------
// Render HTML
// ---------------------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GSP / WDS — Dependency Check</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 14px;
            background: #1a1a2e;
            color: #e0e0e0;
            padding: 20px;
        }
        h1 { font-size: 22px; margin-bottom: 4px; color: #ffffff; }
        h2 { font-size: 15px; font-weight: 600; margin: 18px 0 6px; color: #ccc; border-bottom: 1px solid #333; padding-bottom: 4px; }
        .container { max-width: 1100px; margin: 0 auto; }
        .header { background: #16213e; border-radius: 8px; padding: 18px 22px; margin-bottom: 20px; border: 1px solid #0f3460; }
        .header p { color: #aaa; margin-top: 6px; font-size: 13px; }
        .summary { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; }
        .summary-box { flex: 1 1 130px; border-radius: 8px; padding: 14px 18px; text-align: center; }
        .summary-box .num { font-size: 32px; font-weight: bold; }
        .summary-box .lbl { font-size: 12px; text-transform: uppercase; letter-spacing: .05em; margin-top: 4px; }
        .summary-ok      { background: #1a3a1a; border: 1px solid #2e6b2e; color: #7ddb7d; }
        .summary-warning { background: #3a2e00; border: 1px solid #7a6000; color: #ffd84d; }
        .summary-missing { background: #3a1a1a; border: 1px solid #7a2e2e; color: #ff7b7b; }
        .summary-unknown { background: #252535; border: 1px solid #4a4a6a; color: #aaaacc; }
        .actions { margin-bottom: 22px; }
        .btn {
            display: inline-block; padding: 9px 20px; border-radius: 6px; text-decoration: none;
            font-size: 13px; font-weight: 600; border: none; cursor: pointer;
        }
        .btn-primary { background: #0f3460; color: #fff; border: 1px solid #1a5fa8; }
        .btn-primary:hover { background: #1a5fa8; }
        .btn-secondary { background: #2a2a4a; color: #aaa; border: 1px solid #4a4a7a; margin-left: 8px; }
        .btn-secondary:hover { background: #3a3a6a; color: #fff; }
        table { width: 100%; border-collapse: collapse; background: #12122a; border-radius: 8px; overflow: hidden; border: 1px solid #2a2a4a; margin-bottom: 20px; }
        thead th { background: #0d0d28; color: #aab; text-transform: uppercase; font-size: 11px; letter-spacing: .06em; padding: 10px 12px; text-align: left; border-bottom: 1px solid #2a2a5a; }
        tbody tr { border-bottom: 1px solid #1e1e3a; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #1a1a38; }
        tbody td { padding: 9px 12px; vertical-align: top; }
        .td-name { font-weight: 600; font-family: "Courier New", monospace; font-size: 13px; white-space: nowrap; }
        .td-status { white-space: nowrap; text-align: center; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; }
        .badge-ok      { background: #1a3a1a; color: #7ddb7d; border: 1px solid #2e6b2e; }
        .badge-warning { background: #3a2e00; color: #ffd84d; border: 1px solid #7a6000; }
        .badge-missing { background: #3a1a1a; color: #ff7b7b; border: 1px solid #7a2e2e; }
        .badge-unknown { background: #252535; color: #aaaacc; border: 1px solid #4a4a6a; }
        .td-fix code, .td-current code { font-family: "Courier New", monospace; font-size: 12px; background: #0d0d22; padding: 2px 6px; border-radius: 4px; color: #aaf; word-break: break-all; display: inline-block; }
        .td-fix pre  { font-family: "Courier New", monospace; font-size: 12px; background: #0d0d22; padding: 6px 8px; border-radius: 4px; color: #aaf; white-space: pre-wrap; word-break: break-all; }
        .td-notes { color: #888; font-size: 12px; }
        .section-header td { background: #0d1430; color: #7a9fd4; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; padding: 6px 12px; }
        .note-box { background: #1a2a1a; border: 1px solid #2e5a2e; border-radius: 8px; padding: 14px 18px; margin-bottom: 20px; color: #9ddd9d; font-size: 13px; }
        .note-box strong { color: #7ddb7d; }
        footer { text-align: center; color: #555; font-size: 11px; margin-top: 30px; }

    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <h1>🔍 GSP / WDS — Dependency Check</h1>
        <p>This page checks the server environment for GSP panel compatibility.
           <strong>No dependency is a hard blocker</strong> — missing items appear as warnings only.
           The installer at <a href="install.php" style="color:#7aaaf5">install.php</a> can proceed regardless.</p>
    </div>

    <!-- Summary boxes -->
    <div class="summary">
        <div class="summary-box summary-ok">
            <div class="num"><?= $count_ok ?></div>
            <div class="lbl">OK</div>
        </div>
        <div class="summary-box summary-warning">
            <div class="num"><?= $count_warning ?></div>
            <div class="lbl">Warning</div>
        </div>
        <div class="summary-box summary-missing">
            <div class="num"><?= $count_missing ?></div>
            <div class="lbl">Missing</div>
        </div>
        <div class="summary-box summary-unknown">
            <div class="num"><?= $count_unknown ?></div>
            <div class="lbl">Unknown</div>
        </div>
    </div>

    <!-- Action buttons -->
    <div class="actions">
        <a href="install.php" class="btn btn-primary">⚙ Run Installer</a>
        <a href="check.php"   class="btn btn-secondary">↺ Refresh Check</a>
    </div>

    <?php if ($count_missing === 0 && $count_warning === 0): ?>
    <div class="note-box">
        <strong>✔ All checks passed.</strong>
        Your server environment looks good for GSP installation.
    </div>
    <?php endif; ?>

    <!-- Results table -->
    <table>
        <thead>
            <tr>
                <th style="width:160px">Name</th>
                <th style="width:90px;text-align:center">Status</th>
                <th style="width:220px">Current Value</th>
                <th>Recommended Fix</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $last_section = null;
        foreach ($rows as $row):
            if ($row['section'] !== $last_section):
                $last_section = $row['section'];
        ?>
            <tr class="section-header">
                <td colspan="5"><?= h($row['section']) ?></td>
            </tr>
        <?php endif; ?>
            <tr>
                <td class="td-name"><code><?= h($row['name']) ?></code></td>
                <td class="td-status">
                    <span class="badge badge-<?= h($row['status']) ?>">
                        <?= h($row['status']) ?>
                    </span>
                </td>
                <td class="td-current"><?= h($row['current']) ?></td>
                <td class="td-fix">
                    <?php if (!empty($row['fix'])): ?>
                        <?php if (strpos($row['fix'], "\n") !== false): ?>
                            <pre><?= h($row['fix']) ?></pre>
                        <?php else: ?>
                            <code><?= h($row['fix']) ?></code>
                        <?php endif; ?>
                    <?php else: ?>
                        <span style="color:#555">—</span>
                    <?php endif; ?>
                </td>
                <td class="td-notes"><?= h($row['notes']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Suggested apt install block -->
    <h2>📦 Ubuntu 24.04 — Full Dependency Install</h2>
    <table>
        <thead><tr><th>One-liner to install all recommended packages</th></tr></thead>
        <tbody>
            <tr><td><pre><?= h(
'sudo apt update
sudo apt install apache2 mysql-client unzip tar screen sudo subversion git rsync \
    php8.3 php8.3-mysql php8.3-gd php8.3-curl php8.3-mbstring php8.3-zip \
    php8.3-xml php8.3-xmlrpc php-pear libapache2-mod-php8.3 -y
sudo a2enmod rewrite
sudo systemctl restart apache2'
            ) ?></pre></td></tr>
        </tbody>
    </table>

    <footer>GSP / WDS Dependency Checker — safe to run at any time &mdash; generated at <?= h(gmdate('Y-m-d H:i:s')) ?> UTC</footer>
</div>
</body>
</html>
