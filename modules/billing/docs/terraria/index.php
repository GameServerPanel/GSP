<?php
/**
 * Terraria Dedicated Server - Comprehensive Hosting Guide
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
        <a href="#tshock" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">TShock</a>
        <a href="#troubleshooting" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">🔧 Troubleshooting</a>
        <a href="#performance" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Performance</a>
    </div>
</div>

<h1>Terraria Dedicated Server Hosting Guide</h1>

<h2>Overview</h2>
<p>Terraria is a 2D action-adventure sandbox game developed by Re-Logic. With over 5000 items, bosses, NPCs, and extensive building mechanics, Terraria offers rich multiplayer experiences. This comprehensive guide covers hosting a Terraria dedicated server on a VPS or dedicated server.</p>

<h2 id="quick-info">Quick Info</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <ul style="color: #e5e7eb; line-height: 1.8; margin: 0;">
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">7777</code> (TCP)</li>
        <li><strong style="color: #ffffff;">Protocol:</strong> TCP</li>
        <li><strong style="color: #ffffff;">Minimum RAM:</strong> 1GB (2GB+ for mods)</li>
        <li><strong style="color: #ffffff;">Recommended RAM:</strong> 4-8GB for larger servers</li>
        <li><strong style="color: #ffffff;">Storage:</strong> 500MB+ for server, additional for worlds</li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 8-255 (configurable)</li>
        <li><strong style="color: #ffffff;">Server Executable:</strong> TerrariaServer.exe (Windows), TerrariaServer (Linux)</li>
        <li><strong style="color: #ffffff;">Config File:</strong> serverconfig.txt</li>
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
            <td style="padding: 12px;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">7777</code></td>
            <td style="padding: 12px;"><span style="background: #1e40af; padding: 4px 8px; border-radius: 3px; color: #dbeafe;">TCP</span></td>
            <td style="padding: 12px;">Game server port (player connections)</td>
            <td style="padding: 12px;"><span style="background: #7c2d12; padding: 4px 8px; border-radius: 3px; color: #fed7aa;">✓ Yes</span></td>
        </tr>
    </tbody>
</table>

<div style="background: #1e3a5f; padding: 15px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <p style="color: #e5e7eb; margin: 0;"><strong>Note:</strong> Terraria is one of the simpler games to configure - it only requires a single TCP port. You can change this to any available port (1024-65535), just ensure it matches your <code>serverconfig.txt</code> configuration.</p>
</div>

<h3>Firewall Configuration Examples</h3>

<h4>UFW (Ubuntu/Debian)</h4>
<pre><code>sudo ufw allow 7777/tcp comment 'Terraria server'
sudo ufw reload
</code></pre>

<h4>FirewallD (CentOS/RHEL/Fedora)</h4>
<pre><code>sudo firewall-cmd --permanent --add-port=7777/tcp
sudo firewall-cmd --reload
</code></pre>

<h4>Windows Firewall</h4>
<pre><code># Run in PowerShell as Administrator
New-NetFirewallRule -DisplayName "Terraria Server" -Direction Inbound -Protocol TCP -LocalPort 7777 -Action Allow
</code></pre>

<h4>iptables (Legacy Linux)</h4>
<pre><code>sudo iptables -A INPUT -p tcp --dport 7777 -j ACCEPT
sudo service iptables save
</code></pre>

<h2 id="installation">Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Windows, Linux, or macOS</li>
    <li><strong>CPU:</strong> Dual-core minimum; Quad-core for 8+ players</li>
    <li><strong>RAM:</strong> 1GB minimum; 4-8GB for larger modded servers</li>
    <li><strong>Storage:</strong> 500MB+ for server files; SSD recommended</li>
    <li><strong>Network:</strong> 512kbps per player recommended</li>
</ul>

<h3>Windows Installation</h3>
<pre><code>1. Locate Terraria installation directory:
   C:\Program Files (x86)\Steam\steamapps\common\Terraria\

2. Find TerrariaServer.exe in the main folder

3. Run TerrariaServer.exe
   - Follow the setup wizard
   - Choose existing world or create new
   - Set max players
   - Set port (default 7777)
   - Set password (optional)

4. Server will start and display connection information
</code></pre>

<h3>Linux Installation</h3>
<pre><code># Download Terraria server files
wget https://terraria.org/api/download/pc-dedicated-server/terraria-server-1449.zip

# Extract files
unzip terraria-server-1449.zip
cd 1449/Linux/

# Make executable
chmod +x TerrariaServer*

# Run server
./TerrariaServer.bin.x86_64

# Or for headless/background:
screen -S terraria ./TerrariaServer.bin.x86_64
# Detach with Ctrl+A, D
# Reattach with: screen -r terraria
</code></pre>

<h3>macOS Installation</h3>
<pre><code># Download server files from terraria.org
# Extract and navigate to Mac folder
cd ~/terraria-server/Mac/

# Make executable
chmod +x TerrariaServer*

# Run server
./TerrariaServer.bin.osx
</code></pre>

<h2 id="configuration">Server Configuration</h2>

<h3>Configuration File (serverconfig.txt)</h3>
<p>Create <code>serverconfig.txt</code> in the server directory:</p>
<pre><code># World Configuration
world=/path/to/Worlds/MyWorld.wld
autocreate=3
worldname=MyWorld

# Server Settings
maxplayers=16
port=7777
password=YourPassword
motd=Welcome to my Terraria server!

# Security
banlist=banlist.txt
secure=1

# Network
priority=1
npcstream=60
</code></pre>

<h3>Configuration Parameters</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <tr style="background: #f8f9fa;">
        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Parameter</th>
        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Description</th>
        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Values</th>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>world</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Path to world file</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">/path/to/world.wld</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>autocreate</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Auto-create world size</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">1=Small, 2=Medium, 3=Large</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>maxplayers</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Maximum player slots</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">1-255 (8-16 typical)</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>port</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Server port</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Default: 7777</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>password</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Server password</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Any string (optional)</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>motd</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Message of the day</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Text message</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>difficulty</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">World difficulty</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">0=Normal, 1=Expert, 2=Master</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>secure</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Prevent cheating</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">0=Off, 1=On</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>npcstream</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">NPC update frequency</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Default: 60</td>
    </tr>
</table>

<h3>Port Forwarding</h3>
<pre><code># Forward TCP port 7777 (or your configured port)
# Linux firewall (UFW):
sudo ufw allow 7777/tcp
sudo ufw reload

# Windows Firewall:
New-NetFirewallRule -DisplayName "Terraria Server" -Direction Inbound -Protocol TCP -LocalPort 7777 -Action Allow
</code></pre>

<h2 id="tshock">TShock Server Framework</h2>

<h3>What is TShock?</h3>
<p>TShock is a server modification that adds extensive administrative features, permissions, anti-grief protection, and plugin support to Terraria servers.</p>

<h3>Installing TShock</h3>
<pre><code># Download TShock from https://github.com/Pryaxis/TShock/releases

# Extract TShock files
unzip TShock.zip

# Run TShock server
./TShock.Server

# First run creates configuration files
# Configure in tshock/config.json
</code></pre>

<h3>TShock Features</h3>
<ul>
    <li><strong>User Permissions:</strong> Fine-grained permission system</li>
    <li><strong>Anti-Grief:</strong> Protect regions, prevent item spawn abuse</li>
    <li><strong>User Management:</strong> Registration, login, groups</li>
    <li><strong>Admin Commands:</strong> Extensive server control</li>
    <li><strong>Plugins:</strong> Extend functionality with community plugins</li>
    <li><strong>REST API:</strong> Remote server management</li>
</ul>

<h3>Common TShock Commands</h3>
<pre><code>/user add USERNAME PASSWORD GROUP
/group add GROUPNAME "permissions"
/region define REGIONNAME
/whitelist add USERNAME
/ban add USERNAME reason
/give PLAYER ITEMID AMOUNT
/time set 12:00
/butcher - Kill all hostile NPCs
</code></pre>

<h2 id="troubleshooting">Troubleshooting</h2>

<h3>Server Won't Start</h3>
<p><strong>Problem:</strong> Server fails to launch or crashes immediately.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Check server logs for error messages</li>
    <li>Verify all paths in serverconfig.txt are correct</li>
    <li>Ensure port 7777 isn't already in use (<code>netstat -an | grep 7777</code>)</li>
    <li>Check file permissions (Linux: <code>chmod +x TerrariaServer*</code>)</li>
    <li>Verify world file isn't corrupted</li>
</ul>

<h3>Players Cannot Connect</h3>
<p><strong>Problem:</strong> Players can't join the server.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify port 7777 TCP is forwarded on router</li>
    <li>Check firewall allows traffic on port 7777</li>
    <li>Confirm password is correct (case-sensitive)</li>
    <li>Use external IP address, not local/LAN IP</li>
    <li>Test with <code>telnet SERVERIP 7777</code></li>
    <li>Ensure server is running and accepting connections</li>
</ul>

<h3>Lag and Performance Issues</h3>
<p><strong>Problem:</strong> Server experiences lag or stuttering.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Reduce max players if exceeding capacity</li>
    <li>Use SSD instead of HDD for better I/O</li>
    <li>Increase server RAM allocation</li>
    <li>Disable or reduce mods/plugins</li>
    <li>Clean up excessive items/projectiles in world</li>
    <li>Use smaller world size for lower player counts</li>
</ul>

<h3>World Corruption</h3>
<p><strong>Problem:</strong> World file corrupted or won't load.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Restore from backup (always maintain backups!)</li>
    <li>Try loading world in single-player Terraria client</li>
    <li>Use world repair tools if available</li>
    <li>Check disk for errors</li>
    <li>Avoid forced server shutdowns</li>
</ul>

<h3>Mod/Plugin Issues</h3>
<p><strong>Problem:</strong> Mods not loading or causing crashes.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Ensure mod/plugin versions match Terraria version</li>
    <li>Check for mod conflicts</li>
    <li>Update TShock and plugins to latest versions</li>
    <li>Review mod documentation for dependencies</li>
    <li>Test mods individually to identify problematic ones</li>
</ul>

<h2 id="performance">Performance Optimization</h2>

<h3>Server Sizing Guidelines</h3>
<ul>
    <li><strong>Small (2-4 players):</strong> 1GB RAM, dual-core CPU</li>
    <li><strong>Medium (5-8 players):</strong> 2-4GB RAM, dual/quad-core CPU</li>
    <li><strong>Large (10-16 players):</strong> 4-8GB RAM, quad-core CPU</li>
    <li><strong>Modded servers:</strong> Add 2-4GB RAM depending on mod count</li>
</ul>

<h3>Backup Strategy</h3>
<pre><code># Linux backup script
#!/bin/bash
WORLD_DIR="/path/to/Terraria/Worlds"
BACKUP_DIR="/path/to/backups"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup
tar -czf $BACKUP_DIR/terraria_backup_$DATE.tar.gz $WORLD_DIR

# Keep only last 14 days
find $BACKUP_DIR -name "terraria_backup_*.tar.gz" -mtime +14 -delete
</code></pre>

<h3>Automated Restarts</h3>
<p>Set up daily restarts for optimal performance:</p>
<pre><code># Linux crontab for 4 AM restart
0 4 * * * /path/to/restart_terraria.sh

# restart_terraria.sh:
#!/bin/bash
pkill -9 TerrariaServer
sleep 5
cd /path/to/terraria
screen -dmS terraria ./TerrariaServer.bin.x86_64 -config serverconfig.txt
</code></pre>

<h3>World Management</h3>
<ul>
    <li>Regular backups before major events or boss fights</li>
    <li>Clean up unnecessary items periodically</li>
    <li>Monitor world file size growth</li>
    <li>Consider starting fresh worlds for new major updates</li>
</ul>

<h2>Modding Resources</h2>

<h3>TModLoader</h3>
<p><a href="https://github.com/tModLoader/tModLoader" target="_blank">TModLoader</a> is a mod loader for Terraria that allows players to create and play mods.</p>

<h3>Popular Mods</h3>
<ul>
    <li><strong>Calamity Mod:</strong> Massive content expansion</li>
    <li><strong>Thorium Mod:</strong> New items, bosses, and biomes</li>
    <li><strong>Fargo's Mods:</strong> Quality of life improvements</li>
    <li><strong>Magic Storage:</strong> Advanced item storage system</li>
    <li><strong>Boss Checklist:</strong> Track boss progression</li>
</ul>

<h2>Additional Resources</h2>
<ul>
    <li><a href="https://terraria.fandom.com/wiki/Guide:Setting_up_a_Terraria_server" target="_blank">Terraria Wiki - Server Setup Guide</a></li>
    <li><a href="https://github.com/Pryaxis/TShock" target="_blank">TShock GitHub Repository</a></li>
    <li><a href="https://tshock.readme.io/docs" target="_blank">TShock Documentation</a></li>
    <li><a href="https://forums.terraria.org/index.php?forums/server-help.61/" target="_blank">Official Terraria Forums - Server Help</a></li>
    <li><a href="https://www.reddit.com/r/Terraria/" target="_blank">r/Terraria Community</a></li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Always backup your world files before major changes</li>
        <li>Keep server software updated to match client versions</li>
        <li>Use strong passwords to protect your server</li>
        <li>Monitor resource usage and adjust player limits accordingly</li>
        <li>Consider TShock for advanced server management</li>
    </ul>
</div>