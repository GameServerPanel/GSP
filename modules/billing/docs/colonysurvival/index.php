<?php
/**
 * Colony Survival Server Documentation - Comprehensive Guide
 * General game server hosting information (not platform-specific)
 */
?>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">📚 Quick Navigation</h3>
    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
        <a href="#quick-info" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Quick Info</a>
        <a href="#ports" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">🔌 Ports</a>
        <a href="#installation" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Installation</a>
        <a href="#configuration" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Configuration</a>
        <a href="#parameters" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">⚙️ Startup Parameters</a>
        <a href="#troubleshooting" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">🔧 Troubleshooting</a>
        <a href="#performance" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Performance</a>
        <a href="#security" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Security</a>
    </div>
</div>

<h1>Colony Survival Server Hosting Guide</h1>

<h2>Overview</h2>
<p>Colony Survival is a multiplayer game server that can be hosted on a VPS or dedicated server. This comprehensive guide covers everything you need to know about hosting a Colony Survival server for your community.</p>

<h2 id="quick-info">Quick Info</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <ul style="color: #e5e7eb; line-height: 1.8; margin: 0;">
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27040</code></li>
        <li><strong style="color: #ffffff;">Protocol:</strong> TCP/UDP</li>
        <li><strong style="color: #ffffff;">Minimum RAM:</strong> 1GB</li>
        <li><strong style="color: #ffffff;">Engine:</strong> Various</li>
        <li><strong style="color: #ffffff;">Steam App ID:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">748090</code></li>
        <li><strong style="color: #ffffff;">Recommended OS:</strong> Linux (Ubuntu/Debian) or Windows Server</li>
    </ul>
</div>

<h2 id="ports">🔌 Network Ports</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Required Ports</h3>
    <p style="color: #e5e7eb;">The Colony Survival server typically uses a configurable port. Check your server configuration files for the specific port settings.</p>
    
    <h3 style="color: #ffffff; margin-top: 20px;">Firewall Configuration</h3>
    <p style="color: #e5e7eb;">Allow server ports through your firewall:</p>
    <pre><code style="color: #a5b4fc;"># UFW (Ubuntu/Debian)
sudo ufw allow [PORT]/tcp
sudo ufw allow [PORT]/udp
sudo ufw reload

# FirewallD (CentOS/RHEL)
sudo firewall-cmd --permanent --add-port=[PORT]/tcp
sudo firewall-cmd --permanent --add-port=[PORT]/udp
sudo firewall-cmd --reload

# Windows Firewall
netsh advfirewall firewall add rule name="Colony Survival Server" dir=in action=allow protocol=TCP localport=[PORT]
netsh advfirewall firewall add rule name="Colony Survival Server" dir=in action=allow protocol=UDP localport=[PORT]
</code></pre>

    <h3 style="color: #ffffff; margin-top: 20px;">⚠️ Port Security Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Only open ports that are necessary for the game server to function</li>
        <li>Consider using non-standard ports to reduce automated attacks</li>
        <li>If using cloud hosting, configure security groups properly</li>
        <li>Monitor connection attempts and unusual traffic patterns</li>
    </ul>
</div>

<h2 id="installation">Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Linux (Ubuntu 20.04+ or Debian 11+ recommended) or Windows Server 2019+</li>
    <li><strong>CPU:</strong> 2+ cores recommended (single-threaded performance important for most game servers)</li>
    <li><strong>RAM:</strong> 1GB minimum (more for larger player counts)</li>
    <li><strong>Storage:</strong> 5GB+ for server files (SSD recommended for better performance)</li>
    <li><strong>Network:</strong> Stable internet connection with low latency</li>
</ul>

<h3>Installation Steps</h3>

<h4>Linux (Ubuntu/Debian)</h4>
<pre><code># Update system packages
sudo apt update && sudo apt upgrade -y

# Create server directory
mkdir -p ~/gameserver
cd ~/gameserver

# Download server files (method varies by game)
# Check official documentation for download links
</code></pre>

<h4>Windows Server</h4>
<p>Download the server files from the official game website or through Steam (if applicable). Extract to a dedicated folder and run the server executable.</p>

<h3>Using SteamCMD - RECOMMENDED METHOD</h3>
<p><strong>This game can be installed via SteamCMD using App ID: 748090</strong></p>

<h4>Install SteamCMD (Ubuntu/Debian)</h4>
<pre><code># Update package list
sudo apt update

# Enable 32-bit architecture
sudo dpkg --add-architecture i386
sudo apt update

# Install SteamCMD
sudo apt install -y lib32gcc-s1 steamcmd
</code></pre>

<h4>Download Server Files</h4>
<pre><code># Create directory for game server
mkdir -p ~/gameservers/colonysurvival

# Run SteamCMD and download
steamcmd +login anonymous \
         +force_install_dir ~/gameservers/colonysurvival \
         +app_update 748090 validate \
         +quit

# Server files are now in ~/gameservers/colonysurvival/
cd ~/gameservers/colonysurvival
ls -la
</code></pre>

<h4>Windows Installation with SteamCMD</h4>
<ol>
    <li>Download SteamCMD from: <a href="https://steamcdn-a.akamaihd.net/client/installer/steamcmd.zip" target="_blank">https://steamcdn-a.akamaihd.net/client/installer/steamcmd.zip</a></li>
    <li>Extract to <code>C:\steamcmd\</code></li>
    <li>Open Command Prompt and run:</li>
</ol>
<pre><code>cd C:\steamcmd
steamcmd.exe +login anonymous ^
             +force_install_dir C:\gameservers\colonysurvival ^
             +app_update 748090 validate ^
             +quit
</code></pre>


<h2 id="configuration">Server Configuration</h2>

<p>After installation, you'll need to configure your server. Here's where to find the configuration files and what settings you can change.</p>

<h3>Essential Settings</h3>
<ul>
    <li><strong>Server Name:</strong> Set a descriptive name for your server</li>
    <li><strong>Max Players:</strong> Configure based on your server's resources</li>
    <li><strong>Password:</strong> Optional password protection for private servers</li>
    <li><strong>Admin/RCON Password:</strong> Set a strong password for remote administration</li>
    <li><strong>Game Mode:</strong> Configure game-specific modes and settings</li>
</ul>

<h3>Server Commands</h3>
<p>Common administrative commands (access via console or RCON):</p>
<pre><code># Kick player
kick [player_name]

# Ban player
ban [player_name]

# Change map/level (syntax varies by game)
changelevel [map_name]

# Set admin password (if supported)
setadminpassword [password]
</code></pre>

<h2 id="parameters">⚙️ Startup Parameters</h2>

<h3>Basic Startup</h3>
<pre><code># Generic startup command structure
./server_executable [parameters]
</code></pre>

<h3>Common Parameters</h3>
<ul>
    <li><code>-port [number]</code> - Set the server port</li>
    <li><code>-maxplayers [number]</code> - Maximum player slots</li>
    <li><code>-map [name]</code> - Starting map/level</li>
    <li><code>-console</code> - Enable console output</li>
    <li><code>-nographics</code> - Run without graphics (headless mode)</li>
</ul>

<h3>Creating a Start Script</h3>

<p><strong>Linux (start.sh):</strong></p>
<pre><code>#!/bin/bash
cd /path/to/server
./server_executable [parameters] 2>&1 | tee server.log
</code></pre>
<pre><code>chmod +x start.sh
./start.sh
</code></pre>

<p><strong>Windows (start.bat):</strong></p>
<pre><code>@echo off
cd /d "%~dp0"
server_executable.exe [parameters]
pause
</code></pre>

<h3>Running as a Service</h3>

<p><strong>Linux (systemd):</strong></p>
<pre><code># Create service file: /etc/systemd/system/gameserver.service
[Unit]
Description=Colony Survival Server
After=network.target

[Service]
Type=simple
User=gameserver
WorkingDirectory=/home/gameserver/server
ExecStart=/home/gameserver/server/start.sh
Restart=on-failure
RestartSec=10

[Install]
WantedBy=multi-user.target
</code></pre>

<pre><code># Enable and start service
sudo systemctl daemon-reload
sudo systemctl enable gameserver
sudo systemctl start gameserver
sudo systemctl status gameserver
</code></pre>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Won't Start</h3>

<h4>Check Server Logs</h4>
<pre><code># View recent log entries
tail -f server.log

# Or check system logs
journalctl -u gameserver -f
</code></pre>

<h4>Port Already in Use</h4>
<pre><code># Find what's using the port
sudo lsof -i :[PORT]
sudo netstat -tulpn | grep [PORT]

# Kill the process or change server port
</code></pre>

<h4>Missing Dependencies</h4>
<p>Ensure all required dependencies are installed. Check the error messages for missing libraries or packages.</p>

<h3>Connection Issues</h3>

<h4>Can't Connect to Server</h4>
<ol>
    <li><strong>Verify server is running:</strong> <code>ps aux | grep server</code></li>
    <li><strong>Check port is listening:</strong> <code>netstat -an | grep [PORT]</code></li>
    <li><strong>Verify firewall rules</strong> (see Ports section above)</li>
    <li><strong>Check server IP:</strong> Use external IP, not localhost</li>
    <li><strong>Router/NAT:</strong> Ensure port forwarding is configured</li>
</ol>

<h4>High Latency/Lag</h4>
<ul>
    <li>Check server resource usage (CPU, RAM, disk I/O)</li>
    <li>Verify network bandwidth is adequate</li>
    <li>Consider server location relative to players</li>
    <li>Check for background processes consuming resources</li>
</ul>

<h3>Performance Issues</h3>

<h4>Server Lag</h4>
<ol>
    <li><strong>Monitor resources:</strong> Use <code>htop</code> or <code>top</code></li>
    <li><strong>Check disk I/O:</strong> Use <code>iotop</code></li>
    <li><strong>Review server logs</strong> for errors or warnings</li>
    <li><strong>Reduce player count</strong> or increase server resources</li>
    <li><strong>Optimize configuration</strong> based on server capacity</li>
</ol>

<h4>Memory Leaks</h4>
<pre><code># Monitor memory usage
free -h
top -p $(pgrep -f server)

# Restart server regularly via cron if needed
0 4 * * * /home/gameserver/restart.sh
</code></pre>

<h2 id="performance">Performance Optimization</h2>

<h3>Server Tuning</h3>
<ul>
    <li><strong>CPU:</strong> Ensure adequate CPU allocation; most game servers are single-threaded</li>
    <li><strong>RAM:</strong> Allocate sufficient memory; monitor usage and adjust as needed</li>
    <li><strong>Disk:</strong> Use SSD storage for better I/O performance</li>
    <li><strong>Network:</strong> Ensure stable, low-latency connection</li>
</ul>

<h3>Operating System Optimization</h3>
<pre><code># Increase file descriptor limits
echo "* soft nofile 65536" >> /etc/security/limits.conf
echo "* hard nofile 65536" >> /etc/security/limits.conf

# Network tuning
sysctl -w net.core.rmem_max=16777216
sysctl -w net.core.wmem_max=16777216
sysctl -w net.ipv4.tcp_rmem="4096 87380 16777216"
sysctl -w net.ipv4.tcp_wmem="4096 87380 16777216"
</code></pre>

<h3>Monitoring</h3>
<p>Set up monitoring to track server health:</p>
<ul>
    <li>CPU and memory usage</li>
    <li>Network traffic and latency</li>
    <li>Player count and activity</li>
    <li>Error rates and crash logs</li>
</ul>

<h3>Backup Strategy</h3>
<pre><code>#!/bin/bash
# backup.sh - Run via cron
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/gameserver"
SERVER_DIR="/home/gameserver/server"

# Create backup
tar -czf $BACKUP_DIR/backup_$DATE.tar.gz -C $SERVER_DIR .

# Keep only last 7 days
find $BACKUP_DIR -name "backup_*.tar.gz" -mtime +7 -delete
</code></pre>

<h2 id="security">Security Best Practices</h2>

<h3>Firewall Configuration</h3>
<pre><code># Minimal firewall - only allow necessary ports
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow [SERVER_PORT]/tcp
sudo ufw allow [SERVER_PORT]/udp
sudo ufw allow 22/tcp  # SSH
sudo ufw enable
</code></pre>

<h3>Strong Passwords</h3>
<ul>
    <li>Use strong, unique passwords for admin/RCON access</li>
    <li>Never use default passwords</li>
    <li>Change passwords regularly</li>
    <li>Don't share admin credentials unnecessarily</li>
</ul>

<h3>Regular Updates</h3>
<ul>
    <li>Keep server software updated to the latest stable version</li>
    <li>Update operating system and dependencies regularly</li>
    <li>Subscribe to security advisories for your game</li>
    <li>Test updates on a staging server before production deployment</li>
</ul>

<h3>Access Control</h3>
<ul>
    <li>Limit SSH access to specific IPs if possible</li>
    <li>Use SSH keys instead of passwords</li>
    <li>Disable root login via SSH</li>
    <li>Implement fail2ban or similar intrusion prevention</li>
</ul>

<h3>DDoS Protection</h3>
<ul>
    <li>Consider DDoS protection services (Cloudflare, OVH, etc.)</li>
    <li>Implement rate limiting where supported</li>
    <li>Monitor for unusual traffic patterns</li>
    <li>Have an incident response plan</li>
</ul>

<h2>Additional Resources</h2>
<ul>
    <li>Official Colony Survival documentation and forums</li>
    <li>Community wikis and guides</li>
    <li>Game-specific Discord or Reddit communities</li>
    <li>Server hosting provider documentation</li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8; margin: 0;">
        <li>Always make backups before making configuration changes</li>
        <li>Keep your server and dependencies updated</li>
        <li>Monitor server resources and player activity</li>
        <li>Follow the game's End User License Agreement (EULA) and Terms of Service</li>
        <li>Join community forums for support and best practices</li>
    </ul>
</div>

<p style="text-align: center; margin-top: 30px; color: #666;">
    <em>Last updated: November 2025 | For Colony Survival server hosting</em>
</p>
