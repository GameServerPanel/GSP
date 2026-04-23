<?php
declare(strict_types=1);
/** @var array $config */
/** @var array $lang */
$mods = $config['workshop_items'] ?? [];
?>
<div class="sw-mods">
    <h4><?php echo htmlspecialchars($lang['mods_table_heading']); ?></h4>
    <?php if (empty($mods)): ?>
        <p><?php echo htmlspecialchars($lang['mods_table_empty']); ?></p>
    <?php else: ?>
        <table class="table sw-mods__table">
            <thead>
                <tr>
                    <th><?php echo htmlspecialchars($lang['mods_header_id']); ?></th>
                    <th><?php echo htmlspecialchars($lang['mods_header_label']); ?></th>
                    <th><?php echo htmlspecialchars($lang['mods_header_source']); ?></th>
                    <th><?php echo htmlspecialchars($lang['mods_header_enabled']); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ((array)$mods as $mod): ?>
                <tr>
                    <td><?php echo htmlspecialchars($mod['id']); ?></td>
                    <td><?php echo htmlspecialchars($mod['label']); ?></td>
                    <td><?php echo htmlspecialchars($mod['source']); ?></td>
                    <td><?php echo !empty($mod['enabled']) ? htmlspecialchars($lang['status_enabled']) : htmlspecialchars($lang['status_disabled']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
