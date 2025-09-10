-- Resource Monitoring Schema for OGP
-- This file creates the tables needed for resource monitoring and Discord alerting

-- Table structure for table `ogp_resource_monitoring`
CREATE TABLE IF NOT EXISTS `ogp_resource_monitoring` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `remote_server_id` int(11) NOT NULL,
  `home_id` int(11) DEFAULT NULL COMMENT 'NULL for system-wide metrics',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cpu_usage` decimal(5,2) DEFAULT NULL COMMENT 'CPU usage percentage (0.00-100.00)',
  `memory_usage` decimal(5,2) DEFAULT NULL COMMENT 'Memory usage percentage (0.00-100.00)', 
  `memory_used_mb` int(11) DEFAULT NULL COMMENT 'Memory used in MB',
  `memory_total_mb` int(11) DEFAULT NULL COMMENT 'Total memory in MB',
  `disk_usage` decimal(5,2) DEFAULT NULL COMMENT 'Disk usage percentage (0.00-100.00)',
  `disk_used_mb` bigint(20) DEFAULT NULL COMMENT 'Disk used in MB',
  `disk_total_mb` bigint(20) DEFAULT NULL COMMENT 'Total disk in MB',
  `process_count` int(11) DEFAULT NULL COMMENT 'Number of processes (for game servers)',
  `network_rx_mb` bigint(20) DEFAULT NULL COMMENT 'Network received in MB',
  `network_tx_mb` bigint(20) DEFAULT NULL COMMENT 'Network transmitted in MB',
  PRIMARY KEY (`id`),
  KEY `idx_server_timestamp` (`remote_server_id`, `timestamp`),
  KEY `idx_home_timestamp` (`home_id`, `timestamp`),
  KEY `idx_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Resource monitoring data for servers and game instances';

-- Table structure for table `ogp_resource_alerts`
CREATE TABLE IF NOT EXISTS `ogp_resource_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `remote_server_id` int(11) NOT NULL,
  `home_id` int(11) DEFAULT NULL COMMENT 'NULL for system-wide alerts',
  `alert_type` enum('cpu','memory','disk') NOT NULL,
  `threshold_percentage` decimal(5,2) NOT NULL DEFAULT 80.00,
  `duration_minutes` int(11) NOT NULL DEFAULT 30,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `discord_webhook_url` varchar(500) DEFAULT NULL,
  `last_triggered` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_server_active` (`remote_server_id`, `is_active`),
  KEY `idx_home_active` (`home_id`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Alert configurations and state for resource monitoring';

-- Table structure for table `ogp_resource_alert_history`
CREATE TABLE IF NOT EXISTS `ogp_resource_alert_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alert_id` int(11) NOT NULL,
  `remote_server_id` int(11) NOT NULL,
  `home_id` int(11) DEFAULT NULL,
  `alert_type` enum('cpu','memory','disk') NOT NULL,
  `triggered_value` decimal(5,2) NOT NULL,
  `threshold_value` decimal(5,2) NOT NULL,
  `duration_exceeded` int(11) NOT NULL COMMENT 'Duration in minutes that threshold was exceeded',
  `message_sent` tinyint(1) NOT NULL DEFAULT 0,
  `discord_response` text DEFAULT NULL,
  `triggered_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_alert_triggered` (`alert_id`, `triggered_at`),
  KEY `idx_server_triggered` (`remote_server_id`, `triggered_at`),
  CONSTRAINT `fk_resource_alert_history_alert` FOREIGN KEY (`alert_id`) REFERENCES `ogp_resource_alerts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='History of triggered resource alerts';

-- Table structure for table `ogp_discord_settings`
CREATE TABLE IF NOT EXISTS `ogp_discord_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_name` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_setting` (`setting_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Discord integration settings';

-- Default Discord settings
INSERT INTO `ogp_discord_settings` (setting_name, setting_value, description) VALUES 
('default_webhook_url', '', 'Default Discord webhook URL for alerts'),
('alert_enabled', '1', 'Enable/disable Discord alerts (1=enabled, 0=disabled)'),
('alert_format', 'json', 'Format for Discord messages (json or embed)'),
('bot_username', 'OGP Monitor', 'Username displayed for the bot in Discord'),
('alert_cooldown_minutes', '60', 'Minimum minutes between identical alerts')
ON DUPLICATE KEY UPDATE
  setting_value = VALUES(setting_value),
  description = VALUES(description);

-- Note: Automatic cleanup can be configured via cron job or panel cleanup script
-- Example cron job command:
-- 0 2 * * * mysql -u localuser -p panel -e "DELETE FROM ogp_resource_monitoring WHERE timestamp < DATE_SUB(NOW(), INTERVAL 30 DAY); DELETE FROM ogp_resource_alert_history WHERE triggered_at < DATE_SUB(NOW(), INTERVAL 90 DAY);"