<?php
declare(strict_types=1);
/*
 * OGP - Open Game Panel
 * Steam Workshop module entrypoint.
 */
require_once __DIR__ . '/controllers/SteamWorkshopController.php';

function exec_ogp_module(): void
{
    global $db;

    echo '<h2>' . get_lang('steam_workshop') . '</h2>';

    $controller = new SteamWorkshopController($db);
    $controller->handle();
}
