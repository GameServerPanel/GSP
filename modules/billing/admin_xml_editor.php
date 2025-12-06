<?php
require_once(__DIR__ . '/includes/admin_auth.php');
require_once(__DIR__ . '/includes/config_loader.php');
include(__DIR__ . '/includes/top.php');
include(__DIR__ . '/includes/menu.php');

$serverConfigDir = realpath(__DIR__ . '/../config_games/server_configs');
if ($serverConfigDir === false || !is_dir($serverConfigDir)) {
    die('Server config directory not found.');
}

$messages = [];
$errors = [];

$availableFiles = [];
$directoryIterator = new DirectoryIterator($serverConfigDir);
foreach ($directoryIterator as $fileInfo) {
    if ($fileInfo->isFile() && strtolower($fileInfo->getExtension()) === 'xml') {
        $availableFiles[] = $fileInfo->getFilename();
    }
}
sort($availableFiles, SORT_NATURAL | SORT_FLAG_CASE);

$selectedFile = '';
$fileContents = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedFile = $_POST['file'] ?? '';
    $postedFile = basename(trim((string)$postedFile));
    if ($postedFile === '' || !in_array($postedFile, $availableFiles, true)) {
        $errors[] = 'Invalid file selected.';
    } else {
        $fullPath = $serverConfigDir . DIRECTORY_SEPARATOR . $postedFile;
        if (!is_file($fullPath) || !is_readable($fullPath)) {
            $errors[] = 'Selected file is missing or unreadable.';
        } elseif (!is_writable($fullPath)) {
            $errors[] = 'Selected file is not writable.';
        } else {
            $newContents = $_POST['xml_contents'] ?? '';
            $backupDir = $serverConfigDir . DIRECTORY_SEPARATOR . '_backups';
            if (!is_dir($backupDir)) {
                @mkdir($backupDir, 0775, true);
            }
            $timestamp = date('Ymd-His');
            $backupPath = $backupDir . DIRECTORY_SEPARATOR . $postedFile . '.' . $timestamp . '.bak';
            $original = file_get_contents($fullPath);
            if ($original === false) {
                $errors[] = 'Unable to read original file for backup.';
            } elseif (@file_put_contents($backupPath, $original) === false) {
                $errors[] = 'Failed to create backup copy before saving.';
            } elseif (@file_put_contents($fullPath, $newContents) === false) {
                $errors[] = 'Failed to write new XML contents.';
            } else {
                $messages[] = 'Saved changes to ' . htmlspecialchars($postedFile, ENT_QUOTES, 'UTF-8') . ' (backup: ' . basename($backupPath) . ').';
                $selectedFile = $postedFile;
                $fileContents = $newContents;
            }
        }
    }
}

if ($selectedFile === '') {
    $queryFile = $_GET['file'] ?? '';
    $queryFile = basename(trim((string)$queryFile));
    if ($queryFile !== '' && in_array($queryFile, $availableFiles, true)) {
        $selectedFile = $queryFile;
    }
}

if ($selectedFile !== '' && $fileContents === '') {
    $fullPath = $serverConfigDir . DIRECTORY_SEPARATOR . $selectedFile;
    if (is_file($fullPath) && is_readable($fullPath)) {
        $fileContents = file_get_contents($fullPath);
        if ($fileContents === false) {
            $errors[] = 'Unable to read the selected file.';
            $fileContents = '';
        }
    } else {
        $errors[] = 'Selected file is missing or unreadable.';
        $selectedFile = '';
    }
}

function billing_render_flash(array $items, string $cssClass): void {
    if (!$items) {
        return;
    }
    echo '<div class="panel ' . $cssClass . '" style="margin-bottom:12px">';
    foreach ($items as $item) {
        echo '<div>' . $item . '</div>';
    }
    echo '</div>';
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin — XML Config Editor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/header.css">
    <style>
        .editor-wrapper { max-width: 1100px; margin: 30px auto; background: rgba(0,0,0,0.6); padding: 24px; border-radius: 10px; }
        .editor-wrapper h1 { margin-top: 0; color: #fff; }
        .editor-layout { display: flex; flex-wrap: wrap; gap: 20px; }
        .file-list { flex: 1 1 240px; max-height: 520px; overflow-y: auto; background: rgba(0,0,0,0.35); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 16px; }
        .file-list h2 { margin-top: 0; font-size: 1rem; color: #a5b4fc; }
        .file-list a { display: block; color: #7fb3ff; text-decoration: none; padding: 6px 4px; border-radius: 6px; }
        .file-list a:hover { background: rgba(102, 126, 234, 0.25); }
        .file-list a.active { background: rgba(102, 126, 234, 0.45); color: #fff; }
        .editor-form { flex: 3 1 500px; }
        textarea { width: 100%; min-height: 480px; font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace; font-size: 14px; line-height: 1.4; padding: 12px; color: #e5e7eb; background: rgba(15, 23, 42, 0.85); border: 1px solid rgba(148, 163, 184, 0.4); border-radius: 8px; }
        textarea:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.35); }
        .editor-actions { margin-top: 16px; display: flex; gap: 12px; align-items: center; }
        .editor-actions button { padding: 10px 18px; border: none; border-radius: 6px; background: #667eea; color: #fff; font-weight: 600; cursor: pointer; }
        .editor-actions button:hover { background: #5563d6; }
        .hint { color: #cbd5f5; font-size: 0.85rem; }
        .panel.error div { color: #f87171; }
        .panel.success div { color: #34d399; }
    </style>
</head>
<body>
<div class="editor-wrapper">
    <h1>XML Config Editor</h1>
    <p class="hint">Editing files in <code><?php echo htmlspecialchars($serverConfigDir, ENT_QUOTES, 'UTF-8'); ?></code>. Each save creates a backup under <code>_backups/</code>.</p>

    <?php billing_render_flash($messages, 'success'); ?>
    <?php billing_render_flash($errors, 'error'); ?>

    <div class="editor-layout">
        <div class="file-list">
            <h2>Server Config XML Files</h2>
            <?php if (!$availableFiles): ?>
                <p style="color:#e5e7eb;">No XML files found.</p>
            <?php else: ?>
                <?php foreach ($availableFiles as $fileName): ?>
                    <?php $isActive = ($fileName === $selectedFile); ?>
                    <a href="admin_xml_editor.php?file=<?php echo urlencode($fileName); ?>" class="<?php echo $isActive ? 'active' : ''; ?>"><?php echo htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8'); ?></a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="editor-form">
            <?php if ($selectedFile === ''): ?>
                <p style="color:#e5e7eb;">Select an XML file from the list to begin editing.</p>
            <?php else: ?>
                <form method="post" action="admin_xml_editor.php">
                    <input type="hidden" name="file" value="<?php echo htmlspecialchars($selectedFile, ENT_QUOTES, 'UTF-8'); ?>">
                    <textarea name="xml_contents" spellcheck="false"><?php echo htmlspecialchars($fileContents, ENT_QUOTES, 'UTF-8'); ?></textarea>
                    <div class="editor-actions">
                        <button type="submit">Save Changes</button>
                        <span class="hint">Backup created before each save.</span>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</body>
</html>
