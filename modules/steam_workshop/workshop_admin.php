<?php
declare(strict_types=1);

require_once __DIR__ . '/controllers/AdminWorkshopController.php';
require_once __DIR__ . '/controllers/WorkshopProfileController.php';

function exec_ogp_module(): void
{
	global $db;
	echo '<h2>' . get_lang('steam_workshop') . '</h2>';

	// Route to the DB-driven profile manager when requested
	$swAction = $_GET['sw_action'] ?? '';
	$profileActions = ['profiles', 'profile_form'];
	$postAction = $_POST['sw_action'] ?? '';
	$profilePostActions = ['profile_save', 'profile_delete'];

	if (in_array($swAction, $profileActions, true) || in_array($postAction, $profilePostActions, true)) {
		$controller = new WorkshopProfileController($db);
		$controller->handle();
		return;
	}

	// Default: legacy XML adapter manager + tab link to profiles
	echo '<p><a class="btn secondary" href="?m=steam_workshop&p=workshop_admin&sw_action=profiles">'
		. (function_exists('get_lang') ? get_lang('nav_workshop_profiles') : 'Workshop Profiles')
		. '</a></p>';

	$controller = new AdminWorkshopController($db);
	$controller->handle();
}
