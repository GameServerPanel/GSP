<?php
declare(strict_types=1);

class SteamWorkshopService
{
	private const MIN_INTERVAL = 15;
	private const MAX_INTERVAL = 360;
	private const STEAM_WORKSHOP_DETAIL_URL = 'https://steamcommunity.com/sharedfiles/filedetails/';

	private OGPDatabase $db;
	private string $configDir;
	private string $adapterDir;
	private string $adapterMapFile;
	private string $gameAdapterDir;
	private string $serverConfigDir;
	private string $logDir;
	private string $apiLogFile;
	private string $steamCmdLogDir;
	private string $scraperScript;

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
		$this->logDir = __DIR__ . '/../logs';
		$this->steamCmdLogDir = $this->logDir . '/steamcmd';
		$this->apiLogFile = $this->logDir . '/steam_api.log';
		$this->scraperScript = __DIR__ . '/../bin/workshop_scrape.sh';

		foreach ([$this->configDir, $this->gameAdapterDir, $this->logDir, $this->steamCmdLogDir] as $dir) {
			if (!is_dir($dir)) {
				mkdir($dir, 0775, true);
			}
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
		foreach ((array)$config['workshop_items'] as $item) {
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
		$selectedItems = $this->parseSelectedItemsJson((string)($input['selected_items'] ?? ''));
		if (!empty($selectedItems)) {
			$items = $selectedItems;
			$rawMods = $this->serializeWorkshopItems($selectedItems);
		} else {
			$items = $this->parseWorkshopItems($rawMods);
		}

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
		foreach ((array)$lines as $line) {
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

	/**
	 * Example usage:
	 * $service->installWorkshopItem('/opt/steamcmd/steamcmd.sh', '221100', '1234567890');
	 */
	public function installWorkshopItem(string $steamCmdPath, string $appId, string $workshopId, ?string $username = null, ?string $password = null, ?string $logFile = null): array
	{
		$logPath = $logFile ?? sprintf('%s/%s-%s-%s.log', $this->steamCmdLogDir, $appId, $workshopId, date('Ymd_His'));
		$appId = trim($appId);
		$workshopId = preg_replace('/[^0-9]/', '', $workshopId);

		if ($steamCmdPath === '' || !is_file($steamCmdPath)) {
			$message = sprintf('SteamCMD binary not found at %s', $steamCmdPath);
			$this->appendLog($logPath, $message);
			return ['success' => false, 'error' => $message, 'log_file' => $logPath, 'attempts' => []];
		}

		$attempts = [];
		$logins = [['user' => 'anonymous', 'password' => null]];
		if ($username !== null && $username !== '') {
			$logins[] = ['user' => $username, 'password' => $password];
		}

		foreach ((array)$logins as $credentials) {
			$this->appendLog($logPath, sprintf('SteamCMD download start app=%s workshop=%s login=%s', $appId, $workshopId, $credentials['user']));
			$result = $this->runSteamCmdDownload($steamCmdPath, $appId, $workshopId, $credentials['user'], $credentials['password']);
			$this->appendSteamCmdOutput($logPath, $result['output']);
			$this->appendLog($logPath, sprintf('SteamCMD exit code %d for login %s', $result['exit_code'], $credentials['user']));
			$attempts[] = ['user' => $credentials['user'], 'exit_code' => $result['exit_code']];
			if ($result['exit_code'] === 0) {
				return ['success' => true, 'log_file' => $logPath, 'attempts' => $attempts];
			}
		}

		$message = 'All SteamCMD login attempts failed.';
		$this->appendLog($logPath, $message);
		return ['success' => false, 'error' => $message, 'log_file' => $logPath, 'attempts' => $attempts];
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
		foreach ((array)$mappings as $gameKey => $adapterKey) {
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
		foreach ((array)$decoded as $gameKey => $adapterKey) {
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

		foreach ((array)$groups as &$group) {
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

	public function getSteamAppIdForGameKey(string $gameKey): ?string
	{
		$gameKey = trim($gameKey);
		if ($gameKey === '') {
			return null;
		}

		$adapterKey = $this->getAdapterKeyForGame($gameKey);
		if ($adapterKey !== null && $adapterKey !== '') {
			$adapter = $this->getAdapterByKey($adapterKey);
			$adapterAppId = isset($adapter['steam_app_id']) ? trim((string)$adapter['steam_app_id']) : '';
			if ($adapterAppId !== '') {
				return $adapterAppId;
			}
		}

		$xml = $this->loadServerConfigXml($gameKey);
		if ($xml === null) {
			return null;
		}

		return $this->parseSteamAppIdFromConfig($xml);
	}

	/**
	 * Example usage:
	 * $service->searchWorkshopItems('dayz', 'weapon', 12, 1);
	 */
	public function searchWorkshopItems(string $gameKey, string $query, int $perPage = 12, int $page = 1): array
	{
		$query = trim($query);
		$payload = [
			'results' => [],
			'pagination' => [
				'page' => max(1, $page),
				'per_page' => max(1, min(100, $perPage)),
				'total' => 0,
				'has_more' => false,
			],
			'error' => null,
			'request' => [
				'backend' => 'api',
				'url' => null,
				'params' => [],
				'http_code' => null,
				'transport_error' => null,
				'summary' => null,
				'attempts' => [],
			],
		];

		if ($query === '') {
			$payload['error'] = 'Enter a Workshop ID or keyword.';
			return $payload;
		}

		$appId = $this->getSteamAppIdForGameKey($gameKey);
		if ($appId === null) {
			$payload['error'] = 'Workshop search is not configured for this game.';
			$this->logApiFailure(sprintf('Missing Steam AppID for game key %s during search.', $gameKey));
			return $payload;
		}

		if (ctype_digit($query)) {
			$detail = $this->fetchWorkshopItemByScrape($query);
			$payload['request'] = $detail['request'];
			if ($detail['error'] !== null) {
				$payload['error'] = $detail['error'];
				return $payload;
			}
			if ($detail['item'] !== null) {
				$payload['results'][] = $detail['item'];
				$payload['pagination']['total'] = 1;
			}
			return $payload;
		}

		$scrapeResult = $this->scrapeWorkshopItems($appId, $query, $payload['pagination']['per_page'], $payload['pagination']['page']);
		$payload['request'] = $scrapeResult['request'];
		if (!empty($scrapeResult['attempts'])) {
			$payload['request']['attempts'] = $scrapeResult['attempts'];
		}
		if ($scrapeResult['success']) {
			$payload['results'] = $scrapeResult['results'];
			$payload['pagination']['total'] = $scrapeResult['total'];
			$payload['pagination']['has_more'] = $scrapeResult['has_more'];
		} else {
			$payload['error'] = $scrapeResult['error'] ?? 'Steam Workshop scrape failed.';
			$this->logApiFailure(sprintf('Steam Workshop scrape failed (app=%s query="%s" page=%d): %s', $appId, $query, $payload['pagination']['page'], $payload['error']));
		}

		return $payload;
	}

	private function fetchWorkshopItemByScrape(string $id): array
	{
		$sanitizedId = preg_replace('/[^0-9]/', '', $id);
		$request = [
			'backend' => 'scraper_http',
			'url' => self::STEAM_WORKSHOP_DETAIL_URL,
			'params' => ['id' => $sanitizedId],
			'http_code' => null,
			'transport_error' => null,
		];

		if ($sanitizedId === '') {
			$request['summary'] = $this->formatRequestSummary($request);
			return ['item' => null, 'request' => $request, 'error' => 'Invalid Workshop ID.'];
		}

		$response = $this->httpGet($request['url'], $request['params'], $this->getScraperUserAgent());
		$request['url'] = $response['url'] ?? $request['url'];
		$request['http_code'] = $response['http_code'];
		$request['transport_error'] = $response['error'];
		$request['summary'] = $this->formatRequestSummary($request);

		if ($response['error'] !== null || $response['http_code'] < 200 || $response['http_code'] >= 300 || $response['body'] === null) {
			$reason = $response['error'] !== null ? $response['error'] : 'HTTP ' . $response['http_code'];
			return [
				'item' => null,
				'request' => $request,
				'error' => 'Steam Community detail request failed: ' . $reason,
			];
		}

		$title = $this->parseWorkshopTitle((string)$response['body']);
		if ($title === '') {
			$title = '@' . $sanitizedId;
		}

		return [
			'item' => [
				'id' => $sanitizedId,
				'label' => $title,
				'author' => '',
				'preview_url' => '',
				'enabled' => true,
				'source' => 'search',
			],
			'request' => $request,
			'error' => null,
		];
	}

	private function scrapeWorkshopItems(string $appId, string $query, int $perPage, int $page): array
	{
		$attempts = [];
		$shellError = null;

		if ($this->isScraperAvailable()) {
			$shellResult = $this->runShellScraper($appId, $query, $perPage, $page);
			$attempts[] = $shellResult['request'];
			if ($shellResult['success'] && !empty($shellResult['results'])) {
				$shellResult['attempts'] = $attempts;
				return $shellResult;
			}
			$shellError = $shellResult['error'] ?? null;
		} else {
			$shellError = $this->isWindowsPlatform()
				? 'Shell scraper helper is disabled on Windows hosts.'
				: 'Workshop scraper helper script is missing or unreadable.';
			$attempts[] = $this->buildShellUnavailableContext($shellError);
		}

		$httpResult = $this->scrapeWorkshopItemsHttp($appId, $query, $perPage, $page);
		$attempts[] = $httpResult['request'];
		if ($shellError !== null && !$httpResult['success']) {
			$httpResult['error'] = trim(($httpResult['error'] ?? '') . ' | Shell: ' . $shellError);
		}
		$httpResult['attempts'] = $attempts;

		return $httpResult;
	}

	private function runShellScraper(string $appId, string $query, int $perPage, int $page): array
	{
		$params = [
			'appid' => $appId,
			'searchtext' => $query,
			'page' => $page,
			'limit' => $perPage,
		];
		$request = [
			'backend' => 'scraper',
			'url' => 'https://steamcommunity.com/workshop/browse/',
			'params' => $params,
			'http_code' => null,
			'transport_error' => null,
			'command' => null,
			'exit_code' => null,
			'stderr' => null,
		];

		if (!$this->isScraperAvailable()) {
			$request['summary'] = $this->formatRequestSummary($request);
			return [
				'success' => false,
				'error' => 'Workshop scraper helper is not available.',
				'results' => [],
				'total' => 0,
				'has_more' => false,
				'request' => $request,
			];
		}

		$queryArg = $this->sanitizeScraperQuery($query);
		$command = sprintf(
			'bash %s %s %s %s %s',
			escapeshellarg($this->scraperScript),
			escapeshellarg($appId),
			escapeshellarg($queryArg),
			escapeshellarg((string)$page),
			escapeshellarg((string)$perPage)
		);
		$request['command'] = $command;

		$descriptorSpec = [
			0 => ['pipe', 'r'],
			1 => ['pipe', 'w'],
			2 => ['pipe', 'w'],
		];
		$process = proc_open($command, $descriptorSpec, $pipes);
		if (!is_resource($process)) {
			$request['summary'] = $this->formatRequestSummary($request);
			return [
				'success' => false,
				'error' => 'Unable to start Workshop scraper helper.',
				'results' => [],
				'total' => 0,
				'has_more' => false,
				'request' => $request,
			];
		}

		fclose($pipes[0]);
		$stdout = stream_get_contents($pipes[1]) ?: '';
		$stderr = stream_get_contents($pipes[2]) ?: '';
		fclose($pipes[1]);
		fclose($pipes[2]);
		$exitCode = (int)proc_close($process);
		$request['exit_code'] = $exitCode;
		$request['stderr'] = trim($stderr);

		$results = [];
		$lines = preg_split('/\r\n|\r|\n/', trim($stdout));
		if (is_array($lines)) {
			foreach ((array)$lines as $line) {
				if ($line === '') {
					continue;
				}
				$parts = explode("\t", $line, 2);
				$id = preg_replace('/[^0-9]/', '', $parts[0] ?? '');
				if ($id === '') {
					continue;
				}
				$title = isset($parts[1]) ? trim($parts[1]) : '';
				if ($title === '') {
					$title = '@' . $id;
				}
				$results[] = [
					'id' => $id,
					'label' => $title,
					'author' => '',
					'preview_url' => '',
					'time_updated' => null,
					'subscriptions' => 0,
					'source' => 'scraper',
				];
				if (count((array)$results) >= $perPage) {
					break;
				}
			}
		}

		$request['summary'] = $this->formatRequestSummary($request);
		$success = ($exitCode === 0);
		$errorMessage = $success ? null : ($request['stderr'] !== '' ? $request['stderr'] : 'Scraper exited with code ' . $exitCode);

		return [
			'success' => $success,
			'error' => $errorMessage,
			'results' => $results,
			'total' => count((array)$results),
			'has_more' => count((array)$results) >= $perPage,
			'request' => $request,
		];
	}

	private function scrapeWorkshopItemsHttp(string $appId, string $query, int $perPage, int $page): array
	{
		$perPage = max(1, $perPage);
		$params = [
			'appid' => $appId,
			'browsesort' => 'textsearch',
			'section' => 'readytouseitems',
			'searchtext' => $this->sanitizeScraperQuery($query),
			'p' => $page,
		];
		$request = [
			'backend' => 'scraper_http',
			'url' => 'https://steamcommunity.com/workshop/browse/',
			'params' => $params,
			'http_code' => null,
			'transport_error' => null,
		];

		$response = $this->httpGet($request['url'], $params, $this->getScraperUserAgent());
		$request['url'] = $response['url'] ?? $request['url'];
		$request['http_code'] = $response['http_code'];
		$request['transport_error'] = $response['error'];

		if ($response['error'] !== null || $response['http_code'] < 200 || $response['http_code'] >= 300 || $response['body'] === null) {
			$request['summary'] = $this->formatRequestSummary($request);
			$reason = $response['error'] !== null ? $response['error'] : 'HTTP ' . $response['http_code'];
			return [
				'success' => false,
				'error' => 'Steam Community browse request failed: ' . $reason,
				'results' => [],
				'total' => 0,
				'has_more' => false,
				'request' => $request,
			];
		}

		$html = (string)$response['body'];
		$matches = [];
		preg_match_all('/sharedfiles\/filedetails\/\?id=([0-9]+)/i', $html, $matches);
		$rawIds = $matches[1] ?? [];
		$uniqueIds = [];
		foreach ((array)$rawIds as $rawId) {
			$id = preg_replace('/[^0-9]/', '', (string)$rawId);
			if ($id === '' || isset($uniqueIds[$id])) {
				continue;
			}
			$uniqueIds[$id] = true;
		}
		$orderedIds = array_keys($uniqueIds);
		$hasMore = count((array)$orderedIds) > $perPage;
		$sliceIds = array_slice($orderedIds, 0, $perPage);

		$results = [];
		foreach ((array)$sliceIds as $id) {
			$detailResponse = $this->httpGet(self::STEAM_WORKSHOP_DETAIL_URL, ['id' => $id], $this->getScraperUserAgent());
			$title = '';
			if ($detailResponse['error'] === null && $detailResponse['http_code'] >= 200 && $detailResponse['http_code'] < 300 && $detailResponse['body'] !== null) {
				$title = $this->parseWorkshopTitle((string)$detailResponse['body']);
			}
			if ($title === '') {
				$title = '@' . $id;
			}

			$results[] = [
				'id' => $id,
				'label' => $title,
				'author' => '',
				'preview_url' => '',
				'time_updated' => null,
				'subscriptions' => 0,
				'source' => 'scraper_http',
			];
		}

		$request['summary'] = $this->formatRequestSummary($request);

		return [
			'success' => true,
			'error' => null,
			'results' => $results,
			'total' => count((array)$results),
			'has_more' => $hasMore,
			'request' => $request,
		];
	}

	private function buildShellUnavailableContext(string $message): array
	{
		$context = [
			'backend' => 'scraper',
			'url' => 'https://steamcommunity.com/workshop/browse/',
			'params' => [],
			'http_code' => null,
			'transport_error' => $message,
			'command' => '[unavailable]',
			'exit_code' => null,
			'stderr' => $message,
		];
		$context['summary'] = $this->formatRequestSummary($context);
		return $context;
	}

	private function isScraperAvailable(): bool
	{
		if ($this->isWindowsPlatform()) {
			return false;
		}

		return function_exists('proc_open') && is_file($this->scraperScript) && is_readable($this->scraperScript);
	}

	private function isWindowsPlatform(): bool
	{
		return DIRECTORY_SEPARATOR === '\\';
	}

	private function sanitizeScraperQuery(string $query): string
	{
		$query = preg_replace('/[\r\n\t]+/', ' ', $query);
		$query = trim((string)$query);
		if (function_exists('mb_substr')) {
			return mb_substr($query, 0, 200);
		}
		return substr($query, 0, 200);
	}

	private function getScraperUserAgent(): string
	{
		return 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36';
	}

	private function parseWorkshopTitle(string $html): string
	{
		if (preg_match('/<title>(.*?)<\/title>/is', $html, $matches)) {
			$title = html_entity_decode(trim($matches[1]), ENT_QUOTES, 'UTF-8');
			if ($title !== '') {
				$clean = preg_replace('/ - Steam (Community|Workshop).*$/i', '', $title);
				if (is_string($clean)) {
					$clean = trim($clean);
				}
				return $clean !== '' ? $clean : $title;
			}
		}

		return '';
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
		if (array_key_exists($key, (array)$adapters)) {
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
			$item['author'] = trim((string)($item['author'] ?? ''));
			$item['preview_url'] = trim((string)($item['preview_url'] ?? ''));
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

		foreach ((array)$directories as $dir) {
			if (!is_dir($dir)) {
				mkdir($dir, 0775, true);
			}
		}

		if (!is_file($this->adapterMapFile)) {
			file_put_contents($this->adapterMapFile, json_encode([], JSON_PRETTY_PRINT));
		}

		if (!is_file($this->apiLogFile)) {
			touch($this->apiLogFile);
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

	private function loadServerConfigXml(string $gameKey): ?SimpleXMLElement
	{
		$gameKey = $this->sanitizeGameKey($gameKey);
		if ($gameKey === '') {
			return null;
		}

		$directPath = sprintf('%s/%s.xml', $this->serverConfigDir, $gameKey);
		if (is_file($directPath)) {
			$xml = @simplexml_load_file($directPath);
			if ($xml !== false) {
				return $xml;
			}
		}

		foreach (glob($this->serverConfigDir . '/*.xml') as $file) {
			$xml = @simplexml_load_file($file);
			if ($xml === false) {
				continue;
			}
			$configuredKey = isset($xml->game_key) ? $this->sanitizeGameKey((string)$xml->game_key) : '';
			if ($configuredKey === $gameKey) {
				return $xml;
			}
		}

		return null;
	}

	private function parseSelectedItemsJson(string $json): array
	{
		if ($json === '') {
			return [];
		}

		$decoded = json_decode($json, true);
		if (!is_array($decoded)) {
			return [];
		}

		$result = [];
		foreach ((array)$decoded as $item) {
			if (!is_array($item)) {
				continue;
			}
			$id = preg_replace('/[^0-9]/', '', (string)($item['id'] ?? ''));
			if ($id === '') {
				continue;
			}
			$label = trim((string)($item['label'] ?? ''));
			if ($label === '') {
				$label = '@' . $id;
			}
			$result[$id] = [
				'id' => $id,
				'label' => $label,
				'author' => trim((string)($item['author'] ?? '')),
				'preview_url' => trim((string)($item['preview_url'] ?? '')),
				'enabled' => isset($item['enabled']) ? (bool)$item['enabled'] : true,
				'source' => trim((string)($item['source'] ?? 'search')),
			];
		}

		return array_values($result);
	}

	private function serializeWorkshopItems(array $items): string
	{
		$lines = [];
		foreach ((array)$items as $item) {
			$id = preg_replace('/[^0-9]/', '', (string)($item['id'] ?? ''));
			if ($id === '') {
				continue;
			}
			$label = trim((string)($item['label'] ?? ''));
			if ($label === '') {
				$label = '@' . $id;
			}
			$lines[] = $id . ',' . $label;
		}

		return implode(PHP_EOL, $lines);
	}


	private function httpGet(string $url, array $params = [], ?string $userAgent = null): array
	{
		if (!function_exists('curl_init')) {
			return ['body' => null, 'http_code' => 0, 'error' => 'PHP cURL extension is required', 'url' => $url, 'params' => $params];
		}

		$queryString = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
		$fullUrl = $queryString === '' ? $url : $url . (strpos($url, '?') === false ? '?' : '&') . $queryString;
		$ch = curl_init($fullUrl);
		if ($ch === false) {
			return ['body' => null, 'http_code' => 0, 'error' => 'Unable to initialize cURL', 'url' => $fullUrl, 'params' => $params];
		}

		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_TIMEOUT => 20,
			CURLOPT_ENCODING => '',
			CURLOPT_USERAGENT => $userAgent ?? 'GSP-Workshop/1.0 (+https://github.com/GameServerPanel/GSP)',
			CURLOPT_HTTPHEADER => ['Accept: text/html,application/xhtml+xml;q=0.9,*/*;q=0.8'],
		]);

		$body = curl_exec($ch);
		$error = curl_errno($ch) ? curl_error($ch) : null;
		$status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return [
			'body' => $error === null ? $body : null,
			'http_code' => $status,
			'error' => $error,
			'url' => $fullUrl,
			'params' => $params,
		];
	}

	private function formatRequestSummary(array $request): string
	{
		$backend = strtolower((string)($request['backend'] ?? 'api'));
		$params = http_build_query($request['params'] ?? [], '', '&');
		if ($backend === 'scraper') {
			$command = (string)($request['command'] ?? '');
			$exit = (string)($request['exit_code'] ?? '');
			$stderr = trim((string)($request['stderr'] ?? 'none'));
			if ($stderr === '') {
				$stderr = 'none';
			}
			return sprintf('SCRAPER => COMMAND => %s | PARAMS => %s | EXIT => %s | STDERR => %s', $command, $params, $exit, $stderr);
		} elseif ($backend === 'scraper_http') {
			$url = (string)($request['url'] ?? '');
			$http = (string)($request['http_code'] ?? '');
			$error = (string)($request['transport_error'] ?? 'none');
			return sprintf('SCRAPER_HTTP => URL => %s | PARAMS => %s | HTTP => %s | TRANSPORT => %s', $url, $params, $http, $error);
		}

		$url = (string)($request['url'] ?? '');
		$http = (string)($request['http_code'] ?? '');
		$error = (string)($request['transport_error'] ?? 'none');
		return sprintf('API REQUEST => %s | PARAMS => %s | HTTP => %s | TRANSPORT => %s', $url, $params, $http, $error);
	}

	private function runSteamCmdDownload(string $steamCmdPath, string $appId, string $workshopId, string $username, ?string $password): array
	{
		$command = [$steamCmdPath, '+login', $username];
		if ($username !== 'anonymous' && $password !== null && $password !== '') {
			$command[] = $password;
		}
		$command = array_merge($command, ['+workshop_download_item', $appId, $workshopId, 'validate', '+quit']);

		$descriptorSpec = [
			0 => ['pipe', 'r'],
			1 => ['pipe', 'w'],
			2 => ['pipe', 'w'],
		];
		$process = proc_open($command, $descriptorSpec, $pipes);
		if (!is_resource($process)) {
			return ['exit_code' => 1, 'output' => ['Unable to start steamcmd process.']];
		}

		fclose($pipes[0]);
		$stdout = stream_get_contents($pipes[1]) ?: '';
		$stderr = stream_get_contents($pipes[2]) ?: '';
		fclose($pipes[1]);
		fclose($pipes[2]);
		$exitCode = (int)proc_close($process);
		$combined = trim($stdout . PHP_EOL . $stderr);
		$lines = $combined === '' ? [] : preg_split('/\r\n|\r|\n/', $combined);

		return ['exit_code' => $exitCode, 'output' => is_array($lines) ? $lines : []];
	}

	private function appendLog(string $file, string $message): void
	{
		$dir = dirname($file);
		if (!is_dir($dir)) {
			mkdir($dir, 0775, true);
		}
		file_put_contents($file, sprintf('[%s] %s%s', date('Y-m-d H:i:s'), $message, PHP_EOL), FILE_APPEND);
	}

	private function appendSteamCmdOutput(string $logFile, array $lines): void
	{
		if (empty($lines)) {
			return;
		}
		file_put_contents($logFile, implode(PHP_EOL, $lines) . PHP_EOL, FILE_APPEND);
	}

	private function logApiFailure(string $message): void
	{
		$this->appendLog($this->apiLogFile, $message);
	}


	private function buildWorkshopGroupKey(string $appId): string
	{
		return 'steamapp_' . $appId;
	}
}
