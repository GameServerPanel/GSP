<?php
// Where your panel is hosting the summary
$panel_summary = 'https://panel.yourdomain.com/status/api/summary.php';

// Server-side fetch
$ctx = stream_context_create(['http'=>['timeout'=>5]]);
$json = @file_get_contents($panel_summary, false, $ctx);
if ($json === false) { echo "<p>Unable to load status.</p>"; exit; }
$data = json_decode($json, true);
$hosts = $data['hosts'] ?? [];

echo '<table style="border-collapse:collapse;min-width:480px">';
echo '<tr><th style="text-align:left;padding:6px;border-bottom:1px solid #ccc">Location</th>';
echo '<th style="text-align:left;padding:6px;border-bottom:1px solid #ccc">Status</th>';
echo '<th style="text-align:right;padding:6px;border-bottom:1px solid #ccc">CPU %</th>';
echo '<th style="text-align:right;padding:6px;border-bottom:1px solid #ccc">MEM %</th></tr>';

foreach ((array)$hosts as $h) {
  $color = $h['status']==='UP' ? '#16a34a' : '#dc2626';
  $cpu = is_null($h['cpu_pct']) ? '—' : number_format($h['cpu_pct'],1);
  $mem = is_null($h['mem_pct']) ? '—' : number_format($h['mem_pct'],1);
  echo "<tr>";
  echo "<td style='padding:6px'>{$h['location']}</td>";
  echo "<td style='padding:6px;color:$color;font-weight:600'>{$h['status']}</td>";
  echo "<td style='padding:6px;text-align:right'>{$cpu}</td>";
  echo "<td style='padding:6px;text-align:right'>{$mem}</td>";
  echo "</tr>";
}
echo '</table>';

