<?php
// stats_aggregate.php — call: ?machine=HOSTNAME_OR_ID[&format=html]

/********** CONFIG (panel DB) **********/
require_once __DIR__ . '/includes/config.inc.php'; // sets $db_host, $db_user, $db_pass, $db_name, $table_prefix
$TABLE_PREFIX = $table_prefix ?? 'gsp_';
$AUTO_REFRESH_SECONDS = 30; // set 0 to disable auto-refresh
/***************************************/

$mysqli = @new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_errno) {
  http_response_code(500);
  echo "<pre>DB connect failed: " . htmlspecialchars($mysqli->connect_error) . "</pre>";
  exit;
}
$mysqli->set_charset("utf8mb4");

function q($mysqli, $sql, $params=[]) {
  $stmt = $mysqli->prepare($sql);
  if(!$stmt){ throw new Exception($mysqli->error); }
  if(!empty($params)) {
    // infer types (all strings for simplicity)
    $types = str_repeat('s', count((array)$params));
    $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();
  $res = $stmt->get_result();
  $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
  $stmt->close();
  return $rows;
}
function fmt_bytes($b) {
  if ($b===null) return '—';
  $u = ['B','KB','MB','GB','TB','PB']; $i=0;
  while ($b>=1024 && $i<count((array)$u)-1) { $b/=1024; $i++; }
  return sprintf('%.1f %s', $b, $u[$i]);
}
function pct_class($p) {
  if ($p===null) return 'bar';
  if ($p>=80) return 'bar danger';
  if ($p>=60) return 'bar warn';
  return 'bar ok';
}
function pct($v) { return $v===null ? '—' : sprintf('%.1f%%', $v); }
function num0($v) { return $v===null ? '—' : number_format($v,0); }

$machine = isset($_GET['machine']) ? trim($_GET['machine']) : '';
$windows = ['1h'=>1, '24h'=>24, '7d'=>168];

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>GSP Stats Dashboard<?php if ($machine) { 
  $title_machine_info = q($mysqli, "SELECT hostname FROM {$TABLE_PREFIX}machines WHERE machine_id = ?", [$machine]);
  $title_hostname = $title_machine_info ? $title_machine_info[0]['hostname'] : $machine;
  echo " – ".htmlspecialchars($title_hostname);
} ?></title>
<?php if ($AUTO_REFRESH_SECONDS>0): ?>
<meta http-equiv="refresh" content="<?= (int)$AUTO_REFRESH_SECONDS ?>">
<?php endif; ?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
 :root { --bg:#0b0f14; --panel:#121821; --muted:#9bb0c3; --text:#e6eef6; --ok:#1f9d55; --warn:#d4a017; --danger:#d9534f; --accent:#4ea1ff; }
 body{background:var(--bg);color:var(--text);font:14px/1.45 system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:0;padding:24px;}
 a{color:var(--accent);text-decoration:none}
 h1,h2,h3{margin:0 0 12px 0}
 .wrap{max-width:1200px;margin:0 auto}
 .topbar{display:flex;align-items:center;gap:12px;margin-bottom:16px}
 .card{background:var(--panel);border-radius:14px;padding:16px;box-shadow:0 2px 12px rgba(0,0,0,.25)}
 .grid{display:grid;grid-template-columns:repeat(12,1fr);gap:16px}
 .col-3{grid-column:span 3} .col-4{grid-column:span 4} .col-6{grid-column:span 6} .col-12{grid-column:span 12}
 .muted{color:var(--muted)}
 .pill{display:inline-block;padding:2px 8px;border-radius:999px;background:#1a2330;color:#cfe2ff;border:1px solid #2a3a52}
 .kpi{font-size:22px;font-weight:700}
 .barwrap{background:#0e141d;border-radius:10px;height:12px;overflow:hidden}
 .bar{height:100%;display:block;width:0%;transition:width .5s ease;background:var(--ok)}
 .bar.warn{background:var(--warn)} .bar.danger{background:var(--danger)}
 table{width:100%;border-collapse:separate;border-spacing:0 8px}
 thead th{font-weight:600;color:#bcd; text-align:left; padding:8px}
 tbody td{padding:8px;background:var(--panel)}
 tbody tr{border-radius:10px}
 tbody tr td:first-child{border-top-left-radius:10px;border-bottom-left-radius:10px}
 tbody tr td:last-child{border-top-right-radius:10px;border-bottom-right-radius:10px}
 .right{text-align:right}
 .small{font-size:12px}
 .row{display:flex;gap:10px;flex-wrap:wrap}
 .btn{display:inline-block;padding:8px 10px;border:1px solid #2a3a52;border-radius:10px;background:#0e141d;color:#dfeaff}
 .btn.active{background:#1a2330}
 .split{display:flex;gap:16px;flex-wrap:wrap}
 .split > div{flex:1 1 280px}
 .subtle{opacity:.8}
 .hdr{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
 .sep{height:1px;background:#223143;margin:16px 0}
 .mono{font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace}
</style>
</head>
<body>
<div class="wrap">
  <div class="topbar">
    <h1>GSP Stats Dashboard</h1>
    <span class="pill"><?= $AUTO_REFRESH_SECONDS>0 ? "auto-refresh {$AUTO_REFRESH_SECONDS}s" : "manual refresh" ?></span>
    <a class="btn" href="?">All machines</a>
    <?php if($machine): ?>
      <a class="btn" href="?machine=<?= urlencode($machine) ?>">Refresh</a>
      <a class="btn" target="_blank" href="stats_aggregate.php?machine=<?= urlencode($machine) ?>">View raw JSON</a>
    <?php endif; ?>
  </div>

<?php
try {
  if (!$machine) {
    // list machines with last-ts
    $rows = q($mysqli, "SELECT m.machine_id, m.hostname,
      (SELECT MAX(ts) FROM {$TABLE_PREFIX}machine_samples s WHERE s.machine_id=m.machine_id) AS last_ts
      FROM {$TABLE_PREFIX}machines m ORDER BY m.created_at DESC");
    echo '<div class="grid">';
    foreach ((array)$rows as $r) {
      // Get latest machine sample for this machine to show current status
      $latest_sample = q($mysqli, "SELECT cpu_pct, mem_used_pct, disk_used_pct, load1 FROM {$TABLE_PREFIX}machine_samples WHERE machine_id=? ORDER BY ts DESC LIMIT 1", [$r['machine_id']]);
      $sample = $latest_sample ? $latest_sample[0] : null;
      
      echo '<div class="col-4 card">';
      echo '<div class="hdr"><h3 class="mono">'.htmlspecialchars($r['hostname']).'</h3><span class="muted">'.htmlspecialchars($r['machine_id']).'</span></div>';
      echo '<div class="small muted">Last sample: '.htmlspecialchars($r['last_ts'] ?: '—').'</div>';
      
      // Show current resource usage if available
      if ($sample) {
        echo '<div class="row" style="margin-top:8px;gap:8px;">';
        echo '<div style="flex:1"><div class="small muted">CPU</div><div class="small">'.pct($sample['cpu_pct']).'</div></div>';
        echo '<div style="flex:1"><div class="small muted">MEM</div><div class="small">'.pct($sample['mem_used_pct']).'</div></div>';
        echo '<div style="flex:1"><div class="small muted">DISK</div><div class="small">'.pct($sample['disk_used_pct']).'</div></div>';
        if ($sample['load1'] !== null) {
          echo '<div style="flex:1"><div class="small muted">LOAD</div><div class="small">'.number_format((float)$sample['load1'], 1).'</div></div>';
        }
        echo '</div>';
      }
      
      echo '<div style="margin-top:10px"><a class="btn" href="?machine='.urlencode($r['machine_id']).'">Open</a></div>';
      echo '</div>';
    }
    echo '</div>';
    echo '</div></body></html>'; exit;
  }

  // LAST sample
  $last = q($mysqli, "SELECT * FROM {$TABLE_PREFIX}machine_samples WHERE machine_id=? ORDER BY ts DESC LIMIT 1", [$machine]);
  $last = $last ? $last[0] : null;

  // Windows
  $agg = [];
  foreach ((array)$windows as $label=>$hours){
    $aggM = q($mysqli, "SELECT
              COUNT(*) n,
              AVG(cpu_pct) cpu_avg,
              AVG(mem_used_pct) mem_avg,
              AVG(disk_used_pct) disk_avg
            FROM {$TABLE_PREFIX}machine_samples
            WHERE machine_id=? AND ts >= (NOW() - INTERVAL {$hours} HOUR)", [$machine]);

    $netRows = q($mysqli, "SELECT ts, rx_bytes, tx_bytes, iface_speed_mbps
            FROM {$TABLE_PREFIX}machine_samples
            WHERE machine_id=? AND ts >= (NOW() - INTERVAL {$hours} HOUR)
            ORDER BY ts ASC", [$machine]);
    $net = ['avg_rx_Bps'=>null,'avg_tx_Bps'=>null,'avg_total_Bps'=>null,'avg_util_pct'=>null];
    if (count((array)$netRows)>=2) {
      $first = $netRows[0]; $lastn = $netRows[count((array)$netRows)-1];
      $secs = max(1, strtotime($lastn['ts']) - strtotime($first['ts']));
      $rx_bps = ((int)$lastn['rx_bytes'] - (int)$first['rx_bytes']) / $secs;
      $tx_bps = ((int)$lastn['tx_bytes'] - (int)$first['tx_bytes']) / $secs;
      $speed_mbps = $lastn['iface_speed_mbps'] ? (int)$lastn['iface_speed_mbps'] : null;
      $util_pct = null;
      if ($speed_mbps && $speed_mbps>0) {
        $capacity_Bps = ($speed_mbps * 1000000) / 8.0;
        $util_pct = (($rx_bps + $tx_bps) / $capacity_Bps) * 100.0;
      }
      $net = ['avg_rx_Bps'=>$rx_bps,'avg_tx_Bps'=>$tx_bps,'avg_total_Bps'=>$rx_bps+$tx_bps,'avg_util_pct'=>$util_pct];
    }

    $aggS = q($mysqli, "SELECT server_name,
               AVG(cpu_pct) cpu_avg,
               AVG(mem_pct) mem_avg,
               MAX(folder_size_bytes) folder_size_bytes
             FROM {$TABLE_PREFIX}process_samples
             WHERE machine_id=? AND ts >= (NOW() - INTERVAL {$hours} HOUR)
             GROUP BY server_name
             ORDER BY server_name ASC", [$machine]);

    $agg[$label] = ['machine'=>$aggM[0], 'net'=>$net, 'servers'=>$aggS];
  }

} catch (Throwable $e) {
  echo '<div class="card">Error: '.htmlspecialchars($e->getMessage()).'</div></div></body></html>'; exit;
}
?>

  <div class="grid">
    <div class="col-12 card">
      <div class="hdr">
        <?php 
        // Get machine info to show hostname prominently
        $machine_info = q($mysqli, "SELECT hostname FROM {$TABLE_PREFIX}machines WHERE machine_id = ?", [$machine]);
        $hostname = $machine_info ? $machine_info[0]['hostname'] : $machine;
        ?>
        <h2><?= htmlspecialchars($hostname) ?></h2>
        <div class="muted small">Machine ID: <?= htmlspecialchars($machine) ?> • Last sample: <?= htmlspecialchars($last['ts'] ?? '—') ?> • IF: <?= htmlspecialchars($last['net_iface'] ?? '—') ?></div>
      </div>
      <div class="split">
        <div>
          <div class="muted small">CPU (last)</div>
          <div class="kpi"><?= pct($last ? (float)$last['cpu_pct'] : null) ?></div>
          <div class="barwrap"><span class="<?= pct_class($last ? (float)$last['cpu_pct'] : null) ?>" style="width:<?= $last? min(100,max(0,(float)$last['cpu_pct'])):0 ?>%"></span></div>
        </div>
        <div>
          <div class="muted small">Memory used (last)</div>
          <div class="kpi"><?= pct($last ? (float)$last['mem_used_pct'] : null) ?></div>
          <div class="barwrap"><span class="<?= pct_class($last ? (float)$last['mem_used_pct'] : null) ?>" style="width:<?= $last? min(100,max(0,(float)$last['mem_used_pct'])):0 ?>%"></span></div>
        </div>
        <div>
          <div class="muted small">Disk used (last)</div>
          <div class="kpi"><?= pct($last ? (float)$last['disk_used_pct'] : null) ?></div>
          <div class="barwrap"><span class="<?= pct_class($last ? (float)$last['disk_used_pct'] : null) ?>" style="width:<?= $last? min(100,max(0,(float)$last['disk_used_pct'])):0 ?>%"></span></div>
          <div class="small subtle mono"><?= htmlspecialchars($last['disk_path'] ?? '') ?> • used <?= fmt_bytes($last['disk_used_bytes'] ?? null) ?> / <?= fmt_bytes($last['disk_total_bytes'] ?? null) ?></div>
        </div>
        <div>
          <div class="muted small">Load average (last)</div>
          <?php $load1 = $last ? (float)$last['load1'] : null; ?>
          <div class="kpi"><?= $load1 !== null ? number_format($load1, 1) : '—' ?></div>
          <div class="small subtle mono">1min: <?= $load1 !== null ? number_format($load1, 1) : '—' ?> • 5min: <?= $last && $last['load5'] !== null ? number_format((float)$last['load5'], 1) : '—' ?> • 15min: <?= $last && $last['load15'] !== null ? number_format((float)$last['load15'], 1) : '—' ?></div>
        </div>
        <div>
          <div class="muted small">Net avg util (1h)</div>
          <?php $nu = $agg['1h']['net']['avg_util_pct']; ?>
          <div class="kpi"><?= $nu===null?'—':sprintf('%.1f%%',$nu) ?></div>
          <div class="barwrap"><span class="<?= pct_class($nu) ?>" style="width:<?= $nu!==null ? min(100,max(0,$nu)) : 0 ?>%"></span></div>
          <div class="small subtle mono">rx <?= fmt_bytes($agg['1h']['net']['avg_rx_Bps']) ?>/s • tx <?= fmt_bytes($agg['1h']['net']['avg_tx_Bps']) ?>/s</div>
        </div>
      </div>
    </div>

    <?php foreach ((array)$windows as $label=>$hours): $m=$agg[$label]['machine']; ?>
      <div class="col-12 card">
        <div class="hdr">
          <h3>Window: <?= htmlspecialchars($label) ?></h3>
          <div class="row">
            <span class="small muted">AVG CPU</span>
            <div class="barwrap" style="width:160px;"><span class="<?= pct_class($m['cpu_avg']) ?>" style="width:<?= $m['cpu_avg']!==null?min(100,max(0,$m['cpu_avg'])):0 ?>%"></span></div>
            <span class="small"><?= pct($m['cpu_avg']) ?></span>
            <span class="small muted" style="margin-left:14px;">AVG MEM</span>
            <div class="barwrap" style="width:160px;"><span class="<?= pct_class($m['mem_avg']) ?>" style="width:<?= $m['mem_avg']!==null?min(100,max(0,$m['mem_avg'])):0 ?>%"></span></div>
            <span class="small"><?= pct($m['mem_avg']) ?></span>
            <span class="small muted" style="margin-left:14px;">AVG DISK</span>
            <div class="barwrap" style="width:160px;"><span class="<?= pct_class($m['disk_avg']) ?>" style="width:<?= $m['disk_avg']!==null?min(100,max(0,$m['disk_avg'])):0 ?>%"></span></div>
            <span class="small"><?= pct($m['disk_avg']) ?></span>
          </div>
        </div>

        <div class="sep"></div>

        <div class="tablewrap">
          <table>
            <thead>
              <tr>
                <th>Server</th>
                <th class="right">CPU avg</th>
                <th style="width:220px"></th>
                <th class="right">Mem avg</th>
                <th style="width:220px"></th>
                <th class="right">Folder size</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $rows = $agg[$label]['servers'];
              if (!$rows) {
                echo '<tr><td colspan="6" class="small muted">No server samples in this window.</td></tr>';
              } else {
                foreach ((array)$rows as $s) {
                  $cpu = $s['cpu_avg']!==null ? (float)$s['cpu_avg'] : null;
                  $mem = $s['mem_avg']!==null ? (float)$s['mem_avg'] : null;
                  echo '<tr>';
                  echo '<td class="mono">'.htmlspecialchars($s['server_name']).'</td>';
                  echo '<td class="right">'.pct($cpu).'</td>';
                  echo '<td><div class="barwrap"><span class="'.pct_class($cpu).'" style="width:'.($cpu!==null?min(100,max(0,$cpu)):0).'%"></span></div></td>';
                  echo '<td class="right">'.pct($mem).'</td>';
                  echo '<td><div class="barwrap"><span class="'.pct_class($mem).'" style="width:'.($mem!==null?min(100,max(0,$mem)):0).'%"></span></div></td>';
                  echo '<td class="right">'.fmt_bytes($s['folder_size_bytes']).'</td>';
                  echo '</tr>';
                }
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>
</body>
</html>
