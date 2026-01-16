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
require_once('includes/form_table_class.php');
require_once('modules/config_games/server_config_parser.php');
require_once('modules/steam_workshop/functions.php');
require_once('modules/steam_workshop/lib/Workshop.php');

function exec_ogp_module()
{
	global $db;
	echo '<h2>Steam Workshop – Admin</h2>';

	$homes = $db->getGameHomes();
	$eligibleHomes = [];
	foreach ($homes as $home) {
		$serverXml = read_server_config(SERVER_CONFIG_LOCATION . '/' . $home['home_cfg_file']);
		if ($serverXml !== false && isset($serverXml->installer) && strtolower((string)$serverXml->installer) === 'steamcmd') {
			$eligibleHomes[$home['home_id']] = $home;
		}
	}

	if (empty($eligibleHomes)) {
		print_failure(get_lang('no_game_servers_assigned'));
		return;
	}

	$selectedHomeId = isset($_REQUEST['home_id']) ? $_REQUEST['home_id'] : array_key_first($eligibleHomes);
	if (!isset($eligibleHomes[$selectedHomeId])) {
		$selectedHomeId = array_key_first($eligibleHomes);
	}

	$homeCfg = $db->getGameHome($selectedHomeId);
	$serverXml = read_server_config(SERVER_CONFIG_LOCATION . '/' . $homeCfg['home_cfg_file']);
	$mods = isset($homeCfg['mods']) ? $homeCfg['mods'] : [];
	$firstMod = reset($mods);
	$modKey = isset($firstMod['mod_key']) ? $firstMod['mod_key'] : null;
	$modXml = $modKey ? xml_get_mod($serverXml, $modKey) : null;

	$configStore = new WorkshopConfigStore('modules/steam_workshop/data/configs.json');
	$config = $configStore->get($selectedHomeId);
	if ($config === null) {
		$config = workshop_build_default_config($homeCfg, $modXml, []);
		$configStore->put($selectedHomeId, $config);
	}

	if (isset($_POST['save_config'])) {
		$config['workshop_app_id'] = trim($_POST['workshop_app_id']);
		$config['download_method'] = $_POST['download_method'];
		$config['deploy_mode'] = $_POST['deploy_mode'];
		$config['deploy_destination'] = clean_path($_POST['deploy_destination']);
		$config['staging_path'] = clean_path($_POST['staging_path']);
		$config['regex'] = $_POST['regex'];
		$config['mods_backreference_index'] = (int)$_POST['mods_backreference_index'];
		$config['variable'] = $_POST['variable'];
		$config['place_after'] = $_POST['place_after'];
		$config['mod_string'] = $_POST['mod_string'];
		$config['string_separator'] = $_POST['string_separator'];
		$config['filepath'] = clean_path($_POST['filepath']);
		$config['anonymous_login'] = isset($_POST['anonymous_login']) && $_POST['anonymous_login'] === '1';
		$config['steam_username'] = $_POST['steam_username'];
		$config['steam_password'] = $_POST['steam_password'];
		$config['check_on_start'] = isset($_POST['check_on_start']) && $_POST['check_on_start'] === '1';
		$config['periodic_check_minutes'] = $_POST['periodic_check_minutes'] === '' ? null : (int)$_POST['periodic_check_minutes'];
		$config['apply_updates'] = $_POST['apply_updates'];
		$configStore->put($selectedHomeId, $config);
		print_success(get_lang('settings_updated'));
	}

	$downloadMethods = array('steamcmd' => 'steamcmd');
	$deployModes = array('copy' => 'copy', 'symlink' => 'symlink');
	$applyModes = array('on_start_only' => 'on_start_only', 'download_now_apply_on_next_restart' => 'download_now_apply_on_next_restart');

	$homeOptions = [];
	foreach ($eligibleHomes as $id => $home) {
		$homeOptions[$id] = $home['home_name'];
	}

	$ft = new FormTable();
	$ft->start_form('?m=steam_workshop&p=workshop_admin', 'post', 'autocomplete="off"');
	$ft->start_table();
	$ft->add_custom_field('home_id', create_drop_box_from_array_onchange($homeOptions, 'home_id', $selectedHomeId));
	$ft->add_field('string', 'workshop_app_id', $config['workshop_app_id']);
	$ft->add_custom_field('download_method', create_drop_box_from_array($downloadMethods, 'download_method', $config['download_method']));
	$ft->add_custom_field('deploy_mode', create_drop_box_from_array($deployModes, 'deploy_mode', $config['deploy_mode']));
	$ft->add_field('string', 'deploy_destination', $config['deploy_destination']);
	$ft->add_field('string', 'staging_path', $config['staging_path']);
	$ft->add_field('string', 'regex', $config['regex']);
	$ft->add_field('string', 'mods_backreference_index', $config['mods_backreference_index']);
	$ft->add_field('string', 'variable', $config['variable']);
	$ft->add_field('string', 'place_after', $config['place_after']);
	$ft->add_field('string', 'mod_string', $config['mod_string']);
	$ft->add_field('string', 'string_separator', $config['string_separator']);
	$ft->add_field('string', 'filepath', $config['filepath']);
	$ft->add_field('on_off', 'anonymous_login', $config['anonymous_login'] ? '1' : '0');
	$ft->add_field('string', 'steam_username', $config['steam_username']);
	$ft->add_field('password', 'steam_password', $config['steam_password']);
	$ft->add_field('on_off', 'check_on_start', $config['check_on_start'] ? '1' : '0');
	$ft->add_field('string', 'periodic_check_minutes', $config['periodic_check_minutes']);
	$ft->add_custom_field('apply_updates', create_drop_box_from_array($applyModes, 'apply_updates', $config['apply_updates']));
	$ft->end_table();
	$ft->add_button('submit', 'save_config', get_lang('save_config'));
	$ft->end_form();
}
?>