<?php
declare(strict_types=1);

require_once __DIR__ . '/../lib/SteamWorkshopService.php';

class AdminWorkshopController
{
    private SteamWorkshopService $service;
    private array $lang;
    private ?array $adapterFormOverride = null;
    private ?string $adapterFormGameKey = null;
    private array $gameGroups = [];

    public function __construct(OGPDatabase $db)
    {
        $this->service = new SteamWorkshopService($db);
        $this->lang = $this->loadLang();
    }

    public function handle(): void
    {
        global $db;

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if (!$db->isAdmin($userId)) {
            print_failure($this->lang['error_admin_only'] ?? 'Admin access required.');
            return;
        }

        echo '<link rel="stylesheet" type="text/css" href="modules/steam_workshop/steam_workshop.css" />';

        $this->gameGroups = $this->service->listWorkshopGameGroups();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processPost();
        }

        $mappings = $this->service->getAdapterMappings();
        $adapters = $this->service->loadAdapters();
        $adapterOptions = $this->service->getAdapterOptions();
        $gameRows = $this->buildGameRows($mappings);
        $requestedGame = $this->sanitizeGameKeyInput($_GET['adapter_game'] ?? '');
        $activeGame = $this->adapterFormGameKey !== null ? $this->adapterFormGameKey : $requestedGame;

        $this->render('admin/index', [
            'lang' => $this->lang,
            'mappings' => $mappings,
            'adapters' => $adapters,
            'adapterOptions' => $adapterOptions,
            'gameRows' => $gameRows,
            'activeGameKey' => $activeGame,
        ]);
    }

    private function processPost(): void
    {
        $action = $_POST['admin_action'] ?? 'save_mappings';
        switch ($action) {
            case 'save_adapter':
                $this->processAdapterSave();
                break;
            case 'delete_adapter':
                $this->processAdapterDelete();
                break;
            case 'save_mappings':
            default:
                $this->processSaveMappings();
                break;
        }
    }

    private function processSaveMappings(): void
    {
        $payload = $_POST['mapping'] ?? [];
        if (!is_array($payload)) {
            $payload = [];
        }

        $fanOut = [];
        $groupIndex = $this->indexGameGroups();
        foreach ($payload as $groupKey => $adapterKey) {
            $groupKey = (string)$groupKey;
            $adapterKey = (string)$adapterKey;
            if (!isset($groupIndex[$groupKey])) {
                continue;
            }

            foreach ($groupIndex[$groupKey] as $gameKey) {
                $fanOut[$gameKey] = $adapterKey;
            }
        }

        $this->service->saveAdapterMappings($fanOut);
        print_success($this->lang['message_mappings_saved'] ?? 'Adapter mappings saved.');
    }

    private function processAdapterSave(): void
    {
        $gameKey = $this->sanitizeGameKeyInput($_POST['game_key'] ?? '');
        if ($gameKey === '') {
            print_failure($this->lang['error_game_key_required'] ?? 'Game key required.');
            return;
        }

        $payload = $_POST['adapter'] ?? [];
        if (!is_array($payload)) {
            $payload = [];
        }

        try {
            $this->service->saveGameAdapter($gameKey, $payload);
            $this->propagateAdapterMapping($gameKey);
            print_success($this->lang['message_adapter_saved'] ?? 'Adapter saved.');
            $this->adapterFormOverride = null;
            $this->adapterFormGameKey = null;
        } catch (RuntimeException $e) {
            $this->adapterFormGameKey = $gameKey;
            $this->adapterFormOverride = [
                'name' => trim((string)($payload['name'] ?? '')),
                'steam_app_id' => trim((string)($payload['steam_app_id'] ?? '')),
                'mods_dir' => trim((string)($payload['mods_dir'] ?? '')),
                'keys_dir' => trim((string)($payload['keys_dir'] ?? '')),
                'supports_hot_reload' => !empty($payload['supports_hot_reload']),
                'activation_template' => trim((string)($payload['activation_template'] ?? '')),
                'notes' => trim((string)($payload['notes'] ?? '')),
            ];
            print_failure($e->getMessage());
        }
    }

    private function processAdapterDelete(): void
    {
        $gameKey = $this->sanitizeGameKeyInput($_POST['game_key'] ?? '');
        if ($gameKey === '') {
            print_failure($this->lang['error_game_key_required'] ?? 'Game key required.');
            return;
        }

        if ($this->service->deleteGameAdapter($gameKey)) {
            $this->clearGroupMappings($gameKey);
            print_success($this->lang['message_adapter_deleted'] ?? 'Adapter deleted.');
        } else {
            print_failure($this->lang['error_adapter_delete_failed'] ?? 'Unable to delete adapter.');
        }
    }

    private function buildGameRows(array $mappings): array
    {
        $rows = [];
        foreach ($this->gameGroups as $group) {
            $primaryKey = $group['primary_game_key'];
            $override = ($this->adapterFormGameKey === $primaryKey) ? $this->adapterFormOverride : null;

            $mappingValues = [];
            foreach ($group['game_keys'] as $gameKey) {
                if (isset($mappings[$gameKey]) && $mappings[$gameKey] !== '') {
                    $mappingValues[$mappings[$gameKey]] = true;
                }
            }

            $rows[] = [
                'group_key' => $group['group_key'],
                'app_id' => $group['app_id'],
                'game_name' => $group['game_name'],
                'game_keys' => $group['game_keys'],
                'primary_game_key' => $primaryKey,
                'mixed_mapping' => count($mappingValues) > 1,
                'selected_adapter' => count($mappingValues) === 1 ? array_key_first($mappingValues) : '',
                'exists' => $this->service->gameAdapterExists($primaryKey),
                'adapter' => $this->service->getGameAdapter($primaryKey),
                'updated_at' => $this->service->getGameAdapterUpdatedAt($primaryKey),
                'form' => $this->service->getAdapterFormData($primaryKey, $override),
            ];
        }

        return $rows;
    }

    private function indexGameGroups(): array
    {
        $index = [];
        foreach ($this->gameGroups as $group) {
            $index[$group['group_key']] = $group['game_keys'];
        }

        return $index;
    }

    private function propagateAdapterMapping(string $primaryGameKey): void
    {
        foreach ($this->gameGroups as $group) {
            if (!in_array($primaryGameKey, $group['game_keys'], true)) {
                continue;
            }

            foreach ($group['game_keys'] as $gameKey) {
                $this->service->upsertAdapterMapping($gameKey, $primaryGameKey);
            }
            return;
        }

        $this->service->upsertAdapterMapping($primaryGameKey, $primaryGameKey);
    }

    private function clearGroupMappings(string $primaryGameKey): void
    {
        foreach ($this->gameGroups as $group) {
            if (!in_array($primaryGameKey, $group['game_keys'], true)) {
                continue;
            }

            foreach ($group['game_keys'] as $gameKey) {
                $this->service->removeAdapterMapping($gameKey, $primaryGameKey);
            }
            return;
        }

        $this->service->removeAdapterMapping($primaryGameKey, $primaryGameKey);
    }

    private function sanitizeGameKeyInput($value): string
    {
        $gameKey = strtolower(trim((string)$value));
        $sanitized = preg_replace('/[^a-z0-9_\-.]/', '', $gameKey);
        return is_string($sanitized) ? $sanitized : '';
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        $lang = $this->lang;
        require __DIR__ . '/../views/' . $view . '.php';
    }

    private function loadLang(): array
    {
        $langFile = __DIR__ . '/../lang/en_US.php';
        if (is_file($langFile)) {
            $strings = require $langFile;
            if (is_array($strings)) {
                return $strings;
            }
        }

        return [];
    }
}
