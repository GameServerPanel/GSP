<?php
// === CONFIG (Sandbox) ===
$sandbox       = true; // flip to false for Live
$client_id     = 'AfvY_C2zA_hTHxHq7TIhtOeub4xBdySYrt_Hjj3d_WYQwjWI9NfOAVOTeResx2rgZ_nP5tOoxQSAHw8c';
$client_secret = 'EJ216np9cAj9n7KSddez3fLVxGe-zi4oKKKl1YGqPp88XIikr4Qzbxh0XW2as-V6LgdX-upjtQAg9dC0';

header('Content-Type: application/json');
$in = json_decode(file_get_contents('php://input'), true) ?: [];
$order_id = $in['order_id'] ?? null;
if (!$order_id) { http_response_code(400); echo json_encode(['error'=>'missing order_id']); exit; }

$api = $sandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

// 1) OAuth2
$ch = curl_init("$api/v1/oauth2/token");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
  CURLOPT_HTTPHEADER => ['Accept: application/json'],
  CURLOPT_USERPWD => $client_id . ':' . $client_secret,
]);
$tok  = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($http !== 200) { http_response_code(500); echo json_encode(['error'=>'oauth_fail']); exit; }
$access = json_decode($tok, true)['access_token'] ?? null;

// 2) Capture
$ch = curl_init("$api/v2/checkout/orders/$order_id/capture");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access
  ],
]);
$res  = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http !== 201 && $http !== 200) { http_response_code($http); echo $res; exit; }

$payload = json_decode($res, true);
$status  = $payload['status'] ?? 'UNKNOWN';
$txnId   = $payload['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

echo json_encode(['status'=>$status, 'txn_id'=>$txnId]);

