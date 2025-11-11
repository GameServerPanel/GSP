<?php
/**
 * Call of Duty: Modern Warfare 2 (IW4x) Server Documentation
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

<h1>Call of Duty: Modern Warfare 2 (IW4x) Server Guide</h1>

<h2 id="quick-info">📋 Quick Info</h2>
<div class="info-box">
    <h3 style="color: #ffffff; margin-top: 0;">Server Specifications</h3>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">Game:</strong> Call of Duty: Modern Warfare 2 (2009) - IW4x Client</li>
        <li><strong style="color: #ffffff;">Platform:</strong> Windows (IW4x community project)</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">Varies (configurable)</code></li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 18 (IW4x limitation)</li>
        <li><strong style="color: #ffffff;">Control Protocol:</strong> RCON</li>
        <li><strong style="color: #ffffff;">Executable:</strong> iw4x.exe</li>
        <li><strong style="color: #ffffff;">Special:</strong> Community-maintained client with enhancements</li>
    </ul>
</div>

<h2 id="ports">🔌 Ports & Firewall Configuration</h2>
<p>IW4x Modern Warfare 2 servers use configurable ports. Default configuration typically uses:</p>

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
            <td>28960 (configurable)</td>
            <td>UDP</td>
            <td>Game port</td>
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
<code>sudo ufw allow 28960/udp comment 'MW2 IW4x Game Port'
sudo ufw allow 28960/tcp comment 'MW2 IW4x RCON'</code>
</div>

<p><strong>FirewallD (CentOS/RHEL/Fedora):</strong></p>
<div class="code-block">
<code>sudo firewall-cmd --permanent --add-port=28960/udp
sudo firewall-cmd --permanent --add-port=28960/tcp
sudo firewall-cmd --reload</code>
</div>

<p><strong>Windows Firewall:</strong></p>
<div class="code-block">
<code>netsh advfirewall firewall add rule name="MW2 IW4x UDP" dir=in action=allow protocol=UDP localport=28960
netsh advfirewall firewall add rule name="MW2 IW4x TCP" dir=in action=allow protocol=TCP localport=28960</code>
</div>

<h2 id="installation">⚙️ Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Windows 7/8/10/11 or Windows Server 2016+</li>
    <li><strong>CPU:</strong> 2+ cores @ 2.5GHz recommended</li>
    <li><strong>RAM:</strong> 2GB minimum, 4GB recommended</li>
    <li><strong>Disk:</strong> 15GB for game files</li>
    <li><strong>Network:</strong> Low latency connection, 10Mbps+ bandwidth</li>
    <li><strong>Requirements:</strong> Original MW2 game files + IW4x client</li>
</ul>

<h3>Installation Steps</h3>
<ol>
    <li><strong>Install Modern Warfare 2:</strong> You must have legitimate MW2 game files</li>
    <li><strong>Download IW4x:</strong> Obtain IW4x client from official IW4x website (https://iw4x.org)</li>
    <li><strong>Extract IW4x:</strong> Extract IW4x files into your MW2 game directory</li>
    <li><strong>Create Server Config:</strong> Create <code>server.cfg</code> in <code>userraw</code> folder</li>
    <li><strong>Configure Firewall:</strong> Allow game ports through Windows Firewall</li>
</ol>

<h2 id="configuration">📝 Configuration</h2>

<h3>Basic server.cfg Example</h3>
<div class="code-block">
<code>// Server Name
set sv_hostname "My MW2 IW4x Server"

// Network Settings
set net_port 28960

// Server Type
set dedicated 1

// Player Limits (MAX 18 for IW4x)
set sv_maxclients 18

// RCON Password
set rcon_password "your_secure_password_here"

// Game Settings
set g_gametype "war"    // dm, war, sab, koth, sd, arena, dd, ctf, oneflag
set sv_maxPing 350

// Map Rotation
set sv_mapRotation "gametype war map mp_terminal gametype war map mp_highrise gametype war map mp_rust"

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
set g_logsync 2

// Voice Chat
set sv_voice 0

// Anti-Lag
set sv_fps 20

// Auto-Balance
set scr_teambalance 1

// IW4x-Specific Settings
set party_enable 0
set party_maxplayers 18</code>
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
            <td>-dedicated</td>
            <td>Run as dedicated server</td>
            <td>-dedicated</td>
        </tr>
        <tr>
            <td>+set net_port</td>
            <td>Server port</td>
            <td>+set net_port 28960</td>
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
            <td>+map mp_terminal</td>
        </tr>
    </tbody>
</table>

<h3>Example Startup Command</h3>
<div class="code-block">
<code>iw4x.exe -dedicated +set net_port 28960 +set sv_maxclients 18 +exec server.cfg +map_rotate</code>
</div>

<h3>Example Batch File (start_server.bat)</h3>
<div class="code-block">
<code>@echo off
title MW2 IW4x Dedicated Server
iw4x.exe -dedicated +set net_port 28960 +set sv_maxclients 18 +exec server.cfg +map_rotate
pause</code>
</div>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not Appearing in Server Browser</h3>
<p><strong>Issue:</strong> Players cannot see the server in IW4x browser.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify <code>-dedicated</code> parameter is set</li>
    <li>Ensure UDP port is open in Windows Firewall</li>
    <li>Check IW4x master server connectivity</li>
    <li>Try direct connect using IP:PORT</li>
    <li>Verify latest IW4x version is installed</li>
</ul>

<h3>Server Crashes on Startup</h3>
<p><strong>Issue:</strong> Dedicated server crashes immediately after launch.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Check <code>games_mp.log</code> for error messages</li>
    <li>Verify all required MW2 game files are present</li>
    <li>Ensure <code>server.cfg</code> syntax is correct</li>
    <li>Update to latest IW4x version</li>
    <li>Verify <code>sv_maxclients</code> does not exceed 18</li>
    <li>Check that IW4x files are not corrupted</li>
</ul>

<h3>Connection Problems</h3>
<p><strong>Issue:</strong> Players cannot connect or timeout.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify firewall rules allow traffic on game port</li>
    <li>Check <code>sv_maxPing</code> setting</li>
    <li>Ensure server is not full (max 18 players)</li>
    <li>Disable password if testing: <code>set g_password ""</code></li>
    <li>Verify clients are using same IW4x version</li>
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
    <li>IW4x has a <strong>hard limit of 18 players</strong> - this is by design</li>
    <li>Do not attempt to exceed this limit as it will cause crashes</li>
    <li>This is a limitation of the IW4x engine modification</li>
</ul>

<h3>RCON Not Working</h3>
<p><strong>Issue:</strong> Cannot connect via RCON.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify <code>rcon_password</code> is set in <code>server.cfg</code></li>
    <li>Ensure TCP port is open (same port as game UDP port)</li>
    <li>Use IW4x-compatible RCON tools</li>
    <li>Test RCON from in-game console first</li>
</ul>

<h2 id="game-types">🎮 Game Types</h2>
<p>Modern Warfare 2 (IW4x) supports the following game modes:</p>
<ul>
    <li><strong>dm</strong> - Deathmatch (Free-for-all)</li>
    <li><strong>war</strong> - Team Deathmatch</li>
    <li><strong>sab</strong> - Sabotage</li>
    <li><strong>koth</strong> - Headquarters</li>
    <li><strong>sd</strong> - Search & Destroy</li>
    <li><strong>arena</strong> - Arena</li>
    <li><strong>dd</strong> - Demolition</li>
    <li><strong>ctf</strong> - Capture the Flag</li>
    <li><strong>oneflag</strong> - One Flag CTF</li>
</ul>

<h2 id="maps">🗺️ Default Maps</h2>
<p>Modern Warfare 2 includes the following multiplayer maps:</p>

<h3>Base Game Maps</h3>
<ul>
    <li><strong>mp_afghan</strong> - Afghan</li>
    <li><strong>mp_derail</strong> - Derail</li>
    <li><strong>mp_estate</strong> - Estate</li>
    <li><strong>mp_favela</strong> - Favela</li>
    <li><strong>mp_highrise</strong> - Highrise</li>
    <li><strong>mp_invasion</strong> - Invasion</li>
    <li><strong>mp_checkpoint</strong> - Karachi</li>
    <li><strong>mp_quarry</strong> - Quarry</li>
    <li><strong>mp_rundown</strong> - Rundown</li>
    <li><strong>mp_rust</strong> - Rust</li>
    <li><strong>mp_boneyard</strong> - Scrapyard</li>
    <li><strong>mp_nightshift</strong> - Skidrow</li>
    <li><strong>mp_subbase</strong> - Sub Base</li>
    <li><strong>mp_terminal</strong> - Terminal</li>
    <li><strong>mp_underpass</strong> - Underpass</li>
    <li><strong>mp_brecourt</strong> - Wasteland</li>
</ul>

<h3>DLC Maps</h3>
<ul>
    <li>Stimulus Package: Bailout, Salvage, Storm, Overgrown (CoD4 remake), Crash (CoD4 remake)</li>
    <li>Resurgence Pack: Carnival, Fuel, Trailer Park, Strike (CoD4 remake), Vacant (CoD4 remake)</li>
</ul>

<h2 id="related-mods">🔌 Related Mods & Plugins</h2>
<p>Popular server modifications compatible with IW4x:</p>
<ul>
    <li><a href="../b3/">B3 (Big Brother Bot)</a> - Python-based admin bot with RCON wrapper, player warnings, ban management, statistics tracking, and automated moderation for Call of Duty servers</li>
    <li><strong>IW4x Scripts</strong> - Custom GSC scripts for enhanced gameplay</li>
    <li><strong>Custom Maps</strong> - Community-created maps compatible with IW4x</li>
</ul>

<h2>📚 Resources</h2>
<ul>
    <li><strong>Official Website:</strong> https://iw4x.org</li>
    <li><strong>Community Discord:</strong> IW4x Discord server</li>
    <li><strong>Forums:</strong> IW4x community forums</li>
    <li><strong>Documentation:</strong> IW4x wiki and guides</li>
</ul>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>IW4x is a community-maintained client</strong> that provides dedicated server functionality and bug fixes for MW2</li>
        <li><strong>Maximum 18 players</strong> - this is a hard limit in IW4x and cannot be exceeded</li>
        <li>You <strong>must own legitimate Modern Warfare 2</strong> game files to use IW4x</li>
        <li>IW4x uses <strong>different command-line syntax</strong> than older CoD games (-dedicated flag instead of +set dedicated)</li>
        <li>IW4x includes <strong>built-in anti-cheat</strong> and security improvements</li>
        <li>Custom maps and mods are supported through IW4x</li>
        <li>Always use the <strong>latest IW4x version</strong> for best compatibility and security</li>
        <li>Regular backups recommended for server configurations</li>
        <li>IW4x is <strong>Windows-only</strong> (no Linux dedicated server)</li>
    </ul>
</div>