<?php
require_once(__DIR__ . '/../includes/config.inc.php');
$sandbox       = true; // flip to false for Live
$client_id     = 'AfvY_C2zA_hTHxHq7TIhtOeub4xBdySYrt_Hjj3d_WYQwjWI9NfOAVOTeResx2rgZ_nP5tOoxQSAHw8c';
$client_secret = 'EJ216np9cAj9n7KSddez3fLVxGe-zi4oKKKl1YGqPp88XIikr4Qzbxh0XW2as-V6LgdX-upjtQAg9dC0';

header('Content-Type: application/json');
$in = json_decode(file_get_contents('php://input'), true) ?: [];
$order_id = $in['order_id'] ?? null;
if (!$order_id) { http_response_code(400); echo json_encode(['error'=>'missing order_id']); exit; }

$api = $sandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

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

$ch = curl_init("$api/v2/checkout/orders/$order_id/capture");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => [ 'Content-Type: application/json', 'Authorization: Bearer ' . $access ],
]);
$res  = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http !== 201 && $http !== 200) { http_response_code($http); echo $res; exit; }

// Parse the capture response
$captureData = json_decode($res, true);
$captureStatus = $captureData['status'] ?? '';

// If capture was successful, immediately update the order status to 'paid'
if ($captureStatus === 'COMPLETED') {
    // Extract custom_id which contains the order_id
    $customId = $captureData['purchase_units'][0]['payments']['captures'][0]['custom_id'] ?? null;
    $txnId = $captureData['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;
    
    if ($customId && is_numeric($customId)) {
        // Connect to DB and update order status
        $db = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
        if ($db) {
            $orderId = intval($customId);
            
            // Calculate finish_date based on qty and invoice_duration
            $qtyRes = mysqli_query($db, "SELECT qty, invoice_duration FROM ogp_billing_orders WHERE order_id = $orderId LIMIT 1");
            $finish_date = null;
            if ($qtyRes && $row = mysqli_fetch_assoc($qtyRes)) {
                $qty = intval($row['qty'] ?? 1);
                $duration = strtolower(trim($row['invoice_duration'] ?? 'month'));
                $months = (strpos($duration, 'year') !== false) ? ($qty * 12) : $qty;
                if ($months > 0) {
                    $dt = new DateTime('now');
                    $dt->modify('+' . $months . ' months');
                    $finish_date = $dt->format('Y-m-d H:i:s');
                }
            }
            
            // Update order status to 'paid'
            $sql = "UPDATE ogp_billing_orders SET status = 'paid', payment_txid = '" . mysqli_real_escape_string($db, $txnId) . "', paid_ts = NOW()";
            if ($finish_date) {
                $sql .= ", finish_date = '" . mysqli_real_escape_string($db, $finish_date) . "'";
            }
            $sql .= " WHERE order_id = $orderId AND status = 'in-cart' LIMIT 1";
            mysqli_query($db, $sql);
            mysqli_close($db);
        }
    }
}

// Return the full PayPal response for proper processing
echo $res;

?>
