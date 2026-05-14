<?php
// Simple site logging helper for _website
// Writes to _website/logs/YYYY-MM-DD.log

function site_log_dir(){
    $d = __DIR__ . '/../logs';
    if (!is_dir($d)) @mkdir($d, 0775, true);
    return $d;
}

function site_log_filename(){
    $d = site_log_dir();
    return rtrim($d, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log';
}

function site_log($level, $message, $meta = null){
    $ts = date('c');
    $lvl = strtoupper(substr((string)$level,0,10));
    $line = "[{$ts}] [{$lvl}] {$message}";
    if ($meta !== null) {
        if (!is_string($meta)) $meta = json_encode($meta, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        $line .= ' | ' . $meta;
    }
    $file = site_log_filename();
    @file_put_contents($file, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
}

// Convenience wrappers
function site_log_info($msg, $meta=null){ site_log('INFO', $msg, $meta); }
function site_log_warn($msg, $meta=null){ site_log('WARN', $msg, $meta); }
function site_log_error($msg, $meta=null){ site_log('ERROR', $msg, $meta); }

?>
