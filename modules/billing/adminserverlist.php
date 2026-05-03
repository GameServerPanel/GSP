<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Service Configuration - GSP</title>
    <style>
    .svc-table { border-collapse: collapse; width: 100%; }
    .svc-table th, .svc-table td { border: 1px solid #4a6080; padding: 6px 8px; vertical-align: middle; }
    /* Sticky header: stays visible while scrolling; dark background with light text for readability */
    .svc-table thead th { position: sticky; top: 0; z-index: 10; background: #2c3e50; color: #f0f0f0; white-space: nowrap; text-align: center; }
    .svc-table thead th.game-name { text-align: left; }
    .svc-table td.game-name { text-align: left; white-space: nowrap; }
    .price-input { width: 80px; }
    .slot-input  { width: 60px; }
    .desc-input  { width: 160px; }
    .img-input   { width: 160px; }
    .img-select  { max-width: 180px; }
    .img-fallback { display: none; max-width: 180px; margin-top: 4px; }
    .muted { color: #999; font-size: 0.85em; }
    .flash-ok  { background: #d4edda; border: 1px solid #c3e6cb; padding: 8px 12px; margin-bottom: 10px; border-radius: 4px; color: #155724; }
    .flash-err { background: #f8d7da; border: 1px solid #f5c6cb; padding: 8px 12px; margin-bottom: 10px; border-radius: 4px; color: #721c24; }
    .servers-cell { text-align: left; }
    .server-cb-label { display: block; white-space: nowrap; margin: 2px 0; }
    </style>
</head>
<body>
<?php
/**
 * Admin service configuration page.
 *
 * On every load this page syncs gsp_billing_services with the panel's game
 * config list (config_homes).  One billing_services row is maintained per
 * config_homes entry; the row is keyed by home_cfg_id.  config_mods is NOT
 * used as the identity source — mods are install-time details that belong in
 * the game config tables, not here.
 *
 * remote_server_id in gsp_billing_services stores a comma-separated list of
 * numeric remote server IDs, e.g. "1,3,7".  The deprecated
 * gsp_billing_service_remote_servers mapping table is never referenced here.
 *
 * Columns synced from config_homes (read-only in the UI):
 *   service_name  ← game_name
 *   description   ← game_name (default; admin may override via separate edit)
 *   home_cfg_id   ← home_cfg_id  (sync key)
 *
 * Columns that are admin-editable and NEVER overwritten by sync:
 *   enabled, slot_min_qty, slot_max_qty,
 *   price_daily, price_monthly, price_year,
 *   remote_server_id, description, img_url
 */

require_once(__DIR__ . '/bootstrap.php');
require_once(__DIR__ . '/includes/admin_auth.php');

function h(mixed $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

/**
 * Return a sorted list of image filenames available in /images/games/.
 * Only files with recognised image extensions are included.
 */
function list_game_images(): array
{
    $dir = __DIR__ . '/../../images/games';
    if (!is_dir($dir)) {
        return [];
    }
    $exts  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $files = [];
    foreach (scandir($dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        if (in_array($ext, $exts, true)) {
            $files[] = $f;
        }
    }
    natcasesort($files);
    return array_values($files);
}

/**
 * Normalize a game name or filename stem so that platform/architecture
 * suffixes are stripped before comparison.
 *
 * Examples:
 *   "7 Days to Die linux64"   → "7daystodie"
 *   "arma3_win64"             → "arma3"
 *   "dayz_epoch_mod_win32"    → "dayzepochmod"
 */
function normalize_game_name(string $name): string
{
    $name = strtolower($name);
    // Strip extension if present
    $name = preg_replace('/\.[a-z]{2,4}$/', '', $name);
    // Strip common platform/arch suffixes (as whole words or underscore-delimited tokens)
    $name = preg_replace('/[\s_\-]*(linux64|linux32|linux|win64|win32|windows|win|x64|x86|32|64)/', '', $name);
    // Remove punctuation, spaces and underscores
    $name = preg_replace('/[^a-z0-9]/', '', $name);
    return $name;
}

/**
 * Given a game name (from config_homes.game_name or home_cfg_file), try to find
 * a matching image filename from the list of available game images.
 * Returns the filename (e.g. "arma_3.jpg") or '' if nothing suitable is found.
 */
function guess_game_image(string $gameName, string $cfgFile, array $availableImages): string
{
    if (empty($availableImages)) {
        return '';
    }

    // Build a normalised→filename map for available images
    $normMap = [];
    foreach ($availableImages as $imgFile) {
        $stem = pathinfo($imgFile, PATHINFO_FILENAME);
        $key  = normalize_game_name($stem);
        if ($key !== '') {
            // Keep the first match for duplicate normalised keys
            $normMap[$key] = $normMap[$key] ?? $imgFile;
        }
    }

    // Candidates to try, in priority order: game display name, then cfg file stem
    $candidates = [$gameName];
    if ($cfgFile !== '') {
        $candidates[] = pathinfo($cfgFile, PATHINFO_FILENAME);
    }

    foreach ($candidates as $candidate) {
        $key = normalize_game_name($candidate);
        if ($key !== '' && isset($normMap[$key])) {
            return $normMap[$key];
        }
        // Also try prefix matching: game "dayz epoch" → find "dayz_epochmod"
        foreach ($normMap as $normImgKey => $imgFile) {
            if (str_starts_with($normImgKey, $key) || str_starts_with($key, $normImgKey)) {
                return $imgFile;
            }
        }
    }

    return '';
}

$db = billing_get_db();
if (!($db instanceof mysqli)) {
    die("Database connection failed.");
}

include(__DIR__ . '/includes/top.php');
include(__DIR__ . '/includes/menu.php');

/* -----------------------------------------------------------------------
   Auto-sync: keep billing_services in step with config_homes
   Source: one row per config_homes entry, keyed by home_cfg_id.
   Runs on every page load; INSERT and soft-disable only — never hard-delete.
----------------------------------------------------------------------- */
function sync_billing_services(mysqli $db, string $prefix): array
{
    $messages  = [];
    $tableName = $prefix . 'billing_services';

    // Schema auto-repair: ensure all expected columns exist.
    // col_exists() is provided by bootstrap.php.
    $autoRepairCols = [
        'home_cfg_id'      => "ADD COLUMN `home_cfg_id` INT(11) NOT NULL DEFAULT 0",
        'description'      => "ADD COLUMN `description` VARCHAR(1000) NOT NULL DEFAULT ''",
        'img_url'          => "ADD COLUMN `img_url` VARCHAR(255) NOT NULL DEFAULT ''",
        'slot_min_qty'     => "ADD COLUMN `slot_min_qty` INT(11) NOT NULL DEFAULT 1",
        'slot_max_qty'     => "ADD COLUMN `slot_max_qty` INT(11) NOT NULL DEFAULT 100",
        'price_daily'      => "ADD COLUMN `price_daily` FLOAT(15,4) NOT NULL DEFAULT 0",
        'price_monthly'    => "ADD COLUMN `price_monthly` FLOAT(15,4) NOT NULL DEFAULT 0",
        'price_year'       => "ADD COLUMN `price_year` FLOAT(15,4) NOT NULL DEFAULT 0",
        'remote_server_id' => "ADD COLUMN `remote_server_id` VARCHAR(255) NOT NULL DEFAULT ''",
    ];

    foreach ($autoRepairCols as $col => $alterFragment) {
        if (!col_exists($db, $tableName, $col)) {
            if ($db->query("ALTER TABLE `{$tableName}` {$alterFragment}")) {
                $messages[] = "✔ Auto-repaired: added column '{$col}' to {$tableName}.";
            } else {
                $messages[] = "✖ Could not add column '{$col}' to {$tableName}: " . $db->error;
            }
        }
    }

    // If critical columns are still absent after repair, abort to avoid SQL errors.
    foreach (['service_name', 'home_cfg_id', 'enabled'] as $critical) {
        if (!col_exists($db, $tableName, $critical)) {
            $messages[] = "⚠ Critical column '{$critical}' missing from {$tableName}; skipping sync.";
            return $messages;
        }
    }

    // Load all game configs from config_homes — one entry per game XML.
    $configHomes = [];
    $res = $db->query(
        "SELECT home_cfg_id, game_name, home_cfg_file
         FROM `{$prefix}config_homes`
         ORDER BY game_name"
    );
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $configHomes[(int)$row['home_cfg_id']] = $row;
        }
    }

    if (empty($configHomes)) {
        // config_homes is empty or the table does not exist yet — nothing to sync.
        return $messages;
    }

    // Load existing billing_services indexed by home_cfg_id.
    $existing = [];
    $svcRes = $db->query(
        "SELECT service_id, home_cfg_id, enabled
         FROM `{$tableName}`"
    );
    if ($svcRes) {
        while ($row = $svcRes->fetch_assoc()) {
            $hid = (int)$row['home_cfg_id'];
            if ($hid > 0) {
                $existing[$hid] = $row;
            }
        }
    }

    // Insert a new row for every config_homes entry not yet in billing_services.
    // Admin-editable fields (prices, slots, enabled, etc.) get safe defaults so
    // the service is visible to the admin but not yet live in the store.
    $availableImages = list_game_images();
    foreach ($configHomes as $homeCfgId => $ch) {
        if (isset($existing[$homeCfgId])) {
            continue;
        }
        $svcName = $db->real_escape_string($ch['game_name']);
        $guessedImg = $db->real_escape_string(
            guess_game_image((string)$ch['game_name'], (string)($ch['home_cfg_file'] ?? ''), $availableImages)
        );
        $db->query(
            "INSERT INTO `{$tableName}`
                (home_cfg_id, mod_cfg_id, service_name, description,
                 remote_server_id, enabled,
                 price_daily, price_monthly, price_year,
                 slot_min_qty, slot_max_qty,
                 img_url, ftp, install_method, manual_url, access_rights)
             VALUES
                ({$homeCfgId}, 0, '{$svcName}', '{$svcName}',
                 '', 0,
                 0.00, 0.00, 0.00,
                 1, 100,
                 '{$guessedImg}', '', 'steamcmd', '', '')"
        );
        $msg = "Added new service: " . $ch['game_name'];
        if ($guessedImg !== '') {
            $msg .= " (image auto-set: {$guessedImg})";
        }
        $messages[] = $msg;
    }

    // Soft-disable billing_services whose home_cfg_id no longer appears in config_homes.
    foreach ($existing as $homeCfgId => $svcRow) {
        if (!isset($configHomes[$homeCfgId])) {
            $sid = (int)$svcRow['service_id'];
            $db->query(
                "UPDATE `{$tableName}`
                 SET enabled = 0
                 WHERE service_id = {$sid} AND enabled = 1"
            );
            if ($db->affected_rows > 0) {
                $messages[] = "Service ID {$sid} disabled — game config no longer in config_homes.";
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
   Only admin-editable fields are updated; service_name and home_cfg_id
   are never overwritten here.
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
        $priceDaily   = number_format((float)($svcData['price_daily']   ?? 0), 2, '.', '');
        $priceMonthly = number_format((float)($svcData['price_monthly'] ?? 0), 2, '.', '');
        $priceYear    = number_format((float)($svcData['price_year']    ?? 0), 2, '.', '');
        $slotMin      = max(1, (int)($svcData['slot_min_qty'] ?? 1));
        $slotMax      = max(1, (int)($svcData['slot_max_qty'] ?? 1));
        if ($slotMax < $slotMin) { $slotMax = $slotMin; }
        $description  = $db->real_escape_string(substr((string)($svcData['description'] ?? ''), 0, 1000));
        // Merge dropdown and fallback text input:
        //   - dropdown value "__other__" means use the text fallback field
        //   - otherwise use the dropdown value (bare filename or '')
        $rawImgUrl = (string)($svcData['img_url'] ?? '');
        if ($rawImgUrl === '__other__') {
            $rawImgUrl = (string)($svcData['img_url_other'] ?? '');
        }
        $imgUrl = $db->real_escape_string(substr($rawImgUrl, 0, 255));

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
                 description      = '{$description}',
                 img_url          = '{$imgUrl}',
                 remote_server_id = '{$remoteServerIdStr}'
             WHERE service_id = {$sid}"
        );
    }

    $flash[] = "Services saved.";
}

/* -----------------------------------------------------------------------
   Load data for display — join config_homes to show the config XML filename
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
    "SELECT bs.service_id, bs.service_name, bs.enabled,
            bs.price_daily, bs.price_monthly, bs.price_year,
            bs.slot_min_qty, bs.slot_max_qty,
            bs.remote_server_id, bs.description, bs.img_url,
            ch.home_cfg_file
     FROM `{$table_prefix}billing_services` bs
     LEFT JOIN `{$table_prefix}config_homes` ch ON ch.home_cfg_id = bs.home_cfg_id
     ORDER BY bs.service_name"
);
while ($svcRes && ($row = $svcRes->fetch_assoc())) {
    $services[] = $row;
}
?>

<?php foreach (array_merge((array)$syncMessages, (array)$flash) as $msg): ?>
  <div class="flash-<?php echo $flashType; ?>"><?php echo h($msg); ?></div>
<?php endforeach; ?>

<h2>Service Configuration</h2>
<p class="muted">
    Enable services, configure pricing and slot ranges, and select which remote servers
    each game can be installed on.  The service list is automatically kept in sync with
    the panel game configuration (<code>config_homes</code>).  Check one or more servers
    to make a game available for purchase; leaving all servers unchecked prevents the
    game from appearing in the store.
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
        <th class="game-name">Game Name</th>
        <th>Config XML</th>
        <th>Enabled</th>
        <th>Min Slots</th>
        <th>Max Slots</th>
        <th>Price / Day ($)</th>
        <th>Price / Month ($)</th>
        <th>Price / Year ($)</th>
        <th>Description</th>
        <th>Image</th>
        <th>Available Servers</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $gameImageFiles = list_game_images();
    foreach ((array)$services as $svc):
      $sid = (int)$svc['service_id'];
      $svcEnabled    = (int)$svc['enabled'];
      $cfgFile       = (string)($svc['home_cfg_file'] ?? '');

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

        <td class="muted">
          <?php echo $cfgFile !== '' ? h($cfgFile) : '<em>—</em>'; ?>
        </td>

        <td style="text-align:center;">
          <input type="hidden"   name="svc[<?php echo $sid; ?>][enabled]" value="0">
          <input type="checkbox" name="svc[<?php echo $sid; ?>][enabled]" value="1"
                 <?php echo $svcEnabled ? 'checked' : ''; ?>>
        </td>

        <td>
          <input type="number" min="1" class="slot-input"
                 name="svc[<?php echo $sid; ?>][slot_min_qty]"
                 value="<?php echo (int)$svc['slot_min_qty']; ?>">
        </td>

        <td>
          <input type="number" min="1" class="slot-input"
                 name="svc[<?php echo $sid; ?>][slot_max_qty]"
                 value="<?php echo (int)$svc['slot_max_qty']; ?>">
        </td>

        <td>
          <input type="number" step="0.01" min="0" class="price-input"
                 name="svc[<?php echo $sid; ?>][price_daily]"
                 value="<?php echo h(number_format((float)$svc['price_daily'], 2, '.', '')); ?>">
        </td>

        <td>
          <input type="number" step="0.01" min="0" class="price-input"
                 name="svc[<?php echo $sid; ?>][price_monthly]"
                 value="<?php echo h(number_format((float)$svc['price_monthly'], 2, '.', '')); ?>">
        </td>

        <td>
          <input type="number" step="0.01" min="0" class="price-input"
                 name="svc[<?php echo $sid; ?>][price_year]"
                 value="<?php echo h(number_format((float)$svc['price_year'], 2, '.', '')); ?>">
        </td>

        <td>
          <input type="text" class="desc-input"
                 name="svc[<?php echo $sid; ?>][description]"
                 value="<?php echo h($svc['description']); ?>">
        </td>

        <td>
          <?php
            // Determine whether saved value is a bare filename (in /images/games/),
            // a full external URL, or empty.
            $savedImg    = (string)($svc['img_url'] ?? '');
            $isExternal  = (str_starts_with($savedImg, 'http://') || str_starts_with($savedImg, 'https://'));
            $inDropdown  = !$isExternal && in_array(basename($savedImg), $gameImageFiles, true);
            // Value to pre-select in the dropdown: use bare filename, or '' if external/missing
            $dropdownVal = (!$isExternal && $savedImg !== '') ? basename($savedImg) : '';
          ?>
          <select name="svc[<?php echo $sid; ?>][img_url]"
                  style="max-width:180px;"
                  onchange="
                    var other=document.getElementById('imgfb_<?php echo $sid; ?>');
                    other.style.display=(this.value==='__other__')?'block':'none';
                    if(this.value!=='__other__') other.value='';
                  ">
            <option value="">— none —</option>
            <?php foreach ($gameImageFiles as $imgFile): ?>
              <option value="<?php echo h($imgFile); ?>"
                <?php echo ($dropdownVal === $imgFile) ? 'selected' : ''; ?>>
                <?php echo h($imgFile); ?>
              </option>
            <?php endforeach; ?>
            <option value="__other__" <?php echo ($isExternal || (!$inDropdown && $savedImg !== '')) ? 'selected' : ''; ?>>
              — other / full URL —
            </option>
          </select>
          <?php
            // Show fallback text input when the saved value is external or not in dropdown
            $fbDisplay = ($isExternal || (!$inDropdown && $savedImg !== '')) ? 'block' : 'none';
            $fbValue   = ($isExternal || (!$inDropdown && $savedImg !== '')) ? $savedImg : '';
          ?>
          <input type="text"
                 id="imgfb_<?php echo $sid; ?>"
                 name="svc[<?php echo $sid; ?>][img_url_other]"
                 placeholder="Full URL or filename"
                 style="display:<?php echo $fbDisplay; ?>;max-width:180px;margin-top:4px;"
                 value="<?php echo h($fbValue); ?>">
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
    <li>The <strong>Game Name</strong> and <strong>Config XML</strong> columns are sourced
        from <code><?php echo h("{$table_prefix}config_homes"); ?></code> and are read-only
        here.  To change them, update the game XML config in the panel.</li>
    <li>Available servers are stored as a comma-separated list of server IDs in
        <code><?php echo h("{$table_prefix}billing_services.remote_server_id"); ?></code>.</li>
    <li>The service list is automatically synced with the panel game configuration on
        every page load.  New games are added with <em>Enabled = off</em> so they do not
        appear in the store until you configure and enable them.</li>
    <li>Games removed from the panel configuration are disabled automatically; they are
        never deleted while orders may reference them.</li>
  </ul>
</div>

<?php billing_maybe_close_db($db); ?>
</body>
</html>
