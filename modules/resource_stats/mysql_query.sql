-- Database: gs_metrics  (create and grant user as needed)
CREATE DATABASE IF NOT EXISTS gs_metrics CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Example user:
-- CREATE USER 'gs_metrics'@'%' IDENTIFIED BY 'REPLACE_ME';
-- GRANT ALL PRIVILEGES ON gs_metrics.* TO 'gs_metrics'@'%';
-- FLUSH PRIVILEGES;

USE gs_metrics;

CREATE TABLE IF NOT EXISTS machines (
  id INT AUTO_INCREMENT PRIMARY KEY,
  machine_id VARCHAR(64) NOT NULL,
  hostname   VARCHAR(255) NOT NULL,
  ip         VARCHAR(45) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_machine (machine_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS machine_samples (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  machine_id VARCHAR(64) NOT NULL,
  ts DATETIME NOT NULL,
  load1 DECIMAL(6,2),
  load5 DECIMAL(6,2),
  load15 DECIMAL(6,2),
  cpu_pct DECIMAL(6,2),
  mem_used_bytes BIGINT,
  mem_total_bytes BIGINT,
  mem_used_pct DECIMAL(6,2),
  swap_used_bytes BIGINT,
  swap_total_bytes BIGINT,
  disk_path VARCHAR(255),
  disk_total_bytes BIGINT,
  disk_used_bytes BIGINT,
  disk_used_pct DECIMAL(6,2),
  net_iface VARCHAR(64),
  rx_bytes BIGINT,
  tx_bytes BIGINT,
  iface_speed_mbps INT NULL,
  KEY idx_machine_ts (machine_id, ts),
  KEY idx_ts (ts)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS process_samples (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  machine_id VARCHAR(64) NOT NULL,
  ts DATETIME NOT NULL,
  server_name VARCHAR(255) NOT NULL,
  server_path VARCHAR(512) NOT NULL,
  pid INT NOT NULL,
  proc_name VARCHAR(255),
  cmd TEXT,
  cpu_pct DECIMAL(7,2),
  rss_bytes BIGINT,
  vms_bytes BIGINT,
  mem_pct DECIMAL(6,2),
  io_read_bytes BIGINT,
  io_write_bytes BIGINT,
  open_fds INT,
  listening_ports VARCHAR(255),
  folder_size_bytes BIGINT,
  KEY idx_proc_server (machine_id, server_name, ts),
  KEY idx_proc_pid (machine_id, pid, ts),
  KEY idx_ts (ts)
) ENGINE=InnoDB;
