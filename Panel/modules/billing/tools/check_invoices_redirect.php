<?php
$target = 'http://localhost/GSP/_website/invoices.php';
$ch = curl_init($target);
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_HEADER=>true, CURLOPT_FOLLOWLOCATION=>false]);
$res = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($res, 0, $header_size);
$body = substr($res, $header_size);
curl_close($ch);

echo "Request: $target\n";
echo "HTTP: $http\n";
echo "Headers:\n$headers\n";
echo "Body snippet:\n" . substr($body,0,400) . "\n";
?>
