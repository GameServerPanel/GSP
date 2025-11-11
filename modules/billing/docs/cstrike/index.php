<?php
/**
 * Counter-Strike 1.6 Server Documentation
 * Comprehensive game server hosting guide
 */
?>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">📚 Navigation</h3>
    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
        <a href="#overview" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Overview</a>
        <a href="#ports" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">🔌 Ports</a>
        <a href="#installation" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Installation</a>
        <a href="#configuration" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">⚙️ Configuration</a>
        <a href="#parameters" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Parameters</a>
        <a href="#plugins" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Plugins & Mods</a>
        <a href="#troubleshooting" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">🔧 Troubleshooting</a>
    </div>
</div>

<h1>Counter-Strike 1.6 Server Hosting Guide</h1>

<h2 id="overview">Overview</h2>
<p>Counter-Strike 1.6 is the classic original Counter-Strike game released in 2000. Built on the GoldSrc engine (Half-Life 1 engine), it remains extremely popular with a dedicated competitive community, especially in regions like Eastern Europe, South America, and Asia.</p>

<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Quick Reference</h3>
    <ul style="color: #e5e7eb; line-height: 1.8; margin: 0;">
        <li><strong style="color: #ffffff;">Engine:</strong> GoldSrc (Half-Life 1 engine)</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27015</code> (UDP)</li>
        <li><strong style="color: #ffffff;">Minimum RAM:</strong> 256MB (very lightweight)</li>
        <li><strong style="color: #ffffff;">Recommended RAM:</strong> 512MB+</li>
        <li><strong style="color: #ffffff;">CPU:</strong> Single-core 1GHz+ (extremely low requirements)</li>
        <li><strong style="color: #ffffff;">SteamCMD App ID:</strong> 90 (hlds - Half-Life Dedicated Server)</li>
        <li><strong style="color: #ffffff;">GSLT Required:</strong> No (legacy game)</li>
        <li><strong style="color: #ffffff;">Log Files:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">cstrike/logs/</code></li>
        <li><strong style="color: #ffffff;">Main Config:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">server.cfg</code></li>
    </ul>
</div>

<h2 id="ports">🔌 Ports Required</h2>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0; background: #1e3a5f; border-radius: 8px; overflow: hidden;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff; border-bottom: 2px solid #3b82f6;">Port</th>
            <th style="padding: 12px; text-align: left; color: #ffffff; border-bottom: 2px solid #3b82f6;">Protocol</th>
            <th style="padding: 12px; text-align: left; color: #ffffff; border-bottom: 2px solid #3b82f6;">Purpose</th>
            <th style="padding: 12px; text-align: left; color: #ffffff; border-bottom: 2px solid #3b82f6;">Required</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">27015</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Main game server port (client connections + query)</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #7c2d12; padding: 4px 8px; border-radius: 3px; color: #fed7aa;">✓ Yes</span></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">27015</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #1e40af; padding: 4px 8px; border-radius: 3px; color: #dbeafe;">TCP</span></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">RCON (Remote Console) access</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #713f12; padding: 4px 8px; border-radius: 3px; color: #fef3c7;">Optional</span></td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">27020</code></td>
            <td style="padding: 12px;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span></td>
            <td style="padding: 12px;">HLTV (spectator) port</td>
            <td style="padding: 12px;"><span style="background: #713f12; padding: 4px 8px; border-radius: 3px; color: #fef3c7;">Optional</span></td>
        </tr>
    </tbody>
</table>

<h3>Firewall Configuration Examples</h3>

<h4>UFW (Ubuntu/Debian)</h4>
<pre><code>sudo ufw allow 27015/udp comment 'CS 1.6 game port'
sudo ufw allow 27015/tcp comment 'CS 1.6 RCON'
sudo ufw allow 27020/udp comment 'CS 1.6 HLTV'
sudo ufw reload
</code></pre>

<h4>FirewallD (CentOS/RHEL/Fedora)</h4>
<pre><code>sudo firewall-cmd --permanent --add-port=27015/udp --add-port=27015/tcp
sudo firewall-cmd --permanent --add-port=27020/udp
sudo firewall-cmd --reload
</code></pre>

<h4>Windows Firewall</h4>
<pre><code># Run in PowerShell as Administrator
New-NetFirewallRule -DisplayName "CS 1.6 UDP" -Direction Inbound -Protocol UDP -LocalPort 27015,27020 -Action Allow
New-NetFirewallRule -DisplayName "CS 1.6 TCP" -Direction Inbound -Protocol TCP -LocalPort 27015 -Action Allow
</code></pre>

<h4>iptables (Legacy Linux)</h4>
<pre><code>sudo iptables -A INPUT -p udp --dport 27015 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 27015 -j ACCEPT
sudo iptables -A INPUT -p udp --dport 27020 -j ACCEPT
sudo service iptables save
</code></pre>

<h2 id="installation">Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Linux (any modern distro) or Windows 2000+</li>
    <li><strong>CPU:</strong> Single-core 1GHz+ (GoldSrc engine is extremely lightweight)</li>
    <li><strong>RAM:</strong> 256MB minimum, 512MB recommended</li>
    <li><strong>Disk:</strong> 1GB for server files</li>
    <li><strong>Network:</strong> 1Mbps+ (very low bandwidth)</li>
</ul>

<h3>Installation via SteamCMD (Linux)</h3>

<h4>Install SteamCMD</h4>
<pre><code># Ubuntu/Debian
sudo add-apt-repository multiverse
sudo dpkg --add-architecture i386
sudo apt update
sudo apt install lib32gcc-s1 steamcmd

# Create steam user
sudo useradd -m -s /bin/bash steam
sudo su - steam

# CentOS/RHEL
sudo yum install glibc.i686 libstdc++.i686
mkdir ~/steamcmd && cd ~/steamcmd
wget https://steamcdn-a.akamaihd.net/client/installer/steamcmd_linux.tar.gz
tar -xvzf steamcmd_linux.tar.gz
</code></pre>

<h4>CS 1.6 Server Installation</h4>
<pre><code># Run SteamCMD
./steamcmd.sh

# Login anonymously
login anonymous

# Set install directory
force_install_dir ./cs16-server

# Install CS 1.6 dedicated server (App ID 90 - HLDS)
app_update 90 validate

# Exit
quit
</code></pre>

<h3>Windows Installation</h3>
<ol>
    <li>Download <a href="https://steamcdn-a.akamaihd.net/client/installer/steamcmd.zip" target="_blank">SteamCMD for Windows</a></li>
    <li>Extract to <code>C:\steamcmd\</code></li>
    <li>Run <code>steamcmd.exe</code></li>
    <li>Execute: <code>login anonymous</code></li>
    <li>Execute: <code>force_install_dir C:\cs16-server</code></li>
    <li>Execute: <code>app_update 90 validate</code></li>
</ol>

<h2 id="configuration">⚙️ Server Configuration</h2>

<h3>server.cfg - Essential Settings</h3>
<p>Create <code>cstrike/server.cfg</code>:</p>
<pre><code>// ========================================
// Server Information
// ========================================
hostname "My CS 1.6 Server"
sv_password ""                      // Server password (blank = public)
sv_region "1"                       // Server region

// ========================================
// RCON Configuration
// ========================================
rcon_password "YourSecurePasswordHere"  // CHANGE THIS!
sv_rcon_maxfailures 5

// ========================================
// Server Core Settings
// ========================================
sv_lan 0                            // 0=internet server, 1=LAN only
sv_cheats 0
sv_contact "admin@yoursite.com"     // Admin contact email

// ========================================
// Player Settings
// ========================================
mp_autokick 1                       // Autokick idle/teamkillers
mp_tkpunish 1                       // Punish teamkillers
mp_flashlight 1
mp_footsteps 1
mp_forcecamera 0                    // 0=free cam, 1=team only, 2=fixed
mp_fadetoblack 0

// ========================================
// Game Settings
// ========================================
mp_friendlyfire 1                   // 1=on for competitive
mp_autoteambalance 1
mp_limitteams 2
mp_buytime 0.25                     // Buy time (minutes)
mp_freezetime 6                     // Freeze time start of round
mp_c4timer 45                       // C4 bomb timer
mp_startmoney 800
mp_maxmoney 16000
mp_roundtime 5                      // Round time (minutes)
mp_timelimit 30                     // Map time limit (minutes)
mp_maxrounds 0                      // 0=unlimited

// ========================================
// Communication
// ========================================
sv_alltalk 0                        // Dead can't talk to alive
sv_voiceenable 1

// ========================================
// Logging
// ========================================
log on
sv_logbans 1
sv_logecho 1
sv_logfile 1

// ========================================
// Download Settings
// ========================================
sv_allowdownload 1
sv_allowupload 1
sv_downloadurl ""                   // FastDL URL

// ========================================
// Rates & Performance
// ========================================
sv_maxrate 20000                    // Max bandwidth per player
sv_minrate 5000                     // Min bandwidth
sv_maxupdaterate 101                // Max update rate
sv_minupdaterate 10                 // Min update rate

// ========================================
// Execute Additional Configs
// ========================================
exec banned_user.cfg
exec banned_ip.cfg
</code></pre>

<h3>mapcycle.txt</h3>
<p>Create <code>cstrike/mapcycle.txt</code> for map rotation:</p>
<pre><code>de_dust2
de_dust
de_inferno
de_nuke
de_train
de_aztec
de_cbble
cs_italy
cs_office
cs_assault
</code></pre>

<h2 id="parameters">Startup Parameters</h2>

<h3>Linux Start Script (hlds_run)</h3>
<pre><code>#!/bin/bash
# CS 1.6 Server Startup Script

cd /home/steam/cs16-server

./hlds_run \
    -game cstrike \
    -console \
    +ip 0.0.0.0 \
    +port 27015 \
    +map de_dust2 \
    -maxplayers 16 \
    +exec server.cfg \
    +rcon_password "YourPassword"
</code></pre>

<h3>Windows Startup (hlds.exe)</h3>
<pre><code>hlds.exe ^
    -game cstrike ^
    -console ^
    +ip 0.0.0.0 ^
    +port 27015 ^
    +map de_dust2 ^
    -maxplayers 16 ^
    +exec server.cfg
</code></pre>

<h3>Parameter Reference</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0; background: #1e3a5f; border-radius: 8px; overflow: hidden;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff; border-bottom: 2px solid #3b82f6;">Parameter</th>
            <th style="padding: 12px; text-align: left; color: #ffffff; border-bottom: 2px solid #3b82f6;">Description</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">-game cstrike</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Specify game directory (cstrike for CS 1.6)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">-console</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Enable console output</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">+ip 0.0.0.0</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Bind to all network interfaces</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">+port 27015</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Server port (default 27015)</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">+map de_dust2</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Starting map</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">-maxplayers 16</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Maximum player slots</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">+exec server.cfg</code></td>
            <td style="padding: 12px;">Execute server configuration file</td>
        </tr>
    </tbody>
</table>

<h2 id="plugins">Plugins & Mods</h2>

<h3>AMX Mod X</h3>
<p>AMX Mod X is the most popular plugin framework for CS 1.6 (successor to AMX Mod).</p>

<h4>Installation</h4>
<ol>
    <li>Download <a href="https://www.amxmodx.org/downloads.php" target="_blank">AMX Mod X</a> (latest stable)</li>
    <li>Extract to <code>cstrike/</code> directory</li>
    <li>Edit <code>addons/amxmodx/configs/plugins.ini</code> to enable/disable plugins</li>
    <li>Edit <code>addons/amxmodx/configs/admins.ini</code> to add admins</li>
    <li>Restart server</li>
    <li>Type <code>amxx version</code> in console to verify</li>
</ol>

<h4>Popular AMX Mod X Plugins</h4>
<ul>
    <li><strong>Admin System:</strong> Built-in admin commands and management</li>
    <li><strong>StatsX:</strong> Player statistics and rankings</li>
    <li><strong>Fun Module:</strong> Fun commands (slap, slay, noclip, etc.)</li>
    <li><strong>NextMap Chooser:</strong> Map voting system</li>
    <li><strong>Admin Votes:</strong> Voting for map/kick/ban</li>
    <li><strong>AdminChat:</strong> Admin-only chat</li>
    <li><strong>AdminCMD:</strong> Comprehensive admin commands</li>
    <li><strong>DeathMatch:</strong> Respawn mode</li>
    <li><strong>GunGame:</strong> Progressive weapon mode</li>
    <li><strong>Zombie Mod:</strong> Zombie plague gameplay</li>
</ul>

<h4>Adding Custom Plugins</h4>
<pre><code># Download .amxx file (compiled plugin)
# Place in: cstrike/addons/amxmodx/plugins/

# Add to plugins.ini:
echo "pluginname.amxx" >> cstrike/addons/amxmodx/configs/plugins.ini

# Restart server or reload plugins:
amxx plugins reload
</code></pre>

<h3>Popular Game Modes</h3>

<h4>Classic Competitive</h4>
<p>Standard 5v5 bomb defusal (already configured in server.cfg)</p>

<h4>Public Server (16+ players)</h4>
<pre><code>mp_friendlyfire 0
mp_autoteambalance 1
mp_limitteams 2
mp_roundtime 3
mp_timelimit 0
</code></pre>

<h4>GunGame (AMX Mod X Plugin)</h4>
<pre><code># Players progress through weapons
gg_enabled 1
gg_turbo 0              // Instant respawn
gg_knife_pro 1          // Final knife kill
</code></pre>

<h4>Zombie Mod (AMX Mod X Plugin)</h4>
<pre><code># Zombies vs humans
zp_enabled 1
zp_respawn_zombies 1
zp_infection_limit 0    // Max zombies to release
</code></pre>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Won't Start</h3>

<h4>Port Already in Use</h4>
<pre><code># Check what's using port 27015
sudo lsof -i :27015
# Windows:
netstat -ano | findstr :27015

# Change port
./hlds_run -game cstrike +port 27016 ...
</code></pre>

<h4>Missing Libraries (Linux)</h4>
<pre><code># Ubuntu/Debian
sudo dpkg --add-architecture i386
sudo apt update
sudo apt install lib32gcc-s1 lib32stdc++6

# CentOS/RHEL
sudo yum install glibc.i686 libstdc++.i686
</code></pre>

<h3>Connection Issues</h3>

<h4>Server Not Listed</h4>
<ul>
    <li><strong>Check sv_lan:</strong> Must be 0 (internet mode)</li>
    <li><strong>Firewall:</strong> UDP 27015 must be open</li>
    <li><strong>Direct connect:</strong> In game console: <code>connect IP:27015</code></li>
</ul>

<h4>Players Can't Connect</h4>
<pre><code># Test connectivity
nc -u -v YOUR_SERVER_IP 27015

# Check server console
status
</code></pre>

<h3>Performance Issues</h3>

<h4>High Ping</h4>
<ul>
    <li><strong>Check rates:</strong> <code>sv_maxrate 20000</code></li>
    <li><strong>Bandwidth:</strong> Ensure adequate network</li>
    <li><strong>Location:</strong> Host near players</li>
</ul>

<h4>Server Lag</h4>
<ul>
    <li><strong>Check CPU:</strong> GoldSrc is single-threaded</li>
    <li><strong>Reduce players:</strong> Lower maxplayers if needed</li>
    <li><strong>Disable plugins:</strong> Test without AMX Mod X</li>
</ul>

<h3>Plugin Issues</h3>

<h4>AMX Mod X Not Loading</h4>
<pre><code># Check installation
ls -la cstrike/addons/amxmodx/

# Check metamod
meta list

# Check logs
tail -f cstrike/addons/amxmodx/logs/error_*.log
</code></pre>

<h4>Plugin Crashes Server</h4>
<ul>
    <li><strong>Remove plugin:</strong> Comment out in <code>plugins.ini</code></li>
    <li><strong>Check compatibility:</strong> Ensure plugin supports CS 1.6</li>
    <li><strong>Update AMX Mod X:</strong> Get latest version</li>
</ul>

<h3>Custom Content Issues</h3>

<h4>Custom Maps Won't Download</h4>
<pre><code># Set up FastDL
sv_downloadurl "http://yoursite.com/cs16/"

# Organize files:
# maps/ - .bsp files
# maps/graphs/ - .txt navigation files
# Compress with bzip2 (.bsp.bz2)
</code></pre>

<h2>Performance Optimization</h2>
<ul>
    <li><strong>Lightweight engine:</strong> CS 1.6 runs well on low-end hardware</li>
    <li><strong>High clock speed CPU:</strong> GoldSrc is single-threaded</li>
    <li><strong>FastDL for maps:</strong> Offload downloads to web server</li>
    <li><strong>Limit player count:</strong> 16-32 players optimal</li>
    <li><strong>Monitor resources:</strong> <code>htop</code> on Linux</li>
</ul>

<h2>Security Best Practices</h2>
<ul>
    <li><strong>Strong RCON password:</strong> 20+ random characters</li>
    <li><strong>Firewall RCON:</strong> Whitelist admin IPs only</li>
    <li><strong>Keep updated:</strong> Run <code>app_update 90 validate</code> periodically</li>
    <li><strong>Monitor logs:</strong> Check for exploit attempts</li>
    <li><strong>AMX Mod X admins:</strong> Use Steam ID authentication</li>
</ul>

<h2>Updating Server</h2>
<pre><code># Stop server
# Linux: killall hlds_linux
# Windows: taskkill /IM hlds.exe

# Update via SteamCMD
cd /home/steam/steamcmd
./steamcmd.sh +login anonymous +force_install_dir /path/to/cs16-server +app_update 90 validate +quit

# Restart server
cd /path/to/cs16-server
./hlds_run -game cstrike +map de_dust2 ...
</code></pre>

<h2 id="related-mods">🔌 Related Mods & Plugins</h2>
<p>Popular server modifications compatible with Counter-Strike 1.6:</p>
<ul>
    <li><a href="../amxmodx/">AMX Mod X</a> - Primary plugin framework for CS 1.6 with Pawn scripting, admin system, and extensive plugin library</li>
</ul>

<h2>Additional Resources</h2>
<ul>
    <li><a href="https://developer.valvesoftware.com/wiki/Counter-Strike" target="_blank">Valve Developer Wiki - CS 1.6</a></li>
    <li><a href="https://www.amxmodx.org/" target="_blank">AMX Mod X Official Site</a></li>
    <li><a href="https://forums.alliedmods.net/forumdisplay.php?f=3" target="_blank">AMX Mod X Forums</a></li>
    <li><a href="https://github.com/GameServerManagers/LinuxGSM" target="_blank">LinuxGSM - Server Management</a></li>
    <li><a href="https://www.reddit.com/r/counterstrike/" target="_blank">r/CounterStrike Reddit</a></li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8; margin: 0;">
        <li>CS 1.6 is a <strong>classic legacy game</strong> with active competitive scene</li>
        <li>Extremely <strong>low system requirements</strong> - runs on minimal hardware</li>
        <li>AMX Mod X is the standard plugin framework (not SourceMod)</li>
        <li>Strong RCON password essential - many scanners target CS 1.6 servers</li>
        <li>FastDL recommended for custom maps to reduce bandwidth</li>
        <li>Popular in Eastern Europe, South America, and Asia regions</li>
    </ul>
</div>

<p style="text-align: center; margin-top: 30px; color: #666;">
    <em>Last updated: January 2025 | Counter-Strike 1.6 complete hosting guide</em>
</p>
