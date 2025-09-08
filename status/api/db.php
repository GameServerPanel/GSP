<?php
function db_open(array $cfg): PDO {
  $path = $cfg['db_path'];
  $init = !file_exists($path);
  $pdo = new PDO('sqlite:' . $path, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  if ($init) db_init($pdo);
  return $pdo;
}

function db_init(PDO $db): void {
  $db->exec("
    PRAGMA journal_mode=WAL;
    CREATE TABLE hosts (
      host_id TEXT PRIMARY KEY,
      location TEXT,
      last_seen INTEGER,
      last_up INTEGER DEFAULT 1,
      alert_state TEXT DEFAULT 'OK'  -- OK|DOWN|WARN
    );
    CREATE TABLE host_metrics (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      host_id TEXT,
      ts INTEGER,
      cpu_pct REAL,
      mem_pct REAL,
      disk_total_bytes INTEGER,
      disk_free_bytes INTEGER
    );
    CREATE INDEX idx_host_metrics ON host_metrics(host_id, ts);

    CREATE TABLE process_metrics (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      host_id TEXT,
      ts INTEGER,
      server_id TEXT,   -- numeric folder name under /home/gameserver/
      pid INTEGER,
      exe TEXT,
      cwd TEXT,
      cpu_cores REAL,   -- fraction of a core (e.g., 0.35)
      rss_bytes INTEGER
    );
    CREATE INDEX idx_proc_metrics ON process_metrics(host_id, server_id, ts);

    CREATE TABLE statuses (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      host_id TEXT,
      ts INTEGER,
      up INTEGER  -- 1 up, 0 down (by heartbeat)
    );
    CREATE INDEX idx_status ON statuses(host_id, ts);
  ");
}

