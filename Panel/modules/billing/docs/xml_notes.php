<?php
require_once(__DIR__ . '/../includes/admin_auth.php');
require_once(__DIR__ . '/../includes/config_loader.php');
include(__DIR__ . '/../includes/top.php');
include(__DIR__ . '/../includes/menu.php');

$sourceFile = __DIR__ . '/XML-Notes.md';
$markdown = file_exists($sourceFile) ? file_get_contents($sourceFile) : 'Source file missing.';

function billing_render_markdown($markdown)
{
    $markdown = str_replace("\r\n", "\n", (string)$markdown);
    $lines = explode("\n", $markdown);
    $html = '';
    $inCode = false;
    $inList = false;
    foreach ((array)$lines as $line) {
        $trim = trim($line);
        if ($trim === '```') {
            if ($inCode) {
                $html .= "</code></pre>\n";
                $inCode = false;
            } else {
                if ($inList) {
                    $html .= "</ul>\n";
                    $inList = false;
                }
                $html .= "<pre class=\"code-block\"><code>";
                $inCode = true;
            }
            continue;
        }
        if ($inCode) {
            $html .= htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . "\n";
            continue;
        }
        if ($trim === '___') {
            if ($inList) {
                $html .= "</ul>\n";
                $inList = false;
            }
            $html .= "<hr>\n";
            continue;
        }
        if (preg_match('/^(#{2,6})\s+(.*)$/', $line, $m)) {
            if ($inList) {
                $html .= "</ul>\n";
                $inList = false;
            }
            $level = strlen($m[1]);
            $text = htmlspecialchars($m[2], ENT_QUOTES, 'UTF-8');
            $html .= "<h{$level}>{$text}</h{$level}>\n";
            continue;
        }
        if (strpos($trim, '- ') === 0) {
            if (!$inList) {
                $html .= "<ul>\n";
                $inList = true;
            }
            $item = htmlspecialchars(substr($trim, 2), ENT_QUOTES, 'UTF-8');
            $item = preg_replace('/`([^`]+)`/', '<code>$1</code>', $item);
            $html .= "<li>{$item}</li>\n";
            continue;
        }
        if ($trim === '') {
            if ($inList) {
                $html .= "</ul>\n";
                $inList = false;
            }
            $html .= "<p></p>\n";
            continue;
        }
        if ($inList) {
            $html .= "</ul>\n";
            $inList = false;
        }
        $lineHtml = htmlspecialchars($line, ENT_QUOTES, 'UTF-8');
        $lineHtml = preg_replace('/`([^`]+)`/', '<code>$1</code>', $lineHtml);
        $html .= "<p>{$lineHtml}</p>\n";
    }
    if ($inList) {
        $html .= "</ul>\n";
    }
    if ($inCode) {
        $html .= "</code></pre>\n";
    }
    return $html;
}

$docHtml = billing_render_markdown($markdown);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>XML Configuration Notes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/header.css">
    <style>
        .doc-wrapper {
            max-width: 960px;
            margin: 0 auto;
            padding: 30px;
            background: rgba(0,0,0,0.6);
            border-radius: 8px;
            line-height: 1.5;
        }
        .doc-wrapper h2, .doc-wrapper h3, .doc-wrapper h4 {
            margin-top: 24px;
            color: #fff;
        }
        .doc-wrapper p {
            color: #e3e3e3;
        }
        .doc-wrapper code {
            background: rgba(255,255,255,0.08);
            padding: 2px 4px;
            border-radius: 4px;
        }
        pre.code-block {
            background: rgba(0,0,0,0.85);
            color: #8ef0ff;
            padding: 12px;
            overflow-x: auto;
            border-radius: 6px;
        }
        ul {
            margin-left: 20px;
            color: #e3e3e3;
        }
    </style>
</head>
<body>
<div class="container-wide panel">
    <h1>Game Config XML Reference</h1>
    <p>
        This page mirrors the <a href="https://github.com/OpenGamePanel/OGP-Website/wiki/XML-Notes" target="_blank" rel="noopener noreferrer">
        OGP XML Notes</a> so we can edit and review configuration expectations directly inside the repo.
        Update <code>modules/billing/docs/XML-Notes.md</code> whenever the upstream wiki changes.
    </p>
    <div class="doc-wrapper">
        <?php echo $docHtml; ?>
    </div>
</div>
<?php include(__DIR__ . '/../includes/footer.php'); ?>
</body>
</html>
