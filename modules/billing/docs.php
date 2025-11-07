<?php
/**
 * Documentation Browser
 * Displays a list of documentation categories and allows viewing individual docs
 */

// Start session for navigation state
session_start();

// Include config
require_once(__DIR__ . '/includes/config.inc.php');

// Set the docs directory
$docsDir = __DIR__ . '/docs';

// Get action and doc parameters
$action = $_GET['action'] ?? 'list';
$doc = $_GET['doc'] ?? '';

/**
 * Get all documentation folders with their metadata
 */
function getDocCategories($docsDir) {
    $categories = [];
    
    if (!is_dir($docsDir)) {
        return $categories;
    }
    
    $folders = array_diff(scandir($docsDir), ['.', '..']);
    
    foreach ($folders as $folder) {
        $folderPath = $docsDir . '/' . $folder;
        
        // Skip if not a directory
        if (!is_dir($folderPath)) {
            continue;
        }
        
        // Check for required files
        $indexPath = $folderPath . '/index.php';
        $metadataPath = $folderPath . '/metadata.json';
        
        if (!file_exists($indexPath) || !file_exists($metadataPath)) {
            continue;
        }
        
        // Read metadata
        $metadata = json_decode(file_get_contents($metadataPath), true);
        if (!$metadata) {
            $metadata = [];
        }
        
        // Find icon file
        $icon = '';
        if (file_exists($folderPath . '/icon.png')) {
            $icon = 'docs/' . $folder . '/icon.png';
        } elseif (file_exists($folderPath . '/icon.jpg')) {
            $icon = 'docs/' . $folder . '/icon.jpg';
        }
        
        $categories[] = [
            'folder' => $folder,
            'name' => $metadata['name'] ?? ucfirst($folder),
            'description' => $metadata['description'] ?? '',
            'category' => $metadata['category'] ?? 'other',
            'order' => $metadata['order'] ?? 999,
            'icon' => $icon
        ];
    }
    
    // Sort by category, then order, then name
    usort($categories, function($a, $b) {
        if ($a['category'] !== $b['category']) {
            return strcmp($a['category'], $b['category']);
        }
        if ($a['order'] !== $b['order']) {
            return $a['order'] - $b['order'];
        }
        return strcmp($a['name'], $b['name']);
    });
    
    return $categories;
}

// Get all categories
$categories = getDocCategories($docsDir);

// Group by category
$grouped = [];
foreach ($categories as $cat) {
    $category = $cat['category'];
    if (!isset($grouped[$category])) {
        $grouped[$category] = [];
    }
    $grouped[$category][] = $cat;
}

// Category labels
$categoryLabels = [
    'game' => 'Game Servers',
    'panel' => 'Panel Documentation',
    'mods' => 'Mods & Addons',
    'troubleshooting' => 'Troubleshooting',
    'other' => 'Other'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $action === 'view' ? 'Documentation' : 'Documentation - GameServers.World'; ?></title>
    <link rel="stylesheet" href="css/header.css">
    <style>
        :root {
            --bg: #0f172a;
            --card: #111827;
            --text: #e5e7eb;
            --muted: #94a3b8;
            --accent: #38bdf8;
            --border: #1f2937;
        }
        
        body {
            background: var(--bg);
            color: var(--text);
            font: 16px/1.5 system-ui, -apple-system, Segoe UI, Roboto, Arial;
            margin: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .header {
            margin-bottom: 40px;
        }
        
        .header h1 {
            font-size: 32px;
            margin: 0 0 12px;
        }
        
        .header p {
            color: var(--muted);
            margin: 0;
        }
        
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--accent);
            text-decoration: none;
            margin-bottom: 20px;
            transition: all 0.2s;
        }
        
        .back-button:hover {
            background: #1f2937;
            border-color: var(--accent);
        }
        
        .category-section {
            margin-bottom: 40px;
        }
        
        .category-title {
            font-size: 24px;
            color: var(--accent);
            margin: 0 0 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border);
        }
        
        .docs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .doc-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.2s;
            text-decoration: none;
            display: flex;
            flex-direction: column;
        }
        
        .doc-card:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(56, 189, 248, 0.1);
        }
        
        .doc-icon-wrapper {
            width: 60px;
            height: 60px;
            background: #1f2937;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .doc-icon {
            max-width: 100%;
            max-height: 100%;
            border-radius: 6px;
        }
        
        .doc-icon-placeholder {
            font-size: 28px;
            color: var(--muted);
        }
        
        .doc-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            margin: 0 0 8px;
        }
        
        .doc-description {
            font-size: 14px;
            color: var(--muted);
            margin: 0;
            flex-grow: 1;
        }
        
        .doc-view-container {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 30px;
            min-height: 400px;
        }
        
        .doc-view-container h1,
        .doc-view-container h2,
        .doc-view-container h3,
        .doc-view-container h4 {
            color: var(--text);
        }
        
        .doc-view-container a {
            color: var(--accent);
        }
        
        .doc-view-container code {
            background: #1f2937;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
        
        .doc-view-container pre {
            background: #1f2937;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
        }
        
        .doc-view-container pre code {
            background: none;
            padding: 0;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/includes/menu.php'); ?>
    
    <div class="container">
        <?php if ($action === 'view' && !empty($doc)): ?>
            <!-- View specific documentation -->
            <a href="docs.php" class="back-button">← Back to Documentation List</a>
            
            <div class="doc-view-container">
                <?php
                // Sanitize doc parameter to prevent directory traversal
                $doc = basename($doc);
                $docPath = $docsDir . '/' . $doc . '/index.php';
                
                if (file_exists($docPath)) {
                    include($docPath);
                } else {
                    echo '<p style="color: #ef4444;">Documentation not found.</p>';
                }
                ?>
            </div>
            
        <?php else: ?>
            <!-- List all documentation categories -->
            <div class="header">
                <h1>Documentation</h1>
                <p>Browse our comprehensive documentation for game servers, panel features, and troubleshooting guides.</p>
            </div>
            
            <?php if (empty($grouped)): ?>
                <div class="doc-view-container">
                    <p>No documentation available yet. Documentation folders should contain:</p>
                    <ul>
                        <li><code>index.php</code> - The documentation content</li>
                        <li><code>metadata.json</code> - Category and ordering information</li>
                        <li><code>icon.png</code> or <code>icon.jpg</code> - Category icon</li>
                    </ul>
                </div>
            <?php else: ?>
                <?php foreach ($grouped as $category => $docs): ?>
                    <div class="category-section">
                        <h2 class="category-title"><?php echo htmlspecialchars($categoryLabels[$category] ?? ucfirst($category)); ?></h2>
                        
                        <div class="docs-grid">
                            <?php foreach ($docs as $doc): ?>
                                <a href="docs.php?action=view&doc=<?php echo urlencode($doc['folder']); ?>" class="doc-card">
                                    <div class="doc-icon-wrapper">
                                        <?php if (!empty($doc['icon'])): ?>
                                            <img src="<?php echo htmlspecialchars($doc['icon']); ?>" alt="" class="doc-icon">
                                        <?php else: ?>
                                            <span class="doc-icon-placeholder">📄</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h3 class="doc-title"><?php echo htmlspecialchars($doc['name']); ?></h3>
                                    <?php if (!empty($doc['description'])): ?>
                                        <p class="doc-description"><?php echo htmlspecialchars($doc['description']); ?></p>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
