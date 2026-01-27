<?php
declare(strict_types=1);
/** @var array $lang */
/** @var array $config */
/** @var array $home */
/** @var int $homeId */
$homeId = (int)($home['home_id'] ?? 0);
$scriptPath = (string)($_SERVER['PHP_SELF'] ?? '/index.php');
if ($scriptPath === '') {
    $scriptPath = '/index.php';
}
if ($scriptPath[0] !== '/') {
    $scriptPath = '/' . ltrim($scriptPath, '/');
}
$endpoint = sprintf('%s?m=steam_workshop&p=main&action=search&home_id=%d', $scriptPath, $homeId);
$initialItems = [];
foreach ($config['workshop_items'] ?? [] as $item) {
    if (!is_array($item)) {
        continue;
    }
    $id = preg_replace('/[^0-9]/', '', (string)($item['id'] ?? ''));
    if ($id === '') {
        continue;
    }
    $initialItems[] = [
        'id' => $id,
        'label' => (string)($item['label'] ?? ('@' . $id)),
        'author' => (string)($item['author'] ?? ''),
        'preview_url' => (string)($item['preview_url'] ?? ''),
        'enabled' => !empty($item['enabled']),
        'source' => (string)($item['source'] ?? 'manual'),
    ];
}
$initialJson = htmlspecialchars(json_encode($initialItems, JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
$pickerId = 'sw-picker-' . $homeId;
$langAttrs = [
    'add' => $lang['mod_picker_action_add'] ?? 'Add',
    'remove' => $lang['mod_picker_action_remove'] ?? 'Remove',
    'loading' => $lang['mod_picker_status_loading'] ?? 'Searching Steam Workshop…',
    'error' => $lang['mod_picker_status_error'] ?? 'Unable to load workshop data.',
    'empty' => $lang['mod_picker_results_empty'] ?? 'No results matched your search.',
    'query' => $lang['mod_picker_status_need_query'] ?? 'Enter a Workshop ID or keyword.',
    'sync' => $lang['mod_picker_toggle_label'] ?? 'Sync',
];
?>
<div class="sw-picker" id="<?php echo $pickerId; ?>" data-endpoint="<?php echo htmlspecialchars($endpoint, ENT_QUOTES, 'UTF-8'); ?>"
    <?php foreach ($langAttrs as $key => $value): ?>data-lang-<?php echo $key; ?>="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" <?php endforeach; ?>>
    <div class="sw-picker__header">
        <h4><?php echo htmlspecialchars($lang['mod_picker_heading'] ?? 'Workshop library'); ?></h4>
        <p class="sw-picker__hint"><?php echo htmlspecialchars($lang['mod_picker_hint'] ?? 'Search by Workshop ID or keyword to add mods.'); ?></p>
    </div>

    <div class="sw-picker__search js-sw-search-form" data-home-id="<?php echo $homeId; ?>" role="search">
        <label>
            <span><?php echo htmlspecialchars($lang['mod_picker_search_label'] ?? 'Search Steam Workshop'); ?></span>
            <input type="text" class="sw-picker__search-input js-sw-search-input" placeholder="<?php echo htmlspecialchars($lang['mod_picker_search_placeholder'] ?? 'Example: 221100 or QoL'); ?>" />
        </label>
        <button type="button" class="btn secondary js-sw-search-button"><?php echo htmlspecialchars($lang['mod_picker_search_button'] ?? 'Search'); ?></button>
    </div>

    <div class="sw-picker__request-row">
        <span class="sw-picker__request-label"><?php echo htmlspecialchars($lang['mod_picker_request_label'] ?? 'Submitting request'); ?></span>
        <small class="sw-picker__request-hint"><?php echo htmlspecialchars($lang['mod_picker_request_hint'] ?? 'Exact URL preview. The field below mirrors your search text.'); ?></small>
        <div class="sw-picker__request-line">
            <?php $baseRequest = $endpoint . '&q='; ?>
            <code class="sw-picker__request-summary js-sw-request-summary" data-base="<?php echo htmlspecialchars($baseRequest, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($baseRequest, ENT_QUOTES, 'UTF-8'); ?></code>
            <input type="text" class="sw-picker__request-input js-sw-request-input" value="" readonly aria-label="<?php echo htmlspecialchars($lang['mod_picker_request_input_label'] ?? 'Workshop search text preview'); ?>" />
        </div>
    </div>

    <div class="sw-picker__status js-sw-picker-status" role="status" aria-live="polite"></div>

    <div class="sw-picker__selected">
        <div class="sw-picker__selected-header">
            <h5><?php echo htmlspecialchars($lang['mod_picker_selected_heading'] ?? 'Selected mods'); ?></h5>
            <small><?php echo htmlspecialchars($lang['mod_picker_selected_hint'] ?? 'Checked mods will stay synced automatically.'); ?></small>
        </div>
        <div class="sw-picker__chip-list js-sw-selected-list" data-empty-text="<?php echo htmlspecialchars($lang['mod_picker_selected_empty'] ?? 'No mods selected yet.'); ?>"></div>
    </div>

    <div class="sw-picker__results">
        <h5><?php echo htmlspecialchars($lang['mod_picker_results_heading'] ?? 'Search results'); ?></h5>
        <div class="sw-picker__results-table-wrapper">
            <table class="sw-picker__results-table">
                <thead>
                    <tr>
                        <th><?php echo htmlspecialchars($lang['mod_picker_results_select'] ?? 'Select'); ?></th>
                        <th><?php echo htmlspecialchars($lang['mod_picker_results_title'] ?? 'Title'); ?></th>
                        <th><?php echo htmlspecialchars($lang['mod_picker_results_author'] ?? 'Author'); ?></th>
                    </tr>
                </thead>
                <tbody class="js-sw-results"></tbody>
            </table>
        </div>
    </div>

    <input type="hidden" name="workshop[selected_items]" value="<?php echo $initialJson; ?>" class="js-sw-selected-input" />
</div>
