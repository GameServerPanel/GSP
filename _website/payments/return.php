<?php
// Compatibility wrapper for /payments/return.php
header('Location: /_website/return.php' . (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit;
