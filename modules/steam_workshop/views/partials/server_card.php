<?php
declare(strict_types=1);
/** @var array $currentRecord */
/** @var array $lang */
$home = $currentRecord['home'];
$config = $currentRecord['config'];
$adapter = $currentRecord['adapter'];
$homeName = htmlspecialchars($home['home_name'] ?? ('#' . $home['home_id']));
$homeId = (int)($home['home_id'] ?? 0);
$modCount = count((array)$config['workshop_items']);
$interval = (int)$config['update_interval_minutes'];
$enabled = !empty($config['workshop_enabled']);
$lastSaved = $config['last_saved_at'] ? date('Y-m-d H:i', (int)$config['last_saved_at']) : '—';
$adapterName = htmlspecialchars($adapter['name'] ?? strtoupper($config['adapter_key']));
$hotReload = !empty($adapter['supports_hot_reload']);
$ip = htmlspecialchars($home['ip'] ?? '');
$port = $home['port'] ?? '';
$address = $ip;
if ($ip !== '' && $port !== '') {
    $address .= ':' . htmlspecialchars((string)$port);
}
?>
<div class="sw-card">
    <div class="sw-card__header">
        <div>
            <h3><?php echo $homeName; ?></h3>
            <?php if ($address !== ''): ?>
                <p><?php echo $address; ?></p>
            <?php endif; ?>
        </div>
        <div>
            <a class="btn" href="?m=steam_workshop&amp;p=main&amp;action=edit&amp;home_id=<?php echo $homeId; ?>">
                <?php echo htmlspecialchars($lang['button_edit']); ?>
            </a>
        </div>
    </div>
    <dl class="sw-card__meta">
        <div>
            <dt><?php echo htmlspecialchars($lang['summary_adapter']); ?></dt>
            <dd><?php echo $adapterName; ?></dd>
        </div>
        <div>
            <dt><?php echo htmlspecialchars($lang['summary_interval']); ?></dt>
            <dd><?php echo $interval; ?> min</dd>
        </div>
        <div>
            <dt><?php echo htmlspecialchars($lang['summary_mods']); ?></dt>
            <dd><?php echo $modCount; ?></dd>
        </div>
        <div>
            <dt><?php echo htmlspecialchars($lang['summary_last_saved']); ?></dt>
            <dd><?php echo htmlspecialchars($lastSaved); ?></dd>
        </div>
        <div>
            <dt>Status</dt>
            <dd><?php echo $enabled ? htmlspecialchars($lang['status_enabled']) : htmlspecialchars($lang['status_disabled']); ?></dd>
        </div>
        <div>
            <dt><?php echo htmlspecialchars($lang['summary_hot_reload']); ?></dt>
            <dd><?php echo $hotReload ? htmlspecialchars($lang['status_hot_reload']) : htmlspecialchars($lang['status_restart_required']); ?></dd>
        </div>
    </dl>
</div>
