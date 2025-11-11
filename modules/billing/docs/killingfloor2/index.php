<?php
/**
 * Killing Floor 2 Server Documentation
 */
?>
<h1>📚 Killing Floor 2 Server Guide</h1>

<h3 style="color: #94a3b8; margin-top: 8px;">Wave-Based Co-Op Survival - Comprehensive Setup</h3>

<div style="background: #1e3a5f; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #3b82f6;">
    <h3 style="color: #ffffff; margin-top: 0;">📋 Quick Info</h3>
    <table style="width: 100%; color: #e5e7eb;">
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Engine:</strong></td><td>Unreal Engine 3 (Modified)</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Developer:</strong></td><td>Tripwire Interactive</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">App ID:</strong></td><td>232130</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Game Port:</strong></td><td>7777 UDP (default)</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Query Port:</strong></td><td>27015 UDP (default)</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Web Admin:</strong></td><td>8080 TCP (default)</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Max Players:</strong></td><td>6 (standard), up to 32 with mods</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Game Modes:</strong></td><td>Survival, Endless, Weekly Outbreak, Versus Survival</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Perks:</strong></td><td>10 unique classes (Berserker, Commando, Medic, etc.)</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Platform:</strong></td><td>Windows, Linux</td></tr>
    </table>
</div>

<h3 style="margin-top: 30px;">Navigation</h3>
<ul style="line-height: 2; font-size: 1.05em;">
    <li><a href="#overview">Overview</a></li>
    <li><a href="#ports">🔌 Ports & Firewall</a></li>
    <li><a href="#installation">Installation</a></li>
    <li><a href="#configuration">⚙️ Configuration</a></li>
    <li><a href="#webadmin">Web Admin Interface</a></li>
    <li><a href="#gamemodes">Game Modes & Maps</a></li>
    <li><a href="#perks">Perks & Difficulty</a></li>
    <li><a href="#startup">Startup Commands</a></li>
    <li><a href="#troubleshooting">🔧 Troubleshooting</a></li>
    <li><a href="#resources">Resources</a></li>
</ul>

<h2 id="overview">Overview</h2>
<p><strong>Killing Floor 2</strong> is a brutal wave-based co-op FPS where teams of up to 6 players fight through hordes of genetically engineered monsters (Zeds) developed by the Horzine Biotech Corporation. Each player selects a unique Perk class with specialized weapons and abilities.</p>

<p>The game features a sophisticated economy system where players earn "Dosh" during waves to buy weapons, ammo, and armor. Between waves, players strategically shop at Trader Pods scattered across maps. The action culminates in an epic boss battle after the final wave.</p>

<h3>Key Features</h3>
<ul>
    <li><strong>10 Perk Classes:</strong> Berserker, Commando, Medic, Demolitionist, Firebug, Gunslinger, Sharpshooter, Support, SWAT, Survivalist</li>
    <li><strong>Wave-Based Survival:</strong> 4, 7, or 10 waves with scaling difficulty</li>
    <li><strong>Boss Fights:</strong> Multiple unique bosses (Hans Volter, Patriarch, King Fleshpound, etc.)</li>
    <li><strong>MEAT System:</strong> Realistic gore and dismemberment system</li>
    <li><strong>Weapon Trading:</strong> Buy, upgrade, and share weapons with teammates</li>
    <li><strong>Difficulty Levels:</strong> Normal, Hard, Suicidal, Hell on Earth</li>
    <li><strong>Weekly Outbreaks:</strong> Special modifiers and challenges rotate weekly</li>
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
            <td style="padding: 12px; font-family: monospace; color: #a5b4fc;">7777</td>
            <td style="padding: 12px;">
                <span style="background: #22c55e; color: #ffffff; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold;">UDP</span>
            </td>
            <td style="padding: 12px;">Game server (main connection)</td>
            <td style="padding: 12px; color: #22c55e; font-weight: bold;">✓ Yes</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; font-family: monospace; color: #a5b4fc;">27015</td>
            <td style="padding: 12px;">
                <span style="background: #22c55e; color: #ffffff; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold;">UDP</span>
            </td>
            <td style="padding: 12px;">Query port (server browser)</td>
            <td style="padding: 12px; color: #22c55e; font-weight: bold;">✓ Yes</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; font-family: monospace; color: #a5b4fc;">8080</td>
            <td style="padding: 12px;">
                <span style="background: #3b82f6; color: #ffffff; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold;">TCP</span>
            </td>
            <td style="padding: 12px;">Web Admin interface</td>
            <td style="padding: 12px; color: #94a3b8;">Optional (Recommended)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; font-family: monospace; color: #a5b4fc;">20560</td>
            <td style="padding: 12px;">
                <span style="background: #22c55e; color: #ffffff; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold;">UDP</span>
            </td>
            <td style="padding: 12px;">Steam server list port</td>
            <td style="padding: 12px; color: #94a3b8;">Optional</td>
        </tr>
    </tbody>
</table>

<div style="background: #1e3a5f; padding: 15px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <p style="color: #e5e7eb; margin: 0;"><strong>💡 Note:</strong> Port numbers are configurable. Defaults shown above. Multiple servers require unique port combinations (e.g., Game: 7777, 7778; Query: 27015, 27016).</p>
</div>

<h3>Firewall Configuration Examples</h3>

<h4>Ubuntu/Debian (UFW)</h4>
<pre><code># Allow KF2 game port
sudo ufw allow 7777/udp

# Allow query port (server browser)
sudo ufw allow 27015/udp

# Allow Web Admin (if using)
sudo ufw allow 8080/tcp

# Steam port (optional)
sudo ufw allow 20560/udp

# Enable firewall
sudo ufw enable
sudo ufw status
</code></pre>

<h4>CentOS/RHEL (FirewallD)</h4>
<pre><code># Add KF2 ports
sudo firewall-cmd --permanent --add-port=7777/udp
sudo firewall-cmd --permanent --add-port=27015/udp
sudo firewall-cmd --permanent --add-port=8080/tcp
sudo firewall-cmd --permanent --add-port=20560/udp
sudo firewall-cmd --reload
sudo firewall-cmd --list-ports
</code></pre>

<h4>Windows Firewall (PowerShell)</h4>
<pre><code># Game server
New-NetFirewallRule -DisplayName "KF2 Game Port" -Direction Inbound -LocalPort 7777 -Protocol UDP -Action Allow

# Query port
New-NetFirewallRule -DisplayName "KF2 Query Port" -Direction Inbound -LocalPort 27015 -Protocol UDP -Action Allow

# Web Admin
New-NetFirewallRule -DisplayName "KF2 Web Admin" -Direction Inbound -LocalPort 8080 -Protocol TCP -Action Allow
</code></pre>

<h4>iptables (Advanced)</h4>
<pre><code># Allow KF2 ports
iptables -A INPUT -p udp --dport 7777 -j ACCEPT
iptables -A INPUT -p udp --dport 27015 -j ACCEPT
iptables -A INPUT -p tcp --dport 8080 -j ACCEPT
iptables -A INPUT -p udp --dport 20560 -j ACCEPT

# Save rules (Ubuntu/Debian)
netfilter-persistent save

# Save rules (CentOS/RHEL)
service iptables save
</code></pre>

<h2 id="installation">Installation</h2>

<h3>Prerequisites</h3>
<ul>
    <li><strong>SteamCMD</strong> installed (<a href="https://developer.valvesoftware.com/wiki/SteamCMD" target="_blank">Installation Guide</a>)</li>
    <li>Disk space: ~30 GB for full installation</li>
    <li>RAM: 4GB minimum, 8GB recommended for 6 players</li>
    <li>Open firewall ports (7777, 27015, 8080 TCP)</li>
    <li>Linux: 64-bit system with required libraries</li>
</ul>

<h3>Linux Installation</h3>
<pre><code># Create server directory
mkdir -p ~/kf2_server
cd ~/kf2_server

# Install required libraries (Ubuntu/Debian)
sudo apt-get update
sudo apt-get install libstdc++6:i386 lib32gcc1 libcurl4-gnutls-dev:i386

# Download server files with SteamCMD
steamcmd +force_install_dir ~/kf2_server +login anonymous +app_update 232130 validate +quit

# Note: App ID 232130 is for Killing Floor 2 Dedicated Server
# Download size is approximately 25-30 GB
</code></pre>

<h3>Windows Installation</h3>
<pre><code># Download SteamCMD for Windows
# Extract to C:\steamcmd\

# Create server directory
mkdir C:\kf2_server

# Run SteamCMD
C:\steamcmd\steamcmd.exe +force_install_dir C:\kf2_server +login anonymous +app_update 232130 validate +quit
</code></pre>

<h2 id="configuration">⚙️ Configuration</h2>

<h3>Main Configuration Files</h3>
<p>KF2 server configuration is split across multiple .ini files in <code>KFGame/Config/</code>:</p>

<ul>
    <li><strong>PCServer-KFGame.ini</strong> - Main server settings (game mode, difficulty, maps)</li>
    <li><strong>PCServer-KFEngine.ini</strong> - Engine settings (ports, web admin, network)</li>
    <li><strong>KFWeb.ini</strong> - Web Admin configuration</li>
</ul>

<h3>PCServer-KFEngine.ini (Essential Settings)</h3>
<p>Edit <code>KFGame/Config/PCServer-KFEngine.ini</code>:</p>
<pre><code>[Engine.GameReplicationInfo]
ServerName=My Killing Floor 2 Server
ShortName=KF2

[IpDrv.TcpNetDriver]
AllowDownloads=True
ConnectionTimeout=60.0
InitialConnectTimeout=120.0
MaxClientRate=20000
MaxInternetClientRate=10000
NetServerMaxTickRate=30
LanServerMaxTickRate=35

[OnlineSubsystemSteamworks.KFWorkshopSteamworks]
ServerSubscribedWorkshopItems=  # Add Workshop item IDs here

[IpDrv.TcpNetDriver]
DownloadManagers=IpDrv.HTTPDownload

[IpDrv.HTTPDownload]
# Set up FastDL (optional)
# RedirectToURL=http://yourfastdl.com/kf2/

[Engine.AccessControl]
AdminPassword=YourAdminPassword
GamePassword=  # Leave empty for public server

[SystemSettings]
ResX=800
ResY=600
</code></pre>

<h3>PCServer-KFGame.ini (Game Settings)</h3>
<p>Edit <code>KFGame/Config/PCServer-KFGame.ini</code>:</p>
<pre><code>[KFGame.KFGameInfo]
ServerName=My KF2 Server
GameDifficulty=1  # 0=Normal, 1=Hard, 2=Suicidal, 3=Hell on Earth
GameLength=1  # 0=4 waves, 1=7 waves, 2=10 waves
bDisableMapVote=False
MapVoteDuration=30

[Engine.Game]
MaxPlayers=6
MaxSpectators=2

[KFGame.KFGameReplicationInfo]
bAllowGrenade=True

[SystemSettings]
bAllowBulletHitDecals=True
bAllowBloodDecals=True

# Map Rotation
[KFGame.KFMapSummary]
MapAssociation=(Name="KF-BurningParis",bUsesAuth=False)
MapAssociation=(Name="KF-BlackForest",bUsesAuth=False)
MapAssociation=(Name="KF-ContainmentStation",bUsesAuth=False)
# Add more maps as needed

[KFGame.KFGameInfo_Survival]
GameLengthOptions=(Name="Short", Weight=0)
GameLengthOptions=(Name="Medium", Weight=1)
GameLengthOptions=(Name="Long", Weight=2)
</code></pre>

<h3>Difficulty Levels</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Difficulty</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Value</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Zed Health</th>
            <th style="padding: #12px; text-align: left; color: #ffffff;">Recommended For</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Normal</strong></td>
            <td style="padding: 12px;"><code>0</code></td>
            <td style="padding: 12px;">100%</td>
            <td style="padding: 12px;">New players, casual play</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Hard</strong></td>
            <td style="padding: 12px;"><code>1</code></td>
            <td style="padding: 12px;">135%</td>
            <td style="padding: 12px;">Intermediate players</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Suicidal</strong></td>
            <td style="padding: 12px;"><code>2</code></td>
            <td style="padding: 12px;">185%</td>
            <td style="padding: 12px;">Experienced teams</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Hell on Earth</strong></td>
            <td style="padding: 12px;"><code>3</code></td>
            <td style="padding: 12px;">255%</td>
            <td style="padding: 12px;">Elite players, competitive</td>
        </tr>
    </tbody>
</table>

<h2 id="webadmin">Web Admin Interface</h2>

<h3>Enabling Web Admin</h3>
<p>Edit <code>KFGame/Config/KFWeb.ini</code>:</p>
<pre><code>[IpDrv.WebServer]
bEnabled=True
ListenPort=8080

[WebAdmin.WebAdmin]
bEnabled=True
AuthenticationClass=WebAdmin.MultiWebAdminAuth

[WebAdmin.Chatlog]
Filename=../Logs/chat.log
bIncludeTimeStamp=True
</code></pre>

<h3>Creating Admin Account</h3>
<pre><code># First admin created automatically at first Web Admin access
# Or manually edit: KFGame/Config/KFWebAdmin.ini

[WebAdmin.WebAdminSettings]
AdminName=admin
Password=yourpassword  # Will be hashed on first use
</code></pre>

<h3>Accessing Web Admin</h3>
<p>Open browser to: <code>http://your-server-ip:8080</code></p>

<h4>Web Admin Features</h4>
<ul>
    <li><strong>Dashboard:</strong> Server status, player count, current map</li>
    <li><strong>Current Game:</strong> Live player list, kick/ban controls</li>
    <li><strong>Settings:</strong> Change difficulty, game length, maps</li>
    <li><strong>Maps:</strong> Manage map rotation and voting</li>
    <li><strong>Access Policy:</strong> Ban management and IP whitelist</li>
    <li><strong>Console:</strong> Execute server commands remotely</li>
</ul>

<h2 id="gamemodes">Game Modes & Maps</h2>

<h3>Game Modes</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Mode</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Description</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Waves</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Survival</strong></td>
            <td style="padding: 12px;">Standard wave-based mode with boss at the end</td>
            <td style="padding: 12px;">4, 7, or 10 + Boss</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Endless</strong></td>
            <td style="padding: 12px;">Continuous waves with periodic boss fights</td>
            <td style="padding: 12px;">Infinite</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Weekly Outbreak</strong></td>
            <td style="padding: 12px;">Rotating special modifiers (Poundemonium, Bobble Zed, etc.)</td>
            <td style="padding: 12px;">4 waves</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Versus Survival</strong></td>
            <td style="padding: 12px;">PvP mode - players control Zeds vs Survivors</td>
            <td style="padding: 12px;">4 rounds</td>
        </tr>
    </tbody>
</table>

<h3>Official Maps (Selection)</h3>
<ul>
    <li><strong>KF-BurningParis</strong> - Burning streets of Paris</li>
    <li><strong>KF-BlackForest</strong> - German countryside castle</li>
    <li><strong>KF-BioticsLab</strong> - Horzine research facility</li>
    <li><strong>KF-Outpost</strong> - Desert military outpost</li>
    <li><strong>KF-ContainmentStation</strong> - Space station</li>
    <li><strong>KF-InfernalRealm</strong> - Halloween-themed hellscape</li>
    <li><strong>KF-Nightmare</strong> - Twisted carnival</li>
    <li><strong>KF-Prison</strong> - Abandoned prison facility</li>
</ul>

<h3>Workshop Content</h3>
<p>Subscribe to Workshop items and add to <code>PCServer-KFEngine.ini</code>:</p>
<pre><code>[OnlineSubsystemSteamworks.KFWorkshopSteamworks]
ServerSubscribedWorkshopItems=123456789  # Workshop item ID
ServerSubscribedWorkshopItems=987654321  # Add multiple lines for multiple items
</code></pre>

<h2 id="perks">Perks & Classes</h2>

<h3>The 10 Perks</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Perk</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Role</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Primary Weapons</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Berserker</strong></td>
            <td style="padding: 12px;">Tank/Melee DPS</td>
            <td style="padding: 12px;">Eviscerator, Pulverizer, Katana</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Commando</strong></td>
            <td style="padding: 12px;">Trash Killer</td>
            <td style="padding: 12px;">SCAR, AK-12, M16 M203</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Medic</strong></td>
            <td style="padding: 12px;">Healer/Support</td>
            <td style="padding: 12px;">Medic Assault Rifle, HMTech-501</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Demolitionist</strong></td>
            <td style="padding: 12px;">Explosive Specialist</td>
            <td style="padding: 12px;">RPG-7, M79, C4</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Firebug</strong></td>
            <td style="padding: 12px;">Area Denial/DoT</td>
            <td style="padding: 12px;">Flamethrower, Microwave Gun</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Gunslinger</strong></td>
            <td style="padding: 12px;">Precision DPS</td>
            <td style="padding: 12px;">Dual Magnums, AF2011-A1</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Sharpshooter</strong></td>
            <td style="padding: 12px;">Long-range Sniper</td>
            <td style="padding: 12px;">M14 EBR, Railgun, Crossbow</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Support</strong></td>
            <td style="padding: 12px;">Ammo/Armor Specialist</td>
            <td style="padding: 12px;">AA-12, Doomstick</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>SWAT</strong></td>
            <td style="padding: 12px;">Close-range Tank</td>
            <td style="padding: 12px;">Kriss, P90, MP7</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Survivalist</strong></td>
            <td style="padding: 12px;">Flexible/Hybrid</td>
            <td style="padding: 12px;">Any weapon type</td>
        </tr>
    </tbody>
</table>

<h2 id="startup">Startup Commands</h2>

<h3>Linux</h3>
<pre><code>#!/bin/bash
# start_kf2.sh

cd ~/kf2_server/Binaries/Win64

./KFGameSteamServer.bin.x86_64 \
  KF-BurningParis \
  ?Game=KFGameContent.KFGameInfo_Survival \
  ?Difficulty=1 \
  ?GameLength=1 \
  ?MaxPlayers=6 \
  -Port=7777 \
  -QueryPort=27015 \
  -WebAdminPort=8080
</code></pre>

<h3>Windows</h3>
<pre><code>@echo off
REM start_kf2.bat

cd C:\kf2_server\Binaries\Win64

KFServer.exe KF-BurningParis ^
  ?Game=KFGameContent.KFGameInfo_Survival ^
  ?Difficulty=1 ^
  ?GameLength=1 ^
  ?MaxPlayers=6 ^
  -Port=7777 ^
  -QueryPort=27015 ^
  -WebAdminPort=8080
</code></pre>

<h3>Startup Parameters</h3>
<ul>
    <li><code>KF-MapName</code> - Starting map</li>
    <li><code>?Game=</code> - Game mode class path</li>
    <li><code>?Difficulty=</code> - 0-3 (Normal, Hard, Suicidal, Hell on Earth)</li>
    <li><code>?GameLength=</code> - 0-2 (Short 4 waves, Medium 7, Long 10)</li>
    <li><code>?MaxPlayers=6</code> - Maximum players</li>
    <li><code>-Port=7777</code> - Game port</li>
    <li><code>-QueryPort=27015</code> - Server browser port</li>
    <li><code>-WebAdminPort=8080</code> - Web Admin port</li>
</ul>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not in Browser</h3>
<pre><code># Check ports are open
netstat -an | grep 7777
netstat -an | grep 27015

# Verify GamePassword is empty in PCServer-KFEngine.ini
[Engine.AccessControl]
GamePassword=

# Restart server and wait 5-10 minutes for Steam registration
</code></pre>

<h3>Web Admin Won't Load</h3>
<pre><code># Verify KFWeb.ini settings
[IpDrv.WebServer]
bEnabled=True
ListenPort=8080

# Check firewall allows TCP 8080
# Access via: http://server-ip:8080
</code></pre>

<h3>Performance Issues</h3>
<ul>
    <li>Reduce <code>MaxPlayers</code> to 4 or lower</li>
    <li>Set <code>NetServerMaxTickRate=20</code> in PCServer-KFEngine.ini</li>
    <li>Lower resolution: <code>ResX=640</code>, <code>ResY=480</code></li>
    <li>Disable blood decals: <code>bAllowBloodDecals=False</code></li>
</ul>

<h3>Workshop Content Not Downloading</h3>
<pre><code># Verify Workshop item IDs in PCServer-KFEngine.ini
[OnlineSubsystemSteamworks.KFWorkshopSteamworks]
ServerSubscribedWorkshopItems=123456789

# Check KFGame/Cache/ for downloaded content
# Restart server after adding new Workshop items
</code></pre>

<h3>Players Can't Join</h3>
<ul>
    <li>Check firewall allows UDP 7777 and 27015</li>
    <li>Verify <code>MaxPlayers</code> setting</li>
    <li>Ensure server isn't password-protected unless intended</li>
    <li>Check server logs in <code>KFGame/Logs/</code></li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-lightbulb" style="color: #fbbf24; margin-right: 8px;"></i>Pro Tips</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Web Admin:</strong> Essential for remote management - set it up first</li>
        <li><strong>Balanced Teams:</strong> 6 players is optimal for difficulty scaling</li>
        <li><strong>Perk Diversity:</strong> Encourage varied perk selection for team synergy</li>
        <li><strong>Workshop Maps:</strong> Keep official maps for 95% uptime, custom for variety</li>
        <li><strong>Weekly Outbreaks:</strong> Great for attracting players with fresh challenges</li>
        <li><strong>Suicidal+:</strong> Most active community plays Suicidal and Hell on Earth</li>
        <li><strong>Boss Selection:</strong> Can be configured to specific bosses or random</li>
        <li><strong>FastDL:</strong> Highly recommended for custom maps to reduce join times</li>
    </ul>
</div>

<h2 id="resources">Resources</h2>
<ul>
    <li><a href="https://wiki.killingfloor2.com/index.php?title=Dedicated_Server_(Killing_Floor_2)" target="_blank">Official KF2 Dedicated Server Wiki</a></li>
    <li><a href="https://steamcommunity.com/app/232090/discussions/1/" target="_blank">KF2 Server Hosting Forum</a></li>
    <li><a href="https://steamcommunity.com/app/232090/workshop/" target="_blank">KF2 Steam Workshop</a></li>
    <li><a href="https://forums.tripwireinteractive.com/index.php?forums/kf2-server-hosting.125/" target="_blank">Tripwire Interactive Server Hosting Forum</a></li>
    <li><a href="https://github.com/GenZmeY/KF2-SRV" target="_blank">KF2-SRV - Server Management Scripts</a></li>
</ul>