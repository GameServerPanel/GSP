<?php
declare(strict_types=1);
/** @var array $home */
/** @var array $lang */
/** @var int $homeId */
/** @var string $query */
/** @var int $page */
/** @var int $perPage */
/** @var array $results */
/** @var string|null $error */
/** @var array|null $request */
/** @var string|null $requestSummary */
/** @var string|null $appId */

$homeName = htmlspecialchars($home['home_name'] ?? ('#' . $homeId), ENT_QUOTES, 'UTF-8');
$backUrl = '?m=gamemanager&p=game_monitor';
$requestSummaryText = $requestSummary ?? '';
?>
<div class="sw-monitor">
    <p><a class="sw-monitor__back" href="<?php echo $backUrl; ?>">&larr; <?php echo htmlspecialchars($lang['simple_search_back'] ?? 'Back to Game Monitor'); ?></a></p>

    <div class="sw-monitor__card">
        <h3><?php echo htmlspecialchars($lang['simple_search_heading'] ?? 'Steam Workshop quick search'); ?></h3>
        <p class="sw-monitor__intro">
            <?php echo htmlspecialchars(sprintf($lang['simple_search_intro'] ?? 'Look up Workshop mods for %s and copy the IDs into your config.', $homeName)); ?>
        </p>

        <dl class="sw-monitor__meta">
            <div>
                <dt><?php echo htmlspecialchars($lang['simple_search_server'] ?? 'Server'); ?></dt>
                <dd><?php echo $homeName; ?></dd>
            </div>
            <div>
                <dt><?php echo htmlspecialchars($lang['simple_search_app'] ?? 'Steam App ID'); ?></dt>
                <dd><?php echo $appId !== null ? htmlspecialchars($appId, ENT_QUOTES, 'UTF-8') : ($lang['simple_search_app_missing'] ?? 'Not configured'); ?></dd>
            </div>
        </dl>

        <?php if ($appId === null): ?>
            <div class="sw-monitor__alert sw-monitor__alert--error">
                <?php echo htmlspecialchars($lang['simple_search_app_warning'] ?? 'This server is missing a Steam App ID. Ask an administrator to finish the Workshop adapter setup.'); ?>
            </div>
        <?php endif; ?>

        <form method="get" class="sw-monitor__form">
            <input type="hidden" name="m" value="steam_workshop" />
            <input type="hidden" name="p" value="main" />
            <input type="hidden" name="action" value="monitor_search" />
            <input type="hidden" name="home_id" value="<?php echo $homeId; ?>" />
            <label>
                <span><?php echo htmlspecialchars($lang['simple_search_label'] ?? 'Workshop keyword or ID'); ?></span>
                <input type="text" name="q" value="<?php echo htmlspecialchars($query ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?php echo htmlspecialchars($lang['simple_search_placeholder'] ?? 'Example: basebuilding or 2289460122'); ?>" <?php echo $appId === null ? 'disabled' : ''; ?> />
            </label>
            <div class="sw-monitor__form-row">
                <label>
                    <span><?php echo htmlspecialchars($lang['simple_search_page'] ?? 'Page'); ?></span>
                    <input type="number" min="1" name="page" value="<?php echo (int)$page; ?>" <?php echo $appId === null ? 'disabled' : ''; ?> />
                </label>
                <label>
                    <span><?php echo htmlspecialchars($lang['simple_search_per_page'] ?? 'Items per page'); ?></span>
                    <input type="number" min="1" max="100" name="per_page" value="<?php echo (int)$perPage; ?>" <?php echo $appId === null ? 'disabled' : ''; ?> />
                </label>
            </div>
            <button type="submit" class="btn primary" <?php echo $appId === null ? 'disabled' : ''; ?>><?php echo htmlspecialchars($lang['simple_search_submit'] ?? 'Search Workshop'); ?></button>
        </form>

        <?php if ($requestSummaryText !== ''): ?>
            <div class="sw-monitor__summary">
                <strong><?php echo htmlspecialchars($lang['simple_search_request_label'] ?? 'Request summary'); ?>:</strong>
                <div class="sw-monitor__summary-text"><?php echo htmlspecialchars($requestSummaryText); ?></div>
            </div>
        <?php endif; ?>

        <?php if ($error !== null): ?>
            <div class="sw-monitor__alert sw-monitor__alert--error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($query !== ''): ?>
            <?php if (!empty($results)): ?>
                <div class="sw-monitor__results">
                    <h4><?php echo htmlspecialchars($lang['simple_search_results'] ?? 'Matching Workshop items'); ?></h4>
                    <p class="sw-monitor__hint"><?php echo htmlspecialchars($lang['simple_search_copy_hint'] ?? 'Copy the Workshop ID for each mod you want to add.'); ?></p>
                    <div class="sw-monitor__table-wrapper">
                        <table class="sw-monitor__table">
                            <thead>
                                <tr>
                                    <th><?php echo htmlspecialchars($lang['mods_header_id'] ?? 'Workshop ID'); ?></th>
                                    <th><?php echo htmlspecialchars($lang['mods_header_label'] ?? 'Title'); ?></th>
                                    <th><?php echo htmlspecialchars($lang['mods_header_source'] ?? 'Source'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $item): ?>
                                    <?php
                                        $itemId = htmlspecialchars($item['id'] ?? '', ENT_QUOTES, 'UTF-8');
                                        $label = htmlspecialchars($item['label'] ?? ('@' . $itemId), ENT_QUOTES, 'UTF-8');
                                        $source = htmlspecialchars($item['source'] ?? 'search', ENT_QUOTES, 'UTF-8');
                                    ?>
                                    <tr>
                                        <td>
                                            <code><?php echo $itemId; ?></code>
                                        </td>
                                        <td><?php echo $label; ?></td>
                                        <td><?php echo $source; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php elseif ($error === null): ?>
                <div class="sw-monitor__alert sw-monitor__alert--info">
                    <?php echo htmlspecialchars($lang['simple_search_empty'] ?? 'No Workshop items matched that search.'); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
