<?php
/**
 * DayZ Standalone Server Documentation
 * Comprehensive guide for hosting and managing DayZ dedicated servers
 * 
 * Sources: Bohemia Interactive Wiki, LGSM, DayZ Forums, Steam Community, r/dayzservers
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
    <a href="#mods">Mods</a>
    <a href="#troubleshooting">🔧 Troubleshooting</a>
    <a href="#performance">Performance</a>
    <a href="#resources">Resources</a>
</div>

<h1 id="overview">DayZ Standalone Server Guide</h1>

<div class="info-box">
    <h3 style="color: #ffffff; margin-top: 0;">Quick Information</h3>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">Game:</strong> DayZ Standalone (Survival Horror)</li>
        <li><strong style="color: #ffffff;">Developer:</strong> Bohemia Interactive</li>
        <li><strong style="color: #ffffff;">Server Type:</strong> Dedicated Server (Windows/Linux)</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">2302 UDP</code></li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 60-100+ configurable</li>
        <li><strong style="color: #ffffff;">Workshop:</strong> Steam Workshop mod support</li>
    </ul>
</div>

<p><strong>DayZ</strong> is an unforgiving open-world zombie survival game where players must scavenge for supplies, build bases, and survive against infected and other players. This standalone version is completely separate from the original DayZ Mod.</p>

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
            <td>Game Port - Primary connection</td>
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
        <tr>
            <td><code>27016</code></td>
            <td>TCP</td>
            <td>RCON - Remote administration</td>
            <td><span class="optional">Optional</span></td>
        </tr>
    </tbody>
</table>

<h3>Firewall Configuration</h3>

<h4>UFW (Ubuntu/Debian)</h4>
<pre><code># DayZ game ports
sudo ufw allow 2302:2305/udp comment 'DayZ Server'
# RCON port (if using)
sudo ufw allow 27016/tcp comment 'DayZ RCON'</code></pre>

<h4>FirewallD (CentOS/RHEL)</h4>
<pre><code>sudo firewall-cmd --permanent --add-port=2302-2305/udp
sudo firewall-cmd --permanent --add-port=27016/tcp
sudo firewall-cmd --reload</code></pre>

<h4>Windows Firewall</h4>
<pre><code>New-NetFirewallRule -DisplayName "DayZ Server UDP" -Direction Inbound -Protocol UDP -LocalPort 2302-2305 -Action Allow
New-NetFirewallRule -DisplayName "DayZ RCON TCP" -Direction Inbound -Protocol TCP -LocalPort 27016 -Action Allow</code></pre>

<h2 id="startup">⚙️ Startup Parameters</h2>

<h3>Windows Startup</h3>
<pre><code>DayZServer_x64.exe -config=serverDZ.cfg -port=2302 -profiles=SC -dologs -adminlog -netlog -freezecheck -mod=@CF;@VPPAdminTools</code></pre>

<h3>Linux Startup</h3>
<pre><code>./DayZServer -config=serverDZ.cfg -port=2302 -profiles=SC -dologs -adminlog -netlog -freezecheck -mod=@CF;@VPPAdminTools</code></pre>

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
            <td><code>-config=</code></td>
            <td>Path to serverDZ.cfg</td>
            <td><code>-config=serverDZ.cfg</code></td>
        </tr>
        <tr>
            <td><code>-port=</code></td>
            <td>Game port</td>
            <td><code>-port=2302</code></td>
        </tr>
        <tr>
            <td><code>-profiles=</code></td>
            <td>Profile folder path</td>
            <td><code>-profiles=SC</code></td>
        </tr>
        <tr>
            <td><code>-dologs</code></td>
            <td>Enable logging</td>
            <td>Flag (no value)</td>
        </tr>
        <tr>
            <td><code>-adminlog</code></td>
            <td>Enable admin logging</td>
            <td>Flag (no value)</td>
        </tr>
        <tr>
            <td><code>-netlog</code></td>
            <td>Enable network logging</td>
            <td>Flag (no value)</td>
        </tr>
        <tr>
            <td><code>-freezecheck</code></td>
            <td>Enable freeze detection</td>
            <td>Flag (no value)</td>
        </tr>
        <tr>
            <td><code>-mod=</code></td>
            <td>Mods to load (semicolon separated)</td>
            <td><code>-mod=@CF;@VPP</code></td>
        </tr>
        <tr>
            <td><code>-cpuCount=</code></td>
            <td>CPU cores to use</td>
            <td><code>-cpuCount=4</code></td>
        </tr>
        <tr>
            <td><code>-limitFPS=</code></td>
            <td>Limit server FPS</td>
            <td><code>-limitFPS=60</code></td>
        </tr>
    </tbody>
</table>

<h2 id="configuration">Configuration Files</h2>

<h3>serverDZ.cfg</h3>
<pre><code>hostname = "Your DayZ Server";
password = "";                    // Join password
passwordAdmin = "adminpass";       // Admin password
maxPlayers = 60;

// Missions
Missions = {
    DayZ = {
        template = "dayzOffline.chernarusplus"; // or enoch, livonia, takistanplus, namalsk
    };
};

// Performance
disableVoN = 0;                   // Voice chat enabled
vonCodecQuality = 20;             // Voice quality
disable3rdPerson = 0;             // 0=allow 3rd person
disableCrosshair = 0;

// Persistence
storeHouseStateDisabled = false;   // Save building states
storageAutoFix = 1;               // Auto-fix corrupted storage

// Time acceleration
serverTime = "SystemTime";         // Or specific time
serverTimeAcceleration = 2;        // 2x time speed
serverNightTimeAcceleration = 4;   // 4x night speed

// Weather
serverTimePersistent = 1;          // Persistent time
guaranteedUpdates = 1;
loginQueueConcurrentPlayers = 5;
loginQueueMaxPlayers = 500;
instanceId = 1;                    // Server instance ID

// Logging
lightingConfig = 0;
respawnTime = 5;</code></pre>

<h2 id="mods">Mods & Workshop</h2>

<h3>Popular DayZ Mods</h3>
<ul>
    <li><strong>Community Framework (CF)</strong> - Required for many mods</li>
    <li><strong>VPPAdminTools</strong> - Server administration</li>
    <li><strong>Expansion</strong> - Vehicles, helicopters, missions</li>
    <li><strong>BuilderItems</strong> - Enhanced building</li>
    <li><strong>Trader</strong> - NPC trading systems</li>
    <li><strong>BreachingCharge</strong> - Raid mechanics</li>
    <li><strong>Dabs Framework</strong> - Modding framework</li>
</ul>

<h3>Installing Mods from Workshop</h3>
<ol>
    <li>Subscribe to mod on Steam Workshop</li>
    <li>Copy from workshop folder to server:<br>
        <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">steamapps\workshop\content\221100\{modid}</code></li>
    <li>Rename folder to <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">@ModName</code></li>
    <li>Add to startup: <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">-mod=@CF;@VPPAdminTools</code></li>
    <li>Copy required .bikey files to <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">keys/</code> folder</li>
</ol>

<div class="warning-box">
    <h4 style="color: #ffffff; margin-top: 0;">Mod Load Order</h4>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Always load <strong>Community Framework</strong> first</li>
        <li>Load dependency mods before mods that require them</li>
        <li>Use semicolons (;) to separate mods, no spaces</li>
        <li>Mod folder names are case-sensitive on Linux</li>
    </ul>
</div>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Won't Start</h3>
<ul>
    <li><strong>Port in use:</strong> Check if 2302 is available with <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">netstat</code></li>
    <li><strong>Missing dependencies:</strong> Install Visual C++ Redistributable (Windows) or lib32gcc1 (Linux)</li>
    <li><strong>Config errors:</strong> Validate serverDZ.cfg syntax</li>
    <li><strong>Mission not found:</strong> Verify mission name in serverDZ.cfg matches workshop files</li>
</ul>

<h3>Players Can't Connect</h3>
<ul>
    <li>Verify firewall allows UDP 2302-2305</li>
    <li>Check BattlEye is running (required for official servers)</li>
    <li>Ensure server and client versions match</li>
    <li>Verify all mods are properly loaded and have valid .bikey files</li>
</ul>

<h3>Persistence Issues</h3>
<ul>
    <li><strong>Tents/stashes disappearing:</strong> Check <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">storeHouseStateDisabled = false</code></li>
    <li><strong>Storage corruption:</strong> Enable <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">storageAutoFix = 1</code></li>
    <li><strong>Loot not spawning:</strong> Delete <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">storage_X/data/*.bin</code> files to reset economy</li>
</ul>

<h3>Mod Conflicts</h3>
<ul>
    <li>Check server logs in <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">profiles/</code> folder</li>
    <li>Verify .bikey files are in <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">keys/</code> folder</li>
    <li>Test mods individually to isolate conflicts</li>
    <li>Ensure mod versions match between server and clients</li>
</ul>

<h2 id="performance">Performance Optimization</h2>

<h3>Server Configuration</h3>
<pre><code># Startup parameters for performance
-cpuCount=4 -limitFPS=60 -dologs -freezecheck

# In serverDZ.cfg
serverTimeAcceleration = 1;         // Reduce if performance issues
guaranteedUpdates = 1;
loginQueueConcurrentPlayers = 3;</code></pre>

<h3>Hardware Recommendations</h3>
<ul>
    <li><strong>CPU:</strong> 4+ cores, 3.5+ GHz single-thread</li>
    <li><strong>RAM:</strong> 8-16 GB (16+ for heavy mods)</li>
    <li><strong>Storage:</strong> SSD highly recommended</li>
    <li><strong>Network:</strong> 100+ Mbps, low latency</li>
</ul>

<h2 id="related-mods">🔌 Related Mods & Plugins</h2>
<p>Popular server modifications compatible with DayZ Standalone:</p>
<ul>
    <li><a href="../bec/">BEC (Battleye Extended Controls)</a> - Advanced RCON tool with automated restarts, scheduled messages, custom commands, and robust BattlEye integration for ARMA/DayZ servers</li>
</ul>

<h2 id="resources">📚 Resources</h2>

<ul>
    <li><a href="https://community.bistudio.com/wiki/DayZ:Server_Configuration" target="_blank">BI Wiki - Server Configuration</a></li>
    <li><a href="https://forums.dayz.com/forum/150-servers/" target="_blank">Official DayZ Forums - Servers</a></li>
    <li><a href="https://www.reddit.com/r/dayzservers/" target="_blank">r/dayzservers Community</a></li>
    <li><a href="https://github.com/GameServerManagers/LinuxGSM" target="_blank">LinuxGSM - DayZ Script</a></li>
    <li><a href="https://steamcommunity.com/app/221100/workshop/" target="_blank">Steam Workshop - DayZ Mods</a></li>
</ul>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>BattlEye Required:</strong> Official/public servers must run BattlEye anti-cheat</li>
        <li><strong>Regular Backups:</strong> Backup persistence files and configs regularly</li>
        <li><strong>Mod Updates:</strong> Keep mods updated to prevent conflicts</li>
        <li><strong>Economy System:</strong> DayZ uses dynamic loot spawning system</li>
        <li><strong>64-bit Only:</strong> Use DayZServer_x64.exe on Windows</li>
        <li><strong>Wipe Cycles:</strong> Consider periodic wipes for fresh gameplay</li>
    </ul>
</div>

<hr style="margin: 40px 0; border: none; border-top: 1px solid rgba(255,255,255,0.1);">

<p style="text-align: center; color: rgba(255,255,255,0.6); font-size: 0.9em;">
    <strong>Documentation Version:</strong> 1.0 | <strong>Last Updated:</strong> November 10, 2025<br>
    <strong>Sources:</strong> Bohemia Interactive Wiki, DayZ Forums, LinuxGSM, r/dayzservers<br>
    <em>For the original DayZ Mod, see <a href="/docs.php?action=view&doc=arma2oa">Arma 2: OA + DayZ Mod Guide</a>.</em>
</p>
