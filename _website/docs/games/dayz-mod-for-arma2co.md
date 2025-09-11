# DayZ Mod (Arma 2 OA) — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
```bash
arma2oaserver.exe -port=2302 -config=server.cfg -cfg=basic.cfg -profiles=ServerProfile -mod=@mod1;@mod2
```

**Parameters (exhaustive, server-relevant only)**
- `-port=<port>` — Server port. Default: 2302.
- `-config=<file>` — Server config file (server.cfg).
- `-cfg=<file>` — Basic config file (basic.cfg).
- `-profiles=<dir>` — Profile directory.
- `-mod=<mods>` — Mod folders (semicolon separated).
- `-serverMod=<mods>` — Server-side only mods.
- `-name=<profilename>` — Server profile name.
- `-world=<worldname>` — Empty world name.
- `-noSound` — Disable sound processing.
- `-nosplash` — Skip intro videos.
- `-noPause` — Don't pause when not focused.
- `-cpuCount=<num>` — CPU core count override.
- `-maxMem=<mb>` — Maximum memory usage.

**Ports**
- Game: UDP **2302** (primary)
- Query: UDP **2303** (game port + 1)
- BattlEye: UDP **2344** (if enabled)

## Config Files & Locations
**Windows:**
- `server.cfg` — Main server configuration
- `basic.cfg` — Basic networking and performance settings
- `ServerProfile/` — Profile directory with logs and user data
- `MPMissions/` — Mission files directory
- `keys/` — Signature keys for mod verification

**Linux:**
- `~/arma/server.cfg` — Main server configuration
- `~/arma/basic.cfg` — Basic networking settings
- `~/arma/ServerProfile/` — Profile directory
- `~/arma/MPMissions/` — Mission files
- `~/arma/keys/` — Signature keys

**Key Files:**
- **server.cfg**: Server name, password, mission rotation, admin settings
- **basic.cfg**: Network bandwidth and performance tuning
- **Profile logs**: Server performance and error logs

## Steam Workshop
Not supported by this game.

## Common Mods (curated)
- **DayZ Epoch**
  - **Purpose**: Enhanced DayZ experience with base building and vehicles.
  - **Install**: Download server and client files, place in `@DayZ_Epoch` folder.
  - **Configure**: Database setup and server configuration in `@hive` folder.

- **DayZ Overwatch**
  - **Purpose**: Military-focused DayZ variant with additional weapons.
  - **Install**: Download mod files, combine with Epoch for Overpoch.
  - **Configure**: Mission file configuration for weapon spawns.

- **infiSTAR**
  - **Purpose**: Advanced anti-hack and admin tools.
  - **Install**: Purchase and download, server-side installation only.
  - **Configure**: Admin IDs and settings in infiSTAR configuration files.

## Database
**Engine**: MySQL (required for character persistence)

**Configuration File**: `@hive/HiveExt.ini`
```ini
[Database]
Type = mysql
Host = localhost
Port = 3306
Database = dayz_epoch
Username = dayz_user
Password = your_password_here

[Characters]
;Enables persistence
LoadCharacter = true
SaveCharacter = true
```

**Database Setup**:
1. Install MySQL server
2. Create database: `CREATE DATABASE dayz_epoch;`
3. Import schema from mod documentation
4. Create user with appropriate permissions
5. Test connection before starting server

**Backup Strategy**:
- Daily automated backups of character and vehicle data
- Retention of 7 daily backups
- Test restore procedures regularly
- Monitor database size and performance

## Administration & Scripting
**Remote Administration:**
- RCON (Remote Console) access for server management
- Web-based admin panels (game-specific or third-party)
- In-game admin commands and permissions

**Backup Strategy:**
- Automated daily backups of save files and configuration
- Rotate backups (keep 7 daily, 4 weekly, 12 monthly)
- Test backup restoration procedures regularly
- Store backups in separate location/drive

**Auto-Update:**
- Use SteamCMD for automatic server updates (Steam games)
- Schedule updates during low-traffic periods
- Backup before applying updates
- Monitor for update announcements and patch notes

**Monitoring:**
- Server performance monitoring (CPU, memory, network)
- Player connection logs and statistics
- Error log monitoring and alerting
- Uptime tracking and availability reporting

## Troubleshooting (game-specific)
**"Waiting for host" infinite loop**
- **Cause**: Database connection issues or incorrect mission file
- **Fix**: Verify MySQL connection in HiveExt.ini, check mission PBO integrity, review server RPT logs

**"No message received" / Player disconnect**
- **Cause**: Network timeout or BattlEye issues
- **Fix**: Optimize basic.cfg network settings, update BattlEye filters, check server performance

**Database connection failed**
- **Cause**: MySQL server down or incorrect credentials
- **Fix**: Verify MySQL service running, check HiveExt.ini credentials, test database connectivity

**Script restriction kicks**
- **Cause**: BattlEye script filters blocking legitimate commands
- **Fix**: Update BattlEye filters from mod documentation, merge custom filter exceptions

**Character reset / Database issues**
- **Cause**: Database corruption or incorrect instance configuration
- **Fix**: Verify database integrity, check Instance ID matches between DB and config
