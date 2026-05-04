<?php
declare(strict_types=1);
/** @var array      $lang */
/** @var array      $home */
/** @var int        $homeId */
/** @var array|null $profile */
/** @var string|null $appId */
/** @var array[]    $installedMods */
/** @var array[]    $availableMods */
/** @var array      $serverSettings */
/** @var array[]    $allProfiles */
/** @var bool       $isAdmin */

$homeName   = htmlspecialchars($home['home_name'] ?? ('#' . $homeId));
$baseAction = '?m=steam_workshop&p=main';

$wsEnabled      = !empty($serverSettings['workshop_enabled']);
$curProfileId   = (int)($serverSettings['profile_id'] ?? 0);
$updateMode     = (string)($serverSettings['update_mode'] ?? 'manual');
$restartBehav   = (string)($serverSettings['restart_behavior'] ?? 'none');
$lastStatus     = (string)($serverSettings['last_update_status'] ?? '');
$lastError      = (string)($serverSettings['last_update_error'] ?? '');
$lastUpdateTime = (string)($serverSettings['last_update_time'] ?? '');
$lastSuccess    = (string)($serverSettings['last_success_time'] ?? '');
$updateQueued   = !empty($serverSettings['update_queued']);

$updateModes = [
    'manual'     => $lang['update_mode_manual']    ?? 'Manual only',
    'scheduled'  => $lang['update_mode_scheduled'] ?? 'Scheduled',
    'on_restart' => $lang['update_mode_on_restart'] ?? 'Before server restart',
];
$restartBehaviors = [
    'none'             => $lang['restart_behavior_none']   ?? 'No restart',
    'queue'            => $lang['restart_behavior_queue']  ?? 'Queue restart',
    'stop_update_start'=> $lang['restart_behavior_stop']   ?? 'Stop / Update / Start',
];

$statusClass = match($lastStatus) {
    'success' => 'sw-badge--enabled',
    'failed'  => 'sw-badge--danger',
    'running' => 'sw-badge--info',
    'pending' => 'sw-badge--warning',
    default   => '',
};
?>
<div class="sw-user sw-ws-mods">
    <p><a href="<?php echo $baseAction; ?>">&larr; <?php echo htmlspecialchars($lang['button_cancel'] ?? 'Back'); ?></a></p>
    <h3><?php echo sprintf(htmlspecialchars($lang['user_workshop_server_heading'] ?? 'Workshop Mods – %s'), $homeName); ?></h3>

    <!-- ── Workshop server settings ── -->
    <section class="sw-server-settings">
        <h4><?php echo htmlspecialchars($lang['heading_server_settings'] ?? 'Workshop Settings for this server'); ?></h4>
        <form method="post" action="<?php echo $baseAction; ?>" class="sw-form sw-settings-form">
            <input type="hidden" name="ws_action" value="save_settings">
            <input type="hidden" name="home_id"   value="<?php echo $homeId; ?>">

            <div class="sw-form__grid sw-form__grid--2col">
                <label class="sw-checkbox">
                    <input type="checkbox" name="workshop_enabled" value="1" id="sw-ws-enabled"
                           <?php echo $wsEnabled ? 'checked' : ''; ?>>
                    <span><?php echo htmlspecialchars($lang['label_workshop_enabled'] ?? 'Enable Workshop for this server'); ?></span>
                </label>

                <label>
                    <?php echo htmlspecialchars($lang['label_select_profile'] ?? 'Workshop game profile'); ?>
                    <select name="profile_id">
                        <option value="0">-- <?php echo htmlspecialchars($lang['label_auto_detect'] ?? 'Auto-detect from game type'); ?> --</option>
                        <?php foreach ((array)$allProfiles as $p): ?>
                            <option value="<?php echo (int)$p['id']; ?>"
                                <?php echo $curProfileId === (int)$p['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['game_name'] . ' (' . $p['workshop_app_id'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    <?php echo htmlspecialchars($lang['label_update_mode'] ?? 'Update mode'); ?>
                    <select name="update_mode">
                        <?php foreach ($updateModes as $mVal => $mLabel): ?>
                            <option value="<?php echo $mVal; ?>" <?php echo $updateMode === $mVal ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($mLabel); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    <?php echo htmlspecialchars($lang['label_restart_behavior'] ?? 'Restart behavior'); ?>
                    <select name="restart_behavior">
                        <?php foreach ($restartBehaviors as $rVal => $rLabel): ?>
                            <option value="<?php echo $rVal; ?>" <?php echo $restartBehav === $rVal ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($rLabel); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <div class="sw-form__actions">
                <button type="submit" class="btn primary">
                    <?php echo htmlspecialchars($lang['button_save'] ?? 'Save'); ?>
                </button>
            </div>
        </form>

        <!-- Update status summary -->
        <div class="sw-update-status">
            <dl class="sw-status-grid">
                <?php if ($lastStatus !== ''): ?>
                    <dt><?php echo htmlspecialchars($lang['label_last_update_status'] ?? 'Last update status'); ?></dt>
                    <dd><span class="sw-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($lastStatus); ?></span></dd>
                <?php endif; ?>
                <?php if ($lastUpdateTime !== ''): ?>
                    <dt><?php echo htmlspecialchars($lang['label_last_update_time'] ?? 'Last update time'); ?></dt>
                    <dd><?php echo htmlspecialchars($lastUpdateTime); ?></dd>
                <?php endif; ?>
                <?php if ($lastSuccess !== ''): ?>
                    <dt><?php echo htmlspecialchars($lang['label_last_success_time'] ?? 'Last successful update'); ?></dt>
                    <dd><?php echo htmlspecialchars($lastSuccess); ?></dd>
                <?php endif; ?>
                <?php if ($lastError !== ''): ?>
                    <dt><?php echo htmlspecialchars($lang['label_last_update_error'] ?? 'Last error'); ?></dt>
                    <dd class="sw-error-text"><code><?php echo htmlspecialchars($lastError); ?></code></dd>
                <?php endif; ?>
            </dl>

            <?php if ($updateQueued): ?>
                <p class="sw-notice sw-notice--info">
                    <?php echo htmlspecialchars($lang['update_queued_notice'] ?? 'A manual update is queued and will run on the next scheduler cycle.'); ?>
                </p>
            <?php endif; ?>

            <!-- Queue manual update -->
            <form method="post" action="<?php echo $baseAction; ?>" class="sw-inline">
                <input type="hidden" name="ws_action" value="queue_update">
                <input type="hidden" name="home_id"   value="<?php echo $homeId; ?>">
                <button type="submit" class="btn secondary"
                        <?php echo !$wsEnabled ? 'disabled title="Enable Workshop for this server first."' : ''; ?>>
                    <?php echo htmlspecialchars($lang['btn_queue_update'] ?? 'Queue manual update'); ?>
                </button>
            </form>
        </div>
    </section>

    <?php if ($profile === null): ?>
        <div class="sw-notice">
            <p><?php echo htmlspecialchars($lang['no_profile_notice'] ?? 'No Workshop profile is configured for this game. An administrator needs to create one first.'); ?></p>
        </div>
    <?php else: ?>

    <?php if (!empty($profile['validation_notes'])): ?>
        <div class="sw-notice sw-notice--info">
            <strong><?php echo htmlspecialchars($lang['label_admin_notes'] ?? 'Admin notes:'); ?></strong>
            <?php echo htmlspecialchars($profile['validation_notes']); ?>
        </div>
    <?php endif; ?>

    <!-- ── Installed mods table ── -->
    <h4><?php echo htmlspecialchars($lang['heading_installed_mods'] ?? 'Installed Mods'); ?></h4>
    <?php if (empty($installedMods)): ?>
        <p class="sw-empty"><?php echo htmlspecialchars($lang['no_installed_mods'] ?? 'No mods installed yet.'); ?></p>
    <?php else: ?>
        <table class="table sw-ws-mods__table" id="sw-installed-<?php echo $homeId; ?>">
            <thead>
                <tr>
                    <th><?php echo htmlspecialchars($lang['col_mod_id'] ?? 'Workshop ID'); ?></th>
                    <th><?php echo htmlspecialchars($lang['col_mod_title'] ?? 'Title'); ?></th>
                    <th><?php echo htmlspecialchars($lang['col_mod_folder'] ?? 'Install folder'); ?></th>
                    <th><?php echo htmlspecialchars($lang['mods_header_enabled'] ?? 'Enabled'); ?></th>
                    <th><?php echo htmlspecialchars($lang['col_load_order'] ?? 'Load order'); ?></th>
                    <th><?php echo htmlspecialchars($lang['admin_col_actions'] ?? 'Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ((array)$installedMods as $mod): ?>
                    <?php $wid = htmlspecialchars($mod['workshop_id']); ?>
                    <tr data-workshop-id="<?php echo $wid; ?>">
                        <td>
                            <a href="https://steamcommunity.com/sharedfiles/filedetails/?id=<?php echo $wid; ?>"
                               target="_blank" rel="noopener"><?php echo $wid; ?></a>
                        </td>
                        <td><?php echo htmlspecialchars($mod['title'] ?? $mod['workshop_id']); ?></td>
                        <td><code><?php echo htmlspecialchars($mod['custom_folder'] !== '' ? $mod['custom_folder'] : ($mod['install_path'] ?? '')); ?></code></td>
                        <td>
                            <form method="post" action="<?php echo $baseAction; ?>" class="sw-toggle-form">
                                <input type="hidden" name="ws_action"   value="toggle">
                                <input type="hidden" name="home_id"     value="<?php echo $homeId; ?>">
                                <input type="hidden" name="workshop_id" value="<?php echo $wid; ?>">
                                <label class="sw-toggle">
                                    <input type="checkbox" name="enabled" value="1"
                                           class="js-ws-toggle"
                                           <?php echo !empty($mod['enabled']) ? 'checked' : ''; ?>>
                                    <span><?php echo !empty($mod['enabled']) ? htmlspecialchars($lang['status_enabled'] ?? 'Yes') : htmlspecialchars($lang['status_disabled'] ?? 'No'); ?></span>
                                </label>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="<?php echo $baseAction; ?>" class="sw-order-form">
                                <input type="hidden" name="ws_action"   value="load_order">
                                <input type="hidden" name="home_id"     value="<?php echo $homeId; ?>">
                                <input type="hidden" name="workshop_id" value="<?php echo $wid; ?>">
                                <input type="number" name="load_order"
                                       value="<?php echo (int)$mod['load_order']; ?>"
                                       min="0" max="9999" class="sw-order-input js-ws-order"
                                       style="width:5em">
                            </form>
                        </td>
                        <td class="sw-actions">
                            <!-- Sync now -->
                            <form method="post" action="<?php echo $baseAction; ?>" class="sw-inline">
                                <input type="hidden" name="ws_action"   value="sync">
                                <input type="hidden" name="home_id"     value="<?php echo $homeId; ?>">
                                <input type="hidden" name="workshop_id" value="<?php echo $wid; ?>">
                                <button type="submit" class="btn secondary">
                                    <?php echo htmlspecialchars($lang['btn_sync_now'] ?? 'Sync now'); ?>
                                </button>
                            </form>
                            <!-- Remove -->
                            <form method="post" action="<?php echo $baseAction; ?>" class="sw-inline">
                                <input type="hidden" name="ws_action"   value="remove">
                                <input type="hidden" name="home_id"     value="<?php echo $homeId; ?>">
                                <input type="hidden" name="workshop_id" value="<?php echo $wid; ?>">
                                <button type="submit" class="btn danger"
                                    onclick="return confirm('<?php echo htmlspecialchars($lang['confirm_remove_mod'] ?? 'Remove this mod?'); ?>')">
                                    <?php echo htmlspecialchars($lang['btn_remove_mod'] ?? 'Remove'); ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- ── Available cached mods ── -->
    <?php if (!empty($availableMods)): ?>
        <h4><?php echo htmlspecialchars($lang['heading_cached_mods'] ?? 'Available Cached Mods (this agent)'); ?></h4>
        <table class="table sw-ws-mods__cache-table">
            <thead>
                <tr>
                    <th><?php echo htmlspecialchars($lang['col_mod_id'] ?? 'Workshop ID'); ?></th>
                    <th><?php echo htmlspecialchars($lang['col_mod_title'] ?? 'Title'); ?></th>
                    <th><?php echo htmlspecialchars($lang['col_cache_status'] ?? 'Cache status'); ?></th>
                    <th><?php echo htmlspecialchars($lang['admin_col_actions'] ?? 'Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ((array)$availableMods as $cached): ?>
                    <?php $cid = htmlspecialchars($cached['workshop_id']); ?>
                    <tr>
                        <td><?php echo $cid; ?></td>
                        <td><?php echo htmlspecialchars($cached['title'] ?? $cached['workshop_id']); ?></td>
                        <td><?php echo htmlspecialchars($cached['status']); ?></td>
                        <td>
                            <form method="post" action="<?php echo $baseAction; ?>">
                                <input type="hidden" name="ws_action"   value="install">
                                <input type="hidden" name="home_id"     value="<?php echo $homeId; ?>">
                                <input type="hidden" name="workshop_id" value="<?php echo $cid; ?>">
                                <button type="submit" class="btn secondary">
                                    <?php echo htmlspecialchars($lang['btn_install_mod'] ?? 'Install'); ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- ── Install by Workshop ID ── -->
    <h4><?php echo htmlspecialchars($lang['heading_install_mod'] ?? 'Install Mod by Workshop ID'); ?></h4>
    <form method="post" action="<?php echo $baseAction; ?>" class="sw-form sw-install-form">
        <input type="hidden" name="ws_action" value="install">
        <input type="hidden" name="home_id"   value="<?php echo $homeId; ?>">
        <div class="sw-form__row">
            <label>
                <?php echo htmlspecialchars($lang['label_workshop_id_input'] ?? 'Workshop ID'); ?>
                <input type="text" name="workshop_id" pattern="[0-9]+" required
                       placeholder="<?php echo htmlspecialchars($lang['placeholder_workshop_id'] ?? 'e.g. 1234567890'); ?>">
            </label>
            <button type="submit" class="btn primary">
                <?php echo htmlspecialchars($lang['btn_install_mod'] ?? 'Install'); ?>
            </button>
        </div>
    </form>

    <!-- ── Steam Workshop search widget ── -->
    <?php
    $requestPath     = (string)($_SERVER['PHP_SELF'] ?? '/index.php');
    $searchEndpoint = sprintf('%s?m=steam_workshop&p=main&action=search&home_id=%d', $requestPath, $homeId);
    $langAttrs = [
        'add'     => $lang['mod_picker_action_add'] ?? 'Add',
        'remove'  => $lang['mod_picker_action_remove'] ?? 'Remove',
        'loading' => $lang['mod_picker_status_loading'] ?? 'Searching…',
        'error'   => $lang['mod_picker_status_error'] ?? 'Search failed.',
        'empty'   => $lang['mod_picker_results_empty'] ?? 'No results.',
        'query'   => $lang['mod_picker_status_need_query'] ?? 'Enter a query.',
        'sync'    => $lang['mod_picker_toggle_label'] ?? 'Sync',
    ];
    ?>
    <div class="sw-picker" id="sw-picker-ws-<?php echo $homeId; ?>"
         data-endpoint="<?php echo htmlspecialchars($searchEndpoint, ENT_QUOTES); ?>"
         data-detail-base="https://steamcommunity.com/sharedfiles/filedetails/?id="
         data-install-action="<?php echo $baseAction; ?>"
         data-home-id="<?php echo $homeId; ?>"
         <?php foreach ((array)$langAttrs as $lk => $lv): ?>data-lang-<?php echo $lk; ?>="<?php echo htmlspecialchars($lv, ENT_QUOTES); ?>" <?php endforeach; ?>>
        <div class="sw-picker__header">
            <h5><?php echo htmlspecialchars($lang['mod_picker_heading'] ?? 'Search Steam Workshop'); ?></h5>
        </div>
        <div class="sw-picker__search js-sw-search-form" role="search">
            <label>
                <span><?php echo htmlspecialchars($lang['mod_picker_search_label'] ?? 'Search'); ?></span>
                <input type="text" class="sw-picker__search-input js-sw-search-input"
                       placeholder="<?php echo htmlspecialchars($lang['mod_picker_search_placeholder'] ?? 'ID or keyword'); ?>">
            </label>
            <button type="button" class="btn secondary js-sw-search-button">
                <?php echo htmlspecialchars($lang['mod_picker_search_button'] ?? 'Search'); ?>
            </button>
        </div>
        <div class="sw-picker__status js-sw-picker-status" role="status" aria-live="polite"></div>
        <div class="sw-picker__results">
            <table class="sw-picker__results-table">
                <thead>
                    <tr>
                        <th><?php echo htmlspecialchars($lang['col_mod_id'] ?? 'ID'); ?></th>
                        <th><?php echo htmlspecialchars($lang['col_mod_title'] ?? 'Title'); ?></th>
                        <th><?php echo htmlspecialchars($lang['admin_col_actions'] ?? 'Action'); ?></th>
                    </tr>
                </thead>
                <tbody class="js-sw-results"></tbody>
            </table>
        </div>
    </div>

    <?php endif; // profile !== null ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Toggle enable/disable: submit the parent form immediately on change
    document.querySelectorAll('.js-ws-toggle').forEach(function (cb) {
        cb.addEventListener('change', function () { cb.closest('form').submit(); });
    });

    // Load order: submit on change
    document.querySelectorAll('.js-ws-order').forEach(function (inp) {
        inp.addEventListener('change', function () { inp.closest('form').submit(); });
    });
});
</script>
