<?php
/**
 * Counter-Strike: Global Offensive / CS2 Server Documentation
 * Comprehensive game server hosting guide
 */
?>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Navigation</h3>
    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
        <a href="#quick-info" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Quick Info</a>
        <a href="#installation" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Installation</a>
        <a href="#configuration" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Configuration</a>
        <a href="#parameters" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Parameters</a>
        <a href="#plugins-mods" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Plugins & Mods</a>
        <a href="#troubleshooting" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Troubleshooting</a>
        <a href="#gamemodes" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Game Modes</a>
    </div>
</div>

<h1>Counter-Strike: Global Offensive & CS2 Server Hosting Guide</h1>

<h2>Overview</h2>
<p>Counter-Strike: Global Offensive (CS:GO) and Counter-Strike 2 (CS2) are competitive tactical first-person shooters. This guide covers everything needed to host a dedicated CS:GO or CS2 server on Linux or Windows.</p>

<p><strong>Note:</strong> CS2 replaced CS:GO in September 2023. Most concepts apply to both, but CS2 uses Source 2 engine with some differences. This guide covers both versions.</p>

<h2 id="quick-info">Quick Info</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <ul style="color: #e5e7eb; line-height: 1.8; margin: 0;">
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27015</code> (UDP)</li>
        <li><strong style="color: #ffffff;">Additional Ports:</strong> 27015 (TCP), 27020 (UDP), 27005 (UDP) for SourceTV</li>
        <li><strong style="color: #ffffff;">Protocol:</strong> UDP (primary), TCP (RCON)</li>
        <li><strong style="color: #ffffff;">Minimum RAM:</strong> 2GB (CS:GO), 4GB (CS2)</li>
        <li><strong style="color: #ffffff;">Recommended RAM:</strong> 4GB+ (CS:GO), 8GB+ (CS2)</li>
        <li><strong style="color: #ffffff;">CPU:</strong> High single-thread performance critical</li>
        <li><strong style="color: #ffffff;">App ID:</strong> 740 (CS:GO), 730 (CS2)</li>
        <li><strong style="color: #ffffff;">SteamCMD App:</strong> 740 (dedicated server)</li>
        <li><strong style="color: #ffffff;">Log Files:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">csgo/logs/</code> or <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">cs2/logs/</code></li>
        <li><strong style="color: #ffffff;">Main Config:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">server.cfg</code></li>
        <li><strong style="color: #ffffff;">Server Launcher:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">srcds_run</code> (Linux) or <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">srcds.exe</code> (Windows)</li>
    </ul>
</div>

<h2 id="installation">Installation & Setup</h2>

<h3>System Requirements</h3>
<h4>CS:GO</h4>
<ul>
    <li><strong>OS:</strong> Linux (Ubuntu 18.04+, Debian 9+) or Windows Server 2012+</li>
    <li><strong>CPU:</strong> Dual-core 3GHz+ (quad-core recommended)</li>
    <li><strong>RAM:</strong> 2GB minimum, 4GB+ recommended</li>
    <li><strong>Storage:</strong> 30GB+</li>
    <li><strong>Bandwidth:</strong> 100Mbps+ for competitive play</li>
</ul>

<h4>CS2</h4>
<ul>
    <li><strong>OS:</strong> Linux (Ubuntu 20.04+) or Windows Server 2019+</li>
    <li><strong>CPU:</strong> Quad-core 3.5GHz+ recommended</li>
    <li><strong>RAM:</strong> 4GB minimum, 8GB+ recommended</li>
    <li><strong>Storage:</strong> 40GB+</li>
    <li><strong>Network:</strong> 1Gbps connection recommended</li>
</ul>

<h3>Installing SteamCMD</h3>

<h4>Linux Installation</h4>
<pre><code># Install dependencies (Ubuntu/Debian)
sudo apt update
sudo apt install lib32gcc-s1 lib32stdc++6 steamcmd

# Or manual install
mkdir ~/steamcmd
cd ~/steamcmd
wget https://steamcdn-a.akamaihd.net/client/installer/steamcmd_linux.tar.gz
tar -xvzf steamcmd_linux.tar.gz
</code></pre>

<h4>Windows Installation</h4>
<p>Download SteamCMD from: <a href="https://steamcdn-a.akamaihd.net/client/installer/steamcmd.zip" target="_blank">SteamCMD for Windows</a></p>

<h3>Installing CS:GO/CS2 Server</h3>

<h4>CS:GO Server</h4>
<pre><code># Run SteamCMD
./steamcmd.sh

# Login anonymously
login anonymous

# Set install directory
force_install_dir ./csgo-server

# Install CS:GO dedicated server
app_update 740 validate

# Exit
quit
</code></pre>

<h4>CS2 Server</h4>
<pre><code># Run SteamCMD
./steamcmd.sh

# Login (may require Steam account with CS2)
login anonymous

# Set install directory
force_install_dir ./cs2-server

# Install CS2 dedicated server
app_update 730 validate

# Exit
quit
</code></pre>

<h2 id="configuration">Server Configuration</h2>

<h3>server.cfg - Essential Settings</h3>
<p>Create <code>csgo/cfg/server.cfg</code> or <code>cs2/cfg/server.cfg</code>:</p>
<pre><code>// Server Information
hostname "My CS:GO/CS2 Server"
sv_password ""                  // Server password (leave blank for public)
sv_region "1"                   // 0=US East, 1=US West, 2=South America, 3=Europe, etc.

// RCON Configuration
rcon_password "YourSecurePassword"
sv_rcon_banpenalty 0
sv_rcon_maxfailures 5

// Server Settings
sv_cheats 0
sv_lan 0
sv_pure 1                       // File consistency checking (0=off, 1=on, 2=strict)
sv_pure_kick_clients 1
sv_minrate 128000
sv_maxrate 0                    // 0=unlimited

// Game Settings
mp_autoteambalance 1
mp_limitteams 1
mp_teamcashawards 1
mp_playercashawards 1
mp_maxmoney 16000
mp_startmoney 800
mp_buytime 90
mp_buy_anywhere 0
mp_freezetime 15
mp_friendlyfire 0
mp_c4timer 40
mp_roundtime 5
mp_roundtime_defuse 1.92
mp_maxrounds 30
mp_overtime_enable 1
mp_overtime_maxrounds 6
mp_overtime_startmoney 10000

// Competitive Settings (5v5)
mp_match_end_restart 1
mp_halftime 1
mp_warmuptime 30
mp_do_warmup_period 1
mp_warmup_pausetimer 1

// Communication
sv_alltalk 0
sv_deadtalk 0
sv_full_alltalk 0
sv_talk_enemy_dead 1
sv_talk_enemy_living 0

// Voting
sv_vote_issue_kick_allowed 0
sv_vote_issue_changelevel_allowed 0
sv_vote_issue_nextlevel_allowed 0

// SourceTV (GOTV)
tv_enable 1
tv_delay 90
tv_advertise_watchable 1
tv_name "GOTV"
tv_title "Source TV"
tv_autorecord 1
tv_allow_camera_man 1

// Logging
log on
sv_logbans 1
sv_logecho 1
sv_logfile 1
sv_log_onefile 0

// Execute additional configs
exec banned_user.cfg
exec banned_ip.cfg
</code></pre>

<h3>Game Mode Configuration Files</h3>

<h4>gamemode_competitive.cfg (5v5 Competitive)</h4>
<pre><code>mp_maxrounds 30
mp_roundtime 1.92
mp_roundtime_defuse 1.92
mp_freezetime 15
mp_buytime 90
mp_startmoney 800
mp_maxmoney 16000
mp_timelimit 0
sv_alltalk 0
sv_talk_enemy_dead 1
sv_deadtalk 0
</code></pre>

<h4>gamemode_casual.cfg (10v10 Casual)</h4>
<pre><code>mp_maxrounds 15
mp_roundtime 3
mp_roundtime_defuse 3
mp_freezetime 15
mp_buytime 90
mp_startmoney 1000
mp_maxmoney 16000
sv_alltalk 0
mp_autoteambalance 1
mp_limitteams 2
</code></pre>

<h3>mapcycle.txt</h3>
<p>List maps to rotate through:</p>
<pre><code>de_dust2
de_mirage
de_inferno
de_nuke
de_overpass
de_vertigo
de_ancient
de_anubis
</code></pre>

<h2 id="parameters">Startup Parameters</h2>

<h3>Basic Linux Startup (CS:GO)</h3>
<pre><code>#!/bin/bash
cd /path/to/csgo-server
./srcds_run -game csgo \
    -console \
    -usercon \
    +ip 0.0.0.0 \
    +game_type 0 \
    +game_mode 1 \
    +mapgroup mg_active \
    +map de_dust2 \
    -port 27015 \
    +tv_port 27020 \
    -tickrate 128 \
    +maxplayers 10 \
    +sv_setsteamaccount YOUR_GSLT_TOKEN
</code></pre>

<h3>Basic Linux Startup (CS2)</h3>
<pre><code>#!/bin/bash
cd /path/to/cs2-server
./game/bin/linuxsteamrt64/cs2 \
    -dedicated \
    -console \
    +ip 0.0.0.0 \
    +map de_dust2 \
    -port 27015 \
    +maxplayers 10 \
    +sv_setsteamaccount YOUR_GSLT_TOKEN \
    +game_type 0 \
    +game_mode 1
</code></pre>

<h3>Windows Startup (CS:GO)</h3>
<pre><code>@echo off
cd C:\csgo-server
srcds.exe -game csgo -console -usercon +ip 0.0.0.0 +game_type 0 +game_mode 1 +mapgroup mg_active +map de_dust2 -port 27015 -tickrate 128 +maxplayers 10 +sv_setsteamaccount YOUR_GSLT_TOKEN
pause
</code></pre>

<h3>Parameter Breakdown</h3>
<ul>
    <li><code>-game csgo</code> - Specify game (CS:GO only, not needed for CS2)</li>
    <li><code>-console</code> - Enable server console</li>
    <li><code>-usercon</code> - Enable user console input</li>
    <li><code>+ip 0.0.0.0</code> - Bind to all network interfaces</li>
    <li><code>+game_type 0</code> - Classic game type</li>
    <li><code>+game_mode 1</code> - Competitive mode (0=casual, 1=competitive, 2=wingman)</li>
    <li><code>+mapgroup mg_active</code> - Map group (active duty maps)</li>
    <li><code>+map de_dust2</code> - Starting map</li>
    <li><code>-port 27015</code> - Server port</li>
    <li><code>-tickrate 128</code> - Server tickrate (64 or 128, CS:GO only)</li>
    <li><code>+maxplayers 10</code> - Maximum players</li>
    <li><code>+sv_setsteamaccount TOKEN</code> - Game Server Login Token (GSLT)</li>
</ul>

<h3>Game Server Login Token (GSLT)</h3>
<p><strong>Required for public servers to appear in server browser!</strong></p>
<ol>
    <li>Go to <a href="https://steamcommunity.com/dev/managegameservers" target="_blank">Steam Game Server Account Management</a></li>
    <li>Login with your Steam account</li>
    <li>Click "Create New Game Server Account"</li>
    <li>App ID: 730 (CS:GO) or 730 (CS2)</li>
    <li>Memo: Your server name/description</li>
    <li>Copy the generated token</li>
    <li>Use in +sv_setsteamaccount parameter</li>
</ol>

<h2 id="plugins-mods">Plugins & Mods</h2>

<h3>SourceMod & MetaMod:Source</h3>
<p>The standard plugin framework for Source engine servers.</p>

<h4>Installation</h4>
<ol>
    <li><strong>Download MetaMod:Source:</strong> <a href="https://www.sourcemm.net/downloads.php?branch=stable" target="_blank">SourceMM.net</a></li>
    <li><strong>Download SourceMod:</strong> <a href="https://www.sourcemod.net/downloads.php?branch=stable" target="_blank">SourceMod.net</a></li>
    <li><strong>Extract to server directory:</strong>
        <pre><code># Both extract to csgo/ or cs2/ folder
cd /path/to/csgo-server/csgo
wget https://mms.alliedmods.net/mmsdrop/...
tar -xzf mmsource-...tar.gz

wget https://sm.alliedmods.net/smdrop/...
tar -xzf sourcemod-...tar.gz
</code></pre>
    </li>
    <li><strong>Restart server</strong></li>
    <li><strong>Add yourself as admin:</strong>
        <pre><code># Edit addons/sourcemod/configs/admins_simple.ini
"STEAM_0:1:12345678" "99:z"  // Your Steam ID
</code></pre>
    </li>
</ol>

<h3>Essential Plugins</h3>

<h4>Practice Mode</h4>
<p>For practicing smokes, flashes, and aim.</p>
<ul>
    <li>Download: <a href="https://github.com/splewis/csgo-practice-mode" target="_blank">CS:GO Practice Mode</a></li>
    <li>Features: Noclip, infinite ammo, grenade trajectory, bot spawning</li>
</ul>

<h4>Get5</h4>
<p>Competitive match plugin with knife rounds, veto, and more.</p>
<ul>
    <li>Download: <a href="https://github.com/splewis/get5" target="_blank">Get5 on GitHub</a></li>
    <li>Features: Automated match setup, team management, stats</li>
</ul>

<h4>RetakesPlugin</h4>
<p>Retake game mode - defenders defend bombsite, attackers retake.</p>
<ul>
    <li>Download: <a href="https://github.com/b3none/retakes-plugin" target="_blank">Retakes Plugin</a></li>
</ul>

<h4>RankMe</h4>
<p>Player ranking and statistics system.</p>
<ul>
    <li>Download: <a href="https://forums.alliedmods.net/showthread.php?t=290063" target="_blank">RankMe</a></li>
</ul>

<h4>In-Game Admin Menu</h4>
<p>Built into SourceMod. Access with <code>!admin</code> or <code>sm_admin</code> in chat.</p>

<h3>Workshop Maps & Collections</h3>
<pre><code># In server.cfg or startup parameters
host_workshop_collection 123456789  // Workshop collection ID
workshop_start_map 123456789         // Workshop map ID
</code></pre>

<h2 id="troubleshooting">Troubleshooting</h2>

<h3>Server Won't Start</h3>

<h4>Missing Libraries (Linux)</h4>
<pre><code># Install 32-bit libraries
sudo dpkg --add-architecture i386
sudo apt update
sudo apt install lib32gcc-s1 lib32stdc++6

# CS:GO specific
sudo apt install libsdl2-2.0-0:i386

# CS2 specific
sudo apt install libtinfo5:i386
</code></pre>

<h4>Port Already in Use</h4>
<pre><code># Check what's using port 27015
sudo netstat -tulpn | grep 27015
sudo lsof -i :27015

# Kill existing process or change port
./srcds_run -game csgo -port 27016 ...
</code></pre>

<h3>Server Not in Browser</h3>
<ol>
    <li><strong>Check GSLT is set:</strong> <code>+sv_setsteamaccount YOUR_TOKEN</code></li>
    <li><strong>Verify sv_lan is 0:</strong> <code>sv_lan 0</code> in server.cfg</li>
    <li><strong>Check firewall allows UDP 27015:</strong>
        <pre><code>sudo ufw allow 27015/udp
sudo ufw allow 27015/tcp
sudo ufw allow 27020/udp  # SourceTV
</code></pre>
    </li>
    <li><strong>Wait 5-10 minutes:</strong> Can take time to appear in browser</li>
    <li><strong>Direct connect test:</strong> In CS:GO/CS2 console: <code>connect your.server.ip:27015</code></li>
</ol>

<h3>High Ping / Lag</h3>

<h4>Server-Side</h4>
<ol>
    <li><strong>Check server load:</strong> <code>top</code> or <code>htop</code></li>
    <li><strong>Increase rates:</strong>
        <pre><code>sv_minrate 128000
sv_maxrate 0  // unlimited
</code></pre>
    </li>
    <li><strong>Enable multi-core (CS:GO):</strong>
        <pre><code>host_thread_mode 2
</code></pre>
    </li>
    <li><strong>Reduce bots if present</strong></li>
    <li><strong>Check network saturation</strong></li>
</ol>

<h4>Client-Side</h4>
<pre><code>// Player client commands
rate 786432
cl_interp 0
cl_interp_ratio 1
cl_updaterate 128
cl_cmdrate 128
</code></pre>

<h3>VAC Authentication Error</h3>
<ol>
    <li><strong>Ensure sv_lan 0</strong></li>
    <li><strong>Verify GSLT is valid and not banned</strong></li>
    <li><strong>Check server files integrity:</strong>
        <pre><code>./steamcmd.sh
login anonymous
force_install_dir /path/to/csgo-server
app_update 740 validate
quit
</code></pre>
    </li>
    <li><strong>Restart server after updates</strong></li>
</ol>

<h3>Can't Hear Voice Chat</h3>
<ol>
    <li><strong>Check voice settings in server.cfg:</strong>
        <pre><code>sv_use_steam_voice 1
sv_voiceenable 1
</code></pre>
    </li>
    <li><strong>Verify UDP ports open:</strong> 27015, 27020</li>
    <li><strong>Test with different voice_loopback values:</strong>
        <pre><code>voice_loopback 1  // Hear yourself (testing)
</code></pre>
    </li>
</ol>

<h2 id="gamemodes">Game Modes Configuration</h2>

<h3>Competitive 5v5 (128 tick)</h3>
<pre><code>+game_type 0 +game_mode 1 -tickrate 128 +maxplayers 10
exec gamemode_competitive.cfg
</code></pre>

<h3>Casual 10v10</h3>
<pre><code>+game_type 0 +game_mode 0 +maxplayers 20
exec gamemode_casual.cfg
</code></pre>

<h3>Deathmatch</h3>
<pre><code>+game_type 1 +game_mode 2 +maxplayers 20
mp_respawn_on_death_t 1
mp_respawn_on_death_ct 1
mp_respawnwavetime 3
mp_timelimit 10
mp_dm_bonus_length_max 30
</code></pre>

<h3>Arms Race</h3>
<pre><code>+game_type 1 +game_mode 0 +maxplayers 12
mp_ggprogressive_round_restart_delay 3
mp_timelimit 20
mp_maxrounds 3
</code></pre>

<h3>Wingman 2v2</h3>
<pre><code>+game_type 0 +game_mode 2 +maxplayers 4
exec gamemode_competitive.cfg
mp_maxrounds 16
mp_overtime_maxrounds 4
</code></pre>

<h3>Custom Modes</h3>

<h4>Surf</h4>
<p>Download surf maps and configure:</p>
<pre><code>sv_airaccelerate 150
sv_staminajumpcost 0
sv_staminalandcost 0
sv_accelerate 10
sv_friction 4
</code></pre>

<h4>Bunny Hop</h4>
<pre><code>sv_enablebunnyhopping 1
sv_autobunnyhopping 1
sv_airaccelerate 1000
sv_staminajumpcost 0
sv_staminalandcost 0
</code></pre>

<h4>1v1 Arena</h4>
<p>Use arena plugin and configure multiple arenas on one map.</p>

<h2>Performance Optimization</h2>

<h3>CPU Affinity (Linux)</h3>
<pre><code># Bind server to specific CPU cores
taskset -c 0,1,2,3 ./srcds_run -game csgo ...
</code></pre>

<h3>Process Priority</h3>
<pre><code># Run with higher priority
nice -n -10 ./srcds_run -game csgo ...
</code></pre>

<h3>Network Optimization</h3>
<pre><code># Increase network buffers (Linux)
sudo sysctl -w net.core.rmem_max=16777216
sudo sysctl -w net.core.wmem_max=16777216
sudo sysctl -w net.ipv4.tcp_rmem="4096 87380 16777216"
sudo sysctl -w net.ipv4.tcp_wmem="4096 65536 16777216"
</code></pre>

<h3>Automate Updates</h3>
<pre><code>#!/bin/bash
# update_csgo.sh
cd /home/steam/steamcmd
./steamcmd.sh +login anonymous +force_install_dir /path/to/csgo-server +app_update 740 validate +quit

# Kill and restart server
killall -9 srcds_linux
sleep 5
cd /path/to/csgo-server
./srcds_run -game csgo +map de_dust2 ...
</code></pre>

<h2>Additional Resources</h2>
<ul>
    <li><a href="https://developer.valvesoftware.com/wiki/Counter-Strike:_Global_Offensive_Dedicated_Servers" target="_blank">Valve Developer Wiki - CS:GO Dedicated Servers</a></li>
    <li><a href="https://developer.valvesoftware.com/wiki/Counter-Strike_2/Dedicated_Servers" target="_blank">Valve Developer Wiki - CS2 Dedicated Servers</a></li>
    <li><a href="https://www.sourcemod.net/" target="_blank">SourceMod Official Site</a></li>
    <li><a href="https://forums.alliedmods.net/" target="_blank">AlliedModders Forums</a></li>
    <li><a href="https://steamcommunity.com/dev/managegameservers" target="_blank">Steam GSLT Management</a></li>
    <li><a href="https://github.com/GameServerManagers/LinuxGSM" target="_blank">LinuxGSM - Game Server Management</a></li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8; margin: 0;">
        <li>Always obtain and use a valid Game Server Login Token (GSLT)</li>
        <li>Keep server files updated via SteamCMD</li>
        <li>Monitor server resources (CPU, RAM, network)</li>
        <li>Use strong RCON password</li>
        <li>Configure firewall properly for security</li>
        <li>Join CS:GO/CS2 server admin communities for support</li>
    </ul>
</div>

<p style="text-align: center; margin-top: 30px; color: #666;">
    <em>Last updated: November 2024 | Covers CS:GO & CS2</em>
</p>
