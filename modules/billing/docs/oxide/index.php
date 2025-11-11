<?php
/**
 * Oxide / uMod Documentation
 */
?>
<h1>📚 Oxide / uMod Guide</h1>
<p style="font-size: 1.1em; color: rgba(255,255,255,0.8);">Universal modding framework for Rust, 7 Days to Die, and more</p>

<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Quick Info</h3>
    <table style="width: 100%; color: #e5e7eb;">
        <tr><td><strong style="color: #ffffff;">Supported Games:</strong></td><td>Rust, 7 Days to Die, Hurtworld, Reign of Kings, The Forest</td></tr>
        <tr><td><strong style="color: #ffffff;">Languages:</strong></td><td>C# (primary), Lua (limited support)</td></tr>
        <tr><td><strong style="color: #ffffff;">Plugin Repository:</strong></td><td>umod.org (formerly oxidemod.org)</td></tr>
        <tr><td><strong style="color: #ffffff;">Current Version:</strong></td><td>uMod 2.x (successor to Oxide 2.0)</td></tr>
        <tr><td><strong style="color: #ffffff;">Permissions System:</strong></td><td>Built-in group and user permissions</td></tr>
        <tr><td><strong style="color: #ffffff;">Website:</strong></td><td>umod.org</td></tr>
    </table>
</div>

<h2>Navigation</h2>
<ul style="list-style: none; padding: 0;">
    <li>📚 <a href="#overview">Overview</a></li>
    <li>📥 <a href="#installation">Installation</a></li>
    <li>🔌 <a href="#plugins">Plugin System</a></li>
    <li>👤 <a href="#permissions">Permissions & Groups</a></li>
    <li>💻 <a href="#development">Plugin Development</a></li>
    <li>🎮 <a href="#gamespecific">Game-Specific Setup</a></li>
    <li>🔧 <a href="#troubleshooting">Troubleshooting</a></li>
    <li>📖 <a href="#resources">Resources</a></li>
</ul>

<h2 id="overview">Overview</h2>
<p>Oxide (now uMod) is a modding framework that provides a universal API for creating plugins across multiple games. It allows server owners to add custom functionality without modifying game files.</p>

<h3>Key Features</h3>
<ul>
    <li><strong>Cross-Game Support:</strong> Same framework works across multiple games</li>
    <li><strong>C# Plugins:</strong> Write plugins in C# with hot-reloading</li>
    <li><strong>Hook System:</strong> Intercept game events (player join, death, chat, etc.)</li>
    <li><strong>Permissions:</strong> Built-in user/group permission system</li>
    <li><strong>Data Storage:</strong> Built-in JSON/SQLite data storage</li>
    <li><strong>Plugin Repository:</strong> Thousands of free plugins at umod.org</li>
    <li><strong>Hot-Reload:</strong> Update plugins without server restart</li>
    <li><strong>Console Commands:</strong> Add custom admin and player commands</li>
</ul>

<h3>Supported Games</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Game</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Support Level</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Popular Plugins</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">Rust</td>
            <td style="padding: 12px;"><span style="color: #10b981;">★★★★★ Full</span></td>
            <td style="padding: 12px;">Kits, Economics, Clans, TP, Vote</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">7 Days to Die</td>
            <td style="padding: 12px;"><span style="color: #10b981;">★★★★★ Full</span></td>
            <td style="padding: 12px;">Admin Tools, Teleport, Bloodmoon</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">Hurtworld</td>
            <td style="padding: 12px;"><span style="color: #fbbf24;">★★★★☆ Good</span></td>
            <td style="padding: 12px;">Admin Tools, Kits, Economy</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">Reign of Kings</td>
            <td style="padding: 12px;"><span style="color: #fbbf24;">★★★☆☆ Fair</span></td>
            <td style="padding: 12px;">Admin Tools, Custom Commands</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">The Forest</td>
            <td style="padding: 12px;"><span style="color: #fbbf24;">★★★☆☆ Fair</span></td>
            <td style="padding: 12px;">Limited plugin support</td>
        </tr>
    </tbody>
</table>

<h2 id="installation">📥 Installation</h2>

<h3>Rust Installation</h3>

<h4>Windows</h4>
<pre><code># Stop your Rust server first

# Download uMod for Rust from umod.org
# Extract Oxide.Rust.zip to your server directory
# Files extract to: RustDedicated_Data/Managed/

# Start server - Oxide will create directories:
# oxide/plugins/      (place plugins here)
# oxide/config/       (plugin configs)
# oxide/data/         (plugin data)
# oxide/logs/         (Oxide logs)
</code></pre>

<h4>Linux</h4>
<pre><code># Stop Rust server
cd /home/rustserver

# Download and extract Oxide
wget https://umod.org/games/rust/download
unzip -o Oxide.Rust.zip

# Set permissions
chmod +x RustDedicated

# Start server
./RustDedicated -batchmode +server.port 28015 +server.level "Procedural Map" +server.seed 12345 +server.worldsize 4000 +server.maxplayers 100 +server.hostname "My Rust Server" +server.description "Powered by Oxide" +server.identity "server1"
</code></pre>

<h3>7 Days to Die Installation</h3>

<h4>Windows</h4>
<pre><code># Stop 7 Days to Die server

# Download uMod for 7 Days to Die from umod.org
# Extract to 7DaysToDieServer directory
# Files go to: 7DaysToDieServer_Data/Managed/

# Start server - Oxide directories created in:
# 7DaysToDieServer_Data/oxide/
</code></pre>

<h4>Linux</h4>
<pre><code># Stop server
cd /home/7daysserver

# Download and extract
wget https://umod.org/games/7-days-to-die/download
unzip -o Oxide.7DaysToDie.zip

# Start server
./7DaysToDieServer.sh
</code></pre>

<h2 id="plugins">🔌 Plugin System</h2>

<h3>Installing Plugins</h3>

<h4>Method 1: Manual Installation</h4>
<pre><code># Download plugin .cs file from umod.org
# Example: AdminPanel.cs

# Place in oxide/plugins/ directory
oxide/
└── plugins/
    └── AdminPanel.cs

# Oxide automatically compiles and loads the plugin
# Check oxide/logs/oxide.log for any errors
</code></pre>

<h4>Method 2: In-Game Commands (if supported)</h4>
<pre><code># Some admin tools allow downloading plugins in-game
oxide.load PluginName
oxide.reload PluginName
oxide.unload PluginName
</code></pre>

<h3>Popular Rust Plugins</h3>

<h4>Essential Administration Plugins</h4>
<ul>
    <li><strong>Admin Radar:</strong> ESP-style admin tool showing players/entities</li>
    <li><strong>Vanish:</strong> Make admins invisible to players</li>
    <li><strong>Admin Panel:</strong> GUI admin menu</li>
    <li><strong>Better Chat:</strong> Enhanced chat with colors, titles, groups</li>
    <li><strong>Anti Cheat:</strong> Detect and ban cheaters</li>
</ul>

<h4>Player Features</h4>
<ul>
    <li><strong>Kits:</strong> Starter kits and VIP kits for players</li>
    <li><strong>TP (Teleport):</strong> /home, /tpr (teleport request), /town commands</li>
    <li><strong>Economics:</strong> Virtual currency system</li>
    <li><strong>Clans:</strong> Team/clan system with shared bases</li>
    <li><strong>Vote:</strong> Reward players for voting on server lists</li>
</ul>

<h4>Server Enhancement</h4>
<ul>
    <li><strong>Auto Purge:</strong> Remove old/inactive bases</li>
    <li><strong>Stack Size Controller:</strong> Modify stack sizes</li>
    <li><strong>Loot Multiplier:</strong> Increase/decrease loot spawns</li>
    <li><strong>Gather Manager:</strong> Modify resource gathering rates</li>
    <li><strong>Skip Night:</strong> Vote to skip night time</li>
</ul>

<h3>Plugin Configuration</h3>
<p>After first load, plugins create config files in <code>oxide/config/</code>:</p>
<pre><code># Example: oxide/config/Kits.json
{
  "Kits": {
    "starter": {
      "items": [
        {"shortname": "stones", "amount": 3000},
        {"shortname": "wood", "amount": 5000},
        {"shortname": "bandage", "amount": 5}
      ],
      "cooldown": 3600
    }
  }
}

# Edit configs and use: oxide.reload Kits
</code></pre>

<h2 id="permissions">👤 Permissions & Groups</h2>

<h3>Permission System Overview</h3>
<p>Oxide uses a hierarchical permission system with groups and individual user permissions.</p>

<h3>Basic Permission Commands</h3>
<pre><code># Grant permission to user (use Steam64 ID)
oxide.grant user 76561198012345678 pluginname.permission

# Grant permission to group
oxide.grant group admin adminradar.allowed

# Revoke permission
oxide.revoke user 76561198012345678 pluginname.permission

# Show user permissions
oxide.show user 76561198012345678

# Show group permissions
oxide.show group admin
</code></pre>

<h3>Group Management</h3>
<pre><code># Create group
oxide.group add vip "VIP Members" 1

# Add user to group
oxide.usergroup add 76561198012345678 vip

# Remove user from group
oxide.usergroup remove 76561198012345678 vip

# List all groups
oxide.show groups

# Set parent group (inheritance)
oxide.group parent vip default
</code></pre>

<h3>Default Groups</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Group</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Rank</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Purpose</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">admin</td>
            <td style="padding: 12px;">0 (highest)</td>
            <td style="padding: 12px;">Full permissions</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">moderator</td>
            <td style="padding: 12px;">1</td>
            <td style="padding: 12px;">Moderate permissions</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">vip</td>
            <td style="padding: 12px;">2</td>
            <td style="padding: 12px;">VIP player perks</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">default</td>
            <td style="padding: 12px;">3 (lowest)</td>
            <td style="padding: 12px;">Regular players</td>
        </tr>
    </tbody>
</table>

<h3>Finding Steam64 ID</h3>
<ul>
    <li><strong>In-Game (Rust):</strong> F1 console, type <code>players</code> - shows online players with Steam IDs</li>
    <li><strong>Website:</strong> steamid.io - convert Steam profile URL to Steam64 ID</li>
    <li><strong>Server Logs:</strong> Check oxide/logs/ for player connections</li>
</ul>

<h2 id="development">💻 Plugin Development</h2>

<h3>Basic Plugin Structure</h3>
<pre><code>using Oxide.Core;
using Oxide.Core.Libraries.Covalence;

namespace Oxide.Plugins
{
    [Info("HelloWorld", "YourName", "1.0.0")]
    [Description("Simple hello world plugin")]
    class HelloWorld : RustPlugin
    {
        // Called when plugin is loaded
        void Init()
        {
            Puts("HelloWorld plugin loaded!");
        }

        // Chat command: /hello
        [Command("hello")]
        void HelloCommand(IPlayer player, string command, string[] args)
        {
            player.Reply("Hello, " + player.Name + "!");
        }

        // Hook: Called when player connects
        void OnPlayerConnected(BasePlayer player)
        {
            PrintToChat($"{player.displayName} has joined the server!");
        }
    }
}
</code></pre>

<h3>Common Hooks (Rust)</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Hook</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Purpose</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>Init()</code></td>
            <td style="padding: 12px;">Plugin initialization</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>OnServerInitialized()</code></td>
            <td style="padding: 12px;">Server fully loaded</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>OnPlayerConnected(BasePlayer)</code></td>
            <td style="padding: 12px;">Player joins server</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>OnPlayerDisconnected(BasePlayer)</code></td>
            <td style="padding: 12px;">Player leaves server</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>OnPlayerChat(BasePlayer, string)</code></td>
            <td style="padding: 12px;">Player sends chat message</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>OnEntityDeath(BaseCombatEntity)</code></td>
            <td style="padding: 12px;">Entity (player/NPC) dies</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>OnEntitySpawned(BaseNetworkable)</code></td>
            <td style="padding: 12px;">Entity spawns</td>
        </tr>
    </tbody>
</table>

<h3>Data Storage Example</h3>
<pre><code>// Store player data in JSON file
class PlayerData
{
    public string Name;
    public int Points;
    public DateTime LastSeen;
}

class MyPlugin : RustPlugin
{
    Dictionary<ulong, PlayerData> playerData = new Dictionary<ulong, PlayerData>();

    void LoadData()
    {
        playerData = Interface.Oxide.DataFileSystem.ReadObject<Dictionary<ulong, PlayerData>>("MyPlugin_Data");
    }

    void SaveData()
    {
        Interface.Oxide.DataFileSystem.WriteObject("MyPlugin_Data", playerData);
    }
}
</code></pre>

<h2 id="gamespecific">🎮 Game-Specific Setup</h2>

<h3>Rust</h3>
<pre><code># Server startup with Oxide
./RustDedicated -batchmode \
  +server.port 28015 \
  +server.level "Procedural Map" \
  +server.hostname "My Oxide Server" \
  +rcon.port 28016 \
  +rcon.password "your_rcon_password"

# Essential first plugins:
# 1. BetterChat (chat enhancement)
# 2. AdminRadar (admin tool)
# 3. Vanish (admin invisibility)
</code></pre>

<h3>7 Days to Die</h3>
<pre><code># Oxide config location:
# 7DaysToDieServer_Data/oxide/config/

# Essential plugins:
# 1. Admin Tools
# 2. Teleport System
# 3. Player Info

# Check oxide/logs/ for plugin errors
</code></pre>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Plugin Not Loading</h3>
<pre><code># Check oxide/logs/oxide.log for errors
tail -f oxide/logs/oxide.log

# Common issues:
# 1. Syntax errors in plugin code
# 2. Missing dependencies
# 3. Outdated plugin version
# 4. Permission denied (file permissions)

# Reload plugin manually
oxide.reload PluginName
</code></pre>

<h3>Permissions Not Working</h3>
<ul>
    <li>Verify user's Steam64 ID is correct</li>
    <li>Check group membership: <code>oxide.show user STEAMID</code></li>
    <li>Ensure permission format matches plugin docs (case-sensitive)</li>
    <li>Check plugin config for permission names</li>
</ul>

<h3>Plugin Conflicts</h3>
<pre><code># Disable plugins one by one to find conflict
oxide.unload PluginName

# Check oxide/logs/ for conflict errors
# Some plugins modify same hooks - may conflict
</code></pre>

<h3>High CPU Usage</h3>
<ul>
    <li>Disable resource-intensive plugins (radar, ESP tools)</li>
    <li>Check for plugin loops in oxide/logs/</li>
    <li>Update to latest Oxide version</li>
    <li>Limit entities tracked by admin tools</li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-lightbulb" style="color: #fbbf24; margin-right: 8px;"></i>Pro Tips</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Start Simple:</strong> Install essential plugins first, add more gradually</li>
        <li><strong>Read Plugin Docs:</strong> Each plugin page on umod.org has detailed config examples</li>
        <li><strong>Backup Configs:</strong> Backup oxide/config/ before major changes</li>
        <li><strong>Test Permissions:</strong> Use non-admin account to test player permissions</li>
        <li><strong>Monitor Logs:</strong> Regularly check oxide/logs/ for errors</li>
        <li><strong>Update Plugins:</strong> Keep plugins updated for bug fixes and new game versions</li>
        <li><strong>VIP System:</strong> Use groups for VIP perks (kits, teleports, etc.)</li>
        <li><strong>Hot-Reload:</strong> Most plugin changes can reload without server restart</li>
    </ul>
</div>

<h2 id="resources">Resources</h2>
<ul>
    <li><a href="https://umod.org/" target="_blank">uMod Official Website</a></li>
    <li><a href="https://umod.org/plugins" target="_blank">Plugin Repository</a></li>
    <li><a href="https://umod.org/documentation" target="_blank">Developer Documentation</a></li>
    <li><a href="https://discord.gg/umod" target="_blank">uMod Discord Community</a></li>
    <li>Related Game Documentation: <a href="../rust/">Rust</a>, <a href="../7daystodie/">7 Days to Die</a></li>
</ul>
