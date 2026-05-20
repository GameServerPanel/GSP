<?php

if (!function_exists('gsp_patch_001_layout_bootstrap')) {
function gsp_patch_001_layout_bootstrap($context)
{
$created = [];
$targets = [
!empty($context['root_dir']) ? $context['root_dir'] . '/logs' : null,
!empty($context['root_dir']) ? $context['root_dir'] . '/backups' : null,
!empty($context['root_dir']) ? $context['root_dir'] . '/examples' : null,
!empty($context['panel_dir']) ? $context['panel_dir'] : null,
!empty($context['website_dir']) ? $context['website_dir'] : null,
];
foreach ($targets as $dir) {
if (empty($dir)) {
continue;
}
if (!is_dir($dir)) {
if (!@mkdir($dir, 0755, true) && !is_dir($dir)) {
return ['success' => false, 'details' => 'Failed to create directory: ' . $dir];
}
$created[] = $dir;
}
}
$details = empty($created)
? 'Layout already in expected state.'
: 'Created directories: ' . implode(', ', $created);
return ['success' => true, 'details' => $details];
}
}

return [
'id' => '001_layout_bootstrap',
'title' => 'Ensure baseline GSP root directories exist',
'runner' => 'gsp_patch_001_layout_bootstrap',
];
