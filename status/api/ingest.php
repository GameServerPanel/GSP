<?php
$cfg = require __DIR__ . '/config.php';
require __DIR__ . '/db.php';

header('Content-Type: application/json');

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) { http_response_code(400); echo json_encode(['error'=>'bad json']); exit; }

if (($data['token'] ?? '') !== $cfg['ingest_token']) {
  http_response_code(403); echo json_encode(['error'=>'forbidden']); exit;
}

$host_id  = trim($data['host_id'] ?? '');
$location = trim($data['location'] ?? '');
$machine  = $data['machine'] ?? null;
$procs    = $data['processes'] ?? [];

if ($host_id === '' || !$machine) {
  http_response_code(400); echo json_encode(['error'=>'missing host_id or machine']); exit;
}

$db = db_open($cfg);
$now = time();
$db->beginTransaction();

$db->prepare("
  INSERT INTO hosts(host_id, location, last_seen, last_up, alert_state)
  VALUES(?,?,?,?,?)
  ON CONFLICT(host_id) DO UPDATE SET location=excluded.location, last_seen=excluded.last_seen
")->execute([$host_id, $location, $now, 1, 'OK']);

$db->prepare("
  INSERT INTO host_metrics(host_id, ts, cpu_pct, mem_pct, disk_total_bytes, disk_free_bytes)
  VALUES (?,?,?,?,?,?)
")->execute([
  $host_id, $now,
  (float)$machine['cpu_pct'], (float)$machine['mem_pct'],
  (int)$machine['disk_total_bytes'], (int)$machine['disk_free_bytes']
]);

$insP = $db->prepare("
  INSERT INTO process_metrics(host_id, ts, server_id, pid, exe, cwd, cpu_cores, rss_bytes)
  VALUES (?,?,?,?,?,?,?,?)
");
foreach ((array)$procs as $p) {
  $insP->execute([
    $host_id, $now,
    (string)($p['server_id'] ?? ''),
    (int)$p['pid'], (string)$p['exe'], (string)$p['cwd'],
    (float)$p['cpu_cores'], (int)$p['rss_bytes']
  ]);
}

$db->prepare("INSERT INTO statuses(host_id, ts, up) VALUES (?,?,1)")->execute([$host_id, $now]);

$db->commit();

echo json_encode(['ok'=>true, 'stored_at'=>$now]);

