<?php
declare(strict_types=1);

require_once __DIR__ . '/../lib/SteamWorkshopService.php';

class AdminWorkshopController
{
    private SteamWorkshopService $service;
    private array $lang;
    private ?array $adapterFormOverride = null;
    private ?string $adapterFormGameKey = null;

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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processPost();
        }

        $gameKeys = $this->service->listAvailableGameKeys();
        $mappings = $this->service->getAdapterMappings();
        $adapters = $this->service->loadAdapters();
        $adapterOptions = $this->service->getAdapterOptions();
        $gameRows = $this->buildGameRows($gameKeys);

        $activeGame = $this->resolveActiveGameKey();
        $adapterForm = $activeGame !== ''
            ? $this->service->getAdapterFormData($activeGame, $this->adapterFormOverride)
            : null;

        $this->render('admin/index', [
            'lang' => $this->lang,
            'gameKeys' => $gameKeys,
            'mappings' => $mappings,
            'adapters' => $adapters,
            'adapterOptions' => $adapterOptions,
            'gameRows' => $gameRows,
            'adapterForm' => $adapterForm,
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
        $this->service->saveAdapterMappings($payload);
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
            $this->service->upsertAdapterMapping($gameKey, $gameKey);
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
            $this->service->removeAdapterMapping($gameKey, $gameKey);
            print_success($this->lang['message_adapter_deleted'] ?? 'Adapter deleted.');
        } else {
            print_failure($this->lang['error_adapter_delete_failed'] ?? 'Unable to delete adapter.');
        }
    }

    private function buildGameRows(array $gameKeys): array
    {
        $rows = [];
        foreach ($gameKeys as $gameKey) {
            $rows[] = [
                'game_key' => $gameKey,
                'exists' => $this->service->gameAdapterExists($gameKey),
                'adapter' => $this->service->getGameAdapter($gameKey),
                'updated_at' => $this->service->getGameAdapterUpdatedAt($gameKey),
            ];
        }

        return $rows;
    }

    private function resolveActiveGameKey(): string
    {
        if ($this->adapterFormGameKey !== null) {
            return $this->adapterFormGameKey;
        }

        $queryKey = $_GET['adapter_game'] ?? '';
        return $this->sanitizeGameKeyInput($queryKey);
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
