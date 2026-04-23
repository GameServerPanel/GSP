<?php
declare(strict_types=1);
/** @var array $formConfig */
/** @var array $adapterOptions */
/** @var array $lang */
/** @var bool $adapterLocked */
/** @var bool $isAdmin */
$enabled = !empty($formConfig['workshop_enabled']);
$interval = (int)$formConfig['update_interval_minutes'];
$stagingDir = htmlspecialchars($formConfig['staging_dir']);
$postInstall = htmlspecialchars($formConfig['post_install_script']);
$rawDefinition = htmlspecialchars($formConfig['raw_definition']);
$installStrategy = $formConfig['install_strategy'];
$onUpdateAction = $formConfig['on_update_action'];
$currentAdapterName = $adapterOptions[$formConfig['adapter_key']] ?? strtoupper($formConfig['adapter_key']);
?>
<div class="sw-form__grid">
    <label class="sw-toggle">
        <input type="checkbox" name="workshop[workshop_enabled]" value="1" <?php echo $enabled ? 'checked' : ''; ?> />
        <span><?php echo htmlspecialchars($lang['label_feature_flag']); ?></span>
    </label>

    <label>
        <span><?php echo htmlspecialchars($lang['label_adapter']); ?></span>
        <?php if ($adapterLocked): ?>
            <input type="text" value="<?php echo htmlspecialchars($currentAdapterName); ?>" disabled />
            <small><?php echo htmlspecialchars($lang['adapter_locked_note'] ?? 'This adapter is managed by the administrator.'); ?></small>
        <?php else: ?>
            <select name="workshop[adapter_key]">
                <?php foreach ((array)$adapterOptions as $key => $label): ?>
                    <option value="<?php echo htmlspecialchars($key); ?>" <?php echo $formConfig['adapter_key'] === $key ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </label>

    <label>
        <span><?php echo htmlspecialchars($lang['label_interval']); ?></span>
        <input type="number" min="15" max="360" step="5" name="workshop[update_interval_minutes]" value="<?php echo $interval; ?>" />
        <small><?php echo htmlspecialchars($lang['label_interval_hint']); ?></small>
    </label>

    <input type="hidden" name="workshop[staging_dir]" value="<?php echo $stagingDir; ?>" />

    <input type="hidden" name="workshop[install_strategy]" value="<?php echo htmlspecialchars($installStrategy); ?>" />

    <label>
        <span><?php echo htmlspecialchars($lang['label_on_update_action']); ?></span>
        <select name="workshop[on_update_action]">
            <option value="queue_for_restart" <?php echo $onUpdateAction === 'queue_for_restart' ? 'selected' : ''; ?>><?php echo htmlspecialchars($lang['action_queue_for_restart']); ?></option>
            <option value="hot_reload_if_supported" <?php echo $onUpdateAction === 'hot_reload_if_supported' ? 'selected' : ''; ?>><?php echo htmlspecialchars($lang['action_hot_reload_if_supported']); ?></option>
        </select>
    </label>

    <input type="hidden" name="workshop[post_install_script]" value="<?php echo $postInstall; ?>" />
</div>

<?php if ($isAdmin): ?>
    <label>
        <span><?php echo htmlspecialchars($lang['label_mod_import']); ?></span>
        <textarea name="workshop[raw_items]" rows="8" placeholder="123456789,@Example Mod&#10;987654321,@QoL Pack"><?php echo $rawDefinition; ?></textarea>
        <small><?php echo htmlspecialchars($lang['hint_mod_import']); ?></small>
    </label>
<?php else: ?>
    <input type="hidden" name="workshop[raw_items]" value="<?php echo $rawDefinition; ?>" />
<?php endif; ?>
