<?php
/**
 * Ricochet Dedicated Server Reference (self-hosted Linux/Windows)
 */
?>
<div style="background:#1e3a5f;padding:20px;border-left:4px solid #3b82f6;margin:20px 0;border-radius:4px;">
    <h3 style="color:#fff;margin-top:0;">📚 Quick Navigation</h3>
    <div style="display:flex;flex-wrap:wrap;gap:10px;">
        <a href="#overview" style="background:#0f172a;padding:8px 16px;border-radius:4px;color:#a5b4fc;text-decoration:none;">Overview</a>
        <a href="#ports" style="background:#0f172a;padding:8px 16px;border-radius:4px;color:#a5b4fc;text-decoration:none;">Ports & Firewall</a>
        <a href="#install" style="background:#0f172a;padding:8px 16px;border-radius:4px;color:#a5b4fc;text-decoration:none;">Installation</a>
        <a href="#startup" style="background:#0f172a;padding:8px 16px;border-radius:4px;color:#a5b4fc;text-decoration:none;">Startup Parameters</a>
        <a href="#config" style="background:#0f172a;padding:8px 16px;border-radius:4px;color:#a5b4fc;text-decoration:none;">Configuration</a>
        <a href="#maintenance" style="background:#0f172a;padding:8px 16px;border-radius:4px;color:#a5b4fc;text-decoration:none;">Maintenance</a>
        <a href="#troubleshooting" style="background:#0f172a;padding:8px 16px;border-radius:4px;color:#a5b4fc;text-decoration:none;">Troubleshooting</a>
        <a href="#resources" style="background:#0f172a;padding:8px 16px;border-radius:4px;color:#a5b4fc;text-decoration:none;">Resources</a>
    </div>
</div>

<h1 id="overview">Ricochet Dedicated Server Hosting Guide</h1>
<p>Ricochet is a Half-Life 1 era arena shooter that still uses the classic HLDS (GoldSrc) dedicated server stack. The notes below describe how to deploy and operate a Ricochet server on any Linux or Windows VPS without relying on a control panel.</p>

<h2>Quick Facts</h2>
<ul>
    <li><strong>Engine:</strong> GoldSrc (HLDS AppID 90, mod folder <code>ricochet</code>)</li>
    <li><strong>Default Map:</strong> <code>rc_deathmatch</code>; other stock maps include <code>rc_arena</code> and <code>rc_trinity</code></li>
    <li><strong>Max Players:</strong> 32 (16 recommended for stability)</li>
    <li><strong>Primary Configs:</strong> <code>ricochet/server.cfg</code>, <code>ricochet/mapcycle.txt</code>, <code>ricochet/motd.txt</code></li>
    <li><strong>Binary:</strong> <code>hlds_run</code> (Linux) or <code>hlds.exe</code> (Windows)</li>
    <li><strong>Log Files:</strong> <code>ricochet/logs/Lxxxxx.log</code></li>
</ul>

<h2 id="ports">🔌 Ports & Firewall Rules</h2>
<div style="background:#1e3a5f;padding:20px;border-left:4px solid #3b82f6;margin:20px 0;border-radius:4px;">
    <table style="width:100%;color:#e5e7eb;border-collapse:collapse;">
        <thead>
            <tr style="background:#0f172a;">
                <th style="padding:10px;text-align:left;color:#fff;">Port</th>
                <th style="padding:10px;text-align:left;color:#fff;">Protocol</th>
                <th style="padding:10px;text-align:left;color:#fff;">Purpose</th>
                <th style="padding:10px;text-align:left;color:#fff;">Required?</th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom:1px solid #374151;">
                <td style="padding:10px;"><code style="background:#0f172a;padding:2px 6px;border-radius:3px;">27015</code></td>
                <td style="padding:10px;">UDP</td>
                <td style="padding:10px;">Game traffic & master server heartbeats</td>
                <td style="padding:10px;"><span style="color:#10b981;">Required</span></td>
            </tr>
            <tr style="border-bottom:1px solid #374151;">
                <td style="padding:10px;"><code style="background:#0f172a;padding:2px 6px;border-radius:3px;">27015</code></td>
                <td style="padding:10px;">TCP</td>
                <td style="padding:10px;">RCON & remote console</td>
                <td style="padding:10px;"><span style="color:#f59e0b;">Optional</span></td>
            </tr>
            <tr style="border-bottom:1px solid #374151;">
                <td style="padding:10px;"><code style="background:#0f172a;padding:2px 6px;border-radius:3px;">27016</code></td>
                <td style="padding:10px;">UDP</td>
                <td style="padding:10px;">Spectator / HLTV relay (if used)</td>
                <td style="padding:10px;"><span style="color:#f59e0b;">Optional</span></td>
            </tr>
            <tr>
                <td style="padding:10px;"><code style="background:#0f172a;padding:2px 6px;border-radius:3px;">27005</code></td>
                <td style="padding:10px;">UDP (outbound)</td>
                <td style="padding:10px;">Client auth replies; allow outbound so players can connect</td>
                <td style="padding:10px;"><span style="color:#10b981;">Required outbound</span></td>
            </tr>
        </tbody>
    </table>
    <p style="margin-top:16px;color:#e5e7eb;">Valve master servers also require outbound UDP 27010-27012; keep them open so your instance appears on public listings.</p>
    <div>
<pre><code># UFW (Ubuntu/Debian)
sudo ufw allow 27015/udp comment 'Ricochet game'
sudo ufw allow 27015/tcp comment 'Ricochet RCON'
sudo ufw allow 27016/udp comment 'Ricochet HLTV (optional)'

# FirewallD (RHEL/CentOS)
sudo firewall-cmd --permanent --add-port=27015/udp
sudo firewall-cmd --permanent --add-port=27015/tcp
sudo firewall-cmd --permanent --add-port=27016/udp
sudo firewall-cmd --reload

# Windows Defender Firewall
netsh advfirewall firewall add rule name="Ricochet Game" dir=in action=allow protocol=UDP localport=27015
netsh advfirewall firewall add rule name="Ricochet RCON" dir=in action=allow protocol=TCP localport=27015
</code></pre>
    </div>
</div>

<h2 id="install">Installation & SteamCMD Setup</h2>
<h3>Prerequisites</h3>
<ul>
    <li>64-bit Linux distro (Ubuntu 22.04+, Debian 12+, Rocky/Alma 9) or Windows Server 2019+</li>
    <li>x86_64 CPU, 1 vCPU is plenty</li>
    <li>512 MB RAM minimum (1 GB recommended to leave headroom)</li>
    <li>5 GB disk for HLDS + logs</li>
    <li>Firewall/port forwarding access</li>
</ul>

<h3>Linux Deployment</h3>
<pre><code># Create user	sudo useradd -m -s /bin/bash ricochet
sudo passwd ricochet
sudo -u ricochet bash

# Install dependencies and SteamCMD
sudo apt update && sudo apt install steamcmd lib32gcc-s1 lib32stdc++6 -y
mkdir -p ~/ricochet-server && cd ~/ricochet-server
steamcmd +login anonymous +force_install_dir $PWD +app_update 90 validate +quit

# Launch once (tmux/screen recommended)
./hlds_run -game ricochet -console -port 27015 +ip 0.0.0.0 \
    +map rc_deathmatch +maxplayers 16 +sv_lan 0 +exec server.cfg
</code></pre>

<h3>Windows Deployment</h3>
<ol>
    <li>Install SteamCMD from <a href="https://developer.valvesoftware.com/wiki/SteamCMD" target="_blank">Valve's developer wiki</a> and extract to <code>C:\steamcmd</code>.</li>
    <li>Create <code>C:\servers\ricochet</code> and run:<br>
        <code>steamcmd +login anonymous +force_install_dir C:\servers\ricochet +app_update 90 validate +quit</code></li>
    <li>Launch via batch file:<pre><code>cd /d C:\servers\ricochet
hlds.exe -console -game ricochet -port 27015 -ip 0.0.0.0 ^
    +map rc_deathmatch +maxplayers 16 +sv_lan 0 +exec server.cfg</code></pre></li>
    <li>Use Task Scheduler or NSSM to keep the process running after reboots.</li>
</ol>

<h2 id="startup">Startup Parameters</h2>
<table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
    <thead>
        <tr style="background:#0f172a;color:#fff;">
            <th style="padding:10px;text-align:left;">Parameter</th>
            <th style="padding:10px;text-align:left;">Description</th>
            <th style="padding:10px;text-align:left;">Example</th>
        </tr>
    </thead>
    <tbody>
        <tr style="border-bottom:1px solid #374151;">
            <td style="padding:10px;"><code>-game ricochet</code></td>
            <td style="padding:10px;">Loads the Ricochet mod data folder</td>
            <td style="padding:10px;">Always required</td>
        </tr>
        <tr style="border-bottom:1px solid #374151;">
            <td style="padding:10px;"><code>-console</code></td>
            <td style="padding:10px;">Forces console-only mode (no GUI)</td>
            <td style="padding:10px;">Recommended on servers</td>
        </tr>
        <tr style="border-bottom:1px solid #374151;">
            <td style="padding:10px;"><code>-port</code> / <code>-sport</code></td>
            <td style="padding:10px;">Sets game and spectator ports</td>
            <td style="padding:10px;">-port 27015 -sport 27016</td>
        </tr>
        <tr style="border-bottom:1px solid #374151;">
            <td style="padding:10px;"><code>-ip</code></td>
            <td style="padding:10px;">Binds to a specific interface</td>
            <td style="padding:10px;">-ip 192.0.2.10</td>
        </tr>
        <tr style="border-bottom:1px solid #374151;">
            <td style="padding:10px;"><code>-autoupdate</code></td>
            <td style="padding:10px;">Linux only; pulls updates via SteamCMD when server restarts</td>
            <td style="padding:10px;">-autoupdate -steam_dir ~/steamcmd -steamcmd_script ~/ricochet/update.txt</td>
        </tr>
        <tr style="border-bottom:1px solid #374151;">
            <td style="padding:10px;"><code>+map</code></td>
            <td style="padding:10px;">Starting map when the process boots</td>
            <td style="padding:10px;">+map rc_deathmatch</td>
        </tr>
        <tr style="border-bottom:1px solid #374151;">
            <td style="padding:10px;"><code>+maxplayers</code></td>
            <td style="padding:10px;">Hard slot limit (1–32)</td>
            <td style="padding:10px;">+maxplayers 16</td>
        </tr>
        <tr style="border-bottom:1px solid #374151;">
            <td style="padding:10px;"><code>+sv_lan</code></td>
            <td style="padding:10px;">0 exposes to internet, 1 keeps LAN-only</td>
            <td style="padding:10px;">+sv_lan 0</td>
        </tr>
        <tr>
            <td style="padding:10px;"><code>+exec server.cfg</code></td>
            <td style="padding:10px;">Ensures your config runs on boot</td>
            <td style="padding:10px;">+exec server.cfg</td>
        </tr>
    </tbody>
</table>
<p>Append <code>+log on</code> and <code>+sv_password "pass"</code> directly on the command line to guarantee they are processed even if configs fail.</p>

<h2 id="config">Core Configuration Files</h2>
<h3>server.cfg Template</h3>
<pre><code>// Identity & access
hostname "My Ricochet Arena"
sv_password ""            // Set for private servers
rcon_password "ChangeMe!2025"
sv_lan 0
sv_region 0                // 0 = worldwide listing

// Gameplay
mp_timelimit 20
mp_fraglimit 20
mp_weaponstay 1
mp_falldamage 1
mp_friendlyfire 0
mp_flashlight 0
mp_chattime 10
mp_footsteps 1
sv_cheats 0

// Physics
sv_gravity 800
sv_airaccelerate 10
sv_accelerate 10
sv_friction 4
sv_maxspeed 320

// Networking
sv_minrate 1500
sv_maxrate 20000
sv_minupdaterate 20
sv_maxupdaterate 60
sys_ticrate 300

// Logging & security
log on
sv_logbans 1
sv_logecho 1
sv_log_onefile 0
writeid
writeip
</code></pre>

<h3>mapcycle.txt</h3>
<pre><code>rc_deathmatch
rc_arena
rc_trinity
rc_bounce
</code></pre>
<p>Reorder or duplicate entries to weight the rotation. Use <code>mp_timelimit</code> and <code>mp_fraglimit</code> to control the length of each round.</p>

<h3>Scheduled Maintenance Scripts</h3>
<p>Create <code>restart.sh</code> (Linux) to keep the process alive:</p>
<pre><code>#!/bin/bash
cd /home/ricochet/ricochet-server
while true; do
  ./hlds_run -game ricochet -console -port 27015 \
    +map rc_deathmatch +maxplayers 16 +sv_lan 0 +exec server.cfg
  echo "Server crashed/restarted at $(date)" >> restart.log
  sleep 5
done
</code></pre>
<p>Run it inside <code>tmux</code> or <code>screen</code>, or convert to a <code>systemd</code> service for automatic boot.</p>

<h2 id="maintenance">Maintenance & Best Practices</h2>
<ul>
    <li><strong>Updates:</strong> rerun <code>steamcmd +login anonymous +app_update 90 validate +quit</code> after Valve pushes patches.</li>
    <li><strong>Backups:</strong> copy the entire <code>ricochet/</code> directory (configs, logs, demos) before experimenting with mods.</li>
    <li><strong>Performance:</strong> keep <code>sys_ticrate</code> around 300–500; higher values increase CPU usage without visible benefit.</li>
    <li><strong>Security:</strong> never reuse the same RCON password across servers; restrict RCON by IP using host firewalls when possible.</li>
    <li><strong>Monitoring:</strong> enable <code>log on</code> and parse logs for <code>Banid</code>, <code>kick</code>, or crash traces.</li>
</ul>

<h2 id="troubleshooting">Troubleshooting</h2>
<div style="background:#78350f;padding:20px;border-left:4px solid #f59e0b;margin:20px 0;border-radius:4px;">
    <ul style="color:#fde68a;line-height:1.8;">
        <li><strong>Server does not appear in Steam listings:</strong> confirm <code>sv_lan 0</code>, firewall allows outbound UDP 27010-27012, and public IP is bound via <code>-ip</code>. Restart after making changes.</li>
        <li><strong>Players get "Server is out of date":</strong> run a fresh SteamCMD update with <code>validate</code>. Old binaries are the primary cause.</li>
        <li><strong>Packet loss or warping:</strong> reduce <code>sv_maxrate</code> and <code>sv_maxupdaterate</code>, and ensure the host is not power-saving the CPU. Monitor with <code>net_graph 3</code> in-game.</li>
        <li><strong>Crash on launch in 64-bit Linux:</strong> missing 32-bit libraries. Install <code>lib32gcc-s1</code>, <code>lib32stdc++6</code>, and <code>lib32z1</code> (Debian/Ubuntu) or <code>glibc.i686</code> packages (RHEL).</li>
        <li><strong>RCON brute-force noise:</strong> move RCON to a high random port using <code>-port</code> + <code>+rcon_password</code> and limit access with firewall source rules.</li>
    </ul>
</div>

<h2 id="resources">Reference Links</h2>
<ul>
    <li><a href="https://developer.valvesoftware.com/wiki/Ricochet_Dedicated_Server" target="_blank">Valve Developer Wiki – Ricochet Dedicated Server</a></li>
    <li><a href="https://developer.valvesoftware.com/wiki/Half-Life_Dedicated_Server" target="_blank">HLDS documentation and launch options</a></li>
    <li><a href="https://steamcommunity.com/app/60/discussions/" target="_blank">Steam Community Discussions (Ricochet)</a></li>
</ul>
