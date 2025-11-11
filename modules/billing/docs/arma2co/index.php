<?php
/**
 * Arma 2: Combined Operations Server Documentation
 * The foundation for DayZ Mod and other legacy ARMA 2 content
 * 
 * Sources: Bohemia Interactive Wiki, DayZ Mod Community, Epoch Forums
 * Last Updated: November 10, 2025
 */
?>
<style>
    .doc-nav { background: #1e3a5f; padding: 20px; border-radius: 8px; margin: 20px 0; }
    .doc-nav h3 { color: #ffffff; margin-top: 0; }
    .doc-nav a { display: inline-block; padding: 8px 15px; margin: 5px 10px 5px 0; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 5px; color: #7fb3ff; text-decoration: none; }
    .doc-nav a:hover { background: #3b82f6; color: #ffffff; }
    .info-box { background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px; }
    .warning-box { background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px; }
    .ports-table { width: 100%; border-collapse: collapse; margin: 20px 0; background: rgba(0,0,0,0.2); }
    .ports-table th, .ports-table td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .ports-table th { background: #1e3a5f; color: #ffffff; font-weight: 600; }
    .ports-table code { background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc; }
    .required { color: #10b981; font-weight: 600; }
</style>

<div class="doc-nav">
    <h3>📚 Quick Navigation</h3>
    <a href="#overview">Overview</a>
    <a href="#ports">🔌 Ports</a>
    <a href="#dayzmod">DayZ Mod</a>
    <a href="#startup">⚙️ Startup</a>
    <a href="#troubleshooting">🔧 Troubleshooting</a>
    <a href="#resources">Resources</a>
</div>

<h1 id="overview">Arma 2: Combined Operations Server Guide</h1>

<div class="info-box">
    <h3 style="color: #ffffff; margin-top: 0;">What is Combined Operations?</h3>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">Definition:</strong> Arma 2 + Operation Arrowhead combined package</li>
        <li><strong style="color: #ffffff;">Purpose:</strong> Required base for DayZ Mod and related mods</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">2302 UDP</code></li>
        <li><strong style="color: #ffffff;">Release:</strong> 2010 (Legacy platform)</li>
        <li><strong style="color: #ffffff;">Note:</strong> For modern DayZ, see <a href="/docs.php?action=view&doc=dayz">DayZ Standalone</a></li>
    </ul>
</div>

<p><strong>Arma 2: Combined Operations</strong> merges the base Arma 2 game with its Operation Arrowhead expansion. This combination is the foundation for the original <strong>DayZ Mod</strong>, DayZ Epoch, DayZ Overpoch, and other popular legacy mods.</p>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-info-circle" style="color: #fbbf24; margin-right: 8px;"></i>Important Information</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Legacy Platform:</strong> Arma 2 is a 2010 game with limited modern support</li>
        <li><strong>DayZ Standalone Exists:</strong> For modern DayZ experience, use DayZ Standalone</li>
        <li><strong>Community-Driven:</strong> Most support comes from community forums and modders</li>
        <li><strong>Both Games Required:</strong> Must have Arma 2 AND Operation Arrowhead files</li>
    </ul>
</div>

<h2 id="ports">🔌 Server Ports</h2>

<table class="ports-table">
    <thead>
        <tr>
            <th>Port</th>
            <th>Protocol</th>
            <th>Purpose</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><code>2302</code></td>
            <td>UDP</td>
            <td>Game Port</td>
            <td><span class="required">REQUIRED</span></td>
        </tr>
        <tr>
            <td><code>2303</code></td>
            <td>UDP</td>
            <td>Steam Query Port</td>
            <td><span class="required">REQUIRED</span></td>
        </tr>
        <tr>
            <td><code>2304</code></td>
            <td>UDP</td>
            <td>Steam Port</td>
            <td><span class="required">REQUIRED</span></td>
        </tr>
        <tr>
            <td><code>2305</code></td>
            <td>UDP</td>
            <td>VON (Voice chat)</td>
            <td><span class="required">REQUIRED</span></td>
        </tr>
        <tr>
            <td><code>3306</code></td>
            <td>TCP</td>
            <td>MySQL (for DayZ Epoch/Overpoch)</td>
            <td><span class="required">Required for Epoch</span></td>
        </tr>
    </tbody>
</table>

<h3>Firewall Configuration</h3>
<pre><code># UFW (Ubuntu/Debian)
sudo ufw allow 2302:2305/udp comment 'Arma 2 CO'
sudo ufw allow 3306/tcp comment 'MySQL for DayZ Epoch'

# FirewallD (CentOS)
sudo firewall-cmd --permanent --add-port=2302-2305/udp
sudo firewall-cmd --permanent --add-port=3306/tcp
sudo firewall-cmd --reload</code></pre>

<h2 id="dayzmod">DayZ Mod Setup</h2>

<p>The primary use case for Combined Operations servers is hosting DayZ Mod variants.</p>

<h3>DayZ Mod Variants</h3>
<ul>
    <li><strong>DayZ Mod (Vanilla):</strong> Original zombie survival mod</li>
    <li><strong>DayZ Epoch:</strong> Building, crafting, and base construction focus</li>
    <li><strong>DayZ Overpoch:</strong> Overwatch + Epoch combined (military loot + building)</li>
    <li><strong>DayZ Origins:</strong> Custom storyline and unique mechanics</li>
</ul>

<h3>DayZ Epoch Startup (Most Popular)</h3>
<pre><code># Windows
arma2oaserver.exe -port=2302 -config=instance_11_Chernarus\config.cfg -cfg=instance_11_Chernarus\basic.cfg -profiles=instance_11_Chernarus -name=instance_11_Chernarus "-mod=@DayZ_Epoch;@DayZ_Epoch_Server;"

# Linux
./arma2oaserver -port=2302 -config=instance_11_Chernarus/config.cfg -cfg=instance_11_Chernarus/basic.cfg -profiles=instance_11_Chernarus -name=instance_11_Chernarus -mod=@DayZ_Epoch;@DayZ_Epoch_Server;</code></pre>

<h3>Required Files for DayZ Epoch</h3>
<ul>
    <li><code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">@DayZ_Epoch</code> - Main mod folder</li>
    <li><code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">@DayZ_Epoch_Server</code> - Server-side files</li>
    <li><code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">HiveExt.dll</code> - Database connector</li>
    <li><code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">HiveExt.ini</code> - MySQL configuration</li>
    <li>MySQL database with Epoch schema</li>
</ul>

<h3>HiveExt.ini Configuration</h3>
<pre><code>[Database]
Type = MySql
Host = localhost
Port = 3306
Database = epoch
Username = epochuser
Password = yourpassword

[Objects]
CleanupPlacedAfterDays = 6
CleanupUnlockedAfterDays = 6</code></pre>

<h2 id="startup">⚙️ Startup Parameters</h2>

<table class="ports-table">
    <thead>
        <tr>
            <th>Parameter</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><code>-port=2302</code></td>
            <td>Game port</td>
        </tr>
        <tr>
            <td><code>-config=</code></td>
            <td>Path to server config</td>
        </tr>
        <tr>
            <td><code>-cfg=</code></td>
            <td>Path to basic config</td>
        </tr>
        <tr>
            <td><code>-profiles=</code></td>
            <td>Profile directory</td>
        </tr>
        <tr>
            <td><code>-name=</code></td>
            <td>Profile name</td>
        </tr>
        <tr>
            <td><code>-mod=</code></td>
            <td>Mods to load (semicolon separated)</td>
        </tr>
    </tbody>
</table>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>DayZ Epoch Database Issues</h3>
<ul>
    <li><strong>Can't connect to MySQL:</strong> Verify HiveExt.ini credentials and MySQL is running</li>
    <li><strong>Characters not saving:</strong> Check MySQL user has proper permissions</li>
    <li><strong>Vehicles disappearing:</strong> Verify cleanup settings in HiveExt.ini</li>
</ul>

<h3>Server Won't Start</h3>
<ul>
    <li>Ensure <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">Expansion\beta</code> folder exists</li>
    <li>Verify mod folder names match startup parameters exactly</li>
    <li>Check BattlEye is enabled (required for public servers)</li>
</ul>

<h3>Players Can't Join</h3>
<ul>
    <li>Verify firewall allows UDP 2302-2305</li>
    <li>Ensure clients have matching mod versions</li>
    <li>Check BattlEye filters aren't too restrictive</li>
</ul>

<h2 id="resources">📚 Resources</h2>

<ul>
    <li><a href="https://dayzepoch.com/" target="_blank">DayZ Epoch Official Site</a></li>
    <li><a href="https://epochmod.com/forum/" target="_blank">Epoch Mod Forums</a></li>
    <li><a href="https://www.reddit.com/r/dayzmod/" target="_blank">r/dayzmod Community</a></li>
    <li><a href="https://github.com/EpochModTeam/DayZ-Epoch" target="_blank">DayZ Epoch GitHub</a></li>
    <li><a href="https://community.bistudio.com/wiki/ArmA:_Startup_Parameters" target="_blank">BI Wiki - Startup Parameters</a></li>
</ul>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Database Required:</strong> DayZ Epoch/Overpoch require MySQL database</li>
        <li><strong>Regular Backups:</strong> Backup database and mission files frequently</li>
        <li><strong>Legacy Platform:</strong> Limited official support, rely on community</li>
        <li><strong>Consider Standalone:</strong> DayZ Standalone offers modern experience</li>
        <li><strong>BattlEye Filters:</strong> Properly configure to allow mod scripts</li>
    </ul>
</div>

<hr style="margin: 40px 0; border: none; border-top: 1px solid rgba(255,255,255,0.1);">

<p style="text-align: center; color: rgba(255,255,255,0.6); font-size: 0.9em;">
    <strong>Documentation Version:</strong> 1.0 | <strong>Last Updated:</strong> November 10, 2025<br>
    <strong>Sources:</strong> DayZ Epoch Community, Epoch Forums, Bohemia Wiki<br>
    <em>For modern DayZ, see <a href="/docs.php?action=view&doc=dayz">DayZ Standalone Guide</a>.</em>
</p>
