<?php
/**
 * Call of Duty 2 Server Documentation
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

<h1>Call of Duty 2 Server Guide</h1>

<h2 id="quick-info">📋 Quick Info</h2>
<div class="info-box">
    <h3 style="color: #ffffff; margin-top: 0;">Server Specifications</h3>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">Game:</strong> Call of Duty 2 (2005)</li>
        <li><strong style="color: #ffffff;">Platform:</strong> Linux / Windows</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">28960/UDP</code></li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 64 (typical: 32-64)</li>
        <li><strong style="color: #ffffff;">Control Protocol:</strong> RCON</li>
        <li><strong style="color: #ffffff;">Executable (Linux):</strong> cod2_lnxded</li>
        <li><strong style="color: #ffffff;">Executable (Windows):</strong> CoD2MP_s.exe</li>
    </ul>
</div>

<h2 id="ports">🔌 Ports & Firewall Configuration</h2>
<p>Call of Duty 2 servers require specific ports to be open for proper operation:</p>

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
            <td>28960</td>
            <td>UDP</td>
            <td>Game port (default, configurable)</td>
            <td>Yes</td>
        </tr>
        <tr>
            <td>28960</td>
            <td>TCP</td>
            <td>RCON remote control (optional)</td>
            <td>Optional</td>
        </tr>
        <tr>
            <td>20500-20510</td>
            <td>UDP</td>
            <td>PunkBuster (if enabled)</td>
            <td>Optional</td>
        </tr>
    </tbody>
</table>

<h3>Firewall Examples</h3>

<p><strong>UFW (Ubuntu/Debian):</strong></p>
<div class="code-block">
<code>sudo ufw allow 28960/udp comment 'CoD2 Game Port'
sudo ufw allow 28960/tcp comment 'CoD2 RCON'
sudo ufw allow 20500:20510/udp comment 'CoD2 PunkBuster'</code>
</div>

<p><strong>FirewallD (CentOS/RHEL/Fedora):</strong></p>
<div class="code-block">
<code>sudo firewall-cmd --permanent --add-port=28960/udp
sudo firewall-cmd --permanent --add-port=28960/tcp
sudo firewall-cmd --permanent --add-port=20500-20510/udp
sudo firewall-cmd --reload</code>
</div>

<p><strong>iptables:</strong></p>
<div class="code-block">
<code>sudo iptables -A INPUT -p udp --dport 28960 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 28960 -j ACCEPT
sudo iptables -A INPUT -p udp --dport 20500:20510 -j ACCEPT</code>
</div>

<h2 id="installation">⚙️ Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Linux (Debian, Ubuntu, CentOS) or Windows Server 2016+</li>
    <li><strong>CPU:</strong> 2+ cores recommended</li>
    <li><strong>RAM:</strong> 2GB minimum, 4GB recommended</li>
    <li><strong>Disk:</strong> 5GB for base game + space for logs</li>
    <li><strong>Network:</strong> Low latency connection, 10Mbps+ bandwidth</li>
</ul>

<h3>Linux Installation</h3>
<ol>
    <li><strong>Download Server Files:</strong> Obtain Call of Duty 2 dedicated server files from legitimate source</li>
    <li><strong>Create Server Directory:</strong>
        <div class="code-block"><code>mkdir -p ~/cod2server
cd ~/cod2server</code></div>
    </li>
    <li><strong>Extract Files:</strong> Extract server files to the directory</li>
    <li><strong>Set Permissions:</strong>
        <div class="code-block"><code>chmod +x cod2_lnxded</code></div>
    </li>
    <li><strong>Create Configuration:</strong> Create <code>server.cfg</code> in the <code>main</code> directory</li>
</ol>

<h3>Windows Installation</h3>
<ol>
    <li>Install Call of Duty 2 dedicated server files</li>
    <li>Create a <code>server.cfg</code> file in the <code>main</code> directory</li>
    <li>Ensure Windows Firewall allows the game ports</li>
    <li>Run <code>CoD2MP_s.exe</code> with appropriate parameters</li>
</ol>

<h2 id="configuration">📝 Configuration</h2>

<h3>Basic server.cfg Example</h3>
<div class="code-block">
<code>// Server Name
set sv_hostname "My Call of Duty 2 Server"

// Network Settings
set net_ip "0.0.0.0"
set net_port 28960

// Server Type (1=LAN, 2=Internet)
set dedicated 2

// Player Limits
set sv_maxclients 32

// RCON Password
set rcon_password "your_secure_password_here"

// Game Settings
set g_gametype "dm"    // dm, tdm, sd, ctf, hq, bel
set sv_maxPing 350
set sv_minPing 0

// PunkBuster (0=off, 1=on)
set sv_punkbuster 1

// Map Rotation
set sv_mapRotation "gametype dm map mp_burgundy gametype tdm map mp_toujane gametype sd map mp_carentan"

// Password Protection (leave empty for public)
set g_password ""

// Friendly Fire
set scr_friendlyfire 1

// Kill Cam
set scr_killcam 1

// Game Log
set g_log "games_mp.log"
set g_logsync 1

// Voice Chat
set sv_voice 1
set voice_deadChat 0
set voice_global 0

// Anti-Lag
set sv_fps 20

// Downloads
set sv_allowDownload 1</code>
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
            <td>Server mode (1=LAN, 2=Internet)</td>
            <td>+set dedicated 2</td>
        </tr>
        <tr>
            <td>+set net_ip</td>
            <td>Bind to specific IP address</td>
            <td>+set net_ip "0.0.0.0"</td>
        </tr>
        <tr>
            <td>+set net_port</td>
            <td>Server port (default: 28960)</td>
            <td>+set net_port 28960</td>
        </tr>
        <tr>
            <td>+set fs_basepath</td>
            <td>Base installation directory</td>
            <td>+set fs_basepath "/home/user/cod2"</td>
        </tr>
        <tr>
            <td>+set fs_homepath</td>
            <td>Config and log directory</td>
            <td>+set fs_homepath "/home/user/.callofduty2"</td>
        </tr>
        <tr>
            <td>+set fs_game</td>
            <td>Mod folder (if using mods)</td>
            <td>+set fs_game "mods/mymod"</td>
        </tr>
        <tr>
            <td>+set sv_punkbuster</td>
            <td>Enable PunkBuster (0=off, 1=on)</td>
            <td>+set sv_punkbuster 1</td>
        </tr>
        <tr>
            <td>+exec</td>
            <td>Execute config file on startup</td>
            <td>+exec server.cfg</td>
        </tr>
        <tr>
            <td>+set rcon_password</td>
            <td>RCON password for remote admin</td>
            <td>+set rcon_password "secret123"</td>
        </tr>
        <tr>
            <td>+set sv_maxclients</td>
            <td>Maximum player slots</td>
            <td>+set sv_maxclients 32</td>
        </tr>
        <tr>
            <td>+map_rotate</td>
            <td>Start map rotation from server.cfg</td>
            <td>+map_rotate</td>
        </tr>
    </tbody>
</table>

<h3>Example Startup Command (Linux)</h3>
<div class="code-block">
<code>./cod2_lnxded +set dedicated 2 +set net_ip "0.0.0.0" +set net_port 28960 +set sv_maxclients 32 +set sv_punkbuster 1 +exec server.cfg +map_rotate</code>
</div>

<h3>Example Startup Command (Windows)</h3>
<div class="code-block">
<code>CoD2MP_s.exe +set dedicated 2 +set net_ip "0.0.0.0" +set net_port 28960 +set sv_maxclients 32 +set sv_punkbuster 1 +exec server.cfg +map_rotate</code>
</div>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not Appearing in Browser</h3>
<p><strong>Issue:</strong> Players cannot see the server in the in-game browser.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify <code>+set dedicated 2</code> is set (not 1 for LAN)</li>
    <li>Ensure UDP port 28960 is open in firewall</li>
    <li>Check master server connectivity (master servers may be outdated)</li>
    <li>Verify server is online: <code>ps aux | grep cod2</code></li>
    <li>Try direct connect using IP:PORT</li>
</ul>

<h3>Server Crashes on Startup</h3>
<p><strong>Issue:</strong> Dedicated server crashes immediately after launch.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Check <code>games_mp.log</code> for error messages</li>
    <li>Verify all required game files are present</li>
    <li>Ensure <code>server.cfg</code> syntax is correct (no typos)</li>
    <li>Remove custom mods temporarily to isolate issue</li>
    <li>On Linux, verify 32-bit libraries are installed: <code>sudo apt-get install lib32gcc1</code></li>
    <li>Check file permissions: <code>chmod +x cod2_lnxded</code></li>
</ul>

<h3>Connection Problems</h3>
<p><strong>Issue:</strong> Players cannot connect or timeout.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify firewall rules allow traffic on game port</li>
    <li>Check <code>sv_maxPing</code> setting (increase if players have high latency)</li>
    <li>Ensure server is not full (<code>sv_maxclients</code>)</li>
    <li>Disable password if testing: <code>set g_password ""</code></li>
    <li>Verify client and server game versions match</li>
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

<h3>RCON Not Working</h3>
<p><strong>Issue:</strong> Cannot connect via RCON.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify <code>rcon_password</code> is set in <code>server.cfg</code></li>
    <li>Ensure TCP port 28960 is open (in addition to UDP)</li>
    <li>Use correct RCON syntax: <code>/rcon login password</code> then <code>/rcon command</code></li>
    <li>Test RCON from server console first</li>
</ul>

<h2 id="game-types">🎮 Game Types</h2>
<p>Call of Duty 2 supports the following game modes:</p>
<ul>
    <li><strong>dm</strong> - Deathmatch (Free-for-all)</li>
    <li><strong>tdm</strong> - Team Deathmatch</li>
    <li><strong>sd</strong> - Search & Destroy</li>
    <li><strong>ctf</strong> - Capture the Flag</li>
    <li><strong>hq</strong> - Headquarters</li>
    <li><strong>bel</strong> - Behind Enemy Lines</li>
</ul>

<h2 id="maps">🗺️ Default Maps</h2>
<p>Call of Duty 2 includes the following default multiplayer maps:</p>
<ul>
    <li><strong>mp_burgundy</strong> - Burgundy (France)</li>
    <li><strong>mp_carentan</strong> - Carentan (France)</li>
    <li><strong>mp_downtown</strong> - Downtown (Russia)</li>
    <li><strong>mp_leningrad</strong> - Leningrad (Russia)</li>
    <li><strong>mp_matmata</strong> - Matmata (Tunisia)</li>
    <li><strong>mp_railyard</strong> - Railyard (Russia)</li>
    <li><strong>mp_toujane</strong> - Toujane (Tunisia)</li>
    <li><strong>mp_trainstation</strong> - Train Station (Russia)</li>
    <li><strong>mp_breakout</strong> - Breakout (Egypt)</li>
    <li><strong>mp_brecourt</strong> - Brecourt (France)</li>
    <li><strong>mp_dawnville</strong> - Dawnville (France)</li>
    <li><strong>mp_decoy</strong> - Decoy (Russia)</li>
</ul>

<h2 id="related-mods">🔌 Related Mods & Plugins</h2>
<p>Popular server modifications compatible with Call of Duty 2:</p>
<ul>
    <li><a href="../b3/">B3 (Big Brother Bot)</a> - Python-based admin bot with RCON wrapper, player warnings, ban management, statistics tracking, and automated moderation for Call of Duty servers</li>
</ul>

<h2>📚 Resources</h2>
<ul>
    <li><strong>Official Documentation:</strong> Limited (game released 2005)</li>
    <li><strong>Community Forums:</strong> Various Call of Duty dedicated server communities</li>
    <li><strong>RCON Tools:</strong> B3, CoD2RCON, RconGUI</li>
    <li><strong>Master Server:</strong> May require community master server solutions</li>
</ul>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Call of Duty 2 uses <strong>RCON protocol (old type)</strong> for remote administration</li>
        <li>Map rotation in CoD2 uses the <code>sv_mapRotation</code> variable with a specific syntax</li>
        <li>PunkBuster is optional but recommended for anti-cheat protection</li>
        <li>Master servers for CoD2 may no longer be fully operational; consider direct connect methods</li>
        <li>Always secure your RCON password and restrict access to trusted administrators only</li>
        <li>Regular backups of your server configuration are highly recommended</li>
    </ul>
</div>