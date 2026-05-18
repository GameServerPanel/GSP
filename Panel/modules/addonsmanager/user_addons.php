<?php
/*
 *
 * GSP - Game Server Panel (a heavily customized fork of OGP maintained by WDS)
 *
 * User page: Server Content (module: addonsmanager, page: user_addons)
 * ─────────────────────────────────────────────────────────────────────────────
 * Shows the available Server Content categories for a specific game server
 * home.  Each category that has at least one content item configured for the
 * server's game type is displayed as a clickable link that takes the user to
 * the installer (addons_installer.php).
 *
 * Group filtering: non-admin users only see content items assigned to one of
 * their groups, or to the "All groups" (group_id=0 / NULL) bucket.
 *
 */

// Central category map — load so we can iterate all types dynamically.
require_once(dirname(__FILE__) . '/server_content_categories.php');
require_once(dirname(__FILE__) . '/server_content_helpers.php');

function exec_ogp_module() {
	global $db;
	$home_id = $_GET['home_id'];
	$mod_id  = $_GET['mod_id'];
	$ip      = $_GET['ip'];
	$port    = $_GET['port'];
	$user_id = $_SESSION['user_id'];
	// Check if user has some games.
	$isAdmin = $db->isAdmin( $user_id );
	$query_groups = "";
	if($isAdmin) 
		$home_info = $db->getGameHome($home_id);
	else
	{
		$home_info = $db->getUserGameHome($user_id,$home_id);
		$groups = $db->getUsersGroups($_SESSION['user_id']);
		if (!is_array($groups)) {
			$groups = [];
		}
		$query_groups .= " AND (";
		foreach ((array)$groups as $group)
			$query_groups .= "group_id=".$group['group_id']." OR ";
		$query_groups .= "group_id=0 OR group_id IS NULL)";
	}
	if ($home_info)
	{
		scm_ensure_workshop_schema($db);
		$home_cfg_id = $home_info['home_cfg_id'];
		echo "<h2>Server Content: ".htmlentities($home_info['home_name'])."</h2>\n".
			 "<table class='center' >\n".
			 "<tr>\n";

		// Iterate all registered content types.  Each type that has at least
		// one item for this game generates a link to the installer page.
		// New types added to server_content_categories.php automatically
		// appear here without any further code changes.
		$categories       = get_server_content_categories();   // key => label
		$printed_any_cell = false;

		foreach ((array)$categories as $type_key => $type_label)
		{
			if ($type_key === 'workshop')
			{
				$workshop_count = scm_get_workshop_saved_count($db, (int)$home_id);
				if ($printed_any_cell)
					echo "</td><td>\n";
				else
					echo "<td>\n";
				$printed_any_cell = true;
				echo "<a href='?m=addonsmanager&amp;p=workshop_content" .
					 "&amp;home_id=" . (int)$home_id .
					 "&amp;mod_id=" . (int)$mod_id .
					 "&amp;ip=" . htmlspecialchars($ip) .
					 "&amp;port=" . htmlspecialchars($port) . "'>" .
					 "Workshop Content (" . (int)$workshop_count . ")" .
					 "</a>\n";
				continue;
			}

			$items = $db->resultQuery(
				"SELECT DISTINCT addon_id, name, game_name " .
				"FROM OGP_DB_PREFIXaddons " .
				"NATURAL JOIN OGP_DB_PREFIXconfig_homes " .
				"WHERE addon_type='" . $db->realEscapeSingle($type_key) . "' " .
				"AND home_cfg_id=" . (int)$home_cfg_id . $query_groups
			);
			$items_qty = is_array($items) ? count((array)$items) : 0;
			if ($items && $items_qty >= 1)
			{
				if ($printed_any_cell)
					echo "</td><td>\n";
				else
					echo "<td>\n";
				$printed_any_cell = true;
				// Display label comes from the category map; the internal
				// addon_type key is passed in the URL for backward compatibility.
				echo "<a href='?m=addonsmanager&amp;p=addons" .
					 "&amp;home_id=" . (int)$home_id .
					 "&amp;mod_id=" . (int)$mod_id .
					 "&amp;addon_type=" . urlencode($type_key) .
					 "&amp;ip=" . htmlspecialchars($ip) .
					 "&amp;port=" . htmlspecialchars($port) . "'>" .
					 htmlspecialchars($type_label) . " (" . $items_qty . ")" .
					 "</a>\n";
			}
		}

		if ($printed_any_cell)
			echo "</td>\n";

		echo "</tr>\n".
			 "</table>\n".
			 "<form action='?m=gamemanager&amp;p=game_monitor&amp;home_id-mod_id-ip-port=$home_id-$mod_id-$ip-$port' method='POST'>\n".
			 "<input type='submit' value='".get_lang('back')."' />\n".
			 "</form>\n".
			 "<br>\n";
	}
	else
		print_failure(get_lang('no_games_servers_available'));
}
?>
