<?php
declare(strict_types=1);
/** @var array          $lang */
/** @var array[]        $profiles */
?>
<div class="sw-admin sw-profiles">
    <div class="sw-admin__intro">
        <h3><?php echo htmlspecialchars($lang['config_heading_list'] ?? 'Workshop Game Configurations'); ?></h3>
        <p><?php echo htmlspecialchars($lang['config_intro'] ?? 'One configuration per supported game. Each configuration controls how SteamCMD downloads and installs Workshop mods for servers of that game type.'); ?></p>
        <a class="btn primary" href="?m=steam_workshop&p=workshop_admin&sw_action=config_form">
            <?php echo htmlspecialchars($lang['config_btn_create'] ?? 'Add Game Configuration'); ?>
        </a>
    </div>

    <?php if (empty($profiles)): ?>
        <p class="sw-empty"><?php echo htmlspecialchars($lang['config_list_empty'] ?? 'No Workshop configurations defined yet. Add one for each game that supports Steam Workshop mods.'); ?></p>
    <?php else: ?>
        <table class="table sw-profiles__table">
            <thead>
                <tr>
                    <th><?php echo htmlspecialchars($lang['profile_col_game'] ?? 'Game'); ?></th>
                    <th><?php echo htmlspecialchars($lang['profile_col_key'] ?? 'Game Key'); ?></th>
                    <th>App ID</th>
                    <th>OS</th>
                    <th><?php echo htmlspecialchars($lang['profile_col_method'] ?? 'Install Method'); ?></th>
                    <th><?php echo htmlspecialchars($lang['profile_col_restart'] ?? 'Restart?'); ?></th>
                    <th><?php echo htmlspecialchars($lang['profile_col_status'] ?? 'Status'); ?></th>
                    <th><?php echo htmlspecialchars($lang['admin_col_actions'] ?? 'Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ((array)$profiles as $profile): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($profile['game_name']); ?></td>
                        <td><code><?php echo htmlspecialchars($profile['game_key']); ?></code></td>
                        <td><?php echo htmlspecialchars($profile['workshop_app_id']); ?></td>
                        <td><?php echo htmlspecialchars($profile['supported_os']); ?></td>
                        <td><?php echo htmlspecialchars($profile['copy_method']); ?></td>
                        <td><?php echo $profile['requires_restart'] ? '&#10004;' : '&#10008;'; ?></td>
                        <td>
                            <?php if ($profile['enabled']): ?>
                                <span class="sw-badge sw-badge--enabled"><?php echo htmlspecialchars($lang['status_enabled'] ?? 'Enabled'); ?></span>
                            <?php else: ?>
                                <span class="sw-badge sw-badge--disabled"><?php echo htmlspecialchars($lang['status_disabled'] ?? 'Disabled'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="sw-actions">
                            <a class="btn secondary"
                               href="?m=steam_workshop&p=workshop_admin&sw_action=config_form&profile_id=<?php echo (int)$profile['id']; ?>">
                                <?php echo htmlspecialchars($lang['button_edit'] ?? 'Edit'); ?>
                            </a>
                            <form method="post" action="?m=steam_workshop&p=workshop_admin" class="sw-inline-delete">
                                <input type="hidden" name="sw_action"   value="profile_delete">
                                <input type="hidden" name="profile_id"  value="<?php echo (int)$profile['id']; ?>">
                                <button type="submit" class="btn danger"
                                    onclick="return confirm('<?php echo htmlspecialchars($lang['config_confirm_delete'] ?? 'Delete this Workshop configuration? Servers using it will no longer have Workshop mod support.'); ?>')">
                                    <?php echo htmlspecialchars($lang['button_delete'] ?? 'Delete'); ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
