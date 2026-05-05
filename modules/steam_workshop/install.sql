-- GSP Steam Workshop – Manual SQL Reference
-- =========================================
-- Replace PREFIX_ with your actual table prefix (e.g. gsp_).
-- Compatible with MySQL 5.7 and MySQL 8.0.
-- Do NOT hardcode any database name here.
-- Run in the panel database.

-- ── Drop legacy tables (if upgrading from the old adapter-based implementation) ──
DROP TABLE IF EXISTS `PREFIX_workshop_game_profiles`;
DROP TABLE IF EXISTS `PREFIX_workshop_cache`;
DROP TABLE IF EXISTS `PREFIX_server_workshop_mods`;
DROP TABLE IF EXISTS `PREFIX_server_workshop_settings`;

-- ── Create new tables ─────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `PREFIX_steam_workshop_game_profiles` (
  `id`                              INT           NOT NULL AUTO_INCREMENT,
  `config_name`                     VARCHAR(100)  NOT NULL,
  `game_name`                       VARCHAR(255)  NOT NULL DEFAULT '',
  `enabled`                         TINYINT(1)    NOT NULL DEFAULT 0,
  `steam_app_id`                    VARCHAR(32)   NOT NULL DEFAULT '',
  `workshop_app_id`                 VARCHAR(32)   NOT NULL DEFAULT '',
  `steam_login_required`            TINYINT(1)    NOT NULL DEFAULT 0,
  `steamcmd_login_mode`             ENUM('anonymous','account') NOT NULL DEFAULT 'anonymous',
  `steamcmd_path`                   VARCHAR(512)  NOT NULL DEFAULT '/home/gameserver/steamcmd/steamcmd.sh',
  `workshop_download_dir_template`  TEXT          NULL,
  `server_root_template`            TEXT          NULL,
  `install_path_template`           TEXT          NULL,
  `folder_naming_format`            VARCHAR(64)   NOT NULL DEFAULT '@{MOD_NAME}',
  `mod_launch_param_template`       VARCHAR(255)  NOT NULL DEFAULT '-mod=',
  `servermod_launch_param_template` VARCHAR(255)  NOT NULL DEFAULT '-serverMod=',
  `install_script_template`         TEXT          NULL,
  `update_script_template`          TEXT          NULL,
  `copy_bikeys_enabled`             TINYINT(1)    NOT NULL DEFAULT 1,
  `notes`                           TEXT          NULL,
  `created_at`                      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`                      DATETIME      NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_config_name` (`config_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `PREFIX_steam_workshop_server_mods` (
  `id`               INT          NOT NULL AUTO_INCREMENT,
  `home_id`          INT          NOT NULL,
  `profile_id`       INT          NOT NULL,
  `workshop_id`      VARCHAR(64)  NOT NULL,
  `mod_name`         VARCHAR(255) NOT NULL DEFAULT '',
  `folder_name`      VARCHAR(255) NOT NULL DEFAULT '',
  `mod_type`         ENUM('client','server') NOT NULL DEFAULT 'client',
  `sort_order`       INT          NOT NULL DEFAULT 0,
  `enabled`          TINYINT(1)   NOT NULL DEFAULT 1,
  `install_status`   VARCHAR(32)  NOT NULL DEFAULT '',
  `last_installed_at` DATETIME    NULL,
  `last_updated_at`  DATETIME     NULL,
  `last_error`       TEXT         NULL,
  `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME     NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_home_workshop` (`home_id`, `workshop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Example: DayZ profile ────────────────────────────────────────────────
-- After running the above, insert an example DayZ profile.
-- Adjust config_name to match your actual DayZ game_key from config_homes.
-- (Run `SELECT game_key, game_name FROM PREFIX_config_homes WHERE game_name LIKE '%DayZ%';`
--  to find the right config_name.)
--
-- INSERT INTO `PREFIX_steam_workshop_game_profiles`
--   (`config_name`, `game_name`, `enabled`,
--    `steam_app_id`, `workshop_app_id`,
--    `steamcmd_path`,
--    `workshop_download_dir_template`,
--    `server_root_template`,
--    `install_path_template`,
--    `folder_naming_format`,
--    `mod_launch_param_template`,
--    `servermod_launch_param_template`,
--    `copy_bikeys_enabled`)
-- VALUES
--   ('dayz_win64', 'DayZ', 1,
--    '223350', '221100',
--    '/home/gameserver/steamcmd/steamcmd.sh',
--    '{SERVER_ROOT}/steamapps/workshop/content/{WORKSHOP_APP_ID}',
--    '/home/gameserver/servers/{HOME_ID}',
--    '{SERVER_ROOT}/{MOD_FOLDER}',
--    '@{MOD_NAME}',
--    '-mod=',
--    '-serverMod=',
--    1);
