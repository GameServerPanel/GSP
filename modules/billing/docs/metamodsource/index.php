<?php
/**
 * Metamod:Source Documentation
 */
?>
<h1>📚 Metamod:Source Guide</h1>
<p style="font-size: 1.1em; color: rgba(255,255,255,0.8);">Plugin loader foundation for Source engine servers</p>

<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Quick Info</h3>
    <table style="width: 100%; color: #e5e7eb;">
        <tr><td><strong style="color: #ffffff;">Supported Games:</strong></td><td>All Source engine games (CS:S, TF2, L4D, L4D2, CS:GO, DoD:S)</td></tr>
        <tr><td><strong style="color: #ffffff;">Purpose:</strong></td><td>Plugin loader layer between game and plugins like SourceMod</td></tr>
        <tr><td><strong style="color: #ffffff;">Config Format:</strong></td><td>VDF (Valve Data Format) files</td></tr>
        <tr><td><strong style="color: #ffffff;">Latest Version:</strong></td><td>1.11+ (actively maintained)</td></tr>
        <tr><td><strong style="color: #ffffff;">Website:</strong></td><td>sourcemm.net</td></tr>
        <tr><td><strong style="color: #ffffff;">Install Order:</strong></td><td>Metamod:Source first, then SourceMod</td></tr>
        <tr><td><strong style="color: #ffffff;">Not Compatible:</strong></td><td>Half-Life 1 engine (use original MetaMod)</td></tr>
    </table>
</div>

<h2>Navigation</h2>
<ul style="list-style: none; padding: 0;">
    <li>📚 <a href="#overview">Overview</a></li>
    <li>📥 <a href="#installation">Installation</a></li>
    <li>⚙️ <a href="#configuration">Configuration</a></li>
    <li>🔌 <a href="#sourcemod">SourceMod Integration</a></li>
    <li>🎮 <a href="#gamespecific">Game-Specific Paths</a></li>
    <li>🛠️ <a href="#plugins">Plugin Management</a></li>
    <li>🔧 <a href="#troubleshooting">Troubleshooting</a></li>
    <li>📖 <a href="#resources">Resources</a></li>
</ul>

<h2 id="overview">Overview</h2>
<p>Metamod:Source is a plugin loader and abstraction layer for Source engine dedicated servers. It sits between the game server and server plugins (like SourceMod), allowing multiple plugins to hook into game events and functions without conflicting.</p>

<h3>Key Concepts</h3>
<ul>
    <li><strong>Plugin Loader:</strong> Loads and manages server-side plugins</li>
    <li><strong>Abstraction Layer:</strong> Provides unified API across different Source games</li>
    <li><strong>Foundation for SourceMod:</strong> SourceMod requires Metamod:Source to function</li>
    <li><strong>VServerPlugin Interface:</strong> Hooks into Source engine's plugin system</li>
    <li><strong>Hot-Loading:</strong> Plugins can be loaded/unloaded without server restart</li>
</ul>

<h3>Metamod:Source vs. Original MetaMod</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Feature</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Metamod:Source</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Original MetaMod</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">Engine</td>
            <td style="padding: 12px;">Source (CS:S, TF2, L4D, etc.)</td>
            <td style="padding: 12px;">GoldSrc (CS 1.6, DoD 1.3, etc.)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">Config Format</td>
            <td style="padding: 12px;">VDF (.vdf files)</td>
            <td style="padding: 12px;">INI-style (.ini files)</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">Plugin System</td>
            <td style="padding: 12px;">VServerPlugin interface</td>
            <td style="padding: 12px;">DLL/SO hooking</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">Common Plugins</td>
            <td style="padding: 12px;">SourceMod</td>
            <td style="padding: 12px;">AMX Mod X</td>
        </tr>
    </tbody>
</table>

<h3>Supported Games</h3>
<ul>
    <li><strong>Counter-Strike: Source</strong></li>
    <li><strong>Counter-Strike: Global Offensive</strong></li>
    <li><strong>Team Fortress 2</strong></li>
    <li><strong>Left 4 Dead</strong></li>
    <li><strong>Left 4 Dead 2</strong></li>
    <li><strong>Day of Defeat: Source</strong></li>
    <li><strong>Half-Life 2: Deathmatch</strong></li>
    <li><strong>Garry's Mod</strong></li>
    <li><strong>Insurgency</strong></li>
    <li><strong>Killing Floor</strong> (Source version)</li>
</ul>

<h2 id="installation">📥 Installation</h2>

<h3>Prerequisites</h3>
<ul>
    <li>Source engine dedicated server installed</li>
    <li>Admin access to server files</li>
    <li>Know your game's folder structure</li>
</ul>

<h3>Linux Installation</h3>

<h4>CS:Source / TF2 / L4D Example</h4>
<pre><code># Download latest Metamod:Source for Linux
cd /tmp
wget https://mms.alliedmods.net/mmsdrop/1.11/mmsource-1.11.0-git1148-linux.tar.gz

# Extract to server folder
# For CS:S:
cd /path/to/cstrike
tar -xzf /tmp/mmsource-1.11.0-git1148-linux.tar.gz

# For TF2:
cd /path/to/tf
tar -xzf /tmp/mmsource-1.11.0-git1148-linux.tar.gz

# For L4D2:
cd /path/to/left4dead2
tar -xzf /tmp/mmsource-1.11.0-git1148-linux.tar.gz

# Folder structure created:
# addons/
#   metamod/
#     bin/
#       server.so  (Linux)
</code></pre>

<h4>CS:GO Specific</h4>
<pre><code># CS:GO requires slightly different structure
cd /path/to/csgo
tar -xzf /tmp/mmsource-1.11.0-git1148-linux.tar.gz

# Verify structure:
# csgo/addons/metamod/bin/server.so
</code></pre>

<h3>Windows Installation</h3>
<pre><code># Download Windows version:
# mmsource-1.11.0-git1148-windows.zip

# Extract to game folder (CS:S example):
# Extract to: C:\srcds\cstrike\

# Folder structure:
# cstrike\
#   addons\
#     metamod\
#       bin\
#         server.dll  (Windows)
</code></pre>

<h3>Creating metamod.vdf</h3>
<p>Create <code>addons/metamod.vdf</code> in the game folder:</p>

<h4>CS:Source</h4>
<pre><code>"Plugin"
{
    "file"    "../cstrike/addons/metamod/bin/server"
}
</code></pre>

<h4>Team Fortress 2</h4>
<pre><code>"Plugin"
{
    "file"    "../tf/addons/metamod/bin/server"
}
</code></pre>

<h4>Left 4 Dead 2</h4>
<pre><code>"Plugin"
{
    "file"    "../left4dead2/addons/metamod/bin/server"
}
</code></pre>

<h4>CS:GO</h4>
<pre><code>"Plugin"
{
    "file"    "addons/metamod/bin/server"
}
</code></pre>

<h3>Verify Installation</h3>
<pre><code># Start server and type in console:
meta version

# Expected output:
# Metamod:Source version 1.11.0-dev+1148
# Built from: https://github.com/alliedmodders/metamod-source
# Loaded As: VServerPlugin (gameinfo.txt)
</code></pre>

<h2 id="configuration">⚙️ Configuration</h2>

<h3>gameinfo.txt Modification</h3>
<p>Metamod:Source can load via VDF or gameinfo.txt. VDF method is preferred.</p>

<h4>Manual gameinfo.txt Entry (Alternative Method)</h4>
<pre><code>// Edit gameinfo.txt in game folder
// Add under "SearchPaths":

GameBin |gameinfo_path|addons/metamod/bin

// Example for CS:S (cstrike/gameinfo.txt):
"FileSystem"
{
    "SteamAppId"    "240"
    "SearchPaths"
    {
        GameBin |gameinfo_path|addons/metamod/bin
        Game    |gameinfo_path|.
        Game    cstrike
        Game    hl2
    }
}
</code></pre>

<h3>Plugin Configuration</h3>
<p>Plugins load from <code>addons/metamod/metaplugins.ini</code>:</p>
<pre><code>; Metamod:Source Plugins File
; Each plugin is on its own line
; Format: alias:file

; Example SourceMod entry:
sourcemod:addons/sourcemod/bin/sourcemod_mm
</code></pre>

<h3>Console Commands</h3>
<pre><code># List loaded plugins
meta list

# Load plugin
meta load path/to/plugin

# Unload plugin
meta unload <plugin_id>

# Refresh plugin list
meta refresh

# Version info
meta version

# Plugin info
meta info <plugin_id>
</code></pre>

<h2 id="sourcemod">🔌 SourceMod Integration</h2>

<h3>Why SourceMod Needs Metamod:Source</h3>
<p>SourceMod is built as a Metamod:Source plugin. It cannot run without Metamod:Source as the foundation layer.</p>

<h3>Installation Order</h3>
<ol>
    <li><strong>Install Metamod:Source first</strong> (as described above)</li>
    <li><strong>Verify Metamod works</strong> (meta version command)</li>
    <li><strong>Download SourceMod</strong> from sourcemod.net</li>
    <li><strong>Extract SourceMod</strong> to game folder</li>
    <li><strong>SourceMod auto-registers</strong> with Metamod:Source</li>
</ol>

<h3>SourceMod Installation</h3>
<pre><code># Download SourceMod (Linux example)
wget https://sm.alliedmods.net/smdrop/1.11/sourcemod-1.11.0-git6917-linux.tar.gz

# Extract to game folder (CS:S example)
cd /path/to/cstrike
tar -xzf sourcemod-1.11.0-git6917-linux.tar.gz

# Folder structure:
# addons/
#   metamod/  (already exists)
#   sourcemod/  (new)
#     bin/
#       sourcemod_mm.so  (auto-loads via Metamod)
#     configs/
#     plugins/
#     scripting/
</code></pre>

<h3>Verify SourceMod Loaded</h3>
<pre><code># In server console:
meta list

# Should show:
# [01] SourceMod (1.11.0-dev+6917) by AlliedModders LLC

# Or use SourceMod command:
sm version
</code></pre>

<h2 id="gamespecific">🎮 Game-Specific Paths</h2>

<h3>Path Reference Table</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Game</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Game Folder</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">metamod.vdf Path</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">CS:Source</td>
            <td style="padding: 12px;"><code>cstrike/</code></td>
            <td style="padding: 12px;"><code>../cstrike/addons/metamod/bin/server</code></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">CS:GO</td>
            <td style="padding: 12px;"><code>csgo/</code></td>
            <td style="padding: 12px;"><code>addons/metamod/bin/server</code></td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">Team Fortress 2</td>
            <td style="padding: 12px;"><code>tf/</code></td>
            <td style="padding: 12px;"><code>../tf/addons/metamod/bin/server</code></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">Left 4 Dead</td>
            <td style="padding: 12px;"><code>left4dead/</code></td>
            <td style="padding: 12px;"><code>../left4dead/addons/metamod/bin/server</code></td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">Left 4 Dead 2</td>
            <td style="padding: 12px;"><code>left4dead2/</code></td>
            <td style="padding: 12px;"><code>../left4dead2/addons/metamod/bin/server</code></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">DoD:Source</td>
            <td style="padding: 12px;"><code>dod/</code></td>
            <td style="padding: 12px;"><code>../dod/addons/metamod/bin/server</code></td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">HL2:DM</td>
            <td style="padding: 12px;"><code>hl2mp/</code></td>
            <td style="padding: 12px;"><code>../hl2mp/addons/metamod/bin/server</code></td>
        </tr>
    </tbody>
</table>

<h3>Multi-Server Setup</h3>
<p>For multiple servers sharing files:</p>
<pre><code># Install Metamod once in shared location
/srcds/shared/addons/metamod/

# Symlink to each server
ln -s /srcds/shared/addons /srcds/server1/cstrike/addons
ln -s /srcds/shared/addons /srcds/server2/tf/addons

# Use appropriate metamod.vdf for each game
</code></pre>

<h2 id="plugins">🛠️ Plugin Management</h2>

<h3>Loading Plugins Manually</h3>
<p>Edit <code>addons/metamod/metaplugins.ini</code>:</p>
<pre><code>; SourceMod (loads automatically if installed)
sourcemod:addons/sourcemod/bin/sourcemod_mm

; Custom plugin example
customplugin:addons/customplugin/bin/customplugin_mm
</code></pre>

<h3>Plugin Auto-Discovery</h3>
<p>Metamod:Source auto-discovers plugins in <code>addons/</code> folder that contain:</p>
<ul>
    <li>A valid plugin binary (server.so / server.dll)</li>
    <li>Proper VServerPlugin interface implementation</li>
</ul>

<h3>Common Plugins</h3>
<ul>
    <li><strong>SourceMod:</strong> Admin/plugin framework (most popular)</li>
    <li><strong>SourceBans:</strong> Centralized ban management</li>
    <li><strong>EventScripts:</strong> Python-based scripting (older)</li>
    <li><strong>Custom Plugins:</strong> Game-specific modifications</li>
</ul>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Metamod Not Loading</h3>
<pre><code># Check if metamod.vdf exists
ls -la addons/metamod.vdf

# Verify VDF syntax (no syntax errors)
cat addons/metamod.vdf

# Check file permissions (Linux)
chmod 644 addons/metamod.vdf
chmod -R 755 addons/metamod/

# Verify correct path in VDF for your game
# CS:GO uses "addons/metamod/bin/server"
# Others use "../gamefolder/addons/metamod/bin/server"
</code></pre>

<h3>"meta" Command Not Found</h3>
<ul>
    <li>Metamod:Source not loaded - check console on startup for errors</li>
    <li>Verify metamod.vdf exists in correct location</li>
    <li>Check server console for "Metamod:Source" loaded message</li>
    <li>Try manual gameinfo.txt method as alternative</li>
</ul>

<h3>SourceMod Not Loading</h3>
<pre><code># Verify Metamod loaded first
meta version

# Check if SourceMod plugin is visible
meta list

# If not listed, verify SourceMod installation:
ls -la addons/sourcemod/bin/

# Check for sourcemod_mm.so (Linux) or sourcemod_mm.dll (Windows)

# Manual load:
meta load addons/sourcemod/bin/sourcemod_mm
</code></pre>

<h3>Plugin Conflicts</h3>
<pre><code># List all loaded plugins
meta list

# Unload problematic plugin
meta unload <plugin_number>

# Check server console for error messages

# Common conflicts:
# - Multiple admin systems (AMX Mod X + SourceMod)
# - Outdated plugin versions
# - Incompatible game version
</code></pre>

<h3>CS:GO Specific Issues</h3>
<ul>
    <li><strong>CS:GO uses different VDF path:</strong> <code>addons/metamod/bin/server</code> (no ../ prefix)</li>
    <li><strong>Frequent updates:</strong> Valve updates may break plugins - wait for Metamod update</li>
    <li><strong>Insecure mode:</strong> Some plugins require <code>-insecure</code> launch parameter</li>
</ul>

<h3>Server Crash After Installation</h3>
<pre><code># Check server logs for error messages
tail -f logs/L*.log

# Common causes:
# 1. Wrong architecture (32-bit vs 64-bit)
# 2. Corrupted plugin binary
# 3. Incompatible Metamod version for game

# Test with clean Metamod installation (no plugins)
# Remove addons/sourcemod temporarily
# If server starts, issue is with SourceMod or its plugins
</code></pre>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-lightbulb" style="color: #fbbf24; margin-right: 8px;"></i>Pro Tips</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>VDF Method Preferred:</strong> Use metamod.vdf instead of gameinfo.txt when possible</li>
        <li><strong>Version Compatibility:</strong> Match Metamod:Source version to Source engine version</li>
        <li><strong>CS:GO Path Different:</strong> CS:GO doesn't use "../csgo/" prefix in VDF</li>
        <li><strong>Test After Updates:</strong> Valve updates can break Metamod - test before production</li>
        <li><strong>Backup Configs:</strong> Keep backups of metamod.vdf and metaplugins.ini</li>
        <li><strong>Development Builds:</strong> Use stable releases for production servers</li>
        <li><strong>Plugin Order:</strong> Load order matters - SourceMod should load first</li>
        <li><strong>Console Monitoring:</strong> Watch server console during startup for errors</li>
    </ul>
</div>

<h2 id="resources">Resources</h2>
<ul>
    <li><a href="https://www.sourcemm.net/" target="_blank">Official Metamod:Source Website</a></li>
    <li><a href="https://wiki.alliedmods.net/Metamod:Source" target="_blank">Metamod:Source Wiki</a></li>
    <li><a href="https://github.com/alliedmodders/metamod-source" target="_blank">Metamod:Source GitHub</a></li>
    <li><a href="https://www.sourcemod.net/" target="_blank">SourceMod Official Site</a></li>
    <li><a href="https://forums.alliedmods.net/" target="_blank">Allied Modders Forums</a></li>
    <li><a href="../css/">CS:Source Server Documentation</a></li>
    <li><a href="../csgo/">CS:GO Server Documentation</a></li>
    <li><a href="../tf2/">Team Fortress 2 Server Documentation</a></li>
    <li><a href="../left4dead/">Left 4 Dead Server Documentation</a></li>
    <li><a href="../left4dead2/">Left 4 Dead 2 Server Documentation</a></li>
</ul>
