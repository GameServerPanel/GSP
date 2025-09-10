<?php
// stats_aggregate.php — call: ?machine=HOSTNAME_OR_ID[&format=html]

/********** CONFIG (panel DB) **********/
$db = [
  'host' => '127.0.0.1',
  'user' => 'panel_user',
  'pass' => 'REPLACE_ME',
  'name' => 'panel_database'
];
$TABLE_PREFIX = 'gsp_';
$DISCORD_WEBHOOK = 'https://discord.com/api/webhooks/REPLACE_ME';
$ALERT_THRESHOLD = 80.0;
/***************************************/

$format  = $_GET['format'] ?? 'json';
$machine = $_GET['machine'] ?? null;

if ($format === 'html') header('Content-Type: text/html'); else header('Content-Type: application/json');

$mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
if ($mysqli->connect_errno) {
  http_response_code(500);
  echo json_encode(['error' => 'DB connect failed', 'detail' => $mysqli->connect_error]); exit;
}
$mysqli->set_charset("utf8mb4");

function q($mysqli, $sql, $params=[]) {
  $stmt = $mysqli->prepare($sql);
  if(!$stmt){ throw new Exception($mysqli->error); }
  if(!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();
  $res = $stmt->get_result();
  $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
  $stmt->close();
  return $rows;
}
function send_discord($url, $content) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['content' => $content]));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $resp = curl_exec($ch); $err = curl_error($ch); curl_close($ch);
  return [$resp, $err];
}
function window_clause($hours) {
  return "ts >= (NOW() - INTERVAL {$hours} HOUR)";
}

try {
  if(!$machine){
    $rows = q($mysqli, "SELECT machine_id, hostname, created_at FROM {$TABLE_PREFIX}machines ORDER BY created_at DESC");
    $payload = ['machines' => $rows];
    echo $format==='html' ? "<pre>".htmlspecialchars(json_encode($payload, JSON_PRETTY_PRINT))."</pre>" : json_encode($payload, JSON_PRETTY_PRINT);
    exit;
  }

  $out = ['machine' => $machine, 'windows' => []];

  $lastM = q($mysqli,
    "SELECT * FROM {$TABLE_PREFIX}machine_samples WHERE machine_id=? ORDER BY ts DESC LIMIT 1",
    [$machine]
  );
  $lastTs = $lastM ? $lastM[0]['ts'] : null;

  $lastServers = q($mysqli, "
    SELECT server_name,
           SUM(cpu_pct) AS cpu_pct_sum,
           AVG(mem_pct) AS mem_pct_avg,
           MAX(folder_size_bytes) AS folder_size_bytes,
           GROUP_CONCAT(DISTINCT pid ORDER BY pid) AS pids
    FROM {$TABLE_PREFIX}process_samples
    WHERE machine_id=? AND ts=(SELECT MAX(ts) FROM {$TABLE_PREFIX}process_samples WHERE machine_id=?)
    GROUP BY server_name
    ORDER BY server_name ASC
  ", [$machine, $machine]);

  $out['last'] = [
    'ts' => $lastTs,
    'machine' => $lastM ? [
      'load1' => (float)$lastM[0]['load1'],
      'load5' => (float)$lastM[0]['load5'],
      'load15'=> (float)$lastM[0]['load15'],
      'cpu_pct'=> (float)$lastM[0]['cpu_pct'],
      'mem_used_pct'=> (float)$lastM[0]['mem_used_pct'],
      'disk_used_pct'=> (float)$lastM[0]['disk_used_pct'],
      'net_iface'=> $lastM[0]['net_iface'],
      'rx_bytes'=> (int)$lastM[0]['rx_bytes'],
      'tx_bytes'=> (int)$lastM[0]['tx_bytes'],
      'iface_speed_mbps'=> isset($lastM[0]['iface_speed_mbps']) ? (int)$lastM[0]['iface_speed_mbps'] : null
    ] : null,
    'servers' => $lastServers
  ];

  $windows = [ '1h'=>1, '24h'=>24, '7d'=>24*7 ];
  foreach($windows as $label=>$hours){
    $aggM = q($mysqli, "
      SELECT COUNT(*) AS n,
             AVG(cpu_pct)      AS cpu_avg,
             AVG(mem_used_pct) AS mem_avg,
             AVG(disk_used_pct)AS disk_avg
      FROM {$TABLE_PREFIX}machine_samples
      WHERE machine_id=? AND ".window_clause($hours),
      [$machine]
    );

    $netRows = q($mysqli, "
      SELECT ts, rx_bytes, tx_bytes, iface_speed_mbps
      FROM {$TABLE_PREFIX}machine_samples
      WHERE machine_id=? AND ".window_clause($hours)."
      ORDER BY ts ASC
    ", [$machine]);

    $net = null;
    if(count($netRows) >= 2){
      $first = $netRows[0]; $last = $netRows[count($netRows)-1];
      $secs = max(1, strtotime($last['ts']) - strtotime($first['ts']));
      $rx_bps = ((int)$last['rx_bytes'] - (int)$first['rx_bytes']) / $secs;
      $tx_bps = ((int)$last['tx_bytes'] - (int)$first['tx_bytes']) / $secs;
      $speed_mbps = $last['iface_speed_mbps'] ? (int)$last['iface_speed_mbps'] : null;
      $util_pct = null;
      if($speed_mbps && $speed_mbps > 0){
        $capacity_Bps = ($speed_mbps * 1000000) / 8.0;
        $util_pct = (($rx_bps + $tx_bps) / $capacity_Bps) * 100.0;
      }
      $net = [
        'avg_rx_Bps' => $rx_bps,
        'avg_tx_Bps' => $tx_bps,
        'avg_total_Bps' => $rx_bps + $tx_bps,
        'avg_util_pct'  => $util_pct
      ];
    }

    $aggS = q($mysqli, "
      SELECT server_name,
             AVG(cpu_pct) AS cpu_avg,
             AVG(mem_pct) AS mem_avg,
             MAX(folder_size_bytes) AS folder_size_bytes
      FROM {$TABLE_PREFIX}process_samples
      WHERE machine_id=? AND ".window_clause($hours)."
      GROUP BY server_name
      ORDER BY server_name ASC
    ", [$machine]);

    $out['windows'][$label] = [
      'machine' => [
        'cpu_avg'  => isset($aggM[0]['cpu_avg']) ? (float)$aggM[0]['cpu_avg'] : null,
        'mem_avg'  => isset($aggM[0]['mem_avg']) ? (float)$aggM[0]['mem_avg'] : null,
        'disk_avg' => isset($aggM[0]['disk_avg']) ? (float)$aggM[0]['disk_avg'] : null,
        'net'      => $net
      ],
      'servers' => $aggS
    ];
  }

  $alerts = [];
  foreach (['1h','24h'] as $w) {
    $mw = $out['windows'][$w]['machine'];
    if ($mw['cpu_avg'] !== null && $mw['cpu_avg'] >= $ALERT_THRESHOLD) $alerts[] = "Machine CPU avg {$w}: ".round($mw['cpu_avg'],1)."%";
    if ($mw['mem_avg'] !== null && $mw['mem_avg'] >= $ALERT_THRESHOLD) $alerts[] = "Machine MEM avg {$w}: ".round($mw['mem_avg'],1)."%";
    if ($mw['disk_avg'] !== null && $mw['disk_avg'] >= $ALERT_THRESHOLD) $alerts[] = "Machine DISK avg {$w}: ".round($mw['disk_avg'],1)."%";
    if (isset($mw['net']['avg_util_pct']) && $mw['net']['avg_util_pct'] !== null && $mw['net']['avg_util_pct'] >= $ALERT_THRESHOLD) {
      $alerts[] = "Machine NET util avg {$w}: ".round($mw['net']['avg_util_pct'],1)."%";
    }
    foreach ($out['windows'][$w]['servers'] as $s) {
      if ($s['cpu_avg'] !== null && (float)$s['cpu_avg'] >= $ALERT_THRESHOLD)
        $alerts[] = "Server '{$s['server_name']}' CPU avg {$w}: ".round($s['cpu_avg'],1)."%";
      if ($s['mem_avg'] !== null && (float)$s['mem_avg'] >= $ALERT_THRESHOLD)
        $alerts[] = "Server '{$s['server_name']}' MEM avg {$w}: ".round($s['mem_avg'],1)."%";
    }
  }
  if (!empty($alerts) && !empty($DISCORD_WEBHOOK) && strpos($DISCORD_WEBHOOK, 'REPLACE_ME') === false) {
    $msg = ":warning: **$machine** threshold(s) >= {$ALERT_THRESHOLD}%:\n- " . implode("\n- ", $alerts);
    send_discord($DISCORD_WEBHOOK, $msg);
    $out['alerts_sent'] = $alerts;
  } else {
    $out['alerts_sent'] = [];
  }

  $json = json_encode($out, JSON_PRETTY_PRINT);
  echo $format==='html' ? "<pre>".htmlspecialchars($json)."</pre>" : $json;

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Exception', 'detail' => $e->getMessage()]);
} finally {
  $mysqli->close();
}
