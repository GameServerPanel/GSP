<?php
/**
 * Rust Server Documentation - Comprehensive Hosting Guide
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
        <a href="#performance" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Performance</a>
    </div>
</div>

<h1>Rust Server Hosting Guide</h1>

<h2>Overview</h2>
<p>Rust is a popular multiplayer survival game where players gather resources, build bases, and compete for survival. This comprehensive guide covers hosting a dedicated Rust server on Linux or Windows.</p>

<h2 id="quick-info">Quick Info</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <ul style="color: #e5e7eb; line-height: 1.8; margin: 0;">
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">28015</code> (UDP)</li>
        <li><strong style="color: #ffffff;">RCON Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">28016</code> (TCP)</li>
        <li><strong style="color: #ffffff;">Query Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">28017</code> (UDP/TCP - Rust+ app)</li>
        <li><strong style="color: #ffffff;">Minimum RAM:</strong> 8GB (small server)</li>
        <li><strong style="color: #ffffff;">Recommended RAM:</strong> 16GB+ (medium/large servers)</li>
        <li><strong style="color: #ffffff;">Storage:</strong> 20GB+ (can grow to 50GB+)</li>
        <li><strong style="color: #ffffff;">App ID:</strong> 258550 (dedicated server)</li>
        <li><strong style="color: #ffffff;">Max Players:</strong> Configurable (50-500+)</li>
        <li><strong style="color: #ffffff;">Map Size:</strong> 3000-6000 (default 4000)</li>
        <li><strong style="color: #ffffff;">Log Files:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">RustDedicated_Data/output_log.txt</code></li>
    </ul>
</div>

<h2 id="installation">Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Linux (Ubuntu 20.04+, Debian 10+) or Windows Server 2016+</li>
    <li><strong>CPU:</strong> Quad-core 3.2GHz+ (high single-thread performance)</li>
    <li><strong>RAM:</strong> 8GB minimum, 16GB+ recommended</li>
    <li><strong>Storage:</strong> 20GB+ SSD (HDD not recommended)</li>
    <li><strong>Bandwidth:</strong> 1Gbps+ recommended for 100+ players</li>
</ul>

<h3>Installing via SteamCMD</h3>
<p>Download: <a href="https://developer.valvesoftware.com/wiki/SteamCMD" target="_blank">SteamCMD Guide</a></p>

<h4>Linux Installation</h4>
<pre><code># Install SteamCMD
mkdir ~/steamcmd && cd ~/steamcmd
wget https://steamcdn-a.akamaihd.net/client/installer/steamcmd_linux.tar.gz
tar -xvzf steamcmd_linux.tar.gz

# Run SteamCMD and install Rust
./steamcmd.sh
login anonymous
force_install_dir /home/rust/server
app_update 258550 validate
quit
</code></pre>

<h4>Windows Installation</h4>
<pre><code>1. Download SteamCMD for Windows
2. Extract to C:\steamcmd
3. Run steamcmd.exe
4. login anonymous
5. force_install_dir C:\RustServer
6. app_update 258550 validate
7. quit
</code></pre>

<h2 id="configuration">Server Configuration</h2>

<h3>Basic Startup Script (Linux)</h3>
<pre><code>#!/bin/bash
# start.sh

export LD_LIBRARY_PATH=$LD_LIBRARY_PATH:/home/rust/server/RustDedicated_Data/Plugins/x86_64

cd /home/rust/server

./RustDedicated -batchmode \
    +server.ip 0.0.0.0 \
    +server.port 28015 \
    +server.tickrate 30 \
    +server.hostname "My Rust Server" \
    +server.identity "myserver" \
    +server.maxplayers 100 \
    +server.worldsize 4000 \
    +server.seed 12345 \
    +server.saveinterval 300 \
    +server.globalchat true \
    +server.description "Welcome to my server" \
    +server.headerimage "https://i.imgur.com/yourimage.png" \
    +server.url "https://yourwebsite.com" \
    +rcon.ip 0.0.0.0 \
    +rcon.port 28016 \
    +rcon.password "YourSecurePassword" \
    +rcon.web true \
    -logfile "logs/$(date +%Y%m%d_%H%M%S).txt"
</code></pre>

<h3>Windows Startup (start.bat)</h3>
<pre><code>@echo off
cls
:start
echo Starting Rust server...

RustDedicated.exe -batchmode ^
    +server.ip 0.0.0.0 ^
    +server.port 28015 ^
    +server.hostname "My Rust Server" ^
    +server.identity "myserver" ^
    +server.maxplayers 100 ^
    +server.worldsize 4000 ^
    +server.saveinterval 300 ^
    +rcon.port 28016 ^
    +rcon.password "YourSecurePassword"

goto start
</code></pre>

<h3>Server Identity</h3>
<p>Server data is stored in: <code>server/[identity]/</code></p>
<ul>
    <li><code>server/myserver/cfg/</code> - Config files</li>
    <li><code>server/myserver/UserPersistence/</code> - Player data</li>
    <li><code>server/myserver/proceduralmap.[seed].[size].map</code> - World file</li>
</ul>

<h3>server.cfg</h3>
<p>Create <code>server/myserver/cfg/server.cfg</code>:</p>
<pre><code>server.hostname "My Rust Server"
server.description "Welcome to my Rust server!"
server.url "https://yourwebsite.com"
server.headerimage "https://i.imgur.com/yourimage.png"
server.identity "myserver"
server.seed 12345
server.worldsize 4000
server.maxplayers 100
server.saveinterval 300
server.tickrate 30

# Gameplay
server.pve false
server.radiation true
server.stability true
decay.scale 1.0

# Performance
server.entityrate 16
server.planttick 60
server.planttickscale 1

# Global chat
server.globalchat true
server.chathistory 500

# Voice chat
voice.decay true

# RCON
rcon.password "YourSecurePassword"
rcon.web true
</code></pre>

<h2 id="parameters">Startup Parameters Reference</h2>

<h3>Essential Parameters</h3>
<pre><code>+server.ip "0.0.0.0"              # Server IP (0.0.0.0 = all interfaces)
+server.port 28015                 # Game port (UDP)
+server.hostname "Name"            # Server name (appears in browser)
+server.identity "folder_name"     # Server data folder name
+server.maxplayers 100             # Maximum players
+server.worldsize 4000             # Map size (1000-6000)
+server.seed 12345                 # World seed (random if not set)
+server.saveinterval 300           # Autosave interval (seconds)
+server.tickrate 30                # Server tick rate (10-30)
+server.description "Text"         # Server description
+server.url "https://url"          # Server website
+server.headerimage "URL"          # Server banner image
+rcon.ip "0.0.0.0"                 # RCON bind IP
+rcon.port 28016                   # RCON port (TCP)
+rcon.password "password"          # RCON password
+rcon.web true                     # Enable web/Rust+ RCON
</code></pre>

<h3>Gameplay Parameters</h3>
<pre><code>+server.pve false                  # PvE mode (true/false)
+server.radiation true             # Radiation enabled
+server.stability true             # Building stability
+server.secure true                # Require VAC
decay.scale 1.0                    # Decay rate multiplier
server.itemdespawn 180             # Item despawn time (minutes)
</code></pre>

<h3>Performance Parameters</h3>
<pre><code>+server.entityrate 16              # Entity network update rate
+fps.limit 60                      # Server FPS limit
+gc.buffer 4096                    # Garbage collection buffer
server.planttick 60                # Plant growth tick rate
server.planttickscale 1            # Plant growth speed
</code></pre>

<h2 id="plugins-mods">Plugins & Mods (Oxide/uMod)</h2>

<h3>Installing Oxide/uMod</h3>
<ol>
    <li>Download uMod: <a href="https://umod.org/games/rust" target="_blank">uMod.org</a></li>
    <li>Extract to server root directory</li>
    <li>Files go directly into <code>/server/</code> directory</li>
    <li>Restart server</li>
    <li>Plugins folder created at <code>oxide/plugins/</code></li>
</ol>

<h3>Essential Plugins</h3>

<h4>Admin Tools</h4>
<ul>
    <li><strong>Admin Radar:</strong> ESP-style admin radar</li>
    <li><strong>Vanish:</strong> Invisible admin mode</li>
    <li><strong>Better Chat:</strong> Chat formatting and moderation</li>
    <li><strong>Admin Hammer:</strong> Building modification tool</li>
</ul>

<h4>Gameplay Enhancements</h4>
<ul>
    <li><strong>Kits:</strong> Item kit system</li>
    <li><strong>Teleportation:</strong> /home, /tp commands</li>
    <li><strong>Clans:</strong> Clan/team system</li>
    <li><strong>Economics:</strong> Server currency system</li>
    <li><strong>Skip Night Vote:</strong> Vote to skip night</li>
</ul>

<h4>Protection</h4>
<ul>
    <li><strong>Anti Cheat Enhanced:</strong> Cheat detection</li>
    <li><strong>Raid Block:</strong> Prevent offline raiding</li>
    <li><strong>No Give:</strong> Prevent admin abuse</li>
</ul>

<h4>Performance</h4>
<ul>
    <li><strong>Auto Purge:</strong> Remove abandoned buildings</li>
    <li><strong>Entity Cleanup:</strong> Remove excess entities</li>
</ul>

<h3>Installing Plugins</h3>
<pre><code># 1. Download .cs plugin file
# 2. Place in oxide/plugins/
cd /home/rust/server/oxide/plugins/
wget https://umod.org/plugins/Plugin.cs

# 3. Plugin auto-loads (or use oxide.reload PluginName)
# 4. Configure in oxide/config/PluginName.json
</code></pre>

<h3>Plugin Configuration</h3>
<p>Configs auto-generate in <code>oxide/config/</code> on first load.</p>
<pre><code># Edit config
nano oxide/config/Kits.json

# In-game or RCON
oxide.reload Kits
</code></pre>

<h2 id="troubleshooting">Troubleshooting</h2>

<h3>Server Won't Start</h3>

<h4>Missing Libraries (Linux)</h4>
<pre><code># Install required libraries
sudo apt update
sudo apt install lib32gcc-s1 libcurl4-gnutls-dev:i386

# If still issues
sudo apt install lib32stdc++6 libc6-i386
</code></pre>

<h4>Port Already in Use</h4>
<pre><code># Check ports
sudo netstat -tulpn | grep 28015
sudo lsof -i :28015

# Kill process or change port
+server.port 28016 +rcon.port 28017
</code></pre>

<h4>Permission Denied (Linux)</h4>
<pre><code>chmod +x RustDedicated
chmod +x start.sh
</code></pre>

<h3>Server Not in Browser</h3>
<ol>
    <li><strong>Check firewall:</strong>
        <pre><code>sudo ufw allow 28015/udp
sudo ufw allow 28016/tcp
sudo ufw allow 28017/tcp
</code></pre>
    </li>
    <li><strong>Verify ports open:</strong> <code>netstat -tulpn | grep Rust</code></li>
    <li><strong>Wait 5-10 minutes:</strong> Can take time to appear</li>
    <li><strong>Direct connect:</strong> Press F1, type <code>client.connect your.ip:28015</code></li>
</ol>

<h3>High RAM Usage</h3>
<ol>
    <li><strong>Reduce worldsize:</strong> <code>+server.worldsize 3000</code></li>
    <li><strong>Lower max players:</strong> <code>+server.maxplayers 50</code></li>
    <li><strong>Increase saveinterval:</strong> <code>+server.saveinterval 600</code></li>
    <li><strong>Use Auto Purge plugin</strong></li>
    <li><strong>Regular wipes:</strong> Restart with fresh map weekly/monthly</li>
</ol>

<h3>Lag/Low FPS</h3>
<ol>
    <li><strong>Reduce entity count:</strong> Use Entity Cleanup plugin</li>
    <li><strong>Lower tickrate:</strong> <code>+server.tickrate 20</code> (default 30)</li>
    <li><strong>Increase planttick:</strong> <code>server.planttick 120</code></li>
    <li><strong>Monitor with:</strong> <code>perf 1</code> in console</li>
    <li><strong>Upgrade hardware:</strong> Rust is resource-intensive</li>
</ol>

<h3>Map Wipe</h3>
<pre><code># Stop server
# Delete map file
rm server/myserver/proceduralmap.*

# Change seed (optional)
+server.seed 54321

# Start server (generates new map)
./start.sh
</code></pre>

<h3>Blueprint Wipe</h3>
<pre><code># Stop server
# Delete blueprint data
rm -rf server/myserver/UserPersistence/

# Start server
./start.sh
</code></pre>

<h2 id="performance">Performance Optimization</h2>

<h3>Server Configuration</h3>
<pre><code>server.tickrate 25                 # Lower = better performance
server.entityrate 12               # Lower = less bandwidth
server.planttick 90                # Higher = less CPU usage
fps.limit 60                       # Limit server FPS
gc.buffer 4096                     # Garbage collection
</code></pre>

<h3>Map Size vs Performance</h3>
<ul>
    <li><strong>3000:</strong> Small, 50-75 players, 8GB RAM</li>
    <li><strong>4000:</strong> Medium, 100-150 players, 12GB RAM</li>
    <li><strong>5000:</strong> Large, 200+ players, 16GB+ RAM</li>
    <li><strong>6000:</strong> Huge, 300+ players, 24GB+ RAM</li>
</ul>

<h3>Scheduled Tasks</h3>
<pre><code>#!/bin/bash
# Auto-restart script with backup

# Backup
tar -czf backup_$(date +%Y%m%d).tar.gz server/myserver/

# Stop server
killall RustDedicated
sleep 10

# Update server
cd ~/steamcmd
./steamcmd.sh +login anonymous +force_install_dir /home/rust/server +app_update 258550 +quit

# Start server
cd /home/rust/server
./start.sh
</code></pre>

<h3>Crontab Auto-Restart</h3>
<pre><code># Edit crontab
crontab -e

# Restart daily at 6 AM
0 6 * * * /home/rust/restart.sh

# Save weekly at Sunday 5 AM
0 5 * * 0 tar -czf /backups/rust_$(date +\%Y\%m\%d).tar.gz /home/rust/server/myserver/
</code></pre>

<h2>RCON Management</h2>

<h3>RCON Tools</h3>
<ul>
    <li><strong>RustAdmin:</strong> <a href="https://www.rustadmin.com/" target="_blank">RustAdmin.com</a></li>
    <li><strong>RCONc:</strong> Web-based RCON</li>
    <li><strong>Rust+:</strong> Official mobile app (requires +rcon.web true)</li>
</ul>

<h3>Common RCON Commands</h3>
<pre><code># Player management
kick "PlayerName" "Reason"
ban "PlayerName" "Reason"
banid "SteamID64"
unban "SteamID64"
listid                           # List banned IDs
status                           # Show connected players

# Server management
save                             # Manual save
server.writecfg                  # Save config
server.stop                      # Stop server
server.restart                   # Restart server
oxide.reload PluginName          # Reload plugin

# Game settings
env.time 12                      # Set time (0-24)
weather.rain 0                   # Stop rain
airdrop.min_players 0            # Always allow airdrops
</code></pre>

<h2>Additional Resources</h2>
<ul>
    <li><a href="https://wiki.facepunch.com/rust/" target="_blank">Official Rust Wiki</a></li>
    <li><a href="https://umod.org/documentation" target="_blank">uMod Documentation</a></li>
    <li><a href="https://www.corrosionhour.com/" target="_blank">Corrosion Hour (Community & Guides)</a></li>
    <li><a href="https://www.rustafied.com/" target="_blank">Rustafied (News & Updates)</a></li>
    <li><a href="https://discord.gg/rust-server-admins" target="_blank">Rust Server Admin Discord</a></li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">⚠️ Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8; margin: 0;">
        <li>Rust servers require significant resources (8GB+ RAM minimum)</li>
        <li>Regular map wipes recommended (weekly/monthly)</li>
        <li>Keep server updated via SteamCMD</li>
        <li>Use strong RCON password</li>
        <li>Monitor server performance regularly</li>
        <li>Backup server data before updates</li>
    </ul>
</div>

<p style="text-align: center; margin-top: 30px; color: #666;">
    <em>Last updated: November 2024</em>
</p>
