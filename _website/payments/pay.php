<?php
// Compatibility wrapper: redirect legacy /payments/pay.php to new create_order API
header('Location: /_website/api/create_order.php');
exit;
