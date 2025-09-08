<?php
// docs.php — simple gallery to view or download your PDFs

$docs = [
  [
    'key'   => 'sales',
    'title' => 'Sales & Onboarding Playbook',
    'desc'  => 'Messaging, ICPs, demo flow, onboarding runbook, SLAs.',
  ],
  [
    'key'   => 'security',
    'title' => 'Security & Compliance Playbook',
    'desc'  => 'Hygiene checklist, patching cadence, backups, incident response.',
  ],
  [
    'key'   => 'investor',
    'title' => 'Investor One-Pager',
    'desc'  => 'Market, product, traction, roadmap, team, basic financials.',
  ],
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Playbooks</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    :root { --bg:#0f172a; --card:#111827; --ink:#e5e7eb; --ink-dim:#9ca3af; --accent:#22d3ee; }
    *{box-sizing:border-box} body{margin:0;background:var(--bg);color:var(--ink);font:16px/1.5 system-ui, -apple-system, Segoe UI, Roboto, Arial}
    .wrap{max-width:1100px;margin:40px auto;padding:0 16px}
    h1{font-size:28px;margin:0 0 18px}
    p.lead{color:var(--ink-dim);margin:0 0 26px}
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px}
    .card{background:var(--card);border:1px solid #1f2937;border-radius:14px;padding:16px}
    .card h3{margin:0 0 6px;font-size:18px}
    .card p{margin:0 0 12px;color:var(--ink-dim)}
    .btns{display:flex;gap:8px;flex-wrap:wrap}
    .btn{appearance:none;border:1px solid #334155;border-radius:10px;padding:9px 12px;cursor:pointer;background:#0b1220;color:var(--ink);}
    .btn:hover{border-color:var(--accent)}
    .viewer{margin-top:12px;display:none}
    .viewer iframe{width:100%;height:680px;border:1px solid #1f2937;border-radius:12px;background:#0b1220}
    .footer{margin:36px 0 12px;color:var(--ink-dim);font-size:14px}
    a{color:var(--accent);text-decoration:none} a:hover{text-decoration:underline}
    .embed-snippet{margin-top:32px;background:#0b1220;border:1px solid #1f2937;border-radius:12px;padding:14px;color:#cbd5e1;overflow:auto}
    code{font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;font-size:13px}
  </style>
</head>
<body>
<div class="wrap">
  <h1>Playbooks</h1>
  <p class="lead">View inline or download. You can also copy an embed snippet for any page on your site.</p>

  <div class="grid">
    <?php foreach ($docs as $doc): 
      $key = htmlspecialchars($doc['key'], ENT_QUOTES, 'UTF-8');
      $title = htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8');
      $desc = htmlspecialchars($doc['desc'], ENT_QUOTES, 'UTF-8');
      $viewerId = "viewer-" . $key;
      $src = "serve.php?doc=" . rawurlencode($key); // inline by default
      $dl  = "serve.php?doc=" . rawurlencode($key) . "&download=1";
    ?>
    <div class="card" id="card-<?= $key ?>">
      <h3><?= $title ?></h3>
      <p><?= $desc ?></p>
      <div class="btns">
        <button class="btn" onclick="toggleViewer('<?= $viewerId ?>','<?= $src ?>')">View inline</button>
        <a class="btn" href="<?= $dl ?>">Download</a>
        <button class="btn" onclick="copyEmbed('<?= htmlspecialchars('<iframe src=\"' . $src . '\" width=\"100%\" height=\"720\" style=\"border:1px solid #ddd;border-radius:12px\"></iframe>') ?>')">Copy embed</button>
      </div>
      <div class="viewer" id="<?= $viewerId ?>">
        <iframe loading="lazy" title="<?= $title ?>"></iframe>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="embed-snippet">
    <strong>Generic embed snippet (replace <code>doc=</code> value):</strong>
    <pre><code>&lt;iframe src="/serve.php?doc=sales" width="100%" height="720" style="border:1px solid #ddd;border-radius:12px"&gt;&lt;/iframe&gt;</code></pre>
  </div>

  <p class="footer">Tip: You can place <code>serve.php</code> and <code>docs.php</code> anywhere; just keep the whitelist in <code>serve.php</code> updated to match your files.</p>
</div>

<script>
  function toggleViewer(id, src){
    const el = document.getElementById(id);
    const frame = el.querySelector('iframe');
    if (el.style.display === 'block') { el.style.display = 'none'; return; }
    if (!frame.getAttribute('src')) frame.setAttribute('src', src);
    el.style.display = 'block';
    el.scrollIntoView({behavior:'smooth', block:'start'});
  }

  async function copyEmbed(html){
    try {
      await navigator.clipboard.writeText(html.replace(/&quot;/g,'"'));
      alert('Embed code copied to clipboard!');
    } catch(e) {
      prompt('Copy embed HTML:', html.replace(/&quot;/g,'"'));
    }
  }
</script>
</body>
</html>

