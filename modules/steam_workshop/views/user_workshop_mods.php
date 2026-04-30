<?php
declare(strict_types=1);
/** @var array      $lang */
/** @var array      $home */
/** @var int        $homeId */
/** @var array|null $profile */
/** @var string|null $appId */
/** @var array[]    $installedMods */
/** @var array[]    $availableMods */
/** @var bool       $isAdmin */

$homeName   = htmlspecialchars($home['home_name'] ?? ('#' . $homeId));
$baseAction = '?m=steam_workshop&p=main';
?>
<div class="sw-user sw-ws-mods">
    <p><a href="<?php echo $baseAction; ?>">&larr; <?php echo htmlspecialchars($lang['button_cancel'] ?? 'Back'); ?></a></p>
    <h3><?php echo sprintf(htmlspecialchars($lang['user_workshop_server_heading'] ?? 'Workshop Mods – %s'), $homeName); ?></h3>

    <?php if ($profile === null): ?>
        <div class="sw-notice">
            <p><?php echo htmlspecialchars($lang['no_profile_notice'] ?? 'No Workshop profile is configured for this game. An administrator needs to create one first.'); ?></p>
        </div>
    <?php else: ?>

    <!-- Installed mods table -->
    <h4><?php echo htmlspecialchars($lang['heading_installed_mods'] ?? 'Installed Mods'); ?></h4>
    <?php if (empty($installedMods)): ?>
        <p class="sw-empty"><?php echo htmlspecialchars($lang['no_installed_mods'] ?? 'No mods installed yet.'); ?></p>
    <?php else: ?>
        <table class="table sw-ws-mods__table" id="sw-installed-<?php echo $homeId; ?>">
            <thead>
                <tr>
                    <th><?php echo htmlspecialchars($lang['col_mod_id'] ?? 'Workshop ID'); ?></th>
                    <th><?php echo htmlspecialchars($lang['col_mod_title'] ?? 'Title'); ?></th>
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

    <!-- Install from cache -->
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

    <!-- Search + install by Workshop ID -->
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

    <!-- Steam Workshop search widget (reuse existing JS picker) -->
    <?php
    $scriptPath    = (string)($_SERVER['PHP_SELF'] ?? '/index.php');
    $searchEndpoint = sprintf('%s?m=steam_workshop&p=main&action=search&home_id=%d', $scriptPath, $homeId);
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
/* Simple toggle / order auto-submit for the mods table */
document.addEventListener('DOMContentLoaded', function () {
    // Toggle enable/disable: submit the parent form immediately on change
    document.querySelectorAll('.js-ws-toggle').forEach(function (cb) {
        cb.addEventListener('change', function () {
            cb.closest('form').submit();
        });
    });

    // Load order: submit on change (blur triggers faster than enter on number inputs)
    document.querySelectorAll('.js-ws-order').forEach(function (inp) {
        inp.addEventListener('change', function () {
            inp.closest('form').submit();
        });
    });
});
</script>
