<?php
/**
 * Left 4 Dead 2 Dedicated Server - Comprehensive Hosting Guide
 * General game server hosting information (not platform-specific)
 */
?>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">📚 Navigation</h3>
    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
        <a href="#overview" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Overview</a>
        <a href="#ports" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">🔌 Ports</a>
        <a href="#installation" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Installation</a>
        <a href="#configuration" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">⚙️ Configuration</a>
        <a href="#gamemodes" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Game Modes</a>
        <a href="#addons" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Addons & Mods</a>
        <a href="#troubleshooting" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">🔧 Troubleshooting</a>
    </div>
</div>

<h1>Left 4 Dead 2 Dedicated Server Hosting Guide</h1>

<h2 id="overview">Overview</h2>
<p><strong>Left 4 Dead 2</strong> is a cooperative first-person shooter developed by Valve. Set in the aftermath of a zombie apocalypse, teams of four survivors fight through hordes of infected across various campaigns. The game features intense co-op action, versus mode, and extensive modding support through the Source engine.</p>

<h2 id="quick-info">Quick Info</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <ul style="color: #e5e7eb; line-height: 1.8; margin: 0;">
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27015</code> (UDP)</li>
        <li><strong style="color: #ffffff;">RCON Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27015</code> (TCP, same as game port)</li>
        <li><strong style="color: #ffffff;">SourceTV Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27020</code> (UDP, optional)</li>
        <li><strong style="color: #ffffff;">Minimum RAM:</strong> 1GB (Recommended: 2-4GB)</li>
        <li><strong style="color: #ffffff;">Recommended CPU:</strong> 2+ cores @ 2.5GHz</li>
        <li><strong style="color: #ffffff;">Storage:</strong> 15GB+ for game files</li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 4-8 typical (co-op/versus)</li>
        <li><strong style="color: #ffffff;">SteamCMD App ID:</strong> 222860</li>
        <li><strong style="color: #ffffff;">Engine:</strong> Source Engine</li>
        <li><strong style="color: #ffffff;">Config File:</strong> server.cfg</li>
    </ul>
</div>

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
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span> / <span style="background: #1e40af; padding: 4px 8px; border-radius: 3px; color: #dbeafe;">TCP</span></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Game + RCON port (UDP for game, TCP for RCON)</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #7c2d12; padding: 4px 8px; border-radius: 3px; color: #fed7aa;">✓ Yes</span></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">27020</code></td>
            <td style="padding: 12px;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span></td>
            <td style="padding: 12px;">SourceTV (spectator mode, game replays)</td>
            <td style="padding: 12px;"><span style="background: #713f12; padding: 4px 8px; border-radius: 3px; color: #fef3c7;">Optional</span></td>
        </tr>
    </tbody>
</table>

<div style="background: #1e3a5f; padding: 15px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <p style="color: #e5e7eb; margin: 0;"><strong>Note:</strong> Source engine games use the same port for both UDP (game traffic) and TCP (RCON). If you run multiple L4D2 servers on one machine, use different ports (27016, 27017, etc.) and adjust your startup parameters accordingly.</p>
</div>

<h3>Firewall Configuration Examples</h3>

<h4>UFW (Ubuntu/Debian)</h4>
<pre><code>sudo ufw allow 27015/udp comment 'L4D2 game port'
sudo ufw allow 27015/tcp comment 'L4D2 RCON'
sudo ufw allow 27020/udp comment 'L4D2 SourceTV'
sudo ufw reload
</code></pre>

<h4>FirewallD (CentOS/RHEL/Fedora)</h4>
<pre><code>sudo firewall-cmd --permanent --add-port=27015/udp
sudo firewall-cmd --permanent --add-port=27015/tcp
sudo firewall-cmd --permanent --add-port=27020/udp
sudo firewall-cmd --reload
</code></pre>

<h4>Windows Firewall</h4>
<pre><code># Run in PowerShell as Administrator
New-NetFirewallRule -DisplayName "L4D2 Game/RCON" -Direction Inbound -Protocol UDP -LocalPort 27015 -Action Allow
New-NetFirewallRule -DisplayName "L4D2 RCON TCP" -Direction Inbound -Protocol TCP -LocalPort 27015 -Action Allow
New-NetFirewallRule -DisplayName "L4D2 SourceTV" -Direction Inbound -Protocol UDP -LocalPort 27020 -Action Allow
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
    <li><strong>OS:</strong> Windows Server 2016+ or Linux 64-bit (Ubuntu/Debian recommended)</li>
    <li><strong>CPU:</strong> Dual-core @ 2.5GHz minimum; Quad-core for better performance</li>
    <li><strong>RAM:</strong> 1GB minimum, 2-4GB recommended</li>
    <li><strong>Storage:</strong> 15GB+ for game files and workshop content</li>
    <li><strong>Network:</strong> 5Mbps+ upload recommended</li>
</ul>

<h3>Installing via SteamCMD (Linux)</h3>
<pre><code># Install SteamCMD
sudo apt update
sudo apt install steamcmd

# Create server directory
mkdir -p ~/l4d2-server
cd ~/l4d2-server

# Download server files
steamcmd +login anonymous +force_install_dir ~/l4d2-server +app_update 222860 validate +exit
</code></pre>

<h3>Installing via SteamCMD (Windows)</h3>
<pre><code># Download SteamCMD from https://steamcdn-a.akamaihd.net/client/installer/steamcmd.zip
# Extract to C:\steamcmd\

# Run CMD as Administrator
cd C:\steamcmd
steamcmd.exe +login anonymous +force_install_dir "C:\L4D2Server" +app_update 222860 validate +exit
</code></pre>

<h2 id="configuration">Server Configuration</h2>

<h3>server.cfg</h3>
<p>Create <code>left4dead2/cfg/server.cfg</code> with your settings:</p>

<pre><code>// Server Name and Info
hostname "My Left 4 Dead 2 Server"
sv_steamgroup ""  // Steam group ID (optional)
sv_steamgroup_exclusive 0

// Server Settings
sv_lan 0
sv_region 1  // 0=US East, 1=US West, 2=South America, 3=Europe, etc.
sv_allow_lobby_connect_only 0
sv_gametypes "coop,versus,survival,scavenge"

// RCON
rcon_password "your_secure_password"

// Communication
sv_voiceenable 1
sv_alltalk 0  // 0=team only, 1=all players

// Logging
log on
sv_logbans 1
sv_logecho 1
sv_logfile 1
sv_log_onefile 0

// Rates
sv_minrate 30000
sv_maxrate 100000
sv_mincmdrate 30
sv_maxcmdrate 100
sv_minupdaterate 30
sv_maxupdaterate 100

// Download Settings
sv_allowdownload 1
sv_allowupload 1
net_maxfilesize 64

// Game Rules
mp_disable_autokick 1  // Don't auto-kick idle players
sv_consistency 1
sv_pure 1  // Server purity (1=enforce consistency)
sv_pure_kick_clients 1

// Campaign Settings
mp_gamemode "coop"  // coop, versus, survival, scavenge
z_difficulty "Normal"  // Easy, Normal, Hard, Impossible

// Versus Settings
versus_boss_flow_max 0.9
versus_boss_flow_min 0.2

// Performance
fps_max 300
sv_maxcmdrate 100
sv_maxupdaterate 100

// Plugins (SourceMod/MetaMod if installed)
// sm_cvar mp_autoteambalance 0
</code></pre>

<h3>Starting the Server</h3>

<h4>Windows</h4>
<pre><code># Navigate to server directory
cd C:\L4D2Server\

# Basic startup
srcds.exe -console -game left4dead2 +map c1m1_hotel +maxplayers 8

# With custom config and hostname
srcds.exe -console -game left4dead2 +exec server.cfg +map c1m1_hotel +maxplayers 8 +hostname "My Server"

# Specific game mode
srcds.exe -console -game left4dead2 +map c1m1_hotel +mp_gamemode versus +maxplayers 8
</code></pre>

<h4>Linux</h4>
<pre><code># Make start script executable
chmod +x srcds_run

# Run in screen session
screen -S l4d2 ./srcds_run -console -game left4dead2 +map c1m1_hotel +maxplayers 8

# With custom parameters
./srcds_run -console -game left4dead2 +exec server.cfg +map c1m1_hotel +maxplayers 8 +ip YOUR_SERVER_IP -port 27015

# Detach: Ctrl+A, D
# Reattach: screen -r l4d2
</code></pre>

<h2 id="gamemodes">Game Modes</h2>

<h3>Available Game Modes</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Mode</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Description</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Players</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">ConVar</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Co-op</strong></td>
            <td style="padding: 12px;">4 survivors vs AI infected</td>
            <td style="padding: 12px;">1-4</td>
            <td style="padding: 12px;"><code>mp_gamemode coop</code></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Versus</strong></td>
            <td style="padding: 12px;">4v4 PvP (survivors vs special infected)</td>
            <td style="padding: 12px;">2-8</td>
            <td style="padding: 12px;"><code>mp_gamemode versus</code></td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Survival</strong></td>
            <td style="padding: 12px;">Hold out against endless waves</td>
            <td style="padding: 12px;">1-4</td>
            <td style="padding: 12px;"><code>mp_gamemode survival</code></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Scavenge</strong></td>
            <td style="padding: 12px;">4v4 timed gas can collection</td>
            <td style="padding: 12px;">2-8</td>
            <td style="padding: 12px;"><code>mp_gamemode scavenge</code></td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Realism</strong></td>
            <td style="padding: 12px;">Hardcore co-op (no outlines, harder)</td>
            <td style="padding: 12px;">1-4</td>
            <td style="padding: 12px;"><code>mp_gamemode realism</code></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Mutations</strong></td>
            <td style="padding: 12px;">Special rule variants (weekly)</td>
            <td style="padding: 12px;">Varies</td>
            <td style="padding: 12px;"><code>mp_gamemode mutation#</code></td>
        </tr>
    </tbody>
</table>

<h3>Campaign Maps</h3>
<p>Default starting maps for campaigns:</p>

<ul>
    <li><strong>Dead Center:</strong> <code>c1m1_hotel</code></li>
    <li><strong>Dark Carnival:</strong> <code>c2m1_highway</code></li>
    <li><strong>Swamp Fever:</strong> <code>c3m1_plankcountry</code></li>
    <li><strong>Hard Rain:</strong> <code>c4m1_milltown_a</code></li>
    <li><strong>The Parish:</strong> <code>c5m1_waterfront</code></li>
    <li><strong>The Passing (DLC):</strong> <code>c6m1_riverbank</code></li>
    <li><strong>The Sacrifice (DLC):</strong> <code>c7m1_docks</code></li>
    <li><strong>No Mercy (L4D1):</strong> <code>c8m1_apartment</code></li>
    <li><strong>Crash Course (L4D1):</strong> <code>c9m1_alleys</code></li>
    <li><strong>Death Toll (L4D1):</strong> <code>c10m1_caves</code></li>
    <li><strong>Dead Air (L4D1):</strong> <code>c11m1_greenhouse</code></li>
    <li><strong>Blood Harvest (L4D1):</strong> <code>c12m1_hilltop</code></li>
    <li><strong>Cold Stream (DLC):</strong> <code>c13m1_alpinecreek</code></li>
</ul>

<h2 id="addons">Addons & Mods</h2>

<h3>SourceMod & MetaMod:Source</h3>
<p>Essential for server administration and plugins:</p>

<h4>Installing MetaMod:Source</h4>
<pre><code># Download from https://www.sourcemm.net/downloads.php?branch=stable
# Extract to left4dead2/ directory

# Verify installation - should see "addons/metamod/" folder
</code></pre>

<h4>Installing SourceMod</h4>
<pre><code># Download from https://www.sourcemod.net/downloads.php?branch=stable
# Extract to left4dead2/ directory

# Add yourself as admin in addons/sourcemod/configs/admins_simple.ini:
"STEAM_0:1:12345678" "99:z"  // Replace with your SteamID

# Restart server
</code></pre>

<h3>Popular SourceMod Plugins</h3>
<ul>
    <li><strong>L4DToolZ:</strong> Unlock player slots beyond 8 (up to 32)</li>
    <li><strong>Super Versus:</strong> Custom versus configurations</li>
    <li><strong>Survivor Bots:</strong> Better bot AI and control</li>
    <li><strong>Admin Menu:</strong> In-game admin panel for server management</li>
    <li><strong>Spawn Control:</strong> Customize special infected spawning</li>
    <li><strong>Vocalize:</strong> Enhanced survivor voice lines</li>
</ul>

<h3>Workshop Content</h3>
<p>L4D2 supports Steam Workshop. To auto-download workshop items:</p>

<pre><code># In server.cfg or startup command
+workshop_download_dir "addons/workshop"
+host_workshop_collection "COLLECTION_ID_HERE"

# Find collection IDs on Steam Workshop
# Server will auto-download subscribed content
</code></pre>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not Showing in Browser</h3>
<pre><code># Check sv_lan setting
sv_lan 0  // Must be 0 for internet servers

# Verify region
sv_region 1  // Must be set appropriately

# Check ports
netstat -an | grep 27015

# Test RCON connection
# Use tool like SourceRCON or in-game console
rcon_password "your_password"
rcon status
</code></pre>

<h3>Connection Issues / Lag</h3>
<pre><code># Optimize rates in server.cfg
sv_minrate 30000
sv_maxrate 100000
sv_mincmdrate 30
sv_maxcmdrate 100

# Adjust tickrate (default 30)
-tickrate 30  // Add to startup command

# Check server FPS
fps_max 300  // In server.cfg
</code></pre>

<h3>Players Can't Join / Authentication Failed</h3>
<ul>
    <li>Ensure <code>sv_lan 0</code> is set correctly</li>
    <li>Check firewall allows both UDP and TCP on game port</li>
    <li>Verify Steam authentication is working (servers need Steam running)</li>
    <li>Try <code>sv_allow_lobby_connect_only 0</code> to allow direct connects</li>
</ul>

<h3>Mods/Addons Not Loading</h3>
<pre><code># Check MetaMod loaded
meta version  // In server console

# Check SourceMod loaded
sm version

# Verify file structure
addons/
  metamod/
  sourcemod/
    plugins/
    configs/

# Review error logs
left4dead2/addons/sourcemod/logs/
</code></pre>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-lightbulb" style="color: #fbbf24; margin-right: 8px;"></i>Pro Tips</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Versus balance:</strong> Use SourceMod plugins for better competitive balance</li>
        <li><strong>Custom campaigns:</strong> L4D2 has hundreds of community campaigns on Workshop</li>
        <li><strong>More players:</strong> L4DToolZ plugin allows 10+ player servers (chaotic but fun!)</li>
        <li><strong>Mutations:</strong> Weekly mutation modes provide variety (Taaannnkk!, Gib Fest, etc.)</li>
        <li><strong>Performance:</strong> Source engine is CPU-bound; prioritize single-core performance</li>
        <li><strong>Difficulty scaling:</strong> z_difficulty affects tank health, special spawn rates</li>
    </ul>
</div>

<h2>Resources</h2>
<ul>
    <li><a href="https://developer.valvesoftware.com/wiki/Left_4_Dead_2" target="_blank">Valve Developer Wiki - L4D2</a></li>
    <li><a href="https://www.sourcemod.net/" target="_blank">SourceMod Official Site</a></li>
    <li><a href="https://www.metamodsource.net/" target="_blank">MetaMod:Source Official Site</a></li>
    <li><a href="https://steamcommunity.com/app/550/workshop/" target="_blank">Steam Workshop - L4D2</a></li>
    <li><a href="https://www.gamemaps.com/l4d2/" target="_blank">GameMaps - Custom Campaigns</a></li>
</ul>