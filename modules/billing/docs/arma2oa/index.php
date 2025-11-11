<?php
/**
 * Arma 2: Operation Arrowhead Server Documentation
 * Comprehensive guide for hosting and managing Arma 2: OA dedicated servers
 * 
 * Sources: Bohemia Interactive Wiki, LGSM, Steam Community
 * Last Updated: November 10, 2025
 * Note: This version is the base for DayZ Mod servers (requires Combined Operations)
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
    .optional { color: #f59e0b; }
</style>

<!-- Navigation -->
<div class="doc-nav">
    <h3>📚 Quick Navigation</h3>
    <a href="#overview">Overview</a>
    <a href="#ports">🔌 Ports</a>
    <a href="#installation">Installation</a>
    <a href="#configuration">Configuration</a>
    <a href="#startup">⚙️ Startup Parameters</a>
    <a href="#dayzmod">DayZ Mod Setup</a>
    <a href="#troubleshooting">🔧 Troubleshooting</a>
    <a href="#performance">Performance</a>
    <a href="#resources">Resources</a>
</div>

<h1 id="overview">Arma 2: Operation Arrowhead Server Guide</h1>

<div class="info-box">
    <h3 style="color: #ffffff; margin-top: 0;">Quick Information</h3>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">Game:</strong> Arma 2: Operation Arrowhead (Military Simulation)</li>
        <li><strong style="color: #ffffff;">Developer:</strong> Bohemia Interactive</li>
        <li><strong style="color: #ffffff;">Release:</strong> 2010 (Legacy game)</li>
        <li><strong style="color: #ffffff;">Server Type:</strong> Dedicated Server (Windows/Linux)</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">2302 UDP</code></li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 50-100+ configurable</li>
        <li><strong style="color: #ffffff;">Special Note:</strong> Required base for DayZ Mod (with Combined Operations)</li>
    </ul>
</div>

<p><strong>Arma 2: Operation Arrowhead</strong> is the standalone expansion for Arma 2. When combined with Arma 2 (Combined Operations), it serves as the foundation for the popular <strong>DayZ Mod</strong>. This guide covers dedicated server setup for both standard OA gameplay and DayZ Mod hosting.</p>

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
            <td>Game Port - Primary connection port</td>
            <td><span class="required">REQUIRED</span></td>
        </tr>
        <tr>
            <td><code>2303</code></td>
            <td>UDP</td>
            <td>Steam Query Port - Server browser</td>
            <td><span class="required">REQUIRED</span></td>
        </tr>
        <tr>
            <td><code>2304</code></td>
            <td>UDP</td>
            <td>Steam Port - Steam connectivity</td>
            <td><span class="required">REQUIRED</span></td>
        </tr>
        <tr>
            <td><code>2305</code></td>
            <td>UDP</td>
            <td>VON (Voice Over Network)</td>
            <td><span class="optional">Optional</span></td>
        </tr>
    </tbody>
</table>

<h3>Firewall Configuration</h3>

<h4>UFW (Ubuntu/Debian)</h4>
<pre><code>sudo ufw allow 2302:2305/udp comment 'Arma 2 OA Server'</code></pre>

<h4>FirewallD (CentOS/RHEL)</h4>
<pre><code>sudo firewall-cmd --permanent --add-port=2302-2305/udp
sudo firewall-cmd --reload</code></pre>

<h4>Windows Firewall</h4>
<pre><code>New-NetFirewallRule -DisplayName "Arma 2 OA Server" -Direction Inbound -Protocol UDP -LocalPort 2302-2305 -Action Allow</code></pre>

<h2 id="startup">⚙️ Startup Parameters</h2>

<h3>Windows Startup</h3>
<pre><code>arma2oaserver.exe -port=2302 -config=server.cfg -cfg=basic.cfg -profiles=SC -mod=@mod1;@mod2</code></pre>

<h3>Linux Startup</h3>
<pre><code>./arma2oaserver -port=2302 -config=server.cfg -cfg=basic.cfg -profiles=SC -mod=@mod1;@mod2</code></pre>

<h3>Key Parameters</h3>
<table class="ports-table">
    <thead>
        <tr>
            <th>Parameter</th>
            <th>Description</th>
            <th>Example</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><code>-port=</code></td>
            <td>Game port</td>
            <td><code>-port=2302</code></td>
        </tr>
        <tr>
            <td><code>-config=</code></td>
            <td>Path to server.cfg</td>
            <td><code>-config=server.cfg</code></td>
        </tr>
        <tr>
            <td><code>-cfg=</code></td>
            <td>Path to basic.cfg</td>
            <td><code>-cfg=basic.cfg</code></td>
        </tr>
        <tr>
            <td><code>-profiles=</code></td>
            <td>Profile folder</td>
            <td><code>-profiles=SC</code></td>
        </tr>
        <tr>
            <td><code>-mod=</code></td>
            <td>Mods to load</td>
            <td><code>-mod=@DayZ;@DayZ_Epoch</code></td>
        </tr>
        <tr>
            <td><code>-world=</code></td>
            <td>Default world</td>
            <td><code>-world=Takistan</code></td>
        </tr>
    </tbody>
</table>

<h2 id="dayzmod">DayZ Mod Setup</h2>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;">DayZ Mod Requirements</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Requires <strong>Arma 2: Combined Operations</strong> (Arma 2 + Operation Arrowhead)</li>
        <li>DayZ Mod files must be installed in <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">@DayZ</code> folder</li>
        <li>Optional variants: DayZ Epoch, DayZ Overpoch, DayZ Origins</li>
    </ul>
</div>

<h3>DayZ Mod Startup Example</h3>
<pre><code># Standard DayZ Mod
-mod=Expansion\beta;Expansion\beta\Expansion;ca;@DayZ -world=Chernarus

# DayZ Epoch
-mod=Expansion\beta;Expansion\beta\Expansion;ca;@DayZ_Epoch -world=Chernarus

# DayZ Overpoch (Overwatch + Epoch)
-mod=Expansion\beta;Expansion\beta\Expansion;ca;@DayZ_Overwatch;@DayZ_Epoch</code></pre>

<h3>Common DayZ Mod Variants</h3>
<ul>
    <li><strong>DayZ Mod:</strong> Original zombie survival mod</li>
    <li><strong>DayZ Epoch:</strong> Building/crafting focus</li>
    <li><strong>DayZ Overpoch:</strong> Overwatch + Epoch combined</li>
    <li><strong>DayZ Origins:</strong> Custom lore and mechanics</li>
</ul>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Won't Start</h3>
<ul>
    <li><strong>Missing Beta Folder:</strong> Ensure <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">Expansion\beta</code> exists for DayZ</li>
    <li><strong>Port Conflicts:</strong> Check if 2302 is already in use</li>
    <li><strong>Config Errors:</strong> Validate server.cfg syntax (semicolons required)</li>
</ul>

<h3>Players Can't Join</h3>
<ul>
    <li>Verify firewall allows UDP 2302-2305</li>
    <li>Check mod versions match between server and clients</li>
    <li>Ensure BattlEye is enabled if required</li>
</ul>

<h3>DayZ Mod Issues</h3>
<ul>
    <li><strong>Database connection:</strong> MySQL required for DayZ Epoch/Overpoch</li>
    <li><strong>HiveExt errors:</strong> Check <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">@DayZ_Epoch\HiveExt.ini</code> configuration</li>
    <li><strong>Loot not spawning:</strong> Verify mission file and database tables</li>
</ul>

<h2 id="performance">Performance Optimization</h2>

<h3>Server.cfg Performance Settings</h3>
<pre><code>MaxMsgSend = 256;
MaxSizeGuaranteed = 512;
MaxSizeNonguaranteed = 256;
MinBandwidth = 131072;
MaxBandwidth = 10000000000;
MinErrorToSend = 0.001;
MaxCustomFileSize = 160000;</code></pre>

<h3>Hardware Recommendations</h3>
<ul>
    <li><strong>CPU:</strong> 2-4 cores, 3.0+ GHz</li>
    <li><strong>RAM:</strong> 4-8 GB minimum</li>
    <li><strong>Storage:</strong> HDD acceptable, SSD preferred</li>
    <li><strong>Network:</strong> 50+ Mbps</li>
</ul>

<h2 id="resources">📚 Resources</h2>

<ul>
    <li><a href="https://community.bistudio.com/wiki/Arma_2:_Operation_Arrowhead:_Dedicated_Server" target="_blank">BI Wiki - OA Dedicated Server</a></li>
    <li><a href="https://github.com/GameServerManagers/LinuxGSM" target="_blank">LinuxGSM - Arma 2 OA Script</a></li>
    <li><a href="https://dayzepoch.com/" target="_blank">DayZ Epoch Official Site</a></li>
    <li><a href="https://www.reddit.com/r/dayzmod/" target="_blank">r/dayzmod Community</a></li>
</ul>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Legacy Game:</strong> Arma 2 OA is a 2010 game; community support is limited</li>
        <li><strong>DayZ Standalone Exists:</strong> Consider DayZ Standalone for modern experience</li>
        <li><strong>Combined Operations:</strong> Required for DayZ Mod functionality</li>
        <li><strong>Database Required:</strong> DayZ Epoch/Overpoch need MySQL database</li>
        <li><strong>Backup Regularly:</strong> Always backup database and mission files</li>
    </ul>
</div>

<hr style="margin: 40px 0; border: none; border-top: 1px solid rgba(255,255,255,0.1);">

<p style="text-align: center; color: rgba(255,255,255,0.6); font-size: 0.9em;">
    <strong>Documentation Version:</strong> 1.0 | <strong>Last Updated:</strong> November 10, 2025<br>
    <strong>Sources:</strong> Bohemia Interactive Wiki, LinuxGSM, DayZ Epoch Community<br>
    <em>For DayZ Standalone, see our <a href="/docs.php?action=view&doc=dayz">DayZ Standalone Guide</a>.</em>
</p>
