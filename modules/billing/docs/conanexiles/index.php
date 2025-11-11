<?php
/**
 * Conan Exiles Dedicated Server - Comprehensive Hosting Guide
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
        <a href="#mods" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Mods & Admin</a>
        <a href="#troubleshooting" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">🔧 Troubleshooting</a>
        <a href="#performance" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Performance</a>
    </div>
</div>

<h1>Conan Exiles Dedicated Server Hosting Guide</h1>

<h2 id="overview">Overview</h2>
<p><strong>Conan Exiles</strong> is an open-world survival game set in the brutal lands of Conan the Barbarian. Players must survive in a vast sandbox world, build massive structures, tame thralls, and battle gods and other players in this savage civilization-building game.</p>

<h2 id="quick-info">Quick Info</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <ul style="color: #e5e7eb; line-height: 1.8; margin: 0;">
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">7777</code> (UDP)</li>
        <li><strong style="color: #ffffff;">Query Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27015</code> (UDP)</li>
        <li><strong style="color: #ffffff;">RCON Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">25575</code> (TCP, optional)</li>
        <li><strong style="color: #ffffff;">Minimum RAM:</strong> 6GB (Recommended: 16-32GB)</li>
        <li><strong style="color: #ffffff;">Recommended CPU:</strong> 4+ cores @ 3.5GHz+</li>
        <li><strong style="color: #ffffff;">Storage:</strong> 30GB+ (SSD strongly recommended)</li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 40-70 typical (configurable)</li>
        <li><strong style="color: #ffffff;">SteamCMD App ID:</strong> 443030</li>
        <li><strong style="color: #ffffff;">Config Files:</strong> ServerSettings.ini, Engine.ini, Game.ini</li>
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
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">7777</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Primary game port (player connections)</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #7c2d12; padding: 4px 8px; border-radius: 3px; color: #fed7aa;">✓ Yes</span></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">7778</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Raw UDP socket (automatic +1)</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #7c2d12; padding: 4px 8px; border-radius: 3px; color: #fed7aa;">✓ Yes</span></td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">27015</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Query port (server browser, Steam)</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #7c2d12; padding: 4px 8px; border-radius: 3px; color: #fed7aa;">✓ Yes</span></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">25575</code></td>
            <td style="padding: 12px;"><span style="background: #1e40af; padding: 4px 8px; border-radius: 3px; color: #dbeafe;">TCP</span></td>
            <td style="padding: 12px;">RCON port (remote admin console)</td>
            <td style="padding: 12px;"><span style="background: #713f12; padding: 4px 8px; border-radius: 3px; color: #fef3c7;">Optional</span></td>
        </tr>
    </tbody>
</table>

<div style="background: #1e3a5f; padding: 15px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <p style="color: #e5e7eb; margin: 0;"><strong>Note:</strong> Like ARK: Survival Evolved, Conan Exiles automatically uses port+1 for raw UDP socket connections. Always open both the main port and the port immediately after it (e.g., 7777 and 7778).</p>
</div>

<h3>Firewall Configuration Examples</h3>

<h4>UFW (Ubuntu/Debian)</h4>
<pre><code>sudo ufw allow 7777:7778/udp comment 'Conan Exiles game ports'
sudo ufw allow 27015/udp comment 'Conan Exiles query'
sudo ufw allow 25575/tcp comment 'Conan Exiles RCON'
sudo ufw reload
</code></pre>

<h4>FirewallD (CentOS/RHEL/Fedora)</h4>
<pre><code>sudo firewall-cmd --permanent --add-port=7777-7778/udp
sudo firewall-cmd --permanent --add-port=27015/udp
sudo firewall-cmd --permanent --add-port=25575/tcp
sudo firewall-cmd --reload
</code></pre>

<h4>Windows Firewall</h4>
<pre><code># Run in PowerShell as Administrator
New-NetFirewallRule -DisplayName "Conan Exiles Game" -Direction Inbound -Protocol UDP -LocalPort 7777,7778,27015 -Action Allow
New-NetFirewallRule -DisplayName "Conan Exiles RCON" -Direction Inbound -Protocol TCP -LocalPort 25575 -Action Allow
</code></pre>

<h4>iptables (Legacy Linux)</h4>
<pre><code>sudo iptables -A INPUT -p udp --dport 7777:7778 -j ACCEPT
sudo iptables -A INPUT -p udp --dport 27015 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 25575 -j ACCEPT
sudo service iptables save
</code></pre>

<h2 id="installation">Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Windows Server 2016+ or Linux 64-bit (Ubuntu/Debian recommended)</li>
    <li><strong>CPU:</strong> Minimum 4 cores @ 3.0GHz; Recommended 6-8 cores @ 3.5GHz+</li>
    <li><strong>RAM:</strong> 6GB minimum, 16-32GB recommended for 40+ players</li>
    <li><strong>Storage:</strong> 30GB+ for game files; SSD strongly recommended (world size grows)</li>
    <li><strong>Network:</strong> 5Mbps minimum upload; 20-50Mbps for larger servers</li>
</ul>

<h3>Installing via SteamCMD (Linux)</h3>
<pre><code># Install SteamCMD
sudo add-apt-repository multiverse
sudo dpkg --add-architecture i386
sudo apt update
sudo apt install steamcmd

# Create server directory
mkdir -p ~/conan-server
cd ~/conan-server

# Download server files
steamcmd +login anonymous +force_install_dir ~/conan-server +app_update 443030 validate +exit
</code></pre>

<h3>Installing via SteamCMD (Windows)</h3>
<pre><code># Download SteamCMD from https://steamcdn-a.akamaihd.net/client/installer/steamcmd.zip
# Extract to C:\steamcmd\

# Run CMD as Administrator
cd C:\steamcmd
steamcmd.exe +login anonymous +force_install_dir "C:\ConanServer" +app_update 443030 validate +exit
</code></pre>

<h2 id="configuration">Server Configuration</h2>

<h3>Key Configuration Files</h3>
<p>Conan Exiles uses three main INI files located in <code>ConanSandbox/Saved/Config/WindowsServer/</code> (or <code>LinuxServer/</code>):</p>

<h4>1. ServerSettings.ini</h4>
<p>Primary server configuration file for gameplay settings:</p>
<pre><code>[ServerSettings]
; Server Identity
ServerName=My Conan Server
ServerPassword=
AdminPassword=YourSecureAdminPassword
ServerRegion=0

; Network
Port=7777
QueryPort=27015
MaxPlayers=40

; PvP Settings
PVPEnabled=True
RestrictPVPBuilding=True
RestrictPVPBuildingDamageTime=True
PVPBlitzServer=False

; Server Type (affects XP/harvesting rates)
; 0=No Selection, 1=Conflict, 2=PvE-Conflict, 3=PvE, 4=PvP
ServerType=4

; Progression
PlayerXPRateMultiplier=1.0
PlayerXPKillMultiplier=1.0
PlayerXPHarvestMultiplier=1.0
PlayerXPCraftMultiplier=1.0
PlayerXPTimeMultiplier=1.0

; Harvesting
HarvestAmountMultiplier=1.0
ItemConvertionMultiplier=1.0
ResourceRespawnSpeedMultiplier=1.0

; Thralls & NPCs
ThrallConversionMultiplier=1.0
ThrallCraftingTimeMultiplier=1.0
ThrallDecayTime=604800

; Building & Decay
BuildingDecayTime=604800
BuildingDecayTimeMultiplier=1.0

; Combat
PlayerDamageMultiplier=1.0
NPCDamageMultiplier=1.0
PlayerDamageTakenMultiplier=1.0
MinionDamageMultiplier=1.0

; Containers
ContainerIgnoreOwnership=False

; Purge Settings
EnablePurge=True
PurgeLevel=5
PurgeFrequency=14400

; Stamina & Resources
PlayerStaminaCostMultiplier=1.0
PlayerActiveThirstMultiplier=1.0
PlayerActiveHungerMultiplier=1.0

; Community
ClanMaxSize=10
ServerCommunity=0
</code></pre>

<h4>2. Engine.ini</h4>
<p>Performance and network optimization:</p>
<pre><code>[OnlineSubsystemSteam]
ServerName=My Conan Server
ServerPassword=
bEnabled=true

[/Script/Engine.GameSession]
MaxPlayers=40

[Core.System]
Paths=../../../Engine/Content
Paths=%GAMEDIR%Content
Paths=../../../Engine/Plugins/Runtime/Firebase/FirebaseGoodies/Content
Paths=../../../ConanSandbox/Plugins/ControlIconsToolkit/Content
</code></pre>

<h4>3. Game.ini</h4>
<p>Advanced gameplay customization:</p>
<pre><code>[/Script/ConanSandbox.ConanGameMode]
; Tweak specific game mechanics
NPCRespawnMultiplier=1.0
</code></pre>

<h3>Starting the Server</h3>

<h4>Windows</h4>
<pre><code># Navigate to ConanSandbox\Binaries\Win64\
cd C:\ConanServer\ConanSandbox\Binaries\Win64\

# Basic startup
ConanSandboxServer.exe -log

# With custom config and multihome
ConanSandboxServer.exe -log -Port=7777 -QueryPort=27015 -MaxPlayers=40 MULTIHOME=YOUR_SERVER_IP
</code></pre>

<h4>Linux</h4>
<pre><code># Make executable
chmod +x ConanSandboxServer.sh

# Run in screen session
screen -S conan ./ConanSandboxServer.sh -log

# With custom parameters
./ConanSandboxServer.sh -log -Port=7777 -QueryPort=27015 -MaxPlayers=40

# Detach: Ctrl+A, D
# Reattach: screen -r conan
</code></pre>

<h2 id="mods">Mods & Admin Tools</h2>

<h3>Installing Mods</h3>
<ol>
    <li><strong>Subscribe to mods</strong> on Steam Workshop</li>
    <li><strong>Note the Mod IDs</strong> from Workshop URLs (e.g., 1234567890)</li>
    <li><strong>Edit modlist.txt</strong> in server root directory:</li>
</ol>

<pre><code># modlist.txt format (one mod ID per line)
*1234567890
*9876543210
*5555555555
</code></pre>

<ol start="4">
    <li><strong>Restart server</strong> - mods download automatically on startup</li>
    <li><strong>Check logs</strong> for mod loading confirmation</li>
</ol>

<h3>Popular Mods</h3>
<ul>
    <li><strong>Pippi - User & Server Management:</strong> Essential admin tools, teleportation, spawning</li>
    <li><strong>Age of Calamitous:</strong> Massive content expansion with new armor, weapons, dungeons</li>
    <li><strong>LBPR - Less Building Placement Restrictions:</strong> More flexible building</li>
    <li><strong>Emberlight:</strong> Immersive crafting and character development overhaul</li>
    <li><strong>Fashionist:</strong> Appearance customization and transmogrification</li>
</ul>

<h3>Admin Commands</h3>
<p>Press <code>Insert</code> (or configured key) to open admin panel after authenticating with admin password.</p>

<h4>Common Admin Console Commands</h4>
<pre><code># Make yourself admin (in-game)
MakeMeAdmin YourAdminPassword

# Teleport
TeleportPlayer X Y Z
TeleportToPlayer PlayerName

# Spawning
Summon ItemName
SpawnItem ItemID Quantity

# God mode
God

# Fly mode
Fly / Walk

# Invisibility
Invisibility

# Give all recipes
LearnEmote *

# Server control
SaveGame
RestartServer

# Clan management
SetClanOwner ClanID PlayerID
</code></pre>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not Appearing in Browser</h3>
<pre><code># 1. Check ports are open
netstat -an | grep 7777
netstat -an | grep 27015

# 2. Verify ServerSettings.ini
ServerRegion=0  # Should be set
MaxPlayers=40   # Must be configured

# 3. Check firewall rules
sudo ufw status verbose

# 4. Try direct connect instead of browser
# In game: Open server list → Direct Connect → IP:Port
</code></pre>

<h3>High Memory Usage / Crashes</h3>
<pre><code># Reduce max players
MaxPlayers=20  # Instead of 40+

# Increase server RAM allocation (Linux)
# Edit startup script to use ulimit
ulimit -v 33554432  # 32GB limit

# Disable building decay temporarily for testing
BuildingDecayTime=0

# Regular restarts recommended
# Schedule automatic restart every 24-48 hours
</code></pre>

<h3>Mods Not Loading</h3>
<ul>
    <li>Verify <code>modlist.txt</code> exists in server root</li>
    <li>Ensure each line starts with asterisk (*)</li>
    <li>Check mod IDs are correct (from Steam Workshop URL)</li>
    <li>Look for <code>*_ModControlPanel.txt</code> file generation</li>
    <li>Review server logs for mod loading errors</li>
    <li>Some mods require client-side installation too</li>
</ul>

<h3>Performance Issues / Lag</h3>
<pre><code># Reduce tick rate (ServerSettings.ini)
ServerTickRate=30  # Default is higher

# Lower view distance
ViewDistance=0.5

# Reduce NPC density
NPCRespawnMultiplier=0.5

# Clean up abandoned buildings
BuildingDecayTime=86400  # 1 day instead of 7
</code></pre>

<h2 id="performance">Performance Optimization</h2>

<h3>Hardware Recommendations by Player Count</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Players</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">RAM</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">CPU Cores</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Storage</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">1-10</td>
            <td style="padding: 12px;">6-8GB</td>
            <td style="padding: 12px;">4</td>
            <td style="padding: 12px;">SSD 30GB+</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">11-20</td>
            <td style="padding: 12px;">12-16GB</td>
            <td style="padding: 12px;">6</td>
            <td style="padding: 12px;">SSD 40GB+</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">21-40</td>
            <td style="padding: 12px;">16-24GB</td>
            <td style="padding: 12px;">8</td>
            <td style="padding: 12px;">SSD 50GB+</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">41-70</td>
            <td style="padding: 12px;">32GB+</td>
            <td style="padding: 12px;">12+</td>
            <td style="padding: 12px;">NVMe 60GB+</td>
        </tr>
    </tbody>
</table>

<h3>Optimization Settings</h3>
<pre><code># ServerSettings.ini performance tweaks

; Reduce resource intensity
HarvestAmountMultiplier=2.0  # Faster gathering = less time farming
ThrallConversionMultiplier=0.5  # Faster thrall conversion

; Faster decay for abandoned structures
BuildingDecayTime=259200  # 3 days instead of 7

; Purge optimization
EnablePurge=False  # Disable if causing performance issues
PurgeFrequency=21600  # Less frequent if enabled

; NPC spawn rates
NPCRespawnMultiplier=0.75  # Reduce if too many NPCs

; Stamina (reduces combat calculations)
PlayerStaminaCostMultiplier=0.75  # Less stamina drain

; Clan sizes (affects database queries)
ClanMaxSize=5  # Smaller = better performance
</code></pre>

<h3>Database Maintenance</h3>
<p>Conan Exiles uses SQLite database for world persistence. Regular maintenance improves performance:</p>

<pre><code># Backup database regularly
cp game.db game.db.backup

# The server automatically maintains the database
# But you can manually optimize offline:
sqlite3 game.db "VACUUM;"
</code></pre>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-lightbulb" style="color: #fbbf24; margin-right: 8px;"></i>Pro Tips</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>SSD is mandatory:</strong> World database writes constantly - HDD will cause severe lag</li>
        <li><strong>Restart schedule:</strong> Auto-restart every 24-48 hours prevents memory leaks</li>
        <li><strong>Purge carefully:</strong> The Purge system is resource-intensive; test on low player count first</li>
        <li><strong>Thrall limits:</strong> Too many thralls cause lag - set reasonable limits or use decay</li>
        <li><strong>Building limits:</strong> Large bases impact performance - enforce reasonable building policies</li>
        <li><strong>Backup everything:</strong> Database corruption can occur - automated hourly backups recommended</li>
        <li><strong>Admin tools:</strong> Pippi mod is essential for server management and admin tasks</li>
    </ul>
</div>

<h2>Resources</h2>
<ul>
    <li><a href="https://www.conanexiles.com/" target="_blank">Official Conan Exiles Website</a></li>
    <li><a href="https://forums.funcom.com/c/conan-exiles/pc-discussion" target="_blank">Official Forums</a></li>
    <li><a href="https://steamcommunity.com/app/440900/workshop/" target="_blank">Steam Workshop (Mods)</a></li>
    <li><a href="https://conanexiles.fandom.com/" target="_blank">Conan Exiles Wiki</a></li>
    <li><a href="https://www.conanexilesmap.com/" target="_blank">Interactive Map</a></li>
</ul>