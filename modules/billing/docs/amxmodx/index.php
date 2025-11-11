<?php
/**
 * AMX Mod X Documentation
 */
?>
<h1>📚 AMX Mod X Server Guide</h1>
<p style="font-size: 1.1em; color: rgba(255,255,255,0.8);">Powerful plugin framework for Counter-Strike servers</p>

<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Quick Info</h3>
    <table style="width: 100%; color: #e5e7eb;">
        <tr><td><strong style="color: #ffffff;">Supported Games:</strong></td><td>Counter-Strike 1.6, CS:Source, CS:GO (limited)</td></tr>
        <tr><td><strong style="color: #ffffff;">Language:</strong></td><td>Pawn scripting (.sma source, .amxx compiled)</td></tr>
        <tr><td><strong style="color: #ffffff;">Requirement:</strong></td><td>MetaMod must be installed first</td></tr>
        <tr><td><strong style="color: #ffffff;">Admin System:</strong></td><td>users.ini file-based or database</td></tr>
        <tr><td><strong style="color: #ffffff;">Latest Version:</strong></td><td>1.10+ (actively maintained)</td></tr>
        <tr><td><strong style="color: #ffffff;">Website:</strong></td><td>amxmodx.org</td></tr>
        <tr><td><strong style="color: #ffffff;">Forum:</strong></td><td>forums.alliedmods.net</td></tr>
    </table>
</div>

<h2>Navigation</h2>
<ul style="list-style: none; padding: 0;">
    <li>📚 <a href="#overview">Overview</a></li>
    <li>⚙️ <a href="#installation">Installation</a></li>
    <li>👤 <a href="#admin">Admin System</a></li>
    <li>🔌 <a href="#plugins">Plugin Management</a></li>
    <li>💻 <a href="#scripting">Pawn Scripting Basics</a></li>
    <li>🎮 <a href="#gamemodes">Popular Game Modes</a></li>
    <li>🛠️ <a href="#compilation">Compiling Plugins</a></li>
    <li>🔧 <a href="#troubleshooting">Troubleshooting</a></li>
    <li>📖 <a href="#resources">Resources</a></li>
</ul>

<h2 id="overview">Overview</h2>
<p>AMX Mod X (AMXX) is a powerful server-side modification framework for Half-Life 1 engine games, particularly Counter-Strike. It allows server administrators to add custom functionality through plugins written in Pawn scripting language.</p>

<h3>Key Features</h3>
<ul>
    <li><strong>Plugin System:</strong> Hot-load plugins without server restart</li>
    <li><strong>Admin Management:</strong> Comprehensive admin system with flags/permissions</li>
    <li><strong>Custom Commands:</strong> Create server-side commands and cvars</li>
    <li><strong>Event Hooks:</strong> Hook into game events (player death, spawn, etc.)</li>
    <li><strong>Database Support:</strong> MySQL, SQLite integration</li>
    <li><strong>Menu System:</strong> Create interactive menus for players</li>
    <li><strong>Game Modes:</strong> Enable custom game modes (Zombie Plague, Superhero, etc.)</li>
</ul>

<h3>Game Compatibility</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Game</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Support Level</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Notes</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">Counter-Strike 1.6</td>
            <td style="padding: 12px;"><span style="color: #10b981;">Full Support</span></td>
            <td style="padding: 12px;">Best compatibility, most plugins available</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">CS:Source</td>
            <td style="padding: 12px;"><span style="color: #eab308;">Partial Support</span></td>
            <td style="padding: 12px;">Works but SourceMod preferred</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">CS:GO</td>
            <td style="padding: 12px;"><span style="color: #ef4444;">Limited</span></td>
            <td style="padding: 12px;">SourceMod strongly recommended</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">Day of Defeat</td>
            <td style="padding: 12px;"><span style="color: #10b981;">Full Support</span></td>
            <td style="padding: 12px;">HL1 engine, full compatibility</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">TFC, NS, TS</td>
            <td style="padding: 12px;"><span style="color: #10b981;">Full Support</span></td>
            <td style="padding: 12px;">All HL1 mods supported</td>
        </tr>
    </tbody>
</table>

<h2 id="installation">⚙️ Installation</h2>

<h3>Prerequisites</h3>
<p><strong>Critical:</strong> MetaMod must be installed before AMX Mod X!</p>
<ul>
    <li>Counter-Strike dedicated server</li>
    <li>MetaMod 1.21+ (for CS 1.6) or MetaMod:Source (for Source games)</li>
    <li>Admin access to server files</li>
</ul>

<h3>Step 1: Install MetaMod (CS 1.6)</h3>
<pre><code># Download MetaMod from metamod.org
wget https://www.metamod.org/files/metamod-1.21-am.tar.gz
tar -xzf metamod-1.21-am.tar.gz

# Copy to server (Linux)
cp metamod.so /path/to/cstrike/addons/metamod/dlls/

# Create metamod plugin config
mkdir -p /path/to/cstrike/addons/metamod
echo 'linux addons/metamod/dlls/metamod.so' > /path/to/cstrike/addons/metamod/metamod.so

# Update liblist.gam (CS 1.6 only)
# Replace: gamedll_linux "dlls/cs.so"
# With: gamedll_linux "addons/metamod/dlls/metamod.so"
</code></pre>

<h4>Windows MetaMod Installation</h4>
<pre><code># Download Windows version
# Extract metamod.dll to cstrike\addons\metamod\dlls\

# Edit liblist.gam:
# Replace: gamedll "dlls\mp.dll"
# With: gamedll "addons\metamod\dlls\metamod.dll"
</code></pre>

<h3>Step 2: Install AMX Mod X</h3>

<h4>Linux Installation</h4>
<pre><code># Download latest AMXX from amxmodx.org
cd /tmp
wget https://www.amxmodx.org/amxxdrop/1.10/amxmodx-1.10.0-git5467-base-linux.tar.gz
wget https://www.amxmodx.org/amxxdrop/1.10/amxmodx-1.10.0-git5467-cstrike-linux.tar.gz

# Extract to server root
cd /path/to/cstrike
tar -xzf /tmp/amxmodx-1.10.0-git5467-base-linux.tar.gz
tar -xzf /tmp/amxmodx-1.10.0-git5467-cstrike-linux.tar.gz

# Set permissions
chmod +x addons/amxmodx/dlls/amxmodx_mm_i386.so

# AMX Mod X automatically registers with MetaMod
</code></pre>

<h4>Windows Installation</h4>
<pre><code># Download Windows packages:
# - amxmodx-1.10.0-git5467-base-windows.zip
# - amxmodx-1.10.0-git5467-cstrike-windows.zip

# Extract both to cstrike\ folder
# Folder structure should be:
# cstrike\
#   addons\
#     amxmodx\
#       configs\
#       dlls\
#       plugins\
#       scripting\
</code></pre>

<h3>Step 3: Verify Installation</h3>
<pre><code># Start server and check console for:
# "AMX Mod X version X.X.X loaded"

# In-game, type in console:
amx_version

# Expected output:
# AMX Mod X 1.10.0-git5467 (Counter-Strike 1.6)
</code></pre>

<h2 id="admin">👤 Admin System</h2>

<h3>Admin Configuration (users.ini)</h3>
<p>Located at: <code>addons/amxmodx/configs/users.ini</code></p>

<h4>Basic Admin Entry</h4>
<pre><code>; Name or IP with flags
"PlayerName" "" "abcdefghijklmnopqrstu" "ce"

; Admin by SteamID (recommended)
"STEAM_0:1:12345678" "" "abcdefghijklmnopqrstu" "ce"

; Admin by IP address
"192.168.1.100" "" "abcdefghijklmnopqrstu" "a"

; Password-based admin
"" "mypassword" "abcdefghijklmnopqrstu" "a"
</code></pre>

<h3>Admin Flags Explained</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Flag</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Permission</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>a</code></td>
            <td style="padding: 12px;">Immunity (can't be kicked/banned)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>b</code></td>
            <td style="padding: 12px;">Reservation (can join full server)</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>c</code></td>
            <td style="padding: 12px;">amx_kick command</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>d</code></td>
            <td style="padding: 12px;">amx_ban and amx_unban</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>e</code></td>
            <td style="padding: 12px;">amx_slay and amx_slap</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>f</code></td>
            <td style="padding: 12px;">amx_map command</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>g</code></td>
            <td style="padding: 12px;">amx_cvar (change server cvars)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>h</code></td>
            <td style="padding: 12px;">amx_cfg (execute configs)</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>i</code></td>
            <td style="padding: 12px;">amx_chat and other chat commands</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>j</code></td>
            <td style="padding: 12px;">amx_vote commands</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>k</code></td>
            <td style="padding: 12px;">Access to sv_password cvar</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>l</code></td>
            <td style="padding: 12px;">amx_rcon command</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>m</code></td>
            <td style="padding: 12px;">Custom level A (defined by plugins)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>z</code></td>
            <td style="padding: 12px;">User (no admin privileges)</td>
        </tr>
    </tbody>
</table>

<h3>Common Admin Commands</h3>
<pre><code># Player management
amx_kick <player> [reason]
amx_ban <player> <minutes> [reason]
amx_slay <player>
amx_slap <player> [damage]

# Server management
amx_map <mapname>
amx_cvar <cvar> <value>
amx_cfg <config file>
amx_pausecfg

# Chat and communication
amx_say <message>
amx_chat <message>  # Admin-only chat
amx_psay <player> <message>  # Private message

# Voting
amx_vote <question>
amx_votemap <map1> <map2> <map3>
amx_votekick <player>

# Information
amx_who  # List online players with info
amx_plugins  # List loaded plugins
amx_modules  # List loaded modules
</code></pre>

<h2 id="plugins">🔌 Plugin Management</h2>

<h3>Plugin Files Location</h3>
<ul>
    <li><strong>Compiled plugins (.amxx):</strong> <code>addons/amxmodx/plugins/</code></li>
    <li><strong>Source code (.sma):</strong> <code>addons/amxmodx/scripting/</code></li>
    <li><strong>Plugin configuration:</strong> <code>addons/amxmodx/configs/plugins.ini</code></li>
</ul>

<h3>plugins.ini Configuration</h3>
<p>Located at: <code>addons/amxmodx/configs/plugins.ini</code></p>
<pre><code>; Core plugins (required)
admin.amxx
adminhelp.amxx
adminslots.amxx
multilingual.amxx
menufront.amxx
cmdmenu.amxx
plmenu.amxx
mapchooser.amxx
admincmd.amxx

; Counter-Strike plugins
statsx.amxx
restmenu.amxx
scrollmsg.amxx

; Custom plugins (add your own)
; myplugin.amxx
; zombieplague.amxx

; Disable plugin with semicolon:
; disabled_plugin.amxx
</code></pre>

<h3>Installing New Plugins</h3>
<pre><code># 1. Download .amxx file or .sma source
# 2. If .amxx - copy to plugins/ folder
cp myplugin.amxx /path/to/cstrike/addons/amxmodx/plugins/

# 3. Add to plugins.ini
echo "myplugin.amxx" >> /path/to/cstrike/addons/amxmodx/configs/plugins.ini

# 4. Reload plugins without restart:
# In-game: amx_plugins reload
# Or restart server
</code></pre>

<h3>Plugin Commands</h3>
<pre><code># List all plugins
amx_plugins

# Enable/disable specific plugin
amx_pause <plugin name>
amx_unpause <plugin name>

# Reload all plugins
amx_plugins reload
</code></pre>

<h2 id="scripting">💻 Pawn Scripting Basics</h2>

<h3>Simple Plugin Example</h3>
<pre><code>/* hello_world.sma */
#include <amxmodx>
#include <amxmisc>

#define PLUGIN "Hello World"
#define VERSION "1.0"
#define AUTHOR "YourName"

public plugin_init() {
    register_plugin(PLUGIN, VERSION, AUTHOR)
    register_clcmd("say /hello", "cmd_hello")
}

public cmd_hello(id) {
    client_print(id, print_chat, "Hello, %s!", get_user_name(id))
    return PLUGIN_HANDLED
}
</code></pre>

<h3>Common Includes</h3>
<ul>
    <li><code>#include &lt;amxmodx&gt;</code> - Core AMXX functions</li>
    <li><code>#include &lt;amxmisc&gt;</code> - Miscellaneous utility functions</li>
    <li><code>#include &lt;cstrike&gt;</code> - Counter-Strike specific functions</li>
    <li><code>#include &lt;fun&gt;</code> - Fun commands (slap, godmode, etc.)</li>
    <li><code>#include &lt;engine&gt;</code> - Engine entity manipulation</li>
    <li><code>#include &lt;fakemeta&gt;</code> - Advanced entity control</li>
</ul>

<h3>Event Hooking</h3>
<pre><code>public plugin_init() {
    register_event("DeathMsg", "event_death", "a")
    register_event("CurWeapon", "event_curweapon", "be", "1=1")
}

public event_death() {
    new killer = read_data(1)
    new victim = read_data(2)
    
    client_print(killer, print_chat, "You killed %s!", get_user_name(victim))
}

public event_curweapon(id) {
    new weaponid = read_data(2)
    // Do something when player switches weapon
}
</code></pre>

<h2 id="gamemodes">🎮 Popular Game Modes</h2>

<h3>Zombie Plague</h3>
<p>Most popular zombie mod for CS 1.6:</p>
<ul>
    <li><strong>Features:</strong> Zombies vs Humans, special classes, custom models</li>
    <li><strong>Download:</strong> forums.alliedmods.net</li>
    <li><strong>Installation:</strong> Extract to addons/amxmodx/, add to plugins.ini</li>
    <li><strong>Configuration:</strong> Edit zombieplague.cfg in configs/</li>
</ul>

<h3>Superhero Mod</h3>
<p>Players gain superhero powers:</p>
<ul>
    <li><strong>Features:</strong> 20+ heroes (Superman, Flash, etc.), XP system</li>
    <li><strong>Heroes:</strong> Special abilities (speed, strength, invisibility)</li>
    <li><strong>Config:</strong> Extensive configuration in sh_heroes.cfg</li>
</ul>

<h3>Jailbreak</h3>
<p>Prison-themed game mode:</p>
<ul>
    <li><strong>Teams:</strong> Guards (CTs) vs Prisoners (Ts)</li>
    <li><strong>Features:</strong> Simon says, games, last request system</li>
    <li><strong>Requirements:</strong> Special jailbreak maps</li>
</ul>

<h3>Deathmatch</h3>
<p>Instant respawn deathmatch:</p>
<ul>
    <li><strong>Plugin:</strong> csdm.amxx (CS DeathMatch)</li>
    <li><strong>Features:</strong> Instant respawn, weapon menu, spawn protection</li>
    <li><strong>Config:</strong> csdm.cfg for spawn points and settings</li>
</ul>

<h2 id="compilation">🛠️ Compiling Plugins</h2>

<h3>Using Web Compiler (Easiest)</h3>
<pre><code># Visit: https://www.amxmodx.org/websc.php
# 1. Paste your .sma source code
# 2. Click "Compile"
# 3. Download resulting .amxx file
# 4. Upload to plugins/ folder
</code></pre>

<h3>Local Compilation (Linux)</h3>
<pre><code># Navigate to scripting folder
cd /path/to/cstrike/addons/amxmodx/scripting

# Compile single plugin
./amxxpc myplugin.sma

# Result: myplugin.amxx in compiled/ folder
# Copy to plugins/ folder
cp compiled/myplugin.amxx ../plugins/
</code></pre>

<h3>Local Compilation (Windows)</h3>
<pre><code># Navigate to scripting folder
cd cstrike\addons\amxmodx\scripting

# Compile using batch file
compile.exe myplugin.sma

# Or double-click compile.bat and follow prompts
# Compiled .amxx will be in compiled\ folder
</code></pre>

<h3>Compilation Errors</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Error</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Cause & Fix</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">fatal error 100: cannot read from file</td>
            <td style="padding: 12px;">Missing include file - download required .inc file</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">error 017: undefined symbol</td>
            <td style="padding: 12px;">Missing function or variable - check includes</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">error 021: symbol already defined</td>
            <td style="padding: 12px;">Duplicate variable/function name</td>
        </tr>
    </tbody>
</table>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>AMX Mod X Not Loading</h3>
<pre><code># Check MetaMod is loaded first
# In server console: meta list
# Should show: AMX Mod X

# If not listed:
# 1. Verify MetaMod installation
# 2. Check addons/metamod/plugins.ini contains:
linux addons/amxmodx/dlls/amxmodx_mm_i386.so
# Or for Windows:
win32 addons/amxmodx/dlls/amxmodx_mm.dll

# Check file permissions (Linux)
chmod +x addons/amxmodx/dlls/amxmodx_mm_i386.so
</code></pre>

<h3>Plugin Not Working</h3>
<pre><code># Check plugin is enabled in plugins.ini
cat addons/amxmodx/configs/plugins.ini | grep myplugin

# Verify plugin loaded
# In-game: amx_plugins
# Should list your plugin

# Check for compilation errors
# Recompile plugin and check for errors

# Enable debug mode
# In amxx.cfg: amx_debug 1
# Check logs/L*.log files for errors
</code></pre>

<h3>Admin Commands Not Working</h3>
<ul>
    <li>Verify admin entry in users.ini is correct (check SteamID format)</li>
    <li>Ensure admin flags include required permission (e.g., 'd' for ban)</li>
    <li>Check admincmd.amxx plugin is loaded</li>
    <li>Verify immunity flag 'a' for admin protection</li>
</ul>

<h3>Server Crash After Plugin Installation</h3>
<pre><code># Disable recently added plugin
# Edit plugins.ini and comment out:
; problematic_plugin.amxx

# Restart server and check if crash persists

# Check logs for specific error:
tail -f logs/L*.log

# Common causes:
# - Incompatible plugin version
# - Missing dependencies
# - Conflicting plugins
</code></pre>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-lightbulb" style="color: #fbbf24; margin-right: 8px;"></i>Pro Tips</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>MetaMod First:</strong> Always install MetaMod before AMX Mod X</li>
        <li><strong>SteamID Admins:</strong> Use SteamID in users.ini, not names (easily changed)</li>
        <li><strong>Test Plugins:</strong> Test new plugins on dev server before production</li>
        <li><strong>Backup configs:</strong> Keep backups of users.ini and plugins.ini</li>
        <li><strong>Plugin Updates:</strong> Check forums regularly for plugin updates</li>
        <li><strong>Performance:</strong> Disable unused plugins to improve server performance</li>
        <li><strong>Security:</strong> Never give 'l' flag (rcon) to untrusted admins</li>
        <li><strong>Documentation:</strong> Read plugin documentation before installation</li>
    </ul>
</div>

<h2 id="resources">Resources</h2>
<ul>
    <li><a href="https://www.amxmodx.org/" target="_blank">Official AMX Mod X Website</a></li>
    <li><a href="https://forums.alliedmods.net/" target="_blank">Allied Modders Forums</a></li>
    <li><a href="https://www.amxmodx.org/api/" target="_blank">AMX Mod X API Documentation</a></li>
    <li><a href="https://www.amxmodx.org/websc.php" target="_blank">Online Plugin Compiler</a></li>
    <li><a href="https://wiki.alliedmods.net/AMX_Mod_X_Plugins" target="_blank">Plugin Development Tutorial</a></li>
    <li><a href="../cstrike/">Counter-Strike 1.6 Server Documentation</a></li>
    <li><a href="../css/">CS:Source Server Documentation</a></li>
    <li><a href="../csgo/">CS:GO Server Documentation</a></li>
</ul>
