<?php
/**
 * Day of Defeat Server Documentation
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

<h1>Day of Defeat Server Guide</h1>

<h2 id="quick-info">📋 Quick Info</h2>
<div class="info-box">
    <h3 style="color: #ffffff; margin-top: 0;">Server Specifications</h3>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">Game:</strong> Day of Defeat (GoldSrc Engine)</li>
        <li><strong style="color: #ffffff;">Platform:</strong> Windows, Linux</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27015/UDP</code></li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 32 (typical: 16-32)</li>
        <li><strong style="color: #ffffff;">Control Protocol:</strong> RCON</li>
        <li><strong style="color: #ffffff;">Server Binary:</strong> hlds_run (Linux), hlds.exe (Windows)</li>
        <li><strong style="color: #ffffff;">Mod Folder:</strong> dod</li>
        <li><strong style="color: #ffffff;">Special Features:</strong> WW2 team-based combat, class system</li>
    </ul>
</div>

<h2 id="ports">🔌 Ports & Firewall Configuration</h2>
<p>Day of Defeat servers require specific ports for proper operation:</p>

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
            <td>RCON remote control</td>
            <td>Optional</td>
        </tr>
        <tr>
            <td>27005</td>
            <td>UDP</td>
            <td>Client port (outbound)</td>
            <td>Yes</td>
        </tr>
        <tr>
            <td>26900</td>
            <td>UDP</td>
            <td>Master server communication</td>
            <td>Optional</td>
        </tr>
    </tbody>
</table>

<h3>Firewall Examples</h3>

<p><strong>UFW (Ubuntu/Debian):</strong></p>
<div class="code-block">
<code>sudo ufw allow 27015/udp comment 'DoD Game Port'
sudo ufw allow 27015/tcp comment 'DoD RCON'
sudo ufw allow 27005/udp comment 'DoD Client Port'</code>
</div>

<p><strong>FirewallD (CentOS/RHEL):</strong></p>
<div class="code-block">
<code>sudo firewall-cmd --permanent --add-port=27015/udp
sudo firewall-cmd --permanent --add-port=27015/tcp
sudo firewall-cmd --permanent --add-port=27005/udp
sudo firewall-cmd --reload</code>
</div>

<p><strong>iptables:</strong></p>
<div class="code-block">
<code>iptables -A INPUT -p udp --dport 27015 -j ACCEPT
iptables -A INPUT -p tcp --dport 27015 -j ACCEPT
iptables -A INPUT -p udp --dport 27005 -j ACCEPT</code>
</div>

<h2 id="installation">⚙️ Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Linux (Ubuntu 18.04+, Debian 9+, CentOS 7+) or Windows Server</li>
    <li><strong>CPU:</strong> 1+ cores @ 2.0GHz minimum</li>
    <li><strong>RAM:</strong> 512MB minimum, 1GB+ recommended</li>
    <li><strong>Disk:</strong> 2GB for game files</li>
    <li><strong>Network:</strong> Stable connection, 5Mbps+ bandwidth</li>
</ul>

<h3>Installation via SteamCMD (Linux)</h3>
<ol>
    <li><strong>Install SteamCMD:</strong>
        <div class="code-block"><code>sudo apt-get install steamcmd  # Debian/Ubuntu
sudo yum install steamcmd      # CentOS/RHEL</code></div>
    </li>
    <li><strong>Run SteamCMD and install DoD server:</strong>
        <div class="code-block"><code>steamcmd +login anonymous +force_install_dir /home/steam/dod +app_update 90 validate +quit</code></div>
    </li>
    <li><strong>Create server.cfg:</strong> Navigate to <code>/home/steam/dod/dod/</code> and create configuration file</li>
</ol>

<h3>Installation via SteamCMD (Windows)</h3>
<ol>
    <li>Download SteamCMD from Valve's website</li>
    <li>Extract to <code>C:\steamcmd\</code></li>
    <li>Run: <code>steamcmd +login anonymous +force_install_dir C:\dod +app_update 90 validate +quit</code></li>
    <li>Create <code>server.cfg</code> in <code>C:\dod\dod\</code></li>
</ol>

<h2 id="configuration">📝 Configuration</h2>

<h3>Basic server.cfg Example</h3>
<div class="code-block">
<code>// Server Name
hostname "My Day of Defeat Server"

// RCON Password
rcon_password "your_secure_password"

// Server Password (leave blank for public)
sv_password ""

// Network Settings
sv_region 255  // 0=US East, 1=US West, 2=South America, 3=Europe, etc.
sv_contact "admin@example.com"

// Game Settings
mp_teamplay 1
mp_friendlyfire 0
mp_fraglimit 0
mp_timelimit 30  // Minutes per map
mp_maxrounds 0

// Team Balance
mp_autoteambalance 1
mp_limitteams 2  // Max player difference between teams

// Class Limits (per team)
mp_limit_allies -1    // -1 = no limit
mp_limit_axis -1

// Specific Class Limits
mp_limit_rifleman -1
mp_limit_assault -1
mp_limit_support -1
mp_limit_sniper 2      // Limit snipers
mp_limit_mg 2          // Limit machine gunners

// Spawn Settings
mp_respawnstyle 0     // 0=wave spawn, 1=instant
mp_respawndelay 0

// Hit Registration
sv_maxrate 20000
sv_minrate 5000
sv_maxupdaterate 101
sv_minupdaterate 20

// Server Performance
sv_maxspeed 320
sv_fps_max 1000

// Logging
log on
sv_logblocks 1
sv_logecho 0
sv_logfile 1
sv_log_onefile 0

// Map Cycle
mapcyclefile "mapcycle.txt"

// Execute additional configs
exec banned.cfg</code>
</div>

<h3>mapcycle.txt Example</h3>
<div class="code-block">
<code>dod_anzio
dod_avalanche
dod_caen
dod_charlie
dod_chemille
dod_donner
dod_flash
dod_forest
dod_glider
dod_kalt
dod_kraftstoff
dod_merderet
dod_northbound
dod_palermo
dod_saints
dod_sturm
dod_vicenza
dod_zalec</code>
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
            <td>Specify game mod (dod)</td>
            <td>-game dod</td>
        </tr>
        <tr>
            <td>-port</td>
            <td>Server port</td>
            <td>-port 27015</td>
        </tr>
        <tr>
            <td>+maxplayers</td>
            <td>Maximum player slots</td>
            <td>+maxplayers 32</td>
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
            <td>Enable console output</td>
            <td>-console</td>
        </tr>
        <tr>
            <td>-condebug</td>
            <td>Log console output to file</td>
            <td>-condebug</td>
        </tr>
    </tbody>
</table>

<h3>Example Startup Command (Linux)</h3>
<div class="code-block">
<code>./hlds_run -game dod -port 27015 +maxplayers 32 +map dod_avalanche +exec server.cfg -console</code>
</div>

<h3>Example Startup Command (Windows)</h3>
<div class="code-block">
<code>hlds.exe -game dod -port 27015 +maxplayers 32 +map dod_avalanche +exec server.cfg -console</code>
</div>

<h3>Example Startup Script (Linux)</h3>
<div class="code-block">
<code>#!/bin/bash
cd /home/steam/dod
./hlds_run -game dod -port 27015 +maxplayers 32 +map dod_avalanche +exec server.cfg -console</code>
</div>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not Appearing in Browser</h3>
<p><strong>Issue:</strong> Server not visible in the in-game server browser.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify UDP port 27015 is open in firewall</li>
    <li>Ensure <code>sv_lan 0</code> is set (not LAN-only mode)</li>
    <li>Check that master server communication port (26900/UDP) is open</li>
    <li>Try direct connect using IP:PORT to verify server is running</li>
    <li>Wait 5-10 minutes for server to appear in master server list</li>
</ul>

<h3>Connection Issues</h3>
<p><strong>Issue:</strong> Players cannot connect or timeout.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify firewall allows traffic on game port</li>
    <li>Check server is not full (<code>maxplayers</code> limit)</li>
    <li>Disable password if testing: <code>sv_password ""</code></li>
    <li>Verify clients have same game version</li>
    <li>Check server logs in <code>dod/logs/</code> for connection errors</li>
</ul>

<h3>High Ping/Lag Issues</h3>
<p><strong>Issue:</strong> Players experiencing high latency.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Increase <code>sv_maxrate</code> and <code>sv_maxupdaterate</code></li>
    <li>Set <code>sv_fps_max 1000</code> for smoother gameplay</li>
    <li>Check server CPU usage (should be under 50%)</li>
    <li>Verify network bandwidth is sufficient</li>
    <li>Reduce <code>maxplayers</code> if server is overloaded</li>
</ul>

<h3>RCON Not Working</h3>
<p><strong>Issue:</strong> Cannot connect via RCON.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify <code>rcon_password</code> is set in <code>server.cfg</code></li>
    <li>Ensure TCP port (same as game port) is open</li>
    <li>Use RCON tools compatible with GoldSrc/HLDS</li>
    <li>Test RCON from in-game console first</li>
</ul>

<h3>Server Crashes on Startup</h3>
<p><strong>Issue:</strong> Dedicated server crashes immediately.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Check <code>dod/logs/</code> for error messages</li>
    <li>Verify all game files are present (validate with SteamCMD)</li>
    <li>Ensure <code>server.cfg</code> syntax is correct</li>
    <li>Remove custom plugins/mods temporarily</li>
    <li>On Linux, check library dependencies: <code>ldd hlds_linux</code></li>
</ul>

<h3>Maps Not Loading</h3>
<p><strong>Issue:</strong> Server fails to load specific maps.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify map files exist in <code>dod/maps/</code></li>
    <li>Check <code>mapcycle.txt</code> for typos in map names</li>
    <li>Custom maps require .bsp, .nav, and .txt files</li>
    <li>Validate game files via SteamCMD</li>
</ul>

<h2 id="game-modes">🎮 Game Modes</h2>
<p>Day of Defeat features team-based World War 2 combat with objective-based gameplay:</p>

<ul>
    <li><strong>Territory Control:</strong> Capture and hold flag points to win</li>
    <li><strong>Round-Based:</strong> Teams alternate attacking/defending objectives</li>
    <li><strong>Team Deathmatch:</strong> Eliminate enemy players (custom servers)</li>
</ul>

<h3>Player Classes</h3>
<ul>
    <li><strong>Rifleman:</strong> Basic infantry with semi-automatic rifle</li>
    <li><strong>Assault:</strong> Close-quarters specialist with SMG</li>
    <li><strong>Support:</strong> Provides ammunition and light machine gun fire</li>
    <li><strong>Sniper:</strong> Long-range specialist with scoped rifle</li>
    <li><strong>Machine Gunner:</strong> Heavy suppression with mounted MG</li>
</ul>

<h2 id="maps">🗺️ Default Maps</h2>

<h3>Official Maps</h3>
<ul>
    <li><strong>dod_anzio</strong> - Italian beach landing</li>
    <li><strong>dod_avalanche</strong> - Mountain village combat</li>
    <li><strong>dod_caen</strong> - French city ruins</li>
    <li><strong>dod_charlie</strong> - Dense jungle warfare</li>
    <li><strong>dod_chemille</strong> - French countryside</li>
    <li><strong>dod_donner</strong> - Snowy mountain pass</li>
    <li><strong>dod_flash</strong> - Urban street combat</li>
    <li><strong>dod_forest</strong> - Woodland fighting</li>
    <li><strong>dod_glider</strong> - Crashed glider site</li>
    <li><strong>dod_kalt</strong> - Winter village</li>
    <li><strong>dod_kraftstoff</strong> - Fuel depot raid</li>
    <li><strong>dod_merderet</strong> - River crossing</li>
    <li><strong>dod_northbound</strong> - Train station assault</li>
    <li><strong>dod_palermo</strong> - Sicilian city</li>
    <li><strong>dod_saints</strong> - Church district</li>
    <li><strong>dod_sturm</strong> - Bunker assault</li>
    <li><strong>dod_vicenza</strong> - Italian plaza</li>
    <li><strong>dod_zalec</strong> - Eastern European town</li>
</ul>

<h2 id="related-mods">🔌 Related Mods & Plugins</h2>
<p>Popular server modifications compatible with Day of Defeat:</p>
<ul>
    <li><a href="../amxmodx/">AMX Mod X</a> - Popular plugin platform for Half-Life engine games with admin commands, custom plugins, and gameplay modifications</li>
    <li><a href="../metamodsource/">Metamod</a> - Core plugin loader for Source/GoldSrc engines</li>
    <li><strong>DoD Stats:</strong> Player statistics and ranking systems</li>
    <li><strong>Custom Maps:</strong> Thousands of community-created maps available</li>
</ul>

<h2>📚 Resources</h2>
<ul>
    <li><strong>SteamCMD:</strong> <a href="https://developer.valvesoftware.com/wiki/SteamCMD" target="_blank">https://developer.valvesoftware.com/wiki/SteamCMD</a></li>
    <li><strong>DoD Community:</strong> Forums and mapping communities</li>
    <li><strong>Map Resources:</strong> GameBanana, DoD-Central</li>
    <li><strong>RCON Tools:</strong> HLSW, SourceMod admin panels</li>
</ul>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Day of Defeat runs on the <strong>GoldSrc engine</strong> (original Half-Life engine)</li>
        <li>Maximum <strong>32 players</strong> supported per server</li>
        <li>Regular updates via SteamCMD recommended for security patches</li>
        <li><strong>Class limits</strong> should be configured to prevent team imbalance</li>
        <li>Custom maps require clients to download .bsp and associated files</li>
        <li>AMX Mod X provides extensive admin and gameplay features</li>
        <li>Always secure your RCON password</li>
        <li>World War 2 theme with historical Allied vs Axis combat</li>
        <li>Territory control is the primary game mode</li>
        <li>Server browser may take 5-10 minutes to update after changes</li>
    </ul>
</div>