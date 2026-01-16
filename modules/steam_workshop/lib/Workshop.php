<?php
/*
 * Game-agnostic Steam Workshop support for GSP
 * This library centralizes config/state handling so UI pages can stay thin.
 */

if (!defined('IN_OGP')) {
    exit('Direct access not permitted');
}

class WorkshopConfigStore
{
    private $file;

    public function __construct($file)
    {
        $this->file = $file;
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
    }

    public function all()
    {
        $data = $this->readFile();
        return is_array($data) ? $data : [];
    }

    public function get($homeId)
    {
        $all = $this->all();
        return isset($all[$homeId]) ? $all[$homeId] : null;
    }

    public function put($homeId, array $config)
    {
        $all = $this->all();
        $all[$homeId] = $config;
        $this->writeFile($all);
    }

    public function delete($homeId)
    {
        $all = $this->all();
        if (isset($all[$homeId])) {
            unset($all[$homeId]);
            $this->writeFile($all);
        }
    }

    private function readFile()
    {
        if (!is_file($this->file)) {
            return [];
        }
        $fh = fopen($this->file, 'c+');
        if ($fh === false) {
            return [];
        }
        flock($fh, LOCK_SH);
        $raw = stream_get_contents($fh);
        flock($fh, LOCK_UN);
        fclose($fh);
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    private function writeFile(array $data)
    {
        $fh = fopen($this->file, 'c+');
        if ($fh === false) {
            return;
        }
        flock($fh, LOCK_EX);
        ftruncate($fh, 0);
        rewind($fh);
        fwrite($fh, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        fflush($fh);
        flock($fh, LOCK_UN);
        fclose($fh);
    }
}

class WorkshopStateStore
{
    private $dir;

    public function __construct($dir)
    {
        $this->dir = rtrim($dir, '/');
        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0775, true);
        }
    }

    public function get($homeId)
    {
        $path = $this->statePath($homeId);
        if (!is_file($path)) {
            return ['items' => [], 'last_sync' => null, 'last_status' => null];
        }
        $raw = file_get_contents($path);
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return ['items' => [], 'last_sync' => null, 'last_status' => null];
        }
        if (!isset($data['items'])) {
            $data['items'] = [];
        }
        return $data;
    }

    public function put($homeId, array $state)
    {
        $path = $this->statePath($homeId);
        $payload = json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($path, $payload);
    }

    public function updateItem($homeId, $itemId, array $itemState)
    {
        $state = $this->get($homeId);
        $state['items'][$itemId] = $itemState;
        $this->put($homeId, $state);
    }

    private function statePath($homeId)
    {
        return $this->dir . '/state_' . $homeId . '.json';
    }
}

class WorkshopLock
{
    private $fh;

    public function __construct($lockPath)
    {
        $dir = dirname($lockPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $this->fh = fopen($lockPath, 'c');
    }

    public function acquire()
    {
        if (!$this->fh) {
            return false;
        }
        return flock($this->fh, LOCK_EX | LOCK_NB);
    }

    public function release()
    {
        if ($this->fh) {
            flock($this->fh, LOCK_UN);
        }
    }
}

class WorkshopResolver
{
    public function resolveItems(array $config)
    {
        $items = array_map('trim', isset($config['workshop_item_ids']) ? (array)$config['workshop_item_ids'] : []);
        $collections = array_map('trim', isset($config['collection_ids']) ? (array)$config['collection_ids'] : []);

        if (!empty($collections)) {
            foreach ($collections as $collectionId) {
                $expanded = $this->expandCollection($collectionId);
                $items = array_merge($items, $expanded);
            }
        }

        $items = array_values(array_unique(array_filter($items, 'strlen')));
        return $items;
    }

    private function expandCollection($collectionId)
    {
        $payload = http_build_query([
            'collectioncount' => 1,
            'publishedfileids[0]' => $collectionId,
        ]);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded",
                'content' => $payload,
                'timeout' => 10,
            ],
        ]);

        $json = @file_get_contents('https://api.steampowered.com/ISteamRemoteStorage/GetCollectionDetails/v1/', false, $context);
        if ($json === false) {
            return [];
        }
        $data = json_decode($json, true);
        if (!isset($data['response']['collectiondetails'][0]['children'])) {
            return [];
        }
        $children = $data['response']['collectiondetails'][0]['children'];
        $ids = [];
        foreach ($children as $child) {
            if (isset($child['publishedfileid'])) {
                $ids[] = $child['publishedfileid'];
            }
        }
        return $ids;
    }

    public function fetchItemDetails(array $itemIds)
    {
        if (empty($itemIds)) {
            return [];
        }
        $payload = ['itemcount' => count($itemIds)];
        $index = 0;
        foreach ($itemIds as $id) {
            $payload['publishedfileids[' . $index . ']'] = $id;
            $index++;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded",
                'content' => http_build_query($payload),
                'timeout' => 10,
            ],
        ]);

        $json = @file_get_contents('https://api.steampowered.com/ISteamRemoteStorage/GetPublishedFileDetails/v1/', false, $context);
        if ($json === false) {
            return [];
        }
        $data = json_decode($json, true);
        if (!isset($data['response']['publishedfiledetails'])) {
            return [];
        }
        $details = [];
        foreach ($data['response']['publishedfiledetails'] as $item) {
            $details[$item['publishedfileid']] = $item;
        }
        return $details;
    }
}

class WorkshopSyncResult
{
    public $success;
    public $message;
    public $updatedItems;
    public $skippedItems;

    public function __construct($success, $message, array $updatedItems = [], array $skippedItems = [])
    {
        $this->success = $success;
        $this->message = $message;
        $this->updatedItems = $updatedItems;
        $this->skippedItems = $skippedItems;
    }
}

class WorkshopSyncService
{
    private $remote;
    private $homeCfg;
    private $configStore;
    private $stateStore;
    private $resolver;

    public function __construct($remote, array $homeCfg, WorkshopConfigStore $configStore, WorkshopStateStore $stateStore, WorkshopResolver $resolver)
    {
        $this->remote = $remote;
        $this->homeCfg = $homeCfg;
        $this->configStore = $configStore;
        $this->stateStore = $stateStore;
        $this->resolver = $resolver;
    }

    public function sync($homeId)
    {
        $config = $this->configStore->get($homeId);
        if (!$config) {
            return new WorkshopSyncResult(false, 'No workshop configuration found for this server.');
        }

        $itemIds = $this->resolver->resolveItems($config);
        if (empty($itemIds)) {
            return new WorkshopSyncResult(true, 'No workshop items configured.', [], []);
        }

        $details = $this->resolver->fetchItemDetails($itemIds);
        $state = $this->stateStore->get($homeId);
        $needsUpdate = [];
        $skipped = [];

        foreach ($itemIds as $id) {
            $remoteUpdated = isset($details[$id]['time_updated']) ? (int)$details[$id]['time_updated'] : null;
            $local = isset($state['items'][$id]) ? $state['items'][$id] : null;
            $localSeen = $local && isset($local['last_seen_manifest_id']) ? (int)$local['last_seen_manifest_id'] : null;
            if ($remoteUpdated !== null && $localSeen !== null && $remoteUpdated <= $localSeen) {
                $skipped[] = $id;
                continue;
            }
            $needsUpdate[] = $id;
        }

        if (empty($needsUpdate)) {
            $state['last_sync'] = time();
            $state['last_status'] = 'up-to-date';
            $this->stateStore->put($homeId, $state);
            return new WorkshopSyncResult(true, 'All workshop items are up-to-date.', [], $skipped);
        }

        $modsFullPath = $this->modsPath($config);
        $paths = $this->buildPaths($config);
        $modNamesList = implode(',', $needsUpdate);

        $result = $this->remote->steam_workshop(
            $homeId,
            $modsFullPath,
            $config['workshop_app_id'],
            implode(',', $needsUpdate),
            $config['regex'],
            (int)$config['mods_backreference_index'],
            $config['variable'],
            $config['place_after'],
            $config['mod_string'],
            $config['string_separator'],
            $paths['config_file_path'],
            $config['post_install'],
            $modNamesList,
            $config['anonymous_login'] ? '1' : '0',
            $config['steam_username'],
            $config['steam_password'],
            $config['download_method'],
            '',
            ''
        );

        if ($result !== 1) {
            return new WorkshopSyncResult(false, 'Workshop sync failed to start (agent error code ' . $result . ').');
        }

        $now = time();
        foreach ($needsUpdate as $id) {
            $state['items'][$id] = [
                'last_seen_manifest_id' => isset($details[$id]['time_updated']) ? (int)$details[$id]['time_updated'] : $now,
                'last_downloaded_at' => $now,
                'local_path' => $modsFullPath,
                'deploy_path' => $paths['deploy_destination'],
                'last_error' => null,
            ];
        }
        $state['last_sync'] = $now;
        $state['last_status'] = 'started';
        $this->stateStore->put($homeId, $state);

        return new WorkshopSyncResult(true, 'Workshop sync started.', $needsUpdate, $skipped);
    }

    private function modsPath(array $config)
    {
        if (!empty($config['staging_path'])) {
            return clean_path($config['staging_path']);
        }
        return clean_path($this->homeCfg['home_path'] . '/workshop');
    }

    private function buildPaths(array $config)
    {
        $serverRoot = rtrim($this->homeCfg['home_path'], '/');
        $deployDest = !empty($config['deploy_destination']) ? clean_path($config['deploy_destination']) : ($serverRoot . '/mods');
        $configPath = !empty($config['filepath']) ? clean_path($config['filepath']) : ($serverRoot . '/config.cfg');
        return [
            'deploy_destination' => $deployDest,
            'config_file_path' => $configPath,
        ];
    }
}

function workshop_build_default_config($homeCfg, $modXml, $settings)
{
    $modsPath = ($modXml && isset($modXml->mods_path)) ? (string)$modXml->mods_path : 'mods';
    $configNode = $modXml && isset($modXml->config) ? $modXml->config : null;

    return [
        'workshop_app_id' => ($modXml && isset($modXml->workshop_id)) ? (string)$modXml->workshop_id : '',
        'download_method' => ($modXml && isset($modXml->download_method)) ? (string)$modXml->download_method : 'steamcmd',
        'deploy_mode' => 'copy',
        'deploy_destination' => clean_path($homeCfg['home_path'] . '/' . $modsPath),
        'staging_path' => clean_path($homeCfg['home_path'] . '/' . $modsPath),
        'mods_path' => clean_path($homeCfg['home_path'] . '/' . $modsPath),
        'regex' => ($configNode && isset($configNode->regex)) ? (string)$configNode->regex : '',
        'mods_backreference_index' => ($configNode && isset($configNode->mods_backreference_index)) ? (int)$configNode->mods_backreference_index : 1,
        'variable' => ($configNode && isset($configNode->variable)) ? (string)$configNode->variable : '',
        'place_after' => ($configNode && isset($configNode->place_after)) ? (string)$configNode->place_after : '',
        'mod_string' => ($configNode && isset($configNode->mod_string)) ? (string)$configNode->mod_string : '%workshop_mod_id%',
        'string_separator' => ($configNode && isset($configNode->string_separator)) ? (string)$configNode->string_separator : ';',
        'filepath' => ($configNode && isset($configNode->filepath)) ? clean_path($homeCfg['home_path'] . '/' . $configNode->filepath) : '',
        'post_install' => ($modXml && isset($modXml->post_install)) ? (string)$modXml->post_install : '',
        'uninstall' => ($modXml && isset($modXml->uninstall)) ? (string)$modXml->uninstall : '',
        'anonymous_login' => ($modXml && isset($modXml->anonymous_login)) ? ((string)$modXml->anonymous_login === '1') : true,
        'steam_username' => isset($settings['steam_user']) ? $settings['steam_user'] : '',
        'steam_password' => isset($settings['steam_pass']) ? $settings['steam_pass'] : '',
        'workshop_item_ids' => [],
        'collection_ids' => [],
        'check_on_start' => true,
        'periodic_check_minutes' => null,
        'apply_updates' => 'on_start_only',
    ];
}
