<?php
// Simple script to POST an existing JSON file to the local webhook endpoint
$webhookUrl = 'http://localhost/GSP/_website/webhook.php';
$sample = __DIR__ . '/../data/SIMULATED-WEBHOOK-20251022-101500.json';
if (!file_exists($sample)) {
    echo "Sample file not found: $sample\n";
    exit(1);
}
$raw = file_get_contents($sample);
$ch = curl_init($webhookUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $raw,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'PayPal-Transmission-Id: SIM-TEST',
        'PayPal-Transmission-Time: ' . gmdate('c'),
        'PayPal-Cert-Url: https://example.com/cert.pem',
        'PayPal-Auth-Algo: SHA256withRSA',
        'PayPal-Transmission-Sig: FAKE',
    ],
]);
$res = curl_exec($ch);
$err = curl_error($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP: $http\n";
if ($err) echo "CURL_ERROR: $err\n";
echo "RESPONSE:\n" . $res . "\n";

// show if a new file was written
$dataDir = realpath(__DIR__ . '/../data');
$files = glob($dataDir . '/*.json');
echo "Files in data/ after run: \n";
foreach ((array)$files as $f) echo basename($f) . "\n";

?>
