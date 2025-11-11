<?php
/**
 * Day of Defeat Source Server Documentation
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

<h1>Day of Defeat: Source Server Guide</h1>

<h2 id="quick-info">📋 Quick Info</h2>
<div class="info-box">
    <h3 style="color: #ffffff; margin-top: 0;">Server Specifications</h3>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">Game:</strong> Day of Defeat: Source (Source Engine)</li>
        <li><strong style="color: #ffffff;">Platform:</strong> Windows, Linux</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27015/UDP</code></li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 32 (typical: 16-32)</li>
        <li><strong style="color: #ffffff;">Control Protocol:</strong> RCON</li>
        <li><strong style="color: #ffffff;">Server Binary:</strong> srcds_run (Linux), srcds.exe (Windows)</li>
        <li><strong style="color: #ffffff;">App ID:</strong> 232290</li>
        <li><strong style="color: #ffffff;">Special Features:</strong> Enhanced graphics, physics, class achievements</li>
    </ul>
</div>

<h2 id="ports">🔌 Ports & Firewall Configuration</h2>
<p>Day of Defeat: Source servers require specific ports for proper operation:</p>

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
<code>sudo ufw allow 27015/udp comment 'DoD:S Game Port'
sudo ufw allow 27015/tcp comment 'DoD:S RCON'
sudo ufw allow 27005/udp comment 'DoD:S Client Port'
sudo ufw allow 27020/udp comment 'DoD:S SourceTV'</code>
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
    <li><strong>RAM:</strong> 1GB minimum, 2GB+ recommended for 32 players</li>
    <li><strong>Disk:</strong> 10GB for game files</li>
    <li><strong>Network:</strong> Stable connection, 10Mbps+ bandwidth</li>
</ul>

<h3>Installation via SteamCMD (Linux)</h3>
<ol>
    <li><strong>Install SteamCMD:</strong>
        <div class="code-block"><code>sudo apt-get install steamcmd  # Debian/Ubuntu
sudo yum install steamcmd      # CentOS/RHEL</code></div>
    </li>
    <li><strong>Run SteamCMD and install DoD:S server:</strong>
        <div class="code-block"><code>steamcmd +login anonymous +force_install_dir /home/steam/dods +app_update 232290 validate +quit</code></div>
    </li>
    <li><strong>Create server.cfg:</strong> Navigate to <code>/home/steam/dods/dod/cfg/</code> and create configuration file</li>
</ol>

<h3>Installation via SteamCMD (Windows)</h3>
<ol>
    <li>Download SteamCMD from Valve's website</li>
    <li>Extract to <code>C:\steamcmd\</code></li>
    <li>Run: <code>steamcmd +login anonymous +force_install_dir C:\dods +app_update 232290 validate +quit</code></li>
    <li>Create <code>server.cfg</code> in <code>C:\dods\dod\cfg\</code></li>
</ol>

<h2 id="configuration">📝 Configuration</h2>

<h3>Basic server.cfg Example</h3>
<div class="code-block">
<code>// Server Name
hostname "My DoD:Source Server"

// RCON Password
rcon_password "your_secure_password"

// Server Password (leave blank for public)
sv_password ""

// Network Settings
sv_region 255  // 0=US East, 1=US West, 2=South America, 3=Europe, etc.
sv_contact "admin@example.com"
sv_tags "dods,nocrits,alltalk"

// Game Settings
mp_teamplay 1
mp_friendlyfire 0
mp_autokick 1
mp_autoteambalance 1
mp_limitteams 2  // Max player difference between teams
mp_teams_unbalance_limit 2

// Time Settings
mp_timelimit 30  // Minutes per map
mp_winlimit 0
mp_maxrounds 0
mp_roundtime 5  // Minutes per round

// Hit Registration & Rates
sv_maxrate 0  // 0=unlimited, recommended for good connections
sv_minrate 5000
sv_maxupdaterate 66
sv_minupdaterate 20
sv_maxcmdrate 66
sv_mincmdrate 20

// Server Performance
sv_maxspeed 320
fps_max 600

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
sv_alltalk 0  // 0=team only, 1=everyone

// SourceTV (optional)
tv_enable 0
tv_name "DoD:Source TV"
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
<code>dod_anzio
dod_avalanche
dod_colmar
dod_donner
dod_flash
dod_jagd
dod_kalt
dod_palermo</code>
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
            <td>Specify game (dod)</td>
            <td>-game dod</td>
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
            <td>+map dod_avalanche</td>
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
<code>./srcds_run -game dod -port 27015 -maxplayers 32 +map dod_avalanche +exec server.cfg -tickrate 66</code>
</div>

<h3>Example Startup Command (Windows)</h3>
<div class="code-block">
<code>srcds.exe -game dod -port 27015 -maxplayers 32 +map dod_avalanche +exec server.cfg -console -tickrate 66</code>
</div>

<h3>Example Startup Script (Linux)</h3>
<div class="code-block">
<code>#!/bin/bash
cd /home/steam/dods
./srcds_run -game dod -port 27015 -maxplayers 32 +map dod_avalanche +exec server.cfg -tickrate 66</code>
</div>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not Appearing in Browser</h3>
<p><strong>Issue:</strong> Server not visible in the in-game server browser.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify UDP port 27015 is open in firewall</li>
    <li>Ensure <code>sv_lan 0</code> is set (not LAN-only mode)</li>
    <li>Check Steam master server is reachable</li>
    <li>Try direct connect using IP:PORT to verify server is running</li>
    <li>Validate game files via SteamCMD if corrupted</li>
</ul>

<h3>Connection Issues</h3>
<p><strong>Issue:</strong> Players cannot connect or timeout.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify firewall allows traffic on game port</li>
    <li>Check server is not full (<code>maxplayers</code> limit)</li>
    <li>Disable password if testing: <code>sv_password ""</code></li>
    <li>Check <code>sv_pure</code> settings if custom content conflicts</li>
    <li>Review server logs in <code>dod/logs/</code></li>
</ul>

<h3>High Ping/Lag Issues</h3>
<p><strong>Issue:</strong> Players experiencing high latency.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Set <code>sv_maxrate 0</code> for unlimited bandwidth</li>
    <li>Increase tickrate if CPU can handle it: <code>-tickrate 100</code></li>
    <li>Set <code>fps_max 600</code> or higher</li>
    <li>Check server CPU usage (should be under 70%)</li>
    <li>Verify network bandwidth is sufficient for player count</li>
</ul>

<h3>RCON Not Working</h3>
<p><strong>Issue:</strong> Cannot connect via RCON.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify <code>rcon_password</code> is set in <code>server.cfg</code></li>
    <li>Ensure TCP port (same as game port) is open</li>
    <li>Use Source-compatible RCON tools</li>
    <li>Test RCON from in-game console first</li>
</ul>

<h3>Server Crashes on Startup</h3>
<p><strong>Issue:</strong> Dedicated server crashes immediately.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Check <code>dod/logs/</code> for error messages</li>
    <li>Validate game files: <code>steamcmd +app_update 232290 validate +quit</code></li>
    <li>Ensure <code>server.cfg</code> syntax is correct</li>
    <li>Remove plugins temporarily (SourceMod/Metamod)</li>
    <li>On Linux, install required 32-bit libraries</li>
</ul>

<h3>SourceMod/Metamod Issues</h3>
<p><strong>Issue:</strong> Plugins not loading or causing crashes.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Ensure Metamod:Source is installed before SourceMod</li>
    <li>Verify plugin compatibility with DoD:S</li>
    <li>Check <code>addons/sourcemod/logs/</code> for errors</li>
    <li>Update SourceMod and Metamod to latest stable versions</li>
</ul>

<h2 id="game-modes">🎮 Game Modes</h2>
<p>Day of Defeat: Source features team-based World War 2 combat with objective gameplay:</p>

<ul>
    <li><strong>Territory Control:</strong> Capture and hold flag points to win rounds</li>
    <li><strong>Round-Based:</strong> Teams alternate attacking/defending objectives</li>
    <li><strong>Achievements:</strong> In-game achievement system for players</li>
</ul>

<h3>Player Classes</h3>
<ul>
    <li><strong>Rifleman:</strong> Versatile infantry with semi-automatic rifle</li>
    <li><strong>Assault:</strong> Close-quarters specialist with SMG and grenades</li>
    <li><strong>Support:</strong> Ammunition provider with automatic rifle</li>
    <li><strong>Sniper:</strong> Long-range precision with scoped rifle</li>
    <li><strong>Machine Gunner:</strong> Heavy suppression with MG42/BAR</li>
    <li><strong>Rocket:</strong> Anti-tank specialist with Panzerschreck/Bazooka</li>
</ul>

<h2 id="maps">🗺️ Default Maps</h2>

<h3>Official Maps</h3>
<ul>
    <li><strong>dod_anzio</strong> - Italian coastal assault (remake)</li>
    <li><strong>dod_avalanche</strong> - Alpine village combat (remake)</li>
    <li><strong>dod_colmar</strong> - French town battle</li>
    <li><strong>dod_donner</strong> - Mountain pass (remake)</li>
    <li><strong>dod_flash</strong> - Urban street fighting (remake)</li>
    <li><strong>dod_jagd</strong> - Forest engagement</li>
    <li><strong>dod_kalt</strong> - Winter village (remake)</li>
    <li><strong>dod_palermo</strong> - Sicilian plaza (remake)</li>
</ul>

<h3>Map Features</h3>
<ul>
    <li>Enhanced graphics and physics using Source Engine</li>
    <li>Realistic lighting and particle effects</li>
    <li>Destructible elements and dynamic objects</li>
    <li>Improved audio design with positional sound</li>
</ul>

<h2 id="related-mods">🔌 Related Mods & Plugins</h2>
<p>Popular server modifications compatible with Day of Defeat: Source:</p>
<ul>
    <li><a href="../metamodsource/">Metamod:Source</a> - Core plugin loader for Source engine games</li>
    <li><strong>SourceMod:</strong> Popular admin and plugin platform for Source games</li>
    <li><strong>Custom Maps:</strong> Extensive community map collection</li>
    <li><strong>DoD:S Stats:</strong> Advanced player statistics and ranking</li>
</ul>

<h2>📚 Resources</h2>
<ul>
    <li><strong>SteamCMD:</strong> <a href="https://developer.valvesoftware.com/wiki/SteamCMD" target="_blank">https://developer.valvesoftware.com/wiki/SteamCMD</a></li>
    <li><strong>SourceMod:</strong> <a href="https://www.sourcemod.net/" target="_blank">https://www.sourcemod.net/</a></li>
    <li><strong>Metamod:Source:</strong> <a href="https://www.sourcemm.net/" target="_blank">https://www.sourcemm.net/</a></li>
    <li><strong>Map Resources:</strong> GameBanana, Steam Workshop (if supported)</li>
</ul>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Day of Defeat: Source uses the <strong>Source Engine</strong> (enhanced graphics vs original)</li>
        <li>Maximum <strong>32 players</strong> supported per server</li>
        <li>Regular updates via SteamCMD recommended for security and bug fixes</li>
        <li><strong>SourceMod and Metamod:Source</strong> provide extensive admin features</li>
        <li>Higher system requirements than original DoD due to Source Engine</li>
        <li>Tickrate affects server performance; 66 is standard, 100 requires more CPU</li>
        <li>FastDL server recommended for custom content distribution</li>
        <li>Always secure your RCON password</li>
        <li>Achievement system requires VAC-secure server configuration</li>
        <li>World War 2 theme with Allied (US) vs Axis (German) combat</li>
    </ul>
</div>