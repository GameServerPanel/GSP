<?php
/**
 * Team Fortress 2 - Comprehensive Server Hosting Guide
 * General game server hosting information (not platform-specific)
 */
?>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Navigation</h3>
    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
        <a href="#quick-info" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Quick Info</a>
        <a href="#installation" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Installation</a>
        <a href="#configuration" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Configuration</a>
        <a href="#plugins" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Plugins</a>
        <a href="#troubleshooting" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Troubleshooting</a>
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
sv_region 1
rcon_password "your_secure_password"

// Server Settings
sv_lan 0
sv_pure 2
mp_autoteambalance 1
sv_visiblemaxplayers 24
mp_timelimit 30
mp_maxrounds 5

// Communication
sv_alltalk 0
sv_voiceenable 1

// Logging
log on
sv_logbans 1
</code></pre>

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
