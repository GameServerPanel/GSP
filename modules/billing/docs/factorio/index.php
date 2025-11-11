<?php
/**
 * Factorio Server Documentation
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
    <a href="#mods">Mods</a> |
    <a href="#admin">Admin</a>
</div>

<h1>Factorio Server Guide</h1>

<h2 id="quick-info">📋 Quick Info</h2>
<div class="info-box">
    <h3 style="color: #ffffff; margin-top: 0;">Server Specifications</h3>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">Game:</strong> Factorio (Automation/Factory Building)</li>
        <li><strong style="color: #ffffff;">Platform:</strong> Windows, Linux, macOS</li>
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">34197/UDP</code></li>
        <li><strong style="color: #ffffff;">Max Players:</strong> Unlimited (practical: 10-50 depending on hardware)</li>
        <li><strong style="color: #ffffff;">Control Protocol:</strong> RCON</li>
        <li><strong style="color: #ffffff;">Server Binary:</strong> factorio (headless)</li>
        <li><strong style="color: #ffffff;">Special Features:</strong> Mods, blueprints, trains, logistics, multiplayer scenarios</li>
    </ul>
</div>

<h2 id="ports">🔌 Ports & Firewall Configuration</h2>
<p>Factorio servers require specific ports for proper operation:</p>

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
            <td>34197 (configurable)</td>
            <td>UDP</td>
            <td>Game port</td>
            <td>Yes</td>
        </tr>
        <tr>
            <td>27015</td>
            <td>TCP</td>
            <td>RCON (if enabled)</td>
            <td>Optional</td>
        </tr>
    </tbody>
</table>

<h3>Firewall Examples</h3>

<p><strong>UFW (Ubuntu/Debian):</strong></p>
<div class="code-block">
<code>sudo ufw allow 34197/udp comment 'Factorio Game Port'
sudo ufw allow 27015/tcp comment 'Factorio RCON'</code>
</div>

<p><strong>FirewallD (CentOS/RHEL):</strong></p>
<div class="code-block">
<code>sudo firewall-cmd --permanent --add-port=34197/udp
sudo firewall-cmd --permanent --add-port=27015/tcp
sudo firewall-cmd --reload</code>
</div>

<p><strong>iptables:</strong></p>
<div class="code-block">
<code>iptables -A INPUT -p udp --dport 34197 -j ACCEPT
iptables -A INPUT -p tcp --dport 27015 -j ACCEPT</code>
</div>

<h2 id="installation">⚙️ Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Linux (Ubuntu 18.04+, Debian 9+, CentOS 7+), Windows Server 2012+</li>
    <li><strong>CPU:</strong> 2+ cores @ 3.0GHz recommended (single-threaded performance critical)</li>
    <li><strong>RAM:</strong> 2GB minimum, 4GB+ recommended, 8GB+ for large multiplayer games</li>
    <li><strong>Disk:</strong> 2GB for base game, additional space for saves and mods</li>
    <li><strong>Network:</strong> Stable connection, 1Mbps+ per player</li>
</ul>

<h3>Installation (Linux - Manual Download)</h3>
<ol>
    <li><strong>Download Factorio headless server:</strong>
        <div class="code-block"><code>cd /home/factorio
wget -O factorio.tar.xz https://www.factorio.com/get-download/latest/headless/linux64
tar -xf factorio.tar.xz</code></div>
    </li>
    <li><strong>Create data directory:</strong>
        <div class="code-block"><code>mkdir -p ~/.factorio
cd factorio</code></div>
    </li>
    <li><strong>Generate new map (optional):</strong>
        <div class="code-block"><code>./bin/x64/factorio --create my-save</code></div>
    </li>
</ol>

<h3>Installation (Windows)</h3>
<ol>
    <li>Download headless server from <a href="https://www.factorio.com/download" target="_blank">factorio.com/download</a></li>
    <li>Extract to <code>C:\factorio\</code></li>
    <li>Run <code>factorio.exe --create my-save</code> to generate map</li>
</ol>

<h2 id="configuration">📝 Configuration</h2>

<h3>Directory Structure</h3>
<div class="code-block">
<code>~/.factorio/                # Config directory (Linux)
%APPDATA%\Factorio\         # Config directory (Windows)
├── saves/                   # Save games
├── mods/                    # Installed mods
├── config/
│   ├── server-settings.json
│   ├── server-whitelist.json
│   ├── server-banlist.json
│   └── server-adminlist.json
└── script-output/           # Script output files</code>
</div>

<h3>server-settings.json Example</h3>
<div class="code-block">
<code>{
  "name": "My Factorio Server",
  "description": "Vanilla cooperative factory building",
  "tags": ["game", "tags"],
  
  "_comment_max_players": "Maximum number of players allowed",
  "max_players": 0,
  
  "_comment_visibility": "public: Game will be published on the official Factorio matching server",
  "visibility": {
    "public": true,
    "lan": true
  },
  
  "username": "",
  "password": "",
  "token": "",
  "game_password": "",
  
  "require_user_verification": true,
  "max_upload_in_kilobytes_per_second": 0,
  "max_upload_slots": 5,
  
  "minimum_latency_in_ticks": 0,
  "ignore_player_limit_for_returning_players": false,
  "allow_commands": "admins-only",
  
  "autosave_interval": 10,
  "autosave_slots": 5,
  "afk_autokick_interval": 0,
  
  "auto_pause": true,
  "only_admins_can_pause_the_game": true,
  
  "autosave_only_on_server": true,
  "non_blocking_saving": false,
  
  "_comment_admins": "List of case insensitive usernames, that will be admins on the server",
  "admins": []
}</code>
</div>

<h3>server-whitelist.json Example</h3>
<div class="code-block">
<code>[
  "player1",
  "player2",
  "player3"
]</code>
</div>

<h3>server-adminlist.json Example</h3>
<div class="code-block">
<code>[
  "admin1",
  "admin2"
]</code>
</div>

<h3>Map Generation Settings (map-gen-settings.json)</h3>
<div class="code-block">
<code>{
  "terrain_segmentation": "normal",
  "water": "normal",
  "width": 0,
  "height": 0,
  "starting_area": "normal",
  "peaceful_mode": false,
  "autoplace_controls": {
    "coal": {
      "frequency": "normal",
      "size": "normal",
      "richness": "normal"
    },
    "iron-ore": { "frequency": "normal", "size": "normal", "richness": "normal" },
    "copper-ore": { "frequency": "normal", "size": "normal", "richness": "normal" },
    "stone": { "frequency": "normal", "size": "normal", "richness": "normal" },
    "crude-oil": { "frequency": "normal", "size": "normal", "richness": "normal" },
    "uranium-ore": { "frequency": "normal", "size": "normal", "richness": "normal" },
    "trees": { "frequency": "normal", "size": "normal", "richness": "normal" },
    "enemy-base": { "frequency": "normal", "size": "normal", "richness": "normal" }
  },
  "cliff_settings": {
    "name": "cliff",
    "cliff_elevation_0": 10,
    "cliff_elevation_interval": 40,
    "richness": "normal"
  },
  "property_expression_names": {},
  "starting_points": [ { "x": 0, "y": 0 } ],
  "seed": null
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
            <td>--start-server</td>
            <td>Start multiplayer server with save file</td>
            <td>--start-server my-save</td>
        </tr>
        <tr>
            <td>--create</td>
            <td>Create new map/save</td>
            <td>--create my-save</td>
        </tr>
        <tr>
            <td>--port</td>
            <td>Network port to use</td>
            <td>--port 34197</td>
        </tr>
        <tr>
            <td>--bind</td>
            <td>IP address to bind to</td>
            <td>--bind 0.0.0.0</td>
        </tr>
        <tr>
            <td>--server-settings</td>
            <td>Path to server settings JSON</td>
            <td>--server-settings settings.json</td>
        </tr>
        <tr>
            <td>--server-whitelist</td>
            <td>Path to whitelist JSON</td>
            <td>--server-whitelist whitelist.json</td>
        </tr>
        <tr>
            <td>--server-adminlist</td>
            <td>Path to admin list JSON</td>
            <td>--server-adminlist adminlist.json</td>
        </tr>
        <tr>
            <td>--rcon-port</td>
            <td>RCON port (requires password)</td>
            <td>--rcon-port 27015</td>
        </tr>
        <tr>
            <td>--rcon-password</td>
            <td>RCON password</td>
            <td>--rcon-password secret</td>
        </tr>
        <tr>
            <td>--map-gen-settings</td>
            <td>Map generation settings JSON</td>
            <td>--map-gen-settings mapgen.json</td>
        </tr>
        <tr>
            <td>--console-log</td>
            <td>Path to write console log</td>
            <td>--console-log server.log</td>
        </tr>
    </tbody>
</table>

<h3>Example Startup Command (Linux)</h3>
<div class="code-block">
<code>./bin/x64/factorio --start-server my-save --port 34197 --server-settings server-settings.json</code>
</div>

<h3>Example Startup Command (Windows)</h3>
<div class="code-block">
<code>factorio.exe --start-server my-save --port 34197 --server-settings server-settings.json</code>
</div>

<h3>Example Startup Script (Linux with RCON)</h3>
<div class="code-block">
<code>#!/bin/bash
cd /home/factorio/factorio
./bin/x64/factorio \
  --start-server saves/my-save.zip \
  --port 34197 \
  --server-settings config/server-settings.json \
  --server-adminlist config/server-adminlist.json \
  --rcon-port 27015 \
  --rcon-password "your_secure_password" \
  --console-log /var/log/factorio/server.log</code>
</div>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not Appearing in Browser</h3>
<p><strong>Issue:</strong> Server not visible in public game list.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify UDP port 34197 is open in firewall</li>
    <li>Ensure <code>"visibility": {"public": true}</code> in server-settings.json</li>
    <li>Check that <code>username</code> and <code>token</code> are set (for public listing)</li>
    <li>Try direct connect using IP:PORT</li>
    <li>Review server console logs for matching server errors</li>
</ul>

<h3>Connection Issues</h3>
<p><strong>Issue:</strong> Players cannot connect or timeout.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Verify firewall allows UDP traffic on game port</li>
    <li>Check <code>game_password</code> if server is password protected</li>
    <li>Ensure server and clients are same Factorio version</li>
    <li>Verify <code>max_players</code> is not exceeded (0 = unlimited)</li>
    <li>Check whitelist if <code>server-whitelist.json</code> is used</li>
</ul>

<h3>High Latency/Desync Issues</h3>
<p><strong>Issue:</strong> Players experiencing lag or desyncs.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Increase <code>minimum_latency_in_ticks</code> for high-latency players</li>
    <li>Reduce <code>max_upload_slots</code> if bandwidth limited</li>
    <li>Enable <code>non_blocking_saving</code> to prevent save-related lag spikes</li>
    <li>Check server CPU usage (Factorio is CPU-intensive)</li>
    <li>Remove mods that cause performance issues</li>
</ul>

<h3>Save File Corruption</h3>
<p><strong>Issue:</strong> Save file won't load or crashes.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Try loading older autosave (<code>autosave_slots</code> keeps multiple backups)</li>
    <li>Use <code>--check-save</code> parameter to validate save integrity</li>
    <li>Disable problematic mods one at a time</li>
    <li>Regular backups of <code>saves/</code> directory recommended</li>
</ul>

<h3>Mod Loading Issues</h3>
<p><strong>Issue:</strong> Mods not loading or causing crashes.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Ensure all players have same mod versions</li>
    <li>Check <code>mods/</code> directory for corrupted downloads</li>
    <li>Verify mod compatibility with Factorio version</li>
    <li>Use <code>mod-list.json</code> to enable/disable mods</li>
    <li>Check factorio-current.log for mod errors</li>
</ul>

<h3>Server Performance Issues</h3>
<p><strong>Issue:</strong> Server running slowly with many players.</p>
<p><strong>Solutions:</strong></p>
<ul>
    <li>Optimize factory design (reduce entity count)</li>
    <li>Use more efficient designs (direct insertion vs belts)</li>
    <li>Reduce <code>autosave_interval</code> frequency</li>
    <li>Upgrade to faster CPU (single-thread performance matters)</li>
    <li>Monitor UPS (updates per second) in game</li>
</ul>

<h2 id="game-modes">🎮 Game Modes & Features</h2>

<h3>Multiplayer Modes</h3>
<ul>
    <li><strong>Cooperative:</strong> All players work together on one factory</li>
    <li><strong>PvP:</strong> Players can attack each other (requires mods/scenarios)</li>
    <li><strong>Scenario-based:</strong> Custom scenarios with specific objectives</li>
</ul>

<h3>Core Features</h3>
<ul>
    <li><strong>Automation:</strong> Build and optimize production lines</li>
    <li><strong>Research:</strong> Technology tree with hundreds of upgrades</li>
    <li><strong>Logistics:</strong> Trains, belts, robots, and logistics networks</li>
    <li><strong>Combat:</strong> Defend against alien biters with turrets and walls</li>
    <li><strong>Blueprints:</strong> Copy and paste factory designs</li>
    <li><strong>Multiplayer:</strong> Cooperative building with friends</li>
</ul>

<h3>Map Settings</h3>
<ul>
    <li><strong>Peaceful Mode:</strong> Biters don't attack unless provoked</li>
    <li><strong>Resource Settings:</strong> Adjust frequency, size, and richness of ores</li>
    <li><strong>Enemy Settings:</strong> Configure biter evolution and expansion</li>
    <li><strong>Starting Area:</strong> Size of safe starting zone</li>
</ul>

<h2 id="mods">🔌 Mods & Mod Portal</h2>
<p>Factorio has extensive mod support via the official Mod Portal:</p>

<h3>Popular Mods</h3>
<ul>
    <li><strong>Bob's Mods:</strong> Adds complexity with new tiers of machines</li>
    <li><strong>Angel's Mods:</strong> Complete overhaul of production chains</li>
    <li><strong>Krastorio 2:</strong> Major gameplay overhaul and extension</li>
    <li><strong>Space Exploration:</strong> Adds space travel and new planets</li>
    <li><strong>Factorissimo:</strong> Allows building factories inside buildings</li>
    <li><strong>Quality of Life mods:</strong> Improved UI, automation helpers</li>
</ul>

<h3>Mod Installation</h3>
<ol>
    <li>Download mods from <a href="https://mods.factorio.com/" target="_blank">mods.factorio.com</a></li>
    <li>Place .zip files in <code>~/.factorio/mods/</code></li>
    <li>Edit <code>mod-list.json</code> to enable/disable mods</li>
    <li>Restart server with <code>--mod-directory</code> parameter if needed</li>
</ol>

<h3>Mod Synchronization</h3>
<ul>
    <li>All players must have identical mod versions</li>
    <li>Server can provide <code>mod-list.json</code> for easy sync</li>
    <li>In-game mod portal downloads mods automatically</li>
</ul>

<h2 id="admin">👤 Admin Commands</h2>

<h3>In-Game Commands</h3>
<div class="code-block">
<code>/admin                  # Open admin menu
/ban [player]             # Ban a player
/unban [player]           # Unban a player
/kick [player] [reason]   # Kick a player
/promote [player]         # Promote player to admin
/demote [player]          # Demote player from admin
/mute [player]            # Mute player's chat
/unmute [player]          # Unmute player
/whitelist add [player]   # Add to whitelist
/whitelist remove [player]# Remove from whitelist
/save                     # Manually save game</code>
</div>

<h3>RCON Commands (via External Tools)</h3>
<p>Use RCON to send console commands remotely when <code>--rcon-port</code> and <code>--rcon-password</code> are configured.</p>

<h2>📚 Resources</h2>
<ul>
    <li><strong>Official Website:</strong> <a href="https://www.factorio.com/" target="_blank">https://www.factorio.com/</a></li>
    <li><strong>Mod Portal:</strong> <a href="https://mods.factorio.com/" target="_blank">https://mods.factorio.com/</a></li>
    <li><strong>Wiki:</strong> <a href="https://wiki.factorio.com/" target="_blank">https://wiki.factorio.com/</a></li>
    <li><strong>Reddit:</strong> r/factorio community</li>
    <li><strong>Forums:</strong> Official Factorio forums</li>
</ul>

<div class="warning-box">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Factorio is <strong>CPU-intensive</strong> - single-thread performance is critical</li>
        <li><strong>Unlimited players</strong> supported but practical limit depends on factory size and CPU</li>
        <li>All players must have <strong>identical mod versions</strong> for multiplayer compatibility</li>
        <li><strong>Autosaves</strong> create regular backups - configure frequency and slot count</li>
        <li>Use <strong>RCON</strong> for remote server administration</li>
        <li><strong>Token required</strong> for public server listing (free from factorio.com account)</li>
        <li>Map generation is <strong>highly customizable</strong> - adjust resources and enemies</li>
        <li><strong>Regular backups</strong> of saves/ directory strongly recommended</li>
        <li>Cooperative factory building with <strong>real-time shared control</strong></li>
        <li>The factory must grow!</li>
    </ul>
</div>