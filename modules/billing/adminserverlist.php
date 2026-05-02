<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Service Configuration - GSP</title>
    <style>
    .svc-table { border-collapse: collapse; width: 100%; }
    .svc-table th, .svc-table td { border: 1px solid #ddd; padding: 6px 8px; vertical-align: middle; }
    .svc-table th { background: #f5f5f5; white-space: nowrap; text-align: center; }
    .svc-table td.game-name { text-align: left; white-space: nowrap; }
    .price-input { width: 80px; }
    .slot-input  { width: 60px; }
    .muted { color: #999; font-size: 0.85em; }
    .flash-ok  { background: #d4edda; border: 1px solid #c3e6cb; padding: 8px 12px; margin-bottom: 10px; border-radius: 4px; }
    .flash-err { background: #f8d7da; border: 1px solid #f5c6cb; padding: 8px 12px; margin-bottom: 10px; border-radius: 4px; }
    .servers-cell { text-align: left; }
    .server-cb-label { display: block; white-space: nowrap; margin: 2px 0; }
    </style>
</head>
<body>
<?php
/**
 * Admin service configuration page.
 *
 * On every load this page syncs gsp_billing_services with the panel's game/mod
 * config list (config_mods joined with config_homes).  It provides a table UI
 * where admins can enable/disable services, set prices, configure slot ranges,
 * and choose which remote servers each game can be installed on.
 *
 * remote_server_id in gsp_billing_services stores a comma-separated list of
 * numeric remote server IDs, e.g. "1,3,7".  The deprecated
 * gsp_billing_service_remote_servers mapping table is never referenced here.
 */

require_once(__DIR__ . '/bootstrap.php');
require_once(__DIR__ . '/includes/admin_auth.php');

function h(mixed $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$db = billing_get_db();
if (!($db instanceof mysqli)) {
    die("Database connection failed.");
}

include(__DIR__ . '/includes/top.php');
include(__DIR__ . '/includes/menu.php');

/* -----------------------------------------------------------------------
   Auto-sync: keep billing_services in step with game/mod config list
   Runs on every page load; INSERT and soft-disable only — never hard-delete.
----------------------------------------------------------------------- */
function sync_billing_services(mysqli $db, string $prefix): array
{
    $messages = [];

    // Load all games/mods from panel config tables
    $gameMods = [];
    $res = $db->query(
        "SELECT cm.mod_cfg_id, cm.home_cfg_id, cm.mod_name, ch.game_name
         FROM `{$prefix}config_mods` cm
         JOIN `{$prefix}config_homes` ch ON ch.home_cfg_id = cm.home_cfg_id
         ORDER BY ch.game_name, cm.mod_name"
    );
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $gameMods[(int)$row['mod_cfg_id']] = $row;
        }
    }

    if (empty($gameMods)) {
        // config_mods is empty or tables don't exist yet — nothing to sync
        return $messages;
    }

    // Load existing billing_services indexed by mod_cfg_id
    $existing = [];
    $svcRes = $db->query(
        "SELECT service_id, mod_cfg_id, enabled, out_of_stock
         FROM `{$prefix}billing_services`"
    );
    if ($svcRes) {
        while ($row = $svcRes->fetch_assoc()) {
            $existing[(int)$row['mod_cfg_id']] = $row;
        }
    }

    // Insert new rows for game/mods not yet in billing_services
    foreach ($gameMods as $modCfgId => $gm) {
        if (isset($existing[$modCfgId])) {
            continue;
        }
        $svcName   = $db->real_escape_string($gm['mod_name'] ?: $gm['game_name']);
        $homeCfgId = (int)$gm['home_cfg_id'];
        $db->query(
            "INSERT INTO `{$prefix}billing_services`
                (home_cfg_id, mod_cfg_id, service_name, remote_server_id,
                 enabled, out_of_stock, price_daily, price_monthly, price_year,
                 slot_min_qty, slot_max_qty, install_method)
             VALUES
                ({$homeCfgId}, {$modCfgId}, '{$svcName}', '',
                 0, 0, 0, 0, 0,
                 1, 100, 'steamcmd')"
        );
        $messages[] = "Added new service: " . ($gm['mod_name'] ?: $gm['game_name']);
    }

    // Soft-disable billing_services whose mod_cfg_id no longer appears in config_mods
    foreach ($existing as $modCfgId => $svcRow) {
        if ($modCfgId > 0 && !isset($gameMods[$modCfgId])) {
            $sid = (int)$svcRow['service_id'];
            $db->query(
                "UPDATE `{$prefix}billing_services`
                 SET enabled = 0, out_of_stock = 1
                 WHERE service_id = {$sid} AND enabled = 1"
            );
            if ($db->affected_rows > 0) {
                $messages[] = "Service ID {$sid} disabled — game mod no longer in config.";
            }
        }
    }

    return $messages;
}

$syncMessages = sync_billing_services($db, $table_prefix);

$flash     = [];
$flashType = 'ok';

/* -----------------------------------------------------------------------
   SAVE: service configuration form submitted
----------------------------------------------------------------------- */
if (isset($_POST['save_services'])) {
    // Load valid remote server IDs for validation
    $validServerIds = [];
    $rsRes = $db->query("SELECT remote_server_id FROM `{$table_prefix}remote_servers`");
    while ($rsRes && ($rsRow = $rsRes->fetch_assoc())) {
        $validServerIds[] = (int)$rsRow['remote_server_id'];
    }
    $validSet = array_flip($validServerIds);

    $postedServices = $_POST['svc'] ?? [];
    $postedServers  = $_POST['servers'] ?? [];

    foreach ((array)$postedServices as $sid => $svcData) {
        $sid          = (int)$sid;
        $enabled      = isset($svcData['enabled']) ? 1 : 0;
        $priceDaily   = number_format((float)($svcData['price_daily']   ?? 0), 4, '.', '');
        $priceMonthly = number_format((float)($svcData['price_monthly'] ?? 0), 4, '.', '');
        $priceYear    = number_format((float)($svcData['price_year']    ?? 0), 4, '.', '');
        $slotMin      = max(0, (int)($svcData['slot_min_qty'] ?? 0));
        $slotMax      = max(0, (int)($svcData['slot_max_qty'] ?? 0));
        if ($slotMax < $slotMin) { $slotMax = $slotMin; }

        // Build comma-separated remote_server_id from checkboxes, validating each ID
        $checkedIds = [];
        foreach ((array)($postedServers[$sid] ?? []) as $rawId) {
            $rid = (int)$rawId;
            if (isset($validSet[$rid])) {
                $checkedIds[] = $rid;
            }
        }
        $remoteServerIdStr = $db->real_escape_string(implode(',', $checkedIds));

        $db->query(
            "UPDATE `{$table_prefix}billing_services`
             SET enabled          = {$enabled},
                 price_daily      = '{$priceDaily}',
                 price_monthly    = '{$priceMonthly}',
                 price_year       = '{$priceYear}',
                 slot_min_qty     = {$slotMin},
                 slot_max_qty     = {$slotMax},
                 remote_server_id = '{$remoteServerIdStr}'
             WHERE service_id = {$sid}"
        );
    }

    $flash[] = "Services saved.";
}

/* -----------------------------------------------------------------------
   Load data for display
----------------------------------------------------------------------- */
$remoteServers = [];
$rsRes = $db->query(
    "SELECT remote_server_id, remote_server_name
     FROM `{$table_prefix}remote_servers`
     ORDER BY remote_server_name"
);
while ($rsRes && ($row = $rsRes->fetch_assoc())) {
    $remoteServers[] = $row;
}

$services = [];
$svcRes = $db->query(
    "SELECT service_id, service_name, enabled, price_daily, price_monthly, price_year,
            slot_min_qty, slot_max_qty, remote_server_id
     FROM `{$table_prefix}billing_services`
     ORDER BY service_name"
);
while ($svcRes && ($row = $svcRes->fetch_assoc())) {
    $services[] = $row;
}
?>

<?php foreach (array_merge((array)$syncMessages, (array)$flash) as $idx => $msg):
    $type = ($flashType === 'err' || $idx < count((array)$syncMessages) && count($syncMessages) > 0 && strpos($msg, 'disabled') !== false) ? 'ok' : $flashType;
?>
  <div class="flash-<?php echo $flashType; ?>"><?php echo h($msg); ?></div>
<?php endforeach; ?>

<h2>Service Configuration</h2>
<p class="muted">
    Enable services, configure pricing and slot ranges, and select which remote servers
    each game can be installed on.  The service list is automatically kept in sync with
    the panel game/mod configuration.  Check one or more servers to make a game available
    for purchase; leaving all servers unchecked prevents the game from appearing in the
    store.
</p>

<?php if (empty($services)): ?>
  <p>No billing services found. Ensure game configs are loaded in the panel (Home &rarr; Games configuration).</p>
<?php else: ?>

<form method="post" action="">
  <input type="hidden" name="save_services" value="1">

  <div style="overflow-x:auto;">
  <table class="svc-table">
    <thead>
      <tr>
        <th class="game-name">Game / Service</th>
        <th>Enabled</th>
        <th>Price / Day ($)</th>
        <th>Price / Month ($)</th>
        <th>Price / Year ($)</th>
        <th>Min Slots</th>
        <th>Max Slots</th>
        <th>Available Servers</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ((array)$services as $svc):
      $sid = (int)$svc['service_id'];
      $svcEnabled = (int)$svc['enabled'];

      // Parse existing remote_server_id CSV into a set for fast checkbox lookup
      $savedIds = [];
      foreach (explode(',', (string)$svc['remote_server_id']) as $part) {
          $part = trim($part);
          if ($part !== '' && ctype_digit($part)) {
              $savedIds[(int)$part] = true;
          }
      }
    ?>
      <tr>
        <td class="game-name">
          <?php echo h($svc['service_name']); ?>
          <div class="muted">ID: <?php echo $sid; ?></div>
        </td>

        <td style="text-align:center;">
          <input type="hidden"   name="svc[<?php echo $sid; ?>][enabled]" value="0">
          <input type="checkbox" name="svc[<?php echo $sid; ?>][enabled]" value="1"
                 <?php echo $svcEnabled ? 'checked' : ''; ?>>
        </td>

        <td>
          <input type="number" step="0.0001" min="0" class="price-input"
                 name="svc[<?php echo $sid; ?>][price_daily]"
                 value="<?php echo h(number_format((float)$svc['price_daily'], 4, '.', '')); ?>">
        </td>

        <td>
          <input type="number" step="0.0001" min="0" class="price-input"
                 name="svc[<?php echo $sid; ?>][price_monthly]"
                 value="<?php echo h(number_format((float)$svc['price_monthly'], 4, '.', '')); ?>">
        </td>

        <td>
          <input type="number" step="0.0001" min="0" class="price-input"
                 name="svc[<?php echo $sid; ?>][price_year]"
                 value="<?php echo h(number_format((float)$svc['price_year'], 4, '.', '')); ?>">
        </td>

        <td>
          <input type="number" min="0" class="slot-input"
                 name="svc[<?php echo $sid; ?>][slot_min_qty]"
                 value="<?php echo (int)$svc['slot_min_qty']; ?>">
        </td>

        <td>
          <input type="number" min="0" class="slot-input"
                 name="svc[<?php echo $sid; ?>][slot_max_qty]"
                 value="<?php echo (int)$svc['slot_max_qty']; ?>">
        </td>

        <td class="servers-cell">
          <?php if (empty($remoteServers)): ?>
            <span class="muted">No remote servers configured</span>
          <?php else: ?>
            <?php foreach ((array)$remoteServers as $rs):
              $rid     = (int)$rs['remote_server_id'];
              $checked = isset($savedIds[$rid]) ? 'checked' : '';
            ?>
              <label class="server-cb-label">
                <input type="checkbox"
                       name="servers[<?php echo $sid; ?>][]"
                       value="<?php echo $rid; ?>"
                       <?php echo $checked; ?>>
                <?php echo h($rs['remote_server_name']); ?>
                <span class="muted">(#<?php echo $rid; ?>)</span>
              </label>
            <?php endforeach; ?>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>

  <div style="margin-top:14px;">
    <button type="submit">Save Services</button>
  </div>
</form>

<?php endif; ?>

<div style="margin-top:20px;" class="panel">
  <p><strong>Notes:</strong></p>
  <ul>
    <li>A service will only appear in the store when <strong>Enabled</strong> is checked
        <em>and</em> at least one server is selected.</li>
    <li>Available servers are stored as a comma-separated list of server IDs in
        <code><?php echo h("{$table_prefix}billing_services.remote_server_id"); ?></code>.</li>
    <li>The service list is automatically synced with the panel game/mod configuration on
        every page load.  New games are added with <em>Enabled = off</em> so they do not
        appear in the store until you configure and enable them.</li>
    <li>Games removed from the panel configuration are disabled automatically; they are
        never deleted while orders may reference them.</li>
  </ul>
</div>

<?php billing_maybe_close_db($db); ?>
</body>
</html>
