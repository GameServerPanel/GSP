
<?php
// status_api.php — public, read-only JSON status pulled from OGP Agents.
// Drop into your OGP panel root. Protect with a token (query: ?token=...).
// Caches results for 30s to avoid hammering agents.

define('IN_OGP', true);
require_once __DIR__.'/includes/config.inc.php';
require_once __DIR__.'/includes/functions.php';
require_once __DIR__.'/includes/lib_remote.php'; // OGPRemoteLibrary
require_once __DIR__.'/includes/classes/db.php'; // $db

/* ========== CONFIG ========== */
$PUBLIC_TOKEN = 'CHANGE_ME_LONG_RANDOM';    // set a random token and pass ?token=... from the website
$CACHE_TTL    = 30;                         // seconds
$LOCATION_MAP = [                           // optional: map hostnames/IPs to friendly names
  'nyc.iaregamer.com' => 'NYC',
  'kc.iaregamer.com'  => 'KC',
  'kc2.iaregamer.com' => 'KC2',
  'la.iaregamer.com'  => 'LA',
  'france.iaregamer.com' => 'France',
  'ireland.iaregamer.com' => 'Ireland',
  'atl.iaregamer.com' => 'Atlanta',
  'lab.iaregamer.com' => 'Lab',
];
/* ============================ */

if (!isset($_GET['token']) || $_GET['token'] !== $PUBLIC_TOKEN) {
  http_response_code(403);
  header('Content-Type: application/json');
  echo json_encode(['error'=>'forbidden']);
  exit;
}

header('Content-Type: application/json; charset=utf-8');

// Simple cache file (panel-local)
$cache_dir = __DIR__.'/_cache';
@mkdir($cache_dir, 0775, true);
$cache_file = $cache_dir.'/status_api.json';

if (is_file($cache_file) && (time() - filemtime($cache_file) <= $CACHE_TTL)) {
  readfile($cache_file);
  exit;
}

// Helper: quick TCP test to agent
function agent_up($ip, $port, $timeout=1.0) {
  $errno=0; $err=''; $s = @fsockopen($ip, (int)$port, $errno, $err, $timeout);
  if ($s) { fclose($s); return true; }
  return false;
}

// Try to pull system stats from the agent using known method names.
// Different OGP versions/agents expose slightly different method names/keys.
function fetch_agent_stats($remote) {
  $methods = [
    'get_system_stats',      // preferred if available
    'system_stats',
    'get_agent_stats',
    'sysinfo',
    'agent_status',
    'monitor_get_stats',
  ];
  foreach ((array)$methods as $m) {
    if (method_exists($remote, $m)) {
      $out = @call_user_func([$remote, $m]);
      if (is_array($out) && !empty($out)) return $out;
    }
  }
  return null;
}

// Normalize various shapes into cpu%, mem%, disk%
function normalize_stats($raw) {
  $cpu = null; $mem = null; $disk = null;

  // CPU
  foreach (['cpu_percent','cpu_used','cpu_usage','cpu'] as $k) {
    if (isset($raw[$k]) && is_numeric($raw[$k])) { $cpu = (float)$raw[$k]; break; }
  }
  // Some agents return load avg (0.00–N cores). Convert approx to %
  if ($cpu === null && isset($raw['load'])) {
    $ld = (float)$raw['load']; $cores = (int)@shell_exec('nproc 2>/dev/null') ?: 1;
    $cpu = max(0.0, min(100.0, ($ld / max(1,$cores))*100.0));
  }

  // MEM
  foreach (['mem_percent','mem_used_percent','memory_percent','mem'] as $k) {
    if (isset($raw[$k]) && is_numeric($raw[$k])) { $mem = (float)$raw[$k]; break; }
  }
  if ($mem === null && isset($raw['mem_used']) && isset($raw['mem_total'])) {
    $u = (float)$raw['mem_used']; $t=(float)$raw['mem_total'];
    if ($t>0) $mem = ($u/$t)*100.0;
  } elseif ($mem === null && isset($raw['mem_free']) && isset($raw['mem_total'])) {
    $f = (float)$raw['mem_free']; $t=(float)$raw['mem_total'];
    if ($t>0) $mem = (1.0 - $f/$t)*100.0;
  }

  // DISK (pick root or the highest-used mount)
  // Common shapes: disk_percent / hdd_used_percent / arrays with total/free per mount
  foreach (['disk_percent','hdd_used_percent'] as $k) {
    if (isset($raw[$k]) && is_numeric($raw[$k])) { $disk = (float)$raw[$k]; break; }
  }
  if ($disk === null) {
    // try totals
    $candidates = [
      ['used'=>'disk_used','total'=>'disk_total'],
      ['used'=>'hdd_used','total'=>'hdd_total'],
      ['used'=>'fs_used','total'=>'fs_total'],
    ];
    foreach ((array)$candidates as $pair) {
      $u = $raw[$pair['used']]  ?? null;
      $t = $raw[$pair['total']] ?? null;
      if (is_numeric($u) && is_numeric($t) && $t>0) { $disk = ((float)$u/(float)$t)*100.0; break; }
    }
    // if agent returns array of mounts, pick max %
    if ($disk === null && isset($raw['filesystems']) && is_array($raw['filesystems'])) {
      $mx = null;
      foreach ((array)$raw['filesystems'] as $fs) {
        if (isset($fs['used']) && isset($fs['total']) && $fs['total']>0) {
          $pct = ($fs['used']/$fs['total'])*100.0;
          if ($mx === null || $pct>$mx) $mx = $pct;
        } elseif (isset($fs['percent'])) {
          $mx = max($mx??0, (float)$fs['percent']);
        }
      }
      if ($mx !== null) $disk = $mx;
    }
  }

  return [
    'cpu_percent'  => ($cpu !== null) ? round($cpu,1) : null,
    'mem_percent'  => ($mem !== null) ? round($mem,1) : null,
    'disk_percent' => ($disk!== null) ? round($disk,1): null,
  ];
}

// Pull remote hosts list (OGP DB)
$rows = [];
if (method_exists($db, 'getRemoteServers')) {
  $rows = $db->getRemoteServers();
} else {
  // Fallback SQL (prefix best-effort)
  $prefix = defined('OGP_DB_PREFIX') ? OGP_DB_PREFIX : (defined('OGP_DB_TABLE_PREFIX') ? OGP_DB_TABLE_PREFIX : 'ogp_');
  $rows = $db->resultQuery("SELECT remote_server_id, hostname, agent_ip, agent_port FROM {$prefix}remote_servers ORDER BY hostname");
}

$out = ['generated_at'=>gmdate('c'), 'nodes'=>[]];

foreach ((array)$rows as $h) {
  $agent_ip   = $h['agent_ip']   ?? $h['hostname'] ?? '127.0.0.1';
  $agent_port = (int)($h['agent_port'] ?? 12679);
  $label_key  = $h['hostname']   ?? $agent_ip;
  $name       = $LOCATION_MAP[$label_key] ?? $label_key;

  $up = agent_up($agent_ip, $agent_port);
  $stats = null;

  if ($up) {
    try {
      // OGPRemoteLibrary signature normally: (ip, port, encryption_key) — falls back to global if omitted.
      $remote = new OGPRemoteLibrary($agent_ip, $agent_port);
      $raw    = fetch_agent_stats($remote);
      if (is_array($raw)) $stats = normalize_stats($raw);
    } catch (Throwable $e) {
      $stats = null;
    }
  }

  $out['nodes'][] = [
    'name'  => $name,
    'host'  => $label_key,
    'agent_port' => $agent_port,
    'online' => $up,
    'cpu_percent'  => $stats['cpu_percent']  ?? null,
    'mem_percent'  => $stats['mem_percent']  ?? null,
    'disk_percent' => $stats['disk_percent'] ?? null,
  ];
}

// Cache & print
$tmp = $cache_file.'.tmp';
file_put_contents($tmp, json_encode($out, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
@rename($tmp, $cache_file);
echo json_encode($out, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);

