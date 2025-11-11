<?php
/**
 * Fistful of Frags Server Documentation
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

<h1>Fistful of Frags Server Guide</h1>

<h2 id="quick-info">📋 Quick Info</h2>
<div class="info-box">
    <h3 style="color: #ffffff; margin-top: 0;">Server Specifications</h3>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">Game:</strong> Fistful of Frags (Wild West FPS)</li>
        <li><strong style="color: #ffffff;">Platform:</strong> Source Engine (Free-to-Play on Steam)</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27015/UDP</code></li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 32 (default 16)</li>
        <li><strong style="color: #ffffff;">Control Protocol:</strong> RCON</li>
        <li><strong style="color: #ffffff;">Server Binary:</strong> srcds_run (Linux), srcds.exe (Windows)</li>
        <li><strong style="color: #ffffff;">App ID:</strong> 295230 (server: 295240)</li>
        <li><strong style="color: #ffffff;">Special Features:</strong> Wild West weapons, unique damage model, whiskey power-ups, team elimination</li>
    </ul>
</div>

<h2 id="ports">🔌 Ports & Firewall Configuration</h2>
<p>Fistful of Frags servers require specific ports for proper operation:</p>

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
            <td>Client port (if hosting from client)</td>
            <td>No</td>
        </tr>
        <tr>
            <td>26900</td>
            <td>UDP</td>
            <td>Steam master server updater port</td>
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
<code>sudo ufw allow 27015/udp comment 'FoF Game/Query Port'
sudo ufw allow 27020/udp comment 'FoF SourceTV'
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
    <li><strong>CPU:</strong> 2.0+ GHz processor</li>
    <li><strong>RAM:</strong> 2GB minimum, 4GB recommended</li>
    <li><strong>Disk:</strong> 8GB for base installation</li>
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
    <li><strong>Download Fistful of Frags Server:</strong>
        <div class="code-block"><code>./steamcmd.sh
login anonymous
force_install_dir ./fof-server
app_update 295240 validate
quit</code></div>
    </li>
    <li><strong>Navigate to server directory:</strong>
        <div class="code-block"><code>cd fof-server</code></div>
    </li>
</ol>

<h3>Installation via SteamCMD (Windows)</h3>
<ol>
    <li>Download SteamCMD from <a href="https://steamcdn-a.akamaihd.net/client/installer/steamcmd.zip" target="_blank">Valve's website</a></li>
    <li>Extract to <code>C:\steamcmd\</code></li>
    <li>Run <code>steamcmd.exe</code> and execute:
        <div class="code-block"><code>login anonymous
force_install_dir C:\fof-server
app_update 295240 validate
quit</code></div>
    </li>
</ol>

<h2 id="configuration">📝 Configuration</h2>

<h3>server.cfg Example</h3>
<p>Create or edit <code>fof/cfg/server.cfg</code>:</p>

<div class="code-block">
<code>// Server Identity
hostname "Wild West Showdown - FoF Server"
sv_password ""                  // Leave blank for public server

// Server Rates
sv_minrate 10000
sv_maxrate 30000
sv_minupdaterate 20
sv_maxupdaterate 66
sv_mincmdrate 20
sv_maxcmdrate 66

// Server Region (see Region Codes below)
sv_region 1                     // 1 = East Coast USA

// Server Visibility
sv_lan 0                        // 1 for LAN only, 0 for internet
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
sv_tags "teamplay,nocrits,norespawntime"

// Gameplay Settings
fof_sv_maxteams 2               // Number of teams
fof_sv_adminslots 0             // Reserved admin slots
fof_sv_playerrespawn 1          // Respawn players (0 = round-based)
fof_sv_currentmode 0            // Game mode (0=Teamplay, 1=BreakBad, etc)

// Round Settings
mp_roundtime 10                 // Round time in minutes
mp_timelimit 30                 // Map time limit
mp_fraglimit 0                  // Frag limit (0 = disabled)

// Team Balance
mp_autoteambalance 1
mp_limitteams 1

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
sv_pure 0                       // File consistency checking (0=off, 1=on)
sv_consistency 0

// Exec ban files and additional configs
exec banned_user.cfg
exec banned_ip.cfg</code>
</div>

<h3>mapcycle.txt Example</h3>
<p>Create <code>fof/cfg/mapcycle.txt</code> with your map rotation:</p>
<div class="code-block">
<code>fof_depot
fof_desperados
fof_fistful
fof_mesa
fof_depot2
fof_arena
fof_falls
fof_duelfv2</code>
</div>

<h3>Region Codes</h3>
<ul>
    <li><strong>0:</strong> East Coast USA</li>
    <li><strong>1:</strong> West Coast USA</li>
    <li><strong>2:</strong> South America</li>
    <li><strong>3:</strong> Europe</li>
    <li><strong>4:</strong> Asia</li>
    <li><strong>5:</strong> Australia</li>
    <li><strong>6:</strong> Middle East</li>
    <li><strong>7:</strong> Africa</li>
    <li><strong>255:</strong> World (no specific region)</li>
</ul>

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
            <td>-game fof</td>
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
            <td>+map fof_fistful</td>
        </tr>
        <tr>
            <td>+exec</td>
            <td>Execute config file</td>
            <td>+exec server.cfg</td>
        </tr>
        <tr>
            <td>-tickrate</td>
            <td>Server tickrate (default 66)</td>
            <td>-tickrate 100</td>
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
<code>./srcds_run -game fof -console -port 27015 -maxplayers 16 +map fof_fistful +exec server.cfg -tickrate 66</code>
</div>

<h3>Example Startup Command (Windows)</h3>
<div class="code-block">
<code>srcds.exe -game fof -console -port 27015 -maxplayers 16 +map fof_fistful +exec server.cfg -tickrate 66</code>
</div>

<h3>Example Startup Script (Linux with Screen)</h3>
<div class="code-block">
<code>#!/bin/bash
cd /home/fof/fof-server
screen -dmS fof-server ./srcds_run \
  -game fof \
  -console \
  -port 27015 \
  -maxplayers 16 \
  +map fof_fistful \
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
    <li>Check that server has started successfully (no startup errors)</li>
    <li>Verify Steam Master Server port 26900 is accessible</li>
    <li>Try direct connect using IP:PORT</li>
</ul>

<h3>Connection Failed/Timeout</h3>
<p><strong>Issue:</strong> Players cannot connect to server.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify firewall allows UDP traffic on game port</li>
    <li>Check <code>sv_password</code> if server is password protected</li>
    <li>Ensure server and clients are same game version</li>
    <li>Verify <code>maxplayers</code> limit not reached</li>
    <li>Check server logs for connection errors</li>
</ul>

<h3>High Ping/Lag Issues</h3>
<p><strong>Issue:</strong> Players experiencing latency or rubber-banding.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Adjust <code>sv_maxrate</code> to match server bandwidth</li>
    <li>Set appropriate <code>sv_maxupdaterate</code> (66 recommended)</li>
    <li>Lower tickrate if server CPU can't handle it</li>
    <li>Check server CPU usage and network utilization</li>
    <li>Consider server location relative to players</li>
</ul>

<h3>Map Change Failures</h3>
<p><strong>Issue:</strong> Server crashes or hangs on map change.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify all maps in <code>mapcycle.txt</code> are installed</li>
    <li>Check for corrupted map files (revalidate with SteamCMD)</li>
    <li>Ensure sufficient server RAM available</li>
    <li>Review server logs for specific errors</li>
</ul>

<h3>SourceMod/MetaMod Issues</h3>
<p><strong>Issue:</strong> Plugins not loading or causing crashes.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Ensure SourceMod and MetaMod:Source are up to date</li>
    <li>Check plugin compatibility with FoF version</li>
    <li>Review <code>addons/sourcemod/logs</code> for errors</li>
    <li>Test plugins individually to isolate issues</li>
</ul>

<h3>Custom Content Download Problems</h3>
<p><strong>Issue:</strong> Players can't download custom maps/content.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify <code>sv_allowdownload 1</code> in server.cfg</li>
    <li>Set up FastDL with <code>sv_downloadurl</code> for large files</li>
    <li>Ensure custom content is in correct directories</li>
    <li>Check file sizes (Steam limits direct downloads)</li>
</ul>

<h2 id="game-modes">🎮 Game Modes</h2>

<h3>Teamplay</h3>
<p>Classic team deathmatch with Wild West weapons. Two teams compete to eliminate opponents.</p>
<ul>
    <li><strong>Teams:</strong> 2 (Desperados vs Vigilantes)</li>
    <li><strong>Objective:</strong> Eliminate enemy team members</li>
    <li><strong>Respawn:</strong> Configurable (instant or round-based)</li>
</ul>

<h3>Shootout (Team Elimination)</h3>
<p>Round-based elimination where dead players spectate until round ends.</p>
<ul>
    <li><strong>Teams:</strong> 2</li>
    <li><strong>Objective:</strong> Eliminate all enemy players to win round</li>
    <li><strong>Respawn:</strong> Next round only</li>
</ul>

<h3>Break Bad</h3>
<p>Free-for-all deathmatch mode. Every player for themselves.</p>
<ul>
    <li><strong>Teams:</strong> None (FFA)</li>
    <li><strong>Objective:</strong> Get most kills</li>
    <li><strong>Respawn:</strong> Instant</li>
</ul>

<h3>Elimination</h3>
<p>One life per round, team-based tactical gameplay.</p>
<ul>
    <li><strong>Teams:</strong> 2</li>
    <li><strong>Objective:</strong> Survive and eliminate opponents</li>
    <li><strong>Respawn:</strong> Next round</li>
</ul>

<h3>King of the Hill</h3>
<p>Control designated area to score points for your team.</p>
<ul>
    <li><strong>Teams:</strong> 2</li>
    <li><strong>Objective:</strong> Control the hill area</li>
    <li><strong>Respawn:</strong> Instant</li>
</ul>

<h2 id="maps">🗺️ Official Maps</h2>

<h3>Default Maps</h3>
<ul>
    <li><strong>fof_depot:</strong> Train depot with multiple buildings and cover</li>
    <li><strong>fof_desperados:</strong> Classic Western town setting</li>
    <li><strong>fof_fistful:</strong> Small arena-style map for fast action</li>
    <li><strong>fof_mesa:</strong> Desert canyon environment</li>
    <li><strong>fof_depot2:</strong> Redesigned depot with improved flow</li>
    <li><strong>fof_arena:</strong> Circular arena for elimination matches</li>
    <li><strong>fof_falls:</strong> Waterfall and bridge map</li>
    <li><strong>fof_duelfv2:</strong> Duel arena for 1v1 matches</li>
    <li><strong>fof_coyote:</strong> Ghost town with saloon</li>
    <li><strong>fof_tuscany:</strong> Italian villa setting</li>
    <li><strong>fof_blanco:</strong> White-washed Spanish fort</li>
    <li><strong>fof_campo:</strong> Desert camp with tents</li>
</ul>

<h3>Community Maps</h3>
<p>Install custom maps to <code>fof/maps/</code> directory and add to <code>mapcycle.txt</code>.</p>

<h2 id="mods">🔌 Mods & Plugins</h2>
<p>Fistful of Frags uses Source engine modding:</p>

<h3>SourceMod</h3>
<p>Required for most server plugins and administration tools.</p>
<ul>
    <li><strong>Download:</strong> <a href="https://www.sourcemod.net/" target="_blank">sourcemod.net</a></li>
    <li><strong>Installation:</strong> Extract to server root directory</li>
    <li><strong>Popular Plugins:</strong> Admin management, voting systems, stats tracking</li>
</ul>

<h3>MetaMod:Source</h3>
<p>Required by SourceMod - install first.</p>
<ul>
    <li><strong>Download:</strong> <a href="https://www.sourcemm.net/" target="_blank">sourcemm.net</a></li>
    <li><strong>Installation:</strong> Extract to server root directory</li>
</ul>

<h3>Related Game Documentation</h3>
<ul>
    <li><a href="../csgo/">Counter-Strike: Global Offensive</a> (Source engine gameplay)</li>
    <li><a href="../tf2/">Team Fortress 2</a> (Source engine modding)</li>
    <li><a href="../dods/">Day of Defeat: Source</a> (Similar era, Source engine)</li>
</ul>

<h2>👤 Admin Commands</h2>

<h3>Basic Console Commands</h3>
<div class="code-block">
<code>status                  # Show players and server info
kick [name/userid]      # Kick player
kickid [userid]         # Kick by UserID
banid [minutes] [userid]# Ban player
addip [minutes] [ip]    # Ban IP address
writeid                 # Save bans to banned_user.cfg
writeip                 # Save IP bans to banned_ip.cfg

changelevel [map]       # Change map immediately
map [map]               # Load specific map
mp_restartgame [delay]  # Restart game after delay</code>
</div>

<h3>RCON Commands (Remote)</h3>
<p>Connect via RCON tool using password set in <code>rcon_password</code>:</p>
<div class="code-block">
<code>rcon_password [password]  # Authenticate
rcon [command]            # Execute command remotely</code>
</div>

<h2>📚 Resources</h2>
<ul>
    <li><strong>Official Website:</strong> <a href="http://www.fistful-of-frags.com/" target="_blank">fistful-of-frags.com</a></li>
    <li><strong>Steam Page:</strong> <a href="https://store.steampowered.com/app/265630/" target="_blank">Free-to-Play on Steam</a></li>
    <li><strong>Wiki:</strong> Community wikis and guides</li>
    <li><strong>Forums:</strong> Steam Community forums</li>
    <li><strong>SourceMod:</strong> <a href="https://www.sourcemod.net/" target="_blank">sourcemod.net</a></li>
</ul>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Fistful of Frags is a <strong>free-to-play</strong> Wild West FPS on Source engine</li>
        <li>Uses <strong>unique damage model</strong> - headshots and limb damage matter</li>
        <li><strong>Whiskey power-up</strong> provides temporary advantages (health/damage/speed)</li>
        <li>Server requires <strong>App ID 295240</strong> (different from client 295230)</li>
        <li>Compatible with <strong>SourceMod/MetaMod</strong> for plugins and admin tools</li>
        <li><strong>Multiple game modes</strong> available (Teamplay, Shootout, Break Bad, etc.)</li>
        <li>Supports <strong>custom maps and content</strong> via workshop or manual installation</li>
        <li><strong>Tickrate</strong> configurable (66 default, 100 for competitive)</li>
        <li>Uses <strong>period-authentic weapons</strong> (revolvers, rifles, shotguns)</li>
        <li>May the fastest draw win!</li>
    </ul>
</div>