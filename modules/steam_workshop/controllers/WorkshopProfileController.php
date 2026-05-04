<?php
declare(strict_types=1);
/*
 * OGP / GSP – Steam Workshop
 * WorkshopProfileController: admin CRUD for Workshop game profiles
 * (gsp_workshop_game_profiles table).
 *
 * Routed via workshop_admin.php:
 *   ?m=steam_workshop&p=workshop_admin&sw_action=profiles        → list
 *   ?m=steam_workshop&p=workshop_admin&sw_action=profile_form    → create/edit
 *   POST sw_action=profile_save                                  → save
 *   POST sw_action=profile_delete                                → delete
 */

require_once __DIR__ . '/../lib/WorkshopRepository.php';

class WorkshopProfileController
{
    private WorkshopRepository $repo;
    private array $lang;

    public function __construct(OGPDatabase $db)
    {
        $this->repo = new WorkshopRepository($db);
        $this->lang = $this->loadLang();
    }

    // ------------------------------------------------------------------
    // Dispatch
    // ------------------------------------------------------------------

    public function handle(): void
    {
        global $db;

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if (!$db->isAdmin($userId)) {
            print_failure($this->lang['error_admin_only'] ?? 'Administrator access required.');
            return;
        }

        echo '<link rel="stylesheet" type="text/css" href="modules/steam_workshop/steam_workshop.css" />';

        $action = $_GET['sw_action'] ?? 'list';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postAction = $_POST['sw_action'] ?? '';
            switch ($postAction) {
                case 'profile_save':
                    $this->handleSave();
                    return;
                case 'profile_delete':
                    $this->handleDelete();
                    return;
            }
        }

        switch ($action) {
            case 'config_form':
            case 'profile_form':
                $this->handleForm((int)($_GET['profile_id'] ?? 0));
                break;
            default:
                $this->handleList();
                break;
        }
    }

    // ------------------------------------------------------------------
    // Actions
    // ------------------------------------------------------------------

    private function handleList(): void
    {
        $profiles = $this->repo->listProfiles();
        $this->render('admin/profiles', [
            'lang'     => $this->lang,
            'profiles' => $profiles,
        ]);
    }

    private function handleForm(int $profileId): void
    {
        $profile = $profileId > 0 ? $this->repo->getProfileById($profileId) : null;
        $this->render('admin/profile_form', [
            'lang'      => $this->lang,
            'profile'   => $profile,
            'profileId' => $profileId,
        ]);
    }

    private function handleSave(): void
    {
        $id   = (int)($_POST['profile_id'] ?? 0);
        $data = $this->extractProfileData($_POST);

        $errors = $this->validateProfileData($data);
        if (!empty($errors)) {
            foreach ($errors as $err) {
                print_failure($err);
            }
            $profile = $id > 0 ? $this->repo->getProfileById($id) : null;
            $this->render('admin/profile_form', [
                'lang'      => $this->lang,
                'profile'   => array_merge($profile ?? [], $data, ['id' => $id]),
                'profileId' => $id,
            ]);
            return;
        }

        $data['id'] = $id;
        $savedId = $this->repo->saveProfile($data);

        if ($savedId > 0) {
            print_success($this->lang['profile_saved'] ?? 'Workshop profile saved.');
        } else {
            print_failure($this->lang['profile_save_error'] ?? 'Failed to save Workshop profile.');
        }

        $this->handleList();
    }

    private function handleDelete(): void
    {
        $id = (int)($_POST['profile_id'] ?? 0);
        if ($id <= 0) {
            print_failure($this->lang['profile_not_found'] ?? 'Profile not found.');
            $this->handleList();
            return;
        }

        if ($this->repo->deleteProfile($id)) {
            print_success($this->lang['profile_deleted'] ?? 'Workshop profile deleted.');
        } else {
            print_failure($this->lang['profile_delete_error'] ?? 'Failed to delete Workshop profile.');
        }

        $this->handleList();
    }

    // ------------------------------------------------------------------
    // Input helpers
    // ------------------------------------------------------------------

    /**
     * @param array<string,mixed> $post
     * @return array<string,mixed>
     */
    private function extractProfileData(array $post): array
    {
        // supported_os can be multiple values (SET type)
        $osRaw = $post['supported_os'] ?? [];
        if (!is_array($osRaw)) {
            $osRaw = [$osRaw];
        }
        $allowedOs = ['linux', 'windows'];
        $osValues  = array_values(array_intersect($osRaw, $allowedOs));
        $supportedOs = implode(',', $osValues !== [] ? $osValues : ['linux']);

        $allowedCopyMethods = ['copy', 'rsync', 'symlink'];
        $copyMethod = in_array($post['copy_method'] ?? '', $allowedCopyMethods, true)
            ? (string)$post['copy_method']
            : 'rsync';

        $allowedLoginModes = ['anonymous', 'account'];
        $steamcmdLoginMode = in_array($post['steamcmd_login_mode'] ?? '', $allowedLoginModes, true)
            ? (string)$post['steamcmd_login_mode']
            : 'anonymous';

        $allowedFolderFormats = ['@%mod_name%', '@%workshop_id%', 'custom'];
        $folderNamingFormat = in_array($post['folder_naming_format'] ?? '', $allowedFolderFormats, true)
            ? (string)$post['folder_naming_format']
            : '@%workshop_id%';

        $allowedSeparators = ['semicolon', 'comma', 'space'];
        $modSeparator = in_array($post['mod_separator'] ?? '', $allowedSeparators, true)
            ? (string)$post['mod_separator']
            : 'semicolon';

        // When folder naming is preset (@%mod_name% or @%workshop_id%), derive template from format.
        // When 'custom', use the admin-supplied value.
        $folderNameTemplate = $folderNamingFormat !== 'custom'
            ? $folderNamingFormat
            : trim((string)($post['folder_name_template'] ?? '@%workshop_id%'));

        return [
            'game_key'              => trim((string)($post['game_key'] ?? '')),
            'game_name'             => trim((string)($post['game_name'] ?? '')),
            'steam_app_id'          => preg_replace('/[^0-9]/', '', (string)($post['steam_app_id'] ?? '')) ?? '',
            'workshop_app_id'       => preg_replace('/[^0-9]/', '', (string)($post['workshop_app_id'] ?? '')) ?? '',
            'steam_login_required'  => !empty($post['steam_login_required']) ? 1 : 0,
            'steamcmd_login_mode'   => $steamcmdLoginMode,
            'steamcmd_path'         => trim((string)($post['steamcmd_path'] ?? '')),
            'supported_os'          => $supportedOs,
            'cache_path_template'   => trim((string)($post['cache_path_template'] ?? '')),
            'install_path_template' => trim((string)($post['install_path_template'] ?? '')),
            'folder_naming_format'  => $folderNamingFormat,
            'folder_name_template'  => $folderNameTemplate,
            'mod_launch_param'      => trim((string)($post['mod_launch_param'] ?? '')),
            'mod_separator'         => $modSeparator,
            'copy_method'           => $copyMethod,
            'copy_keys'             => !empty($post['copy_keys']) ? 1 : 0,
            'key_source_path'       => trim((string)($post['key_source_path'] ?? '')),
            'key_dest_path'         => trim((string)($post['key_dest_path'] ?? '')),
            'pre_update_script'     => trim((string)($post['pre_update_script'] ?? '')),
            'install_script'        => trim((string)($post['install_script'] ?? '')),
            'post_update_script'    => trim((string)($post['post_update_script'] ?? '')),
            'config_file_template'  => trim((string)($post['config_file_template'] ?? '')),
            'launch_param_template' => trim((string)($post['launch_param_template'] ?? '')),
            'requires_restart'      => !empty($post['requires_restart']) ? 1 : 0,
            'validation_notes'      => trim((string)($post['validation_notes'] ?? '')),
            'enabled'               => !empty($post['enabled']) ? 1 : 0,
        ];
    }

    /**
     * @param array<string,mixed> $data
     * @return list<string>
     */
    private function validateProfileData(array $data): array
    {
        $errors = [];
        if (($data['game_key'] ?? '') === '') {
            $errors[] = $this->lang['error_game_key_required'] ?? 'Game key is required.';
        } elseif (!preg_match('/^[a-z0-9_\-.]+$/i', (string)$data['game_key'])) {
            $errors[] = $this->lang['error_game_key_invalid'] ?? 'Game key may only contain letters, digits, underscores, dots, and hyphens.';
        }
        if (($data['game_name'] ?? '') === '') {
            $errors[] = $this->lang['error_game_name_required'] ?? 'Game name is required.';
        }
        if (($data['workshop_app_id'] ?? '') === '') {
            $errors[] = $this->lang['error_app_id_required'] ?? 'Workshop App ID is required.';
        }
        if (($data['cache_path_template'] ?? '') === '') {
            $errors[] = $this->lang['error_cache_path_required'] ?? 'SteamCMD cache path template is required.';
        }
        if (($data['install_path_template'] ?? '') === '') {
            $errors[] = $this->lang['error_install_path_required'] ?? 'Server install path template is required.';
        }
        if (($data['folder_naming_format'] ?? '') === 'custom' && ($data['folder_name_template'] ?? '') === '') {
            $errors[] = $this->lang['error_folder_template_required'] ?? 'Custom folder name template is required when format is set to custom.';
        }
        return $errors;
    }

    // ------------------------------------------------------------------
    // Rendering
    // ------------------------------------------------------------------

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
