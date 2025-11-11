<?php
/**
 * 7 Days to Die Dedicated Server - Comprehensive Hosting Guide
 * General game server hosting information (not platform-specific)
 */
?>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">📚 Navigation</h3>
    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
        <a href="#overview" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Overview</a>
        <a href="#ports" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">🔌 Ports</a>
        <a href="#installation" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Installation</a>
        <a href="#configuration" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">⚙️ Configuration</a>
        <a href="#xml-modding" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">XML Modding</a>
        <a href="#troubleshooting" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">🔧 Troubleshooting</a>
        <a href="#performance" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Performance</a>
    </div>
</div>

<h1>7 Days to Die Dedicated Server Hosting Guide</h1>

<h2 id="overview">Overview</h2>
<p><strong>7 Days to Die</strong> is a survival horror game combining first-person shooter, base building, crafting, and role-playing elements in a post-apocalyptic zombie-infested world. Players must survive by scavenging, building bases, and defending against zombie hordes that grow stronger every seven days.</p>

<h2 id="quick-info">Quick Info</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <ul style="color: #e5e7eb; line-height: 1.8; margin: 0;">
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">26900</code> (UDP)</li>
        <li><strong style="color: #ffffff;">Web Control Panel:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">8080-8090</code> (TCP, configurable)</li>
        <li><strong style="color: #ffffff;">Telnet Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">8081</code> (TCP, optional)</li>
        <li><strong style="color: #ffffff;">Minimum RAM:</strong> 4GB (Recommended: 12-16GB)</li>
        <li><strong style="color: #ffffff;">Recommended CPU:</strong> 4+ cores @ 3.0GHz+</li>
        <li><strong style="color: #ffffff;">Storage:</strong> 15GB+ (growing with world size)</li>
        <li><strong style="color: #ffffff;">Max Players:</strong> Configurable (8-32 typical)</li>
        <li><strong style="color: #ffffff;">SteamCMD App ID:</strong> 294420</li>
        <li><strong style="color: #ffffff;">Config Files:</strong> serverconfig.xml</li>
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
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">26900</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Primary game port (player connections)</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #7c2d12; padding: 4px 8px; border-radius: 3px; color: #fed7aa;">✓ Yes</span></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">26901-26902</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Additional game ports (automatic +1, +2)</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #7c2d12; padding: 4px 8px; border-radius: 3px; color: #fed7aa;">✓ Yes</span></td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">8080-8090</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #1e40af; padding: 4px 8px; border-radius: 3px; color: #dbeafe;">TCP</span></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Web control panel (configurable)</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #713f12; padding: 4px 8px; border-radius: 3px; color: #fef3c7;">Optional</span></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">8081</code></td>
            <td style="padding: 12px;"><span style="background: #1e40af; padding: 4px 8px; border-radius: 3px; color: #dbeafe;">TCP</span></td>
            <td style="padding: 12px;">Telnet remote console</td>
            <td style="padding: 12px;"><span style="background: #713f12; padding: 4px 8px; border-radius: 3px; color: #fef3c7;">Optional</span></td>
        </tr>
    </tbody>
</table>

<div style="background: #7c2d12; padding: 15px; border-left: 4px solid #ea580c; margin: 20px 0; border-radius: 4px;">
    <p style="color: #fed7aa; margin: 0;"><strong>Important:</strong> 7 Days to Die automatically uses three consecutive UDP ports starting from your configured game port. If you set port 26900, the server will also use 26901 and 26902. Always open a range of at least 3 consecutive ports.</p>
</div>

<h3>Firewall Configuration Examples</h3>

<h4>UFW (Ubuntu/Debian)</h4>
<pre><code>sudo ufw allow 26900:26902/udp comment '7DTD game ports'
sudo ufw allow 8080/tcp comment '7DTD web panel'
sudo ufw allow 8081/tcp comment '7DTD telnet'
sudo ufw reload
</code></pre>

<h4>FirewallD (CentOS/RHEL/Fedora)</h4>
<pre><code>sudo firewall-cmd --permanent --add-port=26900-26902/udp
sudo firewall-cmd --permanent --add-port=8080/tcp
sudo firewall-cmd --permanent --add-port=8081/tcp
sudo firewall-cmd --reload
</code></pre>

<h4>Windows Firewall</h4>
<pre><code># Run in PowerShell as Administrator
New-NetFirewallRule -DisplayName "7 Days to Die Game" -Direction Inbound -Protocol UDP -LocalPort 26900-26902 -Action Allow
New-NetFirewallRule -DisplayName "7 Days to Die Web Panel" -Direction Inbound -Protocol TCP -LocalPort 8080,8081 -Action Allow
</code></pre>

<h4>iptables (Legacy Linux)</h4>
<pre><code>sudo iptables -A INPUT -p udp --dport 26900:26902 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 8080 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 8081 -j ACCEPT
sudo service iptables save
</code></pre>

<h2 id="installation">Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Windows Server 2016+ or Linux 64-bit (Ubuntu/Debian recommended)</li>
    <li><strong>CPU:</strong> Minimum 4 cores @ 3.0GHz; Recommended 6-8 cores @ 3.5GHz+</li>
    <li><strong>RAM:</strong> 4GB minimum, 12-16GB recommended for 16+ players</li>
    <li><strong>Storage:</strong> 15GB+ for game files; world size grows over time (SSD recommended)</li>
    <li><strong>Network:</strong> 5Mbps minimum upload; 20Mbps+ for larger servers</li>
</ul>

<h3>Installing via SteamCMD (Linux)</h3>
<pre><code># Install SteamCMD
sudo apt update
sudo apt install steamcmd

# Create server directory
mkdir -p ~/7dtd-server
cd ~/7dtd-server

# Download server files
steamcmd +login anonymous +force_install_dir ~/7dtd-server +app_update 294420 validate +exit
</code></pre>

<h3>Installing via Steam (Windows)</h3>
<pre><code>1. Open Steam and go to Library
2. Enable "Tools" in the dropdown menu
3. Find "7 Days to Die Dedicated Server"
4. Install to your preferred directory
5. Navigate to installation folder (typically: C:\Program Files (x86)\Steam\steamapps\common\7 Days to Die Dedicated Server\)
</code></pre>

<h2 id="configuration">Server Configuration</h2>

<h3>serverconfig.xml</h3>
<p>The main configuration file is <code>serverconfig.xml</code> located in the server root directory. Key settings:</p>

<pre><code>&lt;?xml version="1.0"?&gt;
&lt;ServerSettings&gt;
    &lt;!-- Server Settings --&gt;
    &lt;property name="ServerName"                value="My 7DTD Server"/&gt;
    &lt;property name="ServerDescription"         value="Survival Server"/&gt;
    &lt;property name="ServerWebsiteURL"          value=""/&gt;
    &lt;property name="ServerPassword"            value=""/&gt;
    &lt;property name="ServerLoginConfirmationText" value=""/&gt;
    
    &lt;!-- Networking --&gt;
    &lt;property name="ServerPort"                value="26900"/&gt;
    &lt;property name="ServerVisibility"          value="2"/&gt; &lt;!-- 0=Not Listed, 1=Friends, 2=Public --&gt;
    &lt;property name="ServerDisabledNetworkProtocols" value="SteamNetworking"/&gt;
    
    &lt;!-- Slots --&gt;
    &lt;property name="ServerMaxPlayerCount"      value="8"/&gt;
    &lt;property name="ServerReservedSlots"       value="0"/&gt;
    &lt;property name="ServerReservedSlotsPermission" value="100"/&gt;
    
    &lt;!-- Admin --&gt;
    &lt;property name="ServerAdminSlots"          value="0"/&gt;
    &lt;property name="ServerAdminSlotsPermission" value="0"/&gt;
    
    &lt;!-- Web Dashboard --&gt;
    &lt;property name="ControlPanelEnabled"       value="true"/&gt;
    &lt;property name="ControlPanelPort"          value="8080"/&gt;
    &lt;property name="ControlPanelPassword"      value="CHANGEME"/&gt;
    
    &lt;!-- Telnet --&gt;
    &lt;property name="TelnetEnabled"             value="true"/&gt;
    &lt;property name="TelnetPort"                value="8081"/&gt;
    &lt;property name="TelnetPassword"            value="CHANGEME"/&gt;
    
    &lt;!-- World --&gt;
    &lt;property name="GameWorld"                 value="Navezgane"/&gt; &lt;!-- or "RWG" for random --&gt;
    &lt;property name="WorldGenSeed"              value="asdf"/&gt;
    &lt;property name="WorldGenSize"              value="4096"/&gt;
    &lt;property name="GameName"                  value="My Game"/&gt;
    &lt;property name="GameMode"                  value="GameModeSurvival"/&gt;
    
    &lt;!-- Difficulty --&gt;
    &lt;property name="GameDifficulty"            value="2"/&gt; &lt;!-- 0=Scavenger, 1=Adventurer, 2=Nomad, 3=Warrior, 4=Survivalist, 5=Insane --&gt;
    &lt;property name="ZombiesRun"                value="0"/&gt; &lt;!-- 0=Walk, 1=Jog, 2=Run --&gt;
    &lt;property name="BuildCreate"               value="false"/&gt;
    &lt;property name="DayNightLength"            value="60"/&gt; &lt;!-- Real-time minutes --&gt;
    &lt;property name="DayLightLength"            value="18"/&gt;
    
    &lt;!-- Loot & XP --&gt;
    &lt;property name="LootAbundance"             value="100"/&gt;
    &lt;property name="LootRespawnDays"           value="7"/&gt;
    &lt;property name="PlayerKillingMode"         value="3"/&gt; &lt;!-- 0=No Killing, 1=Kill Allies Only, 2=Kill Strangers Only, 3=Kill Everyone --&gt;
&lt;/ServerSettings&gt;
</code></pre>

<h3>Starting the Server</h3>

<h4>Windows</h4>
<pre><code># Navigate to server directory
cd "C:\7DaysToDie\"

# Run dedicated server
startdedicated.bat

# Or with custom config
7DaysToDieServer.exe -configfile=serverconfig.xml
</code></pre>

<h4>Linux</h4>
<pre><code># Make start script executable
chmod +x startserver.sh

# Run in screen session
screen -S 7dtd ./startserver.sh

# Detach: Ctrl+A, D
# Reattach: screen -r 7dtd

# Or direct command
./7DaysToDieServer.x86_64 -configfile=serverconfig.xml
</code></pre>

<h2 id="xml-modding">XML Modding</h2>

<h3>Understanding XML Modding</h3>
<p>7 Days to Die uses XML files for game configuration, making modding highly accessible without programming knowledge. All game items, blocks, recipes, and mechanics are defined in XML.</p>

<h4>Config Files Location</h4>
<ul>
    <li><strong>Vanilla Configs:</strong> <code>Data/Config/</code></li>
    <li><strong>Mod Overrides:</strong> <code>Mods/ModName/Config/</code></li>
</ul>

<h4>Common XML Files</h4>
<ul>
    <li><code>items.xml</code> - Item definitions and properties</li>
    <li><code>blocks.xml</code> - Block types and behaviors</li>
    <li><code>recipes.xml</code> - Crafting recipes</li>
    <li><code>loot.xml</code> - Loot tables and containers</li>
    <li><code>entityclasses.xml</code> - Zombie and animal stats</li>
    <li><code>progression.xml</code> - Skill trees and perks</li>
</ul>

<h4>Example: Increase Stack Sizes</h4>
<pre><code>&lt;configs&gt;
    &lt;set xpath="/items/item[@name='resourceWood']/@Stacknumber"&gt;10000&lt;/set&gt;
    &lt;set xpath="/items/item[@name='resourceIron']/@Stacknumber"&gt;10000&lt;/set&gt;
    &lt;set xpath="/items/item[@name='resourceConcreteMix']/@Stacknumber"&gt;10000&lt;/set&gt;
&lt;/configs&gt;
</code></pre>

<h4>Example: Modify Zombie Health</h4>
<pre><code>&lt;configs&gt;
    &lt;set xpath="/entity_classes/entity_class[@name='zombieSteve']/property[@name='Health']/@value"&gt;500&lt;/set&gt;
    &lt;set xpath="/entity_classes/entity_class[@name='zombieFeral']/property[@name='Health']/@value"&gt;1000&lt;/set&gt;
&lt;/configs&gt;
</code></pre>

<h3>Installing Mods</h3>
<ol>
    <li>Download mod from 7daystodiemods.com or NexusMods</li>
    <li>Extract to <code>Mods/</code> folder in server directory</li>
    <li>Restart server - mods load automatically</li>
    <li>Check <code>output_log.txt</code> for errors</li>
</ol>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not Showing in Browser</h3>
<pre><code># Check ServerVisibility in serverconfig.xml
&lt;property name="ServerVisibility" value="2"/&gt; &lt;!-- Must be 2 for public --&gt;

# Verify ports are open
netstat -an | grep 26900

# Check firewall rules
sudo ufw status
</code></pre>

<h3>High RAM Usage</h3>
<pre><code># Reduce world size
&lt;property name="WorldGenSize" value="4096"/&gt; &lt;!-- Try 2048 or 3072 --&gt;

# Reduce max zombies
&lt;property name="MaxSpawnedZombies" value="60"/&gt; &lt;!-- Lower if needed --&gt;

# Enable/check automatic restarts
# Restart every 24-48 hours to clear memory
</code></pre>

<h3>Connection Timeout</h3>
<ul>
    <li>Verify all three UDP ports (26900-26902) are open</li>
    <li>Check router port forwarding if self-hosting</li>
    <li>Disable Steam networking protocol if having issues</li>
    <li>Try direct IP connection instead of server browser</li>
</ul>

<h3>XML Parsing Errors</h3>
<pre><code># Check output_log.txt for errors
tail -f output_log.txt

# Validate XML syntax (all tags must close)
# Use XML validator tool online

# Remove recently added mods one-by-one
# Common issue: duplicate entries or syntax errors
</code></pre>

<h2 id="performance">Performance Optimization</h2>

<h3>Server Performance Settings</h3>
<pre><code>&lt;!-- Reduce view distance --&gt;
&lt;property name="ServerMaxWorldTransferSpeedKiBs" value="512"/&gt;

&lt;!-- Lower max zombies --&gt;
&lt;property name="MaxSpawnedZombies" value="60"/&gt;

&lt;!-- Reduce AI updates --&gt;
&lt;property name="EnemySpawnMode" value="true"/&gt;

&lt;!-- Disable dynamic mesh --&gt;
&lt;property name="EACEnabled" value="true"/&gt; &lt;!-- Can impact perf if disabled --&gt;
</code></pre>

<h3>Hardware Recommendations by Player Count</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Players</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">RAM</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">CPU Cores</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">1-4</td>
            <td style="padding: 12px;">4-6GB</td>
            <td style="padding: 12px;">2-4</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">5-8</td>
            <td style="padding: 12px;">8-12GB</td>
            <td style="padding: 12px;">4-6</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">9-16</td>
            <td style="padding: 12px;">12-16GB</td>
            <td style="padding: 12px;">6-8</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">17-32</td>
            <td style="padding: 12px;">16-24GB</td>
            <td style="padding: 12px;">8+</td>
        </tr>
    </tbody>
</table>

<h3>World Generation Tips</h3>
<ul>
    <li><strong>Navezgane:</strong> Fixed map, better performance, 16km²</li>
    <li><strong>Random Gen (RWG):</strong> Procedural, larger, higher RAM usage</li>
    <li>Smaller world sizes (2048-4096) reduce RAM and CPU load</li>
    <li>Pre-generate worlds offline before deploying to production server</li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-lightbulb" style="color: #fbbf24; margin-right: 8px;"></i>Pro Tips</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Backup regularly:</strong> World corruption can occur - automated backups every 6-12 hours</li>
        <li><strong>Restart schedule:</strong> Auto-restart every 24-48 hours clears memory leaks</li>
        <li><strong>Update carefully:</strong> Major updates can break worlds - test on backup first</li>
        <li><strong>Monitor logs:</strong> <code>output_log.txt</code> shows errors and performance issues</li>
        <li><strong>Web panel security:</strong> Change default passwords and restrict IP access</li>
    </ul>
</div>

<h2>Resources</h2>
<ul>
    <li><a href="https://7daystodie.com/" target="_blank">Official Website</a></li>
    <li><a href="https://community.7daystodie.com/" target="_blank">Official Forums</a></li>
    <li><a href="https://7daystodiemods.com/" target="_blank">7 Days to Die Mods</a></li>
    <li><a href="https://www.nexusmods.com/7daystodie" target="_blank">NexusMods - 7DTD</a></li>
    <li><a href="https://7dtd.illy.bz/" target="_blank">7DTD Server Manager Tools</a></li>
</ul>