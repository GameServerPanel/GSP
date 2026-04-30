-- GSP Steam Workshop – database-driven tables
-- Run once against your panel database (replace `gsp_` with your table_prefix if different).

-- -------------------------------------------------------
-- Workshop game profiles (one row per supported game)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `gsp_workshop_game_profiles` (
  `id`                    INT          NOT NULL AUTO_INCREMENT,
  `game_key`              VARCHAR(100) NOT NULL,
  `game_name`             VARCHAR(255) NOT NULL,
  `workshop_app_id`       VARCHAR(32)  NOT NULL,
  `supported_os`          SET('linux','windows') NOT NULL DEFAULT 'linux',
  `cache_path_template`   TEXT         NOT NULL,
  `install_path_template` TEXT         NOT NULL,
  `folder_name_template`  VARCHAR(255) NOT NULL DEFAULT '@{mod_id}',
  `copy_method`           ENUM('rsync','robocopy','custom_script') NOT NULL DEFAULT 'rsync',
  `install_script`        TEXT         NULL,
  `config_file_template`  TEXT         NULL,
  `launch_param_template` TEXT         NULL,
  `requires_restart`      TINYINT(1)   NOT NULL DEFAULT 1,
  `enabled`               TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            DATETIME     NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_game_key` (`game_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Per-agent workshop download cache
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `gsp_workshop_cache` (
  `id`               INT          NOT NULL AUTO_INCREMENT,
  `agent_id`         INT          NOT NULL,
  `os_type`          ENUM('linux','windows') NOT NULL DEFAULT 'linux',
  `workshop_app_id`  VARCHAR(32)  NOT NULL,
  `workshop_id`      VARCHAR(64)  NOT NULL,
  `title`            VARCHAR(255) NULL,
  `cache_path`       TEXT         NOT NULL,
  `status`           ENUM('missing','cached','failed') NOT NULL DEFAULT 'missing',
  `last_checked`     DATETIME     NULL,
  `last_updated`     DATETIME     NULL,
  `last_error`       TEXT         NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_agent_workshop` (`agent_id`, `workshop_app_id`, `workshop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Per-server installed Workshop mods
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `gsp_server_workshop_mods` (
  `id`               INT          NOT NULL AUTO_INCREMENT,
  `home_id`          INT          NOT NULL,
  `agent_id`         INT          NOT NULL,
  `profile_id`       INT          NOT NULL,
  `workshop_app_id`  VARCHAR(32)  NOT NULL,
  `workshop_id`      VARCHAR(64)  NOT NULL,
  `title`            VARCHAR(255) NULL,
  `enabled`          TINYINT(1)   NOT NULL DEFAULT 1,
  `install_path`     TEXT         NOT NULL,
  `load_order`       INT          NOT NULL DEFAULT 0,
  `installed_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME     NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_home_workshop` (`home_id`, `workshop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
