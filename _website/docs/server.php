<?php
// serve.php — secure PDF streaming (inline by default, ?download=1 to force download)

// Map short keys to absolute paths (whitelist!)
$docs = [
  'sales'    => __DIR__ . '/assets/docs/OGP_Sales_Onboarding_Playbook.pdf',
  'security' => __DIR__ . '/assets/docs/OGP_Security_Compliance_Playbook.pdf',
  'investor' => __DIR__ . '/assets/docs/OGP_Investor_One_Pager.pdf',
];

$key = $_GET['doc'] ?? '';
if (!isset($docs[$key])) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Document not found.';
    exit;
}

$file = $docs[$key];
if (!is_readable($file)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'File not available.';
    exit;
}

$download = isset($_GET['download']) && $_GET['download'] === '1';
$basename = basename($file);
$filesize = @filesize($file);

header('X-Content-Type-Options: nosniff');
header('Content-Type: application/pdf');
header('Content-Disposition: ' . ($download ? 'attachment' : 'inline') . '; filename="' . $basename . '"');
if ($filesize !== false) {
    header('Content-Length: ' . $filesize);
}
header('Cache-Control: public, max-age=86400'); // cache for 1 day
header('Accept-Ranges: none'); // simple stream

$fp = @fopen($file, 'rb');
if ($fp === false) {
    http_response_code(500);
    echo 'Unable to open file.';
    exit;
}

fpassthru($fp);
fclose($fp);

