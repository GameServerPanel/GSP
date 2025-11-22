<?php
/**
 * Miscreated Server Documentation - Comprehensive Guide
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

<h1>Miscreated Server Hosting Guide</h1>

<h2>Overview</h2>
<p>Miscreated is a multiplayer game server that can be hosted on a VPS or dedicated server. This comprehensive guide covers everything you need to know about hosting a Miscreated server for your community.</p>

<h2 id="quick-info">Quick Info</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <ul style="color: #e5e7eb; line-height: 1.8; margin: 0;">
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">64090</code></li>
        <li><strong style="color: #ffffff;">Protocol:</strong> TCP/UDP</li>
        <li><strong style="color: #ffffff;">Minimum RAM:</strong> 1GB</li>
        <li><strong style="color: #ffffff;">Engine:</strong> Various</li>
        <li><strong style="color: #ffffff;">Steam App ID:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">302200</code></li>
        <li><strong style="color: #ffffff;">Recommended OS:</strong> Linux (Ubuntu/Debian) or Windows Server</li>
            <li><strong style="color: #ffffff;">Configuration Files:</strong><ul style="margin-top: 8px;">
            <li><code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">hosting.cfg</code> - Server Configuration</li>
        </ul></li>
    </ul>
</div>

<h2 id="ports">🔌 Network Ports</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Required Ports</h3>
    <p style="color: #e5e7eb;">The Miscreated server typically uses a configurable port. Check your server configuration files for the specific port settings.</p>
    
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
netsh advfirewall firewall add rule name="Miscreated Server" dir=in action=allow protocol=TCP localport=[PORT]
netsh advfirewall firewall add rule name="Miscreated Server" dir=in action=allow protocol=UDP localport=[PORT]
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
<p><strong>This game can be installed via SteamCMD using App ID: 302200</strong></p>

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
mkdir -p ~/gameservers/miscreated

# Run SteamCMD and download
steamcmd +login anonymous \
         +force_install_dir ~/gameservers/miscreated \
         +app_update 302200 validate \
         +quit

# Server files are now in ~/gameservers/miscreated/
cd ~/gameservers/miscreated
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
             +force_install_dir C:\gameservers\miscreated ^
             +app_update 302200 validate ^
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

<h3>Configuration Files</h3>
<p>Important configuration files for this server:</p>
<ul>
    <li><strong><code>hosting.cfg</code></strong> - Server Configuration</li>
</ul>
