<?php
/**
 * Valheim Dedicated Server - Comprehensive Hosting Guide
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

<h1>Valheim Dedicated Server Hosting Guide</h1>

<h2>Overview</h2>
<p>Valheim is a brutal survival and exploration game for 1-10 players set in a procedurally-generated purgatory inspired by Viking culture. This comprehensive guide covers everything you need to know about hosting a Valheim dedicated server on a VPS or dedicated server.</p>

<h2 id="quick-info">Quick Info</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <ul style="color: #e5e7eb; line-height: 1.8; margin: 0;">
        <li><strong style="color: #ffffff;">Default Ports:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">2456-2458</code> (UDP)</li>
        <li><strong style="color: #ffffff;">Protocol:</strong> UDP (Steam connectivity)</li>
        <li><strong style="color: #ffffff;">Minimum RAM:</strong> 4GB (Recommended: 8GB+)</li>
        <li><strong style="color: #ffffff;">Recommended CPU:</strong> 2+ cores @ 3.5GHz (4+ for 5+ players)</li>
        <li><strong style="color: #ffffff;">Storage:</strong> 5GB+ for game files, additional for worlds</li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 10 vanilla (higher with mods, may cause lag)</li>
        <li><strong style="color: #ffffff;">SteamCMD App ID:</strong> 896660</li>
        <li><strong style="color: #ffffff;">Startup Scripts:</strong> start_headless_server.bat (Windows) / start_server.sh (Linux)</li>
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
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">2456</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Primary game port (client connections)</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #7c2d12; padding: 4px 8px; border-radius: 3px; color: #fed7aa;">✓ Yes</span></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">2457</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Steam query port (automatic +1)</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #7c2d12; padding: 4px 8px; border-radius: 3px; color: #fed7aa;">✓ Yes</span></td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">2458</code></td>
            <td style="padding: 12px;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span></td>
            <td style="padding: 12px;">Server browser port (automatic +2)</td>
            <td style="padding: 12px;"><span style="background: #7c2d12; padding: 4px 8px; border-radius: 3px; color: #fed7aa;">✓ Yes</span></td>
        </tr>
    </tbody>
</table>

<div style="background: #7c2d12; padding: 15px; border-left: 4px solid #ea580c; margin: 20px 0; border-radius: 4px;">
    <p style="color: #fed7aa; margin: 0;"><strong>Important:</strong> Valheim requires all three consecutive ports. The game automatically uses port+1 and port+2. Always open a range of three consecutive ports starting from your base port (e.g., 2456-2458).</p>
</div>

<h3>Firewall Configuration Examples</h3>

<h4>UFW (Ubuntu/Debian)</h4>
<pre><code>sudo ufw allow 2456:2458/udp comment 'Valheim server ports'
sudo ufw reload
</code></pre>

<h4>FirewallD (CentOS/RHEL/Fedora)</h4>
<pre><code>sudo firewall-cmd --permanent --add-port=2456-2458/udp
sudo firewall-cmd --reload
</code></pre>

<h4>Windows Firewall</h4>
<pre><code># Run in PowerShell as Administrator
New-NetFirewallRule -DisplayName "Valheim Server" -Direction Inbound -Protocol UDP -LocalPort 2456-2458 -Action Allow
</code></pre>

<h4>iptables (Legacy Linux)</h4>
<pre><code>sudo iptables -A INPUT -p udp --dport 2456:2458 -j ACCEPT
sudo service iptables save
</code></pre>

<h2 id="installation">Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Windows Server 2016+ or Linux 64-bit (Ubuntu/Debian recommended)</li>
    <li><strong>CPU:</strong> Minimum 2 cores @ 2.4GHz; Recommended 4+ cores @ 3.5GHz+</li>
    <li><strong>RAM:</strong> 4GB minimum, 8GB recommended, 16GB for large groups</li>
    <li><strong>Storage:</strong> 5GB+ for game files, allow extra for world saves and backups</li>
    <li><strong>Network:</strong> 1Mbps minimum upload; 10Mbps+ recommended for stable gameplay</li>
    <li><strong>Bandwidth:</strong> ~1Mbps per player; wired connection strongly recommended</li>
</ul>

<h3>Installing via Steam (Windows)</h3>
<pre><code>1. Open Steam and go to your Library
2. Use the dropdown menu and check "Tools"
3. Locate "Valheim Dedicated Server" in the list
4. Click "Install" and choose installation directory
5. Wait for download to complete
</code></pre>

<h3>Installing via SteamCMD (Linux/Windows)</h3>
<pre><code># Install SteamCMD first (if not already installed)
# Ubuntu/Debian:
sudo apt update
sudo apt install steamcmd

# Create server directory
mkdir -p ~/valheim-server
cd ~/valheim-server

# Download server files
steamcmd +login anonymous +force_install_dir ~/valheim-server +app_update 896660 validate +exit

# The server files will be downloaded to your specified directory
</code></pre>

<h3>First-Time Setup</h3>
<p>Before starting your server for the first time, you'll need to configure the startup parameters.</p>

<h2 id="configuration">Server Configuration</h2>

<h3>Startup Scripts</h3>
<p>Valheim uses startup scripts to configure the server. Edit the appropriate file for your OS:</p>

<h4>Windows: start_headless_server.bat</h4>
<pre><code>@echo off
set SteamAppId=892970
valheim_server.exe -nographics -batchmode ^
    -name "MyValheimServer" ^
    -port 2456 ^
    -world "MyWorld" ^
    -password "MyPassword123" ^
    -public 1
</code></pre>

<h4>Linux: start_server.sh</h4>
<pre><code>#!/bin/bash
export SteamAppId=892970

./valheim_server.x86_64 -nographics -batchmode \
    -name "MyValheimServer" \
    -port 2456 \
    -world "MyWorld" \
    -password "MyPassword123" \
    -public 1 \
    -logfile /path/to/valheim.log
</code></pre>

<h3>Admin Configuration Files</h3>
<p>Create these files in the server directory to manage administrators, bans, and whitelists:</p>

<h4>adminlist.txt</h4>
<pre><code># Add Steam64 IDs (one per line)
76561198012345678
76561198087654321
</code></pre>

<h4>bannedlist.txt</h4>
<pre><code># Add Steam64 IDs of banned players
76561198099999999
</code></pre>

<h4>permittedlist.txt</h4>
<pre><code># For whitelist mode - only these IDs can join
76561198012345678
76561198087654321
</code></pre>

<h2 id="parameters">Startup Parameters</h2>

<h3>Essential Parameters</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <tr style="background: #f8f9fa;">
        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Parameter</th>
        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Description</th>
        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Example</th>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>-name</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Server name (appears in browser)</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">"My Valheim Server"</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>-port</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Server port (default 2456)</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">2456</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>-world</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">World/save name</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">"Midgard"</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>-password</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Server password (required)</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">"SecurePass123"</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>-public</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">1=Public listing, 0=Private</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">1</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>-savedir</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Custom save directory path</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">"/path/to/saves"</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>-logfile</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Path to log file</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">"/var/log/valheim.log"</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>-nographics</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Run headless (no GUI)</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Required for dedicated servers</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #dee2e6;"><code>-batchmode</code></td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Run in batch mode</td>
        <td style="padding: 8px; border: 1px solid #dee2e6;">Required for dedicated servers</td>
    </tr>
</table>

<h3>Port Forwarding</h3>
<p>You must forward/open the following ports on your firewall:</p>
<ul>
    <li><strong>UDP 2456:</strong> Main game port (also set with -port parameter)</li>
    <li><strong>UDP 2457:</strong> Secondary port (2456 + 1)</li>
    <li><strong>UDP 2458:</strong> Tertiary port (2456 + 2)</li>
</ul>

<h4>Linux Firewall (UFW)</h4>
<pre><code># Allow Valheim ports
sudo ufw allow 2456:2458/udp
sudo ufw reload
</code></pre>

<h4>Windows Firewall</h4>
<pre><code># Open Windows Defender Firewall with Advanced Security
# Create new Inbound Rules for UDP ports 2456-2458
# Or use PowerShell:
New-NetFirewallRule -DisplayName "Valheim Server" -Direction Inbound -Protocol UDP -LocalPort 2456-2458 -Action Allow
</code></pre>

<h2 id="troubleshooting">Troubleshooting</h2>

<h3>Server Won't Start</h3>
<p><strong>Problem:</strong> Server fails to start or crashes immediately.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Check log files for error messages</li>
    <li>Verify all parameters are correctly formatted in startup script</li>
    <li>Ensure server files are fully downloaded (run SteamCMD validate)</li>
    <li>Check file permissions on Linux (<code>chmod +x start_server.sh</code>)</li>
    <li>Verify you have sufficient RAM and disk space</li>
</ul>

<h3>Server Not Appearing in Browser</h3>
<p><strong>Problem:</strong> Server doesn't show up in the in-game server list.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Ensure <code>-public 1</code> is set in startup parameters</li>
    <li>Check that ports 2456-2458 UDP are properly forwarded</li>
    <li>Verify firewall rules allow the ports</li>
    <li>Try connecting directly using IP:port in Steam server list</li>
    <li>Wait a few minutes - it can take time to appear in the browser</li>
</ul>

<h3>Connection Issues</h3>
<p><strong>Problem:</strong> Players cannot connect to the server.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Double-check password is correct and communicated to players</li>
    <li>Verify port forwarding is configured correctly</li>
    <li>Test with the public IP address, not local/LAN IP</li>
    <li>Check router NAT type and consider DMZ if necessary</li>
    <li>Disable any VPN on the server</li>
</ul>

<h3>Lag and Performance Issues</h3>
<p><strong>Problem:</strong> Server experiences lag, stuttering, or poor performance.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Reduce player count if exceeding 5-10 players</li>
    <li>Upgrade server hardware (CPU and RAM)</li>
    <li>Use wired Ethernet connection, not WiFi</li>
    <li>Close unnecessary background processes</li>
    <li>Consider professional hosting for high-population servers</li>
    <li>Keep the world size manageable (large explored worlds can lag)</li>
</ul>

<h3>World/Save Corruption</h3>
<p><strong>Problem:</strong> World save is corrupted or progress is lost.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Restore from backup (make regular backups!)</li>
    <li>Check disk health and fix errors</li>
    <li>Avoid forced shutdowns or crashes</li>
    <li>Use a reliable backup system (automated backups recommended)</li>
</ul>

<h2 id="performance">Performance Optimization</h2>

<h3>Server Resource Management</h3>
<ul>
    <li><strong>RAM:</strong> Allocate 8GB+ for smoother experience with multiple players</li>
    <li><strong>CPU:</strong> Higher single-core performance is more important than core count</li>
    <li><strong>Storage:</strong> Use SSD for better world loading performance</li>
    <li><strong>Network:</strong> Minimum 10Mbps upload for 5+ players</li>
</ul>

<h3>Backup Strategy</h3>
<pre><code># Linux backup script example
#!/bin/bash
WORLD_NAME="MyWorld"
BACKUP_DIR="/path/to/backups"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup
cp ~/.config/unity3d/IronGate/Valheim/worlds/$WORLD_NAME.* $BACKUP_DIR/

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.fwl" -mtime +7 -delete
find $BACKUP_DIR -name "*.db" -mtime +7 -delete
</code></pre>

<h3>Automated Restarts</h3>
<p>Set up daily restarts to clear memory and apply updates:</p>
<pre><code># Linux crontab entry for 4 AM restart
0 4 * * * /path/to/restart_valheim.sh

# restart_valheim.sh:
#!/bin/bash
pkill -9 valheim_server
sleep 10
cd /home/valheim/server
./start_server.sh &amp;
</code></pre>

<h3>Console Commands (In-Game Admin)</h3>
<p>Enable console with <code>-console</code> parameter, press F5 in-game:</p>
<ul>
    <li><code>devcommands</code> - Enable admin commands</li>
    <li><code>kick [player name]</code> - Kick a player</li>
    <li><code>ban [player name]</code> - Ban a player</li>
    <li><code>unban [player name]</code> - Unban a player</li>
    <li><code>save</code> - Force save the world</li>
    <li><code>resetskill [skill]</code> - Reset player skill level</li>
</ul>

<h2>Additional Resources</h2>
<ul>
    <li><a href="https://valheim.com/support/a-guide-to-dedicated-servers/" target="_blank">Official Valheim Dedicated Server Guide</a></li>
    <li><a href="https://valheim.fandom.com/wiki/Dedicated_servers" target="_blank">Valheim Wiki - Dedicated Servers</a></li>
    <li><a href="https://steamcommunity.com/app/892970/discussions/" target="_blank">Steam Community Discussions</a></li>
    <li><a href="https://www.reddit.com/r/valheim/" target="_blank">r/valheim - Community Support</a></li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Always keep your server updated to the latest version via SteamCMD</li>
        <li>Make regular automated backups of your world saves</li>
        <li>Test firewall rules and port forwarding before inviting players</li>
        <li>Monitor server performance and adjust resources as needed</li>
        <li>Use strong passwords to protect your server</li>
    </ul>
</div>