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

function exec_ogp_module()
{
	$selectedProtocol = isset($_POST['protocol']) ? $_POST['protocol'] : '';
	$queryChoices = array();
	$queryLabel = '';

	if ($selectedProtocol === 'lgsl') {
		require_once 'protocol/lgsl/lgsl_protocol.php';
		$lgsl_type_list = lgsl_type_list();
		asort($lgsl_type_list);
		foreach ((array)$lgsl_type_list as $type => $description) {
			$queryChoices[$type] = $description;
		}
		$queryLabel = 'LGSL Query Name';
	} elseif ($selectedProtocol === 'gameq') {
		require_once 'protocol/GameQ/Autoloader.php';
		$protocols_path = "protocol/GameQ/Protocols/";
		$dir = dir($protocols_path);
		$protocols = array();
		while (false !== ($entry = $dir->read())) {
			if (!is_file($protocols_path . $entry)) {
				continue;
			}
			$class_name = 'GameQ\Protocols\\' . ucfirst(pathinfo($entry, PATHINFO_FILENAME));
			$reflection = new ReflectionClass($class_name);
			if (!$reflection->IsInstantiable()) {
				continue;
			}
			$class = new $class_name;
			$protocols[$class->name()] = array(
				'name' => $class->nameLong(),
			);
			unset($class);
		}
		unset($dir);
		ksort($protocols);
		foreach ((array)$protocols as $gameq => $info) {
			$queryChoices[$gameq] = $info['name'];
		}
		$queryLabel = 'GameQ Query Name';
	}

	$safeProtocol = htmlspecialchars($selectedProtocol, ENT_QUOTES, 'UTF-8');
	echo <<<CSS
<style>
.xml-creator-wrapper{max-width:1100px;margin:0 auto;padding:20px;color:#eee;font-family:'Segoe UI',Tahoma,Arial,sans-serif}
.xml-creator-title{margin-bottom:4px;font-size:1.8rem;font-weight:600}
.xml-creator-lead{color:#bbb;margin-bottom:20px;max-width:900px}
.xml-creator-card{background:#111;border:1px solid #222;border-radius:10px;padding:18px;margin-bottom:18px;box-shadow:0 8px 18px rgba(0,0,0,0.25)}
.xml-creator-card label{display:block;font-size:0.9rem;color:#aaa;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.08em}
.xml-creator-card select,.xml-creator-card button,.xml-creator-card input[type="text"],.xml-creator-card input[type="number"]{width:100%;padding:10px 12px;border:1px solid #2f2f2f;border-radius:6px;background:#0c0c0c;color:#fff;font-size:1rem}
.xml-creator-hint{font-size:0.85rem;color:#888;margin-top:6px}
.xml-creator-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:18px}
.xml-creator-actions{display:flex;justify-content:flex-end;margin-top:12px}
.xml-creator-note{font-size:0.9rem;color:#bbb;margin:12px 0 24px}
.xml-creator-protocol-form{margin-bottom:26px}
.xml-global-save{background:#1c6dd0;border:1px solid #114b99;color:#fff;padding:10px 28px;border-radius:4px;font-weight:600;text-transform:uppercase;letter-spacing:0.03em;cursor:pointer;transition:background 0.2s ease,transform 0.2s ease;box-shadow:0 2px 6px rgba(0,0,0,0.35)}
.xml-global-save:hover{background:#1f7aec;transform:translateY(-1px)}
</style>
CSS;

	echo "<div class='xml-creator-wrapper'>";
	echo "<h2 class='xml-creator-title'>XML Config Creator</h2>";
	echo "<p class='xml-creator-lead'>Use this guided workflow to spin up a brand new XML configuration template. Pick the query layer first, then define the operating system, architecture, and installer defaults before launching the step-by-step wizard.</p>";

	echo "<form action='' method='POST' class='xml-creator-card xml-creator-protocol-form'>";
	echo "<label for='protocol_select'>Query Protocol</label>";
	echo "<select id='protocol_select' name='protocol' onchange='this.form.submit()'>";
	$protocolOptions = array(
		'' => 'No Query Layer',
		'lgsl' => 'LGSL',
		'gameq' => 'GameQ',
	);
	foreach ((array)$protocolOptions as $value => $label) {
		$safeValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		$safeLabel = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
		$selected = ($selectedProtocol === $value) ? "selected='selected'" : '';
		echo "<option value='{$safeValue}' {$selected}>{$safeLabel}</option>";
	}
	echo "</select>";
	echo "<p class='xml-creator-hint'>Selecting a protocol automatically reloads this page and reveals available query names.</p>";
	echo "</form>";

	echo "<form action='home.php?m=config_games&p=cli-params&type=cleared' method='POST' class='xml-creator-form' name='xml_creator'>";
	if ($selectedProtocol !== '') {
		echo "<input type='hidden' name='protocol' value='{$safeProtocol}'/>";
	}

	echo "<div class='xml-creator-card'>";
	if (!empty($queryChoices)) {
		$label = htmlspecialchars($queryLabel, ENT_QUOTES, 'UTF-8');
		echo "<label for='query_name'>{$label}</label>";
		echo "<select id='query_name' name='query_name'>";
		foreach ((array)$queryChoices as $value => $labelText) {
			$safeValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
			$safeLabelText = htmlspecialchars($labelText, ENT_QUOTES, 'UTF-8');
			echo "<option value='{$safeValue}'>{$safeLabelText}</option>";
		}
		echo "</select>";
		echo "<p class='xml-creator-hint'>The query name informs the monitoring layer which protocol preset to use.</p>";
	} else {
		echo "<label>Query Name</label>";
		echo "<div class='xml-creator-hint'>Select LGSL or GameQ above if this config should publish a query endpoint. You can skip this if the game is not queryable.</div>";
	}
	echo "</div>";

	echo "<div class='xml-creator-grid'>";
	echo "<div class='xml-creator-card'>";
	echo "<label for='os_select'>Operating System</label>";
	echo "<select id='os_select' name='os'>";
	echo "<option value='linux'>Linux</option>";
	echo "<option value='win'>Windows</option>";
	echo "</select>";
	echo "<p class='xml-creator-hint'>Defines which agent pool can install the server.</p>";
	echo "</div>";

	echo "<div class='xml-creator-card'>";
	echo "<label for='arch_select'>Architecture</label>";
	echo "<select id='arch_select' name='arch'>";
	echo "<option value='32'>32-bit</option>";
	echo "<option value='64'>64-bit</option>";
	echo "</select>";
	echo "<p class='xml-creator-hint'>Pick the CPU target that the XML should advertise.</p>";
	echo "</div>";

	echo "<div class='xml-creator-card'>";
	echo "<label for='installer_select'>Installer</label>";
	echo "<select id='installer_select' name='installer'>";
	echo "<option value=''>None</option>";
	echo "<option value='steam'>SteamCMD / HLDSUpdateTool</option>";
	echo "</select>";
	echo "<p class='xml-creator-hint'>Optional helper to bootstrap files directly from Steam.</p>";
	echo "</div>";
	echo "</div>";

	echo "<p class='xml-creator-note'>When you click the button below the panel will send these settings into the XML creation wizard so the initial document already matches your desired platform.</p>";
	echo "<div class='xml-creator-actions'><button type='submit' name='main' class='xml-global-save'>Launch Creator</button></div>";
	echo "</form>";
	echo "</div>";
}
