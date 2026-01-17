<?php
declare(strict_types=1);
/** @var array $lang */
/** @var array $gameKeys */
/** @var array $mappings */
/** @var array $adapterOptions */
/** @var array $adapters */
/** @var array $gameRows */
/** @var array|null $adapterForm */
/** @var string $activeGameKey */
?>
<div class="sw-admin">
    <h3><?php echo htmlspecialchars($lang['admin_heading_game_mapping'] ?? 'Game type adapter mapping'); ?></h3>
    <p><?php echo htmlspecialchars($lang['admin_subheading_game_mapping'] ?? 'Select which adapter will manage Steam Workshop installs for each supported game.'); ?></p>

    <form method="post" class="sw-form">
        <input type="hidden" name="admin_action" value="save_mappings">
        <table class="table sw-mods__table">
            <thead>
                <tr>
                    <th><?php echo htmlspecialchars($lang['admin_col_game_key'] ?? 'Game key'); ?></th>
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

    <h3><?php echo htmlspecialchars($lang['admin_heading_per_game'] ?? 'Per-game adapters'); ?></h3>
    <p><?php echo htmlspecialchars($lang['admin_subheading_per_game'] ?? 'Each game key gets its own adapter XML. Create, edit, or delete them below.'); ?></p>

    <table class="table sw-mods__table">
        <thead>
            <tr>
                <th><?php echo htmlspecialchars($lang['admin_col_game_key'] ?? 'Game key'); ?></th>
                <th><?php echo htmlspecialchars($lang['admin_col_status'] ?? 'Status'); ?></th>
                <th><?php echo htmlspecialchars($lang['admin_col_updated'] ?? 'Last updated'); ?></th>
                <th><?php echo htmlspecialchars($lang['admin_col_actions'] ?? 'Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($gameRows)): ?>
                <tr>
                    <td colspan="4"><?php echo htmlspecialchars($lang['admin_no_game_keys'] ?? 'No game definitions were found in modules/config_games/server_configs.'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($gameRows as $row): ?>
                    <?php $exists = !empty($row['exists']); ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['game_key']); ?></td>
                        <td>
                            <?php if ($exists): ?>
                                <?php echo htmlspecialchars($row['adapter']['name'] ?? $row['game_key']); ?>
                            <?php else: ?>
                                <?php echo htmlspecialchars($lang['status_no_adapter'] ?? 'No adapter'); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($exists && !empty($row['updated_at'])): ?>
                                <?php echo htmlspecialchars(date('Y-m-d H:i', (int)$row['updated_at'])); ?>
                            <?php else: ?>
                                &mdash;
                            <?php endif; ?>
                        </td>
                        <td class="sw-actions">
                            <a class="btn secondary" href="?m=steam_workshop&amp;p=workshop_admin&amp;adapter_game=<?php echo urlencode($row['game_key']); ?>#adapter-form">
                                <?php echo htmlspecialchars($exists ? ($lang['button_edit_adapter'] ?? 'Edit') : ($lang['button_create_adapter'] ?? 'Create')); ?>
                            </a>
                            <?php if ($exists): ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="admin_action" value="delete_adapter">
                                    <input type="hidden" name="game_key" value="<?php echo htmlspecialchars($row['game_key']); ?>">
                                    <button type="submit" class="btn danger" onclick="return confirm('<?php echo htmlspecialchars($lang['confirm_delete_adapter'] ?? 'Delete this adapter?'); ?>');">
                                        <?php echo htmlspecialchars($lang['button_delete_adapter'] ?? 'Delete'); ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div id="adapter-form" class="sw-adapter-form">
        <?php if ($adapterForm): ?>
            <h3><?php echo htmlspecialchars(sprintf($lang['admin_heading_edit_adapter'] ?? 'Editing adapter for %s', $adapterForm['game_key'])); ?></h3>
            <form method="post" class="sw-form">
                <input type="hidden" name="admin_action" value="save_adapter">
                <input type="hidden" name="game_key" value="<?php echo htmlspecialchars($adapterForm['game_key']); ?>">

                <div class="sw-form__row">
                    <label><?php echo htmlspecialchars($lang['label_game_key'] ?? 'Game key'); ?></label>
                    <input type="text" value="<?php echo htmlspecialchars($adapterForm['game_key']); ?>" readonly>
                </div>

                <div class="sw-form__row">
                    <label><?php echo htmlspecialchars($lang['label_adapter_name'] ?? 'Adapter display name'); ?></label>
                    <input type="text" name="adapter[name]" value="<?php echo htmlspecialchars($adapterForm['name']); ?>" required>
                </div>

                <div class="sw-form__row">
                    <label><?php echo htmlspecialchars($lang['label_adapter_app_id'] ?? 'Steam App ID'); ?></label>
                    <input type="text" name="adapter[steam_app_id]" value="<?php echo htmlspecialchars($adapterForm['steam_app_id']); ?>" required>
                </div>

                <div class="sw-form__row">
                    <label><?php echo htmlspecialchars($lang['label_adapter_mods_dir'] ?? 'Mods directory'); ?></label>
                    <input type="text" name="adapter[mods_dir]" value="<?php echo htmlspecialchars($adapterForm['mods_dir']); ?>" required>
                </div>

                <div class="sw-form__row">
                    <label><?php echo htmlspecialchars($lang['label_adapter_keys_dir'] ?? 'Keys directory (optional)'); ?></label>
                    <input type="text" name="adapter[keys_dir]" value="<?php echo htmlspecialchars($adapterForm['keys_dir']); ?>">
                </div>

                <div class="sw-form__row">
                    <label class="checkbox">
                        <input type="checkbox" name="adapter[supports_hot_reload]" value="1" <?php echo !empty($adapterForm['supports_hot_reload']) ? 'checked' : ''; ?> >
                        <span><?php echo htmlspecialchars($lang['label_adapter_hot_reload'] ?? 'Supports hot reload'); ?></span>
                    </label>
                </div>

                <div class="sw-form__row">
                    <label><?php echo htmlspecialchars($lang['label_adapter_activation'] ?? 'Activation template'); ?></label>
                    <textarea name="adapter[activation_template]" rows="4"><?php echo htmlspecialchars($adapterForm['activation_template']); ?></textarea>
                </div>

                <div class="sw-form__row">
                    <label><?php echo htmlspecialchars($lang['label_adapter_notes'] ?? 'Notes'); ?></label>
                    <textarea name="adapter[notes]" rows="3"><?php echo htmlspecialchars($adapterForm['notes']); ?></textarea>
                </div>

                <div class="sw-form__actions">
                    <button class="btn primary" type="submit"><?php echo htmlspecialchars($lang['button_save_adapter'] ?? 'Save adapter'); ?></button>
                    <a class="btn" href="?m=steam_workshop&amp;p=workshop_admin"><?php echo htmlspecialchars($lang['button_cancel'] ?? 'Cancel'); ?></a>
                </div>
            </form>
        <?php else: ?>
            <p><?php echo htmlspecialchars($lang['admin_hint_select_game'] ?? 'Select a game above to start editing its adapter.'); ?></p>
        <?php endif; ?>
    </div>

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
