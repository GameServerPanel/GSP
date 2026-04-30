<?php
declare(strict_types=1);
/*
 * OGP / GSP – Steam Workshop module entrypoint.
 * Routes to either the new DB-driven WorkshopModController or the
 * legacy SteamWorkshopController, depending on the action requested.
 */
require_once __DIR__ . '/controllers/SteamWorkshopController.php';
require_once __DIR__ . '/controllers/WorkshopModController.php';

function exec_ogp_module(): void
{
    global $db;

    $action = $_GET['action'] ?? '';
    $postAction = $_POST['ws_action'] ?? '';

    // JSON search endpoint – no heading
    if ($action === 'search') {
        $controller = new SteamWorkshopController($db);
        $controller->handle();
        return;
    }

    echo '<h2>' . get_lang('steam_workshop') . '</h2>';

    // New DB-driven actions
    $newActions     = ['index', 'mods'];
    $newPostActions = ['install', 'remove', 'toggle', 'load_order', 'sync'];

    if (in_array($action, $newActions, true) || in_array($postAction, $newPostActions, true)) {
        $controller = new WorkshopModController($db);
        $controller->handle();
        return;
    }

    // Legacy controller for old Workshop page actions
    $controller = new SteamWorkshopController($db);
    $controller->handle();
}

