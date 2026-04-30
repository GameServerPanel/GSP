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
        $workshopAppId       = $this->esc($data['workshop_app_id'] ?? '');
        $supportedOs         = $this->esc($data['supported_os'] ?? 'linux');
        $cachePathTpl        = $this->esc($data['cache_path_template'] ?? '');
        $installPathTpl      = $this->esc($data['install_path_template'] ?? '');
        $folderNameTpl       = $this->esc($data['folder_name_template'] ?? '@{mod_id}');
        $copyMethod          = $this->esc($data['copy_method'] ?? 'rsync');
        $installScript       = isset($data['install_script']) && $data['install_script'] !== '' ? "'" . $this->esc($data['install_script']) . "'" : 'NULL';
        $configFileTpl       = isset($data['config_file_template']) && $data['config_file_template'] !== '' ? "'" . $this->esc($data['config_file_template']) . "'" : 'NULL';
        $launchParamTpl      = isset($data['launch_param_template']) && $data['launch_param_template'] !== '' ? "'" . $this->esc($data['launch_param_template']) . "'" : 'NULL';
        $requiresRestart     = empty($data['requires_restart']) ? 0 : 1;
        $enabled             = isset($data['enabled']) && !$data['enabled'] ? 0 : 1;

        if ($id > 0) {
            $this->exec(
                "UPDATE `{$this->prefix}workshop_game_profiles` SET
                    game_key            = '{$gameKey}',
                    game_name           = '{$gameName}',
                    workshop_app_id     = '{$workshopAppId}',
                    supported_os        = '{$supportedOs}',
                    cache_path_template = '{$cachePathTpl}',
                    install_path_template = '{$installPathTpl}',
                    folder_name_template = '{$folderNameTpl}',
                    copy_method         = '{$copyMethod}',
                    install_script      = {$installScript},
                    config_file_template = {$configFileTpl},
                    launch_param_template = {$launchParamTpl},
                    requires_restart    = {$requiresRestart},
                    enabled             = {$enabled},
                    updated_at          = NOW()
                 WHERE id = {$id}"
            );
            return $id;
        }

        $this->exec(
            "INSERT INTO `{$this->prefix}workshop_game_profiles`
                (game_key, game_name, workshop_app_id, supported_os, cache_path_template,
                 install_path_template, folder_name_template, copy_method, install_script,
                 config_file_template, launch_param_template, requires_restart, enabled, created_at)
             VALUES
                ('{$gameKey}', '{$gameName}', '{$workshopAppId}', '{$supportedOs}', '{$cachePathTpl}',
                 '{$installPathTpl}', '{$folderNameTpl}', '{$copyMethod}', {$installScript},
                 {$configFileTpl}, {$launchParamTpl}, {$requiresRestart}, {$enabled}, NOW())"
        );
        return $this->lastInsertId();
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
     * Insert a new mod row or update the existing one (upsert by home_id + workshop_id).
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
        int $loadOrder = 0
    ): int {
        $appId       = $this->esc($appId);
        $workshopId  = $this->esc($workshopId);
        $installPath = $this->esc($installPath);
        $title       = $this->esc($title);

        $existing = $this->getServerMod($homeId, $workshopId);

        if ($existing !== null) {
            $this->exec(
                "UPDATE `{$this->prefix}server_workshop_mods` SET
                    agent_id         = {$agentId},
                    profile_id       = {$profileId},
                    workshop_app_id  = '{$appId}',
                    title            = '{$title}',
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
                (home_id, agent_id, profile_id, workshop_app_id, workshop_id, title, enabled, install_path, load_order, installed_at)
             VALUES
                ({$homeId}, {$agentId}, {$profileId}, '{$appId}', '{$workshopId}', '{$title}', 1, '{$installPath}', {$loadOrder}, NOW())"
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
                    p.cache_path_template, p.install_path_template, p.folder_name_template,
                    p.copy_method, p.install_script, p.config_file_template, p.launch_param_template,
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
}
