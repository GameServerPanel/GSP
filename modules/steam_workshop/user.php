<?php
/*
 * GSP – Steam Workshop: User mod management
 * Copyright (C) 2025 WDS / GameServerPanel
 *
 * Accessible via: home.php?m=steam_workshop&p=user&home_id=123
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 */

require_once __DIR__ . '/includes/functions.php';

function exec_ogp_module()
{
    global $db;

    echo '<h2>Steam Workshop – Mod Manager</h2>';

    $home_id = isset($_REQUEST['home_id']) ? (int)$_REQUEST['home_id'] : 0;

    if (!$home_id) {
        sw_error('No server selected. Please access this page from your game server manager.');
        return;
    }

    // Ownership check
    if (!sw_user_owns_home($db, (int)$_SESSION['user_id'], $home_id)) {
        sw_error('Access denied. You do not own this server.');
        return;
    }

    // Load server info
    $home = sw_get_home_info($db, $home_id);
    if (!$home) {
        sw_error('Server not found.');
        return;
    }

    // Find matching Workshop profile
    $profile = sw_get_profile_for_home($db, $home_id);
    if (!$profile) {
        echo '<p>Steam Workshop is not enabled for this game type (<strong>'
           . sw_h($home['game_name']) . '</strong>).</p>';
        echo '<p>An administrator must enable Workshop support for this game under '
           . '<em>Steam Workshop &rsaquo; Admin</em>.</p>';
        return;
    }

    $action = $_POST['action'] ?? ($_GET['action'] ?? '');

    // ── POST handlers ─────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        switch ($action) {
            case 'add_mod':
                sw_user_add_mod($db, $home_id, $profile);
                break;
            case 'save_mod':
                sw_user_save_mod($db, $home_id);
                break;
            case 'delete_mod':
                sw_user_delete_mod($db, $home_id);
                break;
            case 'toggle_mod':
                sw_user_toggle_mod($db, $home_id);
                break;
            case 'move_up':
            case 'move_down':
                sw_user_reorder_mod($db, $home_id, $action);
                break;
            case 'queue_update':
                sw_user_queue_update($db, $home_id);
                break;
            case 'save_settings':
                sw_user_save_settings($db, $home_id);
                break;
        }
    }

    // ── Render page ───────────────────────────────────────────────────
    sw_user_render($db, $home_id, $home, $profile);
}

// ─────────────────────────────────────────────────────────────────────────
// POST action handlers
// ─────────────────────────────────────────────────────────────────────────

function sw_user_add_mod($db, $home_id, array $profile)
{
    $workshop_id = trim($_POST['workshop_id'] ?? '');
    if (!preg_match('/^\d{1,20}$/', $workshop_id)) {
        sw_error('Invalid Workshop ID – must be a numeric Steam Workshop item ID.');
        return;
    }

    // Prevent duplicates
    $safe_wid = $db->realEscapeSingle($workshop_id);
    $exists   = $db->resultQuery(
        "SELECT id FROM " . sw_table('steam_workshop_server_mods') . "
          WHERE `home_id` = $home_id AND `workshop_id` = '$safe_wid' LIMIT 1"
    );
    if ($exists) {
        sw_error("Workshop ID $workshop_id is already in the list.");
        return;
    }

    // Determine next sort_order
    $last = $db->resultQuery(
        "SELECT MAX(`sort_order`) AS m FROM " . sw_table('steam_workshop_server_mods') . "
          WHERE `home_id` = $home_id"
    );
    $sort = ($last && isset($last[0]['m'])) ? ((int)$last[0]['m'] + 1) : 0;

    $mod_name  = $db->realEscapeSingle(trim($_POST['mod_name']   ?? ''));
    $mod_type  = (($_POST['mod_type'] ?? 'client') === 'server') ? 'server' : 'client';
    $profile_id = (int)$profile['id'];

    // Auto-generate folder name from naming format template
    $folder_name = sw_apply_template(
        $profile['folder_naming_format'],
        array(
            'MOD_NAME'       => !empty($mod_name) ? $mod_name : $workshop_id,
            'WORKSHOP_ID'    => $workshop_id,
            'WORKSHOP_APP_ID'=> $profile['workshop_app_id'],
        )
    );
    $safe_fname = $db->realEscapeSingle($folder_name);
    $safe_mname = $mod_name; // already escaped above via realEscapeSingle

    $ok = $db->query(
        "INSERT INTO " . sw_table('steam_workshop_server_mods') . "
           (`home_id`, `profile_id`, `workshop_id`, `mod_name`, `folder_name`,
             `mod_type`, `sort_order`, `enabled`, `install_status`, `created_at`)
         VALUES ($home_id, $profile_id, '$safe_wid', '$safe_mname', '$safe_fname',
                 '$mod_type', $sort, 1, '', NOW())"
    );

    if ($ok) {
        sw_success("Workshop mod $workshop_id added.");
    } else {
        sw_error('Failed to add mod.');
    }
}

function sw_user_save_mod($db, $home_id)
{
    $mod_id = (int)($_POST['mod_id'] ?? 0);
    if (!$mod_id) {
        return;
    }

    $mod = sw_get_mod_by_id($db, $mod_id);
    if (!$mod || (int)$mod['home_id'] !== $home_id) {
        sw_error('Mod not found or access denied.');
        return;
    }

    $mod_name    = $db->realEscapeSingle(trim($_POST['mod_name']    ?? ''));
    $folder_name = $db->realEscapeSingle(trim($_POST['folder_name'] ?? ''));
    $mod_type    = (($_POST['mod_type'] ?? 'client') === 'server') ? 'server' : 'client';

    if (empty($folder_name)) {
        sw_error('Folder name cannot be empty.');
        return;
    }

    $ok = $db->query(
        "UPDATE " . sw_table('steam_workshop_server_mods') . "
            SET `mod_name`    = '$mod_name',
                `folder_name` = '$folder_name',
                `mod_type`    = '$mod_type',
                `updated_at`  = NOW()
          WHERE `id` = $mod_id AND `home_id` = $home_id LIMIT 1"
    );

    if ($ok) {
        sw_success('Mod updated.');
    } else {
        sw_error('Failed to update mod.');
    }
}

function sw_user_delete_mod($db, $home_id)
{
    $mod_id = (int)($_POST['mod_id'] ?? 0);
    if (!$mod_id) {
        return;
    }

    $mod = sw_get_mod_by_id($db, $mod_id);
    if (!$mod || (int)$mod['home_id'] !== $home_id) {
        sw_error('Mod not found or access denied.');
        return;
    }

    $db->query(
        "DELETE FROM " . sw_table('steam_workshop_server_mods') . "
          WHERE `id` = $mod_id AND `home_id` = $home_id LIMIT 1"
    );
    sw_success('Mod removed from list.');
}

function sw_user_toggle_mod($db, $home_id)
{
    $mod_id = (int)($_POST['mod_id'] ?? 0);
    if (!$mod_id) {
        return;
    }

    $mod = sw_get_mod_by_id($db, $mod_id);
    if (!$mod || (int)$mod['home_id'] !== $home_id) {
        sw_error('Mod not found or access denied.');
        return;
    }

    $new_state = $mod['enabled'] ? 0 : 1;
    $db->query(
        "UPDATE " . sw_table('steam_workshop_server_mods') . "
            SET `enabled` = $new_state, `updated_at` = NOW()
          WHERE `id` = $mod_id AND `home_id` = $home_id LIMIT 1"
    );
}

function sw_user_reorder_mod($db, $home_id, $direction)
{
    $mod_id = (int)($_POST['mod_id'] ?? 0);
    if (!$mod_id) {
        return;
    }

    $mod = sw_get_mod_by_id($db, $mod_id);
    if (!$mod || (int)$mod['home_id'] !== $home_id) {
        return;
    }

    $mods = sw_get_server_mods($db, $home_id);
    if (!$mods) {
        return;
    }

    // Normalise sort_order to 0-based sequential integers
    $sorted = array_values($mods);
    foreach ($sorted as $idx => $m) {
        $db->query(
            "UPDATE " . sw_table('steam_workshop_server_mods') . "
                SET `sort_order` = $idx
              WHERE `id` = " . (int)$m['id'] . " AND `home_id` = $home_id LIMIT 1"
        );
    }

    // Find the position of the target mod
    $pos = -1;
    foreach ($sorted as $idx => $m) {
        if ((int)$m['id'] === $mod_id) {
            $pos = $idx;
            break;
        }
    }
    if ($pos < 0) {
        return;
    }

    if ($direction === 'move_up' && $pos > 0) {
        $swap_pos = $pos - 1;
    } elseif ($direction === 'move_down' && $pos < (count($sorted) - 1)) {
        $swap_pos = $pos + 1;
    } else {
        return; // already at boundary
    }

    $swap_id = (int)$sorted[$swap_pos]['id'];

    // Swap sort_order values
    $db->query(
        "UPDATE " . sw_table('steam_workshop_server_mods') . "
            SET `sort_order` = $swap_pos
          WHERE `id` = $mod_id AND `home_id` = $home_id LIMIT 1"
    );
    $db->query(
        "UPDATE " . sw_table('steam_workshop_server_mods') . "
            SET `sort_order` = $pos
          WHERE `id` = $swap_id AND `home_id` = $home_id LIMIT 1"
    );
}

function sw_user_queue_update($db, $home_id)
{
    // Mark all enabled mods as 'queued' so the agent picks them up.
    $db->query(
        "UPDATE " . sw_table('steam_workshop_server_mods') . "
            SET `install_status` = 'queued', `updated_at` = NOW()
          WHERE `home_id` = $home_id AND `enabled` = 1"
    );
    sw_success('All enabled mods queued for update. Run the agent to process downloads.');
}

function sw_user_save_settings($db, $home_id)
{
    $ok = sw_save_server_settings($db, $home_id, array(
        'update_mode'       => $_POST['update_mode']       ?? 'manual',
        'restart_behavior'  => $_POST['restart_behavior']  ?? 'none',
        'hot_load'          => $_POST['hot_load']          ?? 'disabled',
        'warning_minutes'   => $_POST['warning_minutes']   ?? 10,
        'schedule_interval' => $_POST['schedule_interval'] ?? 'daily',
    ));

    if ($ok) {
        sw_success('Workshop behavior settings saved.');
    } else {
        sw_error('Failed to save settings.');
    }
}

// ─────────────────────────────────────────────────────────────────────────
// Render
// ─────────────────────────────────────────────────────────────────────────

function sw_user_render($db, $home_id, array $home, array $profile)
{
    $mods = sw_get_server_mods($db, $home_id) ?: array();
    $settings = sw_get_server_settings($db, $home_id);

    // Generate launch params from enabled mods
    $enabled_mods = array_filter($mods, function ($m) {
        return !empty($m['enabled']);
    });
    $params = sw_generate_launch_params(array_values($enabled_mods), $profile);

    $base_url = 'home.php?m=steam_workshop&p=user&home_id=' . $home_id;
    ?>

<p>
  <strong>Server:</strong> <?= sw_h($home['home_name']) ?>
  &nbsp;&nbsp;
  <strong>Game:</strong> <?= sw_h($home['game_name']) ?>
  &nbsp;&nbsp;
  <strong>Workshop Profile:</strong> <?= sw_h($profile['config_name']) ?>
</p>

<!-- Add Mod form -->
<h3>Add Workshop Mod</h3>
<form method="post" action="<?= sw_h($base_url) ?>">
  <input type="hidden" name="action" value="add_mod">
  <table>
    <tr>
      <td style="padding:4px 8px;"><label for="workshop_id">Workshop ID</label></td>
      <td style="padding:4px 8px;">
        <input type="text" id="workshop_id" name="workshop_id" value=""
               placeholder="e.g. 2863534533" style="width:180px;" required>
      </td>
    </tr>
    <tr>
      <td style="padding:4px 8px;"><label for="add_mod_name">Display Name (optional)</label></td>
      <td style="padding:4px 8px;">
        <input type="text" id="add_mod_name" name="mod_name" value=""
               placeholder="e.g. CF" style="width:180px;">
      </td>
    </tr>
    <tr>
      <td style="padding:4px 8px;"><label for="add_mod_type">Mod Type</label></td>
      <td style="padding:4px 8px;">
        <select id="add_mod_type" name="mod_type">
          <option value="client">Client mod (-mod=)</option>
          <option value="server">Server-side only (-serverMod=)</option>
        </select>
      </td>
    </tr>
    <tr>
      <td></td>
      <td style="padding:4px 8px;">
        <button type="submit" class="button">Add Mod</button>
      </td>
    </tr>
  </table>
</form>

<hr>

<!-- Mod list -->
<h3>Installed Mods (<?= count($mods) ?>)</h3>

<?php if (empty($mods)): ?>
<p>No mods added yet. Use the form above to add Workshop IDs.</p>
<?php else: ?>
<form method="post" action="<?= sw_h($base_url) ?>">
  <table width="100%" style="border-collapse:collapse;">
    <thead>
      <tr style="background:#f0f0f0;">
        <th style="padding:6px 8px;text-align:center;">#</th>
        <th style="padding:6px 8px;text-align:left;">Workshop ID</th>
        <th style="padding:6px 8px;text-align:left;">Mod Name</th>
        <th style="padding:6px 8px;text-align:left;">Folder Name</th>
        <th style="padding:6px 8px;text-align:center;">Type</th>
        <th style="padding:6px 8px;text-align:center;">Enabled</th>
        <th style="padding:6px 8px;text-align:center;">Status</th>
        <th style="padding:6px 8px;text-align:center;">Order</th>
        <th style="padding:6px 8px;text-align:center;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($mods as $idx => $mod): ?>
      <tr style="border-bottom:1px solid #ddd;<?= !$mod['enabled'] ? 'opacity:0.55;' : '' ?>">
        <td style="padding:6px 8px;text-align:center;"><?= $idx + 1 ?></td>

        <td style="padding:6px 8px;font-family:monospace;"><?= sw_h($mod['workshop_id']) ?></td>

        <!-- Inline edit: name -->
        <td style="padding:6px 8px;">
          <form method="post" action="<?= sw_h($base_url) ?>" style="display:inline;">
            <input type="hidden" name="action"  value="save_mod">
            <input type="hidden" name="mod_id"  value="<?= (int)$mod['id'] ?>">
            <input type="hidden" name="folder_name" value="<?= sw_h($mod['folder_name']) ?>">
            <input type="hidden" name="mod_type"    value="<?= sw_h($mod['mod_type']) ?>">
            <input type="text" name="mod_name" value="<?= sw_h($mod['mod_name']) ?>"
                   style="width:120px;" title="Click Save to apply">
        </td>

        <!-- Inline edit: folder name -->
        <td style="padding:6px 8px;">
            <input type="text" name="folder_name" value="<?= sw_h($mod['folder_name']) ?>"
                   style="width:140px;" title="Folder name inside server root">
        </td>

        <!-- Inline edit: mod type -->
        <td style="padding:6px 8px;text-align:center;">
            <select name="mod_type" style="width:100px;">
              <option value="client" <?= $mod['mod_type'] === 'client' ? 'selected' : '' ?>>-mod=</option>
              <option value="server" <?= $mod['mod_type'] === 'server' ? 'selected' : '' ?>>-serverMod=</option>
            </select>
            <button type="submit" class="button small" title="Save changes">Save</button>
          </form>
        </td>

        <!-- Toggle enabled -->
        <td style="padding:6px 8px;text-align:center;">
          <form method="post" action="<?= sw_h($base_url) ?>" style="display:inline;">
            <input type="hidden" name="action" value="toggle_mod">
            <input type="hidden" name="mod_id" value="<?= (int)$mod['id'] ?>">
            <button type="submit" class="button small"
                    style="<?= $mod['enabled'] ? 'background:#5cb85c;color:#fff;' : '' ?>"
                    title="<?= $mod['enabled'] ? 'Click to disable' : 'Click to enable' ?>">
              <?= $mod['enabled'] ? 'On' : 'Off' ?>
            </button>
          </form>
        </td>

        <!-- Install status -->
        <td style="padding:6px 8px;text-align:center;font-size:0.85em;">
          <?php
          $s = $mod['install_status'];
          if ($s === 'installed') {
              echo '<span style="color:green;">Installed</span>';
          } elseif ($s === 'queued') {
              echo '<span style="color:orange;">Queued</span>';
          } elseif ($s === 'failed') {
              echo '<span style="color:red;" title="' . sw_h($mod['last_error']) . '">Failed</span>';
          } elseif ($s === 'updating') {
              echo '<span style="color:blue;">Updating</span>';
          } else {
              echo '<span style="color:#999;">Not installed</span>';
          }
          ?>
        </td>

        <!-- Order buttons -->
        <td style="padding:6px 8px;text-align:center;white-space:nowrap;">
          <form method="post" action="<?= sw_h($base_url) ?>" style="display:inline;">
            <input type="hidden" name="action" value="move_up">
            <input type="hidden" name="mod_id" value="<?= (int)$mod['id'] ?>">
            <button type="submit" class="button small" <?= $idx === 0 ? 'disabled' : '' ?>>&#9650;</button>
          </form>
          <form method="post" action="<?= sw_h($base_url) ?>" style="display:inline;">
            <input type="hidden" name="action" value="move_down">
            <input type="hidden" name="mod_id" value="<?= (int)$mod['id'] ?>">
            <button type="submit" class="button small"
                    <?= $idx === (count($mods) - 1) ? 'disabled' : '' ?>>&#9660;</button>
          </form>
        </td>

        <!-- Delete -->
        <td style="padding:6px 8px;text-align:center;">
          <form method="post" action="<?= sw_h($base_url) ?>" style="display:inline;">
            <input type="hidden" name="action" value="delete_mod">
            <input type="hidden" name="mod_id" value="<?= (int)$mod['id'] ?>">
            <button type="submit" class="button small danger"
                    onclick="return confirm('Remove this mod from the list?');"
                    style="background:#d9534f;color:#fff;">Remove</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</form>
<?php endif; ?>

<hr>

<!-- Launch params display -->
<h3>Generated Launch Parameters</h3>
<p style="color:#666;font-size:0.9em;">
  Based on the enabled mods above (sorted by order). Copy these into your server startup command.
</p>

<?php if (empty($enabled_mods)): ?>
<p>No enabled mods – launch parameters will be empty.</p>
<?php else: ?>
<?php if ($params['mod']): ?>
<p>
  <strong>Client mods (<code>-mod=</code>):</strong><br>
  <input type="text" value="<?= sw_h($params['mod']) ?>"
         readonly style="width:100%;font-family:monospace;"
         onclick="this.select();">
</p>
<?php endif; ?>
<?php if ($params['servermod']): ?>
<p>
  <strong>Server-side mods (<code>-serverMod=</code>):</strong><br>
  <input type="text" value="<?= sw_h($params['servermod']) ?>"
         readonly style="width:100%;font-family:monospace;"
         onclick="this.select();">
</p>
<?php endif; ?>
<p>
  <strong>Combined:</strong><br>
  <input type="text" value="<?= sw_h($params['combined']) ?>"
         readonly style="width:100%;font-family:monospace;"
         onclick="this.select();">
</p>
<?php endif; ?>

<hr>

<!-- Install/Update -->
<h3>Install / Update Mods</h3>
<p>
  Clicking <strong>Queue Update</strong> marks all enabled mods as <em>queued</em>.
  Then run the agent <strong>on the game server host</strong> (where SteamCMD and the game files are located)
  to download and install the mods. Adjust the path to the panel's
  <code>modules/steam_workshop/agent_update_workshop.php</code> for your server:
</p>
<pre style="background:#222;color:#eee;padding:10px 14px;border-radius:4px;overflow-x:auto;"
>php /path/to/panel/modules/steam_workshop/agent_update_workshop.php --home-id=<?= $home_id ?></pre>

<form method="post" action="<?= sw_h($base_url) ?>">
  <input type="hidden" name="action" value="queue_update">
  <button type="submit" class="button"
          onclick="return confirm('Queue all enabled mods for update?');">
    Queue Update for All Enabled Mods
  </button>
</form>

<hr>

<!-- Workshop Behavior Settings -->
<h3>Workshop Behavior Settings</h3>
<p style="color:#888;font-size:0.9em;">
  Configure how Workshop mods are installed and updated for this server.
  All options default to the safest setting (manual only, no automatic restarts).
</p>

<form method="post" action="<?= sw_h($base_url) ?>">
  <input type="hidden" name="action" value="save_settings">

  <table style="border-collapse:collapse;width:100%;max-width:720px;">
    <colgroup>
      <col style="width:220px;">
      <col>
      <col style="width:340px;">
    </colgroup>
    <thead>
      <tr style="background:#f0f0f0;">
        <th style="padding:6px 8px;text-align:left;">Setting</th>
        <th style="padding:6px 8px;text-align:left;">Value</th>
        <th style="padding:6px 8px;text-align:left;">Help</th>
      </tr>
    </thead>
    <tbody>

      <tr style="border-bottom:1px solid #ddd;">
        <td style="padding:8px;font-weight:bold;">Install / Update Mode</td>
        <td style="padding:8px;">
          <select name="update_mode" style="width:100%;">
            <option value="manual"      <?= ($settings['update_mode'] === 'manual')       ? 'selected' : '' ?>>Manual only</option>
            <option value="on_restart"  <?= ($settings['update_mode'] === 'on_restart')   ? 'selected' : '' ?>>On next server restart</option>
            <option value="before_start"<?= ($settings['update_mode'] === 'before_start') ? 'selected' : '' ?>>Before every server start</option>
            <option value="scheduled"   <?= ($settings['update_mode'] === 'scheduled')    ? 'selected' : '' ?>>Scheduled update check</option>
          </select>
        </td>
        <td style="padding:8px;font-size:0.85em;color:#555;">
          <strong>Manual only</strong> – mods are only updated when you click &ldquo;Queue Update&rdquo; above.<br>
          <strong>On next restart</strong> – queued updates are applied the next time the server restarts.<br>
          <strong>Before every start</strong> – the update check runs automatically each time the server starts.<br>
          <strong>Scheduled</strong> – the update check runs on the interval set below (requires cron / agent).
        </td>
      </tr>

      <tr style="border-bottom:1px solid #ddd;">
        <td style="padding:8px;font-weight:bold;">Restart Behavior</td>
        <td style="padding:8px;">
          <select name="restart_behavior" style="width:100%;">
            <option value="none"         <?= ($settings['restart_behavior'] === 'none')         ? 'selected' : '' ?>>Do not restart automatically</option>
            <option value="if_empty"     <?= ($settings['restart_behavior'] === 'if_empty')     ? 'selected' : '' ?>>Restart only if server is empty</option>
            <option value="immediate"    <?= ($settings['restart_behavior'] === 'immediate')    ? 'selected' : '' ?>>Restart immediately after warning</option>
            <option value="next_restart" <?= ($settings['restart_behavior'] === 'next_restart') ? 'selected' : '' ?>>Install on next manual restart only</option>
          </select>
        </td>
        <td style="padding:8px;font-size:0.85em;color:#555;">
          Controls what happens when new mod updates are found.<br>
          <strong>Do not restart</strong> – updates are staged but the server keeps running (safe default).<br>
          <strong>If empty</strong> – the server is restarted only when there are zero players connected.<br>
          <strong>Immediate with warning</strong> – a countdown warning is broadcast, then the server restarts.<br>
          <strong>Next manual restart</strong> – updates are installed the next time you manually stop/start the server.
        </td>
      </tr>

      <tr style="border-bottom:1px solid #ddd;">
        <td style="padding:8px;font-weight:bold;">Hot-Load</td>
        <td style="padding:8px;">
          <select name="hot_load" style="width:100%;">
            <option value="disabled" <?= ($settings['hot_load'] === 'disabled') ? 'selected' : '' ?>>Disabled</option>
            <option value="attempt"  <?= ($settings['hot_load'] === 'attempt')  ? 'selected' : '' ?>>Attempt hot-load if game supports it</option>
          </select>
        </td>
        <td style="padding:8px;font-size:0.85em;color:#555;">
          <strong>Disabled</strong> – no hot-loading; mod changes take effect only after a server restart (safe default).<br>
          <strong>Attempt</strong> – if the game supports live mod reloading (e.g. via RCON), try to hot-load instead of restarting.
        </td>
      </tr>

      <tr style="border-bottom:1px solid #ddd;">
        <td style="padding:8px;font-weight:bold;">Warning Countdown</td>
        <td style="padding:8px;">
          <input type="number" name="warning_minutes" min="1" max="120"
                 value="<?= (int)$settings['warning_minutes'] ?>"
                 style="width:80px;"> minutes
        </td>
        <td style="padding:8px;font-size:0.85em;color:#555;">
          Minutes of advance warning broadcast to players before an automatic restart.<br>
          Only used when <em>Restart Behavior</em> is set to <strong>Restart immediately after warning</strong>.<br>
          Default: 10 minutes.
        </td>
      </tr>

      <tr style="border-bottom:1px solid #ddd;">
        <td style="padding:8px;font-weight:bold;">Scheduled Check Interval</td>
        <td style="padding:8px;">
          <select name="schedule_interval" style="width:100%;">
            <option value="hourly" <?= ($settings['schedule_interval'] === 'hourly') ? 'selected' : '' ?>>Hourly</option>
            <option value="daily"  <?= ($settings['schedule_interval'] === 'daily')  ? 'selected' : '' ?>>Daily (default)</option>
            <option value="weekly" <?= ($settings['schedule_interval'] === 'weekly') ? 'selected' : '' ?>>Weekly</option>
          </select>
        </td>
        <td style="padding:8px;font-size:0.85em;color:#555;">
          How often the scheduled update check runs.<br>
          Only used when <em>Install / Update Mode</em> is set to <strong>Scheduled update check</strong>.<br>
          Requires the Workshop agent to be running via cron on the game server host.
        </td>
      </tr>

    </tbody>
  </table>

  <p style="margin-top:12px;">
    <button type="submit" class="button">Save Behavior Settings</button>
  </p>
</form>

<?php
}
