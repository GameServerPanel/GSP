<?php
declare(strict_types=1);
/*
 * OGP / GSP – Steam Workshop
 * WorkshopModController: user-facing mod management per game server.
 *
 * Actions (via ?action=...):
 *   mods        → show installed mods + available cached mods for a server
 *   install     → install a mod (POST: home_id, workshop_id)
 *   remove      → remove a mod  (POST: home_id, workshop_id)
 *   toggle      → enable/disable (POST: home_id, workshop_id, enabled)
 *   load_order  → update load order (POST: home_id, workshop_id, load_order)
 *   sync        → sync now (POST: home_id, workshop_id)
 *   search      → JSON search (GET: home_id, q) – reuses SteamWorkshopService
 */

require_once __DIR__ . '/../lib/WorkshopRepository.php';
require_once __DIR__ . '/../lib/WorkshopInstaller.php';
require_once __DIR__ . '/../lib/SteamWorkshopService.php';

class WorkshopModController
{
    private WorkshopRepository   $repo;
    private WorkshopInstaller    $installer;
    private SteamWorkshopService $searchService;
    private array $lang;

    public function __construct(OGPDatabase $db)
    {
        $this->repo          = new WorkshopRepository($db);
        $this->installer     = new WorkshopInstaller($this->repo);
        $this->searchService = new SteamWorkshopService($db);
        $this->lang          = $this->loadLang();
    }

    // ------------------------------------------------------------------
    // Dispatch
    // ------------------------------------------------------------------

    public function handle(): void
    {
        global $db;

        $userId  = (int)($_SESSION['user_id'] ?? 0);
        $isAdmin = $db->isAdmin($userId);
        $action  = $_GET['action'] ?? 'index';

        // JSON endpoint – no HTML output
        if ($action === 'search') {
            $this->handleSearch($userId, $isAdmin);
            return;
        }

        echo '<link rel="stylesheet" type="text/css" href="modules/steam_workshop/steam_workshop.css" />';
        echo '<script src="modules/steam_workshop/steam_workshop.js" defer></script>';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postAction = $_POST['ws_action'] ?? $action;
            switch ($postAction) {
                case 'install':
                    $this->handleInstall($userId, $isAdmin);
                    return;
                case 'remove':
                    $this->handleRemove($userId, $isAdmin);
                    return;
                case 'toggle':
                    $this->handleToggle($userId, $isAdmin);
                    return;
                case 'load_order':
                    $this->handleLoadOrder($userId, $isAdmin);
                    return;
                case 'sync':
                    $this->handleSync($userId, $isAdmin);
                    return;
                case 'save_settings':
                    $this->handleSaveSettings($userId, $isAdmin);
                    return;
                case 'queue_update':
                    $this->handleQueueUpdate($userId, $isAdmin);
                    return;
            }
        }

        switch ($action) {
            case 'mods':
                $this->handleModsPage($userId, $isAdmin);
                break;
            default:
                $this->handleIndex($userId, $isAdmin);
                break;
        }
    }

    // ------------------------------------------------------------------
    // Pages
    // ------------------------------------------------------------------

    private function handleIndex(int $userId, bool $isAdmin): void
    {
        $homes   = $this->getHomesForUser($userId, $isAdmin);
        $records = [];

        foreach ((array)$homes as $home) {
            $homeId  = (int)($home['home_id'] ?? 0);
            $appId   = $this->searchService->getSteamAppIdForGameKey((string)($home['game_key'] ?? ''));
            $profile = $appId !== null ? $this->repo->getProfileByAppId($appId) : null;
            $mods    = $profile !== null ? $this->repo->listModsForHome($homeId) : [];

            $records[] = [
                'home'    => $home,
                'profile' => $profile,
                'mods'    => $mods,
            ];
        }

        $this->render('user_workshop_index', [
            'lang'    => $this->lang,
            'records' => $records,
            'isAdmin' => $isAdmin,
        ]);
    }

    private function handleModsPage(int $userId, bool $isAdmin): void
    {
        $homeId = (int)($_GET['home_id'] ?? 0);
        if ($homeId <= 0) {
            print_failure($this->lang['error_missing_home'] ?? 'Select a server first.');
            $this->handleIndex($userId, $isAdmin);
            return;
        }

        $home = $this->getHome($homeId, $userId, $isAdmin);
        if ($home === null) {
            print_failure($this->lang['error_home_not_found'] ?? 'Server not found.');
            $this->handleIndex($userId, $isAdmin);
            return;
        }

        $agentId = (int)($home['remote_server_id'] ?? 0);

        // Load server-level settings
        $serverSettings = $this->repo->getServerSettings($homeId);

        // Determine active profile: from server settings, or fall back to app-id lookup
        $profile = null;
        if ($serverSettings !== null && !empty($serverSettings['profile_id'])) {
            $profile = $this->repo->getProfileById((int)$serverSettings['profile_id']);
        }
        if ($profile === null) {
            $appId   = $this->searchService->getSteamAppIdForGameKey((string)($home['game_key'] ?? ''));
            $profile = $appId !== null ? $this->repo->getProfileByAppId($appId) : null;
        }
        $appId = $profile !== null ? (string)($profile['workshop_app_id'] ?? '') : null;

        // All enabled profiles for the profile selector
        $allProfiles = $this->repo->listProfiles(true);

        $installedMods = $this->repo->listModsForHome($homeId);
        $availableMods = ($profile !== null && $appId !== null)
            ? $this->repo->listCacheForAgent($agentId, $appId)
            : [];

        $this->render('user_workshop_mods', [
            'lang'           => $this->lang,
            'home'           => $home,
            'homeId'         => $homeId,
            'profile'        => $profile,
            'appId'          => $appId,
            'installedMods'  => $installedMods,
            'availableMods'  => $availableMods,
            'serverSettings' => $serverSettings ?? [],
            'allProfiles'    => $allProfiles,
            'isAdmin'        => $isAdmin,
        ]);
    }

    // ------------------------------------------------------------------
    // AJAX / POST actions
    // ------------------------------------------------------------------

    private function handleInstall(int $userId, bool $isAdmin): void
    {
        $homeId     = (int)($_POST['home_id'] ?? 0);
        $workshopId = preg_replace('/[^0-9]/', '', (string)($_POST['workshop_id'] ?? '')) ?? '';

        if ($homeId <= 0 || $workshopId === '') {
            print_failure($this->lang['error_missing_params'] ?? 'Missing home or workshop ID.');
            $this->handleIndex($userId, $isAdmin);
            return;
        }

        $home = $this->getHome($homeId, $userId, $isAdmin);
        if ($home === null) {
            print_failure($this->lang['error_home_not_found'] ?? 'Server not found or access denied.');
            $this->handleIndex($userId, $isAdmin);
            return;
        }

        $appId   = $this->searchService->getSteamAppIdForGameKey((string)($home['game_key'] ?? ''));
        $profile = $appId !== null ? $this->repo->getProfileByAppId($appId) : null;

        if ($profile === null) {
            print_failure($this->lang['error_no_profile'] ?? 'No Workshop profile configured for this game.');
            $this->handleModsPage($userId, $isAdmin);
            return;
        }

        $result = $this->installer->install($home, $profile, $workshopId);

        if ($result['success']) {
            $msg = $this->lang['mod_installed'] ?? 'Mod installed successfully.';
            if (!empty($result['restart_required'])) {
                $msg .= ' ' . ($this->lang['restart_required'] ?? 'A server restart is required to activate this mod.');
            }
            print_success($msg);
        } else {
            print_failure(($this->lang['mod_install_error'] ?? 'Install failed: ') . $result['message']);
        }

        $_GET['home_id'] = $homeId;
        $this->handleModsPage($userId, $isAdmin);
    }

    private function handleRemove(int $userId, bool $isAdmin): void
    {
        $homeId     = (int)($_POST['home_id'] ?? 0);
        $workshopId = preg_replace('/[^0-9]/', '', (string)($_POST['workshop_id'] ?? '')) ?? '';

        if ($homeId <= 0 || $workshopId === '') {
            print_failure($this->lang['error_missing_params'] ?? 'Missing parameters.');
            $this->handleIndex($userId, $isAdmin);
            return;
        }

        $home = $this->getHome($homeId, $userId, $isAdmin);
        if ($home === null) {
            print_failure($this->lang['error_home_not_found'] ?? 'Server not found.');
            $this->handleIndex($userId, $isAdmin);
            return;
        }

        if ($this->repo->removeMod($homeId, $workshopId)) {
            print_success($this->lang['mod_removed'] ?? 'Mod removed.');
        } else {
            print_failure($this->lang['mod_remove_error'] ?? 'Failed to remove mod.');
        }

        $_GET['home_id'] = $homeId;
        $this->handleModsPage($userId, $isAdmin);
    }

    private function handleToggle(int $userId, bool $isAdmin): void
    {
        $homeId     = (int)($_POST['home_id'] ?? 0);
        $workshopId = preg_replace('/[^0-9]/', '', (string)($_POST['workshop_id'] ?? '')) ?? '';
        $enabled    = !empty($_POST['enabled']);

        if ($homeId <= 0 || $workshopId === '') {
            print_failure($this->lang['error_missing_params'] ?? 'Missing parameters.');
            $this->handleIndex($userId, $isAdmin);
            return;
        }

        $home = $this->getHome($homeId, $userId, $isAdmin);
        if ($home === null) {
            print_failure($this->lang['error_home_not_found'] ?? 'Server not found.');
            $this->handleIndex($userId, $isAdmin);
            return;
        }

        $ok = $this->repo->toggleMod($homeId, $workshopId, $enabled);
        if (!$ok) {
            print_failure($this->lang['error_toggle_failed'] ?? 'Failed to update mod status.');
        }

        $_GET['home_id'] = $homeId;
        $this->handleModsPage($userId, $isAdmin);
    }

    private function handleLoadOrder(int $userId, bool $isAdmin): void
    {
        $homeId     = (int)($_POST['home_id'] ?? 0);
        $workshopId = preg_replace('/[^0-9]/', '', (string)($_POST['workshop_id'] ?? '')) ?? '';
        $order      = (int)($_POST['load_order'] ?? 0);

        if ($homeId <= 0 || $workshopId === '') {
            print_failure($this->lang['error_missing_params'] ?? 'Missing parameters.');
            $this->handleIndex($userId, $isAdmin);
            return;
        }

        $home = $this->getHome($homeId, $userId, $isAdmin);
        if ($home === null) {
            print_failure($this->lang['error_home_not_found'] ?? 'Server not found.');
            $this->handleIndex($userId, $isAdmin);
            return;
        }

        $ok = $this->repo->updateLoadOrder($homeId, $workshopId, $order);
        if (!$ok) {
            print_failure($this->lang['error_order_failed'] ?? 'Failed to update load order.');
        }

        $_GET['home_id'] = $homeId;
        $this->handleModsPage($userId, $isAdmin);
    }

    private function handleSync(int $userId, bool $isAdmin): void
    {
        $homeId     = (int)($_POST['home_id'] ?? 0);
        $workshopId = preg_replace('/[^0-9]/', '', (string)($_POST['workshop_id'] ?? '')) ?? '';

        if ($homeId <= 0 || $workshopId === '') {
            print_failure($this->lang['error_missing_params'] ?? 'Missing parameters.');
            $this->handleIndex($userId, $isAdmin);
            return;
        }

        $home = $this->getHome($homeId, $userId, $isAdmin);
        if ($home === null) {
            print_failure($this->lang['error_home_not_found'] ?? 'Server not found.');
            $this->handleIndex($userId, $isAdmin);
            return;
        }

        $modRow  = $this->repo->getServerMod($homeId, $workshopId);
        $profile = $modRow !== null ? $this->repo->getProfileById((int)$modRow['profile_id']) : null;

        if ($modRow === null || $profile === null) {
            print_failure($this->lang['error_mod_not_found'] ?? 'Mod or profile not found.');
        } else {
            $result = $this->installer->syncMod($home, $modRow, $profile);
            if ($result['success']) {
                print_success($result['changed']
                    ? ($this->lang['sync_success'] ?? 'Mod synced successfully.')
                    : ($this->lang['sync_no_change'] ?? 'Mod is already up to date.'));
            } else {
                print_failure(($this->lang['sync_error'] ?? 'Sync failed: ') . $result['message']);
            }
        }

        $_GET['home_id'] = $homeId;
        $this->handleModsPage($userId, $isAdmin);
    }

    private function handleSearch(int $userId, bool $isAdmin): void
    {
        header('Content-Type: application/json');
        $homeId = (int)($_GET['home_id'] ?? 0);
        $query  = trim((string)($_GET['q'] ?? ''));

        if ($homeId <= 0 || $query === '') {
            echo json_encode(['ok' => false, 'error' => 'Missing parameters.']);
            return;
        }

        $home = $this->getHome($homeId, $userId, $isAdmin);
        if ($home === null) {
            echo json_encode(['ok' => false, 'error' => 'Server not found.']);
            return;
        }

        $gameKey = (string)($home['game_key'] ?? '');
        $payload = $this->searchService->searchWorkshopItems($gameKey, $query, 12, 1);

        if ($payload['error'] !== null) {
            echo json_encode(['ok' => false, 'error' => $payload['error']]);
            return;
        }

        echo json_encode(['ok' => true, 'results' => $payload['results'], 'pagination' => $payload['pagination']]);
    }

    private function handleSaveSettings(int $userId, bool $isAdmin): void
    {
        $homeId = (int)($_POST['home_id'] ?? 0);
        if ($homeId <= 0) {
            print_failure($this->lang['error_missing_home'] ?? 'Select a server first.');
            $this->handleIndex($userId, $isAdmin);
            return;
        }

        $home = $this->getHome($homeId, $userId, $isAdmin);
        if ($home === null) {
            print_failure($this->lang['error_home_not_found'] ?? 'Server not found.');
            $this->handleIndex($userId, $isAdmin);
            return;
        }

        $this->repo->saveServerSettings($homeId, [
            'workshop_enabled' => !empty($_POST['workshop_enabled']) ? 1 : 0,
            'profile_id'       => (int)($_POST['profile_id'] ?? 0),
            'update_mode'      => $_POST['update_mode'] ?? 'manual',
            'restart_behavior' => $_POST['restart_behavior'] ?? 'none',
        ]);

        print_success($this->lang['settings_saved'] ?? 'Workshop settings saved.');
        $_GET['home_id'] = $homeId;
        $this->handleModsPage($userId, $isAdmin);
    }

    private function handleQueueUpdate(int $userId, bool $isAdmin): void
    {
        $homeId = (int)($_POST['home_id'] ?? 0);
        if ($homeId <= 0) {
            print_failure($this->lang['error_missing_home'] ?? 'Select a server first.');
            $this->handleIndex($userId, $isAdmin);
            return;
        }

        $home = $this->getHome($homeId, $userId, $isAdmin);
        if ($home === null) {
            print_failure($this->lang['error_home_not_found'] ?? 'Server not found.');
            $this->handleIndex($userId, $isAdmin);
            return;
        }

        $this->repo->setUpdateQueued($homeId, true);
        print_success($this->lang['update_queued'] ?? 'Manual update queued. It will run on the next scheduler cycle.');
        $_GET['home_id'] = $homeId;
        $this->handleModsPage($userId, $isAdmin);
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /** @return array<int,array<string,mixed>> */
    private function getHomesForUser(int $userId, bool $isAdmin): array
    {
        global $db;
        $accessType = $isAdmin ? 'admin' : 'user_and_group';
        $homes      = $db->getHomesFor($accessType, $userId);
        return is_array($homes) ? array_values($homes) : [];
    }

    private function getHome(int $homeId, int $userId, bool $isAdmin): ?array
    {
        global $db;
        $row = $isAdmin ? $db->getGameHome($homeId) : $db->getUserGameHome($userId, $homeId);
        return is_array($row) ? $row : null;
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../views/' . $view . '.php';
    }

    private function loadLang(): array
    {
        $file = __DIR__ . '/../lang/en_US.php';
        if (is_file($file)) {
            $strings = require $file;
            if (is_array($strings)) {
                return $strings;
            }
        }
        return [];
    }
}
