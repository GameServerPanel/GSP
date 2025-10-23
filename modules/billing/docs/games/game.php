<?php
// game.php — renders a single Markdown doc and shows a Print / Save PDF button.
$g = $_GET['g'] ?? '';
$filenameOnly = pathinfo($g, PATHINFO_FILENAME); // strips .md or any extension
$slug = preg_replace('/[^a-z0-9\-]/', '', strtolower($filenameOnly));

$path = __DIR__ . "/{$slug}.md";


if (!$slug || !file_exists($path)) {
  http_response_code(404);
  echo "Guide not found.";
  exit;
}
require_once __dir__ . '/Parsedown.php'; // single-file Markdown parser (drop into repo root)
$md = file_get_contents($path);
$Parsedown = new Parsedown();
$html = $Parsedown->text($md);
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo htmlspecialchars($slug); ?> — Game Guide</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  :root { --bg:#0f172a; --paper:#ffffff; --text:#111827; --ink:#111827; }
  body { margin:0; background:var(--bg); }
  .toolbar { position:sticky; top:0; background:#0b1220; border-bottom:1px solid #1f2937; padding:10px 16px; display:flex; gap:8px; align-items:center; }
  .btn { border:1px solid #1f2937; background:#111827; color:#e5e7eb; padding:8px 12px; border-radius:10px; cursor:pointer; }
  .btn:hover { opacity:.9; }
  .wrap { max-width: 900px; margin: 18px auto 48px; background:var(--paper); color:var(--text); padding:28px; border-radius:14px; }
  .wrap h1,h2,h3 { color:var(--ink); }
  pre, code { background:#f3f4f6; border-radius:8px; padding:2px 6px; }
  pre { padding:12px; overflow:auto; }
  @media print {
    body { background:#fff; }
    .toolbar { display:none !important; }
    .wrap { margin:0; max-width:100%; border-radius:0; padding:24mm; }
    a[href]:after { content:" (" attr(href) ")"; font-size:10pt; }
    h1 { page-break-before: always; }
    h2 { page-break-after: avoid; }
    pre { page-break-inside: avoid; }
  }
</style>
</head>
<body>
<div class="toolbar">
  <button class="btn" onclick="window.print()">Print / Save PDF</button>
  <a class="btn" href="<?php echo 'https://github.com/Gameservers-World/ControlPanel/tree/main/docs/games/'.rawurlencode($slug).'.md'; ?>" target="_blank" rel="noopener">Edit in GitHub</a>
</div>
<div class="wrap">
  <?php echo $html; ?>
</div>
</body>
</html>
