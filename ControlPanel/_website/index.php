<?php
echo <<<'HTML'
<style>
  .gsw-outer-full{box-sizing:border-box;width:100vw!important;margin-left:calc(50% - 50vw)!important;margin-right:calc(50% - 50vw)!important}
  .gsw-page-center{display:flex;justify-content:center;padding:24px 12px}
  .gsw-wrap{width:min(95vw,1100px);margin:0 auto;line-height:1.55}

  .gsw-hero{display:grid;gap:10px;margin-bottom:18px;text-align:center;justify-items:center}
  .gsw-hero h1{margin:0;font-size:2rem;letter-spacing:.2px}
  .gsw-hero p{margin:0;font-size:1.05rem}
  .gsw-badge{display:inline-block;margin-top:6px;padding:6px 10px;border:1px solid;border-radius:999px;font-weight:600;font-size:.92rem}

  .gsw-callout{margin:14px 0 4px;padding:12px 14px;border:1px dashed;border-radius:10px;text-align:center;font-size:1rem}

  .gsw-locations{margin:12px 0 20px}
  .gsw-locations h2{margin:0 0 10px;font-size:1.15rem;text-transform:uppercase;letter-spacing:.6px}
  .gsw-locations-list{display:grid;gap:8px;grid-template-columns:1fr;list-style:disc;margin:0;padding-left:22px}
  @media(min-width:680px){.gsw-locations-list{grid-template-columns:repeat(2,1fr)}}

  .gsw-grid{display:grid;gap:14px;grid-template-columns:1fr}
  @media(min-width:720px){.gsw-grid{grid-template-columns:repeat(3,1fr)}}
  .gsw-card{padding:16px;border:1px solid;border-radius:10px}
  .gsw-card h3{margin:0 0 6px;font-size:1.1rem}
  .gsw-card p{margin:0}

  .gsw-cta{display:flex;gap:12px;flex-wrap:wrap;margin-top:16px;justify-content:center}
  .gsw-btn{border:1px solid;border-radius:8px;padding:10px 14px;text-decoration:none;display:inline-block;font-weight:600}

  .gsw-fine{font-size:.92rem;opacity:.9;text-align:center;margin-top:10px}
</style>

<div class="gsw-outer-full">
  <div class="gsw-page-center">
    <section class="gsw-wrap" aria-label="GameServers.World">
      <header class="gsw-hero">
        <h1>Virtual Private Gameservers</h1>
        <p>Just like running on your own dedicated box — <strong>full configurability</strong> with <strong>help when you need it</strong>.</p>
        <span class="gsw-badge" aria-label="Never oversold">Never Oversold Capacity</span>
        <p style="margin-top:6px;">We also specialize in classics — <strong>50+ older/community-favorite games</strong> hosted right.</p>
      </header>

      <div class="gsw-callout" role="note">
        Your server gets the resources it’s promised. No cramming, no noisy neighbors — predictable performance for your game and mods.
      </div>

      <section class="gsw-locations" aria-label="Current locations">
        <h2>Current Locations</h2>
        <ul class="gsw-locations-list">
          <li><strong>East USA</strong></li>
          <li><strong>Central USA</strong></li>
          <li><strong>West USA</strong></li>
          <li><strong>Western Europe</strong></li>
        </ul>
      </section>

      <section class="gsw-grid" aria-label="Highlights">
        <article class="gsw-card">
          <h3>Built for the classics</h3>
          <p>Low-latency routing and high-clock CPUs keep legacy engines smooth. We support favorites like CS&nbsp;1.6, Urban Terror, DayZ Mod — and dozens more.</p>
        </article>
        <article class="gsw-card">
          <h3>Simple, affordable plans</h3>
          <p>Clear options, month-to-month flexibility, and room to scale as your community grows.</p>
        </article>
        <article class="gsw-card">
          <h3>Real humans, fast setup</h3>
          <p>We’ll help with configs, common mods, and a clean starter rotation so you can go live quickly.</p>
        </article>
      </section>

      <nav class="gsw-cta" aria-label="Primary actions">
        <a class="gsw-btn" href="/server-list/">Browse Game Servers</a>
        <a class="gsw-btn" href="/contact/">Talk to Support</a>
      </nav>

      <p class="gsw-fine">Looking for a specific title or region? Tell us what you need — we add games and locations regularly.</p>
    </section>
  </div>
</div>
HTML;
?>

