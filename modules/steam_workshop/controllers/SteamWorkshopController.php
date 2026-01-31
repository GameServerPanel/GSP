<?php
declare(strict_types=1);

require_once __DIR__ . '/../lib/SteamWorkshopService.php';

class SteamWorkshopController
{
    private SteamWorkshopService $service;
    private array $lang;

    public function __construct(OGPDatabase $db)
    {
        $this->service = new SteamWorkshopService($db);
        $this->lang = $this->loadLang();
    }

    public function handle(): void
    {
        global $db;

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $isAdmin = $db->isAdmin($userId);
        $action = $_GET['action'] ?? 'index';

        if ($action === 'search') {
            $this->handleSearch($userId, $isAdmin);
            return;
        }

        echo '<link rel="stylesheet" type="text/css" href="modules/steam_workshop/steam_workshop.css" />';
        echo '<script src="modules/steam_workshop/steam_workshop.js" defer></script>';

        if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleSave($userId, $isAdmin);
            return;
        }

        if ($action === 'edit') {
            $this->handleEdit($userId, $isAdmin);
            return;
        }

        $this->renderIndex($userId, $isAdmin);
    }

    private function handleSave(int $userId, bool $isAdmin): void
    {
        $homeId = isset($_POST['home_id']) ? (int)$_POST['home_id'] : 0;
        if ($homeId <= 0) {
            print_failure($this->lang['error_missing_home'] ?? 'Home ID missing.');
            $this->renderIndex($userId, $isAdmin);
            return;
        }

        $home = $this->service->getHome($homeId, $userId, $isAdmin);
        if ($home === null) {
            print_failure($this->lang['error_home_not_found'] ?? 'Home not found.');
            $this->renderIndex($userId, $isAdmin);
            return;
        }

        $config = $this->service->buildConfigFromRequest($_POST);
        $adapterLocked = $this->applyGameAdapterOverride($home, $config);
        $this->service->saveConfig($homeId, $config);
        print_success($this->lang['message_config_saved'] ?? 'Workshop configuration saved.');

        $this->renderEdit($home, $config, $isAdmin, $adapterLocked);
    }

    private function handleEdit(int $userId, bool $isAdmin): void
    {
        $homeId = isset($_GET['home_id']) ? (int)$_GET['home_id'] : 0;
        if ($homeId <= 0) {
            print_failure($this->lang['error_missing_home'] ?? 'Home ID missing.');
            $this->renderIndex($userId, $isAdmin);
            return;
        }

        $home = $this->service->getHome($homeId, $userId, $isAdmin);
        if ($home === null) {
            print_failure($this->lang['error_home_not_found'] ?? 'Home not found.');
            $this->renderIndex($userId, $isAdmin);
            return;
        }

        $config = $this->service->loadConfig($homeId);
        $adapterLocked = $this->applyGameAdapterOverride($home, $config);
        $this->renderEdit($home, $config, $isAdmin, $adapterLocked);
    }

    private function renderIndex(int $userId, bool $isAdmin): void
    {
        $records = [];
        $homes = $this->service->listHomesForUser($userId, $isAdmin);
        foreach ($homes as $home) {
            $config = $this->service->loadConfig((int)$home['home_id']);
            $this->applyGameAdapterOverride($home, $config);
            $adapter = $this->service->getAdapterByKey($config['adapter_key']);
            $records[] = [
                'home' => $home,
                'config' => $config,
                'adapter' => $adapter,
            ];
        }

        $this->render('index', [
            'lang' => $this->lang,
            'records' => $records,
            'isAdmin' => $isAdmin,
            'adapterOptions' => $this->service->getAdapterOptions(),
        ]);
    }

    private function renderEdit(array $home, array $config, bool $isAdmin, bool $adapterLocked): void
    {
        $this->render('edit', [
            'lang' => $this->lang,
            'home' => $home,
            'config' => $config,
            'isAdmin' => $isAdmin,
            'adapterOptions' => $this->service->getAdapterOptions(),
            'adapterLocked' => $adapterLocked,
        ]);
    }

    private function handleSearch(int $userId, bool $isAdmin): void
    {
        header('Content-Type: application/json');
        $homeId = isset($_GET['home_id']) ? (int)$_GET['home_id'] : 0;
        $query = trim((string)($_GET['q'] ?? ''));
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 12;
        if ($homeId <= 0) {
            echo json_encode(['ok' => false, 'error' => $this->lang['error_missing_home'] ?? 'Home ID missing.']);
            return;
        }
        if ($query === '') {
            echo json_encode(['ok' => false, 'error' => $this->lang['error_missing_query'] ?? 'Enter a search term.']);
            return;
        }

        $home = $this->service->getHome($homeId, $userId, $isAdmin);
        if ($home === null) {
            echo json_encode(['ok' => false, 'error' => $this->lang['error_home_not_found'] ?? 'Home not found.']);
            return;
        }

        $gameKey = (string)($home['game_key'] ?? '');
        if ($gameKey === '') {
            echo json_encode(['ok' => false, 'error' => $this->lang['error_home_not_found'] ?? 'Home not found.']);
            return;
        }

        $payload = $this->service->searchWorkshopItems($gameKey, $query, $perPage, $page);
        $requestSummary = $payload['request']['summary'] ?? sprintf('REQUEST => %s | PARAMS => %s | HTTP => %s | TRANSPORT => %s',
            (string)($payload['request']['url'] ?? ''),
            http_build_query($payload['request']['params'] ?? [], '', '&'),
            (string)($payload['request']['http_code'] ?? ''),
            (string)($payload['request']['transport_error'] ?? 'none')
        );

        if ($payload['error'] !== null) {
            echo json_encode([
                'ok' => false,
                'error' => $payload['error'],
                'request' => $payload['request'],
                'status' => $requestSummary,
            ]);
            return;
        }

        $response = [
            'ok' => true,
            'results' => $payload['results'],
            'pagination' => $payload['pagination'],
            'request' => $payload['request'],
            'status' => $requestSummary,
        ];
        if (empty($payload['results'])) {
            $response['empty'] = true;
        }

        echo json_encode($response);
    }

    private function applyGameAdapterOverride(array $home, array &$config): bool
    {
        $gameKey = isset($home['game_key']) ? (string)$home['game_key'] : '';
        $mapped = $this->service->getAdapterKeyForGame($gameKey);
        if ($mapped !== null && $mapped !== '') {
            $config['adapter_key'] = $mapped;
            return true;
        }

        return false;
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
