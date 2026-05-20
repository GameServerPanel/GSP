<?php
/**
 * Session bridge to keep panel + storefront logins in sync.
 * Always call this before rendering billing pages.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_name('opengamepanel_web');
    session_start();
}

// If the panel session is populated, mirror into website-specific keys.
if (!empty($_SESSION['user_id']) && empty($_SESSION['website_user_id'])) {
    $_SESSION['website_user_id'] = (int)$_SESSION['user_id'];
    if (!empty($_SESSION['users_login'])) {
        $_SESSION['website_username'] = $_SESSION['users_login'];
    }
    if (!empty($_SESSION['users_group'])) {
        $_SESSION['website_user_role'] = $_SESSION['users_group'];
    }
}

// If the website session is populated but the panel keys are missing, mirror back.
if (!empty($_SESSION['website_user_id']) && empty($_SESSION['user_id'])) {
    $_SESSION['user_id'] = (int)$_SESSION['website_user_id'];
    if (!empty($_SESSION['website_username'])) {
        $_SESSION['users_login'] = $_SESSION['website_username'];
    }
    if (!empty($_SESSION['website_user_role'])) {
        $_SESSION['users_group'] = $_SESSION['website_user_role'];
    }
}
