<?php
/**
 * Minecraft Server Documentation - Comprehensive Guide
 * General game server hosting information (not platform-specific)
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
        <a href="#security" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Security</a>
    </div>
</div>

<h1>Minecraft Java Edition Server Hosting Guide</h1>

<h2>Overview</h2>
<p>Minecraft Java Edition is one of the most popular sandbox games worldwide, supporting extensive multiplayer capabilities. This comprehensive guide covers everything you need to know about hosting a Minecraft server on a VPS or dedicated server.</p>

<h2 id="quick-info">Quick Info</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <ul style="color: #e5e7eb; line-height: 1.8; margin: 0;">
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">25565</code> (TCP)</li>
        <li><strong style="color: #ffffff;">Protocol:</strong> TCP (Query on UDP 25565 if enabled)</li>
        <li><strong style="color: #ffffff;">Minimum RAM:</strong> 2GB (Vanilla), 4GB+ (Modded)</li>
        <li><strong style="color: #ffffff;">Recommended RAM:</strong> 1GB per 5-10 players + 2GB base</li>
        <li><strong style="color: #ffffff;">Java Version:</strong> Java 17+ (Minecraft 1.17+), Java 8+ (older versions)</li>
        <li><strong style="color: #ffffff;">Server Software:</strong> Vanilla, Spigot, Paper, Forge, Fabric</li>
        <li><strong style="color: #ffffff;">Log File:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">logs/latest.log</code></li>
        <li><strong style="color: #ffffff;">Main Config:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">server.properties</code></li>
        <li><strong style="color: #ffffff;">EULA:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">eula.txt</code> (must set eula=true)</li>
    </ul>
</div>

<h2 id="installation">Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Linux (Ubuntu/Debian recommended), Windows Server, or any Java-compatible OS</li>
    <li><strong>CPU:</strong> 2+ cores (single-threaded performance is critical)</li>
    <li><strong>RAM:</strong> Minimum 2GB, 4GB+ recommended for 10+ players</li>
    <li><strong>Storage:</strong> 1GB+ for server files, additional for worlds (can grow to 10GB+)</li>
    <li><strong>Bandwidth:</strong> ~1Mbps per player</li>
</ul>

<h3>Installing Java</h3>
<p>Minecraft requires Java to run. Install the appropriate version:</p>
<pre><code># Ubuntu/Debian - Java 17 (for MC 1.17+)
sudo apt update
sudo apt install openjdk-17-jre-headless

# Check Java version
java -version

# Set Java 17 as default if multiple versions installed
sudo update-alternatives --config java
</code></pre>

<h3>Downloading Server Files</h3>
<p>Download the official Minecraft server from <a href="https://www.minecraft.net/en-us/download/server" target="_blank">Minecraft.net</a>:</p>
<pre><code># Create server directory
mkdir minecraft-server
cd minecraft-server

# Download server jar (replace version number with desired version)
wget https://piston-data.mojang.com/v1/objects/[hash]/server.jar -O minecraft_server.jar

# Or use curl
curl -o minecraft_server.jar https://piston-data.mojang.com/v1/objects/[hash]/server.jar
</code></pre>

<h3>First-Time Setup</h3>
<pre><code># Run server once to generate files
java -Xmx1024M -Xms1024M -jar minecraft_server.jar nogui

# Accept EULA
echo "eula=true" > eula.txt

# Start server
java -Xmx2048M -Xms2048M -jar minecraft_server.jar nogui
</code></pre>

<h2 id="configuration">Server Configuration</h2>

<h3>server.properties - Essential Settings</h3>
<p>The <code>server.properties</code> file controls all server behavior:</p>
<pre><code># Server identification
server-name=My Minecraft Server
motd=Welcome to My Server!
server-port=25565
server-ip=0.0.0.0

# Gameplay settings
gamemode=survival
difficulty=normal
hardcore=false
pvp=true
enable-command-block=false

# World settings
level-name=world
level-seed=
level-type=default
generate-structures=true
spawn-protection=16
max-build-height=256
view-distance=10
simulation-distance=10

# Player limits
max-players=20
white-list=false
online-mode=true

# Performance & resource settings
max-tick-time=60000
max-world-size=29999984
network-compression-threshold=256
spawn-npcs=true
spawn-animals=true
spawn-monsters=true

# Query & RCON
enable-query=true
query.port=25565
enable-rcon=false
rcon.port=25575
rcon.password=changeme

# Misc
allow-flight=false
enforce-whitelist=false
resource-pack=
resource-pack-sha1=
</code></pre>

<h3>ops.json - Server Operators</h3>
<p>Grant admin privileges to players:</p>
<pre><code>[
  {
    "uuid": "player-uuid-here",
    "name": "PlayerName",
    "level": 4,
    "bypassesPlayerLimit": true
  }
]
</code></pre>
<p>Permission levels: 1 (bypass spawn protection), 2 (use cheat commands), 3 (kick/ban), 4 (full control)</p>

<h3>whitelist.json - Whitelist</h3>
<p>When <code>white-list=true</code> in server.properties:</p>
<pre><code>[
  {
    "uuid": "player-uuid-here",
    "name": "PlayerName"
  }
]
</code></pre>

<h2 id="parameters">Startup Parameters & JVM Arguments</h2>

<h3>Basic Startup Command</h3>
<pre><code>java -Xmx4G -Xms4G -jar minecraft_server.jar nogui
</code></pre>

<h3>Recommended JVM Arguments (Aikar's Flags)</h3>
<p>Optimized for Minecraft server performance:</p>
<pre><code>java -Xms4G -Xmx4G -XX:+UseG1GC -XX:+ParallelRefProcEnabled \
     -XX:MaxGCPauseMillis=200 -XX:+UnlockExperimentalVMOptions \
     -XX:+DisableExplicitGC -XX:+AlwaysPreTouch \
     -XX:G1NewSizePercent=30 -XX:G1MaxNewSizePercent=40 \
     -XX:G1HeapRegionSize=8M -XX:G1ReservePercent=20 \
     -XX:G1HeapWastePercent=5 -XX:G1MixedGCCountTarget=4 \
     -XX:InitiatingHeapOccupancyPercent=15 \
     -XX:G1MixedGCLiveThresholdPercent=90 \
     -XX:G1RSetUpdatingPauseTimePercent=5 \
     -XX:SurvivorRatio=32 -XX:+PerfDisableSharedMem \
     -XX:MaxTenuringThreshold=1 \
     -Dusing.aikars.flags=https://mcflags.emc.gs \
     -Daikars.new.flags=true \
     -jar minecraft_server.jar nogui
</code></pre>

<h3>Parameter Breakdown</h3>
<ul>
    <li><code>-Xms4G</code> - Initial heap size (4GB)</li>
    <li><code>-Xmx4G</code> - Maximum heap size (4GB) - should match Xms</li>
    <li><code>-XX:+UseG1GC</code> - Use G1 Garbage Collector (best for MC)</li>
    <li><code>-XX:+ParallelRefProcEnabled</code> - Parallel reference processing</li>
    <li><code>-XX:MaxGCPauseMillis=200</code> - Target max GC pause time</li>
    <li><code>-XX:+UnlockExperimentalVMOptions</code> - Enable experimental JVM options</li>
    <li><code>-XX:+AlwaysPreTouch</code> - Pre-touch memory pages on startup</li>
    <li><code>nogui</code> - Disable graphical interface (better performance)</li>
</ul>

<h3>Creating a Start Script</h3>
<p><strong>Linux (start.sh):</strong></p>
<pre><code>#!/bin/bash
java -Xms4G -Xmx4G -XX:+UseG1GC -jar minecraft_server.jar nogui
</code></pre>
<pre><code>chmod +x start.sh
./start.sh
</code></pre>

<p><strong>Windows (start.bat):</strong></p>
<pre><code>@echo off
java -Xms4G -Xmx4G -XX:+UseG1GC -jar minecraft_server.jar nogui
pause
</code></pre>

<h2 id="plugins-mods">Plugins, Mods & Server Software</h2>

<h3>Server Software Options</h3>

<h4>1. Vanilla</h4>
<ul>
    <li>Official Mojang server</li>
    <li>No plugin/mod support</li>
    <li>Best for pure vanilla experience</li>
    <li>Download: <a href="https://www.minecraft.net/en-us/download/server" target="_blank">Minecraft.net</a></li>
</ul>

<h4>2. Spigot</h4>
<ul>
    <li>Popular plugin platform</li>
    <li>Better performance than vanilla</li>
    <li>Large plugin ecosystem</li>
    <li>Download: <a href="https://www.spigotmc.org/" target="_blank">SpigotMC.org</a></li>
    <li>Build with BuildTools or download pre-built</li>
</ul>

<h4>3. Paper (Recommended)</h4>
<ul>
    <li>Fork of Spigot with major performance improvements</li>
    <li>Compatible with most Spigot plugins</li>
    <li>Additional bug fixes and features</li>
    <li>Download: <a href="https://papermc.io/" target="_blank">PaperMC.io</a></li>
</ul>

<h4>4. Forge</h4>
<ul>
    <li>Mod platform (not plugins)</li>
    <li>Required for most mods</li>
    <li>Download: <a href="https://files.minecraftforge.net/" target="_blank">MinecraftForge.net</a></li>
</ul>

<h4>5. Fabric</h4>
<ul>
    <li>Lightweight mod platform</li>
    <li>Faster updates than Forge</li>
    <li>Download: <a href="https://fabricmc.net/" target="_blank">FabricMC.net</a></li>
</ul>

<h3>Essential Plugins (Spigot/Paper)</h3>

<h4>EssentialsX</h4>
<p>Core commands and utilities for server management.</p>
<ul>
    <li>Download: <a href="https://essentialsx.net/" target="_blank">EssentialsX.net</a></li>
    <li>Features: /home, /spawn, /tpa, kits, warps, economy</li>
</ul>

<h4>LuckPerms</h4>
<p>Advanced permission management system.</p>
<ul>
    <li>Download: <a href="https://luckperms.net/" target="_blank">LuckPerms.net</a></li>
    <li>Features: Groups, permissions, prefixes, web editor</li>
</ul>

<h4>WorldEdit & WorldGuard</h4>
<p>In-game world editing and region protection.</p>
<ul>
    <li>Download: <a href="https://enginehub.org/" target="_blank">EngineHub.org</a></li>
    <li>WorldEdit: Bulk editing, schematics</li>
    <li>WorldGuard: Region protection, flags</li>
</ul>

<h4>Vault</h4>
<p>Economy and permission API bridge.</p>
<ul>
    <li>Required by many plugins for economy/permissions</li>
    <li>Download: <a href="https://www.spigotmc.org/resources/vault.34315/" target="_blank">SpigotMC</a></li>
</ul>

<h3>Installing Plugins</h3>
<pre><code># 1. Stop server
# 2. Download plugin .jar file
# 3. Place in plugins/ directory
cd plugins/
wget https://example.com/plugin.jar

# 4. Start server
# 5. Configure in plugins/PluginName/config.yml
</code></pre>

<h3>Popular Mods (Forge/Fabric)</h3>
<ul>
    <li><strong>OptiFine:</strong> Graphics and performance optimization</li>
    <li><strong>JourneyMap:</strong> In-game mapping</li>
    <li><strong>Biomes O' Plenty:</strong> New biomes</li>
    <li><strong>Applied Energistics 2:</strong> Storage and automation</li>
    <li><strong>Tinkers' Construct:</strong> Tool customization</li>
</ul>

<h2 id="troubleshooting">Troubleshooting</h2>

<h3>Server Won't Start</h3>

<h4>Java Not Found</h4>
<pre><code># Check if Java is installed
java -version

# If not installed, install Java (Ubuntu/Debian)
sudo apt update
sudo apt install openjdk-17-jre-headless
</code></pre>

<h4>EULA Not Accepted</h4>
<pre><code># You must agree to Minecraft EULA
echo "eula=true" > eula.txt
</code></pre>

<h4>Port Already in Use</h4>
<pre><code># Check what's using port 25565
sudo lsof -i :25565
sudo netstat -tulpn | grep 25565

# Kill process or change server-port in server.properties
</code></pre>

<h4>Out of Memory</h4>
<pre><code># Increase allocated RAM
java -Xms4G -Xmx4G -jar minecraft_server.jar nogui

# Or reduce if system has limited RAM
java -Xms2G -Xmx2G -jar minecraft_server.jar nogui
</code></pre>

<h3>Connection Issues</h3>

<h4>Can't Connect to Server</h4>
<ol>
    <li><strong>Check server is running:</strong> <code>ps aux | grep java</code></li>
    <li><strong>Verify port is listening:</strong> <code>netstat -an | grep 25565</code></li>
    <li><strong>Check firewall:</strong>
        <pre><code># Ubuntu/Debian (UFW)
sudo ufw allow 25565/tcp
sudo ufw reload

# CentOS/RHEL (firewalld)
sudo firewall-cmd --permanent --add-port=25565/tcp
sudo firewall-cmd --reload
</code></pre>
    </li>
    <li><strong>Verify server IP:</strong> Use external IP, not 127.0.0.1</li>
    <li><strong>Check online-mode:</strong> If cracked clients, set <code>online-mode=false</code></li>
</ol>

<h4>Connection Timed Out</h4>
<ul>
    <li>Router/NAT: Forward port 25565 to server</li>
    <li>Cloud provider: Add inbound rule for port 25565</li>
    <li>Server IP: Ensure <code>server-ip=</code> is blank or <code>0.0.0.0</code></li>
</ul>

<h3>Performance Issues</h3>

<h4>Server Lag/TPS Drop</h4>
<ol>
    <li><strong>Check TPS:</strong> <code>/tps</code> or use Spark profiler</li>
    <li><strong>Reduce view distance:</strong> Set <code>view-distance=6-8</code></li>
    <li><strong>Reduce simulation distance:</strong> <code>simulation-distance=4-6</code></li>
    <li><strong>Limit entities:</strong>
        <pre><code># spigot.yml or paper.yml
entity-activation-range:
  animals: 16
  monsters: 24
  misc: 8
</code></pre>
    </li>
    <li><strong>Use Paper:</strong> Better performance than Spigot/Vanilla</li>
    <li><strong>Pregenerate world:</strong> Use Chunky plugin to pre-generate chunks</li>
</ol>

<h4>Memory Leaks</h4>
<pre><code># Monitor memory usage
free -h
top -p $(pgrep -f minecraft_server)

# Restart server regularly (daily/weekly) via cron
0 4 * * * /path/to/restart-script.sh
</code></pre>

<h3>World Corruption</h3>
<ol>
    <li><strong>Stop server immediately</strong></li>
    <li><strong>Backup world folder:</strong> <code>cp -r world/ world_backup/</code></li>
    <li><strong>Use MCEdit or Amulet to repair:</strong> <a href="https://www.amuletmc.com/" target="_blank">AmuletMC.com</a></li>
    <li><strong>Restore from backup if needed</strong></li>
    <li><strong>Prevention:</strong> Always stop server properly, use backup plugins</li>
</ol>

<h3>Plugin Conflicts</h3>
<ol>
    <li><strong>Check console for errors</strong></li>
    <li><strong>Disable plugins one-by-one to isolate issue</strong></li>
    <li><strong>Update all plugins to latest versions</strong></li>
    <li><strong>Check plugin compatibility with server version</strong></li>
</ol>

<h2 id="performance">Performance Optimization</h2>

<h3>Server Configuration</h3>
<pre><code># server.properties
view-distance=8
simulation-distance=6
network-compression-threshold=256
entity-broadcast-range-percentage=100
</code></pre>

<h3>Paper Configuration</h3>
<p>Create/edit <code>paper.yml</code> or <code>config/paper-global.yml</code>:</p>
<pre><code>chunk-loading:
  target-chunk-send-rate: 100.0
  max-concurrent-sends: 2

async-chunks:
  enable: true
  threads: -1

entity-activation-range:
  animals: 16
  monsters: 24
  raiders: 48
  misc: 8
  water: 8
  villagers: 16
  flying-monsters: 48

tick-rates:
  sensor:
    villager:
      secondarypoisensor: 80
  behavior:
    villager:
      validatenearbypoi: 60
</code></pre>

<h3>Pregenerate World</h3>
<p>Use Chunky plugin to pre-generate chunks:</p>
<pre><code># Install Chunky plugin
# In-game or console:
/chunky radius 5000
/chunky world world
/chunky start

# Let it complete before opening server to players
</code></pre>

<h3>Backup Strategy</h3>
<pre><code>#!/bin/bash
# backup.sh - Run via cron
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/minecraft"
SERVER_DIR="/home/minecraft/server"

# Create backup
tar -czf $BACKUP_DIR/world_$DATE.tar.gz -C $SERVER_DIR world/

# Keep only last 7 days
find $BACKUP_DIR -name "world_*.tar.gz" -mtime +7 -delete
</code></pre>

<h2 id="security">Security Best Practices</h2>

<h3>Firewall Configuration</h3>
<pre><code># Only allow Minecraft port
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow 25565/tcp
sudo ufw allow 22/tcp  # SSH
sudo ufw enable
</code></pre>

<h3>Whitelist</h3>
<pre><code># Enable whitelist in server.properties
white-list=true

# Add players in-game or console
/whitelist add PlayerName
/whitelist on
</code></pre>

<h3>RCON Security</h3>
<pre><code># If using RCON, use strong password
enable-rcon=true
rcon.password=Use_A_Very_Strong_Random_Password_Here
rcon.port=25575

# Bind to localhost only if possible
rcon.ip=127.0.0.1
</code></pre>

<h3>Regular Updates</h3>
<ul>
    <li>Keep server software updated</li>
    <li>Update plugins regularly</li>
    <li>Monitor security advisories</li>
    <li>Test updates on staging server first</li>
</ul>

<h3>DDoS Protection</h3>
<ul>
    <li>Use TCP SYN cookies: <code>echo 1 > /proc/sys/net/ipv4/tcp_syncookies</code></li>
    <li>Consider DDoS protection service (Cloudflare Spectrum, OVH Game, etc.)</li>
    <li>Use BungeeCord/Velocity proxy for multiple servers</li>
    <li>Implement rate limiting with iptables/fail2ban</li>
</ul>

<h2>Additional Resources</h2>
<ul>
    <li><a href="https://minecraft.fandom.com/wiki/Server.properties" target="_blank">Minecraft Wiki - Server.properties</a></li>
    <li><a href="https://www.spigotmc.org/" target="_blank">SpigotMC Forums & Resources</a></li>
    <li><a href="https://papermc.io/downloads" target="_blank">Paper Downloads & Documentation</a></li>
    <li><a href="https://aikar.co/mcflags.html" target="_blank">Aikar's JVM Flags</a></li>
    <li><a href="https://docs.papermc.io/" target="_blank">Paper Documentation</a></li>
    <li><a href="https://github.com/YouHaveTrouble/minecraft-optimization" target="_blank">Minecraft Server Optimization Guide</a></li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8; margin: 0;">
        <li>Always accept Mojang's EULA before running a server</li>
        <li>Make regular backups of your world data</li>
        <li>Keep server software and plugins updated</li>
        <li>Monitor server resources (CPU, RAM, disk)</li>
        <li>Join Minecraft server admin communities for support</li>
    </ul>
</div>

<p style="text-align: center; margin-top: 30px; color: #666;">
    <em>Last updated: November 2024 | For Minecraft Java Edition 1.20+</em>
</p>
