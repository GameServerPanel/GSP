<?php
// index.php — lists all games (from CSV) and links to per-game pages in new tabs.

$csvPath = __DIR__ . '/all_hostable_games_union.csv';
$docsDir = __DIR__ ;

function slugify($name) {
  $s = strtolower($name);
  $s = preg_replace('/[^a-z0-9]+/','-',$s);
  $s = trim($s,'-');
  return $s;
}
function hasDoc($slug, $docsDir) {
  return file_exists("$docsDir/$slug.md");
}

$rows = [];
if (file_exists($csvPath)) {
  if (($fh = fopen($csvPath, 'r')) !== false) {
    // Try to detect header; expect first column to be game name.
    $header = fgetcsv($fh);
    $hasHeader = $header && preg_match('/game/i', implode(' ', $header));
    if (!$hasHeader && $header) { $rows[] = $header; } // headerless, keep first line
    while (($data = fgetcsv($fh)) !== false) { $rows[] = $data; }
    fclose($fh);
  }
}
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Game Server Guides</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  :root { --bg:#0f172a; --card:#111827; --text:#e5e7eb; --muted:#94a3b8; --accent:#38bdf8; }
  body { background:var(--bg); color:var(--text); font:16px/1.5 system-ui,Segoe UI,Roboto,Helvetica,Arial; margin:0; }
  header { padding:24px; border-bottom:1px solid #1f2937; }
  h1 { margin:0 0 8px; font-size:24px; }
  .wrap { padding:24px; max-width:1100px; margin:0 auto; }
  .search { width:100%; padding:12px 14px; border-radius:12px; border:1px solid #1f2937; background:#0b1220;color:var(--text); }
  .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:12px; margin-top:16px; }
  .card { background:var(--card); border:1px solid #1f2937; border-radius:12px; padding:14px; }
  .card a { color:var(--accent); text-decoration:none; font-weight:600; }
  .pending { color:var(--muted); }
  .tag { display:inline-block; font-size:12px; color:#cbd5e1; background:#0b1220; padding:2px 8px; border-radius:999px; margin-top:8px; border:1px solid #1f2937; }
  footer { color:var(--muted); padding:24px; border-top:1px solid #1f2937; }
</style>
<script>
function filterList(q) {
  q = q.toLowerCase();
  document.querySelectorAll('.card').forEach(card => {
    const name = card.dataset.name;
    card.style.display = name.includes(q) ? '' : 'none';
  });
}
</script>
</head>
<body>
<header class="wrap">
  <h1>Game Server Guides</h1>
  <input class="search" placeholder="Search games…" oninput="filterList(this.value)">
  <p class="muted">Click a game to open its guide in a new tab; each page has a “Print / Save PDF” button.</p>
</header>
<div class="wrap">
  <div class="grid">
<?php
$count = 0;
foreach ($rows as $r) {
  $game = trim($r[0] ?? '');
  if ($game === '' || strtolower($game) === 'game') continue;
  $slug = slugify($game);
  $exists = hasDoc($slug, $docsDir);
  $count++;
  echo '<div class="card" data-name="'.htmlspecialchars(strtolower($game)).'">';
  if ($exists) {
    echo '<a href="game.php?g='.urlencode($slug).'" target="_blank">'.htmlspecialchars($game).'</a>';
    echo '<div class="tag">Guide ready</div>';
  } else {
    echo '<span class="pending">'.htmlspecialchars($game).'</span>';
    echo '<div class="tag">Pending</div>';
  }
  echo '</div>';
}
if ($count === 0) {
  echo '<p>No games found. Make sure all_hostable_games_union.csv exists in repo root.</p>';
}
?>
  </div>
</div>
<footer class="wrap">Tip: add or edit docs under <code>docs/games/&lt;slug&gt;.md</code>. Slug = lowercased name with non-alphanumerics as dashes.</footer>
</body>
</html>
