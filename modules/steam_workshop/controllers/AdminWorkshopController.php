<?php
declare(strict_types=1);

require_once __DIR__ . '/../lib/SteamWorkshopService.php';

class AdminWorkshopController
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
        if (!$db->isAdmin($userId)) {
            print_failure($this->lang['error_admin_only'] ?? 'Admin access required.');
            return;
        }

        echo '<link rel="stylesheet" type="text/css" href="modules/steam_workshop/steam_workshop.css" />';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processSave();
        }

        $gameKeys = $this->service->listAvailableGameKeys();
        $mappings = $this->service->getAdapterMappings();
        $adapters = $this->service->loadAdapters();
        $adapterOptions = $this->service->getAdapterOptions();

        $this->render('admin/index', [
            'lang' => $this->lang,
            'gameKeys' => $gameKeys,
            'mappings' => $mappings,
            'adapters' => $adapters,
            'adapterOptions' => $adapterOptions,
        ]);
    }

    private function processSave(): void
    {
        $payload = $_POST['mapping'] ?? [];
        if (!is_array($payload)) {
            $payload = [];
        }
        $this->service->saveAdapterMappings($payload);
        print_success($this->lang['message_mappings_saved'] ?? 'Adapter mappings saved.');
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
