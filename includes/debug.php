<?php
/*
 * GSP – Global Debug System
 * -------------------------
 * Loaded early in every request (after config.inc.php defines DEBUG_MODE).
 *
 * Constants (set in includes/config.inc.php):
 *   DEBUG_MODE  – true/false master switch.
 *   DEBUG_LEVEL – 0=off, 1=fatal only, 2=errors+warnings, 3=all (default 1)
 *
 * The panel Settings page exposes a "debug_level" dropdown that overrides
 * DEBUG_LEVEL at runtime (applied in home.php after the DB is ready).
 */

if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', false);
}
if (!defined('DEBUG_LEVEL')) {
    define('DEBUG_LEVEL', 1); // fatal errors only by default
}

if (!DEBUG_MODE) {
    // Production: suppress all output, only log to server error log
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');
    return;
}

// ── Development mode ──────────────────────────────────────────────────────────

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('log_errors', '1');

// Apply the initial level from the constant; may be overridden from DB later.
gsp_apply_debug_level(DEBUG_LEVEL);

// Accumulated non-fatal errors collected by the custom handler
$GLOBALS['_gsp_debug_errors'] = [];

/**
 * Apply an error-reporting level for GSP debug mode.
 *
 * Level map:
 *   0 = off           – suppress everything
 *   1 = fatal only    – E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR
 *   2 = errors+warnings – adds E_WARNING, E_USER_ERROR, E_USER_WARNING
 *   3 = all           – E_ALL
 *
 * Safe to call multiple times (e.g. once from config, once after DB load).
 */
function gsp_apply_debug_level(int $level): void
{
    switch ($level) {
        case 0:
            error_reporting(0);
            ini_set('display_errors', '0');
            break;
        case 1:
            error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
            ini_set('display_errors', '1');
            break;
        case 2:
            error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR
                | E_RECOVERABLE_ERROR | E_WARNING | E_USER_ERROR | E_USER_WARNING);
            ini_set('display_errors', '1');
            break;
        case 3:
        default:
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
            break;
    }
}


/**
 * Custom error handler – captures E_WARNING, E_NOTICE, E_DEPRECATED, etc.
 * Fatal errors (E_ERROR, E_PARSE …) are handled by the shutdown function.
 */
set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
    // Respect the @ operator (error suppression)
    if (!(error_reporting() & $errno)) {
        return false;
    }

    $levels = [
        E_WARNING          => 'Warning',
        E_NOTICE           => 'Notice',
        E_DEPRECATED       => 'Deprecated',
        E_USER_ERROR       => 'User Error',
        E_USER_WARNING     => 'User Warning',
        E_USER_NOTICE      => 'User Notice',
        E_USER_DEPRECATED  => 'User Deprecated',
        E_STRICT           => 'Strict',
        E_RECOVERABLE_ERROR => 'Recoverable Error',
    ];

    $type = $levels[$errno] ?? "Error (#{$errno})";

    $GLOBALS['_gsp_debug_errors'][] = [
        'type'    => $type,
        'message' => $errstr,
        'file'    => $errfile,
        'line'    => $errline,
    ];

    // Do NOT suppress the built-in handler (allows display_errors to also show inline)
    return false;
});

/**
 * Shutdown handler – catches fatal / parse / compile errors and renders them.
 * Also renders the non-fatal error panel collected above.
 */
register_shutdown_function(function (): void {
    if (!defined('DEBUG_MODE') || !DEBUG_MODE) {
        return;
    }

    $fatal = error_get_last();
    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_CORE_WARNING, E_COMPILE_WARNING];
    $hasFatal = $fatal && in_array($fatal['type'], $fatalTypes, true);

    $nonFatalErrors = $GLOBALS['_gsp_debug_errors'] ?? [];

    if (!$hasFatal && empty($nonFatalErrors)) {
        return;
    }

    // Attempt to end any open output buffers so our panel appears at the bottom
    while (ob_get_level() > 0) {
        ob_end_flush();
    }

    echo gsp_debug_render_panel($hasFatal ? $fatal : null, $nonFatalErrors);
});

/**
 * Renders the styled debug panel HTML.
 *
 * @param array|null $fatal       Fatal error array from error_get_last(), or null
 * @param array      $nonFatals   Array of non-fatal error entries
 * @return string
 */
function gsp_debug_render_panel(?array $fatal, array $nonFatals): string
{
    $docRoot  = defined('DOCUMENT_ROOT') ? DOCUMENT_ROOT : ($_SERVER['DOCUMENT_ROOT'] ?? '');
    $stripLen = strlen($docRoot);

    $shortPath = static function (string $path) use ($docRoot, $stripLen): string {
        if ($stripLen > 0 && strpos($path, $docRoot) === 0) {
            return '…' . substr($path, $stripLen);
        }
        return $path;
    };

    $esc = static fn(string $s): string => htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    $html  = '<div id="gsp-debug-panel" style="'
           . 'position:relative;z-index:99999;margin:32px 0 0;padding:0;'
           . 'font-family:monospace;font-size:13px;line-height:1.5;'
           . 'border-top:3px solid #c0392b;background:#1e1e1e;color:#d4d4d4;">';

    $html .= '<div style="padding:10px 16px;background:#2d2d2d;color:#fff;'
           . 'font-weight:bold;font-size:14px;letter-spacing:.5px;">'
           . '⚠ GSP Debug Panel</div>';

    // ── Fatal error block ──────────────────────────────────────────────────
    if ($fatal) {
        $fatalLabels = [
            E_ERROR         => 'Fatal Error',
            E_PARSE         => 'Parse Error',
            E_CORE_ERROR    => 'Core Error',
            E_COMPILE_ERROR => 'Compile Error',
            E_CORE_WARNING  => 'Core Warning',
            E_COMPILE_WARNING => 'Compile Warning',
        ];
        $label = $fatalLabels[$fatal['type']] ?? 'Fatal';

        $html .= '<div style="margin:12px 16px;padding:12px 14px;'
               . 'background:#3d1515;border-left:4px solid #c0392b;'
               . 'border-radius:3px;">'
               . '<span style="color:#e74c3c;font-weight:bold;">[' . $esc($label) . ']</span> '
               . '<span style="color:#f5c842;">' . $esc($fatal['message']) . '</span><br>'
               . '<span style="color:#888;font-size:11px;">'
               . $esc($shortPath($fatal['file'])) . ' &nbsp;line&nbsp;'
               . (int)$fatal['line']
               . '</span></div>';
    }

    // ── Non-fatal errors ──────────────────────────────────────────────────
    if (!empty($nonFatals)) {
        $typeColors = [
            'Warning'          => '#e67e22',
            'Notice'           => '#3498db',
            'Deprecated'       => '#9b59b6',
            'Strict'           => '#1abc9c',
            'User Error'       => '#e74c3c',
            'User Warning'     => '#e67e22',
            'User Notice'      => '#3498db',
            'User Deprecated'  => '#9b59b6',
            'Recoverable Error'=> '#e74c3c',
        ];

        foreach ($nonFatals as $err) {
            $color = $typeColors[$err['type']] ?? '#aaa';
            $html .= '<div style="margin:6px 16px;padding:8px 12px;'
                   . 'background:#252525;border-left:3px solid ' . $color . ';'
                   . 'border-radius:2px;">'
                   . '<span style="color:' . $color . ';font-weight:bold;">[' . $esc($err['type']) . ']</span> '
                   . '<span style="color:#e8e8e8;">' . $esc($err['message']) . '</span><br>'
                   . '<span style="color:#666;font-size:11px;">'
                   . $esc($shortPath($err['file'])) . ' &nbsp;line&nbsp;'
                   . (int)$err['line']
                   . '</span></div>';
        }
    }

    $total = count($nonFatals) + ($fatal ? 1 : 0);
    $html .= '<div style="padding:8px 16px;color:#555;font-size:11px;">'
           . $total . ' issue(s) captured &mdash; DEBUG_MODE is ON. '
           . 'Set <code>define(\'DEBUG_MODE\', false);</code> in config.inc.php to disable.</div>';

    $html .= '</div>' . PHP_EOL;

    return $html;
}
