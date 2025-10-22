<?php
// Compatibility wrapper for old /paypal/return.php — route to unified return page
header('Location: /_website/return.php' . (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit;
