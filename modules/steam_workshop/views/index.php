<?php
declare(strict_types=1);
/** @var array $records */
/** @var array $lang */
/** @var bool $isAdmin */
/** @var array $adapterOptions */
?>
<div class="sw-admin sw-index">
    <?php if (empty($records)): ?>
        <div class="sw-empty">
            <p><?php echo $isAdmin ? htmlspecialchars($lang['empty_state_admin']) : htmlspecialchars($lang['empty_state_user']); ?></p>
        </div>
    <?php else: ?>
        <div class="sw-grid">
            <?php foreach ((array)$records as $record): ?>
                <?php $currentRecord = $record; include __DIR__ . '/partials/server_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
