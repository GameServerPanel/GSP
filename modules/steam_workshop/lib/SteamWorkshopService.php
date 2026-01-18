<?php
declare(strict_types=1);

class SteamWorkshopService
{
	private const MIN_INTERVAL = 15;
	private const MAX_INTERVAL = 360;

	private OGPDatabase $db;
	private string $configDir;
	private string $adapterDir;
	private string $adapterMapFile;
	private string $gameAdapterDir;
	private string $serverConfigDir;

	public function __construct(OGPDatabase $db)
	{
		$this->db = $db;
		$this->configDir = __DIR__ . '/../data/configs';
		$this->adapterDir = __DIR__ . '/GameAdapters';
		$this->adapterMapFile = __DIR__ . '/../data/game_adapter_map.json';
		$this->gameAdapterDir = __DIR__ . '/../data/game_adapters';
		$this->serverConfigDir = defined('SERVER_CONFIG_LOCATION')
			? SERVER_CONFIG_LOCATION
			: __DIR__ . '/../../config_games/server_configs';

		if (!is_dir($this->configDir)) {
			mkdir($this->configDir, 0775, true);
		}

		if (!is_dir($this->gameAdapterDir)) {
			mkdir($this->gameAdapterDir, 0775, true);
		}

		$this->ensureDataFiles();
	}

	/**
	 * @return array<int,array<string,mixed>>
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

	public function getHome(int $homeId, int $userId, bool $isAdmin): ?array
	{
		$home = $isAdmin
			? $this->db->getGameHome($homeId)
			: $this->db->getUserGameHome($userId, $homeId);

		return is_array($home) ? $home : null;
	}

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
		$config['last_saved_at'] = isset($xml->timestamps->savedAt) ? (int)$xml->timestamps->savedAt : null;

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

	public function buildSteamCmdArgs(array $config, string $workshopId, ?string $login = null): array
	{
		$loginUser = $login !== null && $login !== '' ? $login : 'anonymous';
		$adapter = $this->getAdapterByKey($config['adapter_key'] ?? '');
		$appId = $adapter['steam_app_id'] ?? ($config['steam_app_id'] ?? '');

		return ['+login', $loginUser, '+workshop_download_item', $appId, $workshopId, 'validate'];
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

	public function loadAdapters(): array
	{
		$result = [];
		$schema = $this->adapterDir . '/schema.xsd';
		$useSchema = is_file($schema);

		foreach (glob($this->adapterDir . '/*.xml') as $file) {
			if (substr($file, -4) !== '.xml' || basename($file) === 'schema.xsd') {
				continue;
			}

			$parsed = $this->parseAdapterFile($file, $schema, $useSchema);
			if ($parsed !== null) {
				$parsed['origin'] = 'shared';
				$result[] = $parsed;
			}
		}

		foreach (glob($this->gameAdapterDir . '/*.xml') as $file) {
			if (substr($file, -4) !== '.xml') {
				continue;
			}

			$gameKey = basename($file, '.xml');
			$parsed = $this->parseAdapterFile($file, $schema, $useSchema, $gameKey);
			if ($parsed !== null) {
				$parsed['origin'] = 'custom';
				$result[] = $parsed;
			}
		}

		return array_values(array_filter($result, static function (array $adapter): bool {
			return $adapter['key'] !== '';
		}));
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

	public function getAdapterKeyForGame(string $gameKey): ?string
	{
		$gameKey = trim($gameKey);
		if ($gameKey === '') {
			return null;
		}

		$map = $this->getAdapterMappings();
		if (isset($map[$gameKey])) {
			return $map[$gameKey];
		}

		if ($this->gameAdapterExists($gameKey)) {
			return $gameKey;
		}

		return null;
	}

	public function saveAdapterMappings(array $mappings): void
	{
		$sanitized = [];
		$options = $this->getAdapterOptions();
		foreach ($mappings as $gameKey => $adapterKey) {
			$gameKey = trim((string)$gameKey);
			$adapterKey = $this->sanitizeAdapterKey((string)$adapterKey);
			if ($gameKey === '' || !isset($options[$adapterKey])) {
				continue;
			}
			$sanitized[$gameKey] = $adapterKey;
		}

		file_put_contents($this->adapterMapFile, json_encode($sanitized, JSON_PRETTY_PRINT));
	}

	public function upsertAdapterMapping(string $gameKey, string $adapterKey): void
	{
		$gameKey = $this->sanitizeGameKey($gameKey);
		$adapterKey = $this->sanitizeAdapterKey($adapterKey);
		if ($gameKey === '' || $adapterKey === '') {
			return;
		}

		$map = $this->getAdapterMappings();
		$map[$gameKey] = $adapterKey;
		file_put_contents($this->adapterMapFile, json_encode($map, JSON_PRETTY_PRINT));
	}

	public function removeAdapterMapping(string $gameKey, ?string $adapterKey = null): void
	{
		$gameKey = $this->sanitizeGameKey($gameKey);
		if ($gameKey === '') {
			return;
		}

		$map = $this->getAdapterMappings();
		if ($adapterKey === null || (isset($map[$gameKey]) && $map[$gameKey] === $adapterKey)) {
			unset($map[$gameKey]);
			file_put_contents($this->adapterMapFile, json_encode($map, JSON_PRETTY_PRINT));
		}
	}

	public function getAdapterMappings(): array
	{
		if (!is_file($this->adapterMapFile)) {
			return [];
		}

		$raw = file_get_contents($this->adapterMapFile);
		$decoded = json_decode((string)$raw, true);
		if (!is_array($decoded)) {
			return [];
		}

		$result = [];
		foreach ($decoded as $gameKey => $adapterKey) {
			if (!is_string($gameKey) || !is_string($adapterKey)) {
				continue;
			}
			$result[$gameKey] = $adapterKey;
		}

		return $result;
	}

	public function listGameAdapters(): array
	{
		$adapters = [];
		$schema = $this->adapterDir . '/schema.xsd';
		$useSchema = is_file($schema);
		foreach (glob($this->gameAdapterDir . '/*.xml') as $file) {
			$gameKey = basename($file, '.xml');
			$parsed = $this->parseAdapterFile($file, $schema, $useSchema, $gameKey);
			if ($parsed !== null) {
				$parsed['origin'] = 'custom';
				$parsed['game_key'] = $gameKey;
				$adapters[] = $parsed;
			}
		}

		return $adapters;
	}

	public function gameAdapterExists(string $gameKey): bool
	{
		$gameKey = $this->sanitizeGameKey($gameKey);
		if ($gameKey === '') {
			return false;
		}

		return is_file($this->getGameAdapterPath($gameKey));
	}

	public function getGameAdapter(string $gameKey): ?array
	{
		$gameKey = $this->sanitizeGameKey($gameKey);
		if ($gameKey === '') {
			return null;
		}

		$path = $this->getGameAdapterPath($gameKey);
		if (!is_file($path)) {
			return null;
		}

		return $this->parseAdapterFile($path, $this->adapterDir . '/schema.xsd', is_file($this->adapterDir . '/schema.xsd'), $gameKey);
	}

	public function getGameAdapterUpdatedAt(string $gameKey): ?int
	{
		$path = $this->getGameAdapterPath($gameKey);
		if (!is_file($path)) {
			return null;
		}

		$mtime = filemtime($path);
		return $mtime === false ? null : $mtime;
	}

	public function getAdapterFormData(string $gameKey, ?array $overrides = null): array
	{
		$gameKey = $this->sanitizeGameKey($gameKey);
		$defaults = [
			'game_key' => $gameKey,
			'name' => $gameKey,
			'steam_app_id' => '',
			'mods_dir' => '',
			'keys_dir' => '',
			'supports_hot_reload' => false,
			'activation_template' => '',
			'notes' => '',
			'exists' => false,
		];

		$current = $this->getGameAdapter($gameKey);
		if ($current !== null) {
			$defaults = array_merge($defaults, [
				'name' => $current['name'] ?? $gameKey,
				'steam_app_id' => $current['steam_app_id'] ?? '',
				'mods_dir' => $current['mods_dir'] ?? '',
				'keys_dir' => $current['keys_dir'] ?? '',
				'supports_hot_reload' => !empty($current['supports_hot_reload']),
				'activation_template' => $current['activation_template'] ?? '',
				'notes' => $current['notes'] ?? '',
				'exists' => true,
			]);
		}

		if ($overrides !== null) {
			$defaults = array_merge($defaults, $overrides);
		}

		return $defaults;
	}

	public function saveGameAdapter(string $gameKey, array $data): void
	{
		$gameKey = $this->sanitizeGameKey($gameKey);
		if ($gameKey === '') {
			throw new RuntimeException('Game key is required.');
		}

		$normalized = $this->normalizeAdapterData($gameKey, $data);
		if ($normalized['steam_app_id'] === '') {
			throw new RuntimeException('Steam App ID is required.');
		}
		if ($normalized['mods_dir'] === '') {
			throw new RuntimeException('Mods directory is required.');
		}

		$doc = new DOMDocument('1.0', 'UTF-8');
		$doc->formatOutput = true;

		$root = $doc->createElement('adapter');
		$root->setAttribute('key', $gameKey);
		$root->setAttribute('name', $normalized['name']);
		$doc->appendChild($root);

		$root->appendChild($doc->createElement('steamAppId', $normalized['steam_app_id']));
		$root->appendChild($doc->createElement('modsDir', $normalized['mods_dir']));
		if ($normalized['keys_dir'] !== '') {
			$root->appendChild($doc->createElement('keysDir', $normalized['keys_dir']));
		}
		$root->appendChild($doc->createElement('supportsHotReload', $normalized['supports_hot_reload'] ? 'true' : 'false'));

		$activationNode = $doc->createElement('activation');
		$templateNode = $doc->createElement('template');
		if ($normalized['activation_template'] !== '') {
			$templateNode->appendChild($doc->createCDATASection($normalized['activation_template']));
		}
		$activationNode->appendChild($templateNode);
		$root->appendChild($activationNode);

		if ($normalized['notes'] !== '') {
			$root->appendChild($doc->createElement('notes', $normalized['notes']));
		}

		$path = $this->getGameAdapterPath($gameKey);
		$doc->save($path);
	}

	public function deleteGameAdapter(string $gameKey): bool
	{
		$gameKey = $this->sanitizeGameKey($gameKey);
		if ($gameKey === '') {
			return false;
		}

		$path = $this->getGameAdapterPath($gameKey);
		if (!is_file($path)) {
			return false;
		}

		return unlink($path);
	}

	public function listWorkshopGameGroups(): array
	{
		$configDir = $this->serverConfigDir;
		if (!is_dir($configDir)) {
			return [];
		}

		$groups = [];
		foreach (glob($configDir . '/*.xml') as $file) {
			$xml = @simplexml_load_file($file);
			if ($xml === false) {
				continue;
			}

			$installer = isset($xml->installer) ? trim((string)$xml->installer) : '';
			if ($installer !== 'steamcmd') {
				continue;
			}

			$gameKey = isset($xml->game_key) ? trim((string)$xml->game_key) : '';
			if ($gameKey === '') {
				continue;
			}

			$appId = $this->parseSteamAppIdFromConfig($xml);
			if ($appId === null) {
				continue;
			}

			$groupKey = $this->buildWorkshopGroupKey($appId);
			if (!isset($groups[$groupKey])) {
				$gameName = isset($xml->game_name) ? trim((string)$xml->game_name) : '';
				$groups[$groupKey] = [
					'group_key' => $groupKey,
					'app_id' => $appId,
					'game_name' => $gameName !== '' ? $gameName : $gameKey,
					'game_keys' => [],
				];
			}

			$groups[$groupKey]['game_keys'][] = $gameKey;
		}

		foreach ($groups as &$group) {
			$group['game_keys'] = array_values(array_unique($group['game_keys']));
			sort($group['game_keys']);
			$group['primary_game_key'] = $group['game_keys'][0];
		}
		unset($group);

		usort($groups, static function (array $a, array $b): int {
			return strcmp($a['game_name'], $b['game_name']);
		});

		return array_values($groups);
	}

	public function listAvailableGameKeys(): array
	{
		$keys = [];
		foreach ($this->listWorkshopGameGroups() as $group) {
			$keys = array_merge($keys, $group['game_keys']);
		}

		return array_values(array_unique($keys));
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

	private function getGameAdapterPath(string $gameKey): string
	{
		return sprintf('%s/%s.xml', $this->gameAdapterDir, $gameKey);
	}

	private function sanitizeGameKey(string $gameKey): string
	{
		$gameKey = strtolower(trim($gameKey));
		$sanitized = preg_replace('/[^a-z0-9_\-.]/', '', $gameKey);
		return is_string($sanitized) ? $sanitized : '';
	}

	private function normalizeAdapterData(string $gameKey, array $data): array
	{
		$name = trim((string)($data['name'] ?? ''));
		return [
			'name' => $name !== '' ? $name : $gameKey,
			'steam_app_id' => trim((string)($data['steam_app_id'] ?? '')),
			'mods_dir' => trim((string)($data['mods_dir'] ?? '')),
			'keys_dir' => trim((string)($data['keys_dir'] ?? '')),
			'supports_hot_reload' => !empty($data['supports_hot_reload']),
			'activation_template' => trim((string)($data['activation_template'] ?? '')),
			'notes' => trim((string)($data['notes'] ?? '')),
		];
	}

	private function parseAdapterFile(string $file, string $schemaPath, bool $useSchema, ?string $forcedKey = null): ?array
	{
		$previous = libxml_use_internal_errors(true);
		$doc = new DOMDocument();
		if (!$doc->load($file)) {
			libxml_use_internal_errors($previous);
			return null;
		}

		if ($useSchema && is_file($schemaPath) && !$doc->schemaValidate($schemaPath)) {
			libxml_clear_errors();
			libxml_use_internal_errors($previous);
			return null;
		}

		$adapter = simplexml_import_dom($doc);
		if ($adapter === false) {
			libxml_use_internal_errors($previous);
			return null;
		}

		$key = $forcedKey ?? (string)($adapter['key'] ?? '');
		if ($key === '') {
			libxml_use_internal_errors($previous);
			return null;
		}

		$result = [
			'key' => $key,
			'name' => (string)($adapter['name'] ?? $key),
			'steam_app_id' => (string)($adapter->steamAppId ?? ''),
			'mods_dir' => (string)($adapter->modsDir ?? ''),
			'keys_dir' => isset($adapter->keysDir) ? (string)$adapter->keysDir : '',
			'supports_hot_reload' => filter_var((string)($adapter->supportsHotReload ?? 'false'), FILTER_VALIDATE_BOOLEAN),
			'activation_template' => (string)($adapter->activation->template ?? ''),
			'notes' => (string)($adapter->notes ?? ''),
		];

		libxml_use_internal_errors($previous);

		return $result;
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

	private function ensureDataFiles(): void
	{
		$directories = [
			$this->configDir,
			$this->gameAdapterDir,
			dirname($this->adapterMapFile),
		];

		foreach ($directories as $dir) {
			if (!is_dir($dir)) {
				mkdir($dir, 0775, true);
			}
		}

		if (!is_file($this->adapterMapFile)) {
			file_put_contents($this->adapterMapFile, json_encode([], JSON_PRETTY_PRINT));
		}
	}
	public function gameSupportsWorkshop($serverXml): bool
	{
		if (!($serverXml instanceof SimpleXMLElement)) {
			return false;
		}

		$installer = trim((string)($serverXml->installer ?? ''));
		if ($installer !== 'steamcmd') {
			return false;
		}

		$appId = $this->parseSteamAppIdFromConfig($serverXml);
		return $appId !== null;
	}

	private function parseSteamAppIdFromConfig($xml): ?string
	{
		if (!isset($xml->mods) || !isset($xml->mods->mod)) {
			return null;
		}

		$candidate = null;
		foreach ($xml->mods->mod as $mod) {
			$installerName = trim((string)($mod->installer_name ?? ''));
			if ($installerName === '' || preg_match('/\D/', $installerName)) {
				continue;
			}

			$modName = strtolower(trim((string)($mod->name ?? '')));
			$modKey = strtolower(trim((string)($mod['key'] ?? '')));

			if ($modKey === 'default' || $modName === 'none' || $modName === '') {
				return $installerName;
			}

			if ($candidate === null) {
				$candidate = $installerName;
			}
		}

		return $candidate;
	}

	private function buildWorkshopGroupKey(string $appId): string
	{
		return 'steamapp_' . $appId;
	}
}
