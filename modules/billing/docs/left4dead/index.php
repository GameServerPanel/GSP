<?php
/**
 * Left 4 Dead Server Documentation
 */
?>
<h1>📚 Left 4 Dead Server Guide</h1>

<h3 style="color: #94a3b8; margin-top: 8px;">Original Co-Op Zombie Survival - Comprehensive Setup</h3>

<div style="background: #1e3a5f; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #3b82f6;">
    <h3 style="color: #ffffff; margin-top: 0;">📋 Quick Info</h3>
    <table style="width: 100%; color: #e5e7eb;">
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Engine:</strong></td><td>Source Engine</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Developer:</strong></td><td>Valve Corporation</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">App ID:</strong></td><td>222840</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Default Port:</strong></td><td>27015 UDP/TCP</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Max Players:</strong></td><td>8 default (4v4 Versus), up to 32 with mods</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Difficulty:</strong></td><td>Easy, Normal, Advanced, Expert</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Game Modes:</strong></td><td>Campaign, Versus, Survival</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">RCON:</strong></td><td>Supported (same port as game)</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Platform:</strong></td><td>Windows, Linux</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Control:</strong></td><td>RCON, SourceMod, Console</td></tr>
    </table>
</div>

<h3 style="margin-top: 30px;">Navigation</h3>
<ul style="line-height: 2; font-size: 1.05em;">
    <li><a href="#overview">Overview</a></li>
    <li><a href="#ports">🔌 Ports & Firewall</a></li>
    <li><a href="#installation">Installation</a></li>
    <li><a href="#configuration">⚙️ Configuration</a></li>
    <li><a href="#gamemodes">Game Modes & Campaigns</a></li>
    <li><a href="#sourcemod">SourceMod & Plugins</a></li>
    <li><a href="#startup">Startup Commands</a></li>
    <li><a href="#troubleshooting">🔧 Troubleshooting</a></li>
    <li><a href="#resources">Resources</a></li>
</ul>

<h2 id="overview">Overview</h2>
<p><strong>Left 4 Dead</strong> is Valve's original co-operative first-person shooter where four "Survivors" must fight through hordes of infected zombies to reach safety. Released in 2008, L4D pioneered the co-op zombie survival genre and established the foundation for its sequel.</p>

<p>The game features the innovative "AI Director" system that dynamically adjusts gameplay difficulty, pacing, and item placement based on player performance. Each campaign tells the story of four survivors fighting through five chapters (maps) to reach an evacuation point.</p>

<h3>Key Features</h3>
<ul>
    <li><strong>Co-op Campaign:</strong> 4-player cooperative story campaigns against AI hordes</li>
    <li><strong>Versus Mode:</strong> 4v4 multiplayer with players controlling Special Infected</li>
    <li><strong>Survival Mode:</strong> Hold out against endless waves in challenge maps</li>
    <li><strong>5 Original Campaigns:</strong> No Mercy, Crash Course, Death Toll, Dead Air, Blood Harvest</li>
    <li><strong>Special Infected:</strong> Boomer, Hunter, Smoker, Tank, Witch</li>
    <li><strong>AI Director:</strong> Dynamic difficulty and pacing system</li>
    <li><strong>Four Difficulty Levels:</strong> Easy, Normal, Advanced, Expert</li>
</ul>

<h2 id="ports">🔌 Ports & Firewall Configuration</h2>

<h3>Required Ports</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0; background: #0f172a; border-radius: 8px; overflow: hidden;">
    <thead>
        <tr style="background: #1e293b;">
            <th style="padding: 12px; text-align: left; color: #ffffff; border-bottom: 2px solid #3b82f6;">Port</th>
            <th style="padding: 12px; text-align: left; color: #ffffff; border-bottom: 2px solid #3b82f6;">Protocol</th>
            <th style="padding: 12px; text-align: left; color: #ffffff; border-bottom: 2px solid #3b82f6;">Purpose</th>
            <th style="padding: 12px; text-align: left; color: #ffffff; border-bottom: 2px solid #3b82f6;">Required</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; font-family: monospace; color: #a5b4fc;">27015</td>
            <td style="padding: 12px;">
                <span style="background: #22c55e; color: #ffffff; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold;">UDP</span>
                <span style="background: #3b82f6; color: #ffffff; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold; margin-left: 4px;">TCP</span>
            </td>
            <td style="padding: 12px;">Game server + RCON (dual purpose)</td>
            <td style="padding: 12px; color: #22c55e; font-weight: bold;">✓ Yes</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; font-family: monospace; color: #a5b4fc;">27020</td>
            <td style="padding: 12px;">
                <span style="background: #22c55e; color: #ffffff; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold;">UDP</span>
            </td>
            <td style="padding: 12px;">SourceTV (optional spectating)</td>
            <td style="padding: 12px; color: #94a3b8;">Optional</td>
        </tr>
    </tbody>
</table>

<h3>Firewall Configuration Examples</h3>

<h4>Ubuntu/Debian (UFW)</h4>
<pre><code># Allow L4D game port (UDP and TCP)
sudo ufw allow 27015
sudo ufw allow 27015/tcp

# Allow SourceTV (optional)
sudo ufw allow 27020/udp

# Enable firewall
sudo ufw enable
sudo ufw status
</code></pre>

<h4>CentOS/RHEL (FirewallD)</h4>
<pre><code># Add L4D ports
sudo firewall-cmd --permanent --add-port=27015/udp
sudo firewall-cmd --permanent --add-port=27015/tcp
sudo firewall-cmd --permanent --add-port=27020/udp
sudo firewall-cmd --reload
sudo firewall-cmd --list-ports
</code></pre>

<h4>Windows Firewall (PowerShell)</h4>
<pre><code># Game server port
New-NetFirewallRule -DisplayName "L4D Server" -Direction Inbound -LocalPort 27015 -Protocol UDP -Action Allow
New-NetFirewallRule -DisplayName "L4D Server TCP" -Direction Inbound -LocalPort 27015 -Protocol TCP -Action Allow

# SourceTV (optional)
New-NetFirewallRule -DisplayName "L4D SourceTV" -Direction Inbound -LocalPort 27020 -Protocol UDP -Action Allow
</code></pre>

<h4>iptables (Advanced)</h4>
<pre><code># Allow L4D ports
iptables -A INPUT -p udp --dport 27015 -j ACCEPT
iptables -A INPUT -p tcp --dport 27015 -j ACCEPT
iptables -A INPUT -p udp --dport 27020 -j ACCEPT

# Save rules (Ubuntu/Debian)
netfilter-persistent save

# Save rules (CentOS/RHEL)
service iptables save
</code></pre>

<h2 id="installation">Installation</h2>

<h3>Prerequisites</h3>
<ul>
    <li><strong>SteamCMD</strong> installed (<a href="https://developer.valvesoftware.com/wiki/SteamCMD" target="_blank">Installation Guide</a>)</li>
    <li>Adequate disk space (~15 GB for full installation)</li>
    <li>Open firewall ports (27015 UDP/TCP minimum)</li>
    <li>Linux: 32-bit libraries (libstdc++6:i386, lib32gcc1)</li>
</ul>

<h3>Linux Installation</h3>
<pre><code># Create server directory
mkdir -p ~/l4d_server
cd ~/l4d_server

# Install 32-bit libraries (Ubuntu/Debian)
sudo dpkg --add-architecture i386
sudo apt-get update
sudo apt-get install lib32gcc1 libstdc++6:i386

# Download server files with SteamCMD
steamcmd +force_install_dir ~/l4d_server +login anonymous +app_update 222840 validate +quit

# Note: App ID 222840 is for Left 4 Dead Dedicated Server
</code></pre>

<h3>Windows Installation</h3>
<pre><code># Download SteamCMD for Windows
# Extract to C:\steamcmd\

# Create server directory
mkdir C:\l4d_server

# Run SteamCMD
C:\steamcmd\steamcmd.exe +force_install_dir C:\l4d_server +login anonymous +app_update 222840 validate +quit
</code></pre>

<h2 id="configuration">⚙️ Configuration</h2>

<h3>server.cfg</h3>
<p>Create or edit <code>left4dead/cfg/server.cfg</code>:</p>
<pre><code>// Server Identity
hostname "My Left 4 Dead Server"
rcon_password "your_secure_rcon_password"
sv_contact "admin@yourserver.com"
sv_region 1  // 0=US East, 1=US West, 2=South America, 3=Europe

// Basic Settings
sv_lan 0  // 0=internet server, 1=LAN only
sv_password ""  // Leave empty for public, set for private server
sv_allow_lobby_connect_only 0  // Allow direct connections

// Game Settings
z_difficulty "Normal"  // Easy, Normal, Hard, Expert
mp_gamemode "coop"  // coop, versus, survival
sv_cheats 0  // Disable cheats in production

// Player Limits
sv_maxplayers 8  // Max 8 for versus (4v4)
sv_visiblemaxplayers 8

// Voice Communication
sv_voiceenable 1
sv_alltalk 0  // 0=team only, 1=all talk

// Connection Settings
sv_minrate 20000
sv_maxrate 30000
sv_mincmdrate 30
sv_maxcmdrate 67
sv_minupdaterate 30
sv_maxupdaterate 67

// Logging
log on
sv_logecho 1
sv_logfile 1
sv_log_onefile 0
sv_logbans 1

// Director AI
director_no_death_check 0  // 0=normal spawning, 1=disable death checks
director_build_up_min_interval 20  // Minimum seconds between events

// Friendly Fire
mp_friendlyfire 0  // 0=off, 1=on (often enabled in Versus)

// Download Settings
sv_allowdownload 1
sv_allowupload 1
net_maxfilesize 64  // MB

// Performance
fps_max 300  // Server FPS cap
</code></pre>

<h2 id="gamemodes">Game Modes & Campaigns</h2>

<h3>Game Modes</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Mode</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Players</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Description</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Campaign (Coop)</strong></td>
            <td style="padding: 12px;">1-4</td>
            <td style="padding: 12px;">Fight through story campaigns as Survivors against AI infected</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Versus</strong></td>
            <td style="padding: 12px;">4v4</td>
            <td style="padding: 12px;">Teams alternate as Survivors and Special Infected</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Survival</strong></td>
            <td style="padding: 12px;">1-4</td>
            <td style="padding: 12px;">Hold out against endless waves in small challenge maps</td>
        </tr>
    </tbody>
</table>

<h3>Official Campaigns</h3>
<p>All 5 original campaigns included:</p>

<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Campaign</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Starting Map</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Maps</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Setting</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>No Mercy</strong></td>
            <td style="padding: 12px;"><code>l4d_hospital01_apartment</code></td>
            <td style="padding: 12px;">5</td>
            <td style="padding: 12px;">Fairfield (hospital rooftop escape)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Crash Course</strong></td>
            <td style="padding: 12px;"><code>l4d_garage01_alleys</code></td>
            <td style="padding: 12px;">2</td>
            <td style="padding: 12px;">Riverside (truck depot)</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Death Toll</strong></td>
            <td style="padding: 12px;"><code>l4d_river01_docks</code></td>
            <td style="padding: 12px;">5</td>
            <td style="padding: 12px;">Rural Pennsylvania (boathouse escape)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Dead Air</strong></td>
            <td style="padding: 12px;"><code>l4d_airport01_greenhouse</code></td>
            <td style="padding: 12px;">5</td>
            <td style="padding: 12px;">Metro Airport (airplane escape)</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Blood Harvest</strong></td>
            <td style="padding: 12px;"><code>l4d_farm01_hilltop</code></td>
            <td style="padding: 12px;">5</td>
            <td style="padding: 12px;">Allegheny National Forest (farmhouse finale)</td>
        </tr>
    </tbody>
</table>

<h3>Changing Maps via RCON</h3>
<pre><code># Connect with RCON
rcon_password your_password
rcon changelevel l4d_hospital01_apartment

# List all maps
rcon maps *

# Change game mode
rcon mp_gamemode versus
rcon map l4d_hospital01_apartment  // Restart map in new mode
</code></pre>

<h2 id="sourcemod">SourceMod & Plugins</h2>

<h3>Installing SourceMod</h3>

<h4>Step 1: Install MetaMod:Source</h4>
<pre><code># Download from: https://www.sourcemm.net/downloads.php?branch=stable
# Extract to left4dead/ directory

# Structure should be:
left4dead/
  addons/
    metamod/
      bin/

# Verify in server console:
meta version
</code></pre>

<h4>Step 2: Install SourceMod</h4>
<pre><code># Download from: https://www.sourcemod.net/downloads.php?branch=stable
# Extract to left4dead/ directory

# Structure should be:
left4dead/
  addons/
    sourcemod/
      plugins/
      configs/

# Add yourself as admin in:
# left4dead/addons/sourcemod/configs/admins_simple.ini
"STEAM_0:1:12345678" "99:z"

# Verify in-game:
sm version
</code></pre>

<h3>Essential Plugins</h3>
<ul>
    <li><strong>L4DToolZ:</strong> Enable 10+ player servers (bypass 8-player limit)</li>
    <li><strong>AdminMenu:</strong> Full admin control panel (included with SourceMod)</li>
    <li><strong>Simple Chat Processor:</strong> Chat colors and formatting</li>
    <li><strong>Basic Votes:</strong> Kick, ban, and map voting</li>
</ul>

<h3>Popular L4D Plugins</h3>
<ul>
    <li><strong>Super Versus:</strong> 8v8 Versus mode support</li>
    <li><strong>Survivor Bots:</strong> Enhanced bot AI and control</li>
    <li><strong>Competitive Spawns:</strong> Balanced Special Infected spawning for competitive play</li>
    <li><strong>L4D Stats:</strong> Player statistics tracking</li>
    <li><strong>Infected Bots Control:</strong> Manage AI Special Infected behavior</li>
</ul>

<h3>Installing Plugins</h3>
<pre><code># Place .smx files in:
left4dead/addons/sourcemod/plugins/

# Reload plugins:
rcon sm plugins reload pluginname

# List all plugins:
rcon sm plugins list

# Disable a plugin:
rcon sm plugins unload pluginname
</code></pre>

<h2 id="startup">Startup Commands</h2>

<h3>Linux</h3>
<pre><code>#!/bin/bash
# start_l4d.sh

cd ~/l4d_server

./srcds_run -console -game left4dead \
  +map l4d_hospital01_apartment \
  +maxplayers 8 \
  +mp_gamemode coop \
  -port 27015 \
  -ip 0.0.0.0
</code></pre>

<h3>Windows</h3>
<pre><code>@echo off
REM start_l4d.bat

cd C:\l4d_server

srcds.exe -console -game left4dead ^
  +map l4d_hospital01_apartment ^
  +maxplayers 8 ^
  +mp_gamemode coop ^
  -port 27015
</code></pre>

<h3>Startup Parameters</h3>
<ul>
    <li><code>-console</code> - Run with visible console</li>
    <li><code>-game left4dead</code> - Specify game directory</li>
    <li><code>+map mapname</code> - Starting map</li>
    <li><code>+maxplayers 8</code> - Maximum players (4 for coop, 8 for versus)</li>
    <li><code>+mp_gamemode mode</code> - Game mode (coop, versus, survival)</li>
    <li><code>-port 27015</code> - Server port</li>
    <li><code>-ip 0.0.0.0</code> - Bind to all interfaces</li>
    <li><code>+exec server.cfg</code> - Execute config file on start</li>
</ul>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not in Browser</h3>
<pre><code># Check sv_lan setting
sv_lan 0  // Must be 0 for internet

# Verify connection to Steam
status  // Check "Connected to Steam servers"

# Check port availability
netstat -an | grep 27015

# Wait for master server registration (5-10 minutes)
</code></pre>

<h3>Connection Issues</h3>
<ul>
    <li>Verify firewall allows UDP 27015</li>
    <li>Check server password (<code>sv_password</code>)</li>
    <li>Ensure clients can ping server IP</li>
    <li>Verify router port forwarding</li>
</ul>

<h3>SourceMod Not Loading</h3>
<pre><code># Verify directory structure
left4dead/
  addons/
    metamod/
    sourcemod/

# Check metamod first
meta version  // Should show version number

# Then check sourcemod
sm version

# Review error logs
left4dead/addons/sourcemod/logs/errors_*.log
</code></pre>

<h3>Performance Issues</h3>
<pre><code># Check server FPS
stats  // In server console

# Optimize rates
sv_minrate 20000
sv_maxrate 30000
fps_max 300

# Reduce max players if needed
sv_maxplayers 4  // For coop
</code></pre>

<h3>AI Director Too Hard/Easy</h3>
<pre><code># Change difficulty
z_difficulty Normal  // Easy, Normal, Hard, Expert

# Adjust Special Infected spawn rates
director_build_up_min_interval 20  // Seconds between events

# Tank spawn frequency
z_tank_max_players 3000  // Distance before Tank spawns
</code></pre>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-lightbulb" style="color: #fbbf24; margin-right: 8px;"></i>Pro Tips</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>L4DToolZ Plugin:</strong> Unlock 10-32 player servers for custom game modes</li>
        <li><strong>Versus Balance:</strong> 4v4 is standard competitive format</li>
        <li><strong>Custom Campaigns:</strong> L4D has active custom campaign community</li>
        <li><strong>Survival Mode:</strong> Great for warming up or quick sessions</li>
        <li><strong>RCON Management:</strong> Use HLSW or similar tools for remote management</li>
        <li><strong>sv_pure 2:</strong> Enable for competitive to prevent cheats</li>
        <li><strong>Friendly Fire:</strong> Enable in Versus for tactical gameplay</li>
    </ul>
</div>

<h2 id="resources">Resources</h2>
<ul>
    <li><a href="https://developer.valvesoftware.com/wiki/Left_4_Dead_Dedicated_Server" target="_blank">Valve Developer Wiki - L4D Dedicated Server</a></li>
    <li><a href="https://wiki.alliedmods.net/Category:Left_4_Dead" target="_blank">AlliedModders L4D Wiki</a></li>
    <li><a href="https://www.l4dmaps.com/" target="_blank">L4DMaps - Custom Campaign Repository</a></li>
    <li><a href="https://forums.alliedmods.net/forumdisplay.php?f=72" target="_blank">AlliedModders L4D Forum</a></li>
    <li><a href="https://steamcommunity.com/app/500/discussions/" target="_blank">Steam Community Discussions</a></li>
</ul>