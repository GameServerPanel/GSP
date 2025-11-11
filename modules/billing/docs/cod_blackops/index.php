<?php
/**
 * CoD: Black Ops Server Documentation
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

<h1>Call of Duty: Black Ops Server Guide</h1>

<h2 id="quick-info">📋 Quick Info</h2>
<div class="info-box">
    <h3 style="color: #ffffff; margin-top: 0;">Server Specifications</h3>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">Game:</strong> Call of Duty: Black Ops (2010)</li>
        <li><strong style="color: #ffffff;">Platform:</strong> Windows</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">4976/UDP (configurable)</code></li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 32 (typical: 18-32)</li>
        <li><strong style="color: #ffffff;">Control Protocol:</strong> RCON</li>
        <li><strong style="color: #ffffff;">Executable:</strong> BlackOpsMP.exe</li>
        <li><strong style="color: #ffffff;">Special Features:</strong> Zombies mode, Wager matches, Theater mode</li>
    </ul>
</div>

<h2 id="ports">🔌 Ports & Firewall Configuration</h2>
<p>Call of Duty: Black Ops servers require specific ports to be open for proper operation:</p>

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
            <td>4976 (configurable)</td>
            <td>UDP</td>
            <td>Game port (default)</td>
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
<code>sudo ufw allow 4976/udp comment 'Black Ops Game Port'
sudo ufw allow 4976/tcp comment 'Black Ops RCON'</code>
</div>

<p><strong>Windows Firewall:</strong></p>
<div class="code-block">
<code>netsh advfirewall firewall add rule name="Black Ops UDP" dir=in action=allow protocol=UDP localport=4976
netsh advfirewall firewall add rule name="Black Ops TCP" dir=in action=allow protocol=TCP localport=4976</code>
</div>

<h2 id="installation">⚙️ Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Windows 7/8/10/11 or Windows Server 2016+</li>
    <li><strong>CPU:</strong> 2+ cores @ 2.5GHz recommended</li>
    <li><strong>RAM:</strong> 2GB minimum, 4GB+ recommended for 32 players</li>
    <li><strong>Disk:</strong> 20GB for game files + space for logs</li>
    <li><strong>Network:</strong> Low latency connection, 15Mbps+ bandwidth</li>
    <li><strong>Requirements:</strong> Legitimate Black Ops game files</li>
</ul>

<h3>Installation Steps</h3>
<ol>
    <li><strong>Install Black Ops:</strong> You must have legitimate Black Ops game files</li>
    <li><strong>Locate Server Files:</strong> Dedicated server files included with installation</li>
    <li><strong>Create Server Directory:</strong> Separate directory recommended</li>
    <li><strong>Create Server Config:</strong> Create <code>server.cfg</code> in appropriate folder</li>
    <li><strong>Configure Firewall:</strong> Allow game ports through Windows Firewall</li>
</ol>

<h2 id="configuration">📝 Configuration</h2>

<h3>Basic server.cfg Example (Multiplayer)</h3>
<div class="code-block">
<code>// Server Name
set sv_hostname "My Black Ops Server"

// Network Settings
set net_port 4976

// Server Type
set dedicated 2

// Player Limits (MAX 32 for Black Ops)
set sv_maxclients 32

// RCON Password
set rcon_password "your_secure_password_here"

// Game Settings
set g_gametype "war"    // dm, war, sab, koth, sd, dom, dd, ctf, hlnd, dem, gun, shrp, hldr
set sv_maxPing 350

// Map Rotation
set sv_mapRotation "gametype war map mp_cracked gametype war map mp_summit gametype war map mp_firing_range"

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
set scr_teambalance 1

// Ranked/Unranked
set scr_game_onlyparty 0</code>
</div>

<h3>Zombies Server Configuration</h3>
<div class="code-block">
<code>// Zombies Mode Server
set sv_hostname "My Zombies Server"
set g_gametype "zom"    // Zombies game type
set sv_maxclients 4    // Zombies: 4 players recommended
set sv_mapRotation "gametype zom map zombie_theater"</code>
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
            <td>Server port (default: 4976)</td>
            <td>+set net_port 4976</td>
        </tr>
        <tr>
            <td>+set sv_maxclients</td>
            <td>Maximum player slots (MAX 32)</td>
            <td>+set sv_maxclients 32</td>
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
            <td>+map mp_cracked</td>
        </tr>
    </tbody>
</table>

<h3>Example Startup Command (Multiplayer)</h3>
<div class="code-block">
<code>BlackOpsMP.exe +set dedicated 2 +set net_port 4976 +set sv_maxclients 32 +exec server.cfg +map_rotate</code>
</div>

<h3>Example Startup Command (Zombies)</h3>
<div class="code-block">
<code>BlackOpsMP.exe +set dedicated 2 +set net_port 4976 +set sv_maxclients 4 +exec zombies.cfg +map zombie_theater</code>
</div>

<h3>Example Batch File (start_server.bat)</h3>
<div class="code-block">
<code>@echo off
title Black Ops Dedicated Server
BlackOpsMP.exe +set dedicated 2 +set net_port 4976 +set sv_maxclients 32 +exec server.cfg +map_rotate
pause</code>
</div>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not Appearing in Browser</h3>
<p><strong>Issue:</strong> Players cannot see the server in Black Ops browser.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify <code>+set dedicated 2</code> is set</li>
    <li>Ensure UDP port 4976 (or your custom port) is open in firewall</li>
    <li>Try direct connect using IP:PORT</li>
    <li>Master servers may have limited connectivity</li>
</ul>

<h3>Server Crashes on Startup</h3>
<p><strong>Issue:</strong> Dedicated server crashes immediately after launch.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Check <code>games_mp.log</code> for error messages</li>
    <li>Verify all required Black Ops game files are present</li>
    <li>Ensure <code>server.cfg</code> syntax is correct</li>
    <li>Verify <code>sv_maxclients</code> does not exceed 32</li>
    <li>Remove custom mods temporarily to isolate issue</li>
</ul>

<h3>Connection Problems</h3>
<p><strong>Issue:</strong> Players cannot connect or timeout.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify firewall rules allow traffic on game port</li>
    <li>Check <code>sv_maxPing</code> setting</li>
    <li>Ensure server is not full (max 32 players for MP, 4 for Zombies)</li>
    <li>Disable password if testing: <code>set g_password ""</code></li>
    <li>Verify clients are using same Black Ops version</li>
</ul>

<h3>Zombies Mode Issues</h3>
<p><strong>Issue:</strong> Zombies servers not working properly.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify <code>g_gametype "zom"</code> is set correctly</li>
    <li>Use 4-8 player slots for zombies (4 recommended)</li>
    <li>Start with official zombies maps first</li>
    <li>Custom zombies maps may require additional files</li>
</ul>

<h3>High Lag/Ping Issues</h3>
<p><strong>Issue:</strong> Players experience significant lag.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Increase <code>sv_fps</code> setting (recommended: 20)</li>
    <li>Reduce <code>sv_maxclients</code> if server is overloaded</li>
    <li>Check server CPU and RAM usage (32 players requires resources)</li>
    <li>Verify network bandwidth is sufficient</li>
    <li>Lower <code>sv_maxPing</code> to restrict high-ping players</li>
</ul>

<h3>RCON Not Working</h3>
<p><strong>Issue:</strong> Cannot connect via RCON.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify <code>rcon_password</code> is set in <code>server.cfg</code></li>
    <li>Ensure TCP port is open (same as game UDP port)</li>
    <li>Use Black Ops-compatible RCON tools</li>
    <li>Test RCON from in-game console first</li>
</ul>

<h2 id="game-types">🎮 Game Types</h2>
<p>Call of Duty: Black Ops supports the following game modes:</p>

<h3>Standard Modes</h3>
<ul>
    <li><strong>dm</strong> - Deathmatch (Free-for-all)</li>
    <li><strong>war</strong> - Team Deathmatch</li>
    <li><strong>sab</strong> - Sabotage</li>
    <li><strong>koth</strong> - Headquarters</li>
    <li><strong>sd</strong> - Search & Destroy</li>
    <li><strong>dom</strong> - Domination</li>
    <li><strong>dd</strong> - Demolition</li>
    <li><strong>ctf</strong> - Capture the Flag</li>
</ul>

<h3>Special Modes</h3>
<ul>
    <li><strong>hlnd</strong> - One in the Chamber (Wager match)</li>
    <li><strong>dem</strong> - Sticks and Stones (Wager match)</li>
    <li><strong>gun</strong> - Gun Game (Wager match)</li>
    <li><strong>shrp</strong> - Sharpshooter (Wager match)</li>
    <li><strong>hldr</strong> - Grid</li>
    <li><strong>zom</strong> - Zombies (co-op survival mode)</li>
</ul>

<h2 id="maps">🗺️ Default Maps</h2>

<h3>Multiplayer Maps (Base Game)</h3>
<ul>
    <li><strong>mp_array</strong> - Array</li>
    <li><strong>mp_cairo</strong> - Havana</li>
    <li><strong>mp_cosmodrome</strong> - Launch</li>
    <li><strong>mp_cracked</strong> - Cracked</li>
    <li><strong>mp_crisis</strong> - Crisis</li>
    <li><strong>mp_duga</strong> - Grid</li>
    <li><strong>mp_firingrange</strong> - Firing Range</li>
    <li><strong>mp_hanoi</strong> - Hanoi</li>
    <li><strong>mp_havoc</strong> - Jungle</li>
    <li><strong>mp_mountain</strong> - Summit</li>
    <li><strong>mp_nuked</strong> - Nuketown</li>
    <li><strong>mp_radiation</strong> - Radiation</li>
    <li><strong>mp_russianbase</strong> - WMD</li>
    <li><strong>mp_villa</strong> - Villa</li>
</ul>

<h3>Zombies Maps</h3>
<ul>
    <li><strong>zombie_theater</strong> - Kino der Toten</li>
    <li><strong>zombie_pentagon</strong> - "Five" (Pentagon)</li>
    <li><strong>zombie_coast</strong> - Call of the Dead (DLC)</li>
    <li><strong>zombie_temple</strong> - Shangri-La (DLC)</li>
    <li><strong>zombie_moon</strong> - Moon (DLC)</li>
</ul>

<h3>DLC Multiplayer Maps</h3>
<ul>
    <li>First Strike: Berlin Wall, Discovery, Stadium, Kowloon</li>
    <li>Escalation: Hotel, Convoy, Stockpile, Zoo</li>
    <li>Annihilation: Hangar 18, Drive-In, Silo, Hazard</li>
    <li>Rezurrection: Nacht der Untoten, Verrückt, Shi No Numa, Der Riese (remastered zombies maps)</li>
</ul>

<h2 id="related-mods">🔌 Related Mods & Plugins</h2>
<p>Popular server modifications compatible with Black Ops:</p>
<ul>
    <li><a href="../b3/">B3 (Big Brother Bot)</a> - Python-based admin bot with RCON wrapper, player warnings, ban management, statistics tracking, and automated moderation for Call of Duty servers</li>
    <li><strong>Custom Zombies Maps</strong> - Community-created zombies content</li>
</ul>

<h2>📚 Resources</h2>
<ul>
    <li><strong>Community Forums:</strong> Black Ops modding and server communities</li>
    <li><strong>Zombies Modding:</strong> UGX Mods and other zombies communities</li>
    <li><strong>RCON Tools:</strong> B3, various web-based RCON panels</li>
    <li><strong>Key Features:</strong> Cold War setting, wager matches, extensive zombies content</li>
</ul>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Black Ops supports <strong>maximum 32 players</strong> for multiplayer (higher than MW2/MW3's 18)</li>
        <li>Zombies mode typically uses <strong>4 player slots</strong> (can go up to 8 with mods)</li>
        <li>Black Ops features <strong>Wager matches</strong> - unique competitive game modes</li>
        <li>Default port is <strong>4976</strong> (different from other CoD games' 28960)</li>
        <li>You <strong>must own legitimate Black Ops</strong> game files</li>
        <li>Black Ops is <strong>Windows-only</strong> for dedicated servers</li>
        <li>Extensive <strong>zombies content</strong> with storyline progression across maps</li>
        <li>Theater mode allows replay recording and sharing</li>
        <li>Always secure your RCON password and restrict access</li>
        <li>Regular backups recommended for server configurations and player data</li>
    </ul>
</div>