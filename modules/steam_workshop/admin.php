<?php
/*
 * GSP – Steam Workshop: Admin profile management
 * Copyright (C) 2025 WDS / GameServerPanel
 *
 * Accessible via: home.php?m=steam_workshop&p=admin
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 */

require_once __DIR__ . '/includes/functions.php';

// Load the XML config parser so sw_sync_profiles() can read game configs.
if (!defined('SERVER_CONFIG_LOCATION')) {
    require_once __DIR__ . '/../../config_games/server_config_parser.php';
}

function exec_ogp_module()
{
    global $db;

    echo '<h2>Steam Workshop – Admin</h2>';

    $action = isset($_GET['action']) ? $_GET['action'] : '';

    // ── POST: save a profile edit ─────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
        sw_admin_save_profile($db);
        return;
    }

    // ── POST: sync profiles from XML configs ──────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sync_profiles'])) {
        $n = sw_sync_profiles($db);
        sw_success("Sync complete. $n new profile(s) created.");
    }

    // ── GET: show edit form for one profile ───────────────────────────
    if ($action === 'edit' && isset($_GET['id'])) {
        $profile = sw_get_profile_by_id($db, (int)$_GET['id']);
        if ($profile) {
            sw_admin_edit_form($profile);
        } else {
            sw_error('Profile not found.');
            sw_admin_list($db);
        }
        return;
    }

    // ── Default: list all profiles ────────────────────────────────────
    sw_admin_list($db);
}

// ─────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────

function sw_admin_save_profile($db)
{
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if (!$id) {
        sw_error('Invalid profile ID.');
        sw_admin_list($db);
        return;
    }

    $profile = sw_get_profile_by_id($db, $id);
    if (!$profile) {
        sw_error('Profile not found.');
        sw_admin_list($db);
        return;
    }

    // Collect and sanitize fields from POST.
    $fields = array(
        'enabled'                         => isset($_POST['enabled'])    ? 1 : 0,
        'steam_app_id'                    => trim($_POST['steam_app_id']                    ?? ''),
        'workshop_app_id'                 => trim($_POST['workshop_app_id']                 ?? ''),
        'steam_login_required'            => isset($_POST['steam_login_required'])          ? 1 : 0,
        'steamcmd_login_mode'             => $_POST['steamcmd_login_mode'] === 'account' ? 'account' : 'anonymous',
        'steamcmd_path'                   => trim($_POST['steamcmd_path']                   ?? ''),
        'workshop_download_dir_template'  => trim($_POST['workshop_download_dir_template']  ?? ''),
        'server_root_template'            => trim($_POST['server_root_template']            ?? ''),
        'install_path_template'           => trim($_POST['install_path_template']           ?? ''),
        'folder_naming_format'            => trim($_POST['folder_naming_format']            ?? ''),
        'mod_launch_param_template'       => trim($_POST['mod_launch_param_template']       ?? '-mod='),
        'servermod_launch_param_template' => trim($_POST['servermod_launch_param_template'] ?? '-serverMod='),
        'install_script_template'         => trim($_POST['install_script_template']         ?? ''),
        'update_script_template'          => trim($_POST['update_script_template']          ?? ''),
        'copy_bikeys_enabled'             => isset($_POST['copy_bikeys_enabled'])           ? 1 : 0,
        'notes'                           => trim($_POST['notes']                           ?? ''),
    );

    $set_parts = array();
    foreach ($fields as $col => $val) {
        $safe = $db->realEscapeSingle($val);
        $set_parts[] = "`$col` = '$safe'";
    }
    $set_parts[] = "`updated_at` = NOW()";

    $set_sql = implode(', ', $set_parts);

    $ok = $db->query(
        "UPDATE `OGP_DB_PREFIXsteam_workshop_game_profiles`
            SET $set_sql
          WHERE `id` = $id LIMIT 1"
    );

    if ($ok) {
        sw_success('Profile saved.');
    } else {
        sw_error('Failed to save profile.');
    }

    $profile = sw_get_profile_by_id($db, $id);
    if ($profile) {
        sw_admin_edit_form($profile);
    } else {
        sw_admin_list($db);
    }
}

function sw_admin_list($db)
{
    $profiles = sw_get_profiles($db);
    ?>
<p>
  Each game config XML gets one Workshop profile.
  Use <strong>Sync Profiles</strong> to auto-create rows for new game configs.
  Enable and configure each profile to activate Steam Workshop for that game.
</p>

<form method="post" style="display:inline;">
  <button type="submit" name="sync_profiles" value="1"
          onclick="return confirm('Sync workshop profiles from all game config XMLs?');"
          class="button">Sync Profiles from XML Configs</button>
</form>

<hr>

<?php if (empty($profiles)): ?>
<p>No profiles yet. Click <em>Sync Profiles</em> to create them from the installed game configs.</p>
<?php else: ?>
<table class="table" width="100%" style="border-collapse:collapse;">
  <thead>
    <tr style="background:#f0f0f0;">
      <th style="padding:6px 8px;text-align:left;">Config Name</th>
      <th style="padding:6px 8px;text-align:left;">Game Name</th>
      <th style="padding:6px 8px;text-align:center;">Workshop App ID</th>
      <th style="padding:6px 8px;text-align:center;">Enabled</th>
      <th style="padding:6px 8px;text-align:center;">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($profiles as $p): ?>
    <tr style="border-bottom:1px solid #ddd;">
      <td style="padding:6px 8px;font-family:monospace;"><?= sw_h($p['config_name']) ?></td>
      <td style="padding:6px 8px;"><?= sw_h($p['game_name']) ?></td>
      <td style="padding:6px 8px;text-align:center;"><?= sw_h($p['workshop_app_id']) ?></td>
      <td style="padding:6px 8px;text-align:center;">
        <?= $p['enabled'] ? '<span style="color:green;font-weight:bold;">Yes</span>' : '<span style="color:#999;">No</span>' ?>
      </td>
      <td style="padding:6px 8px;text-align:center;">
        <a href="home.php?m=steam_workshop&p=admin&action=edit&id=<?= (int)$p['id'] ?>"
           class="button small">Edit</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php endif;
}

function sw_admin_edit_form(array $profile)
{
    $id = (int)$profile['id'];
    ?>
<p><a href="home.php?m=steam_workshop&p=admin">&laquo; Back to profile list</a></p>

<h3>Edit Profile: <?= sw_h($profile['config_name']) ?> &ndash; <?= sw_h($profile['game_name']) ?></h3>

<p style="background:#fff8dc;border:1px solid #e0d090;padding:8px 12px;border-radius:4px;">
  <strong>Supported placeholders</strong> (use in path/script templates):<br>
  <code>{HOME_ID}</code> &nbsp;
  <code>{SERVER_ID}</code> &nbsp;
  <code>{REMOTE_SERVER_ID}</code> &nbsp;
  <code>{GAME_NAME}</code> &nbsp;
  <code>{CONFIG_NAME}</code> &nbsp;
  <code>{WORKSHOP_ID}</code> &nbsp;
  <code>{MOD_NAME}</code> &nbsp;
  <code>{FOLDER_NAME}</code> &nbsp;
  <code>{STEAM_APP_ID}</code> &nbsp;
  <code>{WORKSHOP_APP_ID}</code> &nbsp;
  <code>{STEAMCMD_PATH}</code> &nbsp;
  <code>{WORKSHOP_DOWNLOAD_DIR}</code> &nbsp;
  <code>{SERVER_ROOT}</code> &nbsp;
  <code>{INSTALL_PATH}</code> &nbsp;
  <code>{MOD_FOLDER}</code>
</p>

<form method="post" action="home.php?m=steam_workshop&p=admin&action=edit&id=<?= $id ?>">
  <input type="hidden" name="id" value="<?= $id ?>">

  <table width="100%" style="border-collapse:collapse;">

    <tr>
      <td colspan="2" style="background:#eee;padding:6px 8px;font-weight:bold;">General</td>
    </tr>

    <tr>
      <td style="padding:6px 8px;width:260px;"><label>Enabled</label></td>
      <td style="padding:6px 8px;">
        <input type="checkbox" name="enabled" value="1"
               <?= $profile['enabled'] ? 'checked' : '' ?>>
        Enable Steam Workshop for this game config
      </td>
    </tr>

    <tr>
      <td colspan="2" style="background:#eee;padding:6px 8px;font-weight:bold;">Steam / SteamCMD</td>
    </tr>

    <tr>
      <td style="padding:6px 8px;"><label for="steam_app_id">Steam App ID</label></td>
      <td style="padding:6px 8px;">
        <input type="text" id="steam_app_id" name="steam_app_id"
               value="<?= sw_h($profile['steam_app_id']) ?>"
               style="width:200px;">
        <span style="color:#666;font-size:0.9em;">(e.g. 223350 for DayZ Dedicated Server)</span>
      </td>
    </tr>

    <tr>
      <td style="padding:6px 8px;"><label for="workshop_app_id">Workshop App ID</label></td>
      <td style="padding:6px 8px;">
        <input type="text" id="workshop_app_id" name="workshop_app_id"
               value="<?= sw_h($profile['workshop_app_id']) ?>"
               style="width:200px;">
        <span style="color:#666;font-size:0.9em;">(e.g. 221100 for DayZ Workshop content)</span>
      </td>
    </tr>

    <tr>
      <td style="padding:6px 8px;"><label for="steamcmd_path">SteamCMD Path</label></td>
      <td style="padding:6px 8px;">
        <input type="text" id="steamcmd_path" name="steamcmd_path"
               value="<?= sw_h($profile['steamcmd_path']) ?>"
               style="width:480px;">
      </td>
    </tr>

    <tr>
      <td style="padding:6px 8px;"><label>Steam Login Required</label></td>
      <td style="padding:6px 8px;">
        <input type="checkbox" name="steam_login_required" value="1"
               <?= $profile['steam_login_required'] ? 'checked' : '' ?>>
        Requires authenticated Steam login (not anonymous)
      </td>
    </tr>

    <tr>
      <td style="padding:6px 8px;"><label for="steamcmd_login_mode">SteamCMD Login Mode</label></td>
      <td style="padding:6px 8px;">
        <select id="steamcmd_login_mode" name="steamcmd_login_mode">
          <option value="anonymous" <?= $profile['steamcmd_login_mode'] === 'anonymous' ? 'selected' : '' ?>>anonymous</option>
          <option value="account"   <?= $profile['steamcmd_login_mode'] === 'account'   ? 'selected' : '' ?>>account (Steam username/password needed)</option>
        </select>
      </td>
    </tr>

    <tr>
      <td colspan="2" style="background:#eee;padding:6px 8px;font-weight:bold;">Paths</td>
    </tr>

    <tr>
      <td style="padding:6px 8px;"><label for="workshop_download_dir_template">Workshop Download Dir</label></td>
      <td style="padding:6px 8px;">
        <input type="text" id="workshop_download_dir_template" name="workshop_download_dir_template"
               value="<?= sw_h($profile['workshop_download_dir_template']) ?>"
               style="width:480px;">
        <br><span style="color:#666;font-size:0.9em;">
          Where SteamCMD downloads mods.<br>
          Example: <code>{SERVER_ROOT}/steamapps/workshop/content/{WORKSHOP_APP_ID}</code>
        </span>
      </td>
    </tr>

    <tr>
      <td style="padding:6px 8px;"><label for="server_root_template">Server Root</label></td>
      <td style="padding:6px 8px;">
        <input type="text" id="server_root_template" name="server_root_template"
               value="<?= sw_h($profile['server_root_template']) ?>"
               style="width:480px;">
        <br><span style="color:#666;font-size:0.9em;">
          Root directory of the game server. Example: <code>/home/gameserver/servers/{HOME_ID}</code>
        </span>
      </td>
    </tr>

    <tr>
      <td style="padding:6px 8px;"><label for="install_path_template">Mod Install Path</label></td>
      <td style="padding:6px 8px;">
        <input type="text" id="install_path_template" name="install_path_template"
               value="<?= sw_h($profile['install_path_template']) ?>"
               style="width:480px;">
        <br><span style="color:#666;font-size:0.9em;">
          Where the renamed mod folder ends up. Example: <code>{SERVER_ROOT}/{MOD_FOLDER}</code>
        </span>
      </td>
    </tr>

    <tr>
      <td colspan="2" style="background:#eee;padding:6px 8px;font-weight:bold;">Folder &amp; Launch Params</td>
    </tr>

    <tr>
      <td style="padding:6px 8px;"><label for="folder_naming_format">Folder Naming Format</label></td>
      <td style="padding:6px 8px;">
        <input type="text" id="folder_naming_format" name="folder_naming_format"
               value="<?= sw_h($profile['folder_naming_format']) ?>"
               style="width:300px;">
        <br><span style="color:#666;font-size:0.9em;">
          Default folder name template. Common values: <code>@{MOD_NAME}</code> or <code>@{WORKSHOP_ID}</code>
        </span>
      </td>
    </tr>

    <tr>
      <td style="padding:6px 8px;"><label for="mod_launch_param_template">Client Mod Launch Param</label></td>
      <td style="padding:6px 8px;">
        <input type="text" id="mod_launch_param_template" name="mod_launch_param_template"
               value="<?= sw_h($profile['mod_launch_param_template']) ?>"
               style="width:200px;">
        <span style="color:#666;font-size:0.9em;">Prefix for client-required mods (e.g. <code>-mod=</code>)</span>
      </td>
    </tr>

    <tr>
      <td style="padding:6px 8px;"><label for="servermod_launch_param_template">Server-Side Mod Launch Param</label></td>
      <td style="padding:6px 8px;">
        <input type="text" id="servermod_launch_param_template" name="servermod_launch_param_template"
               value="<?= sw_h($profile['servermod_launch_param_template']) ?>"
               style="width:200px;">
        <span style="color:#666;font-size:0.9em;">Prefix for server-only mods (e.g. <code>-serverMod=</code>)</span>
      </td>
    </tr>

    <tr>
      <td style="padding:6px 8px;"><label>Copy .bikey Files</label></td>
      <td style="padding:6px 8px;">
        <input type="checkbox" name="copy_bikeys_enabled" value="1"
               <?= $profile['copy_bikeys_enabled'] ? 'checked' : '' ?>>
        Copy .bikey files from mod keys/ folder into server keys/ folder
      </td>
    </tr>

    <tr>
      <td colspan="2" style="background:#eee;padding:6px 8px;font-weight:bold;">Scripts (optional)</td>
    </tr>

    <tr>
      <td style="padding:6px 8px;vertical-align:top;"><label for="install_script_template">Install Script Template</label></td>
      <td style="padding:6px 8px;">
        <textarea id="install_script_template" name="install_script_template"
                  rows="6" style="width:100%;font-family:monospace;"
        ><?= sw_h($profile['install_script_template']) ?></textarea>
        <span style="color:#666;font-size:0.9em;">
          Shell commands to run when installing a mod for the first time. Placeholders expanded before execution.
        </span>
      </td>
    </tr>

    <tr>
      <td style="padding:6px 8px;vertical-align:top;"><label for="update_script_template">Update Script Template</label></td>
      <td style="padding:6px 8px;">
        <textarea id="update_script_template" name="update_script_template"
                  rows="6" style="width:100%;font-family:monospace;"
        ><?= sw_h($profile['update_script_template']) ?></textarea>
        <span style="color:#666;font-size:0.9em;">
          Shell commands to run when updating an already-installed mod.
        </span>
      </td>
    </tr>

    <tr>
      <td colspan="2" style="background:#eee;padding:6px 8px;font-weight:bold;">Notes</td>
    </tr>

    <tr>
      <td style="padding:6px 8px;vertical-align:top;"><label for="notes">Notes</label></td>
      <td style="padding:6px 8px;">
        <textarea id="notes" name="notes"
                  rows="4" style="width:100%;"
        ><?= sw_h($profile['notes']) ?></textarea>
      </td>
    </tr>

  </table>

  <p>
    <button type="submit" name="save_profile" value="1" class="button">Save Profile</button>
    &nbsp;
    <a href="home.php?m=steam_workshop&p=admin" class="button">Cancel</a>
  </p>

</form>

<hr>
<h4>DayZ Default Values (for reference)</h4>
<ul>
  <li><strong>Steam App ID:</strong> 223350 (DayZ Dedicated Server)</li>
  <li><strong>Workshop App ID:</strong> 221100 (DayZ Workshop)</li>
  <li><strong>Workshop Download Dir:</strong> <code>{SERVER_ROOT}/steamapps/workshop/content/{WORKSHOP_APP_ID}</code></li>
  <li><strong>Folder Naming Format:</strong> <code>@{MOD_NAME}</code></li>
  <li><strong>Client Mod Launch Param:</strong> <code>-mod=</code></li>
  <li><strong>Server-Side Mod Launch Param:</strong> <code>-serverMod=</code></li>
  <li><strong>Copy .bikey Files:</strong> Yes</li>
</ul>
<?php
}
