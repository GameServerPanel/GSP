<?php
declare(strict_types=1);
/** @var array      $lang */
/** @var array|null $profile   existing row when editing, null when creating */
/** @var int        $profileId */

$isEdit  = $profileId > 0 && $profile !== null;
$heading = $isEdit
    ? sprintf($lang['config_heading_edit'] ?? 'Edit Workshop Configuration: %s', htmlspecialchars($profile['game_name'] ?? ''))
    : ($lang['config_heading_create'] ?? 'Add Workshop Game Configuration');

/** Helper: return html-safe value from profile array (or default). */
$v = static function (string $key, array $p, string $default = ''): string {
    return htmlspecialchars((string)($p[$key] ?? $default), ENT_QUOTES);
};

$osList          = ['linux' => 'Linux', 'windows' => 'Windows'];
$currentOs       = array_filter(explode(',', (string)($profile['supported_os'] ?? 'linux')));
$folderFormats   = ['@%mod_name%' => '@%mod_name% (mod title)', '@%workshop_id%' => '@%workshop_id% (numeric ID)', 'custom' => 'Custom template'];
$curFolderFormat = (string)($profile['folder_naming_format'] ?? '@%workshop_id%');
$separatorList   = ['semicolon' => 'Semicolon ( ; )', 'comma' => 'Comma ( , )', 'space' => 'Space ( )'];
$curSeparator    = (string)($profile['mod_separator'] ?? 'semicolon');
$copyMethods     = ['rsync' => 'rsync (Linux/Unix)', 'copy' => 'cp / basic copy', 'symlink' => 'Symlink (requires persistent cache path)'];
$curCopyMethod   = (string)($profile['copy_method'] ?? 'rsync');
$loginModes      = ['anonymous' => 'Anonymous (recommended for free mods)', 'account' => 'Configured account (paid games)'];
$curLoginMode    = (string)($profile['steamcmd_login_mode'] ?? 'anonymous');

$tplVarNote = $lang['profile_template_vars'] ?? 'Variables: %home_id% %server_path% %steam_app_id% %workshop_app_id% %workshop_id% %mod_name% %install_name% %download_path% %source_path% %target_path% %keys_source_path% %keys_target_path% %steamcmd_path%';
?>
<div class="sw-admin sw-profile-form">
    <h3><?php echo $heading; ?></h3>
    <p><a href="?m=steam_workshop&p=workshop_admin">&larr; <?php echo htmlspecialchars($lang['config_back_list'] ?? 'Back to configurations'); ?></a></p>

    <div class="sw-info-box">
        <strong><?php echo htmlspecialchars($lang['config_steamcmd_heading'] ?? 'How mods are downloaded'); ?></strong>
        <p><?php echo htmlspecialchars($lang['config_steamcmd_note'] ?? 'Workshop mods are downloaded using SteamCMD: +workshop_download_item <App ID> <Mod ID>. Configure the paths and scripts below to control how mods are installed for servers of this game type.'); ?></p>
        <p><strong><?php echo htmlspecialchars($lang['profile_template_vars_heading'] ?? 'Template variables:'); ?></strong><br>
        <code><?php echo htmlspecialchars($tplVarNote); ?></code></p>
    </div>

    <form method="post" action="?m=steam_workshop&p=workshop_admin" class="sw-form">
        <input type="hidden" name="sw_action"  value="profile_save">
        <input type="hidden" name="profile_id" value="<?php echo $profileId; ?>">

        <!-- Basic identification -->
        <fieldset>
            <legend><?php echo htmlspecialchars($lang['profile_section_basic'] ?? 'Basic identification'); ?></legend>
            <div class="sw-form__grid sw-form__grid--3col">
                <label>
                    <?php echo htmlspecialchars($lang['label_game_key'] ?? 'Game key'); ?> <em>*</em>
                    <small><?php echo htmlspecialchars($lang['config_hint_game_key'] ?? 'Short identifier matching the game XML key, e.g. dayz_linux'); ?></small>
                    <input type="text" name="game_key" value="<?php echo $v('game_key', $profile ?? []); ?>"
                           pattern="[A-Za-z0-9_\-.]+" required maxlength="100"
                           <?php echo $isEdit ? 'readonly' : ''; ?>>
                </label>
                <label>
                    <?php echo htmlspecialchars($lang['profile_label_game_name'] ?? 'Game display name'); ?> <em>*</em>
                    <input type="text" name="game_name" value="<?php echo $v('game_name', $profile ?? []); ?>"
                           required maxlength="255">
                </label>
                <label class="sw-checkbox" style="align-self:end;padding-bottom:0.75rem;">
                    <input type="checkbox" name="enabled" value="1"
                           <?php echo ($profile['enabled'] ?? 1) ? 'checked' : ''; ?>>
                    <span><?php echo htmlspecialchars($lang['config_label_enabled'] ?? 'Profile enabled'); ?></span>
                </label>
            </div>
        </fieldset>

        <!-- Steam / SteamCMD settings -->
        <fieldset>
            <legend><?php echo htmlspecialchars($lang['profile_section_steam'] ?? 'Steam &amp; SteamCMD settings'); ?></legend>
            <div class="sw-form__grid sw-form__grid--3col">
                <label>
                    <?php echo htmlspecialchars($lang['profile_label_steam_app_id'] ?? 'Steam App ID'); ?>
                    <small><?php echo htmlspecialchars($lang['profile_hint_steam_app_id'] ?? 'The Steam game App ID (e.g. 221100 for DayZ). Used when Steam login is required.'); ?></small>
                    <input type="text" name="steam_app_id"
                           value="<?php echo $v('steam_app_id', $profile ?? []); ?>"
                           pattern="[0-9]*" maxlength="32">
                </label>
                <label>
                    <?php echo htmlspecialchars($lang['config_label_app_id'] ?? 'Workshop App ID'); ?> <em>*</em>
                    <small><?php echo htmlspecialchars($lang['config_hint_app_id'] ?? 'The App ID used with +workshop_download_item, e.g. 221100 for DayZ'); ?></small>
                    <input type="text" name="workshop_app_id"
                           value="<?php echo $v('workshop_app_id', $profile ?? []); ?>"
                           pattern="[0-9]+" required maxlength="32">
                </label>
                <label>
                    <?php echo htmlspecialchars($lang['profile_label_steamcmd_path'] ?? 'SteamCMD path on agent'); ?>
                    <small><?php echo htmlspecialchars($lang['profile_hint_steamcmd_path'] ?? 'Full path to steamcmd.sh on the remote agent. Leave blank to use the agent default (/home/gameserver/steamcmd/steamcmd.sh).'); ?></small>
                    <input type="text" name="steamcmd_path"
                           value="<?php echo $v('steamcmd_path', $profile ?? []); ?>"
                           placeholder="/home/gameserver/steamcmd/steamcmd.sh" maxlength="512">
                </label>
            </div>

            <div class="sw-form__grid sw-form__grid--2col">
                <label class="sw-checkbox">
                    <input type="checkbox" name="steam_login_required" value="1"
                           id="sw-login-required"
                           <?php echo !empty($profile['steam_login_required']) ? 'checked' : ''; ?>>
                    <span><?php echo htmlspecialchars($lang['profile_label_steam_login_required'] ?? 'Steam login required (game is not free / requires ownership)'); ?></span>
                </label>
                <label>
                    <?php echo htmlspecialchars($lang['profile_label_steamcmd_login_mode'] ?? 'SteamCMD login mode'); ?>
                    <small><?php echo htmlspecialchars($lang['profile_hint_steamcmd_login_mode'] ?? 'Use anonymous for free Workshop mods. Use configured account for games requiring ownership.'); ?></small>
                    <select name="steamcmd_login_mode">
                        <?php foreach ($loginModes as $mVal => $mLabel): ?>
                            <option value="<?php echo $mVal; ?>" <?php echo $curLoginMode === $mVal ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($mLabel); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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

        <!-- Download & install paths -->
        <fieldset>
            <legend><?php echo htmlspecialchars($lang['profile_section_paths'] ?? 'Download &amp; install paths'); ?></legend>
            <small class="sw-hint"><?php echo htmlspecialchars($tplVarNote); ?></small>

            <label>
                <?php echo htmlspecialchars($lang['profile_label_cache_path'] ?? 'Workshop download/cache path'); ?> <em>*</em>
                <small><?php echo htmlspecialchars($lang['profile_hint_cache_path'] ?? 'Where SteamCMD stores downloaded mod content on the agent. E.g. /home/gameserver/steamcmd/steamapps/workshop/content/%workshop_app_id%/%workshop_id%'); ?></small>
                <input type="text" name="cache_path_template"
                       value="<?php echo $v('cache_path_template', $profile ?? []); ?>" required>
            </label>

            <label>
                <?php echo htmlspecialchars($lang['profile_label_install_path'] ?? 'Server mod install root'); ?> <em>*</em>
                <small><?php echo htmlspecialchars($lang['profile_hint_install_path'] ?? 'Base directory inside the server where mods are installed. E.g. %server_path%/mods/%install_name%'); ?></small>
                <input type="text" name="install_path_template"
                       value="<?php echo $v('install_path_template', $profile ?? []); ?>" required>
            </label>
        </fieldset>

        <!-- Mod folder naming -->
        <fieldset>
            <legend><?php echo htmlspecialchars($lang['profile_section_folder'] ?? 'Mod folder naming'); ?></legend>
            <label>
                <?php echo htmlspecialchars($lang['profile_label_folder_format'] ?? 'Folder naming format'); ?>
                <small><?php echo htmlspecialchars($lang['profile_hint_folder_format'] ?? 'How each mod folder is named inside the install root.'); ?></small>
                <select name="folder_naming_format" id="sw-folder-format">
                    <?php foreach ($folderFormats as $fVal => $fLabel): ?>
                        <option value="<?php echo $fVal; ?>" <?php echo $curFolderFormat === $fVal ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($fLabel); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <div id="sw-custom-folder-wrap" <?php echo $curFolderFormat !== 'custom' ? 'style="display:none"' : ''; ?>>
                <label>
                    <?php echo htmlspecialchars($lang['profile_label_folder_name'] ?? 'Custom folder name template'); ?>
                    <small><?php echo htmlspecialchars($lang['profile_hint_folder_name'] ?? 'Use %workshop_id% or %mod_name%. E.g. @%workshop_id%'); ?></small>
                    <input type="text" name="folder_name_template"
                           value="<?php echo $v('folder_name_template', $profile ?? [], '@%workshop_id%'); ?>">
                </label>
            </div>
        </fieldset>

        <!-- Launch parameters -->
        <fieldset>
            <legend><?php echo htmlspecialchars($lang['profile_section_launch'] ?? 'Launch parameters'); ?></legend>
            <div class="sw-form__grid sw-form__grid--2col">
                <label>
                    <?php echo htmlspecialchars($lang['profile_label_mod_launch_param'] ?? 'Mod launch parameter format'); ?>
                    <small><?php echo htmlspecialchars($lang['profile_hint_mod_launch_param'] ?? 'How the full mod list is passed to the server start command. E.g. -mod=%mods%'); ?></small>
                    <input type="text" name="mod_launch_param"
                           value="<?php echo $v('mod_launch_param', $profile ?? []); ?>"
                           placeholder="-mod=%mods%" maxlength="512">
                </label>
                <label>
                    <?php echo htmlspecialchars($lang['profile_label_mod_separator'] ?? 'Mod separator'); ?>
                    <small><?php echo htmlspecialchars($lang['profile_hint_mod_separator'] ?? 'Character used to join multiple mod folder names in the launch parameter.'); ?></small>
                    <select name="mod_separator">
                        <?php foreach ($separatorList as $sVal => $sLabel): ?>
                            <option value="<?php echo $sVal; ?>" <?php echo $curSeparator === $sVal ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($sLabel); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <label>
                <?php echo htmlspecialchars($lang['profile_label_launch_tpl'] ?? 'Full launch parameter template (optional)'); ?>
                <small><?php echo htmlspecialchars($lang['config_hint_launch_tpl'] ?? 'Complete launch parameter string appended to server start. Each mod folder name is joined with the separator above.'); ?></small>
                <input type="text" name="launch_param_template"
                       value="<?php echo $v('launch_param_template', $profile ?? []); ?>">
            </label>
        </fieldset>

        <!-- Copy / sync method -->
        <fieldset>
            <legend><?php echo htmlspecialchars($lang['config_section_copy'] ?? 'Copy / sync method'); ?></legend>
            <div class="sw-form__grid sw-form__grid--2col">
                <label>
                    <?php echo htmlspecialchars($lang['profile_label_copy_method'] ?? 'Copy method'); ?>
                    <select name="copy_method">
                        <?php foreach ($copyMethods as $mVal => $mLabel): ?>
                            <option value="<?php echo $mVal; ?>" <?php echo $curCopyMethod === $mVal ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($mLabel); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="sw-checkbox" style="align-self:end;padding-bottom:0.5rem;">
                    <input type="checkbox" name="copy_keys" value="1"
                           id="sw-copy-keys"
                           <?php echo !empty($profile['copy_keys']) ? 'checked' : ''; ?>>
                    <span><?php echo htmlspecialchars($lang['profile_label_copy_keys'] ?? 'Copy mod keys (*.bikey) to server keys directory'); ?></span>
                </label>
            </div>

            <div id="sw-key-paths-wrap" <?php echo empty($profile['copy_keys']) ? 'style="display:none"' : ''; ?>>
                <div class="sw-form__grid sw-form__grid--2col">
                    <label>
                        <?php echo htmlspecialchars($lang['profile_label_key_source'] ?? 'Key source path'); ?>
                        <small><?php echo htmlspecialchars($lang['profile_hint_key_source'] ?? 'Path inside the mod cache where key files live. E.g. %source_path%/keys'); ?></small>
                        <input type="text" name="key_source_path"
                               value="<?php echo $v('key_source_path', $profile ?? []); ?>"
                               placeholder="%source_path%/keys">
                    </label>
                    <label>
                        <?php echo htmlspecialchars($lang['profile_label_key_dest'] ?? 'Key destination path'); ?>
                        <small><?php echo htmlspecialchars($lang['profile_hint_key_dest'] ?? 'Where keys are copied on the server. E.g. %server_path%/keys'); ?></small>
                        <input type="text" name="key_dest_path"
                               value="<?php echo $v('key_dest_path', $profile ?? []); ?>"
                               placeholder="%server_path%/keys">
                    </label>
                </div>
            </div>
        </fieldset>

        <!-- Bash scripts -->
        <fieldset>
            <legend><?php echo htmlspecialchars($lang['profile_section_scripts'] ?? 'Bash scripts'); ?></legend>
            <div class="sw-info-box sw-info-box--compact">
                <strong><?php echo htmlspecialchars($lang['profile_scripts_order'] ?? 'Execution order:'); ?></strong>
                1. <?php echo htmlspecialchars($lang['profile_label_pre_script'] ?? 'Pre-update script'); ?> &rarr;
                2. <?php echo htmlspecialchars($lang['profile_label_install_script'] ?? 'Per-mod install script'); ?> (<?php echo htmlspecialchars($lang['profile_scripts_per_mod'] ?? 'repeated for each mod'); ?>) &rarr;
                3. <?php echo htmlspecialchars($lang['profile_label_post_script'] ?? 'Post-update script'); ?>
            </div>

            <label>
                <?php echo htmlspecialchars($lang['profile_label_pre_script'] ?? 'Pre-update bash script'); ?>
                <small><?php echo htmlspecialchars($lang['profile_hint_pre_script'] ?? 'Runs once before any mod is downloaded/installed. Variables: %home_id% %server_path% %workshop_app_id%'); ?></small>
                <textarea name="pre_update_script" rows="4" class="sw-script-textarea"><?php echo $v('pre_update_script', $profile ?? []); ?></textarea>
            </label>

            <label>
                <?php echo htmlspecialchars($lang['profile_label_install_script'] ?? 'Per-mod install bash script'); ?>
                <small><?php echo htmlspecialchars($lang['profile_hint_install_script'] ?? 'Runs once for each mod. All template variables listed above are available.'); ?></small>
                <details class="sw-example-block">
                    <summary><?php echo htmlspecialchars($lang['profile_script_example_toggle'] ?? 'Show DayZ-style example'); ?></summary>
<pre class="sw-code-pre">mkdir -p "%target_path%"
rsync -a --delete "%source_path%/" "%target_path%/"
if [ -d "%source_path%/keys" ]; then
  mkdir -p "%keys_target_path%"
  cp -f "%source_path%/keys/"*.bikey "%keys_target_path%/" 2>/dev/null || true
fi</pre>
                </details>
                <textarea name="install_script" rows="8" class="sw-script-textarea"><?php echo $v('install_script', $profile ?? []); ?></textarea>
            </label>

            <label>
                <?php echo htmlspecialchars($lang['profile_label_post_script'] ?? 'Post-update bash script'); ?>
                <small><?php echo htmlspecialchars($lang['profile_hint_post_script'] ?? 'Runs once after all mods have been installed. Variables: %home_id% %server_path% %workshop_app_id%'); ?></small>
                <textarea name="post_update_script" rows="4" class="sw-script-textarea"><?php echo $v('post_update_script', $profile ?? []); ?></textarea>
            </label>
        </fieldset>

        <!-- Options & validation -->
        <fieldset>
            <legend><?php echo htmlspecialchars($lang['profile_section_flags'] ?? 'Options &amp; validation'); ?></legend>
            <label class="sw-checkbox">
                <input type="checkbox" name="requires_restart" value="1"
                       <?php echo !empty($profile['requires_restart']) ? 'checked' : ''; ?>>
                <span><?php echo htmlspecialchars($lang['profile_label_requires_restart'] ?? 'Server restart required after mod install or update'); ?></span>
            </label>

            <label>
                <?php echo htmlspecialchars($lang['profile_label_validation_notes'] ?? 'Validation notes / help text (shown to server owners)'); ?>
                <textarea name="validation_notes" rows="3"><?php echo $v('validation_notes', $profile ?? []); ?></textarea>
            </label>

            <label>
                <?php echo htmlspecialchars($lang['profile_label_config_tpl'] ?? 'Config file template (optional)'); ?>
                <textarea name="config_file_template" rows="3"><?php echo $v('config_file_template', $profile ?? []); ?></textarea>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Show/hide custom folder template field
    const formatSel  = document.getElementById('sw-folder-format');
    const customWrap = document.getElementById('sw-custom-folder-wrap');
    if (formatSel && customWrap) {
        formatSel.addEventListener('change', function () {
            customWrap.style.display = this.value === 'custom' ? '' : 'none';
        });
    }

    // Show/hide key path fields
    const copyKeysChk  = document.getElementById('sw-copy-keys');
    const keyPathsWrap = document.getElementById('sw-key-paths-wrap');
    if (copyKeysChk && keyPathsWrap) {
        copyKeysChk.addEventListener('change', function () {
            keyPathsWrap.style.display = this.checked ? '' : 'none';
        });
    }
});
</script>
