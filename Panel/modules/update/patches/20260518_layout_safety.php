<?php

if (!function_exists('gsp_patch_20260518_layout_safety')) {
function gsp_patch_20260518_layout_safety($context)
{
$required_dirs = [
GSP_ROOT_DIR,
GSP_PANEL_DIR,
GSP_WEBSITE_DIR,
GSP_ROOT_DIR . '/examples',
GSP_ROOT_DIR . '/backups',
GSP_ROOT_DIR . '/logs',
GSP_ROOT_DIR . '/includes',
];

foreach ($required_dirs as $dir) {
if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
return [
'success' => false,
'message' => 'Failed to create required layout directory: ' . $dir,
];
}
if (!is_writable($dir)) {
return [
'success' => false,
'message' => 'Required layout directory is not writable: ' . $dir,
];
}
}

return [
'success' => true,
'message' => 'Canonical GSP layout directories verified.',
];
}
}

return [
'id' => '20260518_layout_safety',
'title' => 'Ensure canonical GSP layout directories are present and writable',
'required' => true,
'handler' => 'gsp_patch_20260518_layout_safety',
];
