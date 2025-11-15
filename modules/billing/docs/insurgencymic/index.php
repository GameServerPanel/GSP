<?php
/**
 * Insurgency: Modern Infantry Combat Server Documentation
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
    <a href="#mods">Mods</a>
</div>

<h1>Insurgency: Modern Infantry Combat Server Guide</h1>

<h2 id="quick-info">📋 Quick Info</h2>
<div class="info-box">
    <h3 style="color: #ffffff; margin-top: 0;">Server Specifications</h3>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">Game:</strong> Insurgency: Modern Infantry Combat (Source Mod)</li>
        <li><strong style="color: #ffffff;">Platform:</strong> Source Engine (Half-Life 2 Mod)</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27015/UDP</code></li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 32 (default 16)</li>
        <li><strong style="color: #ffffff;">Control Protocol:</strong> RCON</li>
        <li><strong style="color: #ffffff;">Server Binary:</strong> srcds_run (Linux), srcds.exe (Windows)</li>
        <li><strong style="color: #ffffff;">App ID:</strong> 17705 (mod), 222880 (server files)</li>
        <li><strong style="color: #ffffff;">Special Features:</strong> Realistic tactical combat, limited HUD, weapon customization, team coordination</li>
    </ul>
</div>

<h2 id="ports">🔌 Ports & Firewall Configuration</h2>
<p>Insurgency servers require specific ports for proper operation:</p>

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
            <td>27015 (same as game port)</td>
            <td>UDP</td>
            <td>Query/RCON port</td>
            <td>Yes</td>
        </tr>
        <tr>
            <td>27005</td>
            <td>UDP</td>
            <td>Client port</td>
            <td>No</td>
        </tr>
        <tr>
            <td>26900</td>
            <td>UDP</td>
            <td>Steam master server updater</td>
            <td>No</td>
        </tr>
        <tr>
            <td>27020</td>
            <td>UDP</td>
            <td>SourceTV port (if enabled)</td>
            <td>No</td>
        </tr>
    </tbody>
</table>

<h3>Firewall Examples</h3>

<p><strong>UFW (Ubuntu/Debian):</strong></p>
<div class="code-block">
<code>sudo ufw allow 27015/udp comment 'Insurgency Game/Query Port'
sudo ufw allow 27020/udp comment 'Insurgency SourceTV'
sudo ufw allow 26900/udp comment 'Steam Master Server'</code>
</div>

<p><strong>FirewallD (CentOS/RHEL):</strong></p>
<div class="code-block">
<code>sudo firewall-cmd --permanent --add-port=27015/udp
sudo firewall-cmd --permanent --add-port=27020/udp
sudo firewall-cmd --permanent --add-port=26900/udp
sudo firewall-cmd --reload</code>
</div>

<p><strong>iptables:</strong></p>
<div class="code-block">
<code>iptables -A INPUT -p udp --dport 27015 -j ACCEPT
iptables -A INPUT -p udp --dport 27020 -j ACCEPT
iptables -A INPUT -p udp --dport 26900 -j ACCEPT</code>
</div>

<h2 id="installation">⚙️ Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Linux (Ubuntu 18.04+, Debian 9+, CentOS 7+), Windows Server 2012+</li>
    <li><strong>CPU:</strong> 2.0+ GHz processor (dual-core recommended)</li>
    <li><strong>RAM:</strong> 2GB minimum, 4GB recommended</li>
    <li><strong>Disk:</strong> 10GB for base installation + mod files</li>
    <li><strong>Network:</strong> Stable broadband connection</li>
</ul>

<h3>Installation via SteamCMD (Linux)</h3>
<ol>
    <li><strong>Install SteamCMD:</strong>
        <div class="code-block"><code>sudo apt update
sudo apt install lib32gcc1 steamcmd  # Debian/Ubuntu
# OR for manual install:
mkdir ~/steamcmd && cd ~/steamcmd
wget https://steamcdn-a.akamaihd.net/client/installer/steamcmd_linux.tar.gz
tar -xvzf steamcmd_linux.tar.gz</code></div>
    </li>
    <li><strong>Download Insurgency Server Files:</strong>
        <div class="code-block"><code>./steamcmd.sh
login anonymous
force_install_dir ./insurgency-server
app_update 222880 validate
quit</code></div>
    </li>
    <li><strong>Install Insurgency Mod:</strong>
        <div class="code-block"><code># Insurgency mod files should be placed in:
# insurgency-server/insurgency/
# Download mod content from official sources</code></div>
    </li>
</ol>

<h3>Installation via SteamCMD (Windows)</h3>
<ol>
    <li>Download SteamCMD from <a href="https://steamcdn-a.akamaihd.net/client/installer/steamcmd.zip" target="_blank">Valve's website</a></li>
    <li>Extract to <code>C:\steamcmd\</code></li>
    <li>Run <code>steamcmd.exe</code> and execute:
        <div class="code-block"><code>login anonymous
force_install_dir C:\insurgency-server
app_update 222880 validate
quit</code></div>
    </li>
</ol>

<h2 id="configuration">📝 Configuration</h2>

<h3>server.cfg Example</h3>
<p>Create or edit <code>insurgency/cfg/server.cfg</code>:</p>

<div class="code-block">
<code>// Server Identity
hostname "Tactical Insurgency Server"
sv_password ""                  // Leave blank for public server

// Server Rates
sv_minrate 10000
sv_maxrate 30000
sv_minupdaterate 20
sv_maxupdaterate 66
sv_mincmdrate 20
sv_maxcmdrate 66

// Server Region (see Region Codes)
sv_region 1                     // 1 = East Coast USA

// Server Visibility
sv_lan 0                        // 0 for internet, 1 for LAN only
sv_logbans 1
sv_logecho 1
sv_logfile 1
sv_log_onefile 0

// RCON (Remote Console)
rcon_password "your_secure_password"
sv_rcon_banpenalty 0
sv_rcon_maxfailures 5

// Contact & Tags
sv_contact "admin@example.com"
sv_tags "tactical,realistic,teamwork"

// Insurgency-Specific Settings
mp_timelimit 30                 // Time limit per map (minutes)
mp_roundtime 5                  // Round time limit (minutes)
mp_winlimit 0                   // Rounds to win (0 = disabled)

// Friendly Fire
mp_friendlyfire 1               // 1 = enabled (realistic mode)
mp_tkpunish 0                   // TK punishment

// Team Settings
mp_autoteambalance 1
mp_limitteams 1

// Gameplay
ins_bot_quota 0                 // Number of bots (0 = none)
ins_bot_difficulty 2            // Bot skill (0-4, 2 = medium)

// Voice Chat
sv_alltalk 0                    // 0 = team only, 1 = everyone
sv_voiceenable 1

// Download Settings
sv_allowdownload 1
sv_allowupload 1
sv_downloadurl ""               // FastDL URL (optional)

// Performance
sv_maxcmdrate 66
sv_maxupdaterate 66
fps_max 300

// Server Protection
sv_pure 1                       // File consistency checking
sv_consistency 1

// Exec ban files
exec banned_user.cfg
exec banned_ip.cfg</code>
</div>

<h3>mapcycle.txt Example</h3>
<p>Create <code>insurgency/cfg/mapcycle.txt</code> with your map rotation:</p>
<div class="code-block">
<code>ins_baghdad
ins_embassy
ins_ministry
ins_siege
ins_station
ins_almaden
ins_heights
ins_kashan</code>
</div>

<h3>Game Modes Configuration</h3>
<p>Set game mode in startup parameters or console:</p>
<div class="code-block">
<code>// Push Mode (attacking/defending objectives)
ins_gametype push

// Strike Mode (plant/defuse objectives)
ins_gametype strike

// Firefight Mode (capture and hold)
ins_gametype firefight

// Skirmish Mode (small team tactical)
ins_gametype skirmish</code>
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
            <td>Game directory name</td>
            <td>-game insurgency</td>
        </tr>
        <tr>
            <td>-console</td>
            <td>Enable console output</td>
            <td>-console</td>
        </tr>
        <tr>
            <td>-port</td>
            <td>Server port</td>
            <td>-port 27015</td>
        </tr>
        <tr>
            <td>-maxplayers</td>
            <td>Maximum players</td>
            <td>-maxplayers 16</td>
        </tr>
        <tr>
            <td>+map</td>
            <td>Starting map</td>
            <td>+map ins_baghdad</td>
        </tr>
        <tr>
            <td>+exec</td>
            <td>Execute config file</td>
            <td>+exec server.cfg</td>
        </tr>
        <tr>
            <td>-tickrate</td>
            <td>Server tickrate</td>
            <td>-tickrate 66</td>
        </tr>
        <tr>
            <td>-ip</td>
            <td>Bind to specific IP</td>
            <td>-ip 192.168.1.100</td>
        </tr>
        <tr>
            <td>+tv_enable</td>
            <td>Enable SourceTV</td>
            <td>+tv_enable 1</td>
        </tr>
        <tr>
            <td>+sv_lan</td>
            <td>LAN server mode</td>
            <td>+sv_lan 0</td>
        </tr>
    </tbody>
</table>

<h3>Example Startup Command (Linux)</h3>
<div class="code-block">
<code>./srcds_run -game insurgency -console -port 27015 -maxplayers 16 +map ins_baghdad +exec server.cfg -tickrate 66</code>
</div>

<h3>Example Startup Command (Windows)</h3>
<div class="code-block">
<code>srcds.exe -game insurgency -console -port 27015 -maxplayers 16 +map ins_baghdad +exec server.cfg -tickrate 66</code>
</div>

<h3>Example Startup Script (Linux with Screen)</h3>
<div class="code-block">
<code>#!/bin/bash
cd /home/insurgency/insurgency-server
screen -dmS insurgency ./srcds_run \
  -game insurgency \
  -console \
  -port 27015 \
  -maxplayers 16 \
  +map ins_baghdad \
  +exec server.cfg \
  -tickrate 66</code>
</div>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not Appearing in Browser</h3>
<p><strong>Issue:</strong> Server not visible in game server browser.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify UDP port 27015 is open in firewall</li>
    <li>Ensure <code>sv_lan 0</code> in server.cfg</li>
    <li>Check that mod files are correctly installed</li>
    <li>Verify Steam Master Server port 26900 is accessible</li>
    <li>Try direct connect using console: <code>connect IP:PORT</code></li>
</ul>

<h3>Missing Mod Content</h3>
<p><strong>Issue:</strong> Server fails to start or clients can't connect.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify Insurgency mod files are in <code>insurgency/</code> directory</li>
    <li>Check that all required materials and models are present</li>
    <li>Revalidate server files with SteamCMD</li>
    <li>Ensure Half-Life 2 base content is available</li>
</ul>

<h3>Connection Failed/Timeout</h3>
<p><strong>Issue:</strong> Players cannot connect to server.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify firewall allows UDP traffic on game port</li>
    <li>Check <code>sv_password</code> if server is password protected</li>
    <li>Ensure server and clients have matching mod versions</li>
    <li>Verify <code>maxplayers</code> limit not reached</li>
    <li>Check server logs for connection errors</li>
</ul>

<h3>High Ping/Lag Issues</h3>
<p><strong>Issue:</strong> Players experiencing latency.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Adjust <code>sv_maxrate</code> to match server bandwidth</li>
    <li>Set appropriate <code>sv_maxupdaterate</code> (66 recommended)</li>
    <li>Lower tickrate if server CPU can't handle it</li>
    <li>Check server CPU usage and network utilization</li>
    <li>Consider server location relative to player base</li>
</ul>

<h3>Bot Issues</h3>
<p><strong>Issue:</strong> Bots not working or causing problems.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Set <code>ins_bot_quota</code> to desired number (0 to disable)</li>
    <li>Adjust <code>ins_bot_difficulty</code> (0-4)</li>
    <li>Ensure bot navigation files exist for custom maps</li>
    <li>Check console for bot-related errors</li>
</ul>

<h3>SourceMod/MetaMod Issues</h3>
<p><strong>Issue:</strong> Plugins not loading correctly.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Ensure SourceMod and MetaMod:Source are up to date</li>
    <li>Verify plugin compatibility with Insurgency mod</li>
    <li>Check <code>addons/sourcemod/logs</code> for errors</li>
    <li>Test plugins individually to isolate issues</li>
</ul>

<h2 id="game-modes">🎮 Game Modes</h2>

<h3>Push Mode</h3>
<p>Objective-based attack/defense. Attackers must capture objectives in sequence while defenders hold them.</p>
<ul>
    <li><strong>Teams:</strong> Marines (attackers) vs Insurgents (defenders)</li>
    <li><strong>Objective:</strong> Capture all objectives before time expires</li>
    <li><strong>Respawn:</strong> Wave-based respawn system</li>
</ul>

<h3>Strike Mode</h3>
<p>Plant/defuse objectives similar to Counter-Strike. One team plants explosive, other team defuses.</p>
<ul>
    <li><strong>Teams:</strong> 2 sides (attackers/defenders swap)</li>
    <li><strong>Objective:</strong> Plant and defend bomb or prevent planting</li>
    <li><strong>Respawn:</strong> Round-based (no respawns during round)</li>
</ul>

<h3>Firefight Mode</h3>
<p>Capture and hold objectives to score points. Fast-paced tactical combat.</p>
<ul>
    <li><strong>Teams:</strong> 2 competing teams</li>
    <li><strong>Objective:</strong> Control objectives to earn points</li>
    <li><strong>Respawn:</strong> Quick respawn system</li>
</ul>

<h3>Skirmish Mode</h3>
<p>Small team tactical combat with limited lives. Realistic and hardcore.</p>
<ul>
    <li><strong>Teams:</strong> Small squads</li>
    <li><strong>Objective:</strong> Eliminate enemy team or capture objectives</li>
    <li><strong>Respawn:</strong> Limited or no respawns</li>
</ul>

<h2 id="maps">🗺️ Official Maps</h2>

<h3>Default Maps</h3>
<ul>
    <li><strong>ins_baghdad:</strong> Urban combat in Iraqi city streets</li>
    <li><strong>ins_embassy:</strong> Embassy siege scenario</li>
    <li><strong>ins_ministry:</strong> Government building assault</li>
    <li><strong>ins_siege:</strong> Intense urban warfare</li>
    <li><strong>ins_station:</strong> Train station combat</li>
    <li><strong>ins_almaden:</strong> Marketplace and residential areas</li>
    <li><strong>ins_heights:</strong> Multi-level urban environment</li>
    <li><strong>ins_kashan:</strong> Middle Eastern town</li>
</ul>

<h3>Community Maps</h3>
<p>Install custom maps to <code>insurgency/maps/</code> directory and add to <code>mapcycle.txt</code>.</p>

<h2 id="mods">🔌 Mods & Plugins</h2>
<p>Insurgency uses Source engine modding:</p>

<h3>SourceMod</h3>
<p>Server administration and plugin framework.</p>
<ul>
    <li><strong>Download:</strong> <a href="https://www.sourcemod.net/" target="_blank">sourcemod.net</a></li>
    <li><strong>Installation:</strong> Extract to server root directory</li>
    <li><strong>Popular Plugins:</strong> Admin tools, voting, stats tracking</li>
</ul>

<h3>MetaMod:Source</h3>
<p>Required by SourceMod - install first.</p>
<ul>
    <li><strong>Download:</strong> <a href="https://www.sourcemm.net/" target="_blank">sourcemm.net</a></li>
    <li><strong>Installation:</strong> Extract to server root directory</li>
</ul>

<h3>Related Game Documentation</h3>
<ul>
    <li><a href="../csgo/">Counter-Strike: Global Offensive</a> (Similar tactical gameplay)</li>
    <li><a href="../css/">Counter-Strike: Source</a> (Source engine)</li>
    <li><a href="../doi/">Day of Infamy</a> (Similar team-based combat)</li>
</ul>

<h2>👤 Admin Commands</h2>

<h3>Basic Console Commands</h3>
<div class="code-block">
<code>status                  # Show players and server info
kick [name/userid]      # Kick player
kickid [userid]         # Kick by UserID
banid [minutes] [userid]# Ban player
addip [minutes] [ip]    # Ban IP address

changelevel [map]       # Change map immediately
map [map]               # Load specific map
mp_restartgame [delay]  # Restart game after delay

ins_bot_add             # Add bot
ins_bot_kick            # Remove bot</code>
</div>

<h3>RCON Commands (Remote)</h3>
<p>Connect via RCON tool using password set in <code>rcon_password</code>:</p>
<div class="code-block">
<code>rcon_password [password]  # Authenticate
rcon [command]            # Execute command remotely</code>
</div>

<h2>📚 Resources</h2>
<ul>
    <li><strong>ModDB Page:</strong> Original mod information and downloads</li>
    <li><strong>Steam Community:</strong> Forums and community discussions</li>
    <li><strong>SourceMod:</strong> <a href="https://www.sourcemod.net/" target="_blank">sourcemod.net</a></li>
    <li><strong>Note:</strong> This is the original Source mod; see Insurgency (2014) standalone game for the commercial version</li>
</ul>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Insurgency: MIC is the <strong>original Source mod</strong> (2007-2014)</li>
        <li><strong>Realistic tactical combat</strong> with limited HUD and authentic weapons</li>
        <li>Requires <strong>Half-Life 2 content</strong> and Source engine server files</li>
        <li><strong>Team coordination essential</strong> - communication is key to success</li>
        <li>Compatible with <strong>SourceMod/MetaMod</strong> for plugins and admin tools</li>
        <li>Multiple game modes: <strong>Push, Strike, Firefight, Skirmish</strong></li>
        <li><strong>Limited or no HUD</strong> for realistic immersion</li>
        <li><strong>Friendly fire enabled</strong> in most configurations for realism</li>
        <li>Supports <strong>bot opponents</strong> for training or filling servers</li>
        <li>Stay tactical, communicate, and work as a team!</li>
    </ul>
</div>