<?php
// Fetch admin.php and then fetch the invoices.php link to observe redirects and Location headers
$adminUrl = 'http://localhost/GSP/_website/admin.php';
$ch = curl_init($adminUrl);
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_HEADER=>true, CURLOPT_FOLLOWLOCATION=>false]);
$res = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($res, 0, $header_size);
$body = substr($res, $header_size);
curl_close($ch);

echo "admin.php HTTP: $http\n";
echo "Headers:\n$headers\n";
// Find invoices link
if (preg_match('#href="([^"]*invoices\.php)"#i', $body, $m)) {
    $link = $m[1];
    echo "Found invoices link: $link\n";
    // Resolve relative link
    $linkUrl = (strpos($link, 'http')===0) ? $link : 'http://localhost/GSP/_website/' . ltrim($link, './');
    echo "Resolved invoices URL: $linkUrl\n";

    // Fetch invoices.php and show headers
    $ch = curl_init($linkUrl);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_HEADER=>true, CURLOPT_FOLLOWLOCATION=>false]);
    $res2 = curl_exec($ch);
    $h2 = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $http2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headers2 = substr($res2, 0, $h2);
    $body2 = substr($res2, $h2);
    curl_close($ch);

    echo "invoices.php HTTP: $http2\n";
    echo "invoices headers:\n$headers2\n";
    echo "invoices body snippet:\n" . substr($body2,0,400) . "\n";
} else {
    echo "No invoices link found in admin.php body.\n";
}

?>
