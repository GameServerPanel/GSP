# Rust — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
```bash
RustDedicated.exe -batchmode -nographics +server.port 28015 +server.identity "my_server" +server.hostname "My Rust Server" +server.maxplayers 100 +server.worldsize 4000 +server.seed 1234567
```

**Parameters (exhaustive, server-relevant only)**
- `-batchmode` — Run in batch mode (no graphics).
- `-nographics` — Disable graphics rendering.
- `+server.port <port>` — Server port. Default: 28015.
- `+server.identity "<name>"` — Server identity folder name.
- `+server.hostname "<name>"` — Server name in browser.
- `+server.maxplayers <num>` — Maximum players (1-500).
- `+server.worldsize <size>` — World size (1000-8000).
- `+server.seed <num>` — World generation seed.
- `+server.level "<map>"` — Map name (Procedural Map, custom maps).
- `+server.tickrate <rate>` — Server tickrate (10-30).
- `+rcon.port <port>` — RCON port.
- `+rcon.password "<pass>"` — RCON password.
- `+rcon.web <0|1>` — Enable web RCON.
- `+server.saveinterval <seconds>` — Save interval.
- `+server.globalchat <true|false>` — Global chat enabled.
- `+server.description "<text>"` — Server description.
- `+server.headerimage "<url>"` — Header image URL.
- `+server.url "<url>"` — Server website URL.
- `+server.pve <true|false>` — PvE mode.
- `+decay.scale <multiplier>` — Decay rate multiplier.
- `+fps.limit <fps>` — FPS limit.
- `-logfile <path>` — Log file path.

**Ports**
- Game: UDP **28015** (primary)
- RCON: TCP **28016** (game port + 1)
- App: TCP **28082** (Rust+ companion app)

## Config Files & Locations
**Windows:**
- `Data/` — Game data directory
- `Logs/` — Log files directory
- `ServerConfig/` — Configuration files (varies by game)

**Linux:**
- `~/rust/Data/` — Game data directory
- `~/rust/Logs/` — Log files directory
- `~/rust/ServerConfig/` — Configuration files

**Key Files:**
- Configuration file names and locations vary significantly between Unity games
- Common patterns: server.cfg, config.json, settings.xml
- Check game-specific documentation for exact file locations

## Steam Workshop
**Workshop Integration:**
- Check Steam Workshop for community content
- Subscribe to collections via Steam client
- Server automatically downloads subscribed content
- Configure workshop content loading in server configuration

## Common Mods (curated)
- **Oxide/uMod**
  - **Purpose**: Modding framework for Rust servers with extensive plugin ecosystem.
  - **Install**: Download from umod.org, extract to server directory.
  - **Configure**: Plugin configuration in `oxide/config/` directory.

- **AdminHammer**
  - **Purpose**: Advanced admin tools and server management.
  - **Install**: Install via Oxide plugin manager or manual download.
  - **Configure**: Admin permissions in `oxide/data/AdminHammer.json`.

- **Economics**
  - **Purpose**: Server economy system with currency and rewards.
  - **Install**: Download plugin, place in `oxide/plugins/`.
  - **Configure**: Economy settings in `oxide/config/Economics.json`.

- **Kits**
  - **Purpose**: Predefined item kits for players (starter kits, VIP kits).
  - **Install**: Install via Oxide plugin system.
  - **Configure**: Kit definitions in `oxide/config/Kits.json`.

## Database
**Engine**: SQLite (built-in) for basic data, JSON files for Oxide plugins

**Server Data Storage**:
- Player data: `server/my_server_identity/storage/` 
- World saves: `server/my_server_identity/saves/`
- Oxide data: `oxide/data/` directory

**Oxide Plugin Storage**:
- Most plugins use JSON files in `oxide/data/`
- Some plugins support SQLite or MySQL connections
- Configuration in individual plugin config files

**Backup Strategy**:
- Backup entire server identity folder for complete restoration
- Oxide data folder contains all plugin persistent data
- Monitor storage growth on high-population servers

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
**"Couldn't connect to server" / Connection timeout**
- **Cause**: Firewall blocking ports or server not responding
- **Fix**: Open UDP port 28015 and TCP port 28016, verify server is running and responsive

**"Failed to create directory" on startup**
- **Cause**: Insufficient permissions or disk space
- **Fix**: Run server with proper permissions, ensure adequate disk space, check path validity

**High memory usage / Out of memory crashes**
- **Cause**: Large world size, too many entities, or memory leaks in plugins
- **Fix**: Reduce world size, limit entity spawns, restart server regularly, monitor Oxide plugins

**"Disconnected: EAC Authentication Timeout"**
- **Cause**: Easy Anti-Cheat connectivity issues
- **Fix**: Verify internet connection, check EAC service status, ensure firewall allows EAC traffic

**Server lag / Low FPS**
- **Cause**: High player count, complex bases, or inefficient plugins
- **Fix**: Optimize server settings, limit building complexity, review Oxide plugin performance

**Workshop/Oxide plugins not loading**
- **Cause**: Plugin conflicts, outdated plugins, or configuration errors
- **Fix**: Check plugin compatibility, update plugins, review oxide logs for errors
