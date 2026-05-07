<?php
/**
 * Admin: Game Mod/Build Defaults for Billing Auto-Provisioning
 *
 * Allows admins to:
 *  - See available mods/builds per game config (config_mods table)
 *  - Mark exactly one mod/build per game as the billing auto-install default
 *    (is_default_for_billing = 1)
 *  - Override the per-service mod_cfg_id in billing_services
 *
 * A safe migration for is_default_for_billing is included in billing module.php
 * db_version 8.  This page gracefully handles the column being absent.
 */

function exec_ogp_module()
{
	global $db, $view, $table_prefix;
	$db_prefix = isset($table_prefix) ? $table_prefix : '';
	$user_id   = $_SESSION['user_id'];
	$isAdmin   = $db->isAdmin($user_id);

	if (!$isAdmin) {
		echo "<div class='failure'><p>Access Denied: Admin privileges required.</p></div>";
		return;
	}

	// -------------------------------------------------------------------
	// Check whether is_default_for_billing column exists.
	// It is added by db_version 8 migration; show a warning if missing.
	// -------------------------------------------------------------------
	$colExists = false;
	$colCheck = $db->resultQuery(
		"SELECT COUNT(*) AS cnt
		   FROM INFORMATION_SCHEMA.COLUMNS
		  WHERE TABLE_SCHEMA = DATABASE()
		    AND TABLE_NAME   = '{$db_prefix}config_mods'
		    AND COLUMN_NAME  = 'is_default_for_billing'"
	);
	if (!empty($colCheck[0]['cnt']) && intval($colCheck[0]['cnt']) > 0) {
		$colExists = true;
	}

	if (!$colExists) {
		echo "<div class='failure'>"
		   . "<p><strong>Database migration required.</strong> "
		   . "The <code>is_default_for_billing</code> column is not present in <code>{$db_prefix}config_mods</code>. "
		   . "Please run the billing module update (Admin &rarr; Module Manager &rarr; Update) to apply db_version&nbsp;8.</p>"
		   . "</div>";
		return;
	}

	// -------------------------------------------------------------------
	// Handle POST: save default mod for a game (home_cfg_id)
	// -------------------------------------------------------------------
	$saveMsg = '';
	if (isset($_POST['save_default']) && isset($_POST['home_cfg_id'])) {
		$save_home_cfg_id = intval($_POST['home_cfg_id']);
		$save_mod_cfg_id  = intval($_POST['mod_cfg_id'] ?? 0);

		// Clear all current defaults for this game first
		$db->query(
			"UPDATE `{$db_prefix}config_mods`
			    SET is_default_for_billing = 0
			  WHERE home_cfg_id = " . $save_home_cfg_id
		);

		if ($save_mod_cfg_id > 0) {
			// Set the selected mod as default (only if it belongs to this game)
			$updated = $db->query(
				"UPDATE `{$db_prefix}config_mods`
				    SET is_default_for_billing = 1
				  WHERE mod_cfg_id  = " . $save_mod_cfg_id . "
				    AND home_cfg_id = " . $save_home_cfg_id
			);
			$saveMsg = $updated
				? "<div class='success'><p>Default mod/build updated for game config #{$save_home_cfg_id}.</p></div>"
				: "<div class='failure'><p>Failed to update default — mod may not belong to this game.</p></div>";
		} else {
			$saveMsg = "<div class='info'><p>Default cleared for game config #{$save_home_cfg_id}. Billing will use the service-specific mod or fail with an admin-visible error if none is set.</p></div>";
		}
	}

	echo $saveMsg;
	echo "<h2>Game Mod/Build Defaults for Billing</h2>";
	echo "<p>Mark one mod/build per game as the auto-install default used when billing provisions a new server. "
	   . "This is used when a billing service does not specify its own mod (mod_cfg_id&nbsp;=&nbsp;0).</p>";
	echo "<p><strong>Priority order during provisioning:</strong> "
	   . "1) Service-specific mod_cfg_id &rarr; 2) is_default_for_billing here &rarr; "
	   . "3) Single available mod (auto-selected) &rarr; 4) Fail with admin-visible error.</p>";

	// -------------------------------------------------------------------
	// Load all game configs that have at least one mod defined
	// -------------------------------------------------------------------
	$games = $db->resultQuery(
		"SELECT ch.home_cfg_id, ch.home_name
		   FROM `{$db_prefix}config_homes` ch
		  WHERE EXISTS (
		      SELECT 1 FROM `{$db_prefix}config_mods` cm
		       WHERE cm.home_cfg_id = ch.home_cfg_id
		  )
		  ORDER BY ch.home_name ASC"
	);

	if (empty($games)) {
		echo "<div class='info'><p>No game configurations with mods found. Add mods via Admin &rarr; Game Manager &rarr; Configure Games.</p></div>";
		return;
	}

	echo "<table class='tablesorter' style='width:100%;'>";
	echo "<thead><tr><th>Game</th><th>home_cfg_id</th><th>Available Mods/Builds</th><th>Current Default</th><th>Action</th></tr></thead><tbody>";

	foreach ((array)$games as $game) {
		$hcfgid   = intval($game['home_cfg_id']);
		$gameName = htmlspecialchars($game['home_name'] ?? "Game #{$hcfgid}");

		// Load mods for this game
		$mods = $db->resultQuery(
			"SELECT mod_cfg_id, mod_key, mod_name, is_default_for_billing
			   FROM `{$db_prefix}config_mods`
			  WHERE home_cfg_id = " . $hcfgid . "
			  ORDER BY mod_name ASC"
		);

		if (empty($mods)) {
			continue;
		}

		$currentDefault = null;
		foreach ($mods as $m) {
			if (!empty($m['is_default_for_billing'])) {
				$currentDefault = htmlspecialchars($m['mod_name'] . ' (mod_cfg_id=' . $m['mod_cfg_id'] . ')');
			}
		}

		echo "<tr>";
		echo "<td><strong>{$gameName}</strong></td>";
		echo "<td>{$hcfgid}</td>";
		echo "<td>";
		$modNames = array_map(fn($m) => htmlspecialchars($m['mod_name']), $mods);
		echo implode('<br>', $modNames);
		echo "</td>";
		echo "<td>";
		echo $currentDefault
			? "<span style='color:green;'>&#10003; " . $currentDefault . "</span>"
			: "<span style='color:#999;'>None</span>";
		echo "</td>";
		echo "<td>";

		// Form to set default
		echo "<form method='post' action='home.php?m=billing&p=admin_game_defaults' style='white-space:nowrap;'>";
		echo "<input type='hidden' name='save_default' value='1'>";
		echo "<input type='hidden' name='home_cfg_id' value='{$hcfgid}'>";
		echo "<select name='mod_cfg_id'>";
		echo "<option value='0'>(No default / clear)</option>";
		foreach ($mods as $m) {
			$sel = !empty($m['is_default_for_billing']) ? " selected" : "";
			echo "<option value='" . intval($m['mod_cfg_id']) . "'{$sel}>"
			   . htmlspecialchars($m['mod_name'])
			   . " [" . htmlspecialchars($m['mod_key']) . "]"
			   . "</option>";
		}
		echo "</select> ";
		echo "<button type='submit' class='btn btn-sm'>Save</button>";
		echo "</form>";

		echo "</td>";
		echo "</tr>";
	}

	echo "</tbody></table>";

	// -------------------------------------------------------------------
	// Show billing_services with their current mod_cfg_id
	// -------------------------------------------------------------------
	echo "<div style='margin-top:30px;'>";
	echo "<h3>Billing Services — Mod/Build Override</h3>";
	echo "<p>Services below have an explicit <code>mod_cfg_id</code> set. This takes priority over the game default above. "
	   . "Set to 0 to fall back to the game default.</p>";

	$services = $db->resultQuery(
		"SELECT s.service_id, s.service_name, s.home_cfg_id, s.mod_cfg_id,
		        ch.home_name AS game_name,
		        cm.mod_name
		   FROM `{$db_prefix}billing_services` s
		   LEFT JOIN `{$db_prefix}config_homes` ch ON ch.home_cfg_id = s.home_cfg_id
		   LEFT JOIN `{$db_prefix}config_mods` cm ON cm.mod_cfg_id = s.mod_cfg_id
		  ORDER BY s.service_name ASC"
	);

	if (empty($services)) {
		echo "<p style='color:#999;'>No billing services configured.</p>";
	} else {
		echo "<table class='tablesorter' style='width:100%;'>";
		echo "<thead><tr><th>Service</th><th>Game</th><th>Current mod_cfg_id</th><th>Mod Name</th></tr></thead><tbody>";
		foreach ((array)$services as $svc) {
			echo "<tr>";
			echo "<td>".htmlspecialchars($svc['service_name'] ?? '')." (#".intval($svc['service_id']).")</td>";
			echo "<td>".htmlspecialchars($svc['game_name'] ?? 'N/A')."</td>";
			echo "<td>".intval($svc['mod_cfg_id']).(intval($svc['mod_cfg_id']) === 0 ? " <em>(use game default)</em>" : "")."</td>";
			echo "<td>".htmlspecialchars($svc['mod_name'] ?? ($svc['mod_cfg_id'] == 0 ? '—' : 'mod not found'))."</td>";
			echo "</tr>";
		}
		echo "</tbody></table>";
		echo "<p><small>To change a service's mod, edit it in Admin &rarr; Billing &rarr; Services.</small></p>";
	}

	echo "</div>";
}
?>
