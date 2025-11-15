<?php
/**
 * Garry's Mod Server Documentation
 */
?>
<style>
.nav-menu {
    background: #1a1a2e;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 25px;
}
.nav-menu a {
    color: #4a9eff;
    text-decoration: none;
    margin-right: 15px;
    font-size: 14px;
}
.nav-menu a:hover {
    color: #6bb3ff;
    text-decoration: underline;
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
.code-block {
    background: #0f172a;
    padding: 15px;
    border-radius: 4px;
    margin: 15px 0;
    overflow-x: auto;
}
.code-block code {
    color: #a5b4fc;
    font-family: 'Courier New', monospace;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    background: #1a1a2e;
}
table th {
    background: #2a2a4e;
    color: #ffffff;
    padding: 12px;
    text-align: left;
    border: 1px solid #3a3a6e;
}
table td {
    padding: 10px 12px;
    border: 1px solid #3a3a6e;
    color: #e5e7eb;
}
table tr:nth-child(even) {
    background: #222244;
}
</style>

<div class="nav-menu">
    <strong style="color: #ffffff;">Quick Navigation:</strong>
    <a href="#quick-info">Quick Info</a> |
    <a href="#ports">Ports</a> |
    <a href="#installation">Installation</a> |
    <a href="#configuration">Configuration</a> |
    <a href="#parameters">Parameters</a> |
    <a href="#troubleshooting">Troubleshooting</a> |
    <a href="#game-modes">Game Modes</a> |
    <a href="#addons">Addons</a> |
    <a href="#workshop">Workshop</a>
</div>

<h1>Garry's Mod Server Guide</h1>

<h2 id="quick-info">📋 Quick Info</h2>
<div class="info-box">
    <h3 style="color: #ffffff; margin-top: 0;">Server Specifications</h3>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">Game:</strong> Garry's Mod (Sandbox Physics Game)</li>
        <li><strong style="color: #ffffff;">Platform:</strong> Source Engine</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27015/UDP</code></li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 128 (default 16-32)</li>
        <li><strong style="color: #ffffff;">Control Protocol:</strong> RCON</li>
        <li><strong style="color: #ffffff;">Server Binary:</strong> srcds_run (Linux), srcds.exe (Windows)</li>
        <li><strong style="color: #ffffff;">App ID:</strong> 4000 (game), 4020 (server)</li>
        <li><strong style="color: #ffffff;">Special Features:</strong> Sandbox building, Lua scripting, Steam Workshop, extensive addons</li>
    </ul>
</div>

<h2 id="ports">🔌 Ports & Firewall Configuration</h2>
<p>Garry's Mod servers require specific ports for proper operation:</p>

<table>
    <thead>
        <tr>
            <th>Port</th>
            <th>Protocol</th>
            <th>Purpose</th>
            <th>Required</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>27015 (configurable)</td>
            <td>UDP</td>
            <td>Game port</td>
            <td>Yes</td>
        </tr>
        <tr>
            <td>27015 (same as game port)</td>
            <td>UDP</td>
            <td>Query/RCON port</td>
            <td>Yes</td>
        </tr>
        <tr>
            <td>27005</td>
            <td>UDP</td>
            <td>Client port</td>
            <td>No</td>
        </tr>
        <tr>
            <td>26900</td>
            <td>UDP</td>
            <td>Steam master server updater</td>
            <td>No</td>
        </tr>
        <tr>
            <td>27020</td>
            <td>UDP</td>
            <td>SourceTV port (if enabled)</td>
            <td>No</td>
        </tr>
    </tbody>
</table>

<h3>Firewall Examples</h3>

<p><strong>UFW (Ubuntu/Debian):</strong></p>
<div class="code-block">
<code>sudo ufw allow 27015/udp comment 'GMod Game/Query Port'
sudo ufw allow 27020/udp comment 'GMod SourceTV'
sudo ufw allow 26900/udp comment 'Steam Master Server'</code>
</div>

<p><strong>FirewallD (CentOS/RHEL):</strong></p>
<div class="code-block">
<code>sudo firewall-cmd --permanent --add-port=27015/udp
sudo firewall-cmd --permanent --add-port=27020/udp
sudo firewall-cmd --permanent --add-port=26900/udp
sudo firewall-cmd --reload</code>
</div>

<p><strong>iptables:</strong></p>
<div class="code-block">
<code>iptables -A INPUT -p udp --dport 27015 -j ACCEPT
iptables -A INPUT -p udp --dport 27020 -j ACCEPT
iptables -A INPUT -p udp --dport 26900 -j ACCEPT</code>
</div>

<h2 id="installation">⚙️ Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Linux (Ubuntu 18.04+, Debian 9+, CentOS 7+), Windows Server 2012+</li>
    <li><strong>CPU:</strong> 2.0+ GHz processor (quad-core recommended for large servers)</li>
    <li><strong>RAM:</strong> 4GB minimum, 8GB+ recommended for addon-heavy servers</li>
    <li><strong>Disk:</strong> 20GB+ (base game + addons + Workshop content)</li>
    <li><strong>Network:</strong> Stable broadband connection (more bandwidth for Workshop downloads)</li>
</ul>

<h3>Installation via SteamCMD (Linux)</h3>
<ol>
    <li><strong>Install SteamCMD:</strong>
        <div class="code-block"><code>sudo apt update
sudo apt install lib32gcc1 steamcmd  # Debian/Ubuntu
# OR for manual install:
mkdir ~/steamcmd && cd ~/steamcmd
wget https://steamcdn-a.akamaihd.net/client/installer/steamcmd_linux.tar.gz
tar -xvzf steamcmd_linux.tar.gz</code></div>
    </li>
    <li><strong>Download Garry's Mod Server:</strong>
        <div class="code-block"><code>./steamcmd.sh
login anonymous
force_install_dir ./gmod-server
app_update 4020 validate
quit</code></div>
    </li>
    <li><strong>Install Counter-Strike: Source Content (Required):</strong>
        <div class="code-block"><code>./steamcmd.sh
login anonymous
force_install_dir ./css-content
app_update 232330 validate
quit

# Create symbolic link to CSS content
ln -s /path/to/css-content/cstrike /path/to/gmod-server/garrysmod/addons/cstrike</code></div>
    </li>
</ol>

<h3>Installation via SteamCMD (Windows)</h3>
<ol>
    <li>Download SteamCMD from <a href="https://steamcdn-a.akamaihd.net/client/installer/steamcmd.zip" target="_blank">Valve's website</a></li>
    <li>Extract to <code>C:\steamcmd\</code></li>
    <li>Run <code>steamcmd.exe</code> and execute:
        <div class="code-block"><code>login anonymous
force_install_dir C:\gmod-server
app_update 4020 validate
quit</code></div>
    </li>
    <li>Install CSS content similarly and mount it in server configuration</li>
</ol>

<h2 id="configuration">📝 Configuration</h2>

<h3>server.cfg Example</h3>
<p>Create or edit <code>garrysmod/cfg/server.cfg</code>:</p>

<div class="code-block">
<code>// Server Identity
hostname "My Awesome Garry's Mod Server"
sv_password ""                  // Leave blank for public server
sv_steamgroup ""                // Steam Group ID (optional)

// Server Rates
sv_minrate 10000
sv_maxrate 30000
sv_minupdaterate 20
sv_maxupdaterate 66
sv_mincmdrate 20
sv_maxcmdrate 66

// Server Region
sv_region 1                     // 1 = East Coast USA

// Server Visibility
sv_lan 0                        // 0 for internet, 1 for LAN only
sv_logbans 1
sv_logecho 1
sv_logfile 1
sv_log_onefile 0

// RCON (Remote Console)
rcon_password "your_secure_password"
sv_rcon_banpenalty 0
sv_rcon_maxfailures 5

// Contact & Tags
sv_contact "admin@example.com"
sv_tags "sandbox,fun,friendly"

// Gameplay Settings
sbox_maxprops 300               // Max props per player
sbox_maxragdolls 10             // Max ragdolls per player
sbox_maxvehicles 6              // Max vehicles per player
sbox_maxeffects 200             // Max effects per player
sbox_maxballoons 50             // Max balloons per player
sbox_maxlamps 20                // Max lamps per player
sbox_maxthrusters 50            // Max thrusters per player
sbox_maxwheels 50               // Max wheels per player
sbox_maxhoverballs 50           // Max hoverballs per player
sbox_maxnpcs 20                 // Max NPCs per player
sbox_maxsents 100               // Max SENTs per player
sbox_godmode 0                  // God mode (0 = off)
sbox_noclip 1                   // Allow noclip (1 = yes)
sbox_plpldamage 0               // Player vs player damage (0 = off)

// Voice Chat
sv_alltalk 1                    // 1 = everyone can hear, 0 = team only
sv_voiceenable 1

// Download Settings
sv_allowdownload 1
sv_allowupload 1
sv_downloadurl ""               // FastDL URL (highly recommended)

// Workshop Collection
host_workshop_collection "0"    // Your Steam Workshop Collection ID

// Performance
sv_loadingurl ""                // Loading screen URL (optional)
net_maxfilesize 64              // Max downloadable file size (MB)

// Server Protection
sv_pure 0                       // File consistency (usually 0 for GMod)
lua_openscript_cl 0             // Client Lua scripts (security)

// Exec ban files
exec banned_user.cfg
exec banned_ip.cfg</code>
</div>

<h3>mount.cfg Example</h3>
<p>Create <code>garrysmod/cfg/mount.cfg</code> to mount content from other Source games:</p>
<div class="code-block">
<code>// Mount CS:S content (required for most servers)
"mountcfg"
{
    "cstrike"    "C:\css-content\cstrike"
    "tf"         "C:\tf2-content\tf"
    "dod"        "C:\dod-content\dod"
}</code>
</div>

<h3>Game Mode Configuration</h3>
<p>Set game mode in startup parameters:</p>
<div class="code-block">
<code>// Sandbox (default)
+gamemode sandbox

// DarkRP
+gamemode darkrp

// Trouble in Terrorist Town (TTT)
+gamemode terrortown

// Prop Hunt
+gamemode prop_hunt</code>
</div>

<h2 id="parameters">🚀 Startup Parameters</h2>

<table>
    <thead>
        <tr>
            <th>Parameter</th>
            <th>Description</th>
            <th>Example</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>-game</td>
            <td>Game directory name</td>
            <td>-game garrysmod</td>
        </tr>
        <tr>
            <td>-console</td>
            <td>Enable console output</td>
            <td>-console</td>
        </tr>
        <tr>
            <td>-port</td>
            <td>Server port</td>
            <td>-port 27015</td>
        </tr>
        <tr>
            <td>-maxplayers</td>
            <td>Maximum players</td>
            <td>-maxplayers 32</td>
        </tr>
        <tr>
            <td>+map</td>
            <td>Starting map</td>
            <td>+map gm_flatgrass</td>
        </tr>
        <tr>
            <td>+gamemode</td>
            <td>Game mode to load</td>
            <td>+gamemode sandbox</td>
        </tr>
        <tr>
            <td>+exec</td>
            <td>Execute config file</td>
            <td>+exec server.cfg</td>
        </tr>
        <tr>
            <td>-tickrate</td>
            <td>Server tickrate</td>
            <td>-tickrate 66</td>
        </tr>
        <tr>
            <td>-ip</td>
            <td>Bind to specific IP</td>
            <td>-ip 192.168.1.100</td>
        </tr>
        <tr>
            <td>+host_workshop_collection</td>
            <td>Workshop collection ID</td>
            <td>+host_workshop_collection 123456789</td>
        </tr>
        <tr>
            <td>+sv_setsteamaccount</td>
            <td>Steam Game Server Login Token</td>
            <td>+sv_setsteamaccount YOURTOKENHERE</td>
        </tr>
    </tbody>
</table>

<h3>Example Startup Command (Linux)</h3>
<div class="code-block">
<code>./srcds_run -game garrysmod -console -port 27015 -maxplayers 32 \
  +map gm_flatgrass +gamemode sandbox +exec server.cfg \
  +host_workshop_collection YOUR_COLLECTION_ID \
  +sv_setsteamaccount YOUR_GSLT_TOKEN -tickrate 66</code>
</div>

<h3>Example Startup Command (Windows)</h3>
<div class="code-block">
<code>srcds.exe -game garrysmod -console -port 27015 -maxplayers 32 ^
  +map gm_flatgrass +gamemode sandbox +exec server.cfg ^
  +host_workshop_collection YOUR_COLLECTION_ID ^
  +sv_setsteamaccount YOUR_GSLT_TOKEN -tickrate 66</code>
</div>

<h3>Getting Steam Game Server Login Token (GSLT)</h3>
<ol>
    <li>Visit <a href="https://steamcommunity.com/dev/managegameservers" target="_blank">Steam Game Server Account Management</a></li>
    <li>Log in with your Steam account</li>
    <li>Create new token with App ID <strong>4000</strong></li>
    <li>Use token in <code>+sv_setsteamaccount</code> parameter</li>
</ol>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Missing Textures/Errors (Purple/Black Checkerboard)</h3>
<p><strong>Issue:</strong> Players see missing textures or error models.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Install Counter-Strike: Source content (required)</li>
    <li>Mount CSS content correctly in <code>mount.cfg</code></li>
    <li>Verify Workshop addons are downloaded</li>
    <li>Check FastDL is configured properly</li>
    <li>Install additional Source game content (TF2, DOD, HL2:EP2) if needed</li>
</ul>

<h3>Lua Errors on Startup</h3>
<p><strong>Issue:</strong> Server shows Lua errors in console.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Check addon compatibility with current GMod version</li>
    <li>Remove or update problematic addons</li>
    <li>Verify Lua files are not corrupted</li>
    <li>Check <code>garrysmod/lua/autorun/server/</code> for conflicting scripts</li>
    <li>Review server console logs for specific errors</li>
</ul>

<h3>Workshop Content Not Downloading</h3>
<p><strong>Issue:</strong> Workshop addons not appearing on server.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify Steam Game Server Login Token (GSLT) is valid</li>
    <li>Check Workshop Collection ID is correct</li>
    <li>Ensure <code>host_workshop_collection</code> is set in startup</li>
    <li>Use <code>resource.AddWorkshop()</code> in Lua for individual addons</li>
    <li>Check <code>cache/workshop/</code> directory permissions</li>
</ul>

<h3>High Player Ping/Lag</h3>
<p><strong>Issue:</strong> Players experiencing latency.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Reduce prop limits if server is heavily populated</li>
    <li>Optimize addon count (remove unnecessary addons)</li>
    <li>Increase server CPU allocation</li>
    <li>Set up FastDL to reduce client download times</li>
    <li>Adjust <code>sv_maxrate</code> based on bandwidth</li>
</ul>

<h3>Server Crashes</h3>
<p><strong>Issue:</strong> Server randomly crashes or restarts.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Check for conflicting addons (disable one at a time)</li>
    <li>Review crash logs in <code>garrysmod/</code> directory</li>
    <li>Ensure adequate RAM (8GB+ for large servers)</li>
    <li>Update server files with SteamCMD</li>
    <li>Remove memory-intensive addons or maps</li>
</ul>

<h3>RCON Connection Issues</h3>
<p><strong>Issue:</strong> Cannot connect via RCON.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify <code>rcon_password</code> is set in server.cfg</li>
    <li>Ensure firewall allows UDP on game port</li>
    <li>Use correct IP:PORT for RCON connection</li>
    <li>Try alternative RCON tools (SourceAdmin RCON, etc.)</li>
</ul>

<h2 id="game-modes">🎮 Popular Game Modes</h2>

<h3>Sandbox</h3>
<p>The default GMod experience. Build anything with physics props and tools.</p>
<ul>
    <li><strong>Features:</strong> Unlimited creativity, physics manipulation, prop spawning</li>
    <li><strong>Best For:</strong> Creative building, testing contraptions, relaxed gameplay</li>
</ul>

<h3>DarkRP</h3>
<p>Roleplay game mode where players take on jobs in a virtual city.</p>
<ul>
    <li><strong>Features:</strong> Jobs (police, mayor, gangster), economy, laws, guns</li>
    <li><strong>Best For:</strong> Roleplay enthusiasts, social gameplay</li>
    <li><strong>Note:</strong> Requires DarkRP gamemode addon</li>
</ul>

<h3>Trouble in Terrorist Town (TTT)</h3>
<p>Social deduction game. Traitors vs Innocents.</p>
<ul>
    <li><strong>Features:</strong> Hidden roles, detective tools, tension and paranoia</li>
    <li><strong>Best For:</strong> Group play, mystery/deduction fans</li>
    <li><strong>Players:</strong> 8+ recommended</li>
</ul>

<h3>Prop Hunt</h3>
<p>Hide-and-seek with props. Hunters vs Props.</p>
<ul>
    <li><strong>Features:</strong> Props disguise as objects, hunters seek them out</li>
    <li><strong>Best For:</strong> Casual fun, all skill levels</li>
</ul>

<h3>Murder</h3>
<p>One murderer, one detective, bystanders. Survive or solve.</p>
<ul>
    <li><strong>Features:</strong> Stealth kills, detective investigation, bystander survival</li>
    <li><strong>Best For:</strong> Tense gameplay, smaller groups</li>
</ul>

<h3>Deathrun</h3>
<p>Runners navigate deadly obstacle courses while Deaths activate traps.</p>
<ul>
    <li><strong>Features:</strong> Parkour, trap activation, teamwork</li>
    <li><strong>Best For:</strong> Skill-based challenges</li>
</ul>

<h2 id="addons">🔌 Addons & Customization</h2>

<h3>Installing Addons Manually</h3>
<ol>
    <li>Download addon files (.gma or folder structure)</li>
    <li>Place in <code>garrysmod/addons/</code> directory</li>
    <li>Restart server</li>
</ol>

<h3>Popular Server Addons</h3>
<ul>
    <li><strong>ULX/ULib:</strong> Admin mod with extensive permissions</li>
    <li><strong>DarkRP Mods:</strong> Custom jobs, money printers, weapons</li>
    <li><strong>Pointshop:</strong> Player shop system for cosmetics</li>
    <li><strong>PAC3:</strong> Advanced player customization</li>
    <li><strong>AdvDupe2:</strong> Save and load contraptions</li>
    <li><strong>Wire Mod:</strong> Advanced contraption building</li>
    <li><strong>ACF:</strong> Armored Combat Framework (vehicles and weapons)</li>
</ul>

<h3>Lua Scripting</h3>
<p>GMod servers use Lua for custom functionality:</p>
<div class="code-block">
<code>-- Example: garrysmod/lua/autorun/server/welcome.lua
hook.Add("PlayerInitialSpawn", "WelcomeMessage", function(ply)
    ply:ChatPrint("Welcome to the server, " .. ply:Nick() .. "!")
end)</code>
</div>

<h2 id="workshop">🛠️ Steam Workshop Integration</h2>

<h3>Creating Workshop Collection</h3>
<ol>
    <li>Open Garry's Mod and go to Main Menu</li>
    <li>Click "Workshop" → "Collections"</li>
    <li>Create new collection and add desired addons</li>
    <li>Publish collection and note the Collection ID</li>
    <li>Use ID in <code>host_workshop_collection</code> parameter</li>
</ol>

<h3>Adding Individual Workshop Items (Lua)</h3>
<p>In <code>garrysmod/lua/autorun/server/workshop.lua</code>:</p>
<div class="code-block">
<code>-- Add individual Workshop items
resource.AddWorkshop("123456789")  -- Replace with Workshop ID
resource.AddWorkshop("987654321")
resource.AddWorkshop("555555555")</code>
</div>

<h3>FastDL Configuration</h3>
<p>Recommended for faster downloads of custom content:</p>
<ol>
    <li>Set up web server or use hosting service</li>
    <li>Compress and upload custom content (maps, materials, models, sounds)</li>
    <li>Set <code>sv_downloadurl "http://your-fastdl-url/"</code> in server.cfg</li>
    <li>File structure on FastDL must mirror server structure</li>
</ol>

<h2>👤 Admin Commands</h2>

<h3>Basic Console Commands</h3>
<div class="code-block">
<code>status                  # Show players and server info
kick [name/userid]      # Kick player
banid [minutes] [userid]# Ban player
addip [minutes] [ip]    # Ban IP address

changelevel [map]       # Change map immediately
map [map]               # Load specific map

ulx ban [player] [time] # ULX ban command
ulx kick [player]       # ULX kick command
ulx slay [player]       # Kill player</code>
</div>

<h3>Sandbox Commands</h3>
<div class="code-block">
<code>sbox_maxprops [number]      # Set prop limit
sbox_godmode [0/1]          # Toggle god mode
sbox_noclip [0/1]           # Toggle noclip
cleanup                     # Remove all props</code>
</div>

<h2>📚 Resources</h2>
<ul>
    <li><strong>Official Website:</strong> <a href="https://gmod.facepunch.com/" target="_blank">gmod.facepunch.com</a></li>
    <li><strong>Steam Workshop:</strong> Thousands of addons and maps</li>
    <li><strong>GMod Wiki:</strong> <a href="https://wiki.facepunch.com/gmod/" target="_blank">Lua API Documentation</a></li>
    <li><strong>Forums:</strong> Facepunch Studios forums</li>
    <li><strong>ULX:</strong> <a href="https://ulyssesmod.net/" target="_blank">ulyssesmod.net</a></li>
</ul>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Garry's Mod requires <strong>Counter-Strike: Source content</strong> (missing textures without it)</li>
        <li><strong>Steam Game Server Login Token (GSLT) required</strong> for Workshop integration</li>
        <li>Supports up to <strong>128 players</strong> but 16-32 is typical for performance</li>
        <li><strong>Extensive addon ecosystem</strong> - Workshop has 500,000+ items</li>
        <li><strong>Lua scripting</strong> allows virtually unlimited customization</li>
        <li>Popular game modes: <strong>Sandbox, DarkRP, TTT, Prop Hunt, Murder, Deathrun</strong></li>
        <li><strong>FastDL highly recommended</strong> for servers with custom content</li>
        <li><strong>ULX/ULib</strong> most popular admin mod for permissions and management</li>
        <li>Server performance depends heavily on <strong>addon count and complexity</strong></li>
        <li>The possibilities are truly limitless!</li>
    </ul>
</div>