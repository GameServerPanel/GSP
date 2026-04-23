<?php
declare(strict_types=1);
/** @var array $lang */
/** @var array $gameRows */
/** @var array $adapterOptions */
/** @var array $adapters */
/** @var string $activeGameKey */
?>
<div class="sw-admin">
    <div class="sw-admin__intro">
        <h3><?php echo htmlspecialchars($lang['admin_heading_game_mapping'] ?? 'Game type adapter mapping'); ?></h3>
        <p><?php echo htmlspecialchars($lang['admin_subheading_game_mapping'] ?? 'Assign an adapter and edit its XML without leaving the table.'); ?></p>
    </div>

    <form id="sw-mapping-form" method="post">
        <input type="hidden" name="admin_action" value="save_mappings">
    </form>

    <div class="sw-game-table__wrapper">
        <table class="table sw-game-table">
            <thead>
                <tr>
                    <th><?php echo htmlspecialchars($lang['admin_col_game_key'] ?? 'Game key'); ?></th>
                    <th><?php echo htmlspecialchars($lang['admin_col_adapter'] ?? 'Mapping'); ?></th>
                    <th><?php echo htmlspecialchars($lang['admin_col_status'] ?? 'Adapter status'); ?></th>
                    <th><?php echo htmlspecialchars($lang['admin_col_updated'] ?? 'Last updated'); ?></th>
                    <th><?php echo htmlspecialchars($lang['admin_col_actions'] ?? 'Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($gameRows)): ?>
                    <tr>
                        <td colspan="5"><?php echo htmlspecialchars($lang['admin_no_game_keys'] ?? 'No Steam Workshop-enabled game definitions were detected.'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ((array)$gameRows as $row): ?>
                        <?php
                            $groupKey = $row['group_key'];
                            $primaryKey = $row['primary_game_key'];
                            $selectValue = $row['selected_adapter'] ?: ($row['exists'] ? $primaryKey : '');
                            $statusLabel = $row['exists']
                                ? ($row['adapter']['name'] ?? $primaryKey)
                                : ($lang['status_no_adapter'] ?? 'No adapter');
                            $isOpen = ($activeGameKey !== '' && $activeGameKey === $primaryKey);
                            $formId = 'adapter-panel-' . preg_replace('/[^a-z0-9_-]/i', '', $groupKey);
                            $form = $row['form'];
                        ?>
                        <tr class="sw-game-table__row">
                            <td>
                                <div class="sw-game-label">
                                    <div class="sw-game-label__title">
                                        <span class="sw-game-label__name"><?php echo htmlspecialchars($row['game_name']); ?></span>
                                        <span class="sw-badge sw-badge--app">App ID <?php echo htmlspecialchars($row['app_id']); ?></span>
                                        <?php if ($row['exists']): ?>
                                            <span class="sw-badge sw-badge--custom"><?php echo htmlspecialchars($lang['badge_custom_xml'] ?? 'Custom XML'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="sw-game-variants">
                                        <?php foreach ((array)$row['game_keys'] as $variantKey): ?>
                                            <span class="sw-chip"><?php echo htmlspecialchars($variantKey); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <small class="sw-game-label__hint"><?php echo htmlspecialchars($lang['admin_hint_inline_edit'] ?? 'Use the toggle to edit the XML inline.'); ?></small>
                            </td>
                            <td>
                                <select form="sw-mapping-form" name="mapping[<?php echo htmlspecialchars($groupKey); ?>]">
                                    <option value="">--</option>
                                    <?php foreach ((array)$adapterOptions as $key => $label): ?>
                                        <option value="<?php echo htmlspecialchars($key); ?>" <?php echo ($selectValue === $key) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($row['mixed_mapping'])): ?>
                                    <small class="sw-game-label__hint sw-game-label__hint--warning"><?php echo htmlspecialchars($lang['admin_hint_mixed_mapping'] ?? 'Different adapters assigned across variants. Saving will sync them.'); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($statusLabel); ?></td>
                            <td>
                                <?php if (!empty($row['updated_at'])): ?>
                                    <?php echo htmlspecialchars(date('Y-m-d H:i', (int)$row['updated_at'])); ?>
                                <?php else: ?>
                                    &mdash;
                                <?php endif; ?>
                            </td>
                            <td class="sw-actions">
                                <button type="button" class="btn secondary js-toggle-adapter" data-target="<?php echo htmlspecialchars($formId); ?>" aria-expanded="<?php echo $isOpen ? 'true' : 'false'; ?>">
                                    <?php echo htmlspecialchars($row['exists'] ? ($lang['button_edit_adapter'] ?? 'Edit adapter') : ($lang['button_create_adapter'] ?? 'Create adapter')); ?>
                                </button>
                                <?php if ($row['exists']): ?>
                                    <form method="post" class="sw-inline-delete">
                                        <input type="hidden" name="admin_action" value="delete_adapter">
                                        <input type="hidden" name="game_key" value="<?php echo htmlspecialchars($primaryKey); ?>">
                                        <button type="submit" class="btn danger" onclick="return confirm('<?php echo htmlspecialchars($lang['confirm_delete_adapter'] ?? 'Delete this adapter?'); ?>');">
                                            <?php echo htmlspecialchars($lang['button_delete_adapter'] ?? 'Delete'); ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr id="<?php echo htmlspecialchars($formId); ?>" class="sw-game-table__form-row <?php echo $isOpen ? 'is-open' : ''; ?>">
                            <td colspan="5">
                                <form method="post" class="sw-form sw-inline-form">
                                    <input type="hidden" name="admin_action" value="save_adapter">
                                    <input type="hidden" name="game_key" value="<?php echo htmlspecialchars($form['game_key']); ?>">

                                    <div class="sw-form__grid">
                                        <label>
                                            <?php echo htmlspecialchars($lang['label_game_key'] ?? 'Game key'); ?>
                                            <input type="text" value="<?php echo htmlspecialchars($form['game_key']); ?>" readonly>
                                        </label>
                                        <label>
                                            <?php echo htmlspecialchars($lang['label_adapter_name'] ?? 'Adapter display name'); ?>
                                            <input type="text" name="adapter[name]" value="<?php echo htmlspecialchars($form['name']); ?>" required>
                                        </label>
                                        <label>
                                            <?php echo htmlspecialchars($lang['label_adapter_app_id'] ?? 'Steam App ID'); ?>
                                            <input type="text" name="adapter[steam_app_id]" value="<?php echo htmlspecialchars($form['steam_app_id']); ?>" required>
                                        </label>
                                        <label>
                                            <?php echo htmlspecialchars($lang['label_adapter_mods_dir'] ?? 'Mods directory'); ?>
                                            <input type="text" name="adapter[mods_dir]" value="<?php echo htmlspecialchars($form['mods_dir']); ?>" required>
                                        </label>
                                        <label>
                                            <?php echo htmlspecialchars($lang['label_adapter_keys_dir'] ?? 'Keys directory (optional)'); ?>
                                            <input type="text" name="adapter[keys_dir]" value="<?php echo htmlspecialchars($form['keys_dir']); ?>">
                                        </label>
                                        <label class="sw-checkbox">
                                            <input type="checkbox" name="adapter[supports_hot_reload]" value="1" <?php echo !empty($form['supports_hot_reload']) ? 'checked' : ''; ?>>
                                            <span><?php echo htmlspecialchars($lang['label_adapter_hot_reload'] ?? 'Supports hot reload'); ?></span>
                                        </label>
                                    </div>

                                    <label>
                                        <?php echo htmlspecialchars($lang['label_adapter_activation'] ?? 'Activation template'); ?>
                                        <textarea name="adapter[activation_template]" rows="3"><?php echo htmlspecialchars($form['activation_template']); ?></textarea>
                                    </label>

                                    <label>
                                        <?php echo htmlspecialchars($lang['label_adapter_notes'] ?? 'Notes'); ?>
                                        <textarea name="adapter[notes]" rows="2"><?php echo htmlspecialchars($form['notes']); ?></textarea>
                                    </label>

                                    <div class="sw-form__actions">
                                        <button class="btn primary" type="submit"><?php echo htmlspecialchars($lang['button_save_adapter'] ?? 'Save adapter'); ?></button>
                                        <button type="button" class="btn js-toggle-adapter" data-target="<?php echo htmlspecialchars($formId); ?>"><?php echo htmlspecialchars($lang['button_cancel'] ?? 'Cancel'); ?></button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="sw-form__actions sw-admin__mapping-actions">
        <button class="btn primary" type="submit" form="sw-mapping-form"><?php echo htmlspecialchars($lang['button_save']); ?></button>
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
            <?php foreach ((array)$adapters as $adapter): ?>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleRow = function (targetId) {
        const row = document.getElementById(targetId);
        if (!row) {
            return;
        }

        row.classList.toggle('is-open');
        const expanded = row.classList.contains('is-open');

        const toggleButtons = document.querySelectorAll('.js-toggle-adapter[data-target="' + targetId + '"]');
        toggleButtons.forEach(btn => btn.setAttribute('aria-expanded', expanded ? 'true' : 'false'));

        if (expanded) {
            const focusable = row.querySelector('input:not([type="hidden"]), textarea, select');
            if (focusable) {
                focusable.focus();
            }
        }
    };

    document.querySelectorAll('.js-toggle-adapter').forEach(button => {
        button.addEventListener('click', function () {
            const targetId = button.getAttribute('data-target');
            if (targetId) {
                toggleRow(targetId);
            }
        });
    });
});
</script>
