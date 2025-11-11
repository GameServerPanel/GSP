<?php
/**
 * B3 (Big Brother Bot) Documentation
 */
?>
<h1>📚 B3 (Big Brother Bot) Guide</h1>
<p style="font-size: 1.1em; color: rgba(255,255,255,0.8);">Complete admin automation system for COD and Battlefield servers</p>

<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Quick Info</h3>
    <table style="width: 100%; color: #e5e7eb;">
        <tr><td><strong style="color: #ffffff;">Supported Games:</strong></td><td>Call of Duty series, Battlefield series, Urban Terror</td></tr>
        <tr><td><strong style="color: #ffffff;">Language:</strong></td><td>Python 2.7 (legacy) or Python 3.x (BigBrotherBot 2.0+)</td></tr>
        <tr><td><strong style="color: #ffffff;">Database:</strong></td><td>MySQL or SQLite for stats/bans/warnings</td></tr>
        <tr><td><strong style="color: #ffffff;">Communication:</strong></td><td>RCON protocol to game server</td></tr>
        <tr><td><strong style="color: #ffffff;">Web Interface:</strong></td><td>Optional XLRSTATS/ECHELON integration</td></tr>
        <tr><td><strong style="color: #ffffff;">Latest Version:</strong></td><td>B3 1.12+ or BigBrotherBot 2.0+</td></tr>
        <tr><td><strong style="color: #ffffff;">Website:</strong></td><td>bigbrotherbot.net</td></tr>
    </table>
</div>

<h2>Navigation</h2>
<ul style="list-style: none; padding: 0;">
    <li>📚 <a href="#overview">Overview</a></li>
    <li>📥 <a href="#installation">Installation</a></li>
    <li>💾 <a href="#database">Database Setup</a></li>
    <li>⚙️ <a href="#configuration">Configuration (b3.xml)</a></li>
    <li>🔌 <a href="#plugins">Plugin System</a></li>
    <li>👤 <a href="#admin">Admin Management</a></li>
    <li>📊 <a href="#stats">Statistics & Web Interface</a></li>
    <li>🎮 <a href="#gamespecific">Game-Specific Setup</a></li>
    <li>🔧 <a href="#troubleshooting">Troubleshooting</a></li>
    <li>📖 <a href="#resources">Resources</a></li>
</ul>

<h2 id="overview">Overview</h2>
<p>B3 (Big Brother Bot) is a cross-platform, cross-game administration tool for game servers. It provides automated player warnings, kick/ban management, statistics tracking, and extensive customization through plugins.</p>

<h3>Key Features</h3>
<ul>
    <li><strong>Automated Moderation:</strong> Auto-warn, kick, ban based on rules</li>
    <li><strong>Player Statistics:</strong> Track kills, deaths, skill ratings</li>
    <li><strong>Ban Management:</strong> Temporary and permanent bans with reasons</li>
    <li><strong>Warning System:</strong> Progressive punishment (warn → kick → ban)</li>
    <li><strong>RCON Wrapper:</strong> Secure RCON command execution</li>
    <li><strong>Plugin Architecture:</strong> Extensible with Python plugins</li>
    <li><strong>Web Interface:</strong> View stats and manage server via web</li>
    <li><strong>Multi-Server:</strong> Manage multiple servers from one B3 instance</li>
</ul>

<h3>Supported Games</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Game Series</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Specific Titles</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Parser</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">Call of Duty</td>
            <td style="padding: 12px;">COD2, COD4, MW2, MW3, Black Ops</td>
            <td style="padding: 12px;">cod, cod4, cod7, etc.</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">Battlefield</td>
            <td style="padding: 12px;">BF2, BF2142, BF3, BF4, BFH</td>
            <td style="padding: 12px;">bf3, bf4, bfh</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">Urban Terror</td>
            <td style="padding: 12px;">Urban Terror 4.x</td>
            <td style="padding: 12px;">iourt42</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">Smokin' Guns</td>
            <td style="padding: 12px;">Smokin' Guns</td>
            <td style="padding: 12px;">smg</td>
        </tr>
    </tbody>
</table>

<h2 id="installation">📥 Installation</h2>

<h3>Prerequisites</h3>
<ul>
    <li><strong>Python:</strong> 2.7 (legacy B3) or 3.6+ (BigBrotherBot 2.0+)</li>
    <li><strong>Database:</strong> MySQL 5.5+ or SQLite 3</li>
    <li><strong>RCON Access:</strong> Game server RCON password</li>
    <li><strong>Operating System:</strong> Linux, Windows, or macOS</li>
</ul>

<h3>Linux Installation (Ubuntu/Debian)</h3>

<h4>Python 3 Installation (Recommended)</h4>
<pre><code># Install Python 3 and pip
sudo apt-get update
sudo apt-get install python3 python3-pip git

# Install B3 via pip (BigBrotherBot 2.0+)
pip3 install bigbrotherbot

# Or install from GitHub (development version)
git clone https://github.com/BigBrotherBot/big-brother-bot.git
cd big-brother-bot
pip3 install -r requirements.txt
python3 setup.py install
</code></pre>

<h4>Legacy Python 2.7 Installation</h4>
<pre><code># Install Python 2.7 (for older B3 1.x)
sudo apt-get install python2.7 python-pip

# Download B3 1.x
wget https://github.com/BigBrotherBot/big-brother-bot/archive/v1.12.0.tar.gz
tar -xzf v1.12.0.tar.gz
cd big-brother-bot-1.12.0

# Install dependencies
pip install -r requirements.txt
</code></pre>

<h3>Windows Installation</h3>
<pre><code># Download and install Python 3.9+ from python.org
# Ensure "Add Python to PATH" is checked during installation

# Open Command Prompt and install B3
pip install bigbrotherbot

# Or download pre-built B3 package from bigbrotherbot.net
# Extract to C:\B3\
</code></pre>

<h3>Directory Structure</h3>
<pre><code>b3/
├── b3.exe (Windows) or b3_run.py (Linux)
├── b3.xml (main configuration)
├── conf/
│   ├── b3.xml (main config)
│   ├── plugin_admin.ini (admin plugin config)
│   ├── plugin_spamcontrol.ini
│   └── ... (other plugin configs)
├── extplugins/
│   └── (custom plugins)
└── logs/
    └── b3.log
</code></pre>

<h2 id="database">💾 Database Setup</h2>

<h3>MySQL Setup (Recommended for Production)</h3>

<h4>Create Database</h4>
<pre><code># Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE b3 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create user
CREATE USER 'b3'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON b3.* TO 'b3'@'localhost';
FLUSH PRIVILEGES;
</code></pre>

<h4>Import B3 Schema</h4>
<pre><code># B3 will auto-create tables on first run
# Or manually import schema:
mysql -u b3 -p b3 < sql/b3.sql

# Key tables created:
# - clients (player records)
# - penalties (bans, warnings, kicks)
# - aliases (player name history)
# - ipaliases (IP address tracking)
</code></pre>

<h3>SQLite Setup (Simple / Testing)</h3>
<pre><code># SQLite doesn't require separate installation
# B3 will create b3.db file automatically
# Database file location: b3/b3.db

# No manual setup required
# Just configure b3.xml to use SQLite
</code></pre>

<h3>Database Configuration in b3.xml</h3>

<h4>MySQL Configuration</h4>
<pre><code>&lt;configuration&gt;
  &lt;settings name="settings"&gt;
    &lt;set name="database"&gt;mysql://b3:your_password@localhost/b3&lt;/set&gt;
  &lt;/settings&gt;
&lt;/configuration&gt;
</code></pre>

<h4>SQLite Configuration</h4>
<pre><code>&lt;configuration&gt;
  &lt;settings name="settings"&gt;
    &lt;set name="database"&gt;sqlite://b3/b3.db&lt;/set&gt;
  &lt;/settings&gt;
&lt;/configuration&gt;
</code></pre>

<h2 id="configuration">⚙️ Configuration (b3.xml)</h2>

<h3>Basic b3.xml Structure</h3>
<pre><code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;configuration&gt;
  &lt;!-- Settings Section --&gt;
  &lt;settings name="settings"&gt;
    &lt;!-- Database connection --&gt;
    &lt;set name="database"&gt;mysql://b3:password@localhost/b3&lt;/set&gt;
    
    &lt;!-- Parser (game type) --&gt;
    &lt;set name="parser"&gt;cod4&lt;/set&gt;
    
    &lt;!-- Timezone --&gt;
    &lt;set name="time_zone"&gt;America/New_York&lt;/set&gt;
    
    &lt;!-- Logging level (0-22, higher = more verbose) --&gt;
    &lt;set name="log_level"&gt;9&lt;/set&gt;
    
    &lt;!-- Log file --&gt;
    &lt;set name="logfile"&gt;b3.log&lt;/set&gt;
  &lt;/settings&gt;

  &lt;!-- B3 Server Section --&gt;
  &lt;settings name="server"&gt;
    &lt;!-- Public IP and Port --&gt;
    &lt;set name="public_ip"&gt;123.456.789.0&lt;/set&gt;
    &lt;set name="port"&gt;28960&lt;/set&gt;
    
    &lt;!-- RCON Configuration --&gt;
    &lt;set name="rcon_ip"&gt;127.0.0.1&lt;/set&gt;
    &lt;set name="rcon_port"&gt;28960&lt;/set&gt;
    &lt;set name="rcon_password"&gt;your_rcon_password&lt;/set&gt;
    
    &lt;!-- Game log file location --&gt;
    &lt;set name="game_log"&gt;games_mp.log&lt;/set&gt;
  &lt;/settings&gt;

  &lt;!-- Plugins Section --&gt;
  &lt;plugins&gt;
    &lt;plugin name="admin" config="@conf/plugin_admin.ini"/&gt;
    &lt;plugin name="adv" config="@conf/plugin_adv.xml"/&gt;
    &lt;plugin name="spamcontrol" config="@conf/plugin_spamcontrol.ini"/&gt;
    &lt;plugin name="status" config="@conf/plugin_status.ini"/&gt;
    &lt;plugin name="welcome" config="@conf/plugin_welcome.ini"/&gt;
  &lt;/plugins&gt;
&lt;/configuration&gt;
</code></pre>

<h3>Parser Settings (Game-Specific)</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Game</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Parser Value</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">Call of Duty 4</td>
            <td style="padding: 12px;"><code>cod4</code></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">Call of Duty 7 (Black Ops)</td>
            <td style="padding: 12px;"><code>cod7</code></td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">Battlefield 3</td>
            <td style="padding: 12px;"><code>bf3</code></td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">Battlefield 4</td>
            <td style="padding: 12px;"><code>bf4</code></td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">Urban Terror 4.2</td>
            <td style="padding: 12px;"><code>iourt42</code></td>
        </tr>
    </tbody>
</table>

<h2 id="plugins">🔌 Plugin System</h2>

<h3>Built-in Plugins</h3>

<h4>Admin Plugin (Required)</h4>
<p>Core admin commands: kick, ban, warn, tempban</p>
<pre><code>; plugin_admin.ini
[settings]
; Minimum level for kick command
command_kick_level: 40

; Minimum level for ban command
command_ban_level: 60

; Minimum level for tempban command
command_tempban_level: 40

[commands]
kick: 40
ban: 60
tempban: 40
unban: 60
</code></pre>

<h4>SpamControl Plugin</h4>
<p>Prevents chat spam and flooding</p>
<pre><code>; plugin_spamcontrol.ini
[settings]
; Maximum messages per timeframe
max_messages: 5

; Timeframe in seconds
timeframe: 10

; Action: warn, kick, ban
action: warn
</code></pre>

<h4>Welcome Plugin</h4>
<p>Welcomes players on join, shows rules</p>
<pre><code>; plugin_welcome.ini
[settings]
; Welcome message
welcome_msg: ^2Welcome ^7%s^2! Visit our website at example.com

; Show player count
show_playercount: yes

; Show server rules
newb_connections: 15
</code></pre>

<h4>Status Plugin</h4>
<p>Player info and statistics</p>
<pre><code>; Commands: !status, !xlrstats
; Shows player level, kills, deaths, skill
</code></pre>

<h3>External Plugins</h3>
<ul>
    <li><strong>PowerAdminUrt:</strong> Extended admin commands for Urban Terror</li>
    <li><strong>CallVote:</strong> Voting system for maps/kicks</li>
    <li><strong>FollowMe:</strong> Auto-balance teams</li>
    <li><strong>Xlrstats:</strong> Advanced statistics tracking</li>
    <li><strong>Geolocation:</strong> Show player country on join</li>
</ul>

<h3>Installing External Plugins</h3>
<pre><code># Download plugin (example: xlrstats)
cd b3/extplugins
wget https://example.com/xlrstats.py

# Add to b3.xml
&lt;plugin name="xlrstats" config="@b3/extplugins/conf/xlrstats.ini"/&gt;

# Create plugin config
cp xlrstats.ini.sample xlrstats.ini
nano xlrstats.ini

# Restart B3
</code></pre>

<h2 id="admin">👤 Admin Management</h2>

<h3>Admin Levels</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Level</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Title</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Permissions</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">0</td>
            <td style="padding: 12px;">User</td>
            <td style="padding: 12px;">No permissions</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">1</td>
            <td style="padding: 12px;">Regular</td>
            <td style="padding: 12px;">Basic commands (!help, !rules)</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">20</td>
            <td style="padding: 12px;">Moderator</td>
            <td style="padding: 12px;">!warn, !kick (lower levels)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">40</td>
            <td style="padding: 12px;">Admin</td>
            <td style="padding: 12px;">!kick, !tempban, !ban (temporary)</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">60</td>
            <td style="padding: 12px;">Full Admin</td>
            <td style="padding: 12px;">!ban (permanent), !unban</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">80</td>
            <td style="padding: 12px;">Senior Admin</td>
            <td style="padding: 12px;">!maprotate, !maprestart, !lookup</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">100</td>
            <td style="padding: 12px;">Super Admin</td>
            <td style="padding: 12px;">All commands, !putgroup, !leveltest</td>
        </tr>
    </tbody>
</table>

<h3>Adding Admins (In-Game)</h3>
<pre><code># Add admin level 40 (Admin)
!putgroup PlayerName admin

# Add admin level 60 (Full Admin)
!putgroup PlayerName fulladmin

# Add admin level 100 (Super Admin)
!putgroup PlayerName superadmin

# Remove admin
!putgroup PlayerName user
</code></pre>

<h3>Adding Admins (Database)</h3>
<pre><code># Direct database method
mysql -u b3 -p

USE b3;

# Find player's ID
SELECT id, name FROM clients WHERE name LIKE '%PlayerName%';

# Update player group (level 60 = Full Admin)
UPDATE clients SET group_bits = 60 WHERE id = PLAYER_ID;
</code></pre>

<h3>Common Admin Commands</h3>
<pre><code># Player management
!kick PlayerName [reason]
!ban PlayerName [reason]
!tempban PlayerName 1h [reason]  # 1 hour temp ban
!unban PlayerName

# Warnings
!warn PlayerName [reason]
!warntest PlayerName  # Check warn count

# Server management
!maprotate  # Change to next map
!maprestart  # Restart current map
!map de_dust2  # Change to specific map

# Information
!help  # Show available commands
!leveltest PlayerName  # Check player's admin level
!lookup PlayerName  # Player info and history
</code></pre>

<h2 id="stats">📊 Statistics & Web Interface</h2>

<h3>XLRstats Plugin</h3>
<p>Advanced statistics tracking plugin</p>
<pre><code># Install XLRstats plugin
cd b3/extplugins
# Download xlrstats.py and xlrstats.ini

# Add to b3.xml
&lt;plugin name="xlrstats" config="@b3/extplugins/conf/xlrstats.ini"/&gt;

# Commands:
!xlrstats  # Show your stats
!xlrtopstats  # Show top players
!xlrstats PlayerName  # Show another player's stats
</code></pre>

<h3>ECHELON Web Interface</h3>
<p>Web-based B3 management interface</p>
<ul>
    <li><strong>Features:</strong> View stats, manage bans, configure server</li>
    <li><strong>Requirements:</strong> PHP 5.3+, MySQL, web server</li>
    <li><strong>Installation:</strong> Download from b3.echelon.fi</li>
    <li><strong>Setup:</strong> Configure database connection to same B3 database</li>
</ul>

<h2 id="gamespecific">🎮 Game-Specific Setup</h2>

<h3>Call of Duty 4</h3>
<pre><code>&lt;!-- COD4 b3.xml example --&gt;
&lt;set name="parser"&gt;cod4&lt;/set&gt;
&lt;set name="rcon_port"&gt;28960&lt;/set&gt;
&lt;set name="game_log"&gt;/path/to/cod4/main/games_mp.log&lt;/set&gt;

# Enable log file in COD4 server.cfg:
set g_log "games_mp.log"
set g_logsync "1"  # Real-time logging
set rcon_password "your_rcon_password"
</code></pre>

<h3>Battlefield 4</h3>
<pre><code>&lt;!-- BF4 b3.xml example --&gt;
&lt;set name="parser"&gt;bf4&lt;/set&gt;
&lt;set name="rcon_port"&gt;47200&lt;/set&gt;

# BF4 uses different RCON protocol
# Requires BF4 parser-specific settings
</code></pre>

<h3>Urban Terror 4.2</h3>
<pre><code>&lt;!-- Urban Terror b3.xml example --&gt;
&lt;set name="parser"&gt;iourt42&lt;/set&gt;
&lt;set name="rcon_port"&gt;27960&lt;/set&gt;
&lt;set name="game_log"&gt;/path/to/urbanterror/q3ut4/games.log&lt;/set&gt;
</code></pre>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>B3 Not Connecting to Server</h3>
<pre><code># Check RCON password in b3.xml matches server
# Test RCON manually:
# COD4 example:
rcon_ip:port password status

# Verify game log file path is correct
tail -f /path/to/games_mp.log

# Check B3 log for connection errors
tail -f b3/logs/b3.log | grep ERROR
</code></pre>

<h3>Commands Not Working</h3>
<ul>
    <li>Verify admin plugin is loaded in b3.xml</li>
    <li>Check player's admin level (!leveltest PlayerName)</li>
    <li>Ensure command prefix is correct (default: !)</li>
    <li>Check plugin_admin.ini for command permissions</li>
</ul>

<h3>Database Connection Failed</h3>
<pre><code># MySQL connection test
mysql -h 127.0.0.1 -u b3 -p b3

# Check database URL format in b3.xml:
mysql://username:password@host/database
# or
sqlite://path/to/b3.db

# Verify MySQL is running
systemctl status mysql
</code></pre>

<h3>High CPU Usage</h3>
<ul>
    <li>Reduce log_level in b3.xml (try level 9 or lower)</li>
    <li>Disable unused plugins</li>
    <li>Check for plugin loops or errors in logs</li>
    <li>Optimize database (run OPTIMIZE TABLE on large tables)</li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-lightbulb" style="color: #fbbf24; margin-right: 8px;"></i>Pro Tips</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>MySQL Recommended:</strong> Use MySQL for production servers (better performance than SQLite)</li>
        <li><strong>Log Rotation:</strong> Implement log rotation to prevent b3.log from growing too large</li>
        <li><strong>Test Configuration:</strong> Test b3.xml syntax before restarting B3</li>
        <li><strong>Backup Database:</strong> Regular automated backups of B3 database</li>
        <li><strong>Screen/Tmux:</strong> Run B3 in screen/tmux session on Linux for persistence</li>
        <li><strong>Monitor Performance:</strong> Watch CPU/RAM usage, adjust log level if needed</li>
        <li><strong>Plugin Updates:</strong> Keep plugins updated for bug fixes and new features</li>
        <li><strong>Community:</strong> B3 forums have extensive plugin library and support</li>
    </ul>
</div>

<h2 id="resources">Resources</h2>
<ul>
    <li><a href="https://www.bigbrotherbot.net/" target="_blank">Official B3 Website</a></li>
    <li><a href="https://github.com/BigBrotherBot/big-brother-bot" target="_blank">B3 GitHub Repository</a></li>
    <li><a href="https://forum.bigbrotherbot.net/" target="_blank">B3 Support Forums</a></li>
    <li><a href="https://github.com/BigBrotherBot/big-brother-bot/wiki" target="_blank">B3 Wiki Documentation</a></li>
    <li><a href="http://www.b3.echelon.fi/" target="_blank">ECHELON Web Interface</a></li>
    <li>Game Documentation: COD4, BF3, BF4 server setup guides</li>
</ul>
