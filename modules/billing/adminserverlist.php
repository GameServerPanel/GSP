<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Server / Game Matrix - GameServers.World</title>
    <style>
    .matrix-table { border-collapse: collapse; width: 100%; }
    .matrix-table th, .matrix-table td { border: 1px solid #ddd; padding: 6px 8px; vertical-align: middle; text-align: center; }
    .matrix-table th { background: #f5f5f5; white-space: nowrap; }
    .matrix-table td.game-name { text-align: left; white-space: nowrap; }
    .override-input { width: 72px; margin-top: 4px; }
    .muted { color: #999; font-size: 0.85em; }
    .flash-ok  { background: #d4edda; border: 1px solid #c3e6cb; padding: 8px 12px; margin-bottom: 10px; border-radius: 4px; }
    .flash-err { background: #f8d7da; border: 1px solid #f5c6cb; padding: 8px 12px; margin-bottom: 10px; border-radius: 4px; }
    </style>
</head>
<body>
<?php
// Admin matrix page: game × server availability + price overrides

require_once(__DIR__ . '/bootstrap.php');
require_once(__DIR__ . '/includes/admin_auth.php');

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name, isset($db_port) ? (int)$db_port : null);
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

include(__DIR__ . '/includes/top.php');
include(__DIR__ . '/includes/menu.php');

// Ensure the mapping table exists with the override_price column
$db->query(
    "CREATE TABLE IF NOT EXISTS `{$table_prefix}billing_service_remote_servers` (
        `id`               INT(11) NOT NULL AUTO_INCREMENT,
        `service_id`       INT(11) NOT NULL,
        `remote_server_id` INT(11) NOT NULL,
        `enabled`          TINYINT(1) NOT NULL DEFAULT 1,
        `override_price`   DECIMAL(10,2) NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `svc_rs` (`service_id`, `remote_server_id`),
        KEY `service_id` (`service_id`),
        KEY `remote_server_id` (`remote_server_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);
// Add override_price if this is an older install that already has the table without it
$chk = $db->query("SHOW COLUMNS FROM `{$table_prefix}billing_service_remote_servers` LIKE 'override_price'");
if ($chk && $chk->num_rows === 0) {
    $db->query("ALTER TABLE `{$table_prefix}billing_service_remote_servers` ADD COLUMN `override_price` DECIMAL(10,2) NULL");
}

$flash = [];
$flashType = 'ok';

/* -----------------------------------------------------------------------
   SAVE: matrix form submitted
----------------------------------------------------------------------- */
if (isset($_POST['save_matrix'])) {
    $postedServices = $_POST['svc'] ?? [];
    $postedMappings = $_POST['map'] ?? [];

    foreach ((array)$postedServices as $sid => $svcData) {
        $sid        = (int)$sid;
        $enabled    = isset($svcData['enabled']) ? 1 : 0;
        $base_price = number_format((float)($svcData['base_price'] ?? 0), 2, '.', '');
        $period     = in_array($svcData['period'] ?? 'monthly', ['daily','monthly','yearly'], true)
                      ? $svcData['period'] : 'monthly';

        $price_col = $period === 'daily' ? 'price_daily' : ($period === 'yearly' ? 'price_year' : 'price_monthly');
        $base_esc   = $db->real_escape_string($base_price);

        $db->query(
            "UPDATE `{$table_prefix}billing_services`
             SET enabled = {$enabled},
                 `{$price_col}` = '{$base_esc}'
             WHERE service_id = {$sid}"
        );
    }

    // Upsert mappings: for every service x server pair post data received
    $allServerIds = [];
    $rsRes = $db->query("SELECT remote_server_id FROM `{$table_prefix}remote_servers`");
    while ($rsRes && ($rsRow = $rsRes->fetch_assoc())) {
        $allServerIds[] = (int)$rsRow['remote_server_id'];
    }

    $stmt = $db->prepare(
        "INSERT INTO `{$table_prefix}billing_service_remote_servers`
            (service_id, remote_server_id, enabled, override_price)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            enabled        = VALUES(enabled),
            override_price = VALUES(override_price)"
    );
    foreach ((array)$postedServices as $sid => $ignored) {
        $sid = (int)$sid;
        foreach ($allServerIds as $rid) {
            $mapEnabled = isset($postedMappings[$sid][$rid]['enabled']) ? 1 : 0;
            $ovRaw      = $postedMappings[$sid][$rid]['override_price'] ?? '';
            $ovPrice    = (trim($ovRaw) === '') ? null : number_format((float)$ovRaw, 2, '.', '');
            if ($stmt) {
                $stmt->bind_param('iisd', $sid, $rid, $mapEnabled, $ovPrice);
                $stmt->execute();
            }
        }
    }
    if ($stmt) {
        $stmt->close();
    }

    $flash[] = "Matrix saved successfully.";
}

/* -----------------------------------------------------------------------
   Remove a service
----------------------------------------------------------------------- */
if (isset($_POST['remove_service'], $_POST['service_id_remove'])) {
    $sid = (int)$_POST['service_id_remove'];
    $db->query("DELETE FROM `{$table_prefix}billing_service_remote_servers` WHERE service_id = {$sid}");
    $db->query("DELETE FROM `{$table_prefix}billing_services` WHERE service_id = {$sid}");
    $flash[] = "Service #{$sid} removed.";
}

/* -----------------------------------------------------------------------
   Load data
----------------------------------------------------------------------- */
$remoteServers = [];
$rsRes = $db->query("SELECT remote_server_id, remote_server_name FROM `{$table_prefix}remote_servers` ORDER BY remote_server_name");
while ($rsRes && ($row = $rsRes->fetch_assoc())) {
    $remoteServers[] = $row;
}

$services = [];
$svcRes = $db->query(
    "SELECT service_id, service_name, enabled, price_daily, price_monthly, price_year
     FROM `{$table_prefix}billing_services`
     ORDER BY service_name"
);
while ($svcRes && ($row = $svcRes->fetch_assoc())) {
    $services[] = $row;
}

// Load existing mappings into a lookup: $mappings[$service_id][$remote_server_id] = ['enabled'=>..,'override_price'=>..]
$mappings = [];
$mapRes = $db->query(
    "SELECT service_id, remote_server_id, enabled, override_price
     FROM `{$table_prefix}billing_service_remote_servers`"
);
while ($mapRes && ($row = $mapRes->fetch_assoc())) {
    $mappings[(int)$row['service_id']][(int)$row['remote_server_id']] = [
        'enabled'        => (int)$row['enabled'],
        'override_price' => $row['override_price'],
    ];
}
?>

<?php foreach ((array)$flash as $msg): ?>
  <div class="flash-<?php echo $flashType; ?>"><?php echo h($msg); ?></div>
<?php endforeach; ?>

<h2>Game &times; Server Matrix</h2>
<p class="muted">
  Enable or disable each game for billing, set its base price and billing period, then
  toggle availability per server and optionally override the price for that location.
  Leave override blank to use the base price.
</p>

<?php if (empty($services)): ?>
  <p>No billing services found. Add services first via the database or the panel.</p>
<?php else: ?>

<form method="post" action="">
  <input type="hidden" name="save_matrix" value="1">

  <div style="overflow-x:auto;">
  <table class="matrix-table">
    <thead>
      <tr>
        <th class="game-name">Game</th>
        <th>Enabled</th>
        <th>Base Price ($)</th>
        <th>Period</th>
        <?php foreach ((array)$remoteServers as $rs): ?>
          <th><?php echo h($rs['remote_server_name']); ?><br>
            <span class="muted">#<?php echo (int)$rs['remote_server_id']; ?></span>
          </th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
    <?php foreach ((array)$services as $svc):
      $sid        = (int)$svc['service_id'];
      $svcEnabled = (int)$svc['enabled'];
      // Determine current base price and period from existing columns
      if ((float)$svc['price_monthly'] > 0) {
          $basePrice = number_format((float)$svc['price_monthly'], 2, '.', '');
          $period    = 'monthly';
      } elseif ((float)$svc['price_daily'] > 0) {
          $basePrice = number_format((float)$svc['price_daily'], 2, '.', '');
          $period    = 'daily';
      } elseif ((float)$svc['price_year'] > 0) {
          $basePrice = number_format((float)$svc['price_year'], 2, '.', '');
          $period    = 'yearly';
      } else {
          $basePrice = '0.00';
          $period    = 'monthly';
      }
    ?>
      <tr>
        <td class="game-name">
          <?php echo h($svc['service_name']); ?>
          <div class="muted">ID: <?php echo $sid; ?></div>
        </td>

        <td>
          <input type="hidden"   name="svc[<?php echo $sid; ?>][enabled]" value="0">
          <input type="checkbox" name="svc[<?php echo $sid; ?>][enabled]" value="1"
                 <?php echo $svcEnabled ? 'checked' : ''; ?>>
        </td>

        <td>
          <input type="number" step="0.01" min="0"
                 name="svc[<?php echo $sid; ?>][base_price]"
                 value="<?php echo h($basePrice); ?>"
                 style="width:90px;">
        </td>

        <td>
          <select name="svc[<?php echo $sid; ?>][period]">
            <option value="monthly" <?php echo $period === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
            <option value="daily"   <?php echo $period === 'daily'   ? 'selected' : ''; ?>>Daily</option>
            <option value="yearly"  <?php echo $period === 'yearly'  ? 'selected' : ''; ?>>Yearly</option>
          </select>
        </td>

        <?php foreach ((array)$remoteServers as $rs):
          $rid        = (int)$rs['remote_server_id'];
          $mapEntry   = $mappings[$sid][$rid] ?? ['enabled' => 0, 'override_price' => null];
          $mapEnabled = (int)$mapEntry['enabled'];
          $ovPrice    = $mapEntry['override_price'];
        ?>
          <td>
            <input type="hidden"   name="map[<?php echo $sid; ?>][<?php echo $rid; ?>][enabled]" value="0">
            <input type="checkbox" name="map[<?php echo $sid; ?>][<?php echo $rid; ?>][enabled]" value="1"
                   <?php echo $mapEnabled ? 'checked' : ''; ?>>
            <br>
            <input type="number" step="0.01" min="0" placeholder="override"
                   class="override-input"
                   name="map[<?php echo $sid; ?>][<?php echo $rid; ?>][override_price]"
                   value="<?php echo ($ovPrice !== null ? h(number_format((float)$ovPrice, 2, '.', '')) : ''); ?>">
          </td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>

  <div style="margin-top:14px;">
    <button type="submit">Save Matrix</button>
  </div>
</form>

<h3 style="margin-top:28px;">Remove a Service</h3>
<form method="post" action="" style="display:flex;gap:8px;align-items:center;">
  <input type="hidden" name="remove_service" value="1">
  <select name="service_id_remove">
    <?php foreach ((array)$services as $s): ?>
      <option value="<?php echo (int)$s['service_id']; ?>">
        <?php echo h($s['service_name']); ?> (ID: <?php echo (int)$s['service_id']; ?>)
      </option>
    <?php endforeach; ?>
  </select>
  <button type="submit" onclick="return confirm('Remove this service and all its server mappings? This cannot be undone.')">Remove</button>
</form>

<?php endif; ?>

<div class="panel" style="margin-top:20px;">
  <p><strong>Legend:</strong> Checkbox = server is available for this game.
  Override price = customer pays this amount instead of the base price for that location.
  Leave override blank to use the game base price.</p>
  <p class="muted">
    Availability is controlled entirely by <code><?php echo h("{$table_prefix}billing_service_remote_servers"); ?></code>.
    No entry or <code>enabled = 0</code> means the server is not offered for that game.
  </p>
</div>

<?php billing_maybe_close_db($db); ?>
</body>
</html>
