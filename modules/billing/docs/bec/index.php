<?php
/**
 * BEC (BattlEye Extended Controls) Documentation
 */
?>
<h1>📚 BEC (BattlEye Extended Controls) Guide</h1>
<p style="font-size: 1.1em; color: rgba(255,255,255,0.8);">Scheduler and admin tool for ARMA series servers</p>

<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Quick Info</h3>
    <table style="width: 100%; color: #e5e7eb;">
        <tr><td><strong style="color: #ffffff;">Supported Games:</strong></td><td>ARMA 2, ARMA 2 OA, ARMA 2 CO, ARMA 3, DayZ Standalone</td></tr>
        <tr><td><strong style="color: #ffffff;">Platform:</strong></td><td>Windows (64-bit) and Linux</td></tr>
        <tr><td><strong style="color: #ffffff;">RCON Protocol:</strong></td><td>BattlEye RCON wrapper</td></tr>
        <tr><td><strong style="color: #ffffff;">Config File:</strong></td><td>Bec.cfg (INI format)</td></tr>
        <tr><td><strong style="color: #ffffff;">Features:</strong></td><td>Scheduler, auto-restart, admin commands, log management</td></tr>
        <tr><td><strong style="color: #ffffff;">Latest Version:</strong></td><td>BEC 1.647+ (check GitHub releases)</td></tr>
        <tr><td><strong style="color: #ffffff;">Repository:</strong></td><td>github.com/TheGamingChief/BattlEye-Extended-Controls</td></tr>
    </table>
</div>

<h2>Navigation</h2>
<ul style="list-style: none; padding: 0;">
    <li>📚 <a href="#overview">Overview</a></li>
    <li>📥 <a href="#installation">Installation</a></li>
    <li>⚙️ <a href="#configuration">Configuration (Bec.cfg)</a></li>
    <li>📅 <a href="#scheduler">Scheduler System</a></li>
    <li>🔧 <a href="#commands">Admin Commands</a></li>
    <li>🔄 <a href="#autorestart">Auto-Restart on Crash</a></li>
    <li>📝 <a href="#logging">Log Management</a></li>
    <li>🎮 <a href="#gamespecific">Game-Specific Setup</a></li>
    <li>🔧 <a href="#troubleshooting">Troubleshooting</a></li>
    <li>📖 <a href="#resources">Resources</a></li>
</ul>

<h2 id="overview">Overview</h2>
<p>BEC (BattlEye Extended Controls) is a server administration tool for ARMA series games. It provides scheduled tasks, automatic restarts, admin commands, and enhanced BattlEye RCON functionality.</p>

<h3>Key Features</h3>
<ul>
    <li><strong>Scheduler:</strong> Execute commands at specific times or intervals</li>
    <li><strong>Auto-Restart:</strong> Automatically restart server on crash</li>
    <li><strong>Admin Commands:</strong> In-game admin commands via chat (!restart, !lock, !kick)</li>
    <li><strong>BattlEye Wrapper:</strong> Enhanced RCON interface for BattlEye</li>
    <li><strong>Log Management:</strong> Automatic log rotation and archiving</li>
    <li><strong>Message Broadcasting:</strong> Scheduled server messages</li>
    <li><strong>Player Kick/Ban:</strong> Manage players via RCON commands</li>
    <li><strong>Whitelist Support:</strong> Whitelist mode for private servers</li>
</ul>

<h3>Why Use BEC?</h3>
<ul>
    <li><strong>Automated Maintenance:</strong> Schedule restarts without manual intervention</li>
    <li><strong>Crash Recovery:</strong> Server automatically restarts if it crashes</li>
    <li><strong>Player Communication:</strong> Broadcast scheduled messages (restart warnings, events)</li>
    <li><strong>Admin Efficiency:</strong> In-game commands without external RCON tools</li>
    <li><strong>DayZ Essential:</strong> Critical for DayZ servers (database sync, loot spawns)</li>
</ul>

<h2 id="installation">📥 Installation</h2>

<h3>Windows Installation</h3>

<h4>Step 1: Download BEC</h4>
<pre><code># Download latest BEC from GitHub:
# https://github.com/TheGamingChief/BattlEye-Extended-Controls/releases

# Extract Bec.exe to your ARMA server directory
# Example: C:\ARMA3Server\BEC\
</code></pre>

<h4>Step 2: Directory Structure</h4>
<pre><code>ARMA3Server\
├── arma3server_x64.exe
├── BattlEye\
│   └── BEServer_x64.dll (or BEServer.dll for 32-bit)
└── BEC\
    ├── Bec.exe
    ├── Config\
    │   ├── Bec.cfg (main configuration)
    │   ├── Scheduler.xml (scheduled tasks)
    │   └── Config.cfg (admin list)
    └── Logs\
        └── (BEC logs stored here)
</code></pre>

<h4>Step 3: Create Bec.cfg</h4>
<pre><code># Copy Bec.cfg.example to Bec.cfg
# Or create new Bec.cfg with basic settings:

[Bec]
Ip = 127.0.0.1
Port = 2302
RConPassword = your_rcon_password
RestartServer = 1
</code></pre>

<h3>Linux Installation</h3>
<pre><code># BEC for Linux (32-bit and 64-bit available)
cd /home/arma3/server/BEC
wget https://github.com/TheGamingChief/.../BEC_Linux.tar.gz
tar -xzf BEC_Linux.tar.gz

# Make executable
chmod +x Bec

# Run BEC
./Bec -f Config/Bec.cfg
</code></pre>

<h2 id="configuration">⚙️ Configuration (Bec.cfg)</h2>

<h3>Complete Bec.cfg Example</h3>
<pre><code>[Bec]
# BattlEye RCON connection settings
Ip = 127.0.0.1
Port = 2302
RConPassword = your_rcon_password

# Auto-restart on crash (1 = yes, 0 = no)
RestartServer = 1

# Delay before restart attempt (seconds)
RestartDelay = 30

# Announce restart warnings (seconds before restart)
AnnounceRestartTime = 300

# Path to ARMA server executable (Windows)
ServerExePath = C:\ARMA3Server\arma3server_x64.exe

# Server startup parameters
ServerCommandLine = -config=server.cfg -port=2302 -profiles=SC -cfg=basic.cfg -name=SC

# Log rotation (delete logs older than X days)
LogRotation = 7

# Message of the Day (sent to players on join)
MessageOfTheDay = Welcome to our ARMA 3 server! Visit our website: example.com

# Whitelist mode (1 = enabled, 0 = disabled)
Whitelist = 0

# Ban list file
BanList = bans.txt

# Admins configuration file
AdminsFile = Config/Config.cfg

# Scheduler configuration file
SchedulerFile = Config/Scheduler.xml
</code></pre>

<h3>Config.cfg (Admin List)</h3>
<pre><code>[Admins]
# Format: BattlEye GUID = Admin Name
# Get GUID from BattlEye logs or !guid command

12345678901234567890123456789012 = AdminName1
98765432109876543210987654321098 = AdminName2

# Admin levels can be set (not all BEC versions support this)
# Level 1 = Basic commands (!say, !lock)
# Level 2 = Kick/ban commands
</code></pre>

<h2 id="scheduler">📅 Scheduler System</h2>

<h3>Scheduler.xml Structure</h3>
<pre><code>&lt;?xml version="1.0" encoding="UTF-8" standalone="yes" ?&gt;
&lt;Scheduler&gt;
  &lt;!-- Daily restart at 06:00 --&gt;
  &lt;job id="0"&gt;
    &lt;day&gt;1,2,3,4,5,6,7&lt;/day&gt;
    &lt;start&gt;06:00:00&lt;/start&gt;
    &lt;runtime&gt;000000&lt;/runtime&gt;
    &lt;loop&gt;0&lt;/loop&gt;
    &lt;cmd&gt;say -1 Server restart in 5 minutes!&lt;/cmd&gt;
  &lt;/job&gt;

  &lt;!-- Restart warning 1 minute before --&gt;
  &lt;job id="1"&gt;
    &lt;day&gt;1,2,3,4,5,6,7&lt;/day&gt;
    &lt;start&gt;06:04:00&lt;/start&gt;
    &lt;runtime&gt;000000&lt;/runtime&gt;
    &lt;loop&gt;0&lt;/loop&gt;
    &lt;cmd&gt;say -1 Server restarting in 1 minute! Save your progress!&lt;/cmd&gt;
  &lt;/job&gt;

  &lt;!-- Actual restart command --&gt;
  &lt;job id="2"&gt;
    &lt;day&gt;1,2,3,4,5,6,7&lt;/day&gt;
    &lt;start&gt;06:05:00&lt;/start&gt;
    &lt;runtime&gt;000000&lt;/runtime&gt;
    &lt;loop&gt;0&lt;/loop&gt;
    &lt;cmd&gt;#shutdown&lt;/cmd&gt;
  &lt;/job&gt;

  &lt;!-- Repeating message every 30 minutes --&gt;
  &lt;job id="3"&gt;
    &lt;day&gt;1,2,3,4,5,6,7&lt;/day&gt;
    &lt;start&gt;00:00:00&lt;/start&gt;
    &lt;runtime&gt;003000&lt;/runtime&gt;
    &lt;loop&gt;1&lt;/loop&gt;
    &lt;cmd&gt;say -1 Visit our Discord: discord.gg/example&lt;/cmd&gt;
  &lt;/job&gt;
&lt;/Scheduler&gt;
</code></pre>

<h3>Scheduler Field Definitions</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Field</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Description</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Example</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>&lt;day&gt;</code></td>
            <td style="padding: 12px;">Days of week (1=Mon, 7=Sun)</td>
            <td style="padding: 12px;"><code>1,2,3,4,5,6,7</code> (all days)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>&lt;start&gt;</code></td>
            <td style="padding: 12px;">Start time (HH:MM:SS)</td>
            <td style="padding: 12px;"><code>06:00:00</code> (6 AM)</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>&lt;runtime&gt;</code></td>
            <td style="padding: 12px;">Repeat interval (HHMMSS)</td>
            <td style="padding: 12px;"><code>003000</code> (30 minutes)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>&lt;loop&gt;</code></td>
            <td style="padding: 12px;">Loop task? (1=yes, 0=no)</td>
            <td style="padding: 12px;"><code>1</code> (repeating)</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>&lt;cmd&gt;</code></td>
            <td style="padding: 12px;">RCON command to execute</td>
            <td style="padding: 12px;"><code>say -1 Message</code></td>
        </tr>
    </tbody>
</table>

<h3>Common Scheduled Tasks</h3>

<h4>3-Hour Restart Cycle</h4>
<pre><code>&lt;!-- Restart every 3 hours (00:00, 03:00, 06:00, etc.) --&gt;
&lt;job id="10"&gt;
  &lt;day&gt;1,2,3,4,5,6,7&lt;/day&gt;
  &lt;start&gt;00:00:00&lt;/start&gt;
  &lt;runtime&gt;030000&lt;/runtime&gt;
  &lt;loop&gt;1&lt;/loop&gt;
  &lt;cmd&gt;say -1 Server restarting in 5 minutes!&lt;/cmd&gt;
&lt;/job&gt;
</code></pre>

<h4>Hourly Server Message</h4>
<pre><code>&lt;job id="11"&gt;
  &lt;day&gt;1,2,3,4,5,6,7&lt;/day&gt;
  &lt;start&gt;00:00:00&lt;/start&gt;
  &lt;runtime&gt;010000&lt;/runtime&gt;
  &lt;loop&gt;1&lt;/loop&gt;
  &lt;cmd&gt;say -1 Check out our website: example.com&lt;/cmd&gt;
&lt;/job&gt;
</code></pre>

<h2 id="commands">🔧 Admin Commands</h2>

<h3>In-Game Commands (Chat)</h3>
<pre><code># Server control
!restart            # Restart server immediately
!shutdown           # Shutdown server
!lock               # Lock server (no new players)
!unlock             # Unlock server

# Player management
!kick PlayerName [reason]    # Kick player
!ban PlayerName [reason]     # Ban player permanently
!tempban PlayerName minutes  # Temporary ban
!players                     # List connected players
!guid PlayerName             # Get player's BattlEye GUID

# Messages
!say Message                 # Send global message
!sayto PlayerName Message    # Send private message

# Server info
!uptime                      # Show server uptime
!version                     # Show BEC version
!loadscripts                 # Reload BattlEye scripts.txt
</code></pre>

<h3>RCON Commands (Console)</h3>
<pre><code># BattlEye RCON commands (via BEC)
say -1 Message                  # Global message
kick PlayerNumber [reason]      # Kick by player number
ban PlayerGUID [reason]         # Ban by GUID
removeBan BanNumber             # Remove ban entry
players                         # List players with numbers
bans                            # List active bans
missions                        # List available missions
loadScripts                     # Reload BattlEye filter scripts
#shutdown                       # Shutdown server
#restart                        # Restart server (if configured)
</code></pre>

<h2 id="autorestart">🔄 Auto-Restart on Crash</h2>

<h3>Windows Auto-Restart Setup</h3>
<pre><code># Bec.cfg settings for auto-restart
[Bec]
RestartServer = 1
RestartDelay = 30   # Wait 30 seconds before restart
ServerExePath = C:\ARMA3Server\arma3server_x64.exe
ServerCommandLine = -config=server.cfg -port=2302 -profiles=SC
</code></pre>

<h3>How Auto-Restart Works</h3>
<ol>
    <li>BEC monitors server process via RCON connection</li>
    <li>If RCON connection is lost, BEC waits <code>RestartDelay</code> seconds</li>
    <li>BEC checks if server process is still running</li>
    <li>If process is dead, BEC launches server using <code>ServerExePath</code> and <code>ServerCommandLine</code></li>
    <li>BEC reconnects to RCON and resumes monitoring</li>
</ol>

<h3>Restart Notifications</h3>
<pre><code># Scheduler.xml: Warning before scheduled restart
&lt;job id="20"&gt;
  &lt;day&gt;1,2,3,4,5,6,7&lt;/day&gt;
  &lt;start&gt;05:55:00&lt;/start&gt;
  &lt;runtime&gt;000000&lt;/runtime&gt;
  &lt;loop&gt;0&lt;/loop&gt;
  &lt;cmd&gt;say -1 Server restart in 5 minutes!&lt;/cmd&gt;
&lt;/job&gt;

&lt;job id="21"&gt;
  &lt;day&gt;1,2,3,4,5,6,7&lt;/day&gt;
  &lt;start&gt;05:58:00&lt;/start&gt;
  &lt;runtime&gt;000000&lt;/runtime&gt;
  &lt;loop&gt;0&lt;/loop&gt;
  &lt;cmd&gt;say -1 Server restart in 2 minutes! Save your progress!&lt;/cmd&gt;
&lt;/job&gt;

&lt;job id="22"&gt;
  &lt;day&gt;1,2,3,4,5,6,7&lt;/day&gt;
  &lt;start&gt;06:00:00&lt;/start&gt;
  &lt;runtime&gt;000000&lt;/runtime&gt;
  &lt;loop&gt;0&lt;/loop&gt;
  &lt;cmd&gt;#shutdown&lt;/cmd&gt;
&lt;/job&gt;
</code></pre>

<h2 id="logging">📝 Log Management</h2>

<h3>BEC Log Files</h3>
<pre><code>BEC\Logs\
├── Bec_YYYY-MM-DD_HH-MM-SS.log  # Main BEC log
├── Chat_YYYY-MM-DD.log           # Player chat log
└── Admin_YYYY-MM-DD.log          # Admin command log
</code></pre>

<h3>Log Rotation</h3>
<pre><code># Bec.cfg log rotation setting
LogRotation = 7   # Delete logs older than 7 days

# Manual log cleanup (Windows)
cd C:\ARMA3Server\BEC\Logs
forfiles /P . /S /M *.log /D -7 /C "cmd /c del @path"

# Manual log cleanup (Linux)
find /home/arma3/server/BEC/Logs -name "*.log" -mtime +7 -delete
</code></pre>

<h2 id="gamespecific">🎮 Game-Specific Setup</h2>

<h3>ARMA 2 / ARMA 2 OA</h3>
<pre><code># Bec.cfg for ARMA 2
ServerExePath = C:\ARMA2Server\arma2oaserver.exe
ServerCommandLine = -port=2302 -config=server.cfg -profiles=SC -mod=@DayZ

# Port must match server.cfg BattlEye port:
BattlEye = 1
RConPassword = your_rcon_password
RConPort = 2302
</code></pre>

<h3>ARMA 3</h3>
<pre><code># Bec.cfg for ARMA 3
ServerExePath = C:\ARMA3Server\arma3server_x64.exe
ServerCommandLine = -port=2302 -config=server.cfg -profiles=SC -cfg=basic.cfg

# 64-bit BattlEye DLL required
# Ensure BEServer_x64.dll is in BattlEye folder
</code></pre>

<h3>DayZ Standalone</h3>
<pre><code># Bec.cfg for DayZ Standalone
ServerExePath = C:\DayZServer\DayZServer_x64.exe
ServerCommandLine = -config=serverDZ.cfg -port=2302 -profiles=SC -BEpath=battleye

# DayZ uses different config format
# Ensure RCon settings in serverDZ.cfg match Bec.cfg
</code></pre>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>BEC Not Connecting to Server</h3>
<pre><code># Symptoms: "Failed to connect to server" in Bec log

# Verify RCON settings match server config
# ARMA 3 server.cfg:
BattlEye = 1;
RConPassword = "your_rcon_password";
RConPort = 2302;

# Bec.cfg:
Port = 2302
RConPassword = your_rcon_password

# Test RCON manually with BERConCLI or similar tool
</code></pre>

<h3>Scheduler Tasks Not Executing</h3>
<ul>
    <li>Verify Scheduler.xml syntax (valid XML, no typos)</li>
    <li>Check <code>&lt;day&gt;</code> matches current day (1=Monday, 7=Sunday)</li>
    <li>Ensure <code>&lt;start&gt;</code> time uses 24-hour format (HH:MM:SS)</li>
    <li>Check BEC log for scheduler errors</li>
    <li>Test commands manually via !say or RCON</li>
</ul>

<h3>Server Not Restarting After Crash</h3>
<pre><code># Check Bec.cfg settings:
RestartServer = 1  # Must be enabled
ServerExePath = [full path to server exe]
ServerCommandLine = [correct startup parameters]

# Verify server executable path is correct (Windows)
dir "C:\ARMA3Server\arma3server_x64.exe"

# Check BEC has permission to launch server
# Run BEC as administrator if needed (Windows)
</code></pre>

<h3>Admin Commands Not Working</h3>
<ul>
    <li>Verify your GUID is in Config.cfg [Admins] section</li>
    <li>Get your GUID using !guid command or from BattlEye logs</li>
    <li>Restart BEC after editing Config.cfg</li>
    <li>Check admin command prefix (default: !)</li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-lightbulb" style="color: #fbbf24; margin-right: 8px;"></i>Pro Tips</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Test Scheduler First:</strong> Test scheduled tasks with near-future times before deploying</li>
        <li><strong>Multiple Warnings:</strong> Use 10min, 5min, 2min, 1min restart warnings for player courtesy</li>
        <li><strong>DayZ Critical:</strong> For DayZ servers, regular restarts are essential for loot spawns and database sync</li>
        <li><strong>Backup Bec.cfg:</strong> Keep backup of working Bec.cfg before making changes</li>
        <li><strong>Monitor Logs:</strong> Regularly check BEC logs for errors or issues</li>
        <li><strong>GUID Database:</strong> Keep list of admin GUIDs in separate document for reference</li>
        <li><strong>Time Zone:</strong> BEC uses server's system time zone for scheduler</li>
        <li><strong>Graceful Restarts:</strong> Always use scheduled restarts, avoid manual !restart during peak hours</li>
    </ul>
</div>

<h2 id="resources">Resources</h2>
<ul>
    <li><a href="https://github.com/TheGamingChief/BattlEye-Extended-Controls" target="_blank">BEC Official GitHub</a></li>
    <li><a href="https://forums.bistudio.com/forums/topic/176536-bec-battleye-extended-controls/" target="_blank">BEC Forum Thread (Bohemia Interactive)</a></li>
    <li><a href="https://community.bistudio.com/wiki/BattlEye" target="_blank">BattlEye Wiki (Bohemia Interactive)</a></li>
    <li>Related Game Documentation: <a href="../arma2oa/">ARMA 2 OA</a>, <a href="../arma3/">ARMA 3</a>, <a href="../dayzmod/">DayZ Mod</a></li>
</ul>
