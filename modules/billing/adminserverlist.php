<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Server List - GameServers.World</title>
</head>
<body>
<?php
// gameservers.world admin — mysqli only, bulk + per-row update, image base URL + small button

/* === SITE_BASE_URL is loaded from includes/config.inc.php; leave empty to use relative paths === */

// Include database configuration
require_once(__DIR__ . '/includes/config.inc.php');

// Protect this page: require admin
require_once(__DIR__ . '/includes/admin_auth.php');

// Create database connection (admin_auth already validated DB but we need connection for UI ops)
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$db) {
  die("Connection failed: " . mysqli_connect_error());
}

// Include top bar and menu
include(__DIR__ . '/includes/top.php');
include(__DIR__ . '/includes/menu.php');

/* show errors during setup */
@ini_set('display_errors','1'); 
error_reporting(E_ALL);
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function esc_mysqli($db, $v){ return $db->real_escape_string($v); }
function fetch_all_assoc($db, $sql){
  $res = $db->query($sql);
  return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
function col_exists($db, $table, $col){
  $res = $db->query("SHOW COLUMNS FROM `$table` LIKE '".$db->real_escape_string($col)."'");
  return ($res && $res->num_rows > 0);
}
function parse_id_list($s){
  $tokens = preg_split('/\s+/', trim((string)$s));
  $out = [];
  foreach ($tokens as $t) {
    if ($t === '') continue;
    if (preg_match('/^\d+$/', $t)) $out[] = (int)$t;
  }
  return array_values(array_unique($out));
}
/* URL helpers for image preview */
function is_abs_url($u){ return (bool)preg_match('~^(?:https?:)?//|^data:~i', (string)$u); }
function join_base($base, $path){
  $base = rtrim((string)$base, '/');
  $path = ltrim((string)$path, '/');
  return $base !== '' ? $base.'/'.$path : $path;
}

/* which column holds space-separated locations */
$locationCol = col_exists($db, "{$table_prefix}billing_services", 'remote_server_id') ? 'remote_server_id' :
               (col_exists($db, "{$table_prefix}billing_services", 'remote_server') ? 'remote_server' : 'remote_server_id');

$flash = [];

/* A) Update global server location enable flags */
if (isset($_POST['update_remote_servers'])) {
  $enabledIds = array_map('intval', $_POST['rs'] ?? []);
  $enabledSet = array_flip($enabledIds);
  $allIds = fetch_all_assoc($db, "SELECT remote_server_id FROM {$table_prefix}remote_servers");
  foreach ($allIds as $row) {
    $id = (int)$row['remote_server_id'];
    $e  = isset($enabledSet[$id]) ? 1 : 0;
    $db->query("UPDATE {$table_prefix}remote_servers SET enabled={$e} WHERE remote_server_id={$id}");
  }
  $flash[] = "Server locations updated.";
}

/* helper: update one service row from posted array */
function update_service_row(mysqli $db, string $locationCol, int $sid, array $svc){
  $name  = esc_mysqli($db, trim($svc['service_name'] ?? ''));
  $price = esc_mysqli($db, trim($svc['price_monthly'] ?? '0.00'));
  $img   = esc_mysqli($db, trim($svc['img_url'] ?? ''));
  $en    = !empty($svc['enabled']) ? 1 : 0;

  $minSlots = max(1, (int)($svc['slot_min_qty'] ?? 1));
  $maxSlots = max($minSlots, (int)($svc['slot_max_qty'] ?? $minSlots));

  $selected = [];
  if (!empty($svc['locations']) && is_array($svc['locations'])) {
    $selected = array_map('intval', $svc['locations']);
    $selected = array_values(array_unique($selected));
  }
  $primary = isset($svc['primary_location']) ? (int)$svc['primary_location'] : 0;
  if ($primary && in_array($primary, $selected, true)) {
    $selected = array_values(array_diff($selected, [$primary]));
    array_unshift($selected, $primary);
  }
  $locList    = implode(' ', $selected);
  $locListEsc = esc_mysqli($db, $locList);

  $sql = "UPDATE {$table_prefix}billing_services
             SET service_name='{$name}',
                 `{$locationCol}`='{$locListEsc}',
                 slot_min_qty={$minSlots},
                 slot_max_qty={$maxSlots},
                 price_monthly='{$price}',
                 img_url='{$img}',
                 enabled={$en}
           WHERE service_id={$sid}";
  $db->query($sql);
}

/* B1) PER-ROW UPDATE */
if (isset($_POST['update_single']) && isset($_POST['service']) && is_array($_POST['service'])) {
  $sid = (int)$_POST['update_single'];
  if (isset($_POST['service'][$sid])) {
    update_service_row($db, $locationCol, $sid, $_POST['service'][$sid]);
    $flash[] = "Service #{$sid} updated.";
  }
}

/* B2) BULK UPDATE (single button at bottom) */
if (isset($_POST['bulk_update']) && !empty($_POST['service']) && is_array($_POST['service'])) {
  foreach ($_POST['service'] as $sid => $svc) {
    update_service_row($db, $locationCol, (int)$sid, (array)$svc);
  }
  $flash[] = "All edited services have been updated.";
}

/* C) Remove a service (separate small form) */
if (isset($_POST['remove_service'], $_POST['service_id_remove'])) {
  $sid = (int)$_POST['service_id_remove'];
  $db->query("DELETE FROM {$table_prefix}billing_services WHERE service_id={$sid}");
  $flash[] = "Service #{$sid} removed.";
}

/* fetch data for UI */
$remoteServers = fetch_all_assoc($db, "SELECT remote_server_id, remote_server_name, enabled FROM {$table_prefix}remote_servers ORDER BY remote_server_name");
$services      = fetch_all_assoc($db, "SELECT service_id, service_name, `{$locationCol}` AS locs, slot_min_qty, slot_max_qty, price_monthly, img_url, enabled FROM {$table_prefix}billing_services ORDER BY service_name");
?>

<?php if ($flash): ?>
  <div class="panel" style="margin-bottom:12px"><?php foreach($flash as $m) echo "<div>".h($m)."</div>"; ?></div>
    <div class="panel mb-12"><?php foreach($flash as $m) echo "<div>".h($m)."</div>"; ?></div>
<?php endif; ?>

<h2>Enable/Disable Server Locations (Global)</h2>
<form method="post" action="">
  <input type="hidden" name="update_remote_servers" value="1">
  <div style="display:flex;flex-wrap:wrap;gap:10px;">
    <?php foreach ($remoteServers as $rs): ?>
  <label class="loc-label min-w-240">
        <input type="checkbox" name="rs[]" value="<?php echo (int)$rs['remote_server_id']; ?>" <?php echo ((int)$rs['enabled']===1?'checked':''); ?>>
        <b><?php echo h($rs['remote_server_name']); ?></b>
  <small class="muted">(ID: <?php echo (int)$rs['remote_server_id']; ?>)</small>
      </label>
    <?php endforeach; ?>
  </div>
  <div style="margin-top:10px;"><button type="submit">Update Enabled Servers</button></div>
    <div class="mt-10"><button type="submit">Update Enabled Servers</button></div>
</form>

<hr>

<h2>Current Services</h2>
<?php if (!$services): ?>
  <p>No services found.</p>
<?php else: ?>

<!-- SINGLE BULK FORM FOR ALL SERVICES -->
<form method="post" action="">

  <table class="center" style="text-align:center;width:100%;border-collapse:collapse;">
    <thead>
      <tr>
  <th>Enabled</th>
  <th>Service Name <small class="muted">(ID below)</small></th>
  <th>Min Slots</th>
  <th>Max Slots</th>
  <th>Price (Monthly)</th>
  <th>Thumbnail URL</th>
  <th>Preview</th>
  <th>Update Row</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($services as $row): ?>
      <?php
        $sid      = (int)$row['service_id'];
        $selected = parse_id_list($row['locs'] ?? '');
        $primary  = $selected[0] ?? 0; // first ID is "primary"
        $selSet   = array_flip($selected);
        $imgUrl   = trim((string)$row['img_url']);
        $displayUrl = '';
        if ($imgUrl !== '') {
          if (is_abs_url($imgUrl)) {
            $displayUrl = $imgUrl;
          } elseif ($SITE_BASE_URL !== '') {
            $displayUrl = join_base($SITE_BASE_URL, $imgUrl);
          } else {
            // Use relative path (local folder)
            $displayUrl = $imgUrl;
          }
        }
      ?>

      <!-- MAIN ROW (no bottom border) -->
      <tr>
        <!-- Enabled first -->
        <td>
          <input type="hidden" name="service[<?php echo $sid; ?>][enabled]" value="0">
          <input type="checkbox" name="service[<?php echo $sid; ?>][enabled]" value="1" <?php echo ((int)$row['enabled']===1?'checked':''); ?>>
        </td>

        <!-- Service name (with tiny ID under it) -->
        <td>
          <input type="text" name="service[<?php echo $sid; ?>][service_name]" value="<?php echo h($row['service_name']); ?>" class="min-w-260">
          <div class="small-muted">ID: <?php echo $sid; ?></div>
        </td>

        <td>
          <input type="number" name="service[<?php echo $sid; ?>][slot_min_qty]" value="<?php echo (int)$row['slot_min_qty']; ?>" min="1" step="1" class="w-90">
        </td>

        <td>
          <input type="number" name="service[<?php echo $sid; ?>][slot_max_qty]" value="<?php echo (int)$row['slot_max_qty']; ?>" min="1" step="1" class="w-90">
        </td>

        <td>
          <input type="text" name="service[<?php echo $sid; ?>][price_monthly]" value="<?php echo h($row['price_monthly']); ?>" size="8">
        </td>

        <!-- Thumbnail URL input -->
        <td>
          <input type="text" name="service[<?php echo $sid; ?>][img_url]" value="<?php echo h($row['img_url']); ?>" class="min-w-240">
        </td>

        <!-- Preview (uses BASE + relative path) -->
        <td>
          <?php if ($displayUrl !== ''): ?>
      <img src="<?php echo h($displayUrl); ?>" alt="preview" loading="lazy" class="img-preview" onerror="this.style.display='none'">
          <?php else: ?>
            <span class="muted">(no image)</span>
          <?php endif; ?>
        </td>

        <!-- Per-row Update (smaller) -->
  <td>
     <button type="submit" name="update_single" value="<?php echo $sid; ?>" class="btn-small">Update Row</button>
  </td>
      </tr>

      <!-- LOCATIONS ROW (single bottom divider) -->
      <tr>
        <td colspan="8" style="border-bottom:1px solid #f0f0f0; padding:8px 6px; text-align:left;">
          <div class="locs-box" data-sid="<?php echo $sid; ?>" style="display:flex; flex-wrap:wrap; gap:8px;">
            <?php foreach ($remoteServers as $rs): ?>
              <?php
                $rid = (int)$rs['remote_server_id'];
                $isChecked = isset($selSet[$rid]);
                $isPrimary = ($primary === $rid);
              ?>
              <label class="loc-label">
                <input type="checkbox" class="locchk" data-sid="<?php echo $sid; ?>"
                       name="service[<?php echo $sid; ?>][locations][]" value="<?php echo $rid; ?>"
                       <?php echo $isChecked ? 'checked' : ''; ?> class="mr-6">
                <?php echo h($rs['remote_server_name']); ?> (<?php echo $rid; ?>)
                <span style="margin-left:10px;">
                  <input type="radio" class="locprim" data-sid="<?php echo $sid; ?>"
                         name="service[<?php echo $sid; ?>][primary_location]" value="<?php echo $rid; ?>"
                         <?php echo $isPrimary ? 'checked' : ''; ?> <?php echo $isChecked ? '' : 'disabled'; ?>>
                  <small>Primary</small>
                </span>
                <?php if ((int)$rs['enabled'] === 0): ?>
                  <small class="text-danger ml-8">[Globally disabled]</small>
                <?php endif; ?>
              </label>
            <?php endforeach; ?>
          </div>
        </td>
      </tr>

    <?php endforeach; ?>
    </tbody>
  </table>

  <div style="margin-top:14px; text-align:right;">
    <button type="submit" name="bulk_update" value="1">Update All</button>
  </div>

</form>

  <h3 style="margin-top:20px;">Remove a Service</h3>
  <form method="post" action="" style="display:flex;gap:8px;align-items:center;">
    <input type="hidden" name="remove_service" value="1">
    <select name="service_id_remove">
      <?php foreach ($services as $s): ?>
        <option value="<?php echo (int)$s['service_id']; ?>">
          <?php echo h($s['service_name']); ?> (ID: <?php echo (int)$s['service_id']; ?>)
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit" onclick="return confirm('Remove this service? This cannot be undone.')">Remove</button>
  </form>
<?php endif; ?>

<!-- JS: Per-row: enable/disable Primary radios based on whether that location is checked -->
<script>
document.querySelectorAll('.locs-box').forEach(function(box){
  const sid = box.getAttribute('data-sid');
  const checks = box.querySelectorAll('input.locchk[data-sid="'+sid+'"]');

  function refreshRadios() {
    checks.forEach(function(chk){
      const rid = chk.value;
      const rad = box.querySelector('input.locprim[data-sid="'+sid+'"][value="'+rid+'"]');
      if (!rad) return;
      if (chk.checked) {
        rad.disabled = false;
      } else {
        if (rad.checked) rad.checked = false;
        rad.disabled = true;
      }
    });
  }

  checks.forEach(chk => chk.addEventListener('change', refreshRadios));
  refreshRadios();
});
</script>

<?php
// Close database connection
mysqli_close($db);
?>
</body>
</html>
