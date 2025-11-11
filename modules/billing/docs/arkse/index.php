<?php
/**
 * ARK: Survival Evolved - Comprehensive Hosting Guide
 * General game server hosting information (not platform-specific)
 */
?>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">📚 Navigation</h3>
    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
        <a href="#quick-info" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Overview</a>
        <a href="#ports" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">🔌 Ports</a>
        <a href="#installation" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Installation</a>
        <a href="#configuration" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">⚙️ Configuration</a>
        <a href="#parameters" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Parameters</a>
        <a href="#troubleshooting" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">🔧 Troubleshooting</a>
        <a href="#performance" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Performance</a>
    </div>
</div>

<h1>ARK: Survival Evolved Dedicated Server Hosting Guide</h1>

<h2>Overview</h2>
<p>ARK: Survival Evolved is a survival game where players must survive being stranded on an island filled with dinosaurs and other prehistoric animals, natural hazards, and potentially hostile human players. This comprehensive guide covers hosting an ARK: Survival Evolved dedicated server on a VPS or dedicated server.</p>

<h2 id="quick-info">Quick Info</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <ul style="color: #e5e7eb; line-height: 1.8; margin: 0;">
        <li><strong style="color: #ffffff;">Game Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">7777</code> (UDP)</li>
        <li><strong style="color: #ffffff;">Raw UDP Socket:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">7778</code> (UDP)</li>
        <li><strong style="color: #ffffff;">Query Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27015</code> (UDP)</li>
        <li><strong style="color: #ffffff;">RCON Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27020</code> (TCP, optional)</li>
        <li><strong style="color: #ffffff;">Minimum RAM:</strong> 8-12GB (more for mods/high player count)</li>
        <li><strong style="color: #ffffff;">Recommended CPU:</strong> Dual-core minimum, quad-core preferred</li>
        <li><strong style="color: #ffffff;">Storage:</strong> 20-25GB minimum free space</li>
        <li><strong style="color: #ffffff;">SteamCMD App ID:</strong> 346110</li>
        <li><strong style="color: #ffffff;">Config Files:</strong> GameUserSettings.ini, Game.ini</li>
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
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Game server port (client connections)</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #7c2d12; padding: 4px 8px; border-radius: 3px; color: #fed7aa;">✓ Yes</span></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">7778</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Raw UDP socket port (automatic +1)</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #7c2d12; padding: 4px 8px; border-radius: 3px; color: #fed7aa;">✓ Yes</span></td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">27015</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Query port (server browser, Steam)</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #7c2d12; padding: 4px 8px; border-radius: 3px; color: #fed7aa;">✓ Yes</span></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">27020</code></td>
            <td style="padding: 12px;"><span style="background: #1e40af; padding: 4px 8px; border-radius: 3px; color: #dbeafe;">TCP</span></td>
            <td style="padding: 12px;">RCON port (remote console)</td>
            <td style="padding: 12px;"><span style="background: #713f12; padding: 4px 8px; border-radius: 3px; color: #fef3c7;">Optional</span></td>
        </tr>
    </tbody>
</table>

<h3>Firewall Configuration Examples</h3>

<h4>UFW (Ubuntu/Debian)</h4>
<pre><code>sudo ufw allow 7777/udp comment 'ARK game port'
sudo ufw allow 7778/udp comment 'ARK raw UDP'
sudo ufw allow 27015/udp comment 'ARK query port'
sudo ufw allow 27020/tcp comment 'ARK RCON'
sudo ufw reload
</code></pre>

<h4>FirewallD (CentOS/RHEL/Fedora)</h4>
<pre><code>sudo firewall-cmd --permanent --add-port=7777/udp
sudo firewall-cmd --permanent --add-port=7778/udp
sudo firewall-cmd --permanent --add-port=27015/udp
sudo firewall-cmd --permanent --add-port=27020/tcp
sudo firewall-cmd --reload
</code></pre>

<h4>Windows Firewall</h4>
<pre><code># Run in PowerShell as Administrator
New-NetFirewallRule -DisplayName "ARK Game Ports" -Direction Inbound -Protocol UDP -LocalPort 7777,7778,27015 -Action Allow
New-NetFirewallRule -DisplayName "ARK RCON" -Direction Inbound -Protocol TCP -LocalPort 27020 -Action Allow
</code></pre>

<h4>iptables (Legacy Linux)</h4>
<pre><code>sudo iptables -A INPUT -p udp --dport 7777 -j ACCEPT
sudo iptables -A INPUT -p udp --dport 7778 -j ACCEPT
sudo iptables -A INPUT -p udp --dport 27015 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 27020 -j ACCEPT
sudo service iptables save
</code></pre>

<h2 id="installation">Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Windows Server 2016+ (64-bit) or Linux 64-bit (Ubuntu/Debian)</li>
    <li><strong>CPU:</strong> Minimum dual-core; Recommended quad-core @ 3.0GHz+</li>
    <li><strong>RAM:</strong> 8-12GB minimum; 16GB+ for modded/high-pop servers</li>
    <li><strong>Storage:</strong> 20-25GB for base game; additional for mods and saves</li>
    <li><strong>Network:</strong> 10Mbps+ upload recommended</li>
</ul>

<h3>Installing via SteamCMD</h3>
<pre><code># Install SteamCMD first
# Windows: Download from https://steamcdn-a.akamaihd.net/client/installer/steamcmd.zip
# Linux:
sudo apt update
sudo apt install lib32gcc1 steamcmd

# Create server directory
mkdir -p ~/arkserver
cd ~/arkserver

# Download server files
steamcmd +login anonymous +force_install_dir ~/arkserver +app_update 346110 validate +exit

# This will download approximately 20GB of files
</code></pre>

<h3>Windows Startup Script</h3>
<p>Create <code>ServerStart.bat</code> in <code>ShooterGame\Binaries\Win64\</code>:</p>
<pre><code>@echo off
start ShooterGameServer.exe "TheIsland?SessionName=MyARKServer?QueryPort=27015?ServerPassword=YOURPASSWORD?ServerAdminPassword=ADMINPASS?listen?Port=7777?MaxPlayers=20"
exit
</code></pre>

<h3>Linux Startup Script</h3>
<pre><code>#!/bin/bash
cd ~/arkserver/ShooterGame/Binaries/Linux
./ShooterGameServer TheIsland?listen?SessionName=MyARKServer?ServerPassword=YOURPASSWORD?ServerAdminPassword=ADMINPASS?QueryPort=27015?Port=7777?MaxPlayers=20 &gt; ~/arkserver.log 2&gt;&amp;1 &amp;
</code></pre>

<h2 id="configuration">Server Configuration</h2>

<h3>Configuration File Locations</h3>
<p><strong>Windows:</strong></p>
<pre><code>ShooterGame\Saved\Config\WindowsServer\GameUserSettings.ini
ShooterGame\Saved\Config\WindowsServer\Game.ini
</code></pre>

<p><strong>Linux:</strong></p>
<pre><code>ShooterGame/Saved/Config/LinuxServer/GameUserSettings.ini
ShooterGame/Saved/Config/LinuxServer/Game.ini
</code></pre>

<h3>GameUserSettings.ini - Key Settings</h3>
<pre><code>[ServerSettings]
ServerPassword=YourServerPassword
ServerAdminPassword=YourAdminPassword
ServerName=My ARK Server
MaxPlayers=20
DifficultyOffset=1.0
ServerPVE=False
AllowThirdPersonPlayer=True
ShowMapPlayerLocation=True
EnablePVPGamma=True
ServerCrosshair=True
RCONEnabled=True
RCONPort=27020
TheMaxStructuresInRange=10500

# XP and Progression
XPMultiplier=1.5
TamingSpeedMultiplier=3.0
HarvestAmountMultiplier=2.0
HarvestHealthMultiplier=1.5
ResourcesRespawnPeriodMultiplier=0.5

# Player Stats
PlayerCharacterWaterDrainMultiplier=1.0
PlayerCharacterFoodDrainMultiplier=1.0
PlayerCharacterStaminaDrainMultiplier=1.0
PlayerCharacterHealthRecoveryMultiplier=1.0
PlayerDamageMultiplier=1.0
PlayerResistanceMultiplier=1.0

# Dino Settings
DinoCharacterFoodDrainMultiplier=1.0
DinoCharacterStaminaDrainMultiplier=1.0
DinoCharacterHealthRecoveryMultiplier=1.0
DinoCountMultiplier=1.0
DinoResistanceMultiplier=1.0
DinoDamageMultiplier=1.0
</code></pre>

<h3>Available Maps</h3>
<p>Replace the map name in your startup command:</p>
<ul>
    <li><code>TheIsland</code> - Original ARK map</li>
    <li><code>TheCenter</code> - Free expansion map</li>
    <li><code>Ragnarok</code> - Free expansion map</li>
    <li><code>Valguero</code> - Free expansion map</li>
    <li><code>CrystalIsles</code> - Free expansion map</li>
    <li><code>ScorchedEarth_P</code> - Paid DLC</li>
    <li><code>Aberration_P</code> - Paid DLC</li>
    <li><code>Extinction</code> - Paid DLC</li>
    <li><code>Genesis</code> - Paid DLC</li>
</ul>

<h2 id="parameters">Startup Parameters</h2>

<h3>Command Line Options</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <tr style="background: #f8f9fa;">
        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Parameter</th>
        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Description</th>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>?SessionName=NAME</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Server name displayed in browser</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>?Port=7777</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Game port (default 7777)</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>?QueryPort=27015</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Steam query port</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>?ServerPassword=PASS</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Password to join server</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>?ServerAdminPassword=PASS</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Admin password for console</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>?MaxPlayers=20</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Maximum player slots</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>?ServerPVE=true</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Enable PVE mode</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>?AllowThirdPersonPlayer=true</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Allow third-person view</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>?listen</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Required for dedicated server</td>
    </tr>
</table>

<h3>Port Forwarding Requirements</h3>
<pre><code># Forward these ports on your router/firewall:
UDP 7777 - Game Client Port
UDP 7778 - Raw UDP Socket
UDP 27015 - Steam Query Port
TCP 27020 - RCON (if enabled)

# Linux firewall (UFW):
sudo ufw allow 7777:7778/udp
sudo ufw allow 27015/udp
sudo ufw allow 27020/tcp
sudo ufw reload
</code></pre>

<h2 id="troubleshooting">Troubleshooting</h2>

<h3>Server Won't Start</h3>
<p><strong>Problem:</strong> Server fails to start or crashes immediately.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Check log files in <code>ShooterGame/Saved/Logs/</code></li>
    <li>Verify Visual C++ 2013 Redistributable is installed (Windows)</li>
    <li>Ensure sufficient RAM and disk space</li>
    <li>Validate server files: <code>steamcmd +login anonymous +app_update 346110 validate +exit</code></li>
    <li>Check file permissions on Linux (<code>chmod +x</code> on server executable)</li>
</ul>

<h3>Cannot Connect to Server</h3>
<p><strong>Problem:</strong> Players cannot connect or server not visible in browser.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify all ports are forwarded correctly (7777, 7778, 27015)</li>
    <li>Check firewall rules allow traffic</li>
    <li>Confirm QueryPort is set correctly and not in reserved range (27020-27050)</li>
    <li>Try direct connect using IP:Port in Steam</li>
    <li>Ensure server password is communicated correctly</li>
</ul>

<h3>High Resource Usage / Lag</h3>
<p><strong>Problem:</strong> Server uses excessive resources or experiences lag.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Reduce max player count</li>
    <li>Disable or reduce mods</li>
    <li>Lower difficulty and resource multipliers</li>
    <li>Upgrade server hardware (especially RAM)</li>
    <li>Regular server restarts to clear memory</li>
    <li>Clean up abandoned structures with admin commands</li>
</ul>

<h3>Mod Issues</h3>
<p><strong>Problem:</strong> Mods not loading or causing crashes.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Update mods via Steam Workshop</li>
    <li>Add mod IDs to GameUserSettings.ini: <code>ActiveMods=modid1,modid2</code></li>
    <li>Ensure mod compatibility with current game version</li>
    <li>Remove conflicting mods one at a time to identify culprit</li>
</ul>

<h2 id="performance">Performance Optimization</h2>

<h3>Recommended Server Settings</h3>
<ul>
    <li><strong>Small Server (5-10 players):</strong> 8GB RAM, dual-core CPU</li>
    <li><strong>Medium Server (10-20 players):</strong> 12-16GB RAM, quad-core CPU</li>
    <li><strong>Large Server (20+ players):</strong> 16-32GB RAM, high-performance CPU</li>
</ul>

<h3>Admin Console Commands</h3>
<p>Enable admin: Press TAB, type <code>enablecheats ADMINPASSWORD</code></p>
<ul>
    <li><code>SaveWorld</code> - Force save the game</li>
    <li><code>DestroyWildDinos</code> - Respawn all wild dinosaurs</li>
    <li><code>SetTimeOfDay HH:MM:SS</code> - Set time of day</li>
    <li><code>admincheat KillPlayer PLAYERNAME</code> - Kill a player</li>
    <li><code>admincheat BanPlayer PLAYERNAME</code> - Ban a player</li>
    <li><code>admincheat Broadcast MESSAGE</code> - Server-wide message</li>
</ul>

<h3>Backup Strategy</h3>
<pre><code># Backup save files regularly
# Location: ShooterGame/Saved/SavedArks/

# Linux backup script:
#!/bin/bash
BACKUP_DIR="/path/to/backups"
DATE=$(date +%Y%m%d_%H%M%S)
cd ~/arkserver/ShooterGame/Saved
tar -czf $BACKUP_DIR/ark_backup_$DATE.tar.gz SavedArks/
# Keep only last 7 days
find $BACKUP_DIR -name "ark_backup_*.tar.gz" -mtime +7 -delete
</code></pre>

<h2>Additional Resources</h2>
<ul>
    <li><a href="https://ark.fandom.com/wiki/Dedicated_server_setup" target="_blank">ARK Wiki - Dedicated Server Setup</a></li>
    <li><a href="https://ark.wiki.gg/wiki/Server_configuration" target="_blank">ARK Official Wiki - Server Configuration</a></li>
    <li><a href="https://steamcommunity.com/app/346110/discussions/" target="_blank">Steam Community Discussions</a></li>
    <li><a href="https://www.reddit.com/r/playark/" target="_blank">r/playark - Community Support</a></li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Always keep your server updated via SteamCMD to match client versions</li>
        <li>Make regular automated backups of save files</li>
        <li>Monitor resource usage and adjust player limits accordingly</li>
        <li>Use strong admin passwords and protect RCON access</li>
        <li>Test mods thoroughly before deploying to live server</li>
    </ul>
</div>