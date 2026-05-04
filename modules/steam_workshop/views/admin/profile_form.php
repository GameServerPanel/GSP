<?php
declare(strict_types=1);
/** @var array      $lang */
/** @var array|null $profile   existing row when editing, null when creating */
/** @var int        $profileId */

$isEdit      = $profileId > 0 && $profile !== null;
$heading     = $isEdit
    ? sprintf($lang['config_heading_edit'] ?? 'Edit Workshop Configuration: %s', htmlspecialchars($profile['game_name'] ?? ''))
    : ($lang['config_heading_create'] ?? 'Add Workshop Game Configuration');

$v = static function (string $key, array $profile, string $default = ''): string {
    return htmlspecialchars((string)($profile[$key] ?? $default), ENT_QUOTES);
};

$osList      = ['linux' => 'Linux', 'windows' => 'Windows'];
$currentOs   = array_filter(explode(',', (string)($profile['supported_os'] ?? 'linux')));
$methodList  = ['rsync' => 'rsync (Linux)', 'robocopy' => 'robocopy (Windows)', 'custom_script' => 'Custom script'];
$curMethod   = (string)($profile['copy_method'] ?? 'rsync');

$tplVarNote  = $lang['profile_template_vars'] ?? 'Available: {home_id} {agent_id} {workshop_app_id} {mod_id} {mod_title} {mod_folder} {steamcmd_path} {server_path} {install_path} {cache_path}';
?>
<div class="sw-admin sw-profile-form">
    <h3><?php echo $heading; ?></h3>
    <p><a href="?m=steam_workshop&p=workshop_admin">&larr; <?php echo htmlspecialchars($lang['config_back_list'] ?? 'Back to configurations'); ?></a></p>

    <div class="sw-info-box">
        <strong><?php echo htmlspecialchars($lang['config_steamcmd_heading'] ?? 'How mods are downloaded'); ?></strong>
        <p><?php echo htmlspecialchars($lang['config_steamcmd_note'] ?? 'Workshop mods are downloaded using SteamCMD: +workshop_download_item <App ID> <Mod ID>. The cache path below is where SteamCMD stores downloaded content on the agent. The install path is where the mod files are copied into the game server directory.'); ?></p>
    </div>

    <form method="post" action="?m=steam_workshop&p=workshop_admin" class="sw-form">
        <input type="hidden" name="sw_action"  value="profile_save">
        <input type="hidden" name="profile_id" value="<?php echo $profileId; ?>">

        <!-- Basic info -->
        <fieldset>
            <legend><?php echo htmlspecialchars($lang['profile_section_basic'] ?? 'Basic info'); ?></legend>
            <div class="sw-form__grid">
                <label>
                    <?php echo htmlspecialchars($lang['label_game_key'] ?? 'Game key'); ?> <em>*</em>
                    <small><?php echo htmlspecialchars($lang['config_hint_game_key'] ?? 'Short identifier matching the game XML key, e.g. arma3_linux'); ?></small>
                    <input type="text" name="game_key" value="<?php echo $v('game_key', $profile ?? []); ?>"
                           pattern="[A-Za-z0-9_\-.]+" required maxlength="100"
                           <?php echo $isEdit ? 'readonly' : ''; ?>>
                </label>
                <label>
                    <?php echo htmlspecialchars($lang['profile_label_game_name'] ?? 'Game name'); ?> <em>*</em>
                    <input type="text" name="game_name" value="<?php echo $v('game_name', $profile ?? []); ?>"
                           required maxlength="255">
                </label>
                <label>
                    <?php echo htmlspecialchars($lang['config_label_app_id'] ?? 'Steam App ID'); ?> <em>*</em>
                    <small><?php echo htmlspecialchars($lang['config_hint_app_id'] ?? 'The Steam App ID used with +workshop_download_item, e.g. 107410 for Arma 3'); ?></small>
                    <input type="text" name="workshop_app_id"
                           value="<?php echo $v('workshop_app_id', $profile ?? []); ?>"
                           pattern="[0-9]+" required maxlength="32">
                </label>
            </div>

            <fieldset class="sw-form__os-group">
                <legend><?php echo htmlspecialchars($lang['profile_label_os'] ?? 'Supported OS'); ?></legend>
                <?php foreach ($osList as $osVal => $osLabel): ?>
                    <label class="sw-checkbox">
                        <input type="checkbox" name="supported_os[]" value="<?php echo $osVal; ?>"
                               <?php echo in_array($osVal, $currentOs, true) ? 'checked' : ''; ?>>
                        <span><?php echo htmlspecialchars($osLabel); ?></span>
                    </label>
                <?php endforeach; ?>
            </fieldset>
        </fieldset>

        <!-- Paths / templates -->
        <fieldset>
            <legend><?php echo htmlspecialchars($lang['profile_section_paths'] ?? 'Paths &amp; templates'); ?></legend>
            <small class="sw-hint"><?php echo htmlspecialchars($tplVarNote); ?></small>

            <label>
                <?php echo htmlspecialchars($lang['profile_label_cache_path'] ?? 'SteamCMD cache path template'); ?> <em>*</em>
                <small><?php echo htmlspecialchars($lang['profile_hint_cache_path'] ?? 'Where SteamCMD downloads mods on the agent. E.g. {steamcmd_path}/steamapps/workshop/content/{workshop_app_id}/{mod_id}'); ?></small>
                <input type="text" name="cache_path_template"
                       value="<?php echo $v('cache_path_template', $profile ?? []); ?>" required>
            </label>

            <label>
                <?php echo htmlspecialchars($lang['profile_label_install_path'] ?? 'Server install path template'); ?> <em>*</em>
                <small><?php echo htmlspecialchars($lang['profile_hint_install_path'] ?? 'Where mod files are placed inside the game server directory. E.g. {server_path}/mods/{mod_folder}'); ?></small>
                <input type="text" name="install_path_template"
                       value="<?php echo $v('install_path_template', $profile ?? []); ?>" required>
            </label>

            <label>
                <?php echo htmlspecialchars($lang['profile_label_folder_name'] ?? 'Mod folder name template'); ?>
                <small><?php echo htmlspecialchars($lang['profile_hint_folder_name'] ?? 'Folder name for each mod inside the install path. Default: @{mod_id}'); ?></small>
                <input type="text" name="folder_name_template"
                       value="<?php echo $v('folder_name_template', $profile ?? [], '@{mod_id}'); ?>">
            </label>
        </fieldset>

        <!-- Copy method -->
        <fieldset>
            <legend><?php echo htmlspecialchars($lang['config_section_copy'] ?? 'Copy / sync method'); ?></legend>
            <label>
                <?php echo htmlspecialchars($lang['profile_label_copy_method'] ?? 'Method used to copy mod files from SteamCMD cache to the server'); ?>
                <select name="copy_method">
                    <?php foreach ($methodList as $mVal => $mLabel): ?>
                        <option value="<?php echo $mVal; ?>" <?php echo $curMethod === $mVal ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($mLabel); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                <?php echo htmlspecialchars($lang['profile_label_install_script'] ?? 'Custom install script (optional, admin-defined)'); ?>
                <small><?php echo htmlspecialchars($lang['profile_hint_install_script'] ?? 'Only used when copy method is custom_script. Template variables are replaced before execution.'); ?></small>
                <textarea name="install_script" rows="4"><?php echo $v('install_script', $profile ?? []); ?></textarea>
            </label>
        </fieldset>

        <!-- Config / launch params -->
        <fieldset>
            <legend><?php echo htmlspecialchars($lang['profile_section_config'] ?? 'Config &amp; launch parameters'); ?></legend>
            <label>
                <?php echo htmlspecialchars($lang['profile_label_config_tpl'] ?? 'Config file template (optional)'); ?>
                <textarea name="config_file_template" rows="4"><?php echo $v('config_file_template', $profile ?? []); ?></textarea>
            </label>
            <label>
                <?php echo htmlspecialchars($lang['profile_label_launch_tpl'] ?? 'Launch parameter template (optional)'); ?>
                <small><?php echo htmlspecialchars($lang['config_hint_launch_tpl'] ?? 'Extra launch parameters added when this game has Workshop mods enabled. E.g. -mod=@{mod_id}'); ?></small>
                <input type="text" name="launch_param_template"
                       value="<?php echo $v('launch_param_template', $profile ?? []); ?>">
            </label>
        </fieldset>

        <!-- Flags -->
        <fieldset>
            <legend><?php echo htmlspecialchars($lang['profile_section_flags'] ?? 'Flags'); ?></legend>
            <label class="sw-checkbox">
                <input type="checkbox" name="requires_restart" value="1"
                       <?php echo !empty($profile['requires_restart']) ? 'checked' : ''; ?>>
                <span><?php echo htmlspecialchars($lang['profile_label_requires_restart'] ?? 'Server restart required after mod install or update'); ?></span>
            </label>
            <label class="sw-checkbox">
                <input type="checkbox" name="enabled" value="1"
                       <?php echo ($profile['enabled'] ?? 1) ? 'checked' : ''; ?>>
                <span><?php echo htmlspecialchars($lang['config_label_enabled'] ?? 'Configuration enabled (allows servers to use Workshop mods for this game)'); ?></span>
            </label>
        </fieldset>

        <div class="sw-form__actions">
            <button class="btn primary" type="submit">
                <?php echo htmlspecialchars($lang['button_save'] ?? 'Save'); ?>
            </button>
            <a class="btn" href="?m=steam_workshop&p=workshop_admin">
                <?php echo htmlspecialchars($lang['button_cancel'] ?? 'Cancel'); ?>
            </a>
        </div>
    </form>
</div>
