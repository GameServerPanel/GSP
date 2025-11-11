<?php
/**
 * Killing Floor Server Documentation
 */
?>
<h1>📚 Killing Floor Server Guide</h1>

<h3 style="color: #94a3b8; margin-top: 8px;">Original Wave-Based Horror Survival - Comprehensive Setup</h3>

<div style="background: #1e3a5f; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #3b82f6;">
    <h3 style="color: #ffffff; margin-top: 0;">📋 Quick Info</h3>
    <table style="width: 100%; color: #e5e7eb;">
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Engine:</strong></td><td>Unreal Engine 2.5 (Modified)</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Developer:</strong></td><td>Tripwire Interactive</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">App ID:</strong></td><td>215350</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Game Port:</strong></td><td>7707 UDP (default)</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Query Port:</strong></td><td>7708 UDP (default)</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Web Admin:</strong></td><td>8075 TCP (default)</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Max Players:</strong></td><td>6 (standard), up to 32 with mods</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Game Modes:</strong></td><td>Story, Objective</td></tr>
        <tr><td style="padding: 8px 0;"><strong style="color: #a5b4fc;">Perks:</strong></td><td>7 classes (Berserker, Commando, Support, Sharpshooter, etc.)</td></tr>
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
    <li><a href="#mods">Popular Mods & Mutators</a></li>
    <li><a href="#startup">Startup Commands</a></li>
    <li><a href="#troubleshooting">🔧 Troubleshooting</a></li>
    <li><a href="#resources">Resources</a></li>
</ul>

<h2 id="overview">Overview</h2>
<p><strong>Killing Floor</strong> is the original co-op horror FPS that started the franchise. Based on the Unreal Tournament 2004 mod, up to 6 players fight waves of zombie-like "specimens" in various UK locations. The game combines strategic shopping between waves with intense combat and horrific enemy designs.</p>

<p>Originally a mod released in 2005, the standalone game launched in 2009 and built a massive following. Players select from 7 Perk classes, each with unique weapons and passive abilities that level up through gameplay. The final wave always features the terrifying Patriarch boss.</p>

<h3>Key Features</h3>
<ul>
    <li><strong>7 Perk Classes:</strong> Berserker, Commando, Support Specialist, Sharpshooter, Medic, Demolitionist, Firebug</li>
    <li><strong>Wave-Based Survival:</strong> 4, 7, or 10 waves with boss finale</li>
    <li><strong>The Patriarch:</strong> Iconic final boss with multiple attack phases</li>
    <li><strong>Trader System:</strong> Buy weapons and armor between waves</li>
    <li><strong>Persistent Leveling:</strong> Perks level from 0-6 through gameplay achievements</li>
    <li><strong>Difficulty Levels:</strong> Beginner, Normal, Hard, Suicidal, Hell on Earth</li>
    <li><strong>Mod Support:</strong> Extensive mutator and custom map community</li>
    <li><strong>British Horror Theme:</strong> Dark humor and UK locations</li>
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
            <td style="padding: 12px; font-family: monospace; color: #a5b4fc;">7707</td>
            <td style="padding: 12px;">
                <span style="background: #22c55e; color: #ffffff; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold;">UDP</span>
            </td>
            <td style="padding: 12px;">Game server (main connection)</td>
            <td style="padding: 12px; color: #22c55e; font-weight: bold;">✓ Yes</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; font-family: monospace; color: #a5b4fc;">7708</td>
            <td style="padding: 12px;">
                <span style="background: #22c55e; color: #ffffff; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold;">UDP</span>
            </td>
            <td style="padding: 12px;">Query port (server browser)</td>
            <td style="padding: 12px; color: #22c55e; font-weight: bold;">✓ Yes</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; font-family: monospace; color: #a5b4fc;">8075</td>
            <td style="padding: 12px;">
                <span style="background: #3b82f6; color: #ffffff; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold;">TCP</span>
            </td>
            <td style="padding: 12px;">Web Admin interface</td>
            <td style="padding: 12px; color: #94a3b8;">Optional (Recommended)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; font-family: monospace; color: #a5b4fc;">28852</td>
            <td style="padding: 12px;">
                <span style="background: #22c55e; color: #ffffff; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold;">UDP</span>
            </td>
            <td style="padding: 12px;">Steam master server port</td>
            <td style="padding: 12px; color: #94a3b8;">Optional</td>
        </tr>
    </tbody>
</table>

<div style="background: #1e3a5f; padding: 15px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <p style="color: #e5e7eb; margin: 0;"><strong>💡 Note:</strong> Port numbers are configurable. Defaults shown above. Multiple servers require unique port combinations (e.g., Game: 7707, 7717; Query: 7708, 7718).</p>
</div>

<h3>Firewall Configuration Examples</h3>

<h4>Ubuntu/Debian (UFW)</h4>
<pre><code># Allow KF game port
sudo ufw allow 7707/udp

# Allow query port (server browser)
sudo ufw allow 7708/udp

# Allow Web Admin (if using)
sudo ufw allow 8075/tcp

# Steam master server port (optional)
sudo ufw allow 28852/udp

# Enable firewall
sudo ufw enable
sudo ufw status
</code></pre>

<h4>CentOS/RHEL (FirewallD)</h4>
<pre><code># Add KF ports
sudo firewall-cmd --permanent --add-port=7707/udp
sudo firewall-cmd --permanent --add-port=7708/udp
sudo firewall-cmd --permanent --add-port=8075/tcp
sudo firewall-cmd --permanent --add-port=28852/udp
sudo firewall-cmd --reload
sudo firewall-cmd --list-ports
</code></pre>

<h4>Windows Firewall (PowerShell)</h4>
<pre><code># Game server
New-NetFirewallRule -DisplayName "KF Game Port" -Direction Inbound -LocalPort 7707 -Protocol UDP -Action Allow

# Query port
New-NetFirewallRule -DisplayName "KF Query Port" -Direction Inbound -LocalPort 7708 -Protocol UDP -Action Allow

# Web Admin
New-NetFirewallRule -DisplayName "KF Web Admin" -Direction Inbound -LocalPort 8075 -Protocol TCP -Action Allow
</code></pre>

<h4>iptables (Advanced)</h4>
<pre><code># Allow KF ports
iptables -A INPUT -p udp --dport 7707 -j ACCEPT
iptables -A INPUT -p udp --dport 7708 -j ACCEPT
iptables -A INPUT -p tcp --dport 8075 -j ACCEPT
iptables -A INPUT -p udp --dport 28852 -j ACCEPT

# Save rules (Ubuntu/Debian)
netfilter-persistent save

# Save rules (CentOS/RHEL)
service iptables save
</code></pre>

<h2 id="installation">Installation</h2>

<h3>Prerequisites</h3>
<ul>
    <li><strong>SteamCMD</strong> installed (<a href="https://developer.valvesoftware.com/wiki/SteamCMD" target="_blank">Installation Guide</a>)</li>
    <li>Disk space: ~5 GB for full installation</li>
    <li>RAM: 2GB minimum, 4GB recommended for 6 players</li>
    <li>Open firewall ports (7707, 7708 UDP, 8075 TCP)</li>
    <li>Linux: 32-bit libraries required (older Unreal Engine 2 game)</li>
</ul>

<h3>Linux Installation</h3>
<pre><code># Create server directory
mkdir -p ~/kf_server
cd ~/kf_server

# Install required 32-bit libraries (Ubuntu/Debian)
sudo dpkg --add-architecture i386
sudo apt-get update
sudo apt-get install lib32gcc1 libstdc++6:i386

# Download server files with SteamCMD
steamcmd +force_install_dir ~/kf_server +login anonymous +app_update 215350 validate +quit

# Note: App ID 215350 is for Killing Floor Dedicated Server
# Download size is approximately 4-5 GB
</code></pre>

<h3>Windows Installation</h3>
<pre><code># Download SteamCMD for Windows
# Extract to C:\steamcmd\

# Create server directory
mkdir C:\kf_server

# Run SteamCMD
C:\steamcmd\steamcmd.exe +force_install_dir C:\kf_server +login anonymous +app_update 215350 validate +quit
</code></pre>

<h2 id="configuration">⚙️ Configuration</h2>

<h3>Main Configuration Files</h3>
<p>KF server configuration is in <code>System/</code> directory:</p>

<ul>
    <li><strong>KillingFloor.ini</strong> - Main server settings (game mode, difficulty, maps)</li>
    <li><strong>Default.ini</strong> - Engine and network settings</li>
    <li><strong>User.ini</strong> - Client-side settings (ignore for dedicated server)</li>
</ul>

<h3>KillingFloor.ini (Essential Settings)</h3>
<p>Edit <code>System/KillingFloor.ini</code>:</p>
<pre><code>[Engine.GameReplicationInfo]
ServerName=My Killing Floor Server
ShortName=KF Server
Region=0  ; 0=US East, 1=US West, 2=Europe, etc.
MessageOfTheDay=Welcome to our Killing Floor server!

[IpDrv.TcpNetDriver]
AllowDownloads=True
ConnectionTimeout=60.0
InitialConnectTimeout=120.0
AckTimeout=1.0
KeepAliveTime=0.2
MaxClientRate=20000
MaxInternetClientRate=10000
NetServerMaxTickRate=30

[Engine.GameEngine]
EnableDevTools=False
bStartWithMatineeCapture=False

[KFMod.KFGameType]
bChangeLevels=False
bUsePreStartTimer=True
PreStartTime=30
bKickVoteEnabled=True
KickVotePercentage=0.66
bDisableTeamCollision=True
StartingCash=250
MinRespawnCash=250

; Game Length (number of waves)
KFGameLength=2  ; 0=Short (4 waves), 1=Medium (7 waves), 2=Long (10 waves)

; Difficulty
GameDifficulty=2.0  ; 1.0=Beginner, 2.0=Normal, 4.0=Hard, 5.0=Suicidal, 7.0=Hell on Earth

; Enable/disable monsters
bEnableStaticSpecimens=False  ; Static (non-moving) specimens

[Engine.AccessControl]
AdminPassword=YourAdminPassword
GamePassword=  ; Leave empty for public server

[IpDrv.MasterServerUplink]
DoUplink=True
UplinkToGamespy=True
ServerBehindNAT=False  ; Set to True if server is behind NAT/router

[WebAdmin.WebAdmin]
bEnabled=True
ListenPort=8075

</code></pre>

<h3>Difficulty Levels</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Difficulty</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Value</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Specimen Health</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Description</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Beginner</strong></td>
            <td style="padding: 12px;"><code>1.0</code></td>
            <td style="padding: 12px;">50%</td>
            <td style="padding: 12px;">New players learning the game</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Normal</strong></td>
            <td style="padding: 12px;"><code>2.0</code></td>
            <td style="padding: 12px;">100%</td>
            <td style="padding: 12px;">Standard difficulty</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Hard</strong></td>
            <td style="padding: 12px;"><code>4.0</code></td>
            <td style="padding: 12px;">150%</td>
            <td style="padding: 12px;">Challenging gameplay</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Suicidal</strong></td>
            <td style="padding: 12px;"><code>5.0</code></td>
            <td style="padding: 12px;">200%</td>
            <td style="padding: 12px;">Very difficult, experienced teams</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Hell on Earth</strong></td>
            <td style="padding: 12px;"><code>7.0</code></td>
            <td style="padding: 12px;">250%</td>
            <td style="padding: 12px;">Extreme challenge, elite players</td>
        </tr>
    </tbody>
</table>

<h2 id="webadmin">Web Admin Interface</h2>

<h3>Enabling Web Admin</h3>
<p>Edit <code>System/KillingFloor.ini</code>:</p>
<pre><code>[WebAdmin.WebAdmin]
bEnabled=True
ListenPort=8075
</code></pre>

<h3>Accessing Web Admin</h3>
<p>Open browser to: <code>http://your-server-ip:8075</code></p>

<p>Default login: <strong>Admin</strong> with the password set in <code>AdminPassword</code> in KillingFloor.ini</p>

<h4>Web Admin Features</h4>
<ul>
    <li><strong>Current Game:</strong> View players, kick/ban, change map</li>
    <li><strong>Settings:</strong> Change difficulty, game length, server name</li>
    <li><strong>Chat Log:</strong> Monitor in-game chat</li>
    <li><strong>Access Policy:</strong> Manage bans and IP restrictions</li>
    <li><strong>Console:</strong> Execute server commands</li>
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
            <td style="padding: 12px;"><strong>Story Mode</strong></td>
            <td style="padding: 12px;">Standard wave-based survival with Patriarch finale</td>
            <td style="padding: 12px;">4, 7, or 10 + Boss</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Objective Mode</strong></td>
            <td style="padding: 12px;">Complete map-specific objectives while surviving waves</td>
            <td style="padding: 12px;">Varies by map</td>
        </tr>
    </tbody>
</table>

<h3>Official Maps (Selection)</h3>
<ul>
    <li><strong>KF-BioticsLab</strong> - Horzine research facility (original)</li>
    <li><strong>KF-Farm</strong> - Creepy countryside farm</li>
    <li><strong>KF-Manor</strong> - Gothic mansion</li>
    <li><strong>KF-WestLondon</strong> - London streets and Underground</li>
    <li><strong>KF-Offices</strong> - Office building</li>
    <li><strong>KF-Hospital</strong> - Abandoned hospital</li>
    <li><strong>KF-Wyre</strong> - Coastal town</li>
    <li><strong>KF-Bedlam</strong> - Asylum (part of Twisted Christmas)</li>
</ul>

<h3>Map Rotation Configuration</h3>
<p>Edit <code>System/KillingFloor.ini</code>:</p>
<pre><code>[KFMod.KFGameType]
Maps=KF-BioticsLab
Maps=KF-Farm
Maps=KF-Manor
Maps=KF-WestLondon
Maps=KF-Offices

; Enable map voting
bUseMapVote=True
MapVotePercentage=0.50
</code></pre>

<h2 id="perks">Perks & Classes</h2>

<h3>The 7 Perks</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Perk</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Role</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Primary Weapons</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Level Cap</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Berserker</strong></td>
            <td style="padding: 12px;">Tank/Melee DPS</td>
            <td style="padding: 12px;">Katana, Claymore, Fire Axe</td>
            <td style="padding: 12px;">6</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Commando</strong></td>
            <td style="padding: 12px;">Trash Cleaner</td>
            <td style="padding: 12px;">SCAR, Bullpup, AK-47</td>
            <td style="padding: 12px;">6</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Support Specialist</strong></td>
            <td style="padding: 12px;">Shotgun Expert</td>
            <td style="padding: 12px;">AA12, Hunting Shotgun</td>
            <td style="padding: 12px;">6</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Sharpshooter</strong></td>
            <td style="padding: 12px;">Precision Sniper</td>
            <td style="padding: 12px;">Crossbow, M14 EBR, Lever Action</td>
            <td style="padding: 12px;">6</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Medic</strong></td>
            <td style="padding: 12px;">Healer/Support</td>
            <td style="padding: 12px;">MP7M, MP5M</td>
            <td style="padding: 12px;">6</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Demolitionist</strong></td>
            <td style="padding: 12px;">Explosive Specialist</td>
            <td style="padding: 12px;">M79, M32, LAW, Pipe Bombs</td>
            <td style="padding: 12px;">6</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Firebug</strong></td>
            <td style="padding: 12px;">Area Denial/DoT</td>
            <td style="padding: 12px;">Flamethrower, MAC-10</td>
            <td style="padding: 12px;">6</td>
        </tr>
    </tbody>
</table>

<h3>Perk Leveling</h3>
<p>Perks level from 0 to 6 based on completing requirements:</p>
<ul>
    <li><strong>Level 0:</strong> Starting bonuses (small damage/discount)</li>
    <li><strong>Level 6:</strong> Maximum bonuses (significant damage, discount, special abilities)</li>
    <li>Leveling requires kills with perk weapons, healing (Medic), welding (Support), etc.</li>
    <li>Progress persists across servers using Steam ID</li>
</ul>

<h2 id="mods">Popular Mods & Mutators</h2>

<h3>Installing Mutators</h3>
<p>Place mutator files in <code>System/</code> directory, then add to <code>KillingFloor.ini</code>:</p>
<pre><code>[Engine.GameEngine]
ServerActors=MyMutator.MyMutatorClass
ServerPackages=MyMutator
</code></pre>

<h3>Popular Mutators</h3>
<ul>
    <li><strong>Zed Time Extension:</strong> Extends slow-motion "Zed Time" duration</li>
    <li><strong>Golden Weapons:</strong> Adds powerful golden variants of standard weapons</li>
    <li><strong>KF Classic Characters:</strong> Original character models from the mod</li>
    <li><strong>Siren Scream Multiplier:</strong> Modifies Siren difficulty</li>
    <li><strong>Server Perks:</strong> Custom perk systems with additional bonuses</li>
    <li><strong>Custom Trader Inventory:</strong> Modified weapon shops</li>
    <li><strong>Faked Players:</strong> Adjusts difficulty scaling for smaller teams</li>
</ul>

<h3>WhiteList Mode (Custom Content)</h3>
<p>Allow custom maps and mutators:</p>
<pre><code>[IpDrv.TcpNetDriver]
AllowDownloads=True

[Engine.GameEngine]
CacheSizeMegs=32
</code></pre>

<h2 id="startup">Startup Commands</h2>

<h3>Linux</h3>
<pre><code>#!/bin/bash
# start_kf.sh

cd ~/kf_server/System

./ucc-bin server KF-BioticsLab.rom?game=KFMod.KFGameType?VACSecured=true \
  -nohomedir \
  -ini=KillingFloor.ini \
  -port=7707 \
  -queryport=7708
</code></pre>

<h3>Windows</h3>
<pre><code>@echo off
REM start_kf.bat

cd C:\kf_server\System

KillingFloor.exe server KF-BioticsLab.rom?game=KFMod.KFGameType?VACSecured=true ^
  -ini=KillingFloor.ini ^
  -port=7707 ^
  -queryport=7708
</code></pre>

<h3>Startup Parameters</h3>
<ul>
    <li><code>server MapName.rom</code> - Start with specific map</li>
    <li><code>?game=KFMod.KFGameType</code> - Specify game mode</li>
    <li><code>?VACSecured=true</code> - Enable VAC anti-cheat</li>
    <li><code>-port=7707</code> - Game port</li>
    <li><code>-queryport=7708</code> - Server browser port</li>
    <li><code>-nohomedir</code> - (Linux) Don't use home directory for configs</li>
    <li><code>-ini=KillingFloor.ini</code> - Specify config file</li>
</ul>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not in Browser</h3>
<pre><code># Check ports are open
netstat -an | grep 7707
netstat -an | grep 7708

# Verify UplinkToGamespy in KillingFloor.ini
[IpDrv.MasterServerUplink]
DoUplink=True
UplinkToGamespy=True

# Wait 5-10 minutes for Steam master server registration
</code></pre>

<h3>Web Admin Won't Load</h3>
<pre><code># Verify bEnabled in KillingFloor.ini
[WebAdmin.WebAdmin]
bEnabled=True
ListenPort=8075

# Check firewall allows TCP 8075
# Try: http://127.0.0.1:8075 locally first
</code></pre>

<h3>Performance Issues</h3>
<ul>
    <li>Reduce <code>MaxPlayers</code> in KillingFloor.ini</li>
    <li>Lower <code>NetServerMaxTickRate=20</code></li>
    <li>Disable complex mutators</li>
    <li>Use <code>-benchmark</code> flag to test server performance</li>
</ul>

<h3>Custom Maps Not Downloading</h3>
<pre><code># Enable downloads in KillingFloor.ini
[IpDrv.TcpNetDriver]
AllowDownloads=True
ConnectionTimeout=60.0

# Set up redirect (FastDL)
DownloadManagers=IpDrv.HTTPDownload

[IpDrv.HTTPDownload]
RedirectToURL=http://yourdomain.com/kf/
</code></pre>

<h3>Perk Progress Not Saving</h3>
<ul>
    <li>Ensure server connects to Steam (<code>VACSecured=true</code>)</li>
    <li>Players must be logged into Steam</li>
    <li>Check <code>System/KFStatsAndAchievements.ini</code> for errors</li>
    <li>Perk data stored server-side via Steam ID</li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-lightbulb" style="color: #fbbf24; margin-right: 8px;"></i>Pro Tips</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Web Admin Essential:</strong> Much easier than console commands</li>
        <li><strong>Balanced Perks:</strong> Encourage teams to use diverse perk combinations</li>
        <li><strong>Medic Critical:</strong> At least one Medic greatly improves survival rates</li>
        <li><strong>Classic Maps:</strong> BioticsLab, Farm, and WestLondon are community favorites</li>
        <li><strong>Suicidal+ Only:</strong> Most active players prefer Suicidal and Hell on Earth</li>
        <li><strong>Mutators:</strong> Use sparingly - too many can cause instability</li>
        <li><strong>VAC Secured:</strong> Always enable for legitimate perk progression</li>
        <li><strong>Custom Content:</strong> Active custom map community, but test before adding to rotation</li>
    </ul>
</div>

<h2 id="resources">Resources</h2>
<ul>
    <li><a href="https://wiki.killingfloor.com/index.php?title=Main_Page" target="_blank">Killing Floor Wiki</a></li>
    <li><a href="https://forums.tripwireinteractive.com/index.php?forums/killingfloor.3/" target="_blank">Tripwire Interactive Official Forums</a></li>
    <li><a href="https://steamcommunity.com/app/1250/discussions/" target="_blank">Steam Community Discussions</a></li>
    <li><a href="https://github.com/InsultingPros/KFStatsX" target="_blank">KFStatsX - Server Statistics</a></li>
    <li><a href="http://kf-board.com/" target="_blank">KF-Board - Custom Content Repository</a></li>
</ul>