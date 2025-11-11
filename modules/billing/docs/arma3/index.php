<?php
/**
 * Arma 3 Server Documentation
 * Comprehensive guide for hosting and managing Arma 3 dedicated servers
 * 
 * Sources: Bohemia Interactive Wiki, LGSM, Steam Community, r/armaservers
 * Last Updated: November 10, 2025
 */
?>
<style>
    .doc-nav {
        background: #1e3a5f;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
    }
    .doc-nav h3 {
        color: #ffffff;
        margin-top: 0;
    }
    .doc-nav a {
        display: inline-block;
        padding: 8px 15px;
        margin: 5px 10px 5px 0;
        background: rgba(0,0,0,0.3);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 5px;
        color: #7fb3ff;
        text-decoration: none;
    }
    .doc-nav a:hover {
        background: #3b82f6;
        color: #ffffff;
    }
    .info-box {
        background: #1e3a5f;
        padding: 20px;
        border-left: 4px solid #3b82f6;
        margin: 20px 0;
        border-radius: 4px;
    }
    .warning-box {
        background: #78350f;
        padding: 20px;
        border-left: 4px solid #f59e0b;
        margin: 20px 0;
        border-radius: 4px;
    }
    .ports-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        background: rgba(0,0,0,0.2);
    }
    .ports-table th, .ports-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .ports-table th {
        background: #1e3a5f;
        color: #ffffff;
        font-weight: 600;
    }
    .ports-table code {
        background: #0f172a;
        padding: 2px 6px;
        border-radius: 3px;
        color: #a5b4fc;
    }
    .required { color: #10b981; font-weight: 600; }
    .optional { color: #f59e0b; }
</style>

<!-- Navigation -->
<div class="doc-nav">
    <h3>📚 Quick Navigation</h3>
    <a href="#overview">Overview</a>
    <a href="#ports">🔌 Ports</a>
    <a href="#installation">Installation</a>
    <a href="#configuration">Configuration</a>
    <a href="#startup">⚙️ Startup Parameters</a>
    <a href="#mods">Mods & Addons</a>
    <a href="#troubleshooting">🔧 Troubleshooting</a>
    <a href="#performance">Performance</a>
    <a href="#security">Security</a>
    <a href="#resources">Resources</a>
</div>

<h1 id="overview">Arma 3 Server Guide</h1>

<!-- Quick Info -->
<div class="info-box">
    <h3 style="color: #ffffff; margin-top: 0;">Quick Information</h3>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">Game:</strong> Arma 3 (Military Simulation)</li>
        <li><strong style="color: #ffffff;">Developer:</strong> Bohemia Interactive</li>
        <li><strong style="color: #ffffff;">Server Type:</strong> Dedicated Server (Windows/Linux)</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">2302 UDP</code></li>
        <li><strong style="color: #ffffff;">Max Players:</strong> Configurable (typically 64-100+)</li>
        <li><strong style="color: #ffffff;">Server Tools:</strong> BattlEye, RCON, Steam Workshop</li>
    </ul>
</div>

<p><strong>Arma 3</strong> is a realistic military tactical shooter and sandbox military simulation game developed by Bohemia Interactive. Running a dedicated server allows you to host custom missions, scenarios, and mods for your community.</p>

<h2 id="ports">🔌 Server Ports</h2>

<p>Arma 3 servers require multiple ports for different functions. <strong>All ports must be forwarded and opened in your firewall</strong> for the server to function correctly.</p>

<table class="ports-table">
    <thead>
        <tr>
            <th>Port</th>
            <th>Protocol</th>
            <th>Purpose</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><code>2302</code></td>
            <td>UDP</td>
            <td>Game Port - Primary connection port for players</td>
            <td><span class="required">REQUIRED</span></td>
        </tr>
        <tr>
            <td><code>2303</code></td>
            <td>UDP</td>
            <td>Steam Query Port - Server browser listing</td>
            <td><span class="required">REQUIRED</span></td>
        </tr>
        <tr>
            <td><code>2304</code></td>
            <td>UDP</td>
            <td>Steam Port - Steam connectivity</td>
            <td><span class="required">REQUIRED</span></td>
        </tr>
        <tr>
            <td><code>2305</code></td>
            <td>UDP</td>
            <td>VON (Voice Over Network) - In-game voice chat</td>
            <td><span class="optional">Optional</span></td>
        </tr>
        <tr>
            <td><code>2306</code></td>
            <td>UDP</td>
            <td>BattlEye Port - Anti-cheat system</td>
            <td><span class="required">REQUIRED</span></td>
        </tr>
    </tbody>
</table>

<div class="info-box">
    <h4 style="color: #ffffff; margin-top: 0;">Port Configuration Notes</h4>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li>The game port (2302) can be customized using the <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">-port=</code> startup parameter</li>
        <li>Steam Query port is automatically <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">game_port + 1</code></li>
        <li>Steam port is automatically <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">game_port + 2</code></li>
        <li>VON port is automatically <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">game_port + 3</code></li>
        <li>BattlEye port is automatically <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">game_port + 4</code></li>
        <li>If you change the game port, all related ports shift automatically</li>
    </ul>
</div>

<h3>Firewall Configuration</h3>

<p>Configure your firewall to allow Arma 3 server traffic:</p>

<h4>UFW (Ubuntu/Debian)</h4>
<pre><code># Allow Arma 3 game port range
sudo ufw allow 2302:2306/udp comment 'Arma 3 Server'

# Or for custom port (example: 2402)
sudo ufw allow 2402:2406/udp comment 'Arma 3 Custom Port'</code></pre>

<h4>FirewallD (CentOS/RHEL)</h4>
<pre><code># Add Arma 3 port range
sudo firewall-cmd --permanent --add-port=2302-2306/udp
sudo firewall-cmd --reload

# Verify rules
sudo firewall-cmd --list-ports</code></pre>

<h4>Windows Firewall</h4>
<pre><code># Open PowerShell as Administrator
New-NetFirewallRule -DisplayName "Arma 3 Server" -Direction Inbound -Protocol UDP -LocalPort 2302-2306 -Action Allow</code></pre>

<h4>iptables (Generic Linux)</h4>
<pre><code># Allow Arma 3 ports
sudo iptables -A INPUT -p udp -m udp --dport 2302:2306 -j ACCEPT
sudo iptables-save > /etc/iptables/rules.v4</code></pre>

<h2 id="installation">Installation</h2>

<h3>Getting Your Server</h3>
<p>To create an Arma 3 server:</p>
<ol>
    <li>Navigate to the <a href="/serverlist.php">Game Servers</a> page</li>
    <li>Find <strong>Arma 3</strong> in the available games list</li>
    <li>Select your preferred configuration (players, duration, location)</li>
    <li>Add to cart and complete checkout</li>
    <li>Your server will be automatically provisioned within 5-10 minutes</li>
</ol>

<h3>Server Files Structure</h3>
<p>Your Arma 3 server includes these key directories:</p>
<ul>
    <li><code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">arma3server_x64.exe</code> - Server executable (Windows)</li>
    <li><code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">./arma3server</code> - Server executable (Linux)</li>
    <li><code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">server.cfg</code> - Main configuration file</li>
    <li><code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">basic.cfg</code> - Network and performance settings</li>
    <li><code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">@mods/</code> - Mod installation directory</li>
    <li><code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">mpmissions/</code> - Mission files folder</li>
    <li><code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">BattlEye/</code> - Anti-cheat configuration</li>
</ul>

<h2 id="configuration">Configuration Files</h2>

<h3>server.cfg</h3>
<p>The main server configuration file. Key settings:</p>
<pre><code>// Server identity
hostname = "Your Arma 3 Server";
password = "";              // Join password (leave empty for public)
passwordAdmin = "adminpass"; // Admin password
serverCommandPassword = "rconpass"; // RCON password

// Server behavior
persistent = 1;             // Persistent missions
disableVoN = 0;            // Enable voice chat
vonCodecQuality = 10;      // Voice quality (0-30)
forceRotorLibSimulation = 0; // Advanced flight model

// Performance
maxPlayers = 64;
kickDuplicate = 1;
verifySignatures = 2;      // Mod signature verification
equalModRequired = 0;
allowedFilePatching = 0;

// Mission rotation
class Missions {
    class Mission1 {
        template = "yourMission.Altis";
        difficulty = "regular";
    };
};</code></pre>

<h3>basic.cfg</h3>
<p>Network and bandwidth configuration:</p>
<pre><code>// Bandwidth settings
MaxMsgSend = 256;
MaxSizeGuaranteed = 512;
MaxSizeNonguaranteed = 256;
MinBandwidth = 131072;      // 128 Kbps
MaxBandwidth = 10000000000; // Unlimited

// Client tuning
MinErrorToSend = 0.001;
MinErrorToSendNear = 0.01;
MaxCustomFileSize = 160000;</code></pre>

<h2 id="startup">⚙️ Startup Parameters</h2>

<p>Arma 3 server supports extensive command-line parameters for customization:</p>

<h3>Windows Startup Command</h3>
<pre><code>arma3server_x64.exe -port=2302 -config=server.cfg -cfg=basic.cfg -profiles=SC -name=SC -mod=@mod1;@mod2 -serverMod=@serverMod1</code></pre>

<h3>Linux Startup Command</h3>
<pre><code>./arma3server -port=2302 -config=server.cfg -cfg=basic.cfg -profiles=SC -name=SC -mod=@mod1;@mod2 -serverMod=@serverMod1</code></pre>

<h3>Essential Parameters</h3>
<table class="ports-table">
    <thead>
        <tr>
            <th>Parameter</th>
            <th>Description</th>
            <th>Example</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><code>-port=</code></td>
            <td>Game port (default: 2302)</td>
            <td><code>-port=2402</code></td>
        </tr>
        <tr>
            <td><code>-config=</code></td>
            <td>Path to server.cfg</td>
            <td><code>-config=server.cfg</code></td>
        </tr>
        <tr>
            <td><code>-cfg=</code></td>
            <td>Path to basic.cfg</td>
            <td><code>-cfg=basic.cfg</code></td>
        </tr>
        <tr>
            <td><code>-profiles=</code></td>
            <td>Profile folder path</td>
            <td><code>-profiles=SC</code></td>
        </tr>
        <tr>
            <td><code>-name=</code></td>
            <td>Profile name</td>
            <td><code>-name=SC</code></td>
        </tr>
        <tr>
            <td><code>-mod=</code></td>
            <td>Client mods (semicolon separated)</td>
            <td><code>-mod=@CBA_A3;@ACE</code></td>
        </tr>
        <tr>
            <td><code>-serverMod=</code></td>
            <td>Server-only mods</td>
            <td><code>-serverMod=@AdvRapid</code></td>
        </tr>
        <tr>
            <td><code>-world=</code></td>
            <td>Default world/terrain</td>
            <td><code>-world=Altis</code></td>
        </tr>
        <tr>
            <td><code>-loadMissionToMemory</code></td>
            <td>Loads mission to RAM</td>
            <td>Flag (no value)</td>
        </tr>
        <tr>
            <td><code>-noSound</code></td>
            <td>Disable sound (Linux)</td>
            <td>Flag (no value)</td>
        </tr>
        <tr>
            <td><code>-limitFPS=</code></td>
            <td>Limit server FPS</td>
            <td><code>-limitFPS=50</code></td>
        </tr>
        <tr>
            <td><code>-autoInit</code></td>
            <td>Auto-start mission</td>
            <td>Flag (no value)</td>
        </tr>
    </tbody>
</table>

<h3>Performance Parameters</h3>
<pre><code>// Recommended for performance
-limitFPS=50                  // Limit FPS to reduce CPU usage
-loadMissionToMemory          // Faster mission loading
-enableHT                     // Enable Hyper-Threading
-hugepages                    // Use huge memory pages (Linux)
-noSound                      // Disable sound on Linux
-world=empty                  // Start with empty world</code></pre>

<h2 id="mods">Mods & Workshop Content</h2>

<h3>Installing Mods</h3>
<p>Arma 3 supports Steam Workshop mods:</p>
<ol>
    <li>Subscribe to mods on Steam Workshop</li>
    <li>Locate mod files in:<br>
        <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">C:\Program Files (x86)\Steam\steamapps\workshop\content\107410\</code></li>
    <li>Copy mod folders to server <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">@modname</code> format</li>
    <li>Add to startup parameters: <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">-mod=@CBA_A3;@ACE;@RHSUSAF</code></li>
</ol>

<h3>Popular Mods</h3>
<ul>
    <li><strong>CBA_A3</strong> - Community Base Addons (required for many mods)</li>
    <li><strong>ACE3</strong> - Advanced Combat Environment (realism enhancement)</li>
    <li><strong>RHS</strong> - Red Hammer Studios (weapons/vehicles)</li>
    <li><strong>TFAR</strong> - Task Force Arrowhead Radio (realistic radio)</li>
    <li><strong>CUP</strong> - Community Upgrade Project (maps/units)</li>
</ul>

<div class="warning-box">
    <h4 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Mod Loading Order</h4>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Always load <strong>CBA_A3</strong> first if installed</li>
        <li>Load framework mods before content mods</li>
        <li>Use semicolons (;) to separate mod names, no spaces</li>
        <li>Mod names are case-sensitive on Linux</li>
    </ul>
</div>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Won't Start</h3>

<h4>Check Port Conflicts</h4>
<p><strong>Problem:</strong> Port 2302 already in use</p>
<p><strong>Solution:</strong></p>
<pre><code># Check if port is in use (Linux)
netstat -tulpn | grep 2302

# Check if port is in use (Windows PowerShell)
Get-NetTCPConnection -LocalPort 2302

# Use a different port
-port=2402</code></pre>

<h4>Missing Dependencies (Linux)</h4>
<p><strong>Problem:</strong> Library errors on Linux server</p>
<p><strong>Solution:</strong></p>
<pre><code># Install 32-bit libraries (Debian/Ubuntu)
sudo dpkg --add-architecture i386
sudo apt-get update
sudo apt-get install lib32gcc1 lib32stdc++6

# Install dependencies (CentOS/RHEL)
sudo yum install glibc.i686 libstdc++.i686</code></pre>

<h4>Configuration File Errors</h4>
<p><strong>Problem:</strong> Server.cfg syntax errors</p>
<p><strong>Solution:</strong></p>
<ul>
    <li>Check for missing semicolons after each setting</li>
    <li>Validate mission class names match files in <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">mpmissions/</code></li>
    <li>Remove comments with // from inside class definitions</li>
    <li>Check server logs in profiles folder for specific errors</li>
</ul>

<h3>Players Can't Connect</h3>

<h4>Firewall Blocking Connections</h4>
<p><strong>Problem:</strong> Server not visible in browser or players timeout</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify all UDP ports (2302-2306) are open in firewall</li>
    <li>Check router port forwarding if hosting at home</li>
    <li>Disable any VPN that might interfere with connections</li>
    <li>Test with <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">telnet yourip 2302</code> (should get garbage response = port open)</li>
</ul>

<h4>Mod Signature Verification Failures</h4>
<p><strong>Problem:</strong> "File <file> is not signed" errors</p>
<p><strong>Solutions:</strong></p>
<pre><code>// In server.cfg, adjust verification level:
verifySignatures = 0;  // Disable verification (not recommended)
verifySignatures = 1;  // Only v1 signature check
verifySignatures = 2;  // Full signature verification (recommended)

// Or copy .bikey files to /keys/ folder
// Keys are usually in @modName/keys/*.bikey</code></pre>

<h4>Version Mismatch</h4>
<p><strong>Problem:</strong> "Wrong version" or "Bad version" errors</p>
<p><strong>Solution:</strong></p>
<ul>
    <li>Update server using SteamCMD: <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">steamcmd +login anonymous +app_update 233780 validate +quit</code></li>
    <li>Ensure all mods are updated to latest versions</li>
    <li>Clients must match server and mod versions exactly</li>
</ul>

<h3>Performance Issues</h3>

<h4>Low Server FPS</h4>
<p><strong>Problem:</strong> Server running below 20 FPS causing lag</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Reduce AI count in missions (AI are CPU-intensive)</li>
    <li>Use <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">-limitFPS=50</code> to cap server FPS</li>
    <li>Enable <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">-enableHT</code> for hyper-threading support</li>
    <li>Optimize mission scripts (reduce frequent loops)</li>
    <li>Use server-side mods for performance (e.g., Advanced Rappelling)</li>
</ul>

<h4>High Desync</h4>
<p><strong>Problem:</strong> Players experience rubber-banding and desynchronization</p>
<p><strong>Solutions:</strong></p>
<pre><code>// Adjust basic.cfg bandwidth settings:
MaxMsgSend = 512;          // Increase if high bandwidth
MaxSizeGuaranteed = 1024;
MinBandwidth = 262144;     // 256 Kbps minimum
MaxBandwidth = 10000000000; // Remove bandwidth cap

// In server.cfg:
maxPacketSize = 1400;      // Adjust for MTU</code></pre>

<h4>Memory Issues</h4>
<p><strong>Problem:</strong> Server crashes with "Out of memory" errors</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Use 64-bit server executable (<code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">arma3server_x64.exe</code>)</li>
    <li>Add <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">-malloc=system</code> parameter (Windows)</li>
    <li>Use <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">-hugepages</code> on Linux for better memory management</li>
    <li>Reduce texture/terrain detail in missions</li>
    <li>Limit number of active mods</li>
</ul>

<h3>BattlEye Issues</h3>

<h4>BattlEye Kicks Players</h4>
<p><strong>Problem:</strong> Players kicked for script restrictions</p>
<p><strong>Solution:</strong></p>
<ul>
    <li>Check <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">BattlEye/scripts.txt</code> for kicked script hash</li>
    <li>Add exception lines to appropriate .txt files</li>
    <li>Common files to modify: <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">scripts.txt</code>, <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">publicvariable.txt</code>, <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">setvariable.txt</code></li>
    <li>Many mods come with BattlEye filters in their documentation</li>
</ul>

<h4>BattlEye Fails to Initialize</h4>
<p><strong>Problem:</strong> "BattlEye initialization failed" error</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify BattlEye port (2306 UDP) is open</li>
    <li>Check BattlEye folder permissions (needs write access)</li>
    <li>Temporarily disable antivirus and test</li>
    <li>Reinstall BattlEye files from server package</li>
</ul>

<h2 id="performance">⚡ Performance Optimization</h2>

<h3>Server Configuration</h3>
<pre><code>// Recommended basic.cfg for performance
MaxMsgSend = 512;
MaxSizeGuaranteed = 1024;
MaxSizeNonguaranteed = 400;
MinBandwidth = 262144;
MaxBandwidth = 10000000000;
MinErrorToSend = 0.003;
MinErrorToSendNear = 0.02;</code></pre>

<h3>Startup Parameters for Performance</h3>
<pre><code># Performance-optimized startup
-limitFPS=50 -enableHT -hugepages -loadMissionToMemory -noSound -world=empty</code></pre>

<h3>Mission Optimization</h3>
<ul>
    <li><strong>Reduce AI count:</strong> Each AI unit consumes significant CPU</li>
    <li><strong>Use caching:</strong> Cache AI groups when far from players</li>
    <li><strong>Optimize scripts:</strong> Avoid <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">while {true}</code> loops, use scheduled scripts</li>
    <li><strong>Limit view distance:</strong> Set reasonable viewDistance in mission</li>
    <li><strong>Clean up objects:</strong> Delete dead bodies and destroyed vehicles</li>
</ul>

<h3>Hardware Recommendations</h3>
<div class="info-box">
    <h4 style="color: #ffffff; margin-top: 0;">Recommended Server Specs</h4>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">CPU:</strong> 4+ cores, high single-thread performance (3.5 GHz+)</li>
        <li><strong style="color: #ffffff;">RAM:</strong> 8-16 GB minimum, 32 GB for large missions</li>
        <li><strong style="color: #ffffff;">Storage:</strong> SSD recommended for faster loading</li>
        <li><strong style="color: #ffffff;">Network:</strong> 100 Mbps+ with low latency</li>
        <li><strong style="color: #ffffff;">OS:</strong> Windows Server 2019+ or Linux (Ubuntu 20.04+)</li>
    </ul>
</div>

<h2 id="security">🔒 Security Best Practices</h2>

<h3>Password Protection</h3>
<pre><code>// In server.cfg
password = "join_password";        // Server join password
passwordAdmin = "secure_admin_pw";  // Admin password
serverCommandPassword = "rcon_pw";  // RCON password</code></pre>

<h3>BattlEye Configuration</h3>
<ul>
    <li>Always enable BattlEye for public servers</li>
    <li>Keep BattlEye files updated</li>
    <li>Review and update filter files regularly</li>
    <li>Monitor <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">BattlEye/bans.txt</code> for repeat offenders</li>
</ul>

<h3>Signature Verification</h3>
<pre><code>// Enforce mod signatures
verifySignatures = 2;     // Full verification
allowedFilePatching = 0;  // Disable file patching
onUnsignedData = "kick (_this select 0)";  // Kick on unsigned data</code></pre>

<h3>Admin Tools</h3>
<ul>
    <li>Use whitelist for admin slots in mission files</li>
    <li>Restrict RCON access to trusted IPs only</li>
    <li>Enable admin logging to track admin actions</li>
    <li>Consider server-side admin mods (e.g., InfiSTAR)</li>
</ul>

<h3>Regular Maintenance</h3>
<ul>
    <li>Review server logs daily for suspicious activity</li>
    <li>Keep server and mods updated to latest versions</li>
    <li>Backup configuration files and mission files regularly</li>
    <li>Monitor server resources (CPU, RAM, bandwidth)</li>
    <li>Test server changes in staging environment first</li>
</ul>

<h2 id="resources">📚 Additional Resources</h2>

<h3>Official Documentation</h3>
<ul>
    <li><a href="https://community.bistudio.com/wiki/Arma_3:_Dedicated_Server" target="_blank">Bohemia Interactive Wiki - Dedicated Server</a></li>
    <li><a href="https://community.bistudio.com/wiki/server.cfg" target="_blank">BI Wiki - server.cfg Configuration</a></li>
    <li><a href="https://community.bistudio.com/wiki/basic.cfg" target="_blank">BI Wiki - basic.cfg Configuration</a></li>
    <li><a href="https://forums.bohemia.net/forums/forum/160-arma-3-servers-administration/" target="_blank">Bohemia Forums - Server Administration</a></li>
</ul>

<h3>Community Resources</h3>
<ul>
    <li><a href="https://github.com/GameServerManagers/LinuxGSM" target="_blank">LinuxGSM - Arma 3 Server Script</a></li>
    <li><a href="https://www.reddit.com/r/armadev/" target="_blank">r/armadev - Server Development Community</a></li>
    <li><a href="https://discord.gg/arma" target="_blank">Official Arma Discord</a></li>
    <li><a href="https://steamcommunity.com/app/107410/guides/" target="_blank">Steam Community Guides</a></li>
</ul>

<h3>Server Tools & Utilities</h3>
<ul>
    <li><a href="https://community.bistudio.com/wiki/ArmA:_Startup_Parameters" target="_blank">Complete Startup Parameters List</a></li>
    <li><a href="https://github.com/CBATeam/CBA_A3" target="_blank">CBA_A3 - Community Base Addons</a></li>
    <li><a href="https://ace3.acemod.org/" target="_blank">ACE3 Mod</a></li>
    <li><a href="https://github.com/michail-nikolaev/task-force-arma-3-radio" target="_blank">TFAR - Task Force Radio</a></li>
</ul>

<h3>Performance Monitoring</h3>
<ul>
    <li><strong>Server FPS Monitoring:</strong> Use <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">diag_fps</code> command in-game</li>
    <li><strong>RPT Logs:</strong> Check <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">profiles/*.rpt</code> files for errors</li>
    <li><strong>Network Stats:</strong> Use <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">diag_log</code> for network diagnostics</li>
</ul>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Backup Regularly:</strong> Always backup server.cfg, basic.cfg, and mission files before making changes</li>
        <li><strong>Test Mods:</strong> Test new mods on a development server before deploying to production</li>
        <li><strong>Monitor Performance:</strong> Keep server FPS above 20 for acceptable gameplay</li>
        <li><strong>Update Carefully:</strong> Game updates may break mods - have rollback plan ready</li>
        <li><strong>BattlEye Required:</strong> Most public servers require BattlEye for anti-cheat protection</li>
        <li><strong>Read Logs:</strong> Server .rpt files contain valuable troubleshooting information</li>
        <li><strong>Community Support:</strong> Bohemia forums and Arma Discord are excellent support resources</li>
    </ul>
</div>

<hr style="margin: 40px 0; border: none; border-top: 1px solid rgba(255,255,255,0.1);">

<p style="text-align: center; color: rgba(255,255,255,0.6); font-size: 0.9em;">
    <strong>Documentation Version:</strong> 1.0 | <strong>Last Updated:</strong> November 10, 2025<br>
    <strong>Sources:</strong> Bohemia Interactive Wiki, LinuxGSM, Steam Community, r/armadev<br>
    <em>For additional support, visit our <a href="/docs.php?action=view&doc=common-issues">Common Issues</a> guide or contact support.</em>
</p>