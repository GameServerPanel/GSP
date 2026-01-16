<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2018 The OGP Development Team
 *
 * http://www.opengamepanel.org/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */
require_once('includes/lib_remote.php');
require_once('modules/config_games/server_config_parser.php');
require_once('modules/steam_workshop/lib/Workshop.php');
require_once('includes/form_table_class.php');

function exec_ogp_module()
{
	global $db, $view, $settings;
	echo '<h2>Steam Workshop</h2>';

	if (!isset($_GET['home_id-mod_id-ip-port']) || $_GET['home_id-mod_id-ip-port'] === '') {
		print_failure(get_lang('no_game_servers_assigned'));
		return;
	}

	list($homeId, $modId) = explode('-', $_GET['home_id-mod_id-ip-port']);

	$isAdmin = $db->isAdmin($_SESSION['user_id']);
	$homeCfg = $isAdmin ? $db->getGameHome($homeId) : $db->getUserGameHome($_SESSION['user_id'], $homeId);
	if (!$homeCfg) {
		print_failure(get_lang('game_home_not_found'));
		return;
	}

	$serverXml = read_server_config(SERVER_CONFIG_LOCATION . '/' . $homeCfg['home_cfg_file']);
	if ($serverXml === false) {
		print_failure(get_lang_f('failed_reading_xml_file', SERVER_CONFIG_LOCATION . '/' . $homeCfg['home_cfg_file']));
		return;
	}

	if (!isset($homeCfg['mods'][$modId]['mod_key'])) {
		print_failure(get_lang_f('mod_id_does_not_exists_in_home', $modId, $homeId));
		return;
	}

	$modKey = $homeCfg['mods'][$modId]['mod_key'];
	$modXml = xml_get_mod($serverXml, $modKey);
	if (!$modXml) {
		print_failure(get_lang_f('mod_key_not_found_from_xml', $modKey));
		return;
	}

	$configStore = new WorkshopConfigStore('modules/steam_workshop/data/configs.json');
	$stateStore = new WorkshopStateStore('modules/steam_workshop/data');
	$resolver = new WorkshopResolver();

	$config = $configStore->get($homeId);
	if ($config === null) {
		$config = workshop_build_default_config($homeCfg, $modXml, $settings);
		$configStore->put($homeId, $config);
	}

	$remote = new OGPRemoteLibrary($homeCfg['agent_ip'], $homeCfg['agent_port'], $homeCfg['encryption_key'], $homeCfg['timeout']);
	if ($remote->status_chk() !== 1) {
		print_failure(get_lang('remote_server_offline'));
		return;
	}

	$syncService = new WorkshopSyncService($remote, $homeCfg, $configStore, $stateStore, $resolver);
	$state = $stateStore->get($homeId);
	$items = $resolver->resolveItems($config);
	$details = $resolver->fetchItemDetails($items);

	$message = '';
	if (isset($_POST['action'])) {
		switch ($_POST['action']) {
			case 'add_item':
				if (isset($_POST['item_id']) && preg_match('/^[0-9]+$/', $_POST['item_id'])) {
					$config['workshop_item_ids'][] = $_POST['item_id'];
					$config['workshop_item_ids'] = array_values(array_unique(array_filter($config['workshop_item_ids'], 'strlen')));
					$configStore->put($homeId, $config);
					$items = $resolver->resolveItems($config);
					$details = $resolver->fetchItemDetails($items);
					$message = get_lang('mod_installation_started');
				} else {
					print_failure(get_lang('invalid_mod_id')); 
				}
				break;
			case 'add_collection':
				if (isset($_POST['collection_id']) && preg_match('/^[0-9]+$/', $_POST['collection_id'])) {
					$config['collection_ids'][] = $_POST['collection_id'];
					$config['collection_ids'] = array_values(array_unique(array_filter($config['collection_ids'], 'strlen')));
					$configStore->put($homeId, $config);
					$items = $resolver->resolveItems($config);
					$details = $resolver->fetchItemDetails($items);
					$message = get_lang('settings_updated');
				} else {
					print_failure(get_lang('invalid_mod_id'));
				}
				break;
			case 'remove_item':
				if (isset($_POST['item_id'])) {
					$config['workshop_item_ids'] = array_values(array_filter($config['workshop_item_ids'], function ($id) {
						return isset($_POST['item_id']) ? ($id !== $_POST['item_id']) : true;
					}));
					$configStore->put($homeId, $config);
					$items = $resolver->resolveItems($config);
					$details = $resolver->fetchItemDetails($items);
					$message = get_lang('settings_updated');
				}
				break;
			case 'sync_now':
				$result = $syncService->sync($homeId);
				if ($result->success) {
					print_success($result->message);
				} else {
					print_failure($result->message);
				}
				$state = $stateStore->get($homeId);
				break;
		}
	}

	if ($message !== '') {
		print_success($message);
	}

	echo '<div class="panel">';
	echo '<h3>' . get_lang('installed_mods') . '</h3>';
	if (empty($items)) {
		echo '<p>' . get_lang('no_mods_found') . '</p>';
	} else {
		echo '<table class="table table-bordered">';
		echo '<tr><th>ID</th><th>' . get_lang('name') . '</th><th>' . get_lang('status') . '</th><th></th></tr>';
		foreach ($items as $id) {
			$name = isset($details[$id]['title']) ? htmlentities($details[$id]['title']) : $id;
			$status = isset($state['items'][$id]) ? get_lang('installed') : get_lang('pending');
			echo '<tr>';
			echo '<td>' . $id . '</td>';
			echo '<td>' . $name . '</td>';
			echo '<td>' . $status . '</td>';
			echo '<td>';
			echo '<form method="post" style="display:inline">';
			echo '<input type="hidden" name="item_id" value="' . $id . '">';
			echo '<button class="btn btn-xs btn-danger" name="action" value="remove_item">' . get_lang('remove') . '</button>';
			echo '</form>';
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}
	echo '</div>';

	echo '<div class="panel">';
	echo '<h3>' . get_lang('add_mod') . '</h3>';
	echo '<form method="post">';
	echo '<input type="text" name="item_id" placeholder="' . get_lang('workshop_id') . '"> ';
	echo '<button class="btn btn-primary" name="action" value="add_item">' . get_lang('add') . '</button>';
	echo '</form>';
	echo '<form method="post" style="margin-top:10px">';
	echo '<input type="text" name="collection_id" placeholder="' . get_lang('collection_id') . '"> ';
	echo '<button class="btn btn-primary" name="action" value="add_collection">' . get_lang('add') . '</button>';
	echo '</form>';
	echo '</div>';

	echo '<div class="panel">';
	echo '<h3>' . get_lang('actions') . '</h3>';
	echo '<form method="post">';
	echo '<button class="btn btn-success" name="action" value="sync_now">' . get_lang('refresh_steam_workshop_status') . '</button>';
	echo '</form>';
	echo '</div>';
}
?>
