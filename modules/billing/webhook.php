<?php
require_once(__DIR__ . '/includes/config_loader.php');
if (is_file(__DIR__ . '/includes/log.php')) require_once(__DIR__ . '/includes/log.php');

$config = [
  'sandbox'       => $paypal_sandbox ?? true,
  'client_id'     => $paypal_client_id ?? '',
  'client_secret' => $paypal_client_secret ?? '',
  'webhook_id'    => $paypal_webhook_id ?? '',
  'data_dir'      => rtrim(
    (defined('SITE_DATA_DIR') ? SITE_DATA_DIR : '') ?: ($SITE_DATA_DIR ?? ''),
    DIRECTORY_SEPARATOR
  ),
  'log_file'      => __DIR__ . '/data/webhook.log',
];

if (!$config['data_dir']) {
  $config['data_dir'] = realpath(__DIR__ . '/') . DIRECTORY_SEPARATOR . 'data';
}

function log_line($m){global $config; @file_put_contents($config['log_file'],'['.date('c')."] $m\n",FILE_APPEND);} 
function api_base(){global $config; return $config['sandbox'] ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';}

http_response_code(200);
@mkdir($config['data_dir'], 0775, true);

$raw = file_get_contents('php://input');
$headers = array_change_key_case(getallheaders() ?: [], CASE_UPPER);
if (function_exists('site_log_info')) site_log_info('webhook_hit', ['ip'=>($_SERVER['REMOTE_ADDR']??''),'bytes'=>strlen($raw)]);
else log_line("HIT ip=".($_SERVER['REMOTE_ADDR']??'') ." bytes=".strlen($raw));
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
$o = ['http'=>$http,'resp'=>substr($tokenResp,0,400)];
if ($http!==200){ if (function_exists('site_log_warn')) site_log_warn('oauth_fail',$o); else log_line("OAUTH_FAIL http=$http resp=$tokenResp"); exit; }
$access_token = json_decode($tokenResp, true)['access_token'] ?? null;
if (!$access_token){ if (function_exists('site_log_warn')) site_log_warn('oauth_no_token', []); else log_line("OAUTH_NO_TOKEN"); exit; }

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
  if (function_exists('site_log_warn')) site_log_warn('verify_fail', ['http'=>$http,'status'=>($verifyJson['verification_status']??'NONE')]);
  else log_line("VERIFY_FAIL http=$http status=".($verifyJson['verification_status']??'NONE'));
  exit;
}
if (function_exists('site_log_info')) site_log_info('verify_ok', ['http'=>$http]);
else log_line("VERIFY_OK");

// 3) Parse and persist
$evt = json_decode($raw, true);
$type = $evt['event_type'] ?? '';
$res  = $evt['resource'] ?? [];

$invoice = $res['invoice_id'] ?? ($res['invoice_number'] ?? null);
$custom  = $res['custom_id'] ?? ($res['custom'] ?? null);

$amount   = $res['amount']['value'] ?? ($res['amount']['total'] ?? null);
$currency = $res['amount']['currency_code'] ?? ($res['amount']['currency'] ?? null);
$payer    = $res['payer']['email_address'] ?? ($res['payer']['payer_info']['email'] ?? null);

$items = [];
if (isset($res['purchase_units'][0]['items']) && is_array($res['purchase_units'][0]['items'])) {
  $items = $res['purchase_units'][0]['items'];
}

if (!$items && $type === 'PAYMENT.CAPTURE.COMPLETED') {
  $orderId = $res['supplementary_data']['related_ids']['order_id'] ?? null;
  if (!$orderId && isset($res['links']) && is_array($res['links'])) {
    foreach ((array)$res['links'] as $lnk) {
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
      CURLOPT_HTTPHEADER => [ 'Authorization: Bearer '.$access_token, 'Content-Type: application/json' ],
    ]);
    $orderJson = curl_exec($ch);
    $httpOrder = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
  if ($httpOrder === 200) {
      $order = json_decode($orderJson, true);
      if (isset($order['purchase_units'][0]['items']) && is_array($order['purchase_units'][0]['items'])) {
        $items = $order['purchase_units'][0]['items'];
      }
      if (!$invoice) { $invoice = $order['purchase_units'][0]['invoice_id'] ?? $invoice; }
      if (!$custom)  { $custom  = $order['purchase_units'][0]['custom_id']  ?? $custom; }
    } else {
      if (function_exists('site_log_warn')) site_log_warn('order_fetch_fail', ['orderId'=>$orderId,'http'=>$httpOrder]);
      else log_line("ORDER_FETCH_FAIL id=$orderId http=$httpOrder");
    }
  }
}

$status = 'IGNORED';
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
    'items'       => $items,
    'ts'          => date('c'),
  ];
  $name = $invoice ?: 'NO-INVOICE-'.bin2hex(random_bytes(4));
  @file_put_contents($config['data_dir']."/".$name.".json", json_encode($record, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
  $status = 'WROTE_FILE';

  // Attempt to mark order paid in DB
  require_once(__DIR__ . '/includes/payment_processor.php');
  try { 
      process_payment_record($record); 
  } catch (Exception $e) { 
      if (function_exists('site_log_error')) site_log_error('process_payment_fail',['err'=>$e->getMessage()]); 
      else log_line('PROC_FAIL '.$e->getMessage()); 
  }
}

if (function_exists('site_log_info')) site_log_info('webhook_event',['type'=>$type,'invoice'=>($invoice ?: 'none'),'items_count'=>count((array)$items),'status'=>$status]);
else log_line("EVENT $type invoice=".($invoice ?: 'none')." items_count=".count((array)$items)." status=$status");

?>
