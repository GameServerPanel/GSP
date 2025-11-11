<?php
/**
 * Counter-Strike: Global Offensive / CS2 Server Documentation
 * Comprehensive game server hosting guide
 * Enhanced with ports table and complete troubleshooting
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
        <a href="#gamemodes" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Game Modes</a>
    </div>
</div>

<h1>Counter-Strike: Global Offensive & CS2 Server Hosting Guide</h1>

<h2 id="overview">Overview</h2>
<p>Counter-Strike: Global Offensive (CS:GO) and Counter-Strike 2 (CS2) are competitive tactical first-person shooters developed by Valve. CS2 replaced CS:GO in September 2023, transitioning from Source 1 to Source 2 engine.</p>

<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Quick Reference</h3>
    <ul style="color: #e5e7eb; line-height: 1.8; margin: 0;">
        <li><strong style="color: #ffffff;">Engine:</strong> Source 2 (CS2) / Source (CS:GO)</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27015</code> (UDP)</li>
        <li><strong style="color: #ffffff;">Minimum RAM:</strong> 2GB (CS:GO), 4GB (CS2)</li>
        <li><strong style="color: #ffffff;">Recommended RAM:</strong> 4GB+ (CS:GO), 8GB+ (CS2)</li>
        <li><strong style="color: #ffffff;">CPU:</strong> High single-thread performance critical</li>
        <li><strong style="color: #ffffff;">SteamCMD App ID:</strong> 740 (CS:GO), 730 (CS2)</li>
        <li><strong style="color: #ffffff;">GSLT Required:</strong> Yes (<a href="https://steamcommunity.com/dev/managegameservers" target="_blank" style="color: #a5b4fc;">Get Token</a>)</li>
        <li><strong style="color: #ffffff;">Log Files:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">csgo/logs/</code> or <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">game/csgo/logs/</code></li>
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
            <td style="padding: 12px; border-bottom: 1px solid #334155;">SourceTV (GOTV) spectator port</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #713f12; padding: 4px 8px; border-radius: 3px; color: #fef3c7;">Optional</span></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">27005</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Client port (Steam connection)</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #713f12; padding: 4px 8px; border-radius: 3px; color: #fef3c7;">Optional</span></td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">27035-27036</code></td>
            <td style="padding: 12px;"><span style="background: #1e40af; padding: 4px 8px; border-radius: 3px; color: #dbeafe;">TCP</span></td>
            <td style="padding: 12px;">Steam P2P communication (outbound)</td>
            <td style="padding: 12px;"><span style="background: #713f12; padding: 4px 8px; border-radius: 3px; color: #fef3c7;">Optional</span></td>
        </tr>
    </tbody>
</table>

<h3>Firewall Configuration Examples</h3>

<h4>UFW (Ubuntu/Debian)</h4>
<pre><code>sudo ufw allow 27015/udp comment 'CS:GO/CS2 game port'
sudo ufw allow 27015/tcp comment 'CS:GO/CS2 RCON'
sudo ufw allow 27020/udp comment 'CS:GO/CS2 SourceTV'
sudo ufw allow 27005/udp comment 'CS:GO/CS2 client port'
sudo ufw reload
</code></pre>

<h4>FirewallD (CentOS/RHEL/Fedora)</h4>
<pre><code>sudo firewall-cmd --permanent --add-port=27015/udp --add-port=27015/tcp
sudo firewall-cmd --permanent --add-port=27020/udp --add-port=27005/udp
sudo firewall-cmd --reload
</code></pre>

<h4>Windows Firewall</h4>
<pre><code># Run in PowerShell as Administrator
New-NetFirewallRule -DisplayName "CS:GO/CS2 UDP" -Direction Inbound -Protocol UDP -LocalPort 27015,27020,27005 -Action Allow
New-NetFirewallRule -DisplayName "CS:GO/CS2 TCP" -Direction Inbound -Protocol TCP -LocalPort 27015 -Action Allow
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
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;">
    <div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; border-radius: 4px;">
        <h4 style="color: #ffffff; margin-top: 0;">CS:GO Server</h4>
        <ul style="color: #e5e7eb; line-height: 1.8;">
            <li><strong style="color: #fff;">OS:</strong> Linux (Ubuntu 18.04+) / Windows Server 2012+</li>
            <li><strong style="color: #fff;">CPU:</strong> Dual-core 3GHz+ (quad-core recommended)</li>
            <li><strong style="color: #fff;">RAM:</strong> 2GB minimum, 4GB+ recommended</li>
            <li><strong style="color: #fff;">Disk:</strong> 30GB SSD recommended</li>
            <li><strong style="color: #fff;">Network:</strong> 10Mbps+ (1Mbps per player)</li>
        </ul>
    </div>
    <div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #10b981; border-radius: 4px;">
        <h4 style="color: #ffffff; margin-top: 0;">CS2 Server</h4>
        <ul style="color: #e5e7eb; line-height: 1.8;">
            <li><strong style="color: #fff;">OS:</strong> Linux (Ubuntu 20.04+) / Windows Server 2016+</li>
            <li><strong style="color: #fff;">CPU:</strong> Quad-core 3.5GHz+ (6-core recommended)</li>
            <li><strong style="color: #fff;">RAM:</strong> 4GB minimum, 8GB+ recommended</li>
            <li><strong style="color: #fff;">Disk:</strong> 50GB SSD (Source 2 engine larger)</li>
            <li><strong style="color: #fff;">Network:</strong> 15Mbps+ (1.5Mbps per player)</li>
        </ul>
    </div>
</div>

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

<h4>CS:GO Server Installation</h4>
<pre><code># Run SteamCMD
./steamcmd.sh

# Login anonymously
login anonymous

# Set install directory
force_install_dir ./csgo-server

# Install CS:GO dedicated server (App ID 740)
app_update 740 validate

# Exit
quit
</code></pre>

<h4>CS2 Server Installation</h4>
<pre><code># Run SteamCMD
./steamcmd.sh

# Login (CS2 may require Steam account with CS2)
login anonymous
# Or: login <username> <password>

# Set install directory
force_install_dir ./cs2-server

# Install CS2 dedicated server (App ID 730)
app_update 730 validate

# Exit
quit
</code></pre>

<h3>Windows Installation</h3>
<ol>
    <li>Download <a href="https://steamcdn-a.akamaihd.net/client/installer/steamcmd.zip" target="_blank">SteamCMD for Windows</a></li>
    <li>Extract to <code>C:\steamcmd\</code></li>
    <li>Run <code>steamcmd.exe</code></li>
    <li>Execute same commands as Linux (use Windows paths)</li>
</ol>

<h2 id="configuration">⚙️ Server Configuration</h2>

<h3>server.cfg - Essential Settings</h3>
<p>Create <code>csgo/cfg/server.cfg</code> (CS:GO) or <code>game/csgo/cfg/server.cfg</code> (CS2):</p>
<pre><code>// ========================================
// Server Information
// ========================================
hostname "My CS:GO/CS2 Server [128 Tick]"
sv_password ""                      // Server password (blank = public)
sv_region "1"                       // 0=US East, 1=US West, 2=SA, 3=EU, 4=Asia, etc.
sv_tags "128tick,competitive"       // Server browser tags

// ========================================
// RCON Configuration
// ========================================
rcon_password "YourSecurePasswordHere"  // CHANGE THIS!
sv_rcon_banpenalty 0
sv_rcon_maxfailures 5
sv_rcon_minfailures 3
sv_rcon_minfailuretime 30

// ========================================
// Server Core Settings
// ========================================
sv_cheats 0
sv_lan 0
sv_pure 1                           // 0=off, 1=on, 2=strict file consistency
sv_pure_kick_clients 1
sv_minrate 128000                   // Min bandwidth rate (128 tick)
sv_maxrate 0                        // Max bandwidth (0=unlimited)
sv_mincmdrate 128                   // Min client update rate
sv_maxcmdrate 128                   // Max client update rate
sv_minupdaterate 128                // Min server update rate
sv_maxupdaterate 128                // Max server update rate

// ========================================
// Game Settings - Competitive 5v5
// ========================================
game_type 0                         // 0=Classic, 1=Arms Race, etc.
game_mode 1                         // 0=Casual, 1=Competitive, 2=Wingman
mp_teamcashawards 1
mp_playercashawards 1
mp_maxmoney 16000
mp_startmoney 800
mp_buytime 90
mp_buy_anywhere 0
mp_freezetime 15
mp_friendlyfire 0
mp_autoteambalance 1
mp_limitteams 1
mp_maxrounds 30
mp_roundtime 1.92                   // Round time (minutes)
mp_roundtime_defuse 1.92
mp_roundtime_hostage 1.92
mp_c4timer 40                       // C4 bomb timer (seconds)

// ========================================
// Overtime Settings
// ========================================
mp_overtime_enable 1
mp_overtime_maxrounds 6
mp_overtime_startmoney 10000
mp_overtime_halftime_pausetimer 1

// ========================================
// Warmup & Match Settings
// ========================================
mp_do_warmup_period 1
mp_warmuptime 30
mp_warmup_pausetimer 1
mp_halftime 1
mp_halftime_duration 15
mp_match_end_restart 1
mp_match_restart_delay 15

// ========================================
// Communication
// ========================================
sv_alltalk 0                        // Dead players can't talk to alive
sv_deadtalk 0                       // Dead players can't be heard
sv_full_alltalk 0
sv_talk_enemy_dead 1                // Dead can hear enemy team
sv_talk_enemy_living 0              // Living can't hear enemy team

// ========================================
// Voting
// ========================================
sv_vote_issue_kick_allowed 0        // Disable kick votes
sv_vote_issue_changelevel_allowed 0
sv_vote_issue_nextlevel_allowed 0
sv_vote_allow_spectators 0

// ========================================
// SourceTV (GOTV) Configuration
// ========================================
tv_enable 1
tv_delay 90                         // 90 second delay (anti-cheat)
tv_advertise_watchable 1            // List in server browser
tv_name "GOTV"
tv_title "Source TV"
tv_autorecord 1                     // Auto-record matches
tv_allow_camera_man 1
tv_maxclients 10                    // Max GOTV spectators

// ========================================
// Logging
// ========================================
log on
sv_logbans 1
sv_logecho 1
sv_logfile 1
sv_log_onefile 0                    // New log file each map

// ========================================
// Security & Protection
// ========================================
sv_steamauth_enforce 2              // Strict Steam authentication
sv_allow_wait_command 0             // Disable wait command (anti-exploit)
sv_maxconsecutive losses_max 2      // Disconnect laggy players

// ========================================
// Execute Additional Configs
// ========================================
exec banned_user.cfg
exec banned_ip.cfg
</code></pre>

<h3>Game Mode Configuration</h3>

<h4>Competitive 5v5 (server.cfg above already configured)</h4>
<pre><code>game_type 0
game_mode 1
mp_maxrounds 30
mp_roundtime 1.92
</code></pre>

<h4>Casual 10v10</h4>
<pre><code>game_type 0
game_mode 0
mp_maxrounds 10
mp_roundtime 3
mp_friendlyfire 0
</code></pre>

<h4>Wingman 2v2</h4>
<pre><code>game_type 0
game_mode 2
mp_maxrounds 16
mp_roundtime 1.92
</code></pre>

<h4>Deathmatch</h4>
<pre><code>game_type 1
game_mode 2
mp_respawn_on_death_ct 1
mp_respawn_on_death_t 1
</code></pre>

<h2 id="parameters">Startup Parameters</h2>

<h3>Linux Start Script (srcds_run)</h3>
<pre><code>#!/bin/bash
# CS:GO/CS2 Server Startup Script

cd /home/steam/csgo-server  # or cs2-server

./srcds_run \
    -game csgo \
    -console \
    -usercon \
    +ip 0.0.0.0 \
    -port 27015 \
    +game_type 0 \
    +game_mode 1 \
    +mapgroup mg_active \
    +map de_dust2 \
    -tickrate 128 \
    +maxplayers 10 \
    +sv_setsteamaccount "YOUR_GSLT_TOKEN_HERE" \
    +sv_lan 0 \
    +exec server.cfg \
    +tv_port 27020 \
    +tv_enable 1
</code></pre>

<h3>Windows Startup (srcds.exe)</h3>
<pre><code>srcds.exe ^
    -game csgo ^
    -console ^
    -usercon ^
    +ip 0.0.0.0 ^
    -port 27015 ^
    +game_type 0 ^
    +game_mode 1 ^
    +mapgroup mg_active ^
    +map de_dust2 ^
    -tickrate 128 ^
    +maxplayers 10 ^
    +sv_setsteamaccount "YOUR_GSLT_TOKEN_HERE" ^
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
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">-game csgo</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Specify game directory (csgo for both CS:GO and CS2)</td>
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
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">-tickrate 128</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Server tick rate (64 default, 128 competitive)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">+maxplayers 10</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Maximum player slots</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">+sv_setsteamaccount</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Game Server Login Token (REQUIRED for public servers)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">+game_type</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">0=Classic, 1=Arms Race, 2=Demolition, 3=Deathmatch</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">+game_mode</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">0=Casual, 1=Competitive, 2=Wingman/Skirmish</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">+mapgroup</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Map rotation group (mg_active, mg_reserves, etc.)</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">+tv_port 27020</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">SourceTV (GOTV) port</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">+exec server.cfg</code></td>
            <td style="padding: 12px;">Execute server configuration file</td>
        </tr>
    </tbody>
</table>

<h2 id="plugins">Plugins & Mods</h2>

<h3>SourceMod & MetaMod:Source</h3>
<p>Most popular server modification framework for Source engine games.</p>

<h4>Installation</h4>
<ol>
    <li>Download <a href="https://www.metamodsource.net/downloads.php" target="_blank">MetaMod:Source</a> (get latest stable build)</li>
    <li>Download <a href="https://www.sourcemod.net/downloads.php" target="_blank">SourceMod</a> (get latest stable build)</li>
    <li>Extract both to <code>csgo/</code> directory (they merge with existing folders)</li>
    <li>Restart server</li>
    <li>Type <code>sm version</code> in console to verify</li>
</ol>

<h4>Popular SourceMod Plugins</h4>
<ul>
    <li><strong>Admin System:</strong> Built-in admin management (edit <code>configs/admins_simple.ini</code>)</li>
    <li><strong>PugSetup:</strong> 10-man competitive match setup (ReadyUp, map voting, team selection)</li>
    <li><strong>Get5:</strong> Match management and configuration system</li>
    <li><strong>Retakes:</strong> Automated retake scenarios (B site, A site practice)</li>
    <li><strong>Deathmatch:</strong> Respawn, weapon menus, spawn protection</li>
    <li><strong>Advertisements:</strong> Server advertisements in chat</li>
    <li><strong>MapChooser Extended:</strong> Advanced map voting system</li>
    <li><strong>RankMe:</strong> Player statistics and ranking</li>
</ul>

<h4>Installing Plugins</h4>
<pre><code># Download .smx file (compiled plugin)
# Place in: csgo/addons/sourcemod/plugins/

# If .sp file (source):
cd csgo/addons/sourcemod/scripting
./compile.sh pluginname.sp
# Compiled .smx appears in compiled/ directory
mv compiled/pluginname.smx ../plugins/

# Reload plugins without restart
sm plugins reload pluginname
</code></pre>

<h3>Workshop Maps (CS:GO/CS2)</h3>
<pre><code># Add to server.cfg or startup params
host_workshop_collection "COLLECTION_ID"
workshop_start_map "MAP_ID"

# Or in startup command
+host_workshop_collection 125499818 +workshop_start_map 125488374
</code></pre>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Won't Start</h3>

<h4>Missing GSLT Token</h4>
<pre><code>[ERROR] Failed to contact master server

# Fix: Get GSLT token from Steam
# https://steamcommunity.com/dev/managegameservers
# Add to startup: +sv_setsteamaccount "YOUR_TOKEN"
</code></pre>

<h4>Port Already in Use</h4>
<pre><code># Check what's using port 27015
sudo lsof -i :27015
# Or on Windows:
netstat -ano | findstr :27015

# Kill existing process or change port
./srcds_run -game csgo -port 27016 ...
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
    <li><strong>Check GSLT token:</strong> Must be valid and not VAC banned</li>
    <li><strong>Verify sv_lan:</strong> Must be <code>sv_lan 0</code> (not LAN mode)</li>
    <li><strong>Check firewall:</strong> UDP 27015 must be open</li>
    <li><strong>Wait 5-10 minutes:</strong> Steam master server updates are slow</li>
    <li><strong>Use direct IP:</strong> <code>connect IP:27015</code> in console</li>
</ul>

<h4>Players Can't Connect</h4>
<pre><code># Test from external location
nc -u -v YOUR_SERVER_IP 27015

# Check server status
status
sv_lan
</code></pre>

<h4>High Ping / Lag</h4>
<ul>
    <li><strong>Enable sv_pure:</strong> <code>sv_pure 1</code> (file consistency checking)</li>
    <li><strong>Check rates:</strong> Ensure <code>sv_minrate 128000</code> for 128 tick</li>
    <li><strong>Monitor resources:</strong> <code>top</code> or <code>htop</code> - CPU at 100%?</li>
    <li><strong>Network bandwidth:</strong> 1Mbps per player minimum</li>
    <li><strong>Geographic location:</strong> Host server near player base</li>
</ul>

<h3>Performance Issues</h3>

<h4>Low FPS / Stuttering</h4>
<ul>
    <li><strong>CPU bottleneck:</strong> CS requires high single-thread performance</li>
    <li><strong>Reduce tick rate:</strong> Try <code>-tickrate 64</code> instead of 128</li>
    <li><strong>Lower player count:</strong> Reduce <code>maxplayers</code></li>
    <li><strong>Disable SourceTV:</strong> <code>tv_enable 0</code> saves resources</li>
    <li><strong>Check plugins:</strong> Disable SourceMod plugins one-by-one to identify issues</li>
</ul>

<h4>Memory Usage High</h4>
<pre><code># Monitor memory
free -h
htop

# CS2 uses more RAM than CS:GO (Source 2 engine)
# Ensure 8GB+ available for CS2, 4GB+ for CS:GO
</code></pre>

<h3>Plugin/Mod Issues</h3>

<h4>SourceMod Not Loading</h4>
<pre><code># Check MetaMod loaded first
meta list

# Check SourceMod
sm version

# Enable developer mode
developer 1

# Check logs
tail -f csgo/logs/latest.log
tail -f csgo/addons/sourcemod/logs/errors_*.txt
</code></pre>

<h4>Plugin Crashes Server</h4>
<ul>
    <li><strong>Remove plugin:</strong> Move .smx file out of <code>plugins/</code> folder</li>
    <li><strong>Check compatibility:</strong> Ensure plugin supports your game version</li>
    <li><strong>Update SourceMod:</strong> Get latest stable build</li>
    <li><strong>Check logs:</strong> <code>addons/sourcemod/logs/errors_*.txt</code></li>
</ul>

<h3>Map Issues</h3>

<h4>Workshop Map Won't Download</h4>
<pre><code># Verify server can access Steam Workshop
# Check firewall allows outbound HTTPS (443)

# Manual workshop download
# Use tool like DepotDownloader or CSGO Server Launcher
# Place .bsp in csgo/maps/
# Place .nav in csgo/maps/
# Place other files in csgo/maps/workshop/
</code></pre>

<h4>Custom Map Missing Resources</h4>
<ul>
    <li><strong>FastDL:</strong> Set up fast download server for custom content</li>
    <li><strong>sv_downloadurl:</strong> <code>sv_downloadurl "http://yoursite.com/csgo/"</code></li>
    <li><strong>Use Workshop:</strong> Upload custom maps to Steam Workshop</li>
</ul>

<h3>Security Issues</h3>

<h4>Server Hacked / Unauthorized Access</h4>
<ul>
    <li><strong>Change RCON password immediately:</strong> Strong password (20+ chars)</li>
    <li><strong>Check admins:</strong> Review <code>configs/admins_simple.ini</code></li>
    <li><strong>Update server:</strong> <code>app_update 740 validate</code></li>
    <li><strong>Firewall RCON:</strong> Block TCP 27015 or whitelist IPs only</li>
    <li><strong>Monitor logs:</strong> Check for suspicious RCON commands</li>
</ul>

<h2 id="gamemodes">Game Modes</h2>

<h3>Competitive 5v5</h3>
<pre><code>game_type 0
game_mode 1
mp_maxrounds 30
mp_roundtime 1.92
mp_c4timer 40
mp_startmoney 800
mp_maxmoney 16000
-tickrate 128
</code></pre>

<h3>Casual 10v10</h3>
<pre><code>game_type 0
game_mode 0
mp_maxrounds 10
mp_roundtime 3
mp_friendlyfire 0
mp_autokick 0
-tickrate 64
</code></pre>

<h3>Wingman 2v2</h3>
<pre><code>game_type 0
game_mode 2
mp_maxrounds 16
mp_roundtime 1.92
mp_maxplayers 4
-tickrate 128
</code></pre>

<h3>Deathmatch</h3>
<pre><code>game_type 1
game_mode 2
mp_respawn_on_death_ct 1
mp_respawn_on_death_t 1
mp_respawnwavetime_ct 0
mp_respawnwavetime_t 0
mp_timelimit 10
-tickrate 64
</code></pre>

<h3>Retakes (Requires Plugin)</h3>
<p>Install <a href="https://github.com/splewis/csgo-retakes" target="_blank">Retakes plugin</a> via SourceMod.</p>
<pre><code>sm_retakes_enabled 1
sm_retakes_scramble_teams 1
sm_retakes_max_players 10
</code></pre>

<h3>1v1 Arena (Requires Plugin)</h3>
<p>Install <a href="https://github.com/splewis/csgo-multi-1v1" target="_blank">Multi-1v1 plugin</a> via SourceMod.</p>

<h2>Performance Optimization</h2>
<ul>
    <li><strong>Use SSD storage:</strong> Faster map loads and asset streaming</li>
    <li><strong>128 tick requires good CPU:</strong> 3.5GHz+ single-thread performance</li>
    <li><strong>Limit SourceTV spectators:</strong> <code>tv_maxclients 5</code></li>
    <li><strong>Disable unnecessary logs:</strong> Reduce I/O overhead</li>
    <li><strong>Monitor resources:</strong> <code>htop</code>, <code>iotop</code>, <code>nethogs</code></li>
    <li><strong>Geographic proximity:</strong> Host near player base for low ping</li>
    <li><strong>Dedicated server:</strong> Don't run on shared hosting</li>
</ul>

<h2>Security Best Practices</h2>
<ul>
    <li><strong>Strong RCON password:</strong> 20+ characters, random</li>
    <li><strong>Firewall RCON port:</strong> Whitelist admin IPs only (TCP 27015)</li>
    <li><strong>Keep server updated:</strong> Weekly <code>app_update</code> via SteamCMD</li>
    <li><strong>Use sv_pure:</strong> <code>sv_pure 1</code> or <code>2</code> for competitive integrity</li>
    <li><strong>Monitor logs:</strong> Watch for exploit attempts</li>
    <li><strong>Secure SourceMod admins:</strong> Use Steam ID authentication, not passwords</li>
    <li><strong>Disable unnecessary services:</strong> Unused ports, SSH password auth, etc.</li>
</ul>

<h2>Updating Server</h2>
<pre><code># Stop server gracefully
rcon quit
# Or: killall srcds_linux (Linux) / taskkill /IM srcds.exe (Windows)

# Run SteamCMD update
cd /home/steam/steamcmd
./steamcmd.sh +login anonymous +force_install_dir /path/to/csgo-server +app_update 740 validate +quit

# Restart server
cd /path/to/csgo-server
./srcds_run -game csgo +map de_dust2 ...
</code></pre>

<h2>Additional Resources</h2>
<ul>
    <li><a href="https://developer.valvesoftware.com/wiki/Counter-Strike:_Global_Offensive_Dedicated_Servers" target="_blank">Valve Developer Wiki - CS:GO Dedicated Servers</a></li>
    <li><a href="https://developer.valvesoftware.com/wiki/Counter-Strike_2/Dedicated_Servers" target="_blank">Valve Developer Wiki - CS2 Dedicated Servers</a></li>
    <li><a href="https://www.sourcemod.net/" target="_blank">SourceMod Official Site</a></li>
    <li><a href="https://www.metamodsource.net/" target="_blank">MetaMod:Source Official Site</a></li>
    <li><a href="https://forums.alliedmods.net/" target="_blank">AlliedModders Forums</a></li>
    <li><a href="https://steamcommunity.com/dev/managegameservers" target="_blank">Steam Game Server Login Tokens (GSLT)</a></li>
    <li><a href="https://github.com/GameServerManagers/LinuxGSM" target="_blank">LinuxGSM - Server Management Scripts</a></li>
    <li><a href="https://www.reddit.com/r/GlobalOffensive/" target="_blank">r/GlobalOffensive Reddit Community</a></li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8; margin: 0;">
        <li>Always obtain and use a valid <strong>Game Server Login Token (GSLT)</strong> for public servers</li>
        <li>Keep server files updated via SteamCMD (<code>app_update 740 validate</code>)</li>
        <li>Monitor server resources (CPU, RAM, network bandwidth)</li>
        <li>Use <strong>strong RCON password</strong> and secure firewall rules</li>
        <li>Configure firewall properly - UDP 27015 must be accessible</li>
        <li>128 tick requires good CPU (3.5GHz+ single-thread) and bandwidth (1Mbps per player)</li>
        <li>CS2 requires more resources than CS:GO (Source 2 engine)</li>
        <li>Join CS:GO/CS2 server admin communities for support and updates</li>
    </ul>
</div>

<p style="text-align: center; margin-top: 30px; color: #666;">
    <em>Last updated: January 2025 | Covers CS:GO & CS2 | Complete guide with ports, configs, troubleshooting</em>
</p>
