<?php
/**
 * Call of Duty: Modern Warfare 3 Server Documentation
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
    <a href="#game-types">Game Types</a> |
    <a href="#maps">Maps</a> |
    <a href="#related-mods">Mods</a>
</div>

<h1>Call of Duty: Modern Warfare 3 Server Guide</h1>

<h2 id="quick-info">📋 Quick Info</h2>
<div class="info-box">
    <h3 style="color: #ffffff; margin-top: 0;">Server Specifications</h3>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">Game:</strong> Call of Duty: Modern Warfare 3 (2011)</li>
        <li><strong style="color: #ffffff;">Platform:</strong> Windows</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">Varies (configurable)</code></li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 18 (engine limitation)</li>
        <li><strong style="color: #ffffff;">Control Protocol:</strong> RCON</li>
        <li><strong style="color: #ffffff;">Executable:</strong> iw5mp_server.exe</li>
        <li><strong style="color: #ffffff;">Special:</strong> Multi-port configuration required</li>
    </ul>
</div>

<h2 id="ports">🔌 Ports & Firewall Configuration</h2>
<p>Modern Warfare 3 servers require multiple port configurations for proper operation:</p>

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
            <td>27016 (configurable)</td>
            <td>UDP</td>
            <td>Game port</td>
            <td>Yes</td>
        </tr>
        <tr>
            <td>Game port - 1</td>
            <td>UDP</td>
            <td>Query port (net_queryPort)</td>
            <td>Yes</td>
        </tr>
        <tr>
            <td>-18249</td>
            <td>UDP</td>
            <td>Auth port (net_authPort)</td>
            <td>Yes</td>
        </tr>
        <tr>
            <td>Same as game port</td>
            <td>TCP</td>
            <td>RCON remote control</td>
            <td>Optional</td>
        </tr>
    </tbody>
</table>

<h3>Firewall Examples</h3>

<p><strong>UFW (Ubuntu/Debian):</strong></p>
<div class="code-block">
<code>sudo ufw allow 27016/udp comment 'MW3 Game Port'
sudo ufw allow 27015/udp comment 'MW3 Query Port'
sudo ufw allow 27016/tcp comment 'MW3 RCON'</code>
</div>

<p><strong>Windows Firewall:</strong></p>
<div class="code-block">
<code>netsh advfirewall firewall add rule name="MW3 Game" dir=in action=allow protocol=UDP localport=27016
netsh advfirewall firewall add rule name="MW3 Query" dir=in action=allow protocol=UDP localport=27015
netsh advfirewall firewall add rule name="MW3 RCON" dir=in action=allow protocol=TCP localport=27016</code>
</div>

<h2 id="installation">⚙️ Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Windows 7/8/10/11 or Windows Server 2016+</li>
    <li><strong>CPU:</strong> 2+ cores @ 2.5GHz recommended</li>
    <li><strong>RAM:</strong> 2GB minimum, 4GB recommended</li>
    <li><strong>Disk:</strong> 20GB for game files</li>
    <li><strong>Network:</strong> Low latency connection, 10Mbps+ bandwidth</li>
    <li><strong>Requirements:</strong> Legitimate MW3 game files</li>
</ul>

<h3>Installation Steps</h3>
<ol>
    <li><strong>Install Modern Warfare 3:</strong> You must have legitimate MW3 game files</li>
    <li><strong>Locate Dedicated Server Files:</strong> Server files included with MW3 installation</li>
    <li><strong>Create Server Directory:</strong> Separate directory recommended for dedicated server</li>
    <li><strong>Create Server Config:</strong> Create <code>server.cfg</code> in <code>players2</code> folder</li>
    <li><strong>Configure Firewall:</strong> Allow required ports through Windows Firewall</li>
</ol>

<h2 id="configuration">📝 Configuration</h2>

<h3>Basic server.cfg Example</h3>
<div class="code-block">
<code>// Server Name
set sv_hostname "My MW3 Server"

// Network Settings (CRITICAL - Multi-port configuration)
set net_port 27016
set net_queryPort -1        // Auto-set to net_port - 1 (27015)
set net_masterServerPort -1 // Disable master server
set net_authPort -18249     // Auth port offset

// Server Type
set dedicated 2

// Player Limits (MAX 18 for MW3)
set sv_maxclients 18

// RCON Password
set rcon_password "your_secure_password_here"

// Game Settings
set g_gametype "war"    // dm, war, sab, koth, sd, dom, dd, ctf
set sv_maxPing 350

// Map Rotation
set sv_mapRotation "gametype war map mp_dome gametype war map mp_hardhat gametype war map mp_seatown"

// Password Protection (leave empty for public)
set g_password ""

// Friendly Fire
set scr_team_fftype 0    // 0=off, 1=on, 2=reflect

// Kill Cam
set scr_game_allowkillcam 1

// Hardcore Mode
set scr_hardcore 0

// Game Log
set g_log "games_mp.log"
set logfile 2

// Voice Chat
set sv_voice 0

// Anti-Lag
set sv_fps 20

// Auto-Balance
set scr_teambalance 1</code>
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
            <td>+set dedicated</td>
            <td>Server mode (2=Internet)</td>
            <td>+set dedicated 2</td>
        </tr>
        <tr>
            <td>+set net_port</td>
            <td>Main game port</td>
            <td>+set net_port 27016</td>
        </tr>
        <tr>
            <td>+set net_queryPort</td>
            <td>Query port (-1 for auto: port-1)</td>
            <td>+set net_queryPort -1</td>
        </tr>
        <tr>
            <td>+set net_authPort</td>
            <td>Auth port (-18249 standard)</td>
            <td>+set net_authPort -18249</td>
        </tr>
        <tr>
            <td>+set net_masterServerPort</td>
            <td>Master server port (-1 to disable)</td>
            <td>+set net_masterServerPort -1</td>
        </tr>
        <tr>
            <td>+set sv_maxclients</td>
            <td>Maximum player slots (MAX 18)</td>
            <td>+set sv_maxclients 18</td>
        </tr>
        <tr>
            <td>+set rcon_password</td>
            <td>RCON password for remote admin</td>
            <td>+set rcon_password "secret123"</td>
        </tr>
        <tr>
            <td>+exec</td>
            <td>Execute config file on startup</td>
            <td>+exec server.cfg</td>
        </tr>
        <tr>
            <td>+map_rotate</td>
            <td>Start map rotation from config</td>
            <td>+map_rotate</td>
        </tr>
        <tr>
            <td>+map</td>
            <td>Start with specific map</td>
            <td>+map mp_dome</td>
        </tr>
    </tbody>
</table>

<h3>Example Startup Command</h3>
<div class="code-block">
<code>iw5mp_server.exe +set dedicated 2 +set net_port 27016 +set net_queryPort -1 +set net_authPort -18249 +set net_masterServerPort -1 +set sv_maxclients 18 +exec server.cfg +map_rotate</code>
</div>

<h3>Example Batch File (start_server.bat)</h3>
<div class="code-block">
<code>@echo off
title MW3 Dedicated Server
iw5mp_server.exe +set dedicated 2 +set net_port 27016 +set net_queryPort -1 +set net_authPort -18249 +set net_masterServerPort -1 +set sv_maxclients 18 +exec server.cfg +map_rotate
pause</code>
</div>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not Appearing in Browser</h3>
<p><strong>Issue:</strong> Players cannot see the server in MW3 browser.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify all required ports are open (game, query, auth)</li>
    <li>Ensure <code>+set dedicated 2</code> is set</li>
    <li>Check <code>net_queryPort</code> is set to -1 (auto)</li>
    <li>Verify <code>net_authPort -18249</code> is configured</li>
    <li>Try direct connect using IP:PORT</li>
</ul>

<h3>Port Configuration Issues</h3>
<p><strong>Issue:</strong> Server fails to start or clients cannot connect.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>MW3 requires <strong>specific port configuration</strong>:</li>
    <li>Game port: Your chosen port (e.g., 27016)</li>
    <li>Query port: Set to -1 (automatically becomes game port - 1)</li>
    <li>Auth port: Must be set to -18249</li>
    <li>Master server port: Set to -1 (disabled)</li>
    <li>Verify all three ports are open in firewall</li>
</ul>

<h3>Server Crashes on Startup</h3>
<p><strong>Issue:</strong> Dedicated server crashes immediately after launch.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Check <code>games_mp.log</code> for error messages</li>
    <li>Verify all required MW3 game files are present</li>
    <li>Ensure <code>server.cfg</code> syntax is correct</li>
    <li>Verify <code>sv_maxclients</code> does not exceed 18</li>
    <li>Check that port configuration is correct</li>
</ul>

<h3>Connection Problems</h3>
<p><strong>Issue:</strong> Players cannot connect or timeout.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify firewall rules allow traffic on all required ports</li>
    <li>Check <code>sv_maxPing</code> setting</li>
    <li>Ensure server is not full (max 18 players)</li>
    <li>Disable password if testing: <code>set g_password ""</code></li>
    <li>Verify clients are using same MW3 version</li>
</ul>

<h3>High Lag/Ping Issues</h3>
<p><strong>Issue:</strong> Players experience significant lag.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Increase <code>sv_fps</code> setting (recommended: 20)</li>
    <li>Reduce <code>sv_maxclients</code> if server is overloaded</li>
    <li>Check server CPU and RAM usage</li>
    <li>Verify network bandwidth is sufficient</li>
    <li>Lower <code>sv_maxPing</code> to restrict high-ping players</li>
</ul>

<h3>Player Limit Issues</h3>
<p><strong>Issue:</strong> Cannot set more than 18 players.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>MW3 has a <strong>hard limit of 18 players</strong> - this is by design</li>
    <li>Do not attempt to exceed this limit as it will cause issues</li>
    <li>This is an engine limitation, not a configuration issue</li>
</ul>

<h3>RCON Not Working</h3>
<p><strong>Issue:</strong> Cannot connect via RCON.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify <code>rcon_password</code> is set in <code>server.cfg</code></li>
    <li>Ensure TCP port is open (same as game UDP port)</li>
    <li>Use MW3-compatible RCON tools</li>
    <li>Test RCON from in-game console first</li>
</ul>

<h2 id="game-types">🎮 Game Types</h2>
<p>Modern Warfare 3 supports the following game modes:</p>
<ul>
    <li><strong>dm</strong> - Deathmatch (Free-for-all)</li>
    <li><strong>war</strong> - Team Deathmatch</li>
    <li><strong>sab</strong> - Sabotage</li>
    <li><strong>koth</strong> - Headquarters (King of the Hill)</li>
    <li><strong>sd</strong> - Search & Destroy</li>
    <li><strong>dom</strong> - Domination</li>
    <li><strong>dd</strong> - Demolition</li>
    <li><strong>ctf</strong> - Capture the Flag</li>
</ul>

<h2 id="maps">🗺️ Default Maps</h2>
<p>Modern Warfare 3 includes the following multiplayer maps:</p>

<h3>Base Game Maps</h3>
<ul>
    <li><strong>mp_dome</strong> - Dome</li>
    <li><strong>mp_hardhat</strong> - Hardhat</li>
    <li><strong>mp_paris</strong> - Resistance</li>
    <li><strong>mp_seatown</strong> - Seatown</li>
    <li><strong>mp_bravo</strong> - Mission</li>
    <li><strong>mp_underground</strong> - Underground</li>
    <li><strong>mp_village</strong> - Village</li>
    <li><strong>mp_alpha</strong> - Lockdown</li>
    <li><strong>mp_bootleg</strong> - Bootleg</li>
    <li><strong>mp_carbon</strong> - Carbon</li>
    <li><strong>mp_exchange</strong> - Downturn</li>
    <li><strong>mp_hillside_ss</strong> - Fallen</li>
    <li><strong>mp_interchange</strong> - Interchange</li>
    <li><strong>mp_lambeth</strong> - Lambeth</li>
    <li><strong>mp_mogadishu</strong> - Bakaara</li>
    <li><strong>mp_plaza2</strong> - Arkaden</li>
</ul>

<h3>DLC Maps</h3>
<ul>
    <li>Collection 1: Liberation, Piazza, Overwatch, Black Box</li>
    <li>Collection 2: Sanctuary, Foundation, Oasis, Terminal (MW2 remake)</li>
    <li>Collection 3: Boardwalk, Parish, Off Shore, Gulch</li>
    <li>Collection 4: Intersection, Vortex, U-Turn, Lookout</li>
</ul>

<h2 id="related-mods">🔌 Related Mods & Plugins</h2>
<p>Popular server modifications compatible with MW3:</p>
<ul>
    <li><a href="../b3/">B3 (Big Brother Bot)</a> - Python-based admin bot with RCON wrapper, player warnings, ban management, statistics tracking, and automated moderation for Call of Duty servers</li>
    <li><strong>Custom GSC Scripts</strong> - Server-side gameplay modifications</li>
</ul>

<h2>📚 Resources</h2>
<ul>
    <li><strong>Community Forums:</strong> MW3 modding and server communities</li>
    <li><strong>Documentation:</strong> Limited official documentation available</li>
    <li><strong>RCON Tools:</strong> B3, various web-based RCON panels</li>
</ul>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Maximum 18 players</strong> - this is a hard engine limit and cannot be exceeded</li>
        <li>MW3 requires <strong>multi-port configuration</strong> for proper operation:
            <ul style="margin-top: 8px;">
                <li>Game port: Your chosen port</li>
                <li>Query port: -1 (auto: game port - 1)</li>
                <li>Auth port: -18249 (required)</li>
                <li>Master server port: -1 (disabled)</li>
            </ul>
        </li>
        <li>You <strong>must own legitimate Modern Warfare 3</strong> game files</li>
        <li>MW3 is <strong>Windows-only</strong> for dedicated servers</li>
        <li>The <code>players2</code> folder contains server configuration and data</li>
        <li>Always secure your RCON password and restrict access</li>
        <li>Regular backups recommended for server configurations</li>
        <li>Ensure all three required ports (game, query, auth) are open in firewall</li>
    </ul>
</div>