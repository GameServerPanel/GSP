<?php
/**
 * Counter-Strike: Source Server Documentation
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

<h1>Counter-Strike: Source Server Hosting Guide</h1>

<h2 id="overview">Overview</h2>
<p>Counter-Strike: Source (CSS) is the 2004 Source engine remake of the original Counter-Strike 1.6. It remains popular with a dedicated community and is known for its smooth gameplay, extensive mod support through SourceMod/MetaMod, and active competitive scene.</p>

<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Quick Reference</h3>
    <ul style="color: #e5e7eb; line-height: 1.8; margin: 0;">
        <li><strong style="color: #ffffff;">Engine:</strong> Source Engine (v1)</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27015</code> (UDP)</li>
        <li><strong style="color: #ffffff;">Minimum RAM:</strong> 1GB</li>
        <li><strong style="color: #ffffff;">Recommended RAM:</strong> 2GB+</li>
        <li><strong style="color: #ffffff;">CPU:</strong> Dual-core 2GHz+ (Source engine legacy, less demanding than CS:GO/CS2)</li>
        <li><strong style="color: #ffffff;">SteamCMD App ID:</strong> 232330</li>
        <li><strong style="color: #ffffff;">GSLT Required:</strong> No (legacy game, optional)</li>
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
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Main game server port (client connections)</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #7c2d12; padding: 4px 8px; border-radius: 3px; color: #fed7aa;">✓ Yes</span></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">27015</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #1e40af; padding: 4px 8px; border-radius: 3px; color: #dbeafe;">TCP</span></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">RCON (Remote Console) access</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #713f12; padding: 4px 8px; border-radius: 3px; color: #fef3c7;">Optional</span></td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">27020</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">SourceTV spectator port</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #713f12; padding: 4px 8px; border-radius: 3px; color: #fef3c7;">Optional</span></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">27005</code></td>
            <td style="padding: 12px;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span></td>
            <td style="padding: 12px;">Client port (Steam connection)</td>
            <td style="padding: 12px;"><span style="background: #713f12; padding: 4px 8px; border-radius: 3px; color: #fef3c7;">Optional</span></td>
        </tr>
    </tbody>
</table>

<h3>Firewall Configuration Examples</h3>

<h4>UFW (Ubuntu/Debian)</h4>
<pre><code>sudo ufw allow 27015/udp comment 'CSS game port'
sudo ufw allow 27015/tcp comment 'CSS RCON'
sudo ufw allow 27020/udp comment 'CSS SourceTV'
sudo ufw allow 27005/udp comment 'CSS client port'
sudo ufw reload
</code></pre>

<h4>FirewallD (CentOS/RHEL/Fedora)</h4>
<pre><code>sudo firewall-cmd --permanent --add-port=27015/udp --add-port=27015/tcp
sudo firewall-cmd --permanent --add-port=27020/udp --add-port=27005/udp
sudo firewall-cmd --reload
</code></pre>

<h4>Windows Firewall</h4>
<pre><code># Run in PowerShell as Administrator
New-NetFirewallRule -DisplayName "CS:Source UDP" -Direction Inbound -Protocol UDP -LocalPort 27015,27020,27005 -Action Allow
New-NetFirewallRule -DisplayName "CS:Source TCP" -Direction Inbound -Protocol TCP -LocalPort 27015 -Action Allow
</code></pre>

<h4>iptables (Legacy Linux)</h4>
<pre><code>sudo iptables -A INPUT -p udp --dport 27015 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 27015 -j ACCEPT
sudo iptables -A INPUT -p udp --dport 27020 -j ACCEPT
sudo iptables -A INPUT -p udp --dport 27005 -j ACCEPT
sudo service iptables save
</code></pre>

<h2 id="installation">Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Linux (Ubuntu 16.04+, Debian 8+) or Windows Server 2008+</li>
    <li><strong>CPU:</strong> Dual-core 2GHz+ (CSS is less demanding than modern CS:GO/CS2)</li>
    <li><strong>RAM:</strong> 1GB minimum, 2GB+ recommended</li>
    <li><strong>Disk:</strong> 15GB for server files</li>
    <li><strong>Network:</strong> 5Mbps+ (0.5Mbps per player)</li>
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

<h4>CS:Source Server Installation</h4>
<pre><code># Run SteamCMD
./steamcmd.sh

# Login anonymously
login anonymous

# Set install directory
force_install_dir ./css-server

# Install CS:Source dedicated server (App ID 232330)
app_update 232330 validate

# Exit
quit
</code></pre>

<h3>Windows Installation</h3>
<ol>
    <li>Download <a href="https://steamcdn-a.akamaihd.net/client/installer/steamcmd.zip" target="_blank">SteamCMD for Windows</a></li>
    <li>Extract to <code>C:\steamcmd\</code></li>
    <li>Run <code>steamcmd.exe</code></li>
    <li>Execute: <code>login anonymous</code></li>
    <li>Execute: <code>force_install_dir C:\css-server</code></li>
    <li>Execute: <code>app_update 232330 validate</code></li>
</ol>

<h2 id="configuration">⚙️ Server Configuration</h2>

<h3>server.cfg - Essential Settings</h3>
<p>Create <code>cstrike/cfg/server.cfg</code>:</p>
<pre><code>// ========================================
// Server Information
// ========================================
hostname "My CS:Source Server"
sv_password ""                      // Server password (blank = public)
sv_region "1"                       // 0=US East, 1=US West, 2=SA, 3=EU, 4=Asia
sv_tags "classic,source"            // Server browser tags

// ========================================
// RCON Configuration
// ========================================
rcon_password "YourSecurePasswordHere"  // CHANGE THIS!
sv_rcon_banpenalty 0
sv_rcon_maxfailures 5

// ========================================
// Server Core Settings
// ========================================
sv_cheats 0
sv_lan 0
sv_pure 1                           // File consistency (0=off, 1=on, 2=strict)
sv_pure_kick_clients 1
sv_minrate 10000                    // Minimum bandwidth rate
sv_maxrate 0                        // Maximum bandwidth (0=unlimited)
sv_mincmdrate 66                    // Min client update rate
sv_maxcmdrate 100                   // Max client update rate
sv_minupdaterate 66                 // Min server update rate
sv_maxupdaterate 100                // Max server update rate

// ========================================
// Game Settings
// ========================================
mp_friendlyfire 0                   // 0=off, 1=on
mp_autoteambalance 1
mp_limitteams 1
mp_buytime 0.25                     // Buy time (minutes)
mp_freezetime 6                     // Freeze time (seconds)
mp_c4timer 45                       // C4 bomb timer
mp_startmoney 800
mp_maxmoney 16000
mp_roundtime 5                      // Round time (minutes)
mp_timelimit 30                     // Map time limit (minutes)
mp_maxrounds 0                      // 0=unlimited

// ========================================
// Team Settings
// ========================================
mp_autokick 1                       // Autokick idle/teamkillers
mp_tkpunish 1                       // Punish teamkillers
mp_flashlight 1                     // Allow flashlight
mp_footsteps 1                      // Enable footsteps
mp_forcecamera 1                    // 0=free, 1=team only, 2=fixed
mp_fadetoblack 0                    // Screen fades to black on death

// ========================================
// Communication
// ========================================
sv_alltalk 0                        // Dead can't talk to alive
sv_deadtalk 0                       // Dead can't be heard
sv_voiceenable 1                    // Enable voice chat

// ========================================
// SourceTV Configuration
// ========================================
tv_enable 1
tv_delay 30                         // 30 second delay
tv_advertise_watchable 1
tv_name "SourceTV"
tv_title "Source TV"
tv_autorecord 0                     // Auto-record demos
tv_maxclients 5                     // Max SourceTV spectators

// ========================================
// Logging
// ========================================
log on
sv_logbans 1
sv_logecho 1
sv_logfile 1
sv_log_onefile 0                    // New log file each map

// ========================================
// Download & FastDL
// ========================================
sv_allowdownload 1                  // Allow clients to download files
sv_allowupload 1
sv_downloadurl ""                   // FastDL URL (e.g., http://yoursite.com/css/)

// ========================================
// Execute Additional Configs
// ========================================
exec banned_user.cfg
exec banned_ip.cfg
</code></pre>

<h2 id="parameters">Startup Parameters</h2>

<h3>Linux Start Script (srcds_run)</h3>
<pre><code>#!/bin/bash
# CS:Source Server Startup Script

cd /home/steam/css-server

./srcds_run \
    -game cstrike \
    -console \
    -usercon \
    +ip 0.0.0.0 \
    -port 27015 \
    +map de_dust2 \
    -maxplayers 16 \
    -autoupdate \
    -steam_dir /home/steam/steamcmd \
    -steamcmd_script /home/steam/steamcmd/steamcmd.sh \
    +exec server.cfg \
    +tv_port 27020
</code></pre>

<h3>Windows Startup (srcds.exe)</h3>
<pre><code>srcds.exe ^
    -game cstrike ^
    -console ^
    -usercon ^
    +ip 0.0.0.0 ^
    -port 27015 ^
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
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Specify game directory (cstrike for CS:Source)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">-console</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Enable console output</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">-usercon</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Enable RCON (remote console)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">+ip 0.0.0.0</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Bind to all network interfaces</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">-port 27015</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Server port (default 27015)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">+map de_dust2</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Starting map</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">-maxplayers 16</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Maximum player slots</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">-autoupdate</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Automatically update server on restart</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">+exec server.cfg</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Execute server configuration file</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">+tv_port 27020</code></td>
            <td style="padding: 12px;">SourceTV port</td>
        </tr>
    </tbody>
</table>

<h2 id="plugins">Plugins & Mods</h2>

<h3>SourceMod & MetaMod:Source</h3>
<p>CS:Source has the most mature SourceMod plugin ecosystem. Thousands of plugins available.</p>

<h4>Installation</h4>
<ol>
    <li>Download <a href="https://www.metamodsource.net/downloads.php" target="_blank">MetaMod:Source</a> (latest stable)</li>
    <li>Download <a href="https://www.sourcemod.net/downloads.php" target="_blank">SourceMod</a> (latest stable)</li>
    <li>Extract both to <code>cstrike/</code> directory</li>
    <li>Restart server</li>
    <li>Type <code>sm version</code> in console to verify</li>
</ol>

<h4>Popular Plugins for CS:Source</h4>
<ul>
    <li><strong>Admin System:</strong> Built-in admin management</li>
    <li><strong>GunGame:</strong> Progressive weapon mode</li>
    <li><strong>Zombie Mod:</strong> Zombies vs humans gameplay</li>
    <li><strong>Deathmatch:</strong> Respawn, weapon menus, spawn protection</li>
    <li><strong>Surf Timer:</strong> Surfing map timers and rankings</li>
    <li><strong>Jail Break:</strong> Prison-themed gamemode</li>
    <li><strong>Hide and Seek:</strong> Props vs seekers</li>
    <li><strong>MapChooser Extended:</strong> Advanced map voting</li>
    <li><strong>RankMe:</strong> Player statistics and ranking</li>
    <li><strong>Chat Processor:</strong> Custom chat colors and tags</li>
</ul>

<h3>Popular Game Modes</h3>

<h4>Classic Competitive</h4>
<p>Standard 5v5 bomb defusal mode (already configured in server.cfg above)</p>

<h4>GunGame (SourceMod Plugin)</h4>
<pre><code># Install GunGame plugin from AlliedModders
# Players progress through weapons by getting kills
sm_gg_enabled 1
sm_gg_turbo 0           // Turbo mode (instant respawn)
sm_gg_knife_elite 1     // Knife fight at final level
</code></pre>

<h4>Zombie Mod (SourceMod Plugin)</h4>
<pre><code># Zombies vs humans survival gameplay
# Mother zombie infects others
zr_enabled 1
zr_classes_menu_spawn 1
zr_respawn 1
</code></pre>

<h4>Surf Maps</h4>
<pre><code># Popular surf maps
+map surf_ski_2
+map surf_mesa
+map surf_greatriver

# Install Surf Timer plugin for rankings
</code></pre>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Won't Start</h3>

<h4>Port Already in Use</h4>
<pre><code># Check what's using port 27015
sudo lsof -i :27015
# Or Windows:
netstat -ano | findstr :27015

# Kill process or change port
./srcds_run -game cstrike -port 27016 ...
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

<h4>Server Not Listed in Browser</h4>
<ul>
    <li><strong>Check sv_lan:</strong> Must be <code>sv_lan 0</code></li>
    <li><strong>Verify firewall:</strong> UDP 27015 must be open</li>
    <li><strong>Wait 5-10 minutes:</strong> Steam master server updates are slow</li>
    <li><strong>Use direct connect:</strong> In game console: <code>connect IP:27015</code></li>
</ul>

<h4>Players Can't Connect</h4>
<pre><code># Test from external location
nc -u -v YOUR_SERVER_IP 27015

# Check server console
status
sv_lan
</code></pre>

<h3>Performance Issues</h3>

<h4>Low FPS / Stuttering</h4>
<ul>
    <li><strong>Check CPU usage:</strong> <code>top</code> or <code>htop</code></li>
    <li><strong>Reduce player count:</strong> Lower <code>-maxplayers</code></li>
    <li><strong>Disable SourceTV:</strong> <code>tv_enable 0</code></li>
    <li><strong>Check plugins:</strong> Disable plugins to identify performance issues</li>
</ul>

<h4>High Ping</h4>
<ul>
    <li><strong>Check rates:</strong> <code>sv_minrate 10000</code>, <code>sv_maxrate 0</code></li>
    <li><strong>Network bandwidth:</strong> 0.5Mbps per player minimum</li>
    <li><strong>Geographic location:</strong> Host near player base</li>
</ul>

<h3>Plugin Issues</h3>

<h4>SourceMod Not Loading</h4>
<pre><code># Check MetaMod loaded
meta list

# Check SourceMod
sm version

# Check logs
tail -f cstrike/logs/latest.log
tail -f cstrike/addons/sourcemod/logs/errors_*.txt
</code></pre>

<h4>Plugin Crashes Server</h4>
<ul>
    <li><strong>Remove plugin:</strong> Move .smx file out of <code>plugins/</code></li>
    <li><strong>Check compatibility:</strong> Ensure plugin supports CS:Source</li>
    <li><strong>Update SourceMod:</strong> Get latest stable</li>
    <li><strong>Check error logs:</strong> <code>addons/sourcemod/logs/</code></li>
</ul>

<h3>Custom Map Issues</h3>

<h4>Map Won't Download</h4>
<ul>
    <li><strong>Set up FastDL:</strong> <code>sv_downloadurl "http://yoursite.com/css/"</code></li>
    <li><strong>Compress files:</strong> Use bzip2 (.bsp.bz2)</li>
    <li><strong>Organize files:</strong> Mirror server structure (maps/, materials/, models/, sound/)</li>
</ul>

<h4>Missing Textures</h4>
<pre><code># Ensure all custom content is on FastDL server:
# maps/ - .bsp files
# materials/ - textures
# models/ - models
# sound/ - sounds
# Compress all with bzip2
</code></pre>

<h2>Performance Optimization</h2>
<ul>
    <li><strong>Use SSD storage:</strong> Faster map loads</li>
    <li><strong>Source engine is single-threaded:</strong> High clock speed CPU important</li>
    <li><strong>Limit SourceTV spectators:</strong> <code>tv_maxclients 5</code></li>
    <li><strong>Monitor resources:</strong> <code>htop</code>, <code>iotop</code></li>
    <li><strong>FastDL for custom content:</strong> Offload downloads to web server</li>
    <li><strong>Geographic proximity:</strong> Low ping for players</li>
</ul>

<h2>Security Best Practices</h2>
<ul>
    <li><strong>Strong RCON password:</strong> 20+ characters</li>
    <li><strong>Firewall RCON:</strong> Whitelist admin IPs (TCP 27015)</li>
    <li><strong>Keep server updated:</strong> Weekly <code>app_update 232330 validate</code></li>
    <li><strong>Use sv_pure:</strong> <code>sv_pure 1</code> for competitive integrity</li>
    <li><strong>Monitor logs:</strong> Watch for exploits</li>
    <li><strong>Secure SourceMod admins:</strong> Use Steam ID authentication</li>
</ul>

<h2>Updating Server</h2>
<pre><code># Stop server
rcon quit
# Or: killall srcds_linux (Linux) / taskkill /IM srcds.exe (Windows)

# Run SteamCMD update
cd /home/steam/steamcmd
./steamcmd.sh +login anonymous +force_install_dir /path/to/css-server +app_update 232330 validate +quit

# Restart server
cd /path/to/css-server
./srcds_run -game cstrike +map de_dust2 ...
</code></pre>

<h2 id="related-mods">🔌 Related Mods & Plugins</h2>
<p>Popular server modifications compatible with Counter-Strike: Source:</p>
<ul>
    <li><a href="../metamodsource/">Metamod:Source</a> - Foundation plugin loader required for SourceMod and other Source engine plugins</li>
    <li><a href="../amxmodx/">AMX Mod X</a> - Alternative plugin framework (partial CS:S support, primarily for CS 1.6)</li>
</ul>

<h2>Additional Resources</h2>
<ul>
    <li><a href="https://developer.valvesoftware.com/wiki/Counter-Strike:_Source" target="_blank">Valve Developer Wiki - CS:Source</a></li>
    <li><a href="https://www.sourcemod.net/" target="_blank">SourceMod Official Site</a></li>
    <li><a href="https://www.metamodsource.net/" target="_blank">MetaMod:Source Official Site</a></li>
    <li><a href="https://forums.alliedmods.net/" target="_blank">AlliedModders Forums (Plugins)</a></li>
    <li><a href="https://github.com/GameServerManagers/LinuxGSM" target="_blank">LinuxGSM - Server Management</a></li>
    <li><a href="https://www.reddit.com/r/counterstrike/" target="_blank">r/CounterStrike Reddit</a></li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8; margin: 0;">
        <li>CS:Source is a <strong>legacy game</strong> with established community and mods</li>
        <li>Keep server updated via SteamCMD for security patches</li>
        <li>Strong RCON password essential - many bots scan for weak passwords</li>
        <li>SourceMod plugin ecosystem is very mature for CS:Source</li>
        <li>FastDL recommended for custom maps/mods to reduce load times</li>
        <li>sv_pure 1 or 2 for competitive integrity (prevents client-side exploits)</li>
    </ul>
</div>

<p style="text-align: center; margin-top: 30px; color: #666;">
    <em>Last updated: January 2025 | CS:Source dedicated server complete guide</em>
</p>
