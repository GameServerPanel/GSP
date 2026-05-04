<?php
declare(strict_types=1);

require_once __DIR__ . '/controllers/WorkshopProfileController.php';

function exec_ogp_module(): void
{
	global $db;
	echo '<h2>' . get_lang('steam_workshop') . '</h2>';

	$controller = new WorkshopProfileController($db);
	$controller->handle();
}
