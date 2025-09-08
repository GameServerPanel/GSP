<?php
// === CONFIG (Sandbox) ===
$sandbox       = true; // flip to false for Live
$client_id     = 'AfvY_C2zA_hTHxHq7TIhtOeub4xBdySYrt_Hjj3d_WYQwjWI9NfOAVOTeResx2rgZ_nP5tOoxQSAHw8c';
$client_secret = 'EJ216np9cAj9n7KSddez3fLVxGe-zi4oKKKl1YGqPp88XIikr4Qzbxh0XW2as-V6LgdX-upjtQAg9dC0';

header('Content-Type: application/json');

$in = json_decode(file_get_contents('php://input'), true) ?: [];

// Incoming fields from your cart/client
$amount_in    = $in['amount'] ?? '0.00';          // overall intended amount (string)
$currency     = $in['currency'] ?? 'USD';
$invoice_id   = $in['invoice_id'] ?? null;        // overall invoice id (string)
$custom_id    = $in['custom_id'] ?? null;         // your short reference (<=127 chars)
$description  = $in['description'] ?? 'Order';
$return_url   = $in['return_url'] ?? null;
$cancel_url   = $in['cancel_url'] ?? null;

// Optional payloads:
$items        = (isset($in['items']) && is_array($in['items'])) ? $in['items'] : null;           // PayPal items
$line_invoices= (isset($in['line_invoices']) && is_array($in['line_invoices'])) ? $in['line_invoices'] : null; // your raw detail

// --- Server-side reconciliation for items ---
// If items are provided, ensure the order 'amount' equals the sum of item unit_amount * quantity.
// (Simplest policy: set the order amount to the exact sum of items.)
$amount_value = number_format((float)$amount_in, 2, '.', '');

// Compute sum of items if present
if ($items) {
  $sum = 0.00;
  foreach ($items as $it) {
    $qty  = isset($it['quantity']) ? (int)$it['quantity'] : 1;
    $val  = isset($it['unit_amount']['value']) ? (float)$it['unit_amount']['value'] : 0.00;
    $sum += $qty * $val;
  }
  // Use the item sum as the authoritative amount for PayPal (avoids mismatch errors)
  $amount_value = number_format($sum, 2, '.', '');
}

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
if (!$access) { http_response_code(500); echo json_encode(['error'=>'oauth_no_token']); exit; }

// 2) Build purchase unit
$purchaseUnit = [
  'amount' => [
    'currency_code' => $currency,
    'value' => $amount_value,
  ],
  'description' => $description,
  // Critical for webhook reconciliation:
  'invoice_id' => $invoice_id,
  'custom_id'  => $custom_id
];

// If items provided, include them and add a breakdown with item_total to match the overall amount
if ($items) {
  $purchaseUnit['items'] = $items;
  $purchaseUnit['amount']['breakdown'] = [
    'item_total' => [
      'currency_code' => $currency,
      'value' => $amount_value
    ]
  ];
}

// (Optional) Persist your raw line_invoices server-side here if you wish.
// For example, write to a DB keyed by $invoice_id so you can join later.

// 3) Create order (intent = CAPTURE)
$body = [
  'intent' => 'CAPTURE',
  'purchase_units' => [ $purchaseUnit ],
  // Guides PayPal where to send the buyer if the flow becomes a full-page redirect
  'application_context' => [
    'return_url'  => $return_url,
    'cancel_url'  => $cancel_url,
    'user_action' => 'PAY_NOW'
  ]
];

$ch = curl_init("$api/v2/checkout/orders");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => json_encode($body),
  CURLOPT_HTTPHEADER => [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access
  ],
]);
$res  = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http !== 201) { http_response_code($http); echo $res; exit; }
echo $res;

