# DayZ — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
```bash
DayZServer_x64.exe -config=serverDZ.cfg -port=2302 -profiles=ServerProfile -dologs -adminlog -netlog -freezecheck
```

**Parameters (exhaustive, server-relevant only)**
- `-config=<file>` — Server config file (serverDZ.cfg).
- `-port=<port>` — Server port. Default: 2302.
- `-profiles=<dir>` — Profile directory name.
- `-dologs` — Enable logging.
- `-adminlog` — Enable admin logging.
- `-netlog` — Enable network logging.
- `-freezecheck` — Enable freeze detection.
- `-scriptDebug=<true|false>` — Enable script debugging.
- `-filePatching` — Enable file patching (dev mode).
- `-scrAllowFileWrite` — Allow script file writing.
- `-limitFPS=<fps>` — Limit server FPS.
- `-cpuCount=<num>` — CPU core count override.
- `-maxMem=<mb>` — Maximum memory usage.
- `-malloc=<system>` — Memory allocator (system, tbb4malloc_bi).
- `-exThreads=<num>` — Extra threads count.
- `-enableHT` — Enable hyperthreading.
- `-hugepages` — Enable huge pages (Linux).
- `-BEpath=<path>` — BattlEye path.
- `-instanceId=<num>` — Server instance ID.

**Ports**
- Game: UDP **2302** (primary)
- Steam Query: UDP **27016** (fixed)
- BattlEye: UDP **2344** (game port + 42)
- VON: UDP **2303** (game port + 1)

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
- **CF (Community Framework)**
  - **Purpose**: Modding framework required by many DayZ mods.
  - **Install**: Subscribe via Workshop, ensure loads first in mod order.
  - **Configure**: No direct configuration - provides API framework.

- **BuildAnywhere**
  - **Purpose**: Allows base building in normally restricted areas.
  - **Install**: Workshop subscription, add to server mods.
  - **Configure**: Settings in mod configuration files.

- **Trader**
  - **Purpose**: NPC traders and economy system.
  - **Install**: Download from modding sites, requires server restart.
  - **Configure**: Trader locations and items in mod config files.

- **DayZ Editor Loader**
  - **Purpose**: Custom spawns, buildings, and map modifications.
  - **Install**: Workshop or manual installation.
  - **Configure**: Map edits and spawn configurations in JSON files.

## Database
**Engine**: SQLite

**Configuration**:
- Database settings typically in main server configuration file
- Connection parameters: host, port, database name, credentials
- Enable persistence features in server configuration

**Setup**:
1. Install database engine if required
2. Create database and user with appropriate permissions
3. Configure connection settings in server config
4. Test connection before starting server
5. Set up automated backups

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
**"Authentication timeout" on connect**
- **Cause**: Steam authentication server issues or network problems
- **Fix**: Check Steam server status, verify internet connectivity, restart Steam services

**"Session lost" / Frequent disconnects**
- **Cause**: Network instability or server performance issues
- **Fix**: Optimize server performance, check network quality, adjust timeout settings

**Mods not loading / Version mismatch**
- **Cause**: Client-server mod version differences or missing dependencies
- **Fix**: Verify all mods updated, check mod dependencies, ensure Workshop sync

**Character stuck / Can't move**
- **Cause**: Database synchronization issues or server lag
- **Fix**: Character reset via admin tools, server restart, check database performance

**BattlEye initialization failed**
- **Cause**: Missing BattlEye files or configuration issues
- **Fix**: Verify BattlEye installation, check file permissions, update BattlEye client
