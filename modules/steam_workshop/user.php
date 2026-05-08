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
        echo '<p>Steam Workshop is not enabled for this game.</p>';
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
    sw_success('All enabled mods were queued. Updates are processed automatically by the server agent.');
}

function sw_user_save_settings($db, $home_id)
{
    $ok = sw_save_server_settings($db, $home_id, array(
        'update_mode'       => $_POST['update_mode']       ?? 'manual',
        'restart_behavior'  => $_POST['restart_behavior']  ?? 'none',
        'schedule_interval' => $_POST['schedule_interval'] ?? 'disabled',
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
    $queuedCount = 0;
    $failedCount = 0;
    $installedCount = 0;
    $latestUpdateAt = '';
    $latestError = '';
    foreach ($mods as $mod) {
        if (($mod['install_status'] ?? '') === 'queued') {
            $queuedCount++;
        } elseif (($mod['install_status'] ?? '') === 'failed') {
            $failedCount++;
        } elseif (($mod['install_status'] ?? '') === 'installed') {
            $installedCount++;
        }
        if (!empty($mod['last_updated_at']) && $mod['last_updated_at'] > $latestUpdateAt) {
            $latestUpdateAt = $mod['last_updated_at'];
        }
        if (($mod['install_status'] ?? '') === 'failed' && !empty($mod['last_error']) && $latestError === '') {
            $latestError = $mod['last_error'];
        }
    }

    $base_url = 'home.php?m=steam_workshop&p=user&home_id=' . $home_id;
    ?>
<style>
.sw-user-panel{background:#161616;border:1px solid #2f2f2f;border-radius:6px;padding:14px;margin:10px 0;color:#ececec}
.sw-user-panel h3{margin:0 0 10px 0;color:#fff}
.sw-user-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px}
.sw-user-grid label{display:block}
.sw-user-grid span{display:block;font-size:12px;color:#bebebe;margin-bottom:4px}
.sw-user-grid input[type=text],.sw-user-grid select{width:100%;box-sizing:border-box;background:#0d0d0d;border:1px solid #3a3a3a;color:#f0f0f0;padding:7px;border-radius:4px}
.sw-user-table-wrap{overflow-x:auto}
.sw-user-table{width:100%;border-collapse:collapse;min-width:860px}
.sw-user-table th,.sw-user-table td{border:1px solid #353535;padding:8px;vertical-align:middle}
.sw-user-table th{background:#202020;text-align:left;color:#fff}
.sw-status-ok{color:#7cdc7c;font-weight:700}
.sw-status-queued{color:#ffca63;font-weight:700}
.sw-status-failed{color:#ff8484;font-weight:700}
.sw-status-progress{color:#8cc7ff;font-weight:700}
.sw-muted{color:#b4b4b4}
</style>

<div class="sw-user-panel">
  <p>
    <strong>Server:</strong> <?= sw_h($home['home_name']) ?>
    &nbsp;&nbsp;<strong>Game:</strong> <?= sw_h($home['game_name']) ?>
    &nbsp;&nbsp;<strong>Workshop Profile:</strong> <?= sw_h($profile['config_name']) ?>
  </p>
  <p class="sw-muted" style="margin-bottom:0;">
    Queue updates from this page. The server agent applies queued updates automatically.
  </p>
</div>

<div class="sw-user-panel">
  <h3>Add Workshop Mod</h3>
  <form method="post" action="<?= sw_h($base_url) ?>">
    <input type="hidden" name="action" value="add_mod">
    <div class="sw-user-grid">
      <label>
        <span>Workshop ID</span>
        <input type="text" id="workshop_id" name="workshop_id" placeholder="e.g. 2863534533" required>
      </label>
      <label>
        <span>Display Name (optional)</span>
        <input type="text" id="add_mod_name" name="mod_name" placeholder="e.g. CF">
      </label>
      <label>
        <span>Mod Type</span>
        <select id="add_mod_type" name="mod_type">
          <option value="client">Client mod</option>
          <option value="server">Server-side only</option>
        </select>
      </label>
    </div>
    <p style="margin:12px 0 0;">
      <button type="submit" class="button">Add Mod</button>
    </p>
  </form>
</div>

<div class="sw-user-panel">
  <h3>Update Queue &amp; Last Result</h3>
  <p>
    <strong>Enabled mods:</strong> <?= count(array_filter($mods, function ($m) { return !empty($m['enabled']); })) ?>
    &nbsp;&nbsp;<strong>Queued:</strong> <?= $queuedCount ?>
    &nbsp;&nbsp;<strong>Installed:</strong> <?= $installedCount ?>
    &nbsp;&nbsp;<strong>Failed:</strong> <?= $failedCount ?>
  </p>
  <p>
    <strong>Last update time:</strong> <?= $latestUpdateAt ? sw_h($latestUpdateAt) : 'Never' ?>
  </p>
  <?php if ($latestError !== ''): ?>
    <p><strong>Last error:</strong> <?= sw_h($latestError) ?></p>
  <?php endif; ?>
  <form method="post" action="<?= sw_h($base_url) ?>">
    <input type="hidden" name="action" value="queue_update">
    <button type="submit" class="button" onclick="return confirm('Queue all enabled mods for update?');">Queue Update for All Enabled Mods</button>
  </form>
</div>

<div class="sw-user-panel">
  <h3>Workshop Behavior Settings</h3>
  <form method="post" action="<?= sw_h($base_url) ?>">
    <input type="hidden" name="action" value="save_settings">
    <div class="sw-user-grid">
      <label>
        <span>Update Mode</span>
        <select name="update_mode">
          <option value="manual" <?= ($settings['update_mode'] === 'manual') ? 'selected' : '' ?>>Manual only</option>
          <option value="on_restart" <?= ($settings['update_mode'] === 'on_restart') ? 'selected' : '' ?>>On next restart</option>
          <option value="before_start" <?= ($settings['update_mode'] === 'before_start') ? 'selected' : '' ?>>Before server start</option>
        </select>
      </label>
      <label>
        <span>Restart Behavior</span>
        <select name="restart_behavior">
          <option value="none" <?= ($settings['restart_behavior'] === 'none') ? 'selected' : '' ?>>Never restart automatically</option>
          <option value="if_stopped" <?= ($settings['restart_behavior'] === 'if_stopped') ? 'selected' : '' ?>>Restart only if server is stopped</option>
        </select>
      </label>
      <label>
        <span>Scheduled Checks</span>
        <select name="schedule_interval">
          <option value="disabled" <?= ($settings['schedule_interval'] === 'disabled') ? 'selected' : '' ?>>Disabled</option>
          <option value="daily" <?= ($settings['schedule_interval'] === 'daily') ? 'selected' : '' ?>>Daily</option>
          <option value="weekly" <?= ($settings['schedule_interval'] === 'weekly') ? 'selected' : '' ?>>Weekly</option>
        </select>
      </label>
    </div>
    <p style="margin:12px 0 0;">
      <button type="submit" class="button">Save Behavior Settings</button>
    </p>
  </form>
</div>

<div class="sw-user-panel">
  <h3>Installed Mods (<?= count($mods) ?>)</h3>
  <?php if (empty($mods)): ?>
    <p>No mods added yet. Use the form above to add Workshop IDs.</p>
  <?php else: ?>
    <div class="sw-user-table-wrap">
      <table class="sw-user-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Workshop ID</th>
            <th>Mod Name</th>
            <th>Folder Name</th>
            <th>Type</th>
            <th>Enabled</th>
            <th>Status</th>
            <th>Last Update</th>
            <th>Last Error</th>
            <th>Order</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($mods as $idx => $mod): ?>
          <tr style="<?= !$mod['enabled'] ? 'opacity:0.55;' : '' ?>">
            <td><?= $idx + 1 ?></td>
            <td style="font-family:monospace;"><?= sw_h($mod['workshop_id']) ?></td>
            <td>
              <form method="post" action="<?= sw_h($base_url) ?>" style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                <input type="hidden" name="action" value="save_mod">
                <input type="hidden" name="mod_id" value="<?= (int)$mod['id'] ?>">
                <input type="text" name="mod_name" value="<?= sw_h($mod['mod_name']) ?>" style="width:120px;">
            </td>
            <td><input type="text" name="folder_name" value="<?= sw_h($mod['folder_name']) ?>" style="width:120px;"></td>
            <td>
                <select name="mod_type" style="width:120px;">
                  <option value="client" <?= $mod['mod_type'] === 'client' ? 'selected' : '' ?>>Client</option>
                  <option value="server" <?= $mod['mod_type'] === 'server' ? 'selected' : '' ?>>Server</option>
                </select>
                <button type="submit" class="button small">Save</button>
              </form>
            </td>
            <td>
              <form method="post" action="<?= sw_h($base_url) ?>" style="display:inline;">
                <input type="hidden" name="action" value="toggle_mod">
                <input type="hidden" name="mod_id" value="<?= (int)$mod['id'] ?>">
                <button type="submit" class="button small" style="<?= $mod['enabled'] ? 'background:#5cb85c;color:#fff;' : '' ?>"><?= $mod['enabled'] ? 'On' : 'Off' ?></button>
              </form>
            </td>
            <td>
              <?php
              $s = $mod['install_status'];
              if ($s === 'installed') {
                  echo '<span class="sw-status-ok">Installed</span>';
              } elseif ($s === 'queued') {
                  echo '<span class="sw-status-queued">Queued</span>';
              } elseif ($s === 'failed') {
                  echo '<span class="sw-status-failed">Failed</span>';
              } elseif ($s === 'updating') {
                  echo '<span class="sw-status-progress">Updating</span>';
              } else {
                  echo '<span class="sw-muted">Not installed</span>';
              }
              ?>
            </td>
            <td><?= !empty($mod['last_updated_at']) ? sw_h($mod['last_updated_at']) : '-' ?></td>
            <?php $shortError = !empty($mod['last_error']) ? (strlen($mod['last_error']) > 70 ? (substr($mod['last_error'], 0, 67) . '...') : $mod['last_error']) : ''; ?>
            <td title="<?= sw_h($mod['last_error'] ?? '') ?>"><?= $shortError !== '' ? sw_h($shortError) : '-' ?></td>
            <td style="white-space:nowrap;">
              <form method="post" action="<?= sw_h($base_url) ?>" style="display:inline;">
                <input type="hidden" name="action" value="move_up">
                <input type="hidden" name="mod_id" value="<?= (int)$mod['id'] ?>">
                <button type="submit" class="button small" <?= $idx === 0 ? 'disabled' : '' ?>>&#9650;</button>
              </form>
              <form method="post" action="<?= sw_h($base_url) ?>" style="display:inline;">
                <input type="hidden" name="action" value="move_down">
                <input type="hidden" name="mod_id" value="<?= (int)$mod['id'] ?>">
                <button type="submit" class="button small" <?= $idx === (count($mods) - 1) ? 'disabled' : '' ?>>&#9660;</button>
              </form>
            </td>
            <td>
              <form method="post" action="<?= sw_h($base_url) ?>" style="display:inline;">
                <input type="hidden" name="action" value="delete_mod">
                <input type="hidden" name="mod_id" value="<?= (int)$mod['id'] ?>">
                <button type="submit" class="button small danger" onclick="return confirm('Remove this mod from the list?');" style="background:#d9534f;color:#fff;">Remove</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php
}
