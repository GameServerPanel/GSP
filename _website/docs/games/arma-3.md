# Arma 3 — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
```bash
arma3server_x64.exe -port=2302 -config=server.cfg -cfg=basic.cfg -profiles=ServerProfile -name=server -serverMod=""
```

**Parameters (exhaustive, server-relevant only)**
- `-port=<port>` — Server port. Default: 2302.
- `-config=<file>` — Server config file (server.cfg).
- `-cfg=<file>` — Basic config file (basic.cfg).
- `-profiles=<dir>` — Profile directory.
- `-name=<profilename>` — Server profile name.
- `-serverMod="<mods>"` — Server-side only mods (semicolon separated).
- `-mod="<mods>"` — Client and server mods (semicolon separated).
- `-world=<worldname>` — Empty world for headless client.
- `-autoInit` — Auto-initialize mission.
- `-loadMissionToMemory` — Load mission into memory.
- `-noSound` — Disable sound.
- `-enableHT` — Enable hyperthreading.
- `-hugepages` — Enable huge pages (Linux).
- `-malloc=<allocator>` — Memory allocator.
- `-maxMem=<mb>` — Maximum memory.
- `-cpuCount=<num>` — CPU core count.
- `-exThreads=<num>` — Extra threads.

**Ports**
- Game: UDP **2302** (primary)
- Steam Query: UDP **2303** (game port + 1)
- BattlEye: UDP **2344** (game port + 42)
- VON: UDP **2304** (game port + 2)

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
**Mod Installation:**
1. Subscribe to mods via Steam Workshop
2. Add mod IDs to server startup: `-mod="@workshop_id_1;@workshop_id_2"`
3. For server-only mods use: `-serverMod="@mod_folder"`

**Workshop Collection:**
- Create Steam collection with all server mods
- Use Arma 3 Server tools or community launchers for bulk downloading
- Verify all clients have same mod versions

**Mod Loading:**
- Order matters for some mods (CBA_A3 should load first)
- Use proper folder structure: `@mod_name/addons/*.pbo`
- Include signature files (.bisign) for key verification

**Cache Location:**
- Windows: `steamapps/workshop/content/107410/`
- Linux: `~/.steam/steamapps/workshop/content/107410/`

## Common Mods (curated)
- **ACE3**
  - **Purpose**: Advanced Combat Environment - realistic medical, ballistics, and logistics.
  - **Install**: Subscribe via Workshop, add `@ace` to startup mods.
  - **Configure**: ACE settings via in-game addon options or mission parameters.

- **CBA_A3**
  - **Purpose**: Community Base Addons - framework required by most Arma 3 mods.
  - **Install**: Subscribe via Workshop, ensure loads before other mods.
  - **Configure**: No direct configuration - provides API for other mods.

- **TFAR (Task Force Radio)**
  - **Purpose**: Realistic radio communication system.
  - **Install**: Workshop subscription, requires TeamSpeak 3 plugin for clients.
  - **Configure**: Radio frequencies and settings in mission files.

- **RHS: Armed Forces**
  - **Purpose**: High-quality modern military units and equipment.
  - **Install**: Subscribe to RHS collections via Workshop.
  - **Configure**: Unit availability configured in mission editor.

## Database
Not applicable - this game does not use a database for core functionality.

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
**"Bad module info" / Addon errors**
- **Cause**: Corrupted mod files or version mismatches
- **Fix**: Verify mod integrity, ensure client-server version matching, check signature verification

**"Session lost" during gameplay**
- **Cause**: Network issues or memory problems
- **Fix**: Optimize basic.cfg network settings, increase server memory allocation, check mod conflicts

**High memory usage / Server crashes**
- **Cause**: Memory leaks from scripts or excessive mod usage
- **Fix**: Restart server regularly, optimize mission scripts, reduce active mod count

**BattlEye script restriction kicks**
- **Cause**: Scripts triggering BE filters
- **Fix**: Update BattlEye filters for installed mods, configure proper exceptions

**Signature verification failed**
- **Cause**: Missing or mismatched .bikey files
- **Fix**: Ensure all mod .bikey files present in keys/ directory, verify verifySignatures setting

**Performance issues / Low FPS**
- **Cause**: Complex missions, AI overload, or insufficient server resources
- **Fix**: Optimize mission complexity, reduce AI count, upgrade server hardware, tune basic.cfg
