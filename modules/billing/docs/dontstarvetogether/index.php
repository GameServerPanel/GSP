<?php
/**
 * Don't Starve Together Server Documentation
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
    <a href="#worlds">Worlds</a> |
    <a href="#related-mods">Mods</a>
</div>

<h1>Don't Starve Together Server Guide</h1>

<h2 id="quick-info">📋 Quick Info</h2>
<div class="info-box">
    <h3 style="color: #ffffff; margin-top: 0;">Server Specifications</h3>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">Game:</strong> Don't Starve Together (Co-op Survival)</li>
        <li><strong style="color: #ffffff;">Platform:</strong> Windows, Linux</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">10999/UDP (game) + 10998/UDP (authentication)</code></li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 64 (typical: 4-16)</li>
        <li><strong style="color: #ffffff;">Control Protocol:</strong> Console commands</li>
        <li><strong style="color: #ffffff;">Server Binary:</strong> dontstarve_dedicated_server_nullrenderer</li>
        <li><strong style="color: #ffffff;">App ID:</strong> 343050</li>
        <li><strong style="color: #ffffff;">Special Features:</strong> Seasons, caves, mods, world regeneration</li>
    </ul>
</div>

<h2 id="ports">🔌 Ports & Firewall Configuration</h2>
<p>Don't Starve Together servers require specific ports for proper operation:</p>

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
            <td>10999 (configurable)</td>
            <td>UDP</td>
            <td>Game port (server)</td>
            <td>Yes</td>
        </tr>
        <tr>
            <td>10998 (configurable)</td>
            <td>UDP</td>
            <td>Authentication/master server</td>
            <td>Yes</td>
        </tr>
        <tr>
            <td>10900 (configurable)</td>
            <td>UDP</td>
            <td>Cave/shard server (if using caves)</td>
            <td>Optional</td>
        </tr>
        <tr>
            <td>27016-27017</td>
            <td>UDP</td>
            <td>Steam master server queries</td>
            <td>Optional</td>
        </tr>
    </tbody>
</table>

<h3>Firewall Examples</h3>

<p><strong>UFW (Ubuntu/Debian):</strong></p>
<div class="code-block">
<code>sudo ufw allow 10999/udp comment 'DST Game Port'
sudo ufw allow 10998/udp comment 'DST Auth Port'
sudo ufw allow 10900/udp comment 'DST Cave Port'
sudo ufw allow 27016:27017/udp comment 'DST Steam Query'</code>
</div>

<p><strong>FirewallD (CentOS/RHEL):</strong></p>
<div class="code-block">
<code>sudo firewall-cmd --permanent --add-port=10999/udp
sudo firewall-cmd --permanent --add-port=10998/udp
sudo firewall-cmd --permanent --add-port=10900/udp
sudo firewall-cmd --permanent --add-port=27016-27017/udp
sudo firewall-cmd --reload</code>
</div>

<p><strong>iptables:</strong></p>
<div class="code-block">
<code>iptables -A INPUT -p udp --dport 10999 -j ACCEPT
iptables -A INPUT -p udp --dport 10998 -j ACCEPT
iptables -A INPUT -p udp --dport 10900 -j ACCEPT
iptables -A INPUT -p udp -m multiport --dports 27016:27017 -j ACCEPT</code>
</div>

<h2 id="installation">⚙️ Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Linux (Ubuntu 18.04+, Debian 9+, CentOS 7+) or Windows Server 2012+</li>
    <li><strong>CPU:</strong> 2+ cores @ 2.0GHz recommended</li>
    <li><strong>RAM:</strong> 1GB minimum, 2GB+ recommended (4GB+ with caves)</li>
    <li><strong>Disk:</strong> 2GB for game files, additional space for saves</li>
    <li><strong>Network:</strong> Stable connection, 5Mbps+ bandwidth</li>
</ul>

<h3>Installation via SteamCMD (Linux)</h3>
<ol>
    <li><strong>Install SteamCMD:</strong>
        <div class="code-block"><code>sudo apt-get install steamcmd  # Debian/Ubuntu
sudo yum install steamcmd      # CentOS/RHEL</code></div>
    </li>
    <li><strong>Run SteamCMD and install DST server:</strong>
        <div class="code-block"><code>steamcmd +login anonymous +force_install_dir /home/steam/dst +app_update 343050 validate +quit</code></div>
    </li>
    <li><strong>Generate Server Token:</strong> Visit <a href="https://accounts.klei.com/account/game/servers?game=DontStarveTogether" target="_blank">Klei Account Portal</a> to create a server token</li>
    <li><strong>Create cluster configuration:</strong> Navigate to <code>~/.klei/DoNotStarveTogether/</code> and create cluster folder</li>
</ol>

<h3>Installation via SteamCMD (Windows)</h3>
<ol>
    <li>Download SteamCMD from Valve's website</li>
    <li>Extract to <code>C:\steamcmd\</code></li>
    <li>Run: <code>steamcmd +login anonymous +force_install_dir C:\dst +app_update 343050 validate +quit</code></li>
    <li>Generate server token from Klei portal</li>
    <li>Create cluster in <code>%USERPROFILE%\Documents\Klei\DoNotStarveTogether\</code></li>
</ol>

<h2 id="configuration">📝 Configuration</h2>

<h3>Cluster Structure</h3>
<p>DST uses a cluster-based configuration system:</p>
<div class="code-block">
<code>~/.klei/DoNotStarveTogether/MyCluster/
├── cluster.ini           # Main cluster config
├── cluster_token.txt     # Server token from Klei
├── Master/               # Overworld shard
│   ├── server.ini
│   ├── worldgenoverride.lua
│   └── modoverrides.lua
└── Caves/                # Cave shard (optional)
    ├── server.ini
    ├── worldgenoverride.lua
    └── modoverrides.lua</code>
</div>

<h3>cluster.ini Example</h3>
<div class="code-block">
<code>[GAMEPLAY]
game_mode = survival  # survival, endless, or wilderness
max_players = 16
pvp = false
pause_when_empty = true

[NETWORK]
cluster_description = My Don't Starve Together Server
cluster_name = MyDSTServer
cluster_intention = cooperative  # cooperative, competitive, social, or madness
cluster_password =  # Leave blank for public

[MISC]
console_enabled = true
max_snapshots = 6  # Number of save backups
</code>
</div>

<h3>Master/server.ini Example (Overworld)</h3>
<div class="code-block">
<code>[NETWORK]
server_port = 10999

[SHARD]
is_master = true
name = Master
id = 1

[STEAM]
master_server_port = 27016
authentication_port = 10998
</code>
</div>

<h3>Caves/server.ini Example (Cave Shard)</h3>
<div class="code-block">
<code>[NETWORK]
server_port = 10900

[SHARD]
is_master = false
name = Caves
id = 2
master_ip = 127.0.0.1
master_port = 10999

[STEAM]
master_server_port = 27017
authentication_port = 10897
</code>
</div>

<h3>World Generation (worldgenoverride.lua)</h3>
<div class="code-block">
<code>return {
    override_enabled = true,
    preset = "SURVIVAL_TOGETHER",  -- or SURVIVAL_TOGETHER_CLASSIC, SURVIVAL_DEFAULT_PLUS
    overrides = {
        -- World Size
        world_size = "default",  -- small, medium, default, huge
        
        -- Season Settings
        autumn = "default",  -- noseason, veryshortseason, shortseason, default, longseason, verylongseason, random
        winter = "default",
        spring = "default",
        summer = "default",
        
        -- Resources
        carrots = "default",  -- never, rare, uncommon, default, often, mostly, always, insane
        berrybush = "default",
        grass = "default",
        saplings = "default",
        trees = "default",
        
        -- Mobs
        beefaloheat = "default",
        beefalo = "default",
        spiders = "default",
        hounds = "default",
        
        -- Difficulty
        season_start = "default",  -- autumn, winter, spring, summer
        day = "default",  -- default, longday, longdusk, longnight, noday, nodusk, nonight, onlyday, onlydusk, onlynight
        
        -- Boss Locations
        bearger = "default",
        deerclops = "default",
        goosemoose = "default",
        dragonfly = "default",
        
        -- Special Settings
        specialevent = "default",  -- none, default, auto
        touchstone = "default",
        
        -- Custom Settings
        task_set = "default",  -- default, classic
    }
}</code>
</div>

<h3>Mod Configuration (modoverrides.lua)</h3>
<div class="code-block">
<code>return {
    -- Example: Global Positions mod
    ["workshop-378160973"] = { enabled = true },
    
    -- Example: Simple Health Bar
    ["workshop-1207269058"] = { enabled = true },
    
    -- Mods use Workshop IDs from Steam Workshop
}</code>
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
            <td>-console</td>
            <td>Enable console commands</td>
            <td>-console</td>
        </tr>
        <tr>
            <td>-cluster</td>
            <td>Specify cluster name/path</td>
            <td>-cluster MyCluster</td>
        </tr>
        <tr>
            <td>-shard</td>
            <td>Specify which shard to run</td>
            <td>-shard Master</td>
        </tr>
        <tr>
            <td>-monitor_parent_process</td>
            <td>Monitor parent and exit if it dies</td>
            <td>-monitor_parent_process ####</td>
        </tr>
        <tr>
            <td>-persistent_storage_root</td>
            <td>Set save directory</td>
            <td>-persistent_storage_root /path/to/saves</td>
        </tr>
        <tr>
            <td>-conf_dir</td>
            <td>Configuration directory</td>
            <td>-conf_dir DoNotStarveTogether</td>
        </tr>
    </tbody>
</table>

<h3>Example Startup Command (Linux - Master/Overworld)</h3>
<div class="code-block">
<code>cd /home/steam/dst/bin
./dontstarve_dedicated_server_nullrenderer -console -cluster MyCluster -shard Master</code>
</div>

<h3>Example Startup Command (Linux - Caves)</h3>
<div class="code-block">
<code>cd /home/steam/dst/bin
./dontstarve_dedicated_server_nullrenderer -console -cluster MyCluster -shard Caves</code>
</div>

<h3>Example Startup Script (Linux - Both Shards)</h3>
<div class="code-block">
<code>#!/bin/bash
cd /home/steam/dst/bin

# Start Master (Overworld)
screen -dmS dst-master ./dontstarve_dedicated_server_nullrenderer -console -cluster MyCluster -shard Master

# Wait for master to initialize
sleep 10

# Start Caves
screen -dmS dst-caves ./dontstarve_dedicated_server_nullrenderer -console -cluster MyCluster -shard Caves</code>
</div>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not Appearing in Browser</h3>
<p><strong>Issue:</strong> Server not visible in game browser.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify <code>cluster_token.txt</code> contains valid token from Klei</li>
    <li>Check UDP ports 10999 and 10998 are open</li>
    <li>Ensure <code>cluster_intention</code> is set in cluster.ini</li>
    <li>Try direct connect using IP:PORT</li>
    <li>Check server logs in cluster folder</li>
</ul>

<h3>Cave Connection Issues</h3>
<p><strong>Issue:</strong> Players cannot travel to caves.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify both Master and Caves shards are running</li>
    <li>Ensure <code>master_ip</code> and <code>master_port</code> are correct in Caves/server.ini</li>
    <li>Check shard ports don't conflict</li>
    <li>Review both shard logs for connection errors</li>
    <li>Ensure firewall allows cave shard port (10900)</li>
</ul>

<h3>Server Crashes on Startup</h3>
<p><strong>Issue:</strong> Dedicated server crashes immediately.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Check cluster/Master/server_log.txt for errors</li>
    <li>Verify <code>cluster_token.txt</code> exists and has valid token</li>
    <li>Ensure cluster.ini syntax is correct</li>
    <li>Validate game files via SteamCMD</li>
    <li>Check worldgenoverride.lua for syntax errors</li>
</ul>

<h3>Mod Loading Issues</h3>
<p><strong>Issue:</strong> Mods not loading or causing crashes.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify mod Workshop IDs are correct in modoverrides.lua</li>
    <li>Ensure mods are server-compatible (not client-only)</li>
    <li>Check <code>dedicated_server_mods_setup.lua</code> in <code>mods/</code> folder</li>
    <li>Disable mods one at a time to identify conflicts</li>
    <li>Update mods via SteamCMD</li>
</ul>

<h3>World Generation Fails</h3>
<p><strong>Issue:</strong> Server fails to generate world.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Check worldgenoverride.lua syntax (Lua format required)</li>
    <li>Verify override settings are valid values</li>
    <li>Try removing worldgenoverride.lua for default generation</li>
    <li>Increase server RAM if generation times out</li>
    <li>Review server_log.txt for generation errors</li>
</ul>

<h3>High Memory Usage</h3>
<p><strong>Issue:</strong> Server using excessive memory.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Reduce <code>max_players</code> if server is overloaded</li>
    <li>Disable caves if not needed (saves RAM)</li>
    <li>Reduce <code>max_snapshots</code> to save disk space</li>
    <li>Check for memory leaks from mods</li>
    <li>Consider regenerating old worlds</li>
</ul>

<h2 id="game-modes">🎮 Game Modes & Features</h2>

<h3>Game Modes</h3>
<ul>
    <li><strong>Survival:</strong> Players respawn at portal, world persists on death</li>
    <li><strong>Endless:</strong> Easier mode with multiple respawns and resources</li>
    <li><strong>Wilderness:</strong> Permadeath mode, world resets on TPK (total party kill)</li>
</ul>

<h3>Core Features</h3>
<ul>
    <li><strong>Seasons:</strong> Autumn, Winter, Spring, Summer - each with unique challenges</li>
    <li><strong>Caves:</strong> Underground shard with unique resources and dangers</li>
    <li><strong>Character Selection:</strong> 20+ unique characters with different abilities</li>
    <li><strong>Crafting System:</strong> Extensive crafting and building mechanics</li>
    <li><strong>Sanity System:</strong> Mental health affects gameplay</li>
    <li><strong>Hunger/Health:</strong> Survival resource management</li>
</ul>

<h3>Boss Monsters</h3>
<ul>
    <li><strong>Deerclops:</strong> Winter boss</li>
    <li><strong>Bearger:</strong> Autumn boss</li>
    <li><strong>Moose/Goose:</strong> Spring boss</li>
    <li><strong>Dragonfly:</strong> Summer boss</li>
    <li><strong>Ancient Guardian:</strong> Cave boss</li>
    <li><strong>Bee Queen:</strong> Late game boss</li>
    <li><strong>Klaus:</strong> Winter event boss</li>
    <li><strong>Ancient Fuelweaver:</strong> Endgame boss</li>
</ul>

<h2 id="worlds">🗺️ World Types & Biomes</h2>

<h3>Overworld Biomes</h3>
<ul>
    <li><strong>Grasslands:</strong> Safe starting area with basic resources</li>
    <li><strong>Forest:</strong> Dense trees and renewable resources</li>
    <li><strong>Savanna:</strong> Beefalo herds and open spaces</li>
    <li><strong>Marsh:</strong> Tentacles and spiders, dangerous</li>
    <li><strong>Desert:</strong> Hot, limited resources, hounds</li>
    <li><strong>Rockyland:</strong> Rocky terrain with gold and flint</li>
    <li><strong>Mosaic:</strong> Mixed biome</li>
</ul>

<h3>Cave Biomes</h3>
<ul>
    <li><strong>Mushtree Forest:</strong> Underground mushroom biome</li>
    <li><strong>Rocky Plains:</strong> Cave spiders and minerals</li>
    <li><strong>Sinkhole:</strong> Entry/exit points</li>
    <li><strong>Ancient Ruins:</strong> Endgame area with Thulecite</li>
</ul>

<h2 id="related-mods">🔌 Related Mods & Plugins</h2>
<p>Popular mods for Don't Starve Together (via Steam Workshop):</p>
<ul>
    <li><strong>Global Positions (378160973):</strong> Show player positions on map</li>
    <li><strong>Simple Health Bar (1207269058):</strong> Health bars for mobs</li>
    <li><strong>Combined Status (376333686):</strong> Enhanced HUD information</li>
    <li><strong>Geometric Placement (351325790):</strong> Precise building placement</li>
    <li><strong>Wormhole Marks (362175979):</strong> Mark wormhole destinations</li>
</ul>

<h2>📚 Resources</h2>
<ul>
    <li><strong>SteamCMD:</strong> <a href="https://developer.valvesoftware.com/wiki/SteamCMD" target="_blank">https://developer.valvesoftware.com/wiki/SteamCMD</a></li>
    <li><strong>Server Token:</strong> <a href="https://accounts.klei.com/account/game/servers?game=DontStarveTogether" target="_blank">Klei Account Portal</a></li>
    <li><strong>Steam Workshop:</strong> Mods and custom content</li>
    <li><strong>DST Wiki:</strong> Game mechanics and guides</li>
    <li><strong>Klei Forums:</strong> Official community and support</li>
</ul>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Server token required:</strong> Must generate token from Klei account portal</li>
        <li>Supports up to <strong>64 players</strong> but 4-16 recommended for performance</li>
        <li><strong>Caves require separate shard</strong> running simultaneously with Master</li>
        <li>World generation can be <strong>customized extensively</strong> via worldgenoverride.lua</li>
        <li><strong>Seasons affect gameplay:</strong> Winter freezing, Summer overheating, etc.</li>
        <li>Mods use <strong>Steam Workshop IDs</strong> (workshop-#########)</li>
        <li>Server saves automatically and on shutdown</li>
        <li><strong>Regular backups recommended</strong> - world corruption possible</li>
        <li>Console commands available for admin tasks</li>
        <li>Co-op survival game with permadeath potential depending on game mode</li>
    </ul>
</div>