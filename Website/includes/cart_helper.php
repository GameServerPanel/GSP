<?php
// Helper to read cart items stored in session and return count
// Non-invasive: reads $_SESSION['cart'] if present and returns total quantity or items count

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

function get_cart_count() {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        return 0;
    }
    $count = 0;
    foreach ((array)$_SESSION['cart'] as $item) {
        if (is_array($item) && isset($item['quantity'])) {
            $count += (int) $item['quantity'];
        } else {
            $count += 1;
        }
    }
    return $count;
}
