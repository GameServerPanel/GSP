<?php
/**
 * Team Fortress 2 - Comprehensive Server Hosting Guide
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
        <a href="#gamemodes" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Game Modes</a>
        <a href="#plugins" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Plugins</a>
        <a href="#troubleshooting" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">🔧 Troubleshooting</a>
    </div>
</div>

<h1>Team Fortress 2 Dedicated Server Hosting Guide</h1>

<h2>Overview</h2>
<p>Team Fortress 2 (TF2) is Valve's class-based multiplayer FPS game. This guide covers hosting a TF2 dedicated server using Source Dedicated Server (srcds) on VPS or dedicated servers.</p>

<h2 id="quick-info">Quick Info</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <ul style="color: #e5e7eb; line-height: 1.8; margin: 0;">
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27015</code> (UDP)</li>
        <li><strong style="color: #ffffff;">RCON Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27015</code> (TCP)</li>
        <li><strong style="color: #ffffff;">Additional Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">27020</code> (TCP/UDP)</li>
        <li><strong style="color: #ffffff;">Minimum RAM:</strong> 1GB (2GB+ recommended)</li>
        <li><strong style="color: #ffffff;">Recommended CPU:</strong> 2+ cores @ 2.4GHz+</li>
        <li><strong style="color: #ffffff;">Storage:</strong> 15-20GB for game files</li>
        <li><strong style="color: #ffffff;">SteamCMD App ID:</strong> 232250</li>
        <li><strong style="color: #ffffff;">Server Binary:</strong> srcds.exe (Windows) / srcds_run (Linux)</li>
        <li><strong style="color: #ffffff;">Config Location:</strong> tf/cfg/server.cfg</li>
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
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">27015</code></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span> / <span style="background: #1e40af; padding: 4px 8px; border-radius: 3px; color: #dbeafe;">TCP</span></td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;">Game + RCON port (UDP for game, TCP for RCON)</td>
            <td style="padding: 12px; border-bottom: 1px solid #334155;"><span style="background: #7c2d12; padding: 4px 8px; border-radius: 3px; color: #fed7aa;">✓ Yes</span></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code style="background: #0f172a; padding: 4px 8px; border-radius: 3px; color: #a5b4fc;">27020</code></td>
            <td style="padding: 12px;"><span style="background: #065f46; padding: 4px 8px; border-radius: 3px; color: #d1fae5;">UDP</span> / <span style="background: #1e40af; padding: 4px 8px; border-radius: 3px; color: #dbeafe;">TCP</span></td>
            <td style="padding: 12px;">SourceTV (spectator/streaming)</td>
            <td style="padding: 12px;"><span style="background: #713f12; padding: 4px 8px; border-radius: 3px; color: #fef3c7;">Optional</span></td>
        </tr>
    </tbody>
</table>

<h3>Firewall Configuration Examples</h3>

<h4>UFW (Ubuntu/Debian)</h4>
<pre><code>sudo ufw allow 27015/udp comment 'TF2 game port'
sudo ufw allow 27015/tcp comment 'TF2 RCON'
sudo ufw allow 27020 comment 'TF2 SourceTV'
sudo ufw reload
</code></pre>

<h4>FirewallD (CentOS/RHEL/Fedora)</h4>
<pre><code>sudo firewall-cmd --permanent --add-port=27015/udp
sudo firewall-cmd --permanent --add-port=27015/tcp
sudo firewall-cmd --permanent --add-port=27020/tcp
sudo firewall-cmd --permanent --add-port=27020/udp
sudo firewall-cmd --reload
</code></pre>

<h4>Windows Firewall</h4>
<pre><code># Run in PowerShell as Administrator
New-NetFirewallRule -DisplayName "TF2 Game/RCON" -Direction Inbound -Protocol UDP -LocalPort 27015 -Action Allow
New-NetFirewallRule -DisplayName "TF2 RCON TCP" -Direction Inbound -Protocol TCP -LocalPort 27015 -Action Allow
New-NetFirewallRule -DisplayName "TF2 SourceTV" -Direction Inbound -LocalPort 27020 -Action Allow
</code></pre>

<h4>iptables (Legacy Linux)</h4>
<pre><code>sudo iptables -A INPUT -p udp --dport 27015 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 27015 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 27020 -j ACCEPT
sudo iptables -A INPUT -p udp --dport 27020 -j ACCEPT
sudo service iptables save
</code></pre>

<h2 id="installation">Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Windows Server 2012+ or Linux (Ubuntu/Debian)</li>
    <li><strong>CPU:</strong> 2+ cores @ 2.4GHz minimum</li>
    <li><strong>RAM:</strong> 2GB minimum, 4GB recommended for 24 players</li>
    <li><strong>Storage:</strong> 15-20GB for server files</li>
    <li><strong>Network:</strong> 100Mbps recommended</li>
</ul>

<h3>Installing via SteamCMD</h3>
<pre><code># Install SteamCMD
# Linux:
sudo add-apt-repository multiverse
sudo dpkg --add-architecture i386
sudo apt update
sudo apt install lib32gcc1 steamcmd

# Create server directory
mkdir -p ~/tf2server
cd ~/tf2server

# Download TF2 server files (App ID 232250)
steamcmd +login anonymous +force_install_dir ~/tf2server +app_update 232250 validate +quit
</code></pre>

<h3>Startup Scripts</h3>
<p><strong>Windows (start_tf2.bat):</strong></p>
<pre><code>srcds.exe -console -game tf +map ctf_2fort +maxplayers 24 -port 27015 +exec server.cfg
</code></pre>

<p><strong>Linux (start_tf2.sh):</strong></p>
<pre><code>#!/bin/bash
./srcds_run -console -game tf +map ctf_2fort +maxplayers 24 -port 27015 +exec server.cfg
</code></pre>

<h2 id="configuration">Server Configuration</h2>

<h3>server.cfg Example</h3>
<p>Create <code>tf/cfg/server.cfg</code>:</p>
<pre><code>// Server Information
hostname "My TF2 Server"
sv_region 1  // 0=US East, 1=US West, 2=South America, 3=Europe, etc.
rcon_password "your_secure_password"
sv_password ""  // Leave empty for public, or set server password

// Server Settings
sv_lan 0
sv_pure 2  // 0=off, 1=loose, 2=strict file checking
mp_autoteambalance 1
sv_visiblemaxplayers 24
mp_timelimit 30
mp_maxrounds 5
mp_winlimit 0

// Class Limits (competitive servers)
// tf_tournament_classlimit_scout 2
// tf_tournament_classlimit_soldier 2
// tf_tournament_classlimit_demoman 2
// tf_tournament_classlimit_medic 1

// Communication
sv_alltalk 0  // 0=team only, 1=all players
sv_voiceenable 1
sv_allow_voice_from_file 0

// Logging
log on
sv_logbans 1
sv_logecho 1
sv_logfile 1
sv_log_onefile 0

// Network Settings
sv_minrate 20000
sv_maxrate 100000
sv_mincmdrate 66
sv_maxcmdrate 66
sv_minupdaterate 66
sv_maxupdaterate 66

// Anti-Cheat
sv_cheats 0
sv_consistency 1
sv_pure 2
</code></pre>

<h2 id="gamemodes">Game Modes & Maps</h2>

<h3>Official Game Modes</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Mode</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Description</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Example Maps</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Capture the Flag</strong></td>
            <td style="padding: 12px;">Capture enemy intelligence briefcase</td>
            <td style="padding: 12px;">ctf_2fort, ctf_turbine</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Control Point</strong></td>
            <td style="padding: 12px;">Capture all control points</td>
            <td style="padding: 12px;">cp_dustbowl, cp_gorge</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Payload</strong></td>
            <td style="padding: 12px;">Push/stop cart to destination</td>
            <td style="padding: 12px;">pl_badwater, pl_upward</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Payload Race</strong></td>
            <td style="padding: 12px;">Both teams push carts</td>
            <td style="padding: 12px;">plr_hightower, plr_pipeline</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>King of the Hill</strong></td>
            <td style="padding: 12px;">Control single point for timer</td>
            <td style="padding: 12px;">koth_harvest, koth_viaduct</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Arena</strong></td>
            <td style="padding: 12px;">No respawns, last team standing</td>
            <td style="padding: 12px;">arena_lumberyard, arena_well</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><strong>Mann vs Machine</strong></td>
            <td style="padding: 12px;">Co-op vs AI robot waves</td>
            <td style="padding: 12px;">mvm_decoy, mvm_coaltown</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><strong>Pass Time</strong></td>
            <td style="padding: 12px;">Sports-like mode with JACK</td>
            <td style="padding: 12px;">pass_brickyard, pass_district</td>
        </tr>
    </tbody>
</table>

<h3>Map Rotation</h3>
<p>Create <code>tf/cfg/mapcycle.txt</code>:</p>
<pre><code>ctf_2fort
ctf_turbine
cp_dustbowl
cp_gorge
pl_badwater
pl_upward
koth_harvest
koth_viaduct
</code></pre>

<h3>Popular Community Maps</h3>
<ul>
    <li><strong>pl_barnblitz:</strong> Community favorite payload map</li>
    <li><strong>cp_process:</strong> Competitive 5CP map</li>
    <li><strong>koth_product:</strong> Competitive KOTH variant</li>
    <li><strong>pl_swiftwater:</strong> Popular payload map</li>
    <li><strong>cp_steel:</strong> Unique attack/defend CP map</li>
</ul>

<h3>Installing Custom Maps</h3>
<pre><code># Download map .bsp file
# Place in: tf/maps/

# Download .nav file (for bots) if available
# Place in: tf/maps/

# Add to mapcycle.txt
echo "custom_map_name" >> tf/cfg/mapcycle.txt

# Change map via RCON
rcon changelevel custom_map_name
</code></pre>

<h2 id="plugins">Plugins & Extensions</h2>

<h3>SourceMod & MetaMod Installation</h3>

<h4>1. Install MetaMod:Source</h4>
<pre><code># Download from https://www.sourcemm.net/downloads.php?branch=stable
# Extract to tf/ directory

# Verify - should see:
tf/addons/metamod/

# Test in server console:
meta version
</code></pre>

<h4>2. Install SourceMod</h4>
<pre><code># Download from https://www.sourcemod.net/downloads.php?branch=stable
# Extract to tf/ directory

# Should see:
tf/addons/sourcemod/

# Add admin in: tf/addons/sourcemod/configs/admins_simple.ini
"STEAM_0:1:12345678" "99:z"  // Replace with your SteamID

# Test in server:
sm version
</code></pre>

<h3>Essential Plugins</h3>
<ul>
    <li><strong>AdminMenu:</strong> In-game admin panel (included with SourceMod)</li>
    <li><strong>RTV (Rock The Vote):</strong> Player-initiated map voting</li>
    <li><strong>MapChooser:</strong> End-of-map voting system</li>
    <li><strong>Basic Votes:</strong> Kick, ban, map change votes</li>
    <li><strong>BaseBans:</strong> Permanent ban system</li>
    <li><strong>BaseComm:</strong> Mute/gag player communications</li>
</ul>

<h3>Popular TF2-Specific Plugins</h3>
<ul>
    <li><strong>TF2 Competitive Fixes:</strong> Tournament mode improvements</li>
    <li><strong>TF2 Stats:</strong> Track player statistics</li>
    <li><strong>MGE Mod:</strong> 1v1/2v2 training arenas</li>
    <li><strong>Randomizer:</strong> Random weapon/class attributes</li>
    <li><strong>PropHunt:</strong> Hide as props, seekers find them</li>
    <li><strong>Dodgeball:</strong> Airblast-redirected rocket gameplay</li>
    <li><strong>Surf Timer:</strong> Surfing movement maps with records</li>
    <li><strong>VSH (Vs Saxton Hale):</strong> All players vs one boss</li>
</ul>

<h3>Installing Plugins</h3>
<pre><code># Download .smx plugin file
# Place in: tf/addons/sourcemod/plugins/

# Restart server or reload plugins:
sm plugins reload pluginname

# List loaded plugins:
sm plugins list

# Disable plugin:
sm plugins unload pluginname
</code></pre>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Not in Browser</h3>
<pre><code># Verify sv_lan setting
sv_lan 0  // Must be 0

# Check region
sv_region 1

# Test connectivity
netstat -an | grep 27015

# Wait 5-10 minutes for Steam master server registration
</code></pre>

<h3>Players Can't Connect</h3>
<ul>
    <li>Check firewall allows UDP 27015</li>
    <li>Verify <code>sv_password</code> is empty or known to players</li>
    <li>Ensure server has valid Steam connection</li>
    <li>Check <code>sv_pure</code> settings aren't too strict</li>
    <li>Review server console for connection errors</li>
</ul>

<h3>MetaMod/SourceMod Not Loading</h3>
<pre><code># Check file structure
tf/
  addons/
    metamod/
      bin/
    sourcemod/
      plugins/
      configs/

# Verify in server console
meta version
sm version

# Check logs
tf/addons/sourcemod/logs/errors_*.log
</code></pre>

<h3>Performance Issues / Low FPS</h3>
<pre><code># Optimize rates
sv_maxrate 100000
sv_maxcmdrate 66
sv_maxupdaterate 66

# Set tickrate (requires startup parameter)
-tickrate 66  // Add to startup command

# Check server FPS
stats  // In server console

# Reduce max players if needed
maxplayers 16  // Instead of 24
</code></pre>

<h3>Custom Content Not Downloading</h3>
<pre><code># Enable downloads in server.cfg
sv_allowdownload 1
sv_allowupload 1
net_maxfilesize 64  // MB

# Use FastDL for faster downloads
sv_downloadurl "http://yourdomain.com/tf/"
// Upload maps/materials/sounds to web server
</code></pre>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-lightbulb" style="color: #fbbf24; margin-right: 8px;"></i>Pro Tips</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Tickrate:</strong> TF2 defaults to 66 tick - use <code>-tickrate 66</code> for consistency</li>
        <li><strong>sv_pure:</strong> Set to 2 for competitive, 1 for casual with custom content</li>
        <li><strong>Class limits:</strong> Use tournament cvars for competitive format restrictions</li>
        <li><strong>Crits:</strong> Disable random crits in competitive: <code>tf_weapon_criticals 0</code></li>
        <li><strong>Spread:</strong> Disable random spread: <code>tf_use_fixed_weaponspreads 1</code></li>
        <li><strong>FastDL:</strong> Essential for custom maps - set up web server for fast downloads</li>
        <li><strong>Logs.tf:</strong> Use for match statistics and competitive logging</li>
    </ul>
</div>

<h2 id="related-mods">🔌 Related Mods & Plugins</h2>
<p>Popular server modifications compatible with Team Fortress 2:</p>
<ul>
    <li><a href="../metamodsource/">Metamod:Source</a> - Foundation plugin loader required for SourceMod and other Source engine plugins</li>
</ul>

<h2>Resources</h2>
<ul>
    <li><a href="https://wiki.teamfortress.com/wiki/Windows_dedicated_server" target="_blank">Official TF2 Dedicated Server Wiki</a></li>
    <li><a href="https://wiki.alliedmods.net/Introduction_to_SourceMod_Plugins" target="_blank">SourceMod Plugin Development</a></li>
    <li><a href="https://forums.alliedmods.net/forumdisplay.php?f=108" target="_blank">AlliedModders TF2 Forum</a></li>
    <li><a href="https://comp.tf/" target="_blank">Competitive TF2 Community</a></li>
    <li><a href="https://logs.tf/" target="_blank">Logs.tf - Match Statistics</a></li>
    <li><a href="https://tf2maps.net/" target="_blank">TF2Maps - Custom Map Community</a></li>
</ul>

<h3>Port Forwarding</h3>
<pre><code># Required ports:
UDP 27015 - Game server
TCP 27015 - RCON
TCP/UDP 27020 - SourceTV

# Linux:
sudo ufw allow 27015
sudo ufw allow 27020
</code></pre>

<h2 id="plugins">Plugins & Extensions</h2>

<h3>SourceMod Installation</h3>
<p>Most TF2 servers use SourceMod for admin commands and plugins. Install Metamod:Source first, then SourceMod.</p>

<h3>Popular Plugins</h3>
<ul>
    <li><strong>AdminMenu:</strong> Complete admin interface</li>
    <li><strong>RTV (Rock The Vote):</strong> Player map voting</li>
    <li><strong>TF2 Competitive Fixes:</strong> Competitive tweaks</li>
    <li><strong>MGE Mod:</strong> 1v1/2v2 training mode</li>
</ul>

<h2 id="troubleshooting">Troubleshooting</h2>

<h3>Server Won't Start</h3>
<ul>
    <li>Verify files: <code>steamcmd +app_update 232250 validate</code></li>
    <li>Check port 27015 availability</li>
    <li>Review console errors</li>
</ul>

<h3>Not in Server Browser</h3>
<ul>
    <li>Verify <code>sv_lan 0</code></li>
    <li>Confirm ports forwarded</li>
    <li>Wait 5-10 minutes for registration</li>
</ul>

<h2>Additional Resources</h2>
<ul>
    <li><a href="https://wiki.teamfortress.com/wiki/Windows_dedicated_server" target="_blank">Official TF2 Server Wiki</a></li>
    <li><a href="https://wiki.alliedmods.net/" target="_blank">SourceMod Documentation</a></li>
    <li><a href="https://forums.alliedmods.net/forumdisplay.php?f=108" target="_blank">AlliedModders TF2 Forum</a></li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Keep server updated via SteamCMD</li>
        <li>Use strong RCON passwords</li>
        <li>Regular config backups</li>
        <li>Monitor for exploits</li>
    </ul>
</div>
