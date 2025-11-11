<?php
/**
 * Day of Infamy Server Documentation
 */
?>
<style>
.nav-menu {
    background: #1a1a2e;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 25px;
}
.nav-menu a {
    color: #4a9eff;
    text-decoration: none;
    margin-right: 15px;
    font-size: 14px;
}
.nav-menu a:hover {
    color: #6bb3ff;
    text-decoration: underline;
}
.info-box {
    background: #1e3a5f;
    padding: 20px;
    border-left: 4px solid #3b82f6;
    margin: 20px 0;
    border-radius: 4px;
}
.warning-box {
    background: #78350f;
    padding: 20px;
    border-left: 4px solid #f59e0b;
    margin: 20px 0;
    border-radius: 4px;
}
.code-block {
    background: #0f172a;
    padding: 15px;
    border-radius: 4px;
    margin: 15px 0;
    overflow-x: auto;
}
.code-block code {
    color: #a5b4fc;
    font-family: 'Courier New', monospace;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    background: #1a1a2e;
}
table th {
    background: #2a2a4e;
    color: #ffffff;
    padding: 12px;
    text-align: left;
    border: 1px solid #3a3a6e;
}
table td {
    padding: 10px 12px;
    border: 1px solid #3a3a6e;
    color: #e5e7eb;
}
table tr:nth-child(even) {
    background: #222244;
}
</style>

<div class="nav-menu">
    <strong style="color: #ffffff;">Quick Navigation:</strong>
    <a href="#quick-info">Quick Info</a> |
    <a href="#ports">Ports</a> |
    <a href="#installation">Installation</a> |
    <a href="#configuration">Configuration</a> |
    <a href="#parameters">Parameters</a> |
    <a href="#troubleshooting">Troubleshooting</a> |
    <a href="#game-modes">Game Modes</a> |
    <a href="#maps">Maps</a> |
    <a href="#related-mods">Mods</a>
</div>

<h1>Day of Infamy Server Guide</h1>

<h2 id="quick-info">📋 Quick Info</h2>
<div class="info-box">
    <h3 style="color: #ffffff; margin-top: 0;">Server Specifications</h3>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">Game:</strong> Day of Infamy (Modified Source Engine)</li>
        <li><strong style="color: #ffffff;">Platform:</strong> Windows, Linux</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27015/UDP</code></li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 32 (typical: 16-32)</li>
        <li><strong style="color: #ffffff;">Control Protocol:</strong> RCON</li>
        <li><strong style="color: #ffffff;">Server Binary:</strong> srcds_run (Linux), srcds.exe (Windows)</li>
        <li><strong style="color: #ffffff;">App ID:</strong> 462310</li>
        <li><strong style="color: #ffffff;">Special Features:</strong> Squad-based combat, class system, WW2 theaters</li>
    </ul>
</div>

<h2 id="ports">🔌 Ports & Firewall Configuration</h2>
<p>Day of Infamy servers require specific ports for proper operation:</p>

<table>
    <thead>
        <tr>
            <th>Port</th>
            <th>Protocol</th>
            <th>Purpose</th>
            <th>Required</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>27015 (configurable)</td>
            <td>UDP</td>
            <td>Game port</td>
            <td>Yes</td>
        </tr>
        <tr>
            <td>27015 (same as game)</td>
            <td>TCP</td>
            <td>RCON/Source TV</td>
            <td>Optional</td>
        </tr>
        <tr>
            <td>27005</td>
            <td>UDP</td>
            <td>Client port</td>
            <td>Yes</td>
        </tr>
        <tr>
            <td>27020</td>
            <td>UDP</td>
            <td>SourceTV port</td>
            <td>Optional</td>
        </tr>
    </tbody>
</table>

<h3>Firewall Examples</h3>

<p><strong>UFW (Ubuntu/Debian):</strong></p>
<div class="code-block">
<code>sudo ufw allow 27015/udp comment 'DoI Game Port'
sudo ufw allow 27015/tcp comment 'DoI RCON'
sudo ufw allow 27005/udp comment 'DoI Client Port'
sudo ufw allow 27020/udp comment 'DoI SourceTV'</code>
</div>

<p><strong>FirewallD (CentOS/RHEL):</strong></p>
<div class="code-block">
<code>sudo firewall-cmd --permanent --add-port=27015/udp
sudo firewall-cmd --permanent --add-port=27015/tcp
sudo firewall-cmd --permanent --add-port=27005/udp
sudo firewall-cmd --permanent --add-port=27020/udp
sudo firewall-cmd --reload</code>
</div>

<p><strong>iptables:</strong></p>
<div class="code-block">
<code>iptables -A INPUT -p udp --dport 27015 -j ACCEPT
iptables -A INPUT -p tcp --dport 27015 -j ACCEPT
iptables -A INPUT -p udp --dport 27005 -j ACCEPT
iptables -A INPUT -p udp --dport 27020 -j ACCEPT</code>
</div>

<h2 id="installation">⚙️ Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Linux (Ubuntu 18.04+, Debian 9+, CentOS 7+) or Windows Server 2012+</li>
    <li><strong>CPU:</strong> 2+ cores @ 2.5GHz recommended</li>
    <li><strong>RAM:</strong> 2GB minimum, 4GB+ recommended for 32 players</li>
    <li><strong>Disk:</strong> 15GB for game files</li>
    <li><strong>Network:</strong> Stable connection, 10Mbps+ bandwidth</li>
</ul>

<h3>Installation via SteamCMD (Linux)</h3>
<ol>
    <li><strong>Install SteamCMD:</strong>
        <div class="code-block"><code>sudo apt-get install steamcmd  # Debian/Ubuntu
sudo yum install steamcmd      # CentOS/RHEL</code></div>
    </li>
    <li><strong>Run SteamCMD and install DoI server:</strong>
        <div class="code-block"><code>steamcmd +login anonymous +force_install_dir /home/steam/doi +app_update 462310 validate +quit</code></div>
    </li>
    <li><strong>Create server.cfg:</strong> Navigate to <code>/home/steam/doi/doi/cfg/</code> and create configuration file</li>
</ol>

<h3>Installation via SteamCMD (Windows)</h3>
<ol>
    <li>Download SteamCMD from Valve's website</li>
    <li>Extract to <code>C:\steamcmd\</code></li>
    <li>Run: <code>steamcmd +login anonymous +force_install_dir C:\doi +app_update 462310 validate +quit</code></li>
    <li>Create <code>server.cfg</code> in <code>C:\doi\doi\cfg\</code></li>
</ol>

<h2 id="configuration">📝 Configuration</h2>

<h3>Basic server.cfg Example</h3>
<div class="code-block">
<code>// Server Name
hostname "My Day of Infamy Server"

// RCON Password
rcon_password "your_secure_password"

// Server Password (leave blank for public)
sv_password ""

// Network Settings
sv_region 255  // 0=US East, 1=US West, 2=South America, 3=Europe, etc.
sv_contact "admin@example.com"
sv_tags "doi,hardcore,custom"

// Game Settings
mp_teamplay 1
mp_friendlyfire 1  // Friendly fire on (realistic)
mp_autokick 0
mp_autoteambalance 1
mp_limitteams 2

// Time Settings
mp_roundtime 10  // Minutes per round
mp_round_restart_delay 15  // Seconds between rounds

// Spawn Settings
mp_tkpunish 0  // Team kill punishment
mp_forcecamera 1  // Force spectator camera rules

// Hit Registration & Rates
sv_maxrate 0  // 0=unlimited
sv_minrate 20000
sv_maxupdaterate 66
sv_minupdaterate 20
sv_maxcmdrate 66
sv_mincmdrate 20

// Server Performance
sv_maxspeed 320
fps_max 300

// Logging
log on
sv_logbans 1
sv_logecho 0
sv_logfile 1
sv_log_onefile 0

// Download Settings
sv_allowdownload 1
sv_allowupload 1
sv_downloadurl ""  // FastDL URL if available

// Voice Chat
sv_voiceenable 1
sv_alltalk 0  // Team-only voice

// Gameplay Settings
doi_coop_max_waves 10  // Co-op mode wave count
doi_squad_enabled 1  // Enable squad system
doi_squad_leadership_enabled 1  // Squad leader mechanics

// Theater-specific settings
mp_theater "default"  // default, rifle_only, bolt_action, etc.

// SourceTV (optional)
tv_enable 0
tv_name "Day of Infamy TV"
tv_maxclients 4
tv_delay 30

// Map Cycle
mapcyclefile "mapcycle_default.txt"

// Execute additional configs
exec banned_user.cfg
exec banned_ip.cfg</code>
</div>

<h3>mapcycle_default.txt Example</h3>
<div class="code-block">
<code>bastogne
bocage
brenner
crete
dog_red
dunkirk
foy
ortona
salerno
sicily
stgilles</code>
</div>

<h2 id="parameters">🚀 Startup Parameters</h2>

<table>
    <thead>
        <tr>
            <th>Parameter</th>
            <th>Description</th>
            <th>Example</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>-game</td>
            <td>Specify game (doi)</td>
            <td>-game doi</td>
        </tr>
        <tr>
            <td>-port</td>
            <td>Server port</td>
            <td>-port 27015</td>
        </tr>
        <tr>
            <td>-maxplayers</td>
            <td>Maximum player slots</td>
            <td>-maxplayers 32</td>
        </tr>
        <tr>
            <td>+map</td>
            <td>Starting map</td>
            <td>+map bastogne</td>
        </tr>
        <tr>
            <td>+exec</td>
            <td>Execute config file on startup</td>
            <td>+exec server.cfg</td>
        </tr>
        <tr>
            <td>-ip</td>
            <td>Bind to specific IP</td>
            <td>-ip 192.168.1.100</td>
        </tr>
        <tr>
            <td>-console</td>
            <td>Enable console output (Windows)</td>
            <td>-console</td>
        </tr>
        <tr>
            <td>-tickrate</td>
            <td>Server tickrate (default: 66)</td>
            <td>-tickrate 100</td>
        </tr>
        <tr>
            <td>+sv_pure</td>
            <td>File consistency checking (0-2)</td>
            <td>+sv_pure 1</td>
        </tr>
    </tbody>
</table>

<h3>Example Startup Command (Linux)</h3>
<div class="code-block">
<code>./srcds_run -game doi -port 27015 -maxplayers 32 +map bastogne +exec server.cfg -tickrate 66</code>
</div>

<h3>Example Startup Command (Windows)</h3>
<div class="code-block">
<code>srcds.exe -game doi -port 27015 -maxplayers 32 +map bastogne +exec server.cfg -console -tickrate 66</code>
</div>

<h3>Example Startup Script (Linux)</h3>
<div class="code-block">
<code>#!/bin/bash
cd /home/steam/doi
./srcds_run -game doi -port 27015 -maxplayers 32 +map bastogne +exec server.cfg -tickrate 66</code>
</div>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not Appearing in Browser</h3>
<p><strong>Issue:</strong> Server not visible in the in-game server browser.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify UDP port 27015 is open in firewall</li>
    <li>Ensure <code>sv_lan 0</code> is set</li>
    <li>Check Steam master server connectivity</li>
    <li>Try direct connect using IP:PORT</li>
    <li>Validate game files via SteamCMD</li>
</ul>

<h3>Connection Issues</h3>
<p><strong>Issue:</strong> Players cannot connect or timeout.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify firewall allows traffic on game port</li>
    <li>Check server is not full (<code>maxplayers</code> limit)</li>
    <li>Disable password if testing: <code>sv_password ""</code></li>
    <li>Check <code>sv_pure</code> settings for custom content</li>
    <li>Review server logs in <code>doi/logs/</code></li>
</ul>

<h3>High Ping/Lag Issues</h3>
<p><strong>Issue:</strong> Players experiencing high latency.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Set <code>sv_maxrate 0</code> for unlimited bandwidth</li>
    <li>Increase tickrate if CPU allows: <code>-tickrate 100</code></li>
    <li>Set <code>fps_max 300</code> or higher</li>
    <li>Check server CPU usage (under 70%)</li>
    <li>Verify network bandwidth is sufficient</li>
</ul>

<h3>Squad System Issues</h3>
<p><strong>Issue:</strong> Squad mechanics not working properly.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify <code>doi_squad_enabled 1</code> in server.cfg</li>
    <li>Ensure <code>doi_squad_leadership_enabled 1</code> for squad leader features</li>
    <li>Check player counts meet minimum for squad formation</li>
    <li>Review squad-related console variables</li>
</ul>

<h3>Co-op Mode Problems</h3>
<p><strong>Issue:</strong> Co-op missions not functioning correctly.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Use co-op specific maps (prefix: <code>coop_</code>)</li>
    <li>Set <code>doi_coop_max_waves</code> appropriately</li>
    <li>Verify AI bot settings are configured</li>
    <li>Check for conflicting plugins/mods</li>
</ul>

<h3>SourceMod/Metamod Issues</h3>
<p><strong>Issue:</strong> Plugins not loading or causing crashes.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Ensure Metamod:Source is installed before SourceMod</li>
    <li>Verify plugin compatibility with Day of Infamy</li>
    <li>Check <code>addons/sourcemod/logs/</code> for errors</li>
    <li>Update SourceMod and Metamod to latest versions</li>
</ul>

<h2 id="game-modes">🎮 Game Modes</h2>
<p>Day of Infamy features multiple game modes with squad-based WW2 combat:</p>

<h3>Multiplayer Modes</h3>
<ul>
    <li><strong>Liberation:</strong> Attack and defend objectives sequentially</li>
    <li><strong>Offensive:</strong> One team attacks all objectives while defenders hold</li>
    <li><strong>Entrenchment:</strong> Defend positions against enemy waves</li>
    <li><strong>Stronghold:</strong> Capture and hold key positions</li>
</ul>

<h3>Co-op Modes</h3>
<ul>
    <li><strong>Cooperative:</strong> Team vs AI bot waves</li>
    <li><strong>Survival:</strong> Hold out against increasingly difficult bot attacks</li>
</ul>

<h3>Player Classes</h3>
<ul>
    <li><strong>Rifleman:</strong> Standard infantry with semi-auto rifle</li>
    <li><strong>Assault:</strong> Close-quarters with SMG and grenades</li>
    <li><strong>Support:</strong> Ammunition and suppression fire</li>
    <li><strong>Engineer:</strong> Repair and construction specialist</li>
    <li><strong>Machine Gunner:</strong> Heavy suppression weapon</li>
    <li><strong>Sniper:</strong> Long-range precision</li>
    <li><strong>Radioman:</strong> Artillery and air support caller</li>
</ul>

<h2 id="maps">🗺️ Default Maps</h2>

<h3>Official Maps</h3>
<ul>
    <li><strong>bastogne</strong> - Battle of the Bulge, Belgium</li>
    <li><strong>bocage</strong> - French hedgerow country</li>
    <li><strong>brenner</strong> - Alpine mountain pass</li>
    <li><strong>crete</strong> - Greek island invasion</li>
    <li><strong>dog_red</strong> - Omaha Beach landing</li>
    <li><strong>dunkirk</strong> - British evacuation</li>
    <li><strong>foy</strong> - Belgian village combat</li>
    <li><strong>ortona</strong> - Italian city battle</li>
    <li><strong>salerno</strong> - Italian coastal invasion</li>
    <li><strong>sicily</strong> - Sicilian campaign</li>
    <li><strong>stgilles</strong> - French town liberation</li>
</ul>

<h3>Co-op Maps</h3>
<ul>
    <li><strong>coop_* variants</strong> - Co-op versions of multiplayer maps</li>
    <li>Designed for players vs AI bots</li>
</ul>

<h3>Map Theaters</h3>
<ul>
    <li><strong>Western Front:</strong> France, Belgium, Netherlands</li>
    <li><strong>Mediterranean:</strong> Italy, Sicily, Greece, Crete</li>
    <li><strong>Eastern Front:</strong> (Available in DLC/expansions)</li>
</ul>

<h2 id="related-mods">🔌 Related Mods & Plugins</h2>
<p>Popular server modifications compatible with Day of Infamy:</p>
<ul>
    <li><a href="../metamodsource/">Metamod:Source</a> - Core plugin loader for Source engine games</li>
    <li><strong>SourceMod:</strong> Admin and plugin platform for Source games</li>
    <li><strong>Custom Maps:</strong> Community-created maps and theaters</li>
    <li><strong>Custom Theaters:</strong> Weapon loadout modifications</li>
    <li><strong>Bot Improvements:</strong> Enhanced AI behavior plugins</li>
</ul>

<h2>📚 Resources</h2>
<ul>
    <li><strong>SteamCMD:</strong> <a href="https://developer.valvesoftware.com/wiki/SteamCMD" target="_blank">https://developer.valvesoftware.com/wiki/SteamCMD</a></li>
    <li><strong>SourceMod:</strong> <a href="https://www.sourcemod.net/" target="_blank">https://www.sourcemod.net/</a></li>
    <li><strong>Metamod:Source:</strong> <a href="https://www.sourcemm.net/" target="_blank">https://www.sourcemm.net/</a></li>
    <li><strong>Steam Community:</strong> Workshop and guides</li>
    <li><strong>Map Resources:</strong> Steam Workshop integration</li>
</ul>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Day of Infamy uses a <strong>modified Source Engine</strong> (based on Insurgency)</li>
        <li>Maximum <strong>32 players</strong> per server (16v16)</li>
        <li><strong>Squad system</strong> is central to gameplay - configure appropriately</li>
        <li><strong>Friendly fire</strong> is recommended for realistic gameplay</li>
        <li>Co-op mode supports player vs AI bot gameplay</li>
        <li><strong>Theater system</strong> allows custom weapon loadouts</li>
        <li>Regular updates via SteamCMD recommended</li>
        <li>Steam Workshop integration for custom content</li>
        <li>Squad leaders can call artillery and air support</li>
        <li>World War 2 setting across multiple theaters of war</li>
    </ul>
</div>