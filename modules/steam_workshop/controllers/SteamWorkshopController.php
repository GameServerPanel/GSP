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

        echo '<link rel="stylesheet" type="text/css" href="modules/steam_workshop/steam_workshop.css" />';

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
        $this->service->saveConfig($homeId, $config);
        print_success($this->lang['message_config_saved'] ?? 'Workshop configuration saved.');

        $this->renderEdit($home, $config, $isAdmin);
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
        $this->renderEdit($home, $config, $isAdmin);
    }

    private function renderIndex(int $userId, bool $isAdmin): void
    {
        $records = [];
        $homes = $this->service->listHomesForUser($userId, $isAdmin);
        foreach ($homes as $home) {
            $config = $this->service->loadConfig((int)$home['home_id']);
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

    private function renderEdit(array $home, array $config, bool $isAdmin): void
    {
        $this->render('edit', [
            'lang' => $this->lang,
            'home' => $home,
            'config' => $config,
            'isAdmin' => $isAdmin,
            'adapterOptions' => $this->service->getAdapterOptions(),
        ]);
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
