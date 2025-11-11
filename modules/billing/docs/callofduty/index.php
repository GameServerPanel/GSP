<?php
/**
 * Call of Duty Server Documentation - Comprehensive Guide
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
        <a href="#related-mods" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Related Mods</a>
    </div>
</div>

<h1>Call of Duty Server Hosting Guide</h1>

<h2>Overview</h2>
<p>Call of Duty is a legendary World War II first-person shooter that revolutionized multiplayer gaming. This guide covers everything you need to know about hosting a dedicated Call of Duty server on Linux or Windows platforms.</p>

<h2 id="quick-info">Quick Info</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <ul style="color: #e5e7eb; line-height: 1.8; margin: 0;">
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">28960</code> (UDP)</li>
        <li><strong style="color: #ffffff;">Protocol:</strong> UDP</li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 64 (recommended 16-32 for best performance)</li>
        <li><strong style="color: #ffffff;">Server Executable:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">cod_lnxded</code> (Linux) / <code>CoDMP.exe</code> (Windows)</li>
        <li><strong style="color: #ffffff;">Control Protocol:</strong> RCON (Remote Console)</li>
        <li><strong style="color: #ffffff;">Main Config:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">server.cfg</code></li>
        <li><strong style="color: #ffffff;">Recommended RAM:</strong> 512MB - 1GB</li>
        <li><strong style="color: #ffffff;">PunkBuster Support:</strong> Yes</li>
    </ul>
</div>

<h2 id="ports">🔌 Network Ports Used</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Required Ports</h3>
    <table style="width: 100%; color: #e5e7eb; border-collapse: collapse;">
        <thead>
            <tr style="background: #0f172a;">
                <th style="padding: 10px; text-align: left; color: #ffffff;">Port</th>
                <th style="padding: 10px; text-align: left; color: #ffffff;">Protocol</th>
                <th style="padding: 10px; text-align: left; color: #ffffff;">Purpose</th>
                <th style="padding: 10px; text-align: left; color: #ffffff;">Required?</th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom: 1px solid #374151;">
                <td style="padding: 10px;"><code style="background: #0f172a; padding: 2px 6px; border-radius: 3px;">28960</code></td>
                <td style="padding: 10px;">UDP</td>
                <td style="padding: 10px;">Main game server port (default)</td>
                <td style="padding: 10px;"><span style="color: #10b981;">✓ Yes</span></td>
            </tr>
            <tr style="border-bottom: 1px solid #374151;">
                <td style="padding: 10px;"><code style="background: #0f172a; padding: 2px 6px; border-radius: 3px;">20500-20510</code></td>
                <td style="padding: 10px;">UDP</td>
                <td style="padding: 10px;">Master server queries</td>
                <td style="padding: 10px;"><span style="color: #f59e0b;">○ Optional</span></td>
            </tr>
        </tbody>
    </table>
    
    <h3 style="color: #ffffff; margin-top: 20px;">Firewall Configuration Examples</h3>
    <pre><code style="color: #a5b4fc;"># UFW (Ubuntu/Debian)
sudo ufw allow 28960/udp comment 'CoD Server'

# FirewallD (CentOS/RHEL)
sudo firewall-cmd --permanent --add-port=28960/udp
sudo firewall-cmd --reload

# iptables
sudo iptables -A INPUT -p udp --dport 28960 -j ACCEPT
sudo iptables-save > /etc/iptables/rules.v4
</code></pre>
</div>

<h2 id="installation">Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Linux (32-bit or 64-bit with 32-bit libraries) or Windows</li>
    <li><strong>CPU:</strong> 1+ GHz processor</li>
    <li><strong>RAM:</strong> Minimum 512MB, 1GB recommended</li>
    <li><strong>Storage:</strong> 500MB for server files</li>
    <li><strong>Bandwidth:</strong> 256kbps upload per player</li>
</ul>

<h3>Linux Installation</h3>
<pre><code># Install 32-bit libraries (if on 64-bit system)
sudo dpkg --add-architecture i386
sudo apt update
sudo apt install lib32gcc1 lib32stdc++6

# Create server directory
mkdir ~/cod-server
cd ~/cod-server

# Extract server files (upload via FTP or download)
# Server files typically include: cod_lnxded, main/ directory, etc.
chmod +x cod_lnxded
</code></pre>

<h3>First-Time Setup</h3>
<pre><code># Create basic server.cfg
cat > main/server.cfg << 'EOF'
// Server Settings
set sv_hostname "My CoD Server"
set sv_maxclients 32
set rcon_password "your_secure_password"

// Game Settings
set g_gametype "dm"  // dm, tdm, sd, retrieval
set scr_friendlyfire "1"
set g_allowvote "1"

// Map Rotation
set sv_mapRotation "gametype dm map mp_harbor gametype tdm map mp_pavlov"
EOF

# Start server
./cod_lnxded +set dedicated 2 +set net_port 28960 +exec server.cfg +map_rotate
</code></pre>

<h2 id="configuration">Server Configuration</h2>

<h3>server.cfg - Essential Settings</h3>
<pre><code>// Server Identity
set sv_hostname "^1My ^7CoD Server"
set sv_maxclients 32

// Network Settings
set sv_maxRate 25000
set sv_maxPing 0
set sv_minPing 0

// Administrative
set rcon_password "your_secure_rcon_password"
set g_password ""  // Server password (leave empty for public)
set g_log "games_mp.log"
set g_logsync "1"

// Gameplay Settings
set g_gametype "dm"  // dm, tdm, sd, retrieval, hq
set scr_friendlyfire "1"
set scr_killcam "1"
set scr_drawfriend "1"
set g_allowvote "1"

// Time Limits and Score
set scr_dm_timelimit "15"
set scr_dm_scorelimit "50"
set scr_tdm_timelimit "15"
set scr_tdm_scorelimit "100"

// Map Rotation
set sv_mapRotation "gametype dm map mp_harbor gametype tdm map mp_pavlov gametype dm map mp_brecourt gametype tdm map mp_carentan"

// PunkBuster (Anti-Cheat)
set sv_punkbuster "1"
pb_sv_enable
</code></pre>

<h2 id="parameters">⚙️ Startup Parameters</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Command Line Parameters</h3>
    <table style="width: 100%; color: #e5e7eb; border-collapse: collapse;">
        <thead>
            <tr style="background: #0f172a;">
                <th style="padding: 10px; text-align: left; color: #ffffff;">Parameter</th>
                <th style="padding: 10px; text-align: left; color: #ffffff;">Description</th>
                <th style="padding: 10px; text-align: left; color: #ffffff;">Example</th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom: 1px solid #374151;">
                <td style="padding: 10px;"><code>+set dedicated</code></td>
                <td style="padding: 10px;">Set server type (1=LAN, 2=Internet)</td>
                <td style="padding: 10px;"><code>+set dedicated 2</code></td>
            </tr>
            <tr style="border-bottom: 1px solid #374151;">
                <td style="padding: 10px;"><code>+set net_ip</code></td>
                <td style="padding: 10px;">Bind to specific IP address</td>
                <td style="padding: 10px;"><code>+set net_ip 0.0.0.0</code></td>
            </tr>
            <tr style="border-bottom: 1px solid #374151;">
                <td style="padding: 10px;"><code>+set net_port</code></td>
                <td style="padding: 10px;">Set server port</td>
                <td style="padding: 10px;"><code>+set net_port 28960</code></td>
            </tr>
            <tr style="border-bottom: 1px solid #374151;">
                <td style="padding: 10px;"><code>+set fs_basepath</code></td>
                <td style="padding: 10px;">Base game directory path</td>
                <td style="padding: 10px;"><code>+set fs_basepath /home/cod</code></td>
            </tr>
            <tr style="border-bottom: 1px solid #374151;">
                <td style="padding: 10px;"><code>+set fs_homepath</code></td>
                <td style="padding: 10px;">Home directory for configs/logs</td>
                <td style="padding: 10px;"><code>+set fs_homepath /home/cod</code></td>
            </tr>
            <tr style="border-bottom: 1px solid #374151;">
                <td style="padding: 10px;"><code>+set fs_game</code></td>
                <td style="padding: 10px;">Load a mod (folder in main/)</td>
                <td style="padding: 10px;"><code>+set fs_game mods/mymod</code></td>
            </tr>
            <tr style="border-bottom: 1px solid #374151;">
                <td style="padding: 10px;"><code>+set rcon_password</code></td>
                <td style="padding: 10px;">RCON password for remote admin</td>
                <td style="padding: 10px;"><code>+set rcon_password mypass</code></td>
            </tr>
            <tr style="border-bottom: 1px solid #374151;">
                <td style="padding: 10px;"><code>+set sv_maxclients</code></td>
                <td style="padding: 10px;">Maximum player slots</td>
                <td style="padding: 10px;"><code>+set sv_maxclients 32</code></td>
            </tr>
            <tr style="border-bottom: 1px solid #374151;">
                <td style="padding: 10px;"><code>+set sv_punkbuster</code></td>
                <td style="padding: 10px;">Enable PunkBuster (0=off, 1=on)</td>
                <td style="padding: 10px;"><code>+set sv_punkbuster 1</code></td>
            </tr>
            <tr style="border-bottom: 1px solid #374151;">
                <td style="padding: 10px;"><code>+exec</code></td>
                <td style="padding: 10px;">Execute config file on startup</td>
                <td style="padding: 10px;"><code>+exec server.cfg</code></td>
            </tr>
            <tr>
                <td style="padding: 10px;"><code>+map_rotate</code></td>
                <td style="padding: 10px;">Start map rotation defined in config</td>
                <td style="padding: 10px;"><code>+map_rotate</code></td>
            </tr>
        </tbody>
    </table>

    <h3 style="color: #ffffff; margin-top: 20px;">Full Startup Command Example</h3>
    <pre><code style="color: #a5b4fc;">./cod_lnxded +set dedicated 2 +set net_ip 0.0.0.0 +set net_port 28960 +set fs_basepath /home/cod +set fs_homepath /home/cod +set sv_punkbuster 0 +exec server.cfg +set rcon_password mypassword +set sv_maxclients 32 +map_rotate</code></pre>
</div>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Common Issues & Solutions</h3>
    
    <h4 style="color: #fef3c7;">Server Not Visible in Server Browser</h4>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Check firewall:</strong> Ensure port 28960/UDP is open</li>
        <li><strong>Verify dedicated setting:</strong> Must be <code>+set dedicated 2</code> for internet servers</li>
        <li><strong>Master server:</strong> Official master servers may no longer be active; consider server listing services</li>
        <li><strong>Port forwarding:</strong> If behind NAT, forward UDP port 28960 to server IP</li>
    </ul>

    <h4 style="color: #fef3c7;">Server Crashes on Startup</h4>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Missing libraries:</strong> Install 32-bit libraries on 64-bit Linux systems</li>
        <li><strong>Permissions:</strong> Ensure server executable has execute permissions (<code>chmod +x cod_lnxded</code>)</li>
        <li><strong>Invalid paths:</strong> Check fs_basepath and fs_homepath point to valid directories</li>
        <li><strong>Corrupted files:</strong> Re-verify server file integrity</li>
    </ul>

    <h4 style="color: #fef3c7;">Players Can't Connect</h4>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Version mismatch:</strong> Ensure clients and server run same game version/patch</li>
        <li><strong>Password protected:</strong> Remove g_password or share password with players</li>
        <li><strong>Server full:</strong> Check sv_maxclients setting</li>
        <li><strong>Firewall blocking:</strong> Verify firewall rules allow incoming UDP on game port</li>
    </ul>

    <h4 style="color: #fef3c7;">High Ping / Lag Issues</h4>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Bandwidth:</strong> Ensure sufficient upload bandwidth (256kbps per player minimum)</li>
        <li><strong>Max rate:</strong> Adjust <code>sv_maxRate</code> to match available bandwidth</li>
        <li><strong>Player count:</strong> Reduce sv_maxclients if server can't handle load</li>
        <li><strong>CPU usage:</strong> Monitor CPU usage; upgrade if consistently maxed out</li>
    </ul>

    <h4 style="color: #fef3c7;">RCON Not Working</h4>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Password:</strong> Verify rcon_password is set correctly</li>
        <li><strong>Firewall:</strong> RCON uses same port as game (28960/UDP)</li>
        <li><strong>Tools:</strong> Use proper RCON tools (B3, web-based RCON, or in-game console)</li>
    </ul>
</div>

<h3>Checking Server Status</h3>
<pre><code># Check if server process is running
ps aux | grep cod_lnxded

# View server logs
tail -f main/games_mp.log

# Check port is listening
netstat -ulnp | grep 28960
# or
ss -ulnp | grep 28960
</code></pre>

<h2>Game Types</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">dm</strong> - Free-for-All Deathmatch</li>
        <li><strong style="color: #ffffff;">tdm</strong> - Team Deathmatch</li>
        <li><strong style="color: #ffffff;">sd</strong> - Search and Destroy</li>
        <li><strong style="color: #ffffff;">retrieval</strong> - Retrieval (Capture the Flag variant)</li>
        <li><strong style="color: #ffffff;">hq</strong> - Headquarters</li>
        <li><strong style="color: #ffffff;">bel</strong> - Behind Enemy Lines</li>
    </ul>
</div>

<h2>Default Maps</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <ul style="color: #e5e7eb; line-height: 1.8; columns: 2;">
        <li>mp_harbor</li>
        <li>mp_pavlov</li>
        <li>mp_brecourt</li>
        <li>mp_carentan</li>
        <li>mp_chateau</li>
        <li>mp_depot</li>
        <li>mp_dawnville</li>
        <li>mp_downtown</li>
        <li>mp_hurtgen</li>
        <li>mp_neuville</li>
        <li>mp_powcamp</li>
        <li>mp_railyard</li>
        <li>mp_rocket</li>
        <li>mp_ship</li>
        <li>mp_tigertown</li>
    </ul>
</div>

<h2 id="related-mods">🔌 Related Mods & Plugins</h2>
<p>Popular server modifications compatible with Call of Duty:</p>
<ul>
    <li><a href="../b3/">B3 (Big Brother Bot)</a> - Python-based admin bot with RCON wrapper, player warnings, ban management, statistics tracking, and automated moderation for Call of Duty servers</li>
</ul>

<h2>Resources</h2>
<ul>
    <li><a href="https://www.callofduty.com" target="_blank">Official Call of Duty Website</a></li>
    <li><a href="/docs.php?action=view&doc=common-issues">Common Issues Guide</a></li>
    <li>Server configuration generators and map rotation tools available online</li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Call of Duty (2003) master servers may no longer be operational; direct connect via IP may be required</li>
        <li>PunkBuster support may be deprecated; consider community anti-cheat solutions</li>
        <li>Back up your server.cfg and any custom configurations regularly</li>
        <li>Monitor logs for cheaters and troublemaking players</li>
        <li>Keep RCON password secure and change it regularly</li>
    </ul>
</div>