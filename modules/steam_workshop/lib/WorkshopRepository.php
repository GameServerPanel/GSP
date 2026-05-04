<?php
declare(strict_types=1);
/*
 * OGP / GSP – Steam Workshop
 * WorkshopRepository: database access layer for the three Workshop tables.
 */

class WorkshopRepository
{
    private OGPDatabase $db;
    private string $prefix;

    public function __construct(OGPDatabase $db)
    {
        $this->db    = $db;
        $this->prefix = $db->getTablePrefix();
    }

    // ------------------------------------------------------------------
    // Internal helpers
    // ------------------------------------------------------------------

    private function esc(mixed $val): string
    {
        return $this->db->realEscapeSingle((string)$val);
    }

    /** Execute a query that returns no result set (INSERT / UPDATE / DELETE). */
    private function exec(string $sql): bool
    {
        return $this->db->query($sql) !== false;
    }

    /** Execute a SELECT query; returns array of rows or empty array. */
    private function select(string $sql): array
    {
        $result = $this->db->resultQuery($sql);
        return is_array($result) ? $result : [];
    }

    /** Return the first row or null. */
    private function selectOne(string $sql): ?array
    {
        $rows = $this->select($sql);
        return $rows[0] ?? null;
    }

    private function lastInsertId(): int
    {
        $row = $this->selectOne('SELECT LAST_INSERT_ID() AS id');
        return isset($row['id']) ? (int)$row['id'] : 0;
    }

    // ------------------------------------------------------------------
    // WORKSHOP GAME PROFILES
    // ------------------------------------------------------------------

    /** @return array<int,array<string,mixed>> */
    public function listProfiles(bool $enabledOnly = false): array
    {
        $where = $enabledOnly ? ' WHERE enabled = 1' : '';
        return $this->select(
            "SELECT * FROM `{$this->prefix}workshop_game_profiles`{$where} ORDER BY game_name ASC"
        );
    }

    public function getProfileById(int $id): ?array
    {
        return $this->selectOne(
            "SELECT * FROM `{$this->prefix}workshop_game_profiles` WHERE id = {$id} LIMIT 1"
        );
    }

    public function getProfileByGameKey(string $gameKey): ?array
    {
        return $this->selectOne(
            "SELECT * FROM `{$this->prefix}workshop_game_profiles`
             WHERE game_key = '" . $this->esc($gameKey) . "' AND enabled = 1 LIMIT 1"
        );
    }

    public function getProfileByAppId(string $appId): ?array
    {
        return $this->selectOne(
            "SELECT * FROM `{$this->prefix}workshop_game_profiles`
             WHERE workshop_app_id = '" . $this->esc($appId) . "' AND enabled = 1 LIMIT 1"
        );
    }

    /**
     * Insert (id = 0) or update (id > 0) a Workshop game profile.
     * Returns the row id.
     */
    public function saveProfile(array $data): int
    {
        $id = isset($data['id']) ? (int)$data['id'] : 0;

        $gameKey             = $this->esc($data['game_key'] ?? '');
        $gameName            = $this->esc($data['game_name'] ?? '');
        $steamAppId          = $this->esc($data['steam_app_id'] ?? '');
        $workshopAppId       = $this->esc($data['workshop_app_id'] ?? '');
        $steamLoginRequired  = empty($data['steam_login_required']) ? 0 : 1;
        $steamcmdLoginMode   = in_array($data['steamcmd_login_mode'] ?? '', ['anonymous', 'account'], true)
                                   ? $this->esc($data['steamcmd_login_mode'])
                                   : 'anonymous';
        $steamcmdPath        = $this->esc($data['steamcmd_path'] ?? '');
        $supportedOs         = $this->esc($data['supported_os'] ?? 'linux');
        $cachePathTpl        = $this->esc($data['cache_path_template'] ?? '');
        $installPathTpl      = $this->esc($data['install_path_template'] ?? '');
        $folderNamingFormat  = in_array($data['folder_naming_format'] ?? '', ['@%mod_name%', '@%workshop_id%', 'custom'], true)
                                   ? $this->esc($data['folder_naming_format'])
                                   : '@%workshop_id%';
        $folderNameTpl       = $this->esc($data['folder_name_template'] ?? '@%workshop_id%');
        $modLaunchParam      = $this->esc($data['mod_launch_param'] ?? '');
        $modSeparator        = in_array($data['mod_separator'] ?? '', ['semicolon', 'comma', 'space'], true)
                                   ? $this->esc($data['mod_separator'])
                                   : 'semicolon';
        $copyMethod          = in_array($data['copy_method'] ?? '', ['copy', 'rsync', 'symlink'], true)
                                   ? $this->esc($data['copy_method'])
                                   : 'rsync';
        $copyKeys            = empty($data['copy_keys']) ? 0 : 1;
        $keySourcePath       = $this->nullOrStr($data['key_source_path'] ?? '');
        $keyDestPath         = $this->nullOrStr($data['key_dest_path'] ?? '');
        $preUpdateScript     = $this->nullOrStr($data['pre_update_script'] ?? '');
        $installScript       = $this->nullOrStr($data['install_script'] ?? '');
        $postUpdateScript    = $this->nullOrStr($data['post_update_script'] ?? '');
        $configFileTpl       = $this->nullOrStr($data['config_file_template'] ?? '');
        $launchParamTpl      = $this->nullOrStr($data['launch_param_template'] ?? '');
        $requiresRestart     = empty($data['requires_restart']) ? 0 : 1;
        $validationNotes     = $this->nullOrStr($data['validation_notes'] ?? '');
        $enabled             = isset($data['enabled']) && !$data['enabled'] ? 0 : 1;

        if ($id > 0) {
            $this->exec(
                "UPDATE `{$this->prefix}workshop_game_profiles` SET
                    game_key              = '{$gameKey}',
                    game_name             = '{$gameName}',
                    steam_app_id          = '{$steamAppId}',
                    workshop_app_id       = '{$workshopAppId}',
                    steam_login_required  = {$steamLoginRequired},
                    steamcmd_login_mode   = '{$steamcmdLoginMode}',
                    steamcmd_path         = '{$steamcmdPath}',
                    supported_os          = '{$supportedOs}',
                    cache_path_template   = '{$cachePathTpl}',
                    install_path_template = '{$installPathTpl}',
                    folder_naming_format  = '{$folderNamingFormat}',
                    folder_name_template  = '{$folderNameTpl}',
                    mod_launch_param      = '{$modLaunchParam}',
                    mod_separator         = '{$modSeparator}',
                    copy_method           = '{$copyMethod}',
                    copy_keys             = {$copyKeys},
                    key_source_path       = {$keySourcePath},
                    key_dest_path         = {$keyDestPath},
                    pre_update_script     = {$preUpdateScript},
                    install_script        = {$installScript},
                    post_update_script    = {$postUpdateScript},
                    config_file_template  = {$configFileTpl},
                    launch_param_template = {$launchParamTpl},
                    requires_restart      = {$requiresRestart},
                    validation_notes      = {$validationNotes},
                    enabled               = {$enabled},
                    updated_at            = NOW()
                 WHERE id = {$id}"
            );
            return $id;
        }

        $this->exec(
            "INSERT INTO `{$this->prefix}workshop_game_profiles`
                (game_key, game_name, steam_app_id, workshop_app_id, steam_login_required,
                 steamcmd_login_mode, steamcmd_path, supported_os, cache_path_template,
                 install_path_template, folder_naming_format, folder_name_template,
                 mod_launch_param, mod_separator, copy_method, copy_keys,
                 key_source_path, key_dest_path, pre_update_script, install_script,
                 post_update_script, config_file_template, launch_param_template,
                 requires_restart, validation_notes, enabled, created_at)
             VALUES
                ('{$gameKey}', '{$gameName}', '{$steamAppId}', '{$workshopAppId}', {$steamLoginRequired},
                 '{$steamcmdLoginMode}', '{$steamcmdPath}', '{$supportedOs}', '{$cachePathTpl}',
                 '{$installPathTpl}', '{$folderNamingFormat}', '{$folderNameTpl}',
                 '{$modLaunchParam}', '{$modSeparator}', '{$copyMethod}', {$copyKeys},
                 {$keySourcePath}, {$keyDestPath}, {$preUpdateScript}, {$installScript},
                 {$postUpdateScript}, {$configFileTpl}, {$launchParamTpl},
                 {$requiresRestart}, {$validationNotes}, {$enabled}, NOW())"
        );
        return $this->lastInsertId();
    }

    /** Return NULL or an escaped quoted string, for optional TEXT columns. */
    private function nullOrStr(string $value): string
    {
        return $value !== '' ? "'" . $this->esc($value) . "'" : 'NULL';
    }

    public function deleteProfile(int $id): bool
    {
        return $this->exec(
            "DELETE FROM `{$this->prefix}workshop_game_profiles` WHERE id = {$id}"
        );
    }

    // ------------------------------------------------------------------
    // WORKSHOP CACHE
    // ------------------------------------------------------------------

    public function getCacheEntry(int $agentId, string $appId, string $workshopId): ?array
    {
        return $this->selectOne(
            "SELECT * FROM `{$this->prefix}workshop_cache`
             WHERE agent_id = {$agentId}
               AND workshop_app_id = '" . $this->esc($appId) . "'
               AND workshop_id     = '" . $this->esc($workshopId) . "'
             LIMIT 1"
        );
    }

    /**
     * Insert or update a cache row.
     * $status: 'missing' | 'cached' | 'failed'
     */
    public function upsertCacheEntry(
        int $agentId,
        string $osType,
        string $appId,
        string $workshopId,
        string $cachePath,
        string $status,
        ?string $title = null,
        ?string $error = null
    ): void {
        $osType    = $this->esc($osType);
        $appId     = $this->esc($appId);
        $workshopId = $this->esc($workshopId);
        $cachePath = $this->esc($cachePath);
        $status    = $this->esc($status);
        $titleSql  = $title !== null ? "'" . $this->esc($title) . "'" : 'NULL';
        $errorSql  = $error !== null ? "'" . $this->esc($error) . "'" : 'NULL';
        $updatedSql = ($status === 'cached') ? 'NOW()' : 'NULL';

        $this->exec(
            "INSERT INTO `{$this->prefix}workshop_cache`
                (agent_id, os_type, workshop_app_id, workshop_id, title, cache_path, status, last_checked, last_updated, last_error)
             VALUES
                ({$agentId}, '{$osType}', '{$appId}', '{$workshopId}', {$titleSql}, '{$cachePath}', '{$status}', NOW(), {$updatedSql}, {$errorSql})
             ON DUPLICATE KEY UPDATE
                os_type      = '{$osType}',
                cache_path   = '{$cachePath}',
                status       = '{$status}',
                title        = {$titleSql},
                last_checked = NOW(),
                last_updated = {$updatedSql},
                last_error   = {$errorSql}"
        );
    }

    /** Return all cached entries for a specific agent+appId (for the "available mods" picker). */
    public function listCacheForAgent(int $agentId, string $appId): array
    {
        return $this->select(
            "SELECT * FROM `{$this->prefix}workshop_cache`
             WHERE agent_id = {$agentId}
               AND workshop_app_id = '" . $this->esc($appId) . "'
             ORDER BY COALESCE(title, workshop_id) ASC"
        );
    }

    /** Return all cache rows that should be refreshed (enabled mods installed somewhere). */
    public function listCacheEntriesForAgent(int $agentId): array
    {
        return $this->select(
            "SELECT DISTINCT c.*
             FROM `{$this->prefix}workshop_cache` c
             JOIN `{$this->prefix}server_workshop_mods` m
                ON m.agent_id = c.agent_id
               AND m.workshop_app_id = c.workshop_app_id
               AND m.workshop_id = c.workshop_id
             WHERE c.agent_id = {$agentId} AND m.enabled = 1"
        );
    }

    // ------------------------------------------------------------------
    // SERVER WORKSHOP MODS
    // ------------------------------------------------------------------

    public function getServerMod(int $homeId, string $workshopId): ?array
    {
        return $this->selectOne(
            "SELECT * FROM `{$this->prefix}server_workshop_mods`
             WHERE home_id = {$homeId}
               AND workshop_id = '" . $this->esc($workshopId) . "'
             LIMIT 1"
        );
    }

    /** @return array<int,array<string,mixed>> */
    public function listModsForHome(int $homeId): array
    {
        return $this->select(
            "SELECT m.*, p.game_name, p.game_key, p.requires_restart, p.copy_method
             FROM `{$this->prefix}server_workshop_mods` m
             LEFT JOIN `{$this->prefix}workshop_game_profiles` p ON m.profile_id = p.id
             WHERE m.home_id = {$homeId}
             ORDER BY m.load_order ASC, m.installed_at ASC"
        );
    }

    /** @return array<int,array<string,mixed>> */
    public function listEnabledModsForHome(int $homeId): array
    {
        return $this->select(
            "SELECT * FROM `{$this->prefix}server_workshop_mods`
             WHERE home_id = {$homeId} AND enabled = 1
             ORDER BY load_order ASC"
        );
    }

    /**
     * Insert (id = 0) or update (id > 0) a Workshop mod entry for a game home.
     * Returns the row id.
     */
    public function insertOrUpdateMod(
        int $homeId,
        int $agentId,
        int $profileId,
        string $appId,
        string $workshopId,
        string $installPath,
        string $title = '',
        int $loadOrder = 0,
        string $customFolder = ''
    ): int {
        $appId        = $this->esc($appId);
        $workshopId   = $this->esc($workshopId);
        $installPath  = $this->esc($installPath);
        $title        = $this->esc($title);
        $customFolder = $this->esc($customFolder);

        $existing = $this->getServerMod($homeId, $workshopId);

        if ($existing !== null) {
            $this->exec(
                "UPDATE `{$this->prefix}server_workshop_mods` SET
                    agent_id         = {$agentId},
                    profile_id       = {$profileId},
                    workshop_app_id  = '{$appId}',
                    title            = '{$title}',
                    custom_folder    = '{$customFolder}',
                    install_path     = '{$installPath}',
                    load_order       = {$loadOrder},
                    enabled          = 1,
                    updated_at       = NOW()
                 WHERE home_id = {$homeId} AND workshop_id = '{$workshopId}'"
            );
            return (int)$existing['id'];
        }

        $this->exec(
            "INSERT INTO `{$this->prefix}server_workshop_mods`
                (home_id, agent_id, profile_id, workshop_app_id, workshop_id, title, custom_folder, enabled, install_path, load_order, installed_at)
             VALUES
                ({$homeId}, {$agentId}, {$profileId}, '{$appId}', '{$workshopId}', '{$title}', '{$customFolder}', 1, '{$installPath}', {$loadOrder}, NOW())"
        );
        return $this->lastInsertId();
    }

    public function removeMod(int $homeId, string $workshopId): bool
    {
        return $this->exec(
            "DELETE FROM `{$this->prefix}server_workshop_mods`
             WHERE home_id = {$homeId} AND workshop_id = '" . $this->esc($workshopId) . "'"
        );
    }

    public function toggleMod(int $homeId, string $workshopId, bool $enabled): bool
    {
        $val = $enabled ? 1 : 0;
        return $this->exec(
            "UPDATE `{$this->prefix}server_workshop_mods`
             SET enabled = {$val}, updated_at = NOW()
             WHERE home_id = {$homeId} AND workshop_id = '" . $this->esc($workshopId) . "'"
        );
    }

    public function updateLoadOrder(int $homeId, string $workshopId, int $order): bool
    {
        return $this->exec(
            "UPDATE `{$this->prefix}server_workshop_mods`
             SET load_order = {$order}, updated_at = NOW()
             WHERE home_id = {$homeId} AND workshop_id = '" . $this->esc($workshopId) . "'"
        );
    }

    /**
     * Return all enabled installed mods joined with their profile data.
     * Used by the scheduled updater to know what needs refreshing.
     *
     * @return array<int,array<string,mixed>>
     */
    public function listAllEnabledMods(): array
    {
        return $this->select(
            "SELECT m.*,
                     p.steam_app_id, p.cache_path_template, p.install_path_template,
                     p.folder_naming_format, p.folder_name_template,
                     p.copy_method, p.copy_keys, p.key_source_path, p.key_dest_path,
                     p.pre_update_script, p.install_script, p.post_update_script,
                     p.steamcmd_path, p.steamcmd_login_mode,
                     p.config_file_template, p.launch_param_template,
                     p.requires_restart
             FROM `{$this->prefix}server_workshop_mods` m
             JOIN `{$this->prefix}workshop_game_profiles` p ON m.profile_id = p.id
             WHERE m.enabled = 1 AND p.enabled = 1
             ORDER BY m.agent_id ASC, m.workshop_app_id ASC, m.workshop_id ASC"
        );
    }

    // ------------------------------------------------------------------
    // Agent / remote server helpers (for WorkshopUpdater)
    // ------------------------------------------------------------------

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Return the agent connection row for a remote_server_id.
     * Returns null if not found.
     */
    public function getAgentRow(int $agentId): ?array
    {
        return $this->selectOne(
            "SELECT remote_server_id AS agent_id, agent_ip, agent_port, encryption_key, timeout
             FROM `{$this->prefix}remote_servers`
             WHERE remote_server_id = {$agentId}
             LIMIT 1"
        );
    }

    // ------------------------------------------------------------------
    // Distinct Workshop ID queries (for WorkshopUpdater)
    // ------------------------------------------------------------------

    /**
     * Return distinct (agent_id, workshop_app_id, workshop_id) triplets for enabled mods.
     * Used by the updater to avoid duplicate SteamCMD calls.
     *
     * @return array<int,array<string,mixed>>
     */
    public function listDistinctEnabledWorkshopIds(): array
    {
        return $this->select(
            "SELECT DISTINCT m.agent_id, m.workshop_app_id, m.workshop_id, m.title
             FROM `{$this->prefix}server_workshop_mods` m
             JOIN `{$this->prefix}workshop_game_profiles` p ON m.profile_id = p.id
             WHERE m.enabled = 1 AND p.enabled = 1
             ORDER BY m.agent_id ASC, m.workshop_app_id ASC"
        );
    }

    /** Distinct (agent_id, workshop_app_id, workshop_id) for a single agent. */
    public function listDistinctEnabledWorkshopIdsForAgent(int $agentId): array
    {
        return $this->select(
            "SELECT DISTINCT m.agent_id, m.workshop_app_id, m.workshop_id, m.title
             FROM `{$this->prefix}server_workshop_mods` m
             JOIN `{$this->prefix}workshop_game_profiles` p ON m.profile_id = p.id
             WHERE m.enabled = 1 AND p.enabled = 1 AND m.agent_id = {$agentId}
             ORDER BY m.workshop_app_id ASC"
        );
    }

    /** Distinct Workshop IDs for a specific home. */
    public function listDistinctEnabledWorkshopIdsForHome(int $homeId): array
    {
        return $this->select(
            "SELECT DISTINCT m.agent_id, m.workshop_app_id, m.workshop_id, m.title
             FROM `{$this->prefix}server_workshop_mods` m
             JOIN `{$this->prefix}workshop_game_profiles` p ON m.profile_id = p.id
             WHERE m.enabled = 1 AND p.enabled = 1 AND m.home_id = {$homeId}"
        );
    }

    /** Distinct Workshop IDs for a specific profile. */
    public function listDistinctEnabledWorkshopIdsForProfile(int $profileId): array
    {
        return $this->select(
            "SELECT DISTINCT m.agent_id, m.workshop_app_id, m.workshop_id, m.title
             FROM `{$this->prefix}server_workshop_mods` m
             WHERE m.enabled = 1 AND m.profile_id = {$profileId}"
        );
    }

    // ------------------------------------------------------------------
    // SERVER WORKSHOP SETTINGS (per-server/home configuration)
    // ------------------------------------------------------------------

    /**
     * Return the workshop settings row for a game home, or null if not set.
     */
    public function getServerSettings(int $homeId): ?array
    {
        return $this->selectOne(
            "SELECT * FROM `{$this->prefix}server_workshop_settings`
             WHERE home_id = {$homeId} LIMIT 1"
        );
    }

    /**
     * Upsert server-level workshop settings.
     */
    public function saveServerSettings(int $homeId, array $data): void
    {
        $workshopEnabled  = empty($data['workshop_enabled']) ? 0 : 1;
        $profileId        = isset($data['profile_id']) && (int)$data['profile_id'] > 0
                                ? (int)$data['profile_id']
                                : 'NULL';
        $updateMode       = in_array($data['update_mode'] ?? '', ['manual', 'scheduled', 'on_restart'], true)
                                ? "'" . $this->esc($data['update_mode']) . "'"
                                : "'manual'";
        $restartBehavior  = in_array($data['restart_behavior'] ?? '', ['none', 'queue', 'stop_update_start'], true)
                                ? "'" . $this->esc($data['restart_behavior']) . "'"
                                : "'none'";
        $updateQueued     = empty($data['update_queued']) ? 0 : 1;

        $this->exec(
            "INSERT INTO `{$this->prefix}server_workshop_settings`
                 (home_id, workshop_enabled, profile_id, update_mode, restart_behavior, update_queued, updated_at)
             VALUES
                 ({$homeId}, {$workshopEnabled}, {$profileId}, {$updateMode}, {$restartBehavior}, {$updateQueued}, NOW())
             ON DUPLICATE KEY UPDATE
                 workshop_enabled = {$workshopEnabled},
                 profile_id       = {$profileId},
                 update_mode      = {$updateMode},
                 restart_behavior = {$restartBehavior},
                 update_queued    = {$updateQueued},
                 updated_at       = NOW()"
        );
    }

    /**
     * Record the result of an update run for a home.
     */
    public function recordUpdateResult(int $homeId, string $status, string $error = ''): void
    {
        $status    = $this->esc($status);
        $errorSql  = $error !== '' ? "'" . $this->esc($error) . "'" : 'NULL';
        $successSql = $status === 'success' ? 'NOW()' : 'last_success_time';

        $this->exec(
            "INSERT INTO `{$this->prefix}server_workshop_settings`
                 (home_id, last_update_status, last_update_error, last_update_time, last_success_time)
             VALUES
                 ({$homeId}, '{$status}', {$errorSql}, NOW(), " . ($status === 'success' ? 'NOW()' : 'NULL') . ")
             ON DUPLICATE KEY UPDATE
                 last_update_status = '{$status}',
                 last_update_error  = {$errorSql},
                 last_update_time   = NOW(),
                 last_success_time  = {$successSql}"
        );
    }

    /**
     * Mark a manual update as queued (or clear the queue flag).
     */
    public function setUpdateQueued(int $homeId, bool $queued): void
    {
        $val = $queued ? 1 : 0;
        $this->exec(
            "INSERT INTO `{$this->prefix}server_workshop_settings` (home_id, update_queued)
             VALUES ({$homeId}, {$val})
             ON DUPLICATE KEY UPDATE update_queued = {$val}"
        );
    }

    /**
     * Return all home IDs that have a queued manual update.
     *
     * @return array<int,int>
     */
    public function listQueuedUpdateHomes(): array
    {
        $rows = $this->select(
            "SELECT home_id FROM `{$this->prefix}server_workshop_settings`
             WHERE update_queued = 1 AND workshop_enabled = 1"
        );
        return array_column($rows, 'home_id');
    }
}
