<?php
declare(strict_types=1);
/** @var array $lang */
/** @var array $gameKeys */
/** @var array $mappings */
/** @var array $adapterOptions */
/** @var array $adapters */
?>
<div class="sw-admin">
    <h3><?php echo htmlspecialchars($lang['admin_heading_game_mapping'] ?? 'Game type adapter mapping'); ?></h3>
    <p><?php echo htmlspecialchars($lang['admin_subheading_game_mapping'] ?? 'Select which adapter will manage Steam Workshop installs for each supported game.'); ?></p>

    <form method="post" class="sw-form">
        <table class="table sw-mods__table">
            <thead>
                <tr>
                    <th><?php echo htmlspecialchars($lang['admin_col_game_key'] ?? 'Game Key'); ?></th>
                    <th><?php echo htmlspecialchars($lang['admin_col_adapter'] ?? 'Adapter'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($gameKeys)): ?>
                    <tr>
                        <td colspan="2"><?php echo htmlspecialchars($lang['admin_no_game_keys'] ?? 'No game definitions were found in modules/config_games/server_configs.'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($gameKeys as $gameKey): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($gameKey); ?></td>
                            <td>
                                <select name="mapping[<?php echo htmlspecialchars($gameKey); ?>]">
                                    <option value="">--</option>
                                    <?php foreach ($adapterOptions as $key => $label): ?>
                                        <option value="<?php echo htmlspecialchars($key); ?>" <?php echo (isset($mappings[$gameKey]) && $mappings[$gameKey] === $key) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="sw-form__actions">
            <button class="btn primary" type="submit"><?php echo htmlspecialchars($lang['button_save']); ?></button>
        </div>
    </form>

    <h3><?php echo htmlspecialchars($lang['admin_heading_adapters'] ?? 'Available adapters'); ?></h3>
    <table class="table sw-mods__table">
        <thead>
            <tr>
                <th><?php echo htmlspecialchars($lang['admin_col_key'] ?? 'Key'); ?></th>
                <th><?php echo htmlspecialchars($lang['summary_adapter']); ?></th>
                <th>Steam App ID</th>
                <th><?php echo htmlspecialchars($lang['admin_col_mods_dir'] ?? 'Mods Dir'); ?></th>
                <th><?php echo htmlspecialchars($lang['summary_hot_reload']); ?></th>
                <th><?php echo htmlspecialchars($lang['admin_col_notes'] ?? 'Notes'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($adapters as $adapter): ?>
                <tr>
                    <td><?php echo htmlspecialchars($adapter['key']); ?></td>
                    <td><?php echo htmlspecialchars($adapter['name']); ?></td>
                    <td><?php echo htmlspecialchars($adapter['steam_app_id']); ?></td>
                    <td><?php echo htmlspecialchars($adapter['mods_dir']); ?></td>
                    <td><?php echo !empty($adapter['supports_hot_reload']) ? htmlspecialchars($lang['status_hot_reload']) : htmlspecialchars($lang['status_restart_required']); ?></td>
                    <td><?php echo htmlspecialchars($adapter['notes']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
