<?php
/**
 * Insurgency: Sandstorm Server Documentation
 */
?>
<h1>📚 Insurgency: Sandstorm Server Guide</h1>
<p style="font-size: 1.1em; color: rgba(255,255,255,0.8);">Tactical team-based FPS with realistic combat mechanics</p>

<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Quick Info</h3>
    <table style="width: 100%; color: #e5e7eb;">
        <tr><td><strong style="color: #ffffff;">Engine:</strong></td><td>Unreal Engine 4</td></tr>
        <tr><td><strong style="color: #ffffff;">Developer:</strong></td><td>New World Interactive</td></tr>
        <tr><td><strong style="color: #ffffff;">App ID:</strong></td><td>581320 (Dedicated Server)</td></tr>
        <tr><td><strong style="color: #ffffff;">Default Ports:</strong></td><td>27102 (Game), 27131 (Query), 27015 (RCON)</td></tr>
        <tr><td><strong style="color: #ffffff;">Max Players:</strong></td><td>8v8 (16 players typical), up to 32 possible</td></tr>
        <tr><td><strong style="color: #ffffff;">Game Modes:</strong></td><td>Push, Firefight, Skirmish, Frontline, Checkpoint, Outpost, Survival</td></tr>
        <tr><td><strong style="color: #ffffff;">Platforms:</strong></td><td>Linux, Windows</td></tr>
    </table>
</div>

<h2>Navigation</h2>
<ul style="list-style: none; padding: 0;">
    <li>📚 <a href="#overview">Overview</a></li>
    <li>🔌 <a href="#ports">Ports & Firewall</a></li>
    <li>📥 <a href="#installation">Installation</a></li>
    <li>⚙️ <a href="#configuration">Configuration</a></li>
    <li>🎮 <a href="#gamemodes">Game Modes</a></li>
    <li>🗺️ <a href="#maps">Maps & Scenarios</a></li>
    <li>🔧 <a href="#admin">Admin Tools</a></li>
    <li>🛠️ <a href="#mods">Mods & Mutators</a></li>
    <li>🚀 <a href="#startup">Startup Commands</a></li>
    <li>🔧 <a href="#troubleshooting">Troubleshooting</a></li>
    <li>📖 <a href="#resources">Resources</a></li>
</ul>

<h2 id="overview">Overview</h2>
<p>Insurgency: Sandstorm is a hardcore tactical FPS focusing on close-quarters combat and teamwork. The game features realistic ballistics, minimal HUD, and high lethality combat where positioning and communication matter more than twitch reflexes.</p>

<h3>Key Features</h3>
<ul>
    <li><strong>Realistic Combat:</strong> Minimal HUD, no health regeneration, realistic weapon behavior</li>
    <li><strong>7 Game Modes:</strong> Competitive and cooperative gameplay options</li>
    <li><strong>Tactical Depth:</strong> Fire support, smoke grenades, objective-based gameplay</li>
    <li><strong>Customizable Loadouts:</strong> Weapon attachments and gear choices affect playstyle</li>
    <li><strong>Mod Support:</strong> Community maps, mutators, and custom scenarios</li>
    <li><strong>Voice Communication:</strong> Proximity-based voice chat adds immersion</li>
    <li><strong>Fire Support:</strong> Call in artillery, helicopter gunships, and smoke strikes</li>
</ul>

<h2 id="ports">🔌 Ports & Firewall Configuration</h2>

<h3>Required Ports</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Port</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Protocol</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Purpose</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Required?</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>27102</code></td>
            <td style="padding: 12px;"><span style="background: #10b981; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 0.85em;">UDP</span></td>
            <td style="padding: 12px;">Game Port (client connections)</td>
            <td style="padding: 12px;"><strong style="color: #10b981;">✓ Required</strong></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>27131</code></td>
            <td style="padding: 12px;"><span style="background: #10b981; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 0.85em;">UDP</span></td>
            <td style="padding: 12px;">Query Port (server browser)</td>
            <td style="padding: 12px;"><strong style="color: #10b981;">✓ Required</strong></td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>27015</code></td>
            <td style="padding: 12px;"><span style="background: #3b82f6; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 0.85em;">TCP</span></td>
            <td style="padding: 12px;">RCON Port (remote administration)</td>
            <td style="padding: 12px;"><span style="color: #fbbf24;">Optional Recommended</span></td>
        </tr>
    </tbody>
</table>

<p><strong>Note:</strong> Ports are configurable in Game.ini and Engine.ini. Default ports shown above.</p>

<h3>Firewall Configuration Examples</h3>

<h4>Ubuntu/Debian (UFW)</h4>
<pre><code># Allow Insurgency: Sandstorm ports
sudo ufw allow 27102/udp comment "Insurgency Game Port"
sudo ufw allow 27131/udp comment "Insurgency Query Port"
sudo ufw allow 27015/tcp comment "Insurgency RCON"
sudo ufw reload
</code></pre>

<h4>CentOS/RHEL (FirewallD)</h4>
<pre><code># Add Insurgency: Sandstorm ports
sudo firewall-cmd --permanent --add-port=27102/udp  # Game
sudo firewall-cmd --permanent --add-port=27131/udp  # Query
sudo firewall-cmd --permanent --add-port=27015/tcp  # RCON
sudo firewall-cmd --reload
</code></pre>

<h4>Windows (PowerShell - Run as Administrator)</h4>
<pre><code># Create firewall rules for Insurgency: Sandstorm
New-NetFirewallRule -DisplayName "Insurgency Game" -Direction Inbound -Protocol UDP -LocalPort 27102 -Action Allow
New-NetFirewallRule -DisplayName "Insurgency Query" -Direction Inbound -Protocol UDP -LocalPort 27131 -Action Allow
New-NetFirewallRule -DisplayName "Insurgency RCON" -Direction Inbound -Protocol TCP -LocalPort 27015 -Action Allow
</code></pre>

<h4>iptables (Advanced)</h4>
<pre><code># Allow Insurgency: Sandstorm ports
iptables -A INPUT -p udp --dport 27102 -j ACCEPT -m comment --comment "Insurgency Game"
iptables -A INPUT -p udp --dport 27131 -j ACCEPT -m comment --comment "Insurgency Query"
iptables -A INPUT -p tcp --dport 27015 -j ACCEPT -m comment --comment "Insurgency RCON"

# Save rules (Ubuntu/Debian)
netfilter-persistent save

# Save rules (CentOS/RHEL)
service iptables save
</code></pre>

<h2 id="installation">Installation</h2>

<h3>Prerequisites</h3>
<ul>
    <li><strong>SteamCMD</strong> installed (<a href="https://developer.valvesoftware.com/wiki/SteamCMD" target="_blank">Installation Guide</a>)</li>
    <li>Disk space: ~20 GB for full installation</li>
    <li>RAM: 4GB minimum, 8GB recommended for 16+ players</li>
    <li>Open firewall ports (27102, 27131 UDP, 27015 TCP)</li>
</ul>

<h3>Linux Installation</h3>
<pre><code># Create server directory
mkdir -p ~/insurgency_server
cd ~/insurgency_server

# Download server files with SteamCMD
steamcmd +force_install_dir ~/insurgency_server +login anonymous +app_update 581320 validate +quit

# Note: App ID 581320 is for Insurgency: Sandstorm Dedicated Server
# Download size is approximately 18-20 GB
</code></pre>

<h3>Windows Installation</h3>
<pre><code># Download SteamCMD for Windows
# Extract to C:\steamcmd\

# Create server directory
mkdir C:\insurgency_server

# Run SteamCMD
C:\steamcmd\steamcmd.exe +force_install_dir C:\insurgency_server +login anonymous +app_update 581320 validate +quit
</code></pre>

<h2 id="configuration">⚙️ Configuration</h2>

<h3>Main Configuration Files</h3>
<p>Configuration files are in <code>Insurgency/Saved/Config/LinuxServer/</code> (Linux) or <code>Insurgency/Saved/Config/WindowsServer/</code> (Windows):</p>

<ul>
    <li><strong>Game.ini</strong> - Game settings, scenarios, map rotation</li>
    <li><strong>Engine.ini</strong> - Network settings, port configuration</li>
    <li><strong>ServerConfig.txt</strong> - Server name, admin password, RCON</li>
</ul>

<h3>Engine.ini (Network Configuration)</h3>
<p>Edit <code>Insurgency/Saved/Config/LinuxServer/Engine.ini</code>:</p>
<pre><code>[URL]
Port=27102

[/Script/Engine.GameNetworkManager]
TotalNetBandwidth=64000
MaxDynamicBandwidth=32000
MinDynamicBandwidth=16000

[SystemSettings]
net.MaxRepArraySize=2048
net.MaxRepArrayMemory=2048

[/Script/OnlineSubsystemUtils.IpNetDriver]
MaxClientRate=25000
MaxInternetClientRate=25000
</code></pre>

<h3>Game.ini (Server Settings)</h3>
<p>Edit <code>Insurgency/Saved/Config/LinuxServer/Game.ini</code>:</p>
<pre><code>[/Script/Insurgency.INSMultiplayerMode]
bAllowFriendlyFire=True
bMapVoting=True
RoundLimit=3
WinLimit=3
GameTimeLimit=-1
PreRoundTime=15
PostRoundTime=15
PostGameTime=15

[/Script/Insurgency.INSCoopMode]
bAllowFriendlyFire=True
RoundLimit=1
WinLimit=1
</code></pre>

<h3>Map Rotation Configuration</h3>
<p>Create <code>Insurgency/Saved/Config/LinuxServer/MapCycle.txt</code>:</p>
<pre><code># Competitive Push rotation
(Scenario="Scenario_Crossing_Push",Lighting="Day")
(Scenario="Scenario_Hideout_Push",Lighting="Day")
(Scenario="Scenario_Summit_Push",Lighting="Day")
(Scenario="Scenario_Tell_Push",Lighting="Day")
(Scenario="Scenario_Ministry_Push",Lighting="Day")

# Mix in night maps
(Scenario="Scenario_Hideout_Push",Lighting="Night")
(Scenario="Scenario_Ministry_Push",Lighting="Night")
</code></pre>

<h2 id="gamemodes">Game Modes</h2>

<h3>Competitive Modes (PvP)</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Mode</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Description</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Players</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Push</strong></td>
            <td style="padding: 12px;">Attackers push through objectives, defenders hold ground</td>
            <td style="padding: 12px;">8v8 (16)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Firefight</strong></td>
            <td style="padding: 12px;">Fast-paced 3 objective control, single life per round</td>
            <td style="padding: 12px;">5v5 (10)</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Frontline</strong></td>
            <td style="padding: 12px;">Tug-of-war over middle objectives with respawn waves</td>
            <td style="padding: 12px;">8v8 (16)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Skirmish</strong></td>
            <td style="padding: 12px;">Elimination with limited respawns, capture objectives to gain more</td>
            <td style="padding: 12px;">8v8 (16)</td>
        </tr>
    </tbody>
</table>

<h3>Cooperative Modes (PvE)</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Mode</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Description</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Players</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Checkpoint</strong></td>
            <td style="padding: 12px;">Team captures objectives against AI defenders</td>
            <td style="padding: 12px;">Up to 8 co-op</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Outpost</strong></td>
            <td style="padding: 12px;">Defend single objective against AI waves (Horde mode)</td>
            <td style="padding: 12px;">Up to 8 co-op</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Survival</strong></td>
            <td style="padding: 12px;">Extract-based co-op with permadeath and limited resources</td>
            <td style="padding: 12px;">Up to 8 co-op</td>
        </tr>
    </tbody>
</table>

<h2 id="maps">🗺️ Official Maps</h2>

<h3>Core Maps</h3>
<ul>
    <li><strong>Crossing</strong> - Mountain pass with open sightlines</li>
    <li><strong>Hideout</strong> - Close-quarters village combat</li>
    <li><strong>Summit</strong> - Snowy mountain compound</li>
    <li><strong>Tell</strong> - Dense urban environment</li>
    <li><strong>Ministry</strong> - Multi-story government building</li>
    <li><strong>Precinct</strong> - Police station and streets</li>
    <li><strong>Refinery</strong> - Industrial oil refinery complex</li>
    <li><strong>Farmhouse</strong> - Rural farmland and buildings</li>
</ul>

<h3>DLC Maps</h3>
<ul>
    <li><strong>Citadel</strong> - Ancient fortress ruins</li>
    <li><strong>Gap</strong> - Afghan village in mountain valley</li>
    <li><strong>PowerPlant</strong> - Geothermal power facility</li>
    <li><strong>Tideway</strong> - Port and cargo ship</li>
</ul>

<h3>Lighting Options</h3>
<p>Most maps support multiple lighting scenarios:</p>
<ul>
    <li><strong>Day</strong> - Standard daytime lighting</li>
    <li><strong>Night</strong> - Nighttime with NVG gameplay</li>
    <li><strong>Dusk/Dawn</strong> - Available on select maps</li>
</ul>

<h2 id="admin">🔧 Admin Tools & RCON</h2>

<h3>Enabling RCON</h3>
<p>RCON allows remote administration. Configure in startup parameters or config files.</p>

<h4>RCON Commands (Sample)</h4>
<pre><code># Player management
kick [PlayerName] [Reason]
ban [PlayerName]
listplayers

# Map control
travel [MapName]
restartround

# Server settings
say [Message]
setnextmap [MapName]
</code></pre>

<h3>Admin Console Commands</h3>
<p>In-game console (tilde key ~):</p>
<pre><code># Admin commands (requires admin login)
adminlogin [password]
kick [PlayerName]
ban [PlayerName]
travel [MapName]
restartround
say [Message]
</code></pre>

<h2 id="mods">🛠️ Mods & Mutators</h2>

<h3>Workshop Content</h3>
<p>Insurgency: Sandstorm supports Steam Workshop mods:</p>
<ul>
    <li>Custom maps and scenarios</li>
    <li>New weapons and attachments</li>
    <li>Gameplay mutators</li>
    <li>Cosmetic items</li>
</ul>

<h3>Installing Workshop Mods</h3>
<p>Subscribe to mods on Steam Workshop, then add their IDs to your startup command:</p>
<pre><code>-Mods=123456789,987654321
</code></pre>

<h3>Popular Mutators</h3>
<ul>
    <li><strong>Ismc Mod:</strong> Adds 100+ weapons and attachments</li>
    <li><strong>Day of Infamy Maps:</strong> Classic maps ported to Sandstorm</li>
    <li><strong>Custom Scenarios:</strong> Community-created game modes</li>
    <li><strong>Hardcore Mutators:</strong> Increased realism and difficulty</li>
</ul>

<h2 id="startup">Startup Commands</h2>

<h3>Linux</h3>
<pre><code>#!/bin/bash
# start_insurgency.sh

cd ~/insurgency_server

./Insurgency/Binaries/Linux/InsurgencyServer-Linux-Shipping \
  Crossing?Scenario=Scenario_Crossing_Push?Lighting=Day \
  -Port=27102 -QueryPort=27131 \
  -log -AdminList=Admins -MapCycle=MapCycle \
  -MaxPlayers=16 -Mods
</code></pre>

<h3>Windows</h3>
<pre><code>@echo off
REM start_insurgency.bat

cd C:\insurgency_server

InsurgencyServer.exe Crossing?Scenario=Scenario_Crossing_Push?Lighting=Day ^
  -Port=27102 -QueryPort=27131 ^
  -log -AdminList=Admins -MapCycle=MapCycle ^
  -MaxPlayers=16 -Mods
</code></pre>

<h3>Startup Parameters</h3>
<ul>
    <li><code>MapName?Scenario=ScenarioName?Lighting=Day</code> - Start map with scenario</li>
    <li><code>-Port=27102</code> - Game port</li>
    <li><code>-QueryPort=27131</code> - Server browser query port</li>
    <li><code>-MaxPlayers=16</code> - Maximum players (8v8 typical)</li>
    <li><code>-AdminList=Admins</code> - Admin list file (Admins.txt)</li>
    <li><code>-MapCycle=MapCycle</code> - Map rotation file (MapCycle.txt)</li>
    <li><code>-Mods</code> - Enable Workshop mods</li>
    <li><code>-log</code> - Enable detailed logging</li>
    <li><code>-hostname="Server Name"</code> - Server name in browser</li>
    <li><code>-password=serverpass</code> - Server password (optional)</li>
</ul>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not Appearing in Browser</h3>
<pre><code># Check ports are open
netstat -an | grep 27102
netstat -an | grep 27131

# Verify server is running
ps aux | grep Insurgency  # Linux
tasklist | findstr Insurgency  # Windows

# Check firewall allows both game and query ports
# Wait 5-10 minutes for Steam master server registration
</code></pre>

<h3>High Ping / Lag Issues</h3>
<ul>
    <li>Increase <code>MaxClientRate</code> in Engine.ini (try 50000-100000)</li>
    <li>Reduce <code>MaxPlayers</code> if server hardware is limited</li>
    <li>Ensure server has adequate CPU (UE4 is CPU-intensive)</li>
    <li>Check network bandwidth and latency to players</li>
</ul>

<h3>Mods Not Loading</h3>
<pre><code># Ensure -Mods flag is in startup command
# Verify Workshop mod IDs are correct
# Check mod compatibility with current game version
# Some mods require client and server to both have them

# Example with specific mod IDs:
-Mods=2389387394,2318862735
</code></pre>

<h3>Players Can't Connect</h3>
<ul>
    <li>Verify game port (27102) is open in firewall</li>
    <li>Check server isn't password-protected unintentionally</li>
    <li>Ensure server and client game versions match</li>
    <li>Verify <code>MaxPlayers</code> isn't reached</li>
    <li>Check logs in <code>Insurgency/Saved/Logs/</code></li>
</ul>

<h3>RCON Won't Connect</h3>
<pre><code># Verify RCON port (27015) is open
# Check RCON password is set in startup command or config
# Try connecting from localhost first (127.0.0.1)
# Use RCON tools compatible with Source-style RCON protocol
</code></pre>

<h3>Server Crashes on Startup</h3>
<ul>
    <li>Verify full installation via SteamCMD (validate)</li>
    <li>Check map/scenario names are spelled correctly</li>
    <li>Remove conflicting mods</li>
    <li>Ensure adequate disk space and RAM</li>
    <li>Check logs: <code>Insurgency/Saved/Logs/Insurgency.log</code></li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-lightbulb" style="color: #fbbf24; margin-right: 8px;"></i>Pro Tips</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Mixed Lighting:</strong> Rotate day/night versions of maps for variety</li>
        <li><strong>Balanced Teams:</strong> 8v8 is optimal for most maps and modes</li>
        <li><strong>Fire Support:</strong> Configure in Game.ini - adds tactical depth</li>
        <li><strong>Friendly Fire:</strong> Enable for realistic tactical gameplay</li>
        <li><strong>Vote Systems:</strong> Enable map voting for community choice</li>
        <li><strong>RCON Tools:</strong> Use tools like RconSharp or HLSW for easier admin</li>
        <li><strong>Regular Updates:</strong> Game updates frequently - validate files weekly</li>
        <li><strong>Hardware:</strong> CPU-heavy game - prioritize single-thread performance</li>
        <li><strong>Competitive Focus:</strong> Push and Firefight are most popular competitive modes</li>
        <li><strong>Co-op Casual:</strong> Checkpoint is great for casual co-op sessions</li>
    </ul>
</div>

<h2 id="resources">Resources</h2>
<ul>
    <li><a href="https://insurgencysandstorm.mod.io/" target="_blank">Insurgency: Sandstorm Mod.io</a></li>
    <li><a href="https://steamcommunity.com/app/581320/discussions/" target="_blank">Official Community Discussions</a></li>
    <li><a href="https://newworldinteractive.com/" target="_blank">New World Interactive (Developer)</a></li>
    <li><a href="https://sandstorm-support.newworldinteractive.com/hc/en-us" target="_blank">Official Support Portal</a></li>
    <li><a href="https://www.reddit.com/r/insurgency/" target="_blank">r/insurgency - Community Subreddit</a></li>
</ul>