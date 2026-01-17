<?php
declare(strict_types=1);

class SteamWorkshopService
{
    private const MIN_INTERVAL = 15;
    private const MAX_INTERVAL = 360;

    private OGPDatabase $db;
    private string $configDir;
    private string $adapterDir;

    public function __construct(OGPDatabase $db)
    {
        $this->db = $db;
        $this->configDir = __DIR__ . '/../data/configs';
        $this->adapterDir = __DIR__ . '/GameAdapters';

        if (!is_dir($this->configDir)) {
            mkdir($this->configDir, 0775, true);
        }
    }

    /**
     * Fetch all homes visible to the given user.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listHomesForUser(int $userId, bool $isAdmin): array
    {
        $accessType = $isAdmin ? 'admin' : 'user_and_group';
        $homes = $this->db->getHomesFor($accessType, $userId);

        if ($homes === false || $homes === null) {
            return [];
        }

        return array_values($homes);
    }

    /**
     * Retrieve a single home, ensuring the user is allowed to see it.
     */
    public function getHome(int $homeId, int $userId, bool $isAdmin): ?array
    {
        $home = $isAdmin
            ? $this->db->getGameHome($homeId)
            : $this->db->getUserGameHome($userId, $homeId);

        return is_array($home) ? $home : null;
    }

    /**
     * @return array{
     *   workshop_enabled: bool,
     *   adapter_key: string,
     *   update_interval_minutes: int,
     *   staging_dir: string,
     *   install_strategy: string,
     *   on_update_action: string,
     *   post_install_script: string,
     *   workshop_items: array<int, array<string, mixed>>,
     *   raw_definition: string,
     *   last_saved_at: int|null
     * }
     */
    public function loadConfig(int $homeId): array
    {
        $path = $this->getConfigPath($homeId);

        if (!is_file($path)) {
            return $this->defaultConfig();
        }

        $xml = @simplexml_load_file($path);
        if ($xml === false) {
            return $this->defaultConfig();
        }

        $config = $this->defaultConfig();
        $config['workshop_enabled'] = ((string)($xml->enabled ?? 'false')) === 'true';
        $config['adapter_key'] = (string)($xml->adapter['key'] ?? $config['adapter_key']);
        $config['update_interval_minutes'] = $this->sanitizeInterval((int)($xml->updateInterval ?? $config['update_interval_minutes']));
        $config['staging_dir'] = trim((string)($xml->stagingDir ?? ''));
        $config['install_strategy'] = (string)($xml->installStrategy ?? $config['install_strategy']);
        $config['on_update_action'] = (string)($xml->onUpdateAction ?? $config['on_update_action']);
        $config['post_install_script'] = trim((string)($xml->postInstallScript ?? ''));
        $config['raw_definition'] = (string)($xml->rawDefinition ?? '');
        $config['last_saved_at'] = isset($xml->timestamps->savedAt)
            ? (int)$xml->timestamps->savedAt
            : null;

        $mods = [];
        if (isset($xml->mods)) {
            foreach ($xml->mods->mod as $mod) {
                $mods[] = [
                    'id' => (string)$mod['id'],
                    'label' => (string)$mod['label'],
                    'enabled' => ((string)$mod['enabled']) !== 'false',
                    'source' => (string)($mod['source'] ?? 'manual'),
                ];
            }
        }

        $config['workshop_items'] = $mods;

        return $config;
    }

    public function saveConfig(int $homeId, array $config): void
    {
        $path = $this->getConfigPath($homeId);
        $config = $this->normalizeConfig($config);
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        $root = $doc->createElement('workshop');
        $doc->appendChild($root);

        $root->appendChild($doc->createElement('enabled', $config['workshop_enabled'] ? 'true' : 'false'));

        $adapterNode = $doc->createElement('adapter');
        $adapterNode->setAttribute('key', $config['adapter_key']);
        $root->appendChild($adapterNode);

        $root->appendChild($doc->createElement('updateInterval', (string)$config['update_interval_minutes']));
        $root->appendChild($doc->createElement('stagingDir', $config['staging_dir']));
        $root->appendChild($doc->createElement('installStrategy', $config['install_strategy']));
        $root->appendChild($doc->createElement('onUpdateAction', $config['on_update_action']));
        $root->appendChild($doc->createElement('postInstallScript', $config['post_install_script']));
        $root->appendChild($doc->createElement('rawDefinition', $config['raw_definition']));

        $modsNode = $doc->createElement('mods');
        foreach ($config['workshop_items'] as $item) {
            $mod = $doc->createElement('mod');
            $mod->setAttribute('id', (string)$item['id']);
            $mod->setAttribute('label', (string)$item['label']);
            $mod->setAttribute('enabled', !empty($item['enabled']) ? 'true' : 'false');
            $mod->setAttribute('source', (string)($item['source'] ?? 'manual'));
            $modsNode->appendChild($mod);
        }
        $root->appendChild($modsNode);

        $timestampsNode = $doc->createElement('timestamps');
        $timestampsNode->appendChild($doc->createElement('savedAt', (string)time()));
        $root->appendChild($timestampsNode);

        $doc->save($path);
    }

    /**
     * Convert POST payload into a config array and merge defaults.
     */
    public function buildConfigFromRequest(array $payload): array
    {
        $input = $payload['workshop'] ?? [];
        $rawMods = trim((string)($input['raw_items'] ?? ''));
        $items = $this->parseWorkshopItems($rawMods);

        return [
            'workshop_enabled' => isset($input['workshop_enabled']) ? (bool)$input['workshop_enabled'] : false,
            'adapter_key' => $this->sanitizeAdapterKey((string)($input['adapter_key'] ?? 'dayz')),
            'update_interval_minutes' => $this->sanitizeInterval(isset($input['update_interval_minutes']) ? (int)$input['update_interval_minutes'] : null),
            'staging_dir' => trim((string)($input['staging_dir'] ?? '')),
            'install_strategy' => $this->sanitizeInstallStrategy((string)($input['install_strategy'] ?? 'copy')),
            'on_update_action' => $this->sanitizeUpdateAction((string)($input['on_update_action'] ?? 'queue_for_restart')),
            'post_install_script' => trim((string)($input['post_install_script'] ?? '')),
            'workshop_items' => $items,
            'raw_definition' => $rawMods,
        ];
    }

    /**
     * Accepts imports such as "123456,@My Mod" per line.
     *
     * @return array<int, array{id:string,label:string,enabled:bool,source:string}>
     */
    public function parseWorkshopItems(string $raw): array
    {
        if ($raw === '') {
            return [];
        }

        $items = [];
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $parts = array_map('trim', explode(',', $line, 2));
            $id = preg_replace('/[^0-9]/', '', $parts[0]);
            if ($id === '') {
                continue;
            }
            $label = $parts[1] ?? '';
            if ($label === '') {
                $label = '@' . $id;
            }

            $items[] = [
                'id' => $id,
                'label' => $label,
                'enabled' => true,
                'source' => 'manual',
            ];
        }

        return $items;
    }

    /**
     * Build a SteamCMD command array for a single workshop item.
     */
    public function buildSteamCmdArgs(array $config, string $workshopId, ?string $login = null): array
    {
        $loginUser = $login !== null && $login !== '' ? $login : 'anonymous';
        $adapter = $this->getAdapterByKey($config['adapter_key'] ?? '');
        $appId = $adapter['steam_app_id'] ?? ($config['steam_app_id'] ?? '');

        return [
            '+login', $loginUser,
            '+workshop_download_item', $appId,
            $workshopId,
            'validate',
        ];
    }

    public function getAdapterOptions(): array
    {
        $options = [];
        foreach ($this->loadAdapters() as $adapter) {
            $options[$adapter['key']] = $adapter['name'];
        }

        if (empty($options)) {
            $options['dayz'] = 'DayZ (fallback)';
        }

        return $options;
    }

    /**
     * Load adapter metadata for UI and validation.
     *
     * @return array<int, array<string, mixed>>
     */
    public function loadAdapters(): array
    {
        $adapters = [];
        $schema = $this->adapterDir . '/schema.xsd';
        $useSchema = is_file($schema);
        $previousLibxml = libxml_use_internal_errors(true);

        foreach (glob($this->adapterDir . '/*.xml') as $file) {
            if (substr($file, -4) !== '.xml') {
                continue;
            }
            if (basename($file) === 'schema.xsd') {
                continue;
            }

            $doc = new DOMDocument();
            if (!$doc->load($file)) {
                continue;
            }

            if ($useSchema && !$doc->schemaValidate($schema)) {
                libxml_clear_errors();
                continue;
            }

            $adapter = simplexml_import_dom($doc);
            if ($adapter === false) {
                continue;
            }

            $adapters[] = [
                'key' => (string)($adapter['key'] ?? ''),
                'name' => (string)($adapter['name'] ?? ''),
                'steam_app_id' => (string)($adapter->steamAppId ?? ''),
                'mods_dir' => (string)($adapter->modsDir ?? ''),
                'keys_dir' => isset($adapter->keysDir) ? (string)$adapter->keysDir : null,
                'supports_hot_reload' => filter_var((string)($adapter->supportsHotReload ?? 'false'), FILTER_VALIDATE_BOOLEAN),
                'activation_template' => (string)($adapter->activation->template ?? ''),
                'notes' => (string)($adapter->notes ?? ''),
            ];
        }

        $result = array_values(array_filter($adapters, static function (array $adapter): bool {
            return $adapter['key'] !== '';
        }));

        libxml_use_internal_errors($previousLibxml);

        return $result;
    }

    public function getAdapterByKey(string $key): array
    {
        foreach ($this->loadAdapters() as $adapter) {
            if ($adapter['key'] === $key) {
                return $adapter;
            }
        }

        return [];
    }

    private function sanitizeInterval(?int $minutes): int
    {
        if ($minutes === null || $minutes <= 0) {
            $minutes = 60;
        }

        return max(self::MIN_INTERVAL, min(self::MAX_INTERVAL, $minutes));
    }

    private function sanitizeAdapterKey(string $key): string
    {
        $key = strtolower(trim($key));
        if ($key === '') {
            return 'dayz';
        }

        $adapters = $this->getAdapterOptions();
        if (array_key_exists($key, $adapters)) {
            return $key;
        }

        $adapterKeys = array_keys($adapters);
        return $adapterKeys[0] ?? 'dayz';
    }

    private function sanitizeInstallStrategy(string $strategy): string
    {
        $valid = ['copy', 'symlink', 'staging'];
        return in_array($strategy, $valid, true) ? $strategy : 'copy';
    }

    private function sanitizeUpdateAction(string $action): string
    {
        $valid = ['queue_for_restart', 'hot_reload_if_supported'];
        return in_array($action, $valid, true) ? $action : 'queue_for_restart';
    }

    private function normalizeConfig(array $config): array
    {
        $config = array_merge($this->defaultConfig(), $config);
        $config['update_interval_minutes'] = $this->sanitizeInterval((int)$config['update_interval_minutes']);
        $config['adapter_key'] = $this->sanitizeAdapterKey((string)$config['adapter_key']);
        $config['install_strategy'] = $this->sanitizeInstallStrategy((string)$config['install_strategy']);
        $config['on_update_action'] = $this->sanitizeUpdateAction((string)$config['on_update_action']);
        $config['workshop_items'] = array_map(static function (array $item): array {
            $item['id'] = preg_replace('/[^0-9]/', '', (string)($item['id'] ?? ''));
            $item['label'] = trim((string)($item['label'] ?? ''));
            $item['enabled'] = !empty($item['enabled']);
            $item['source'] = $item['source'] ?? 'manual';
            return $item;
        }, $config['workshop_items']);

        $config['workshop_items'] = array_values(array_filter($config['workshop_items'], static function (array $item): bool {
            return $item['id'] !== '';
        }));

        return $config;
    }

    private function getConfigPath(int $homeId): string
    {
        return sprintf('%s/%d.xml', $this->configDir, $homeId);
    }

    private function defaultConfig(): array
    {
        return [
            'workshop_enabled' => false,
            'adapter_key' => 'dayz',
            'update_interval_minutes' => 60,
            'staging_dir' => '',
            'install_strategy' => 'copy',
            'on_update_action' => 'queue_for_restart',
            'post_install_script' => '',
            'workshop_items' => [],
            'raw_definition' => '',
            'last_saved_at' => null,
        ];
    }
}
