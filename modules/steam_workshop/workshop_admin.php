<?php
declare(strict_types=1);

require_once __DIR__ . '/controllers/AdminWorkshopController.php';

function exec_ogp_module(): void
{
	global $db;
	echo '<h2>' . get_lang('steam_workshop') . '</h2>';
	$controller = new AdminWorkshopController($db);
	$controller->handle();
}
?>