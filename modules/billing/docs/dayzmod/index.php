<?php
/**
 * DayZ Mod Documentation
 */
?>
<h1>📚 DayZ Mod for ARMA 2 Server Guide</h1>
<p style="font-size: 1.1em; color: rgba(255,255,255,0.8);">Zombie survival mod that revolutionized the genre</p>

<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Quick Info</h3>
    <table style="width: 100%; color: #e5e7eb;">
        <tr><td><strong style="color: #ffffff;">Base Game:</strong></td><td>ARMA 2: Operation Arrowhead + ARMA 2 (Combined Operations)</td></tr>
        <tr><td><strong style="color: #ffffff;">Version:</strong></td><td>DayZ Mod 1.9.0+ (final official release)</td></tr>
        <tr><td><strong style="color: #ffffff;">Database:</strong></td><td>MySQL/MariaDB required for character persistence</td></tr>
        <tr><td><strong style="color: #ffffff;">Server Type:</strong></td><td>Reality build + Hive server architecture</td></tr>
        <tr><td><strong style="color: #ffffff;">Max Players:</strong></td><td>Up to 100 (50-60 typical for performance)</td></tr>
        <tr><td><strong style="color: #ffffff;">Anti-Cheat:</strong></td><td>BattlEye required</td></tr>
        <tr><td><strong style="color: #ffffff;">Port:</strong></td><td>2302 UDP (game), 2303 UDP (query)</td></tr>
    </table>
</div>

<h2>Navigation</h2>
<ul style="list-style: none; padding: 0;">
    <li>📚 <a href="#overview">Overview</a></li>
    <li>💾 <a href="#database">Database Setup</a></li>
    <li>📥 <a href="#installation">Server Installation</a></li>
    <li>🔧 <a href="#hive">Hive Server Configuration</a></li>
    <li>⚙️ <a href="#armaserver">ARMA Server Configuration</a></li>
    <li>🛡️ <a href="#battleye">BattlEye Filters</a></li>
    <li>🗺️ <a href="#maps">Maps & Variants</a></li>
    <li>🚀 <a href="#startup">Startup & Launch</a></li>
    <li>🔧 <a href="#troubleshooting">Troubleshooting</a></li>
    <li>📖 <a href="#resources">Resources</a></li>
</ul>

<h2 id="overview">Overview</h2>
<p>DayZ Mod is the original zombie survival mod for ARMA 2 that popularized the survival genre. Players spawn on a massive map with minimal gear, scavenging for supplies while avoiding zombies and other players in a persistent open world.</p>

<h3>Key Features</h3>
<ul>
    <li><strong>Persistent Characters:</strong> MySQL database stores player data (position, inventory, health)</li>
    <li><strong>225 km² Map:</strong> Chernarus provides massive open-world gameplay</li>
    <li><strong>Survival Mechanics:</strong> Hunger, thirst, blood, temperature systems</li>
    <li><strong>Permadeath:</strong> Death means starting over from the coast</li>
    <li><strong>Zombies (Infected):</strong> AI-controlled threats across the map</li>
    <li><strong>Vehicle Persistence:</strong> Database-stored vehicle locations</li>
    <li><strong>Base Building:</strong> Limited tent/vehicle storage systems</li>
</ul>

<h3>Architecture Overview</h3>
<p>DayZ Mod uses a unique two-server architecture:</p>
<ul>
    <li><strong>ARMA 2 Server:</strong> Runs the game world and handles clients</li>
    <li><strong>Hive Server (HiveExt.dll):</strong> Connects ARMA to MySQL database for persistence</li>
    <li><strong>MySQL Database:</strong> Stores all persistent data (characters, vehicles, objects)</li>
</ul>

<h2 id="database">💾 Database Setup</h2>

<h3>Installing MySQL/MariaDB</h3>

<h4>Ubuntu/Debian</h4>
<pre><code># Install MariaDB
sudo apt-get update
sudo apt-get install mariadb-server mariadb-client

# Secure installation
sudo mysql_secure_installation

# Start service
sudo systemctl start mariadb
sudo systemctl enable mariadb
</code></pre>

<h4>Windows</h4>
<pre><code># Download MySQL Community Server from mysql.com
# Or use XAMPP which includes MySQL

# Install MySQL Server
# Set root password during installation
# Ensure MySQL service is running
</code></pre>

<h3>Creating DayZ Database</h3>
<pre><code># Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE dayz_epoch CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create user with permissions
CREATE USER 'dayz'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON dayz_epoch.* TO 'dayz'@'localhost';
FLUSH PRIVILEGES;

# If Hive is on different machine
CREATE USER 'dayz'@'%' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON dayz_epoch.* TO 'dayz'@'%';
FLUSH PRIVILEGES;
</code></pre>

<h3>Importing DayZ Schema</h3>
<p>DayZ Mod includes SQL schema files in the database folder:</p>
<pre><code># Import the schema (adjust path to your installation)
mysql -u root -p dayz_epoch < path/to/dayz_server/SQL/dayz_schema.sql

# Or import via MySQL client
mysql -u root -p
USE dayz_epoch;
SOURCE /path/to/dayz_server/SQL/dayz_schema.sql;
</code></pre>

<h3>Database Tables (Key Tables)</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Table</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Purpose</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>character_data</code></td>
            <td style="padding: 12px;">Player characters (inventory, position, stats)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>survivor_data</code></td>
            <td style="padding: 12px;">Survivor profile information</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>object_data</code></td>
            <td style="padding: 12px;">Persistent objects (tents, storage, vehicles)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>player_data</code></td>
            <td style="padding: 12px;">Player login tracking</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>instance_deployable</code></td>
            <td style="padding: 12px;">Deployed items (wire, tank traps)</td>
        </tr>
    </tbody>
</table>

<h2 id="installation">📥 Server Installation</h2>

<h3>Prerequisites</h3>
<ul>
    <li>ARMA 2 Operation Arrowhead dedicated server</li>
    <li>ARMA 2 base game files (for Combined Operations)</li>
    <li>MySQL/MariaDB database server</li>
    <li>DayZ Mod server files</li>
    <li>Reality build files</li>
</ul>

<h3>Linux Installation</h3>
<pre><code># Install ARMA 2 OA server via SteamCMD
steamcmd +force_install_dir ~/arma2oa_server +login YOUR_USERNAME +app_update 33935 validate +quit

# Download DayZ Mod server files
# Available from DayZ GitHub: https://github.com/DayZMod/DayZ
# Or DayZ Launcher repositories

# Extract DayZ files
cd ~/arma2oa_server
unzip dayz_server_files.zip

# Install Reality build (custom ARMA 2 build for DayZ)
# Copy @Reality folder to server root
cp -r /path/to/@Reality ~/arma2oa_server/

# Install HiveExt
# Copy HiveExt.so to server root
cp HiveExt.so ~/arma2oa_server/

# Set permissions
chmod +x ~/arma2oa_server/arma2oaserver
chmod +x ~/arma2oa_server/HiveExt.so
</code></pre>

<h3>Windows Installation</h3>
<pre><code># Install ARMA 2 OA server via SteamCMD
steamcmd +force_install_dir C:\arma2oa_server +login YOUR_USERNAME +app_update 33935 validate +quit

# Download and extract DayZ Mod server files
# Extract to C:\arma2oa_server\

# Install @Reality folder
# Copy to C:\arma2oa_server\@Reality\

# Install HiveExt.dll
# Copy to C:\arma2oa_server\
</code></pre>

<h2 id="hive">🔧 Hive Server Configuration</h2>

<h3>HiveExt.ini</h3>
<p>Configure <code>HiveExt.ini</code> in server root:</p>
<pre><code>[Database]
;Hostname or IP of the MySQL server
Host = 127.0.0.1

;Port to connect to MySQL (default 3306)
Port = 3306

;Database name
Database = dayz_epoch

;Username
Username = dayz

;Password
Password = your_secure_password

[Time]
;Type of time to use
;Possible values:
; Local (use server machine time)
; Static (use fixed time from config)
; Custom (use time from database)
Type = Local

;If Type is Static, set the time here (24-hour format)
Hour = 8
Minute = 0

[Logging]
;Enable detailed logging for debugging
;0 = Off, 1 = Errors only, 2 = All
LogLevel = 1
LogFile = HiveExt.log
</code></pre>

<h3>Database Connection Test</h3>
<p>Test database connectivity before starting server:</p>
<pre><code># Linux
telnet 127.0.0.1 3306

# Windows
Test-NetConnection -ComputerName 127.0.0.1 -Port 3306

# Or use MySQL client
mysql -h 127.0.0.1 -u dayz -p dayz_epoch
</code></pre>

<h2 id="armaserver">⚙️ ARMA Server Configuration</h2>

<h3>server.cfg</h3>
<p>Basic ARMA 2 server configuration:</p>
<pre><code>hostname = "My DayZ Server";
password = "";
passwordAdmin = "AdminPassword";
serverCommandPassword = "RconPassword";

maxPlayers = 50;
kickDuplicate = 1;
verifySignatures = 2;  // BattlEye enforcement
BattlEye = 1;

persistent = 1;  // Required for DayZ persistence

// Mission file
class Missions
{
    class DayZ
    {
        template = dayz_1.chernarus;
        difficulty = "regular";
    };
};

motd[] = {
    "Welcome to My DayZ Server",
    "Visit our website: example.com"
};

motdInterval = 300;

disableVoN = 0;  // Voice enabled
vonCodecQuality = 10;

// Performance settings
MinBandwidth = 768000;
MaxBandwidth = 10000000;
MaxMsgSend = 256;
MaxSizeGuaranteed = 512;
MaxSizeNonguaranteed = 256;
MinErrorToSend = 0.001;
MinErrorToSendNear = 0.01;
</code></pre>

<h3>Mission Folder Structure</h3>
<pre><code>MPMissions/
└── dayz_1.chernarus/
    ├── mission.sqm          // Mission file (player spawn, time settings)
    ├── init.sqf             // Initialization script
    ├── description.ext      // Mission description and parameters
    ├── scripts/             // Custom scripts folder
    └── addons/              // Mission-specific addons
</code></pre>

<h3>init.sqf (Sample)</h3>
<pre><code>// DayZ initialization
startLoadingScreen ["","RscDisplayLoadCustom"];
cutText ["","BLACK OUT"];
enableSaving [false, false];

// Variables
dayZ_instance = 1;  // Server instance ID
dayzHiveRequest = [];
initialized = false;

// Load DayZ
call compile preprocessFileLineNumbers "\z\addons\dayz_code\init\variables.sqf";
progressLoadingScreen 0.1;
call compile preprocessFileLineNumbers "\z\addons\dayz_code\init\publicEH.sqf";
progressLoadingScreen 0.2;
call compile preprocessFileLineNumbers "\z\addons\dayz_code\medical\setup_functions_med.sqf";
progressLoadingScreen 0.4;
call compile preprocessFileLineNumbers "\z\addons\dayz_code\init\compiles.sqf";
progressLoadingScreen 0.5;

// Server-side initialization
if (isServer) then {
    call compile preprocessFileLineNumbers "\z\addons\dayz_server\system\dynamic_vehicle.sqf";
    call compile preprocessFileLineNumbers "\z\addons\dayz_server\system\server_monitor.sqf";
};

progressLoadingScreen 1.0;
cutText ["","BLACK IN", 1];
</code></pre>

<h2 id="battleye">🛡️ BattlEye Filters</h2>

<h3>BattlEye Configuration</h3>
<p>BattlEye filters in <code>BattlEye/</code> folder prevent exploits and hacks. DayZ-specific filters are critical.</p>

<h4>Key Filter Files</h4>
<ul>
    <li><strong>scripts.txt</strong> - Blocks malicious scripts (most important)</li>
    <li><strong>publicvariable.txt</strong> - Filters public variable injections</li>
    <li><strong>remoteexec.txt</strong> - Prevents remote execution exploits</li>
    <li><strong>createvehicle.txt</strong> - Controls vehicle spawning</li>
    <li><strong>setpos.txt</strong> - Limits teleportation exploits</li>
    <li><strong>setvariable.txt</strong> - Filters variable setting</li>
</ul>

<h4>Updating Filters</h4>
<pre><code># Download latest filters from community sources
# Recommended: DayZ GitHub or DZMS filter packs

# Example: Download from GitHub
wget https://github.com/DayZMod/DayZ/tree/master/BattlEye -O battleye_filters.zip
unzip battleye_filters.zip -d BattlEye/

# Always backup existing filters before updating!
cp -r BattlEye/ BattlEye_backup_$(date +%Y%m%d)/
</code></pre>

<h3>Common BattlEye Issues</h3>
<ul>
    <li><strong>Players kicked on spawn:</strong> scripts.txt too strict - check logs and add exceptions</li>
    <li><strong>Vehicle spawn kicks:</strong> createvehicle.txt blocking legitimate vehicles</li>
    <li><strong>Admin tool kicks:</strong> Whitelist admin scripts in filters</li>
</ul>

<h2 id="maps">🗺️ Maps & Variants</h2>

<h3>Official Maps</h3>
<ul>
    <li><strong>Chernarus</strong> - Original 225 km² map (default)</li>
    <li><strong>Lingor</strong> - Tropical island map</li>
    <li><strong>Takistan</strong> - Desert environment</li>
    <li><strong>Namalsk</strong> - Arctic island with unique hazards</li>
    <li><strong>Panthera</strong> - Large European-style map</li>
    <li><strong>Celle</strong> - German countryside</li>
</ul>

<h3>Map Installation</h3>
<p>Additional maps require mission files and sometimes map addons:</p>
<pre><code># Install map addon (e.g., @Namalsk)
cp -r @Namalsk ~/arma2oa_server/

# Add to startup parameters
-mod=@Reality;@DayZ;@Namalsk

# Install mission file
cp -r dayz_1.namalsk MPMissions/
</code></pre>

<h2 id="startup">🚀 Startup & Launch</h2>

<h3>Linux Startup Script</h3>
<pre><code>#!/bin/bash
# start_dayz.sh

# Variables
SERVER_DIR="/home/user/arma2oa_server"
CONFIG="server.cfg"
PORT="2302"
MODS="-mod=@Reality;@DayZ"

cd $SERVER_DIR

# Start server
./arma2oaserver \
    $MODS \
    -port=$PORT \
    -config=$CONFIG \
    -cfg=basic.cfg \
    -profiles=profiles \
    -name=server \
    -world=empty \
    -nosound \
    -noCB \
    -cpuCount=4 \
    -exThreads=7
</code></pre>

<h3>Windows Startup Batch</h3>
<pre><code>@echo off
REM start_dayz.bat

SET SERVER_DIR=C:\arma2oa_server
SET MODS=-mod=@Reality;@DayZ
SET PORT=2302

cd %SERVER_DIR%

start "DayZ Server" /high arma2oaserver.exe ^
    %MODS% ^
    -port=%PORT% ^
    -config=server.cfg ^
    -cfg=basic.cfg ^
    -profiles=profiles ^
    -name=server ^
    -world=empty ^
    -cpuCount=4 ^
    -exThreads=7
</code></pre>

<h3>Startup Parameters Explained</h3>
<ul>
    <li><code>-mod=@Reality;@DayZ</code> - Load Reality and DayZ mods</li>
    <li><code>-port=2302</code> - Game port (default 2302)</li>
    <li><code>-config=server.cfg</code> - Server configuration file</li>
    <li><code>-profiles=profiles</code> - Profile folder for logs</li>
    <li><code>-world=empty</code> - Don't load world at startup (faster)</li>
    <li><code>-cpuCount=4</code> - Number of CPU cores to use</li>
    <li><code>-exThreads=7</code> - Extra threads for better performance</li>
</ul>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Hive Connection Failed</h3>
<pre><code># Check HiveExt.log for errors
tail -f HiveExt.log

# Common causes:
# 1. Wrong database credentials in HiveExt.ini
# 2. MySQL not running
# 3. Firewall blocking port 3306
# 4. Database name doesn't exist

# Test connection manually
mysql -h 127.0.0.1 -u dayz -p dayz_epoch

# Check MySQL is listening
netstat -an | grep 3306
</code></pre>

<h3>Players Spawn as Seagulls</h3>
<p>This indicates database connection issues:</p>
<ul>
    <li>Verify HiveExt.ini database settings</li>
    <li>Check character_data table exists and is accessible</li>
    <li>Ensure instance ID matches in database and mission file</li>
    <li>Check HiveExt.log for SQL errors</li>
</ul>

<h3>Server Crashes on Startup</h3>
<pre><code># Check server logs
tail -f arma2oaserver.rpt

# Common causes:
# 1. Missing @Reality or @DayZ files
# 2. Corrupted mission file
# 3. Wrong mod load order
# 4. Insufficient RAM (4GB+ recommended)

# Verify mod folders exist
ls -la @Reality
ls -la @DayZ

# Check mission file syntax
# mission.sqm must be valid ARMA format
</code></pre>

<h3>BattlEye Kicking All Players</h3>
<ul>
    <li>Check BattlEye logs in <code>BattlEye/</code> folder</li>
    <li>Update BattlEye filters to latest version</li>
    <li>Temporarily disable specific filters to identify issue</li>
    <li>Add exceptions for legitimate scripts/actions</li>
</ul>

<h3>Character Data Not Saving</h3>
<pre><code># Verify database connection
mysql -u dayz -p dayz_epoch

# Check character_data table
SELECT * FROM character_data LIMIT 10;

# Verify HiveExt is loading
grep "HiveExt" arma2oaserver.rpt

# Check for SQL errors in HiveExt.log
grep "ERROR" HiveExt.log
</code></pre>

<h3>Poor Server Performance</h3>
<ul>
    <li>Reduce max players (50-60 optimal)</li>
    <li>Increase server RAM (8GB+ recommended)</li>
    <li>Use SSD storage for database</li>
    <li>Optimize MySQL with proper indexes</li>
    <li>Limit AI zombies via mission settings</li>
    <li>Clean old objects from database regularly</li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-lightbulb" style="color: #fbbf24; margin-right: 8px;"></i>Pro Tips</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Database Backups:</strong> Automate daily MySQL backups - character loss is devastating</li>
        <li><strong>BattlEye Updates:</strong> Keep filters current to prevent exploits</li>
        <li><strong>Hive Monitoring:</strong> Watch HiveExt.log in real-time during launches</li>
        <li><strong>Restart Schedule:</strong> Daily restarts clean up objects and improve performance</li>
        <li><strong>Player Limit:</strong> 50-60 players maximum for smooth gameplay</li>
        <li><strong>Custom Scripts:</strong> Always test in dev environment first</li>
        <li><strong>Community Support:</strong> DayZ forums and Discord are very active</li>
        <li><strong>Version Lock:</strong> Keep server and client versions synchronized</li>
    </ul>
</div>

<h2 id="resources">Resources</h2>
<ul>
    <li><a href="https://github.com/DayZMod/DayZ" target="_blank">Official DayZ Mod GitHub</a></li>
    <li><a href="https://dayzlauncher.com/" target="_blank">DayZ Launcher (Client)</a></li>
    <li><a href="https://forums.dayz.com/forum/102-mod-servers-troubleshooting/" target="_blank">DayZ Mod Server Forums</a></li>
    <li><a href="https://www.reddit.com/r/dayzmod/" target="_blank">r/dayzmod - Reddit Community</a></li>
    <li><a href="https://github.com/oiad/DayZMissionEditor" target="_blank">DayZ Mission Editor</a></li>
    <li><a href="../arma2oa/">ARMA 2 OA Server Documentation</a> - Base server setup</li>
    <li><a href="../arma2co/">ARMA 2 CO Server Documentation</a> - Combined Operations</li>
</ul>