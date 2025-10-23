<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name("gameservers_website");
    session_start();
}

// Debugging mode: do not enforce login redirects. Pages can load without authentication.
// If you later want to re-enable, restore the original redirect behavior.
// (This file intentionally left as a no-op during debugging.)
return;
?>
