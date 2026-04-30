<?php
declare(strict_types=1);
/** @var array   $lang */
/** @var array[] $records  each: {home, profile, mods} */
/** @var bool    $isAdmin */
?>
<div class="sw-user sw-ws-index">
    <h3><?php echo htmlspecialchars($lang['user_workshop_heading'] ?? 'Steam Workshop'); ?></h3>

    <?php if (empty($records)): ?>
        <p class="sw-empty">
            <?php echo htmlspecialchars($isAdmin ? ($lang['empty_state_admin'] ?? 'No game homes assigned.') : ($lang['empty_state_user'] ?? 'No servers available.')); ?>
        </p>
    <?php else: ?>
        <table class="table sw-ws-index__table">
            <thead>
                <tr>
                    <th><?php echo htmlspecialchars($lang['col_server'] ?? 'Server'); ?></th>
                    <th><?php echo htmlspecialchars($lang['col_game'] ?? 'Game'); ?></th>
                    <th><?php echo htmlspecialchars($lang['col_mods_count'] ?? 'Installed mods'); ?></th>
                    <th><?php echo htmlspecialchars($lang['col_profile'] ?? 'Profile'); ?></th>
                    <th><?php echo htmlspecialchars($lang['admin_col_actions'] ?? 'Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ((array)$records as $record): ?>
                    <?php
                    $home    = $record['home'];
                    $profile = $record['profile'];
                    $mods    = $record['mods'];
                    $homeId  = (int)($home['home_id'] ?? 0);
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($home['home_name'] ?? ('#' . $homeId)); ?></td>
                        <td><?php echo htmlspecialchars($home['game_key'] ?? ''); ?></td>
                        <td><?php echo count((array)$mods); ?></td>
                        <td>
                            <?php if ($profile !== null): ?>
                                <span class="sw-badge sw-badge--enabled">
                                    <?php echo htmlspecialchars($profile['game_name']); ?>
                                </span>
                            <?php else: ?>
                                <span class="sw-badge sw-badge--disabled">
                                    <?php echo htmlspecialchars($lang['no_profile'] ?? 'No profile'); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="sw-actions">
                            <?php if ($profile !== null): ?>
                                <a class="btn secondary"
                                   href="?m=steam_workshop&p=main&action=mods&home_id=<?php echo $homeId; ?>">
                                    <?php echo htmlspecialchars($lang['btn_manage_mods'] ?? 'Manage Mods'); ?>
                                </a>
                            <?php else: ?>
                                <span class="sw-hint">
                                    <?php echo htmlspecialchars($lang['hint_no_profile'] ?? 'Ask an admin to create a Workshop profile for this game.'); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
