<?php
/**
 * DayZ Epoch Mod Documentation
 */
?>
<h1>📚 DayZ Epoch Mod Server Guide</h1>
<p style="font-size: 1.1em; color: rgba(255,255,255,0.8);">Enhanced DayZ with base building, traders, and persistent economy</p>

<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Quick Info</h3>
    <table style="width: 100%; color: #e5e7eb;">
        <tr><td><strong style="color: #ffffff;">Base Requirement:</strong></td><td>DayZ Mod 1.8.8+ (builds on DayZ)</td></tr>
        <tr><td><strong style="color: #ffffff;">Version:</strong></td><td>Epoch 1.0.6.2+ (final stable release)</td></tr>
        <tr><td><strong style="color: #ffffff;">Database:</strong></td><td>MySQL/MariaDB with complex schema</td></tr>
        <tr><td><strong style="color: #ffffff;">Key Features:</strong></td><td>Base building, trader NPCs, banking, vehicle spawns</td></tr>
        <tr><td><strong style="color: #ffffff;">Variants:</strong></td><td>Pure Epoch, Overpoch (Overwatch + Epoch)</td></tr>
        <tr><td><strong style="color: #ffffff;">Max Players:</strong></td><td>Up to 100 (50-70 optimal)</td></tr>
        <tr><td><strong style="color: #ffffff;">Port:</strong></td><td>2302 UDP (game), 2303 UDP (query)</td></tr>
    </table>
</div>

<h2>Navigation</h2>
<ul style="list-style: none; padding: 0;">
    <li>📚 <a href="#overview">Overview</a></li>
    <li>💾 <a href="#database">Database Setup</a></li>
    <li>📥 <a href="#installation">Server Installation</a></li>
    <li>🏗️ <a href="#basebuilding">Base Building System</a></li>
    <li>💰 <a href="#traders">Trader System</a></li>
    <li>🚗 <a href="#vehicles">Vehicle System</a></li>
    <li>💸 <a href="#banking">Banking System</a></li>
    <li>⚙️ <a href="#configuration">Configuration</a></li>
    <li>🔧 <a href="#troubleshooting">Troubleshooting</a></li>
    <li>📖 <a href="#resources">Resources</a></li>
</ul>

<h2 id="overview">Overview</h2>
<p>DayZ Epoch is an extensive modification built on top of DayZ Mod. It adds base building with plot poles, trader NPCs with a dynamic economy, banking system, enhanced vehicles, and custom loot tables. Epoch significantly extends DayZ's survival mechanics with persistent base construction and economy management.</p>

<h3>Key Features</h3>
<ul>
    <li><strong>Plot Pole System:</strong> Build bases with walls, floors, gates using plot poles for protection</li>
    <li><strong>Trader Cities:</strong> Safe zones with AI trader NPCs selling weapons, vehicles, supplies</li>
    <li><strong>Dynamic Economy:</strong> Prices fluctuate based on server supply/demand</li>
    <li><strong>Banking:</strong> Deposit coins at traders, safe storage with interest</li>
    <li><strong>Vehicle Spawns:</strong> Custom spawn system with database persistence</li>
    <li><strong>Enhanced Loot:</strong> Custom loot tables with gems, rare items, crafting materials</li>
    <li><strong>Crafting:</strong> Build storage boxes, sandbags, wire kits, metal parts</li>
</ul>

<h3>Epoch vs. Overpoch</h3>
<ul>
    <li><strong>Pure Epoch:</strong> Standard Epoch with vanilla DayZ Mod base</li>
    <li><strong>Overpoch:</strong> Combines Overwatch mod (military weapons/vehicles) with Epoch features</li>
    <li><strong>Performance:</strong> Pure Epoch lighter, Overpoch more content but heavier</li>
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
# Start MySQL service from Services panel
</code></pre>

<h3>Creating Epoch Database</h3>
<pre><code># Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE epoch CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create user with permissions
CREATE USER 'epoch'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON epoch.* TO 'epoch'@'localhost';
FLUSH PRIVILEGES;

# If Hive is on different machine
CREATE USER 'epoch'@'%' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON epoch.* TO 'epoch'@'%';
FLUSH PRIVILEGES;
</code></pre>

<h3>Importing Epoch Schema</h3>
<p>Epoch includes comprehensive SQL schema files:</p>
<pre><code># Import Epoch schema (adjust path)
mysql -u root -p epoch < path/to/epoch_server/SQL/epoch.sql

# Or import via MySQL client
mysql -u root -p
USE epoch;
SOURCE /path/to/epoch_server/SQL/epoch.sql;

# Import additional tables if using Overpoch
SOURCE /path/to/epoch_server/SQL/overpoch_additions.sql;
</code></pre>

<h3>Epoch Database Tables (Key Tables)</h3>
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
            <td style="padding: 12px;">Player characters (inventory, position, stats, coins)</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>object_data</code></td>
            <td style="padding: 12px;">Player-built objects (walls, floors, storage) and plot poles</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>player_data</code></td>
            <td style="padding: 12px;">Player login tracking and stats</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>trader_data</code></td>
            <td style="padding: 12px;">Trader inventory, prices, stock levels</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>vehicle_spawns</code></td>
            <td style="padding: 12px;">Vehicle spawn points and configurations</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;"><code>trader_tids</code></td>
            <td style="padding: 12px;">Trader item IDs and categories</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;"><code>player_login</code></td>
            <td style="padding: 12px;">Player authentication and banking</td>
        </tr>
    </tbody>
</table>

<h3>Database Maintenance</h3>
<p><strong>Critical:</strong> Epoch databases grow rapidly. Regular cleanup is essential.</p>
<pre><code># Cleanup old objects (run weekly)
DELETE FROM object_data WHERE Datestamp < DATE_SUB(NOW(), INTERVAL 30 DAY) AND CharacterID = 0;

# Remove abandoned vehicles
DELETE FROM object_data WHERE ObjectUID IN (
    SELECT ObjectUID FROM object_data 
    WHERE Classname LIKE '%_DZ' AND Damage = 1
);

# Optimize tables
OPTIMIZE TABLE object_data, character_data, trader_data;

# Backup before cleanup!
mysqldump -u epoch -p epoch > epoch_backup_$(date +%Y%m%d).sql
</code></pre>

<h2 id="installation">📥 Server Installation</h2>

<h3>Prerequisites</h3>
<ul>
    <li>DayZ Mod 1.8.8+ server files installed</li>
    <li>ARMA 2 Operation Arrowhead + ARMA 2 (Combined Operations)</li>
    <li>MySQL/MariaDB database server configured</li>
    <li>Epoch Mod server files (from EpochMod.com)</li>
    <li>HiveExt configured for Epoch database</li>
</ul>

<h3>Linux Installation</h3>
<pre><code># Assumes DayZ Mod already installed at ~/arma2oa_server

# Download Epoch server files
cd ~/arma2oa_server
wget https://github.com/EpochModTeam/DayZ-Epoch/releases/download/1.0.6.2/DayZ_Epoch_Server_1.0.6.2.zip
unzip DayZ_Epoch_Server_1.0.6.2.zip

# Extract Epoch files
# Copy @DayZ_Epoch folder to server root
cp -r DayZ_Epoch_Server_1.0.6.2/@DayZ_Epoch ~/arma2oa_server/

# Update HiveExt for Epoch
cp DayZ_Epoch_Server_1.0.6.2/HiveExt.ini ~/arma2oa_server/
cp DayZ_Epoch_Server_1.0.6.2/HiveExt.so ~/arma2oa_server/

# Extract mission files
cp -r DayZ_Epoch_Server_1.0.6.2/MPMissions/DayZ_Epoch_11.Chernarus ~/arma2oa_server/MPMissions/

# Set permissions
chmod +x ~/arma2oa_server/HiveExt.so
</code></pre>

<h3>Windows Installation</h3>
<pre><code># Download Epoch server files from EpochMod.com
# Extract to temporary location

# Copy @DayZ_Epoch folder to C:\arma2oa_server\
xcopy /E /I DayZ_Epoch_Server_1.0.6.2\@DayZ_Epoch C:\arma2oa_server\@DayZ_Epoch

# Replace HiveExt files
copy DayZ_Epoch_Server_1.0.6.2\HiveExt.dll C:\arma2oa_server\
copy DayZ_Epoch_Server_1.0.6.2\HiveExt.ini C:\arma2oa_server\

# Copy mission files
xcopy /E /I DayZ_Epoch_Server_1.0.6.2\MPMissions\DayZ_Epoch_11.Chernarus C:\arma2oa_server\MPMissions\DayZ_Epoch_11.Chernarus
</code></pre>

<h2 id="basebuilding">🏗️ Base Building System</h2>

<h3>Plot Pole Mechanics</h3>
<p>The plot pole is central to Epoch's base building:</p>
<ul>
    <li><strong>Range:</strong> 30m radius from pole (configurable)</li>
    <li><strong>Protection:</strong> Prevents other players from building or destroying within range</li>
    <li><strong>Maintenance:</strong> Must be maintained every 7 days or base decays</li>
    <li><strong>Upgrades:</strong> Can upgrade with additional materials for extended range</li>
</ul>

<h3>Buildable Objects</h3>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background: #0f172a;">
            <th style="padding: 12px; text-align: left; color: #ffffff;">Item</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Materials Required</th>
            <th style="padding: 12px; text-align: left; color: #ffffff;">Notes</th>
        </tr>
    </thead>
    <tbody style="color: #e5e7eb;">
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">Plot Pole</td>
            <td style="padding: 12px;">4x Wood, 1x Etool</td>
            <td style="padding: 12px;">Required for building</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">Wooden Wall</td>
            <td style="padding: 12px;">4x Wood</td>
            <td style="padding: 12px;">Basic defense</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">Metal Wall</td>
            <td style="padding: 12px;">2x Metal Panel</td>
            <td style="padding: 12px;">Strong defense</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">Storage Shed</td>
            <td style="padding: 12px;">8x Wood, 4x Metal Panel</td>
            <td style="padding: 12px;">Large storage (500 slots)</td>
        </tr>
        <tr style="background: #1e3a5f;">
            <td style="padding: 12px;">Gate</td>
            <td style="padding: 12px;">6x Wood, 2x Metal Panel</td>
            <td style="padding: 12px;">Base entrance</td>
        </tr>
        <tr style="background: #152642;">
            <td style="padding: 12px;">Floor</td>
            <td style="padding: 12px;">4x Wood</td>
            <td style="padding: 12px;">Horizontal surface</td>
        </tr>
    </tbody>
</table>

<h3>Configuration (init.sqf)</h3>
<pre><code>// Plot pole settings (mission init.sqf)
DZE_PlotPole = [30,45];  // [Range in meters, Maintenance period in days]

// Building requirements
DZE_requireplot = 1;  // 1 = Require plot pole for building, 0 = Free build

// Building limit per plot
DZE_BuildingLimit = 150;

// Allow doorway building
DZE_doorManagement = true;
</code></pre>

<h2 id="traders">💰 Trader System</h2>

<h3>Trader Types</h3>
<ul>
    <li><strong>General Trader:</strong> Food, medical supplies, basic tools</li>
    <li><strong>Weapons Trader:</strong> Firearms, ammunition, explosives</li>
    <li><strong>Vehicle Trader:</strong> Cars, trucks, motorcycles</li>
    <li><strong>Helicopter Trader:</strong> Aircraft (expensive)</li>
    <li><strong>Building Supplies:</strong> Construction materials, plot poles</li>
    <li><strong>Black Market:</strong> High-end weapons, rare items</li>
</ul>

<h3>Currency System</h3>
<p>Epoch uses briefcases of gold/silver coins:</p>
<ul>
    <li><strong>Gold Bars (10oz):</strong> 100,000 coins each</li>
    <li><strong>Gold Bars (1oz):</strong> 10,000 coins each</li>
    <li><strong>Gold Coins:</strong> 10 coins each</li>
    <li><strong>Silver Bars (10oz):</strong> 1,000 coins each</li>
    <li><strong>Silver Coins:</strong> 1 coin each</li>
</ul>

<h3>Dynamic Economy</h3>
<p>Prices fluctuate based on server activity:</p>
<pre><code>// Economy settings (mission init.sqf)
DZE_EconomyCore = true;  // Enable dynamic economy

// Price adjustment rates
DZE_PriceAdjustment = [0.8, 1.2];  // [Min, Max] multipliers

// Restock timers (in seconds)
DZE_RestockTimer = 1800;  // 30 minutes

// Banking interest rate
DZE_BankInterest = 0.001;  // 0.1% per day
</code></pre>

<h3>Configuring Traders (trader_data table)</h3>
<pre><code>-- Add custom item to trader
INSERT INTO trader_data (item, qty, buy, sell, order, tid, afile) 
VALUES ('ItemGPS', 5, '[2,"ItemGoldBar",1]', '[1,"ItemGoldBar",1]', 0, 126, 'trade_items');

-- Adjust trader prices
UPDATE trader_data SET buy = '[1,"ItemGoldBar10oz",1]' WHERE item = 'M4A1_HWS_GL_camo';

-- Restock trader inventory
UPDATE trader_data SET qty = 10 WHERE tid = 126;
</code></pre>

<h2 id="vehicles">🚗 Vehicle System</h2>

<h3>Vehicle Spawn Configuration</h3>
<p>Epoch uses <code>vehicle_spawns</code> table for dynamic spawning:</p>
<pre><code>-- Add vehicle spawn point
INSERT INTO vehicle_spawns (vehicle, chance, position, direction, hitpoints, fuel, damage) 
VALUES ('UAZ_Unarmed_TK_EP1', 0.6, '[4532,9516,0]', 90, '[]', 0.5, 0);

-- Configure spawn chances
UPDATE vehicle_spawns SET chance = 0.8 WHERE vehicle LIKE 'Offroad%';
</code></pre>

<h3>Vehicle Maintenance</h3>
<ul>
    <li><strong>Damage System:</strong> Vehicles take damage and require repairs</li>
    <li><strong>Fuel Consumption:</strong> Must refuel at gas stations or with jerry cans</li>
    <li><strong>Locking:</strong> Vehicles can be locked with keys (database stored)</li>
    <li><strong>Persistence:</strong> Saved/locked vehicles persist in database</li>
</ul>

<h3>Key System</h3>
<pre><code>// Key settings (mission init.sqf)
DZE_KeysAllow = true;  // Enable vehicle key system

// Key types
// Green key - Can access vehicle
// Red key - Owner, can give access
</code></pre>

<h2 id="banking">💸 Banking System</h2>

<h3>Banking Features</h3>
<ul>
    <li><strong>Deposit Coins:</strong> Store currency safely at trader cities</li>
    <li><strong>Withdrawal:</strong> Retrieve coins anytime at any trader</li>
    <li><strong>Interest:</strong> Earn small interest on banked coins</li>
    <li><strong>Death Protection:</strong> Banked coins don't drop on death</li>
</ul>

<h3>Configuration</h3>
<pre><code>// Banking settings (mission init.sqf)
DZE_EnableBanking = true;

// Banking interest rate (per day)
DZE_BankInterest = 0.001;  // 0.1%

// Max bank storage
DZE_MaxBankMoney = 10000000;  // 10 million coins

// Banking fees
DZE_BankFee = 0;  // No fee for deposits/withdrawals
</code></pre>

<h2 id="configuration">⚙️ Configuration</h2>

<h3>HiveExt.ini (Epoch-specific)</h3>
<pre><code>[Database]
Host = 127.0.0.1
Port = 3306
Database = epoch
Username = epoch
Password = your_password

[Objects]
;Maximum number of objects per player
MaxObjectsPerPlayer = 150

;Object cleanup timer (days)
CleanupPeriod = 30

[Traders]
;Enable trader menu
TraderMenuEnabled = true

;Trader safe zone radius
SafeZoneRadius = 150
</code></pre>

<h3>Mission init.sqf (Key Settings)</h3>
<pre><code>// DayZ Epoch initialization
dayZ_instance = 11;  // Server instance ID
dayzHiveRequest = [];

// Epoch settings
DZE_ConfigTrader = true;  // Use trader_data table
DZE_GodModeBase = false;  // Can destroy bases (set true for invincible)
DZE_BuildOnRoads = false;  // Prevent building on roads
DZE_SelfTransfuse = true;  // Allow self blood bag usage

// Plot pole settings
DZE_PlotPole = [30,45];  // [Range meters, Maintenance days]
DZE_PlotManagement = true;  // Enable plot management menu

// Building
DZE_BuildingLimit = 150;
DZE_requireplot = 1;
DZE_doorManagement = true;

// Traders
DZE_EnableBanking = true;
DZE_BankInterest = 0.001;
DZE_EconomyCore = true;

// Vehicles
DZE_KeysAllow = true;
</code></pre>

<h3>Loot Tables (CfgLoot/)</h3>
<p>Epoch includes custom loot tables in mission folder:</p>
<pre><code>// Edit CfgLoot/CfgBuildingLoot.hpp
class BuildingLoot {
    class Supermarket {
        zombieChance = 0.3;
        maxRoaming = 2;
        zombieClass[] = {"zZombie_Base","z_hunter"};
        lootChance = 0.4;
        lootPos[] = {};
        itemType[] = {
            {"ItemSodaMdew",0.03},
            {"FoodCanBakedBeans",0.02},
            {"ItemBandage",0.01}
        };
    };
};
</code></pre>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Base Not Saving</h3>
<pre><code># Check object_data table
mysql -u epoch -p
USE epoch;
SELECT * FROM object_data WHERE CharacterID = YOUR_PLAYER_ID LIMIT 10;

# Common causes:
# 1. HiveExt not connecting to database
# 2. CharacterID mismatch
# 3. Database full / locked
# 4. Plot pole not placed correctly

# Check HiveExt.log
grep "object_data" HiveExt.log
</code></pre>

<h3>Trader Not Working</h3>
<ul>
    <li>Verify <code>trader_data</code> table exists and is populated</li>
    <li>Check <code>DZE_ConfigTrader = true</code> in mission init.sqf</li>
    <li>Ensure trader AI is spawned in mission.sqm</li>
    <li>Check player is within safe zone radius</li>
</ul>

<h3>Vehicle Spawns Empty</h3>
<pre><code># Check vehicle_spawns table
SELECT * FROM vehicle_spawns LIMIT 20;

# Adjust spawn chances if too low
UPDATE vehicle_spawns SET chance = 1.0 WHERE chance < 0.5;

# Force respawn (wipe existing vehicles)
DELETE FROM object_data WHERE Classname IN (SELECT vehicle FROM vehicle_spawns);
</code></pre>

<h3>Database Growing Too Large</h3>
<pre><code># Check database size
SELECT table_name, 
       ROUND(((data_length + index_length) / 1024 / 1024), 2) AS "Size (MB)" 
FROM information_schema.TABLES 
WHERE table_schema = "epoch";

# Run cleanup script (backup first!)
DELETE FROM object_data WHERE Damage = 1 OR CharacterID = 0;
DELETE FROM object_data WHERE LastUpdated < DATE_SUB(NOW(), INTERVAL 30 DAY);

OPTIMIZE TABLE object_data;
</code></pre>

<h3>Players Losing Coins</h3>
<ul>
    <li>Ensure banking is enabled in mission config</li>
    <li>Check character_data table for coin field corruption</li>
    <li>Verify HiveExt is saving character data correctly</li>
    <li>Check for database rollbacks (restore from backup)</li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-lightbulb" style="color: #fbbf24; margin-right: 8px;"></i>Pro Tips</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li><strong>Database Maintenance:</strong> Run cleanup scripts weekly - Epoch databases grow rapidly</li>
        <li><strong>Plot Protection:</strong> Educate players about 30m range and maintenance requirements</li>
        <li><strong>Trader Balance:</strong> Adjust prices to prevent economy inflation</li>
        <li><strong>Vehicle Spawns:</strong> Lower spawn chances for rare vehicles (helicopters, armored)</li>
        <li><strong>Restart Schedule:</strong> Daily 4am restarts with 30-minute warnings</li>
        <li><strong>Backup Everything:</strong> Automated database backups before every restart</li>
        <li><strong>Community Resources:</strong> Epoch forums have extensive guides and custom scripts</li>
        <li><strong>Performance:</strong> Limit max objects per player to prevent lag</li>
    </ul>
</div>

<h2 id="resources">Resources</h2>
<ul>
    <li><a href="https://github.com/EpochModTeam/DayZ-Epoch" target="_blank">Official Epoch GitHub</a></li>
    <li><a href="http://epochmod.com/" target="_blank">EpochMod.com - Official Site</a></li>
    <li><a href="https://github.com/EpochModTeam/DayZ-Epoch/wiki" target="_blank">Epoch Wiki</a></li>
    <li><a href="https://forums.dayz.com/forum/135-epoch-mod/" target="_blank">Epoch Mod Forums</a></li>
    <li><a href="../dayzmod/">DayZ Mod Documentation</a> - Base mod setup</li>
    <li><a href="../arma2oa/">ARMA 2 OA Server Documentation</a></li>
</ul>
</ol>

<h2>Server Configuration</h2>
<p>After your server is created, you can configure it through the control panel:</p>
<ul>
    <li>Server settings and parameters</li>
    <li>Player slots and limits</li>
    <li>RCON/remote control access</li>
    <li>FTP file access</li>
</ul>

<h2>Common Tasks</h2>

<h3>Starting Your Server</h3>
<p>Servers are automatically started after creation. You can stop/start your server from the control panel.</p>

<h3>Connecting to Your Server</h3>
<p>Use your server's IP address and port to connect from the game client.</p>

<h3>Managing Files</h3>
<p>Access your server files via FTP using the credentials provided in your control panel.</p>

<h2>Support</h2>
<p>If you need assistance with your DayZ Epoch Mod server:</p>
<ul>
    <li>Check our <a href="/docs.php?action=view&doc=common-issues">Common Issues</a> guide</li>
    <li>Contact support through your account dashboard</li>
    <li>Visit the official DayZ Epoch Mod community for game-specific help</li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Always keep your server updated to the latest version</li>
        <li>Make regular backups of your server configuration</li>
        <li>Review and follow the game's End User License Agreement (EULA)</li>
    </ul>
</div>