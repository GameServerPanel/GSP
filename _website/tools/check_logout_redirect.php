<?php
// Simple header fetcher for logout.php
$url = 'http://localhost/GSP/_website/logout.php';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
$res = curl_exec($ch);
if ($res === false) {
    echo "Curl error: " . curl_error($ch) . PHP_EOL;
    exit(1);
}
$info = curl_getinfo($ch);
echo "Request: $url\n";
echo "HTTP: " . ($info['http_code'] ?? '0') . "\n";
echo "Headers:\n";
$header_text = substr($res, 0, $info['header_size'] ?? 0);
echo $header_text . PHP_EOL;
curl_close($ch);
?>