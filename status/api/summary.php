<?php
$cfg = require __DIR__ . '/config.php';
require __DIR__ . '/db.php';

if ($cfg['cors_allow_origin']) {
  header('Access-Control-Allow-Origin: '.$cfg['cors_allow_origin']);
  header('Vary: Origin');
}
header('Content-Type: application/json');

$db = db_open($cfg);
$grace = (int)$cfg['up_grace_seconds'];
$now   = time();

$rows = $db->query("
  SELECT h.host_id, h.location, h.last_seen,
         (SELECT cpu_pct FROM host_metrics WHERE host_id=h.host_id ORDER BY ts DESC LIMIT 1) AS cpu_pct,
         (SELECT mem_pct FROM host_metrics WHERE host_id=h.host_id ORDER BY ts DESC LIMIT 1) AS mem_pct
    FROM hosts h
    ORDER BY h.location, h.host_id
")->fetchAll();

$out = [];
foreach ($rows as $r) {
  $up = ($now - (int)$r['last_seen']) <= $grace;
  $out[] = [
    'host_id'   => $r['host_id'],
    'location'  => $r['location'],
    'status'    => $up ? 'UP' : 'DOWN',
    'cpu_pct'   => is_null($r['cpu_pct']) ? null : round($r['cpu_pct'],1),
    'mem_pct'   => is_null($r['mem_pct']) ? null : round($r['mem_pct'],1),
    'last_seen' => (int)$r['last_seen'],
  ];
}

echo json_encode(['generated_at'=>$now, 'hosts'=>$out]);

