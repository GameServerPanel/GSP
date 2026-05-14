<?php
/*
 * GSP – Steam Workshop: Admin profile management
 * Copyright (C) 2025 WDS / GameServerPanel
 */

require_once __DIR__ . '/includes/functions.php';
if (!defined('SERVER_CONFIG_LOCATION')) {
    require_once __DIR__ . '/../../config_games/server_config_parser.php';
}

function exec_ogp_module()
{
    global $db;

    echo '<h2>Steam Workshop &ndash; Admin</h2>';
    sw_admin_print_styles();

    $action = $_GET['action'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
        sw_admin_save_profile($db);
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sync_profiles'])) {
        $n = sw_sync_profiles($db);
        sw_success("Sync complete. $n new profile(s) created.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['detect_defaults'])) {
        $profile = sw_get_profile_by_id($db, (int)($_POST['id'] ?? 0));
        if (!$profile) {
            sw_error('Profile not found.');
            sw_admin_list($db);
            return;
        }
        $detected = sw_detect_profile_defaults_from_xml($profile['config_name']);
        if (empty($detected)) {
            sw_error('No Steam defaults were detected in this game XML. You can still enter values manually.');
        } else {
            sw_success('Detected XML defaults. Review and apply when ready.');
        }
        sw_admin_edit_form($profile, $detected, true);
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_detected_defaults'])) {
        $profile = sw_get_profile_by_id($db, (int)($_POST['id'] ?? 0));
        if (!$profile) {
            sw_error('Profile not found.');
            sw_admin_list($db);
            return;
        }
        $detected = sw_detect_profile_defaults_from_xml($profile['config_name']);
        if (empty($detected)) {
            sw_error('No Steam defaults were detected in this game XML.');
            sw_admin_edit_form($profile);
            return;
        }

        $overwrite = isset($_POST['overwrite_existing']) && $_POST['overwrite_existing'] === '1';
        $updated = sw_apply_detected_profile_defaults($db, $profile, $detected, $overwrite);
        if ($updated > 0) {
            $overwriteMessage = $overwrite
                ? ' Existing values were allowed to be overwritten.'
                : ' Existing non-empty values were kept.';
            sw_success("Applied $updated detected default value(s)." . $overwriteMessage);
        } else {
            sw_success('No profile values needed updating based on current overwrite setting.');
        }

        $profile = sw_get_profile_by_id($db, (int)$profile['id']);
        sw_admin_edit_form($profile, $detected, true);
        return;
    }

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

    sw_admin_list($db);
}

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

    $fields = array(
        'enabled'                         => isset($_POST['enabled']) ? 1 : 0,
        'steam_app_id'                    => trim($_POST['steam_app_id'] ?? ''),
        'workshop_app_id'                 => trim($_POST['workshop_app_id'] ?? ''),
        'steam_login_required'            => isset($_POST['steam_login_required']) ? 1 : 0,
        'steamcmd_login_mode'             => (($_POST['steamcmd_login_mode'] ?? 'anonymous') === 'account') ? 'account' : 'anonymous',
        'steamcmd_path'                   => trim($_POST['steamcmd_path'] ?? ''),
        'workshop_download_dir_template'  => trim($_POST['workshop_download_dir_template'] ?? ''),
        'server_root_template'            => trim($_POST['server_root_template'] ?? ''),
        'install_path_template'           => trim($_POST['install_path_template'] ?? ''),
        'folder_naming_format'            => trim($_POST['folder_naming_format'] ?? ''),
        'mod_launch_param_template'       => trim($_POST['mod_launch_param_template'] ?? '-mod='),
        'servermod_launch_param_template' => trim($_POST['servermod_launch_param_template'] ?? '-serverMod='),
        'install_script_template'         => trim($_POST['install_script_template'] ?? ''),
        'update_script_template'          => trim($_POST['update_script_template'] ?? ''),
        'copy_bikeys_enabled'             => isset($_POST['copy_bikeys_enabled']) ? 1 : 0,
        'notes'                           => trim($_POST['notes'] ?? ''),
    );

    // Per-profile default behavior fields
    $valid_update_modes      = array('manual', 'on_restart', 'before_start');
    $valid_restart_behaviors = array('none', 'if_stopped');
    $posted_um  = $_POST['default_update_mode']      ?? 'manual';
    $posted_rb  = $_POST['default_restart_behavior'] ?? 'none';
    $fields['default_update_mode']      = in_array($posted_um, $valid_update_modes,      true) ? $posted_um : 'manual';
    $fields['default_restart_behavior'] = in_array($posted_rb, $valid_restart_behaviors, true) ? $posted_rb : 'none';

    $setParts = array();
    foreach ($fields as $col => $val) {
        $setParts[] = "`$col` = '" . $db->realEscapeSingle($val) . "'";
    }
    $setParts[] = "`updated_at` = NOW()";

    $ok = $db->query(
        "UPDATE " . sw_table('steam_workshop_game_profiles') . "
            SET " . implode(', ', $setParts) . "
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
<div class="sw-admin-panel">
  <p class="sw-muted">
    Profiles map game XML configs to Steam Workshop defaults. Sync to create missing profiles, then edit each profile for game-specific paths and launch templates.
  </p>

  <form method="post" style="margin-bottom:12px;">
    <button type="submit" name="sync_profiles" value="1" class="button"
            onclick="return confirm('Sync workshop profiles from all game config XMLs?');">Sync Profiles from XML Configs</button>
  </form>

  <?php if (empty($profiles)): ?>
    <p>No profiles yet. Click <em>Sync Profiles</em> to create them from installed game configs.</p>
  <?php else: ?>
    <table class="sw-admin-table" width="100%">
      <thead>
        <tr>
          <th>Config Name</th>
          <th>Game Name</th>
          <th style="text-align:center;">Steam App ID</th>
          <th style="text-align:center;">Workshop App ID</th>
          <th style="text-align:center;">Enabled</th>
          <th style="text-align:center;">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($profiles as $p): ?>
        <tr>
          <td><code><?= sw_h($p['config_name']) ?></code></td>
          <td><?= sw_h($p['game_name']) ?></td>
          <td style="text-align:center;"><?= sw_h($p['steam_app_id']) ?></td>
          <td style="text-align:center;"><?= sw_h($p['workshop_app_id']) ?></td>
          <td style="text-align:center;"><?= $p['enabled'] ? '<span class="sw-state-on">Yes</span>' : '<span class="sw-state-off">No</span>' ?></td>
          <td style="text-align:center;"><a class="button small" href="home.php?m=steam_workshop&p=admin&action=edit&id=<?= (int)$p['id'] ?>">Edit</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php
}

function sw_admin_edit_form(array $profile, array $detected = array(), $showDetectedBox = false)
{
    $id = (int)$profile['id'];
    ?>
<p><a href="home.php?m=steam_workshop&p=admin">&laquo; Back to profile list</a></p>
<h3>Edit Profile: <?= sw_h($profile['config_name']) ?> &ndash; <?= sw_h($profile['game_name']) ?></h3>

<div class="sw-admin-panel">
  <div class="sw-note">
    <strong>Placeholder tokens:</strong>
    <code>{SERVER_ROOT}</code> <code>{HOME_ID}</code> <code>{STEAM_APP_ID}</code> <code>{WORKSHOP_APP_ID}</code> <code>{MOD_FOLDER}</code>
  </div>

  <div class="sw-section">
    <h4>XML-Assisted Defaults</h4>
    <p class="sw-muted">Use values detected from this game XML. Existing values are not overwritten unless you explicitly allow it.</p>
    <form method="post" action="home.php?m=steam_workshop&p=admin&action=edit&id=<?= $id ?>" style="display:inline-block; margin-right:8px;">
      <input type="hidden" name="id" value="<?= $id ?>">
      <button type="submit" name="detect_defaults" value="1" class="button">Detect from XML</button>
    </form>

    <?php if ($showDetectedBox && !empty($detected)): ?>
      <div class="sw-detected-box">
        <strong>Detected values:</strong>
        <ul>
          <li>Steam App ID: <code><?= sw_h($detected['steam_app_id'] ?? '') ?></code></li>
          <li>Workshop App ID: <code><?= sw_h($detected['workshop_app_id'] ?? '') ?></code></li>
          <li>SteamCMD path: <code><?= sw_h($detected['steamcmd_path'] ?? '') ?></code></li>
          <li>Workshop download dir: <code><?= sw_h($detected['workshop_download_dir_template'] ?? '') ?></code></li>
          <li>Server root: <code><?= sw_h($detected['server_root_template'] ?? '') ?></code></li>
          <li>Mod install path: <code><?= sw_h($detected['install_path_template'] ?? '') ?></code></li>
        </ul>
        <form method="post" action="home.php?m=steam_workshop&p=admin&action=edit&id=<?= $id ?>">
          <input type="hidden" name="id" value="<?= $id ?>">
          <label style="display:block;margin-bottom:8px;">
            <input type="checkbox" name="overwrite_existing" value="1">
            Allow overwrite of existing non-empty values.
          </label>
          <button type="submit" name="apply_detected_defaults" value="1" class="button">Refresh defaults from XML</button>
        </form>
      </div>
    <?php endif; ?>
  </div>

  <form method="post" action="home.php?m=steam_workshop&p=admin&action=edit&id=<?= $id ?>">
    <input type="hidden" name="id" value="<?= $id ?>">

    <div class="sw-section">
      <h4>Global Profile Defaults</h4>
      <div class="sw-grid">
        <label><span>Enabled</span><input type="checkbox" name="enabled" value="1" <?= $profile['enabled'] ? 'checked' : '' ?>></label>
        <label><span>Steam App ID</span><input type="text" name="steam_app_id" value="<?= sw_h($profile['steam_app_id']) ?>" placeholder="Detected from XML when available"></label>
        <label><span>Workshop App ID</span><input type="text" name="workshop_app_id" value="<?= sw_h($profile['workshop_app_id']) ?>" placeholder="Detected from XML when available"></label>
        <label><span>SteamCMD Path</span><input type="text" name="steamcmd_path" value="<?= sw_h($profile['steamcmd_path']) ?>" placeholder="/home/gameserver/steamcmd/steamcmd.sh"></label>
        <label><span>Steam Login Required</span><input type="checkbox" name="steam_login_required" value="1" <?= $profile['steam_login_required'] ? 'checked' : '' ?>></label>
        <label><span>SteamCMD Login Mode</span>
          <select name="steamcmd_login_mode">
            <option value="anonymous" <?= $profile['steamcmd_login_mode'] === 'anonymous' ? 'selected' : '' ?>>anonymous</option>
            <option value="account" <?= $profile['steamcmd_login_mode'] === 'account' ? 'selected' : '' ?>>account</option>
          </select>
        </label>
      </div>
    </div>

    <div class="sw-section">
      <h4>Path Templates</h4>
      <p class="sw-muted">Use placeholders so paths stay portable between server homes.</p>
      <div class="sw-grid">
        <label><span>Workshop Download Directory</span><input type="text" name="workshop_download_dir_template" value="<?= sw_h($profile['workshop_download_dir_template']) ?>" placeholder="{SERVER_ROOT}/steamapps/workshop/content/{WORKSHOP_APP_ID}"></label>
        <label><span>Server Root</span><input type="text" name="server_root_template" value="<?= sw_h($profile['server_root_template']) ?>" placeholder="{SERVER_ROOT}"></label>
        <label><span>Mod Install Path</span><input type="text" name="install_path_template" value="<?= sw_h($profile['install_path_template']) ?>" placeholder="{SERVER_ROOT}/{MOD_FOLDER}"></label>
      </div>
    </div>

    <div class="sw-section">
      <h4>Per-Game Runtime Values</h4>
      <div class="sw-grid">
        <label><span>Folder Naming Format</span><input type="text" name="folder_naming_format" value="<?= sw_h($profile['folder_naming_format']) ?>" placeholder="@{MOD_NAME}"></label>
        <label><span>Client Launch Param</span><input type="text" name="mod_launch_param_template" value="<?= sw_h($profile['mod_launch_param_template']) ?>" placeholder="-mod="></label>
        <label><span>Server Launch Param</span><input type="text" name="servermod_launch_param_template" value="<?= sw_h($profile['servermod_launch_param_template']) ?>" placeholder="-serverMod="></label>
        <label><span>Copy .bikey files</span><input type="checkbox" name="copy_bikeys_enabled" value="1" <?= $profile['copy_bikeys_enabled'] ? 'checked' : '' ?>></label>
      </div>
    </div>

    <div class="sw-section">
      <h4>Optional Script Templates</h4>
      <label><span>Install Script Template</span><textarea name="install_script_template" rows="6"><?= sw_h($profile['install_script_template']) ?></textarea></label>
      <label><span>Update Script Template</span><textarea name="update_script_template" rows="6"><?= sw_h($profile['update_script_template']) ?></textarea></label>
    </div>

    <div class="sw-section">
      <h4>Notes</h4>
      <label><textarea name="notes" rows="4"><?= sw_h($profile['notes']) ?></textarea></label>
    </div>

    <div class="sw-section">
      <h4>Default Workshop Behavior for New Servers</h4>
      <p class="sw-muted">
        These defaults are applied when a user enables Workshop on a server that has no saved behavior settings yet.
        Users can always override them on their own server pages.
        All defaults are intentionally set to the safest option (manual / no automatic restart).
      </p>
      <div class="sw-grid">
        <label>
          <span>Default Install / Update Mode</span>
          <select name="default_update_mode">
            <option value="manual"       <?= (($profile['default_update_mode'] ?? 'manual') === 'manual')       ? 'selected' : '' ?>>Manual only (safe default)</option>
            <option value="on_restart"   <?= (($profile['default_update_mode'] ?? 'manual') === 'on_restart')   ? 'selected' : '' ?>>On next restart</option>
            <option value="before_start" <?= (($profile['default_update_mode'] ?? 'manual') === 'before_start') ? 'selected' : '' ?>>Before every server start</option>
          </select>
        </label>
        <label>
          <span>Default Restart Behavior</span>
          <select name="default_restart_behavior">
            <option value="none"         <?= (($profile['default_restart_behavior'] ?? 'none') === 'none')         ? 'selected' : '' ?>>Do not restart automatically (safe default)</option>
            <option value="if_stopped"   <?= (($profile['default_restart_behavior'] ?? 'none') === 'if_stopped')   ? 'selected' : '' ?>>Restart only if server is stopped</option>
          </select>
        </label>
      </div>
    </div>

    <p>
      <button type="submit" name="save_profile" value="1" class="button">Save Profile</button>
      <a href="home.php?m=steam_workshop&p=admin" class="button">Cancel</a>
    </p>
  </form>
</div>
<?php
}

function sw_admin_print_styles()
{
    static $printed = false;
    if ($printed) {
        return;
    }
    $printed = true;
    echo '<style>
    .sw-admin-panel{background:#171717;border:1px solid #2d2d2d;border-radius:6px;padding:14px;margin:10px 0;color:#e7e7e7}
    .sw-admin-table{border-collapse:collapse;background:#121212}
    .sw-admin-table th,.sw-admin-table td{border:1px solid #2c2c2c;padding:8px}
    .sw-admin-table thead th{background:#232323;color:#fff}
    .sw-state-on{color:#78d978;font-weight:700}
    .sw-state-off{color:#9a9a9a}
    .sw-section{margin-top:14px;padding:12px;border:1px solid #2f2f2f;border-radius:4px;background:#111}
    .sw-section h4{margin:0 0 8px 0;color:#f6f6f6}
    .sw-note{margin-bottom:10px;background:#202020;border-left:3px solid #3f80d0;padding:10px}
    .sw-muted{color:#b3b3b3}
    .sw-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:10px}
    .sw-grid label, .sw-section > label{display:block}
    .sw-grid span, .sw-section span{display:block;font-size:12px;color:#bdbdbd;margin-bottom:4px}
    .sw-grid input[type=text], .sw-grid select, .sw-section textarea{width:100%;box-sizing:border-box;background:#0d0d0d;border:1px solid #3a3a3a;color:#eee;padding:7px;border-radius:4px}
    .sw-grid input[type=checkbox]{transform:scale(1.1);margin-top:4px}
    .sw-detected-box{margin-top:10px;padding:10px;background:#1d2a1d;border:1px solid #335933;border-radius:4px}
    .sw-detected-box ul{margin:8px 0 10px 18px}
    .sw-detected-box code,.sw-note code{background:#0b0b0b;padding:1px 4px;border-radius:3px;color:#9fd4ff}
    </style>';
}
