<?php
// ========== CONFIG ==========
$config = [
  'sandbox'       => true, // flip to false for Live
  'client_id'     => 'AfvY_C2zA_hTHxHq7TIhtOeub4xBdySYrt_Hjj3d_WYQwjWI9NfOAVOTeResx2rgZ_nP5tOoxQSAHw8c',
  'client_secret' => 'EJ216np9cAj9n7KSddez3fLVxGe-zi4oKKKl1YGqPp88XIikr4Qzbxh0XW2as-V6LgdX-upjtQAg9dC0',
  'webhook_id'    => '6N620673281740730',
  'data_dir'      => __DIR__ . '/data',
  'log_file'      => __DIR__ . '/webhook.log',
];

function log_line($m){global $config; @file_put_contents($config['log_file'],'['.date('c')."] $m\n",FILE_APPEND);}
function api_base(){global $config; return $config['sandbox'] ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';}

http_response_code(200);
@mkdir($config['data_dir'], 0775, true);

$raw = file_get_contents('php://input');
$headers = array_change_key_case(getallheaders() ?: [], CASE_UPPER);
log_line("HIT ip=".($_SERVER['REMOTE_ADDR']??'') ." bytes=".strlen($raw));
if (!$raw) { log_line("NO_BODY"); exit; }

// 1) OAuth2
$ch = curl_init(api_base().'/v1/oauth2/token');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER=>true,
  CURLOPT_POST=>true,
  CURLOPT_POSTFIELDS=>'grant_type=client_credentials',
  CURLOPT_HTTPHEADER=>['Accept: application/json'],
  CURLOPT_USERPWD=>$config['client_id'].':'.$config['client_secret'],
]);
$tokenResp = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($http!==200){ log_line("OAUTH_FAIL http=$http resp=$tokenResp"); exit; }
$access_token = json_decode($tokenResp, true)['access_token'] ?? null;
if (!$access_token){ log_line("OAUTH_NO_TOKEN"); exit; }

// 2) Verify webhook signature
$verifyPayload = [
  'transmission_id'    => $headers['PAYPAL-TRANSMISSION-ID'] ?? '',
  'transmission_time'  => $headers['PAYPAL-TRANSMISSION-TIME'] ?? '',
  'cert_url'           => $headers['PAYPAL-CERT-URL'] ?? '',
  'auth_algo'          => $headers['PAYPAL-AUTH-ALGO'] ?? '',
  'transmission_sig'   => $headers['PAYPAL-TRANSMISSION-SIG'] ?? '',
  'webhook_id'         => $config['webhook_id'],
  'webhook_event'      => json_decode($raw, true),
];
$ch = curl_init(api_base().'/v1/notifications/verify-webhook-signature');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER=>true,
  CURLOPT_POST=>true,
  CURLOPT_POSTFIELDS=>json_encode($verifyPayload),
  CURLOPT_HTTPHEADER=>[
    'Content-Type: application/json',
    'Authorization: Bearer '.$access_token
  ],
]);
$verifyResp = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
$verifyJson = json_decode($verifyResp, true);
if ($http!==200 || ($verifyJson['verification_status'] ?? '') !== 'SUCCESS'){
  log_line("VERIFY_FAIL http=$http status=".($verifyJson['verification_status']??'NONE'));
  exit;
}
log_line("VERIFY_OK");

// 3) Parse and persist (now with items)
$evt = json_decode($raw, true);
$type = $evt['event_type'] ?? '';
$res  = $evt['resource'] ?? [];

// Extract common fields
$invoice = $res['invoice_id'] ?? ($res['invoice_number'] ?? null);
$custom  = $res['custom_id'] ?? ($res['custom'] ?? null);

// Amounts/payer
$amount   = $res['amount']['value'] ?? ($res['amount']['total'] ?? null);
$currency = $res['amount']['currency_code'] ?? ($res['amount']['currency'] ?? null);
$payer    = $res['payer']['email_address'] ?? ($res['payer']['payer_info']['email'] ?? null);

// Try to capture line items if present directly in this event:
// (Some events—like ORDER.*—include purchase_units; CAPTURE events often don't.)
$items = [];
if (isset($res['purchase_units'][0]['items']) && is_array($res['purchase_units'][0]['items'])) {
  $items = $res['purchase_units'][0]['items'];
}

// If capture event, try to fetch the parent ORDER to get items
if (!$items && $type === 'PAYMENT.CAPTURE.COMPLETED') {
  $orderId =
    $res['supplementary_data']['related_ids']['order_id']  // preferred
    ?? null;

  if (!$orderId && isset($res['links']) && is_array($res['links'])) {
    // Fallback: look for a link to the parent order
    foreach ($res['links'] as $lnk) {
      if (!empty($lnk['href']) && !empty($lnk['rel']) && stripos($lnk['href'], '/v2/checkout/orders/') !== false) {
        $orderId = basename(parse_url($lnk['href'], PHP_URL_PATH));
        break;
      }
    }
  }

  if ($orderId) {
    $ch = curl_init(api_base()."/v2/checkout/orders/".urlencode($orderId));
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => [
        'Authorization: Bearer '.$access_token,
        'Content-Type: application/json'
      ],
    ]);
    $orderJson = curl_exec($ch);
    $httpOrder = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpOrder === 200) {
      $order = json_decode($orderJson, true);
      if (isset($order['purchase_units'][0]['items']) && is_array($order['purchase_units'][0]['items'])) {
        $items = $order['purchase_units'][0]['items'];
      }
      // If the order has invoice/custom (sometimes more reliable), prefer those:
      if (!$invoice) { $invoice = $order['purchase_units'][0]['invoice_id'] ?? $invoice; }
      if (!$custom)  { $custom  = $order['purchase_units'][0]['custom_id']  ?? $custom; }
    } else {
      log_line("ORDER_FETCH_FAIL id=$orderId http=$httpOrder");
    }
  }
}

$status = 'IGNORED';

// We persist on payment completed events
if (in_array($type, ['PAYMENT.CAPTURE.COMPLETED','PAYMENT.SALE.COMPLETED'], true)) {
  $record = [
    'event_type'  => $type,
    'status'      => 'PAID',
    'amount'      => $amount,
    'currency'    => $currency,
    'payer'       => $payer,
    'invoice'     => $invoice,
    'custom'      => $custom,
    'resource_id' => $res['id'] ?? null,
    'items'       => $items, // <— Persist line items for your return.php/UI
    'ts'          => date('c'),
  ];
  $name = $invoice ?: 'NO-INVOICE';
  @file_put_contents($config['data_dir']."/$name.json", json_encode($record, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
  $status = 'WROTE_FILE';
}

log_line("EVENT $type invoice=".($invoice ?: 'none')." items_count=".count($items)." status=$status");

