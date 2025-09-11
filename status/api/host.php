<?php

/*

5) Panel API — per-host detail (optional)

/var/www/html/panel/status/api/host.php?host_id=KC
*/
$cfg = require __DIR__ . '/config.php';
require __DIR__ . '/db.php';
header('Content-Type: application/json');

$host_id = $_GET['host_id'] ?? '';
if ($host_id===''){ http_response_code(400); echo json_encode(['error'=>'host_id required']); exit; }

$db = db_open($cfg);
$grace = (int)$cfg['up_grace_seconds'];
$now=time();

$h = $db->prepare("SELECT * FROM hosts WHERE host_id=?");
$h->execute([$host_id]);
$host = $h->fetch();
if (!$host){ http_response_code(404); echo json_encode(['error'=>'unknown host']); exit; }

$up = ($now - (int)$host['last_seen']) <= $grace;

$metrics = $db->prepare("
  SELECT ts, cpu_pct, mem_pct, disk_total_bytes, disk_free_bytes
  FROM host_metrics WHERE host_id=? ORDER BY ts DESC LIMIT 1
"); $metrics->execute([$host_id]); $m = $metrics->fetch();

$procs = $db->prepare("
  SELECT server_id, pid, exe, cwd, cpu_cores, rss_bytes
  FROM process_metrics WHERE host_id=? AND ts=(SELECT MAX(ts) FROM process_metrics WHERE host_id=?)
"); $procs->execute([$host_id,$host_id]); $plist = $procs->fetchAll();

echo json_encode([
  'host'    => ['host_id'=>$host['host_id'], 'location'=>$host['location'], 'status'=>$up?'UP':'DOWN', 'last_seen'=>(int)$host['last_seen']],
  'metrics' => $m ?: null,
  'processes' => array_map(function($p){
      $p['rss_mib'] = round($p['rss_bytes']/1048576,1);
      return $p;
    }, $plist),
]);

