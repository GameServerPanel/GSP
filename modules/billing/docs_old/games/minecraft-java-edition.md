# Minecraft: Java Edition — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
```bash
./minecraft-java-edition_server -port 27015 -maxplayers 16 -config server.cfg
```

**Parameters (common server flags)**
- `-port <port>` — Server port (default varies by game)
- `-maxplayers <num>` — Maximum player count
- `-config <file>` — Configuration file to load
- `-log` — Enable logging
- `-console` — Enable console output
- `-dedicated` — Run as dedicated server
- `-name "<name>"` — Server name
- `-password "<pass>"` — Server password

**Ports**
- Game: UDP **27015** (typical default)
- Query: UDP **27016** (game port + 1)
- Admin/RCON: TCP **varies by game**

## Config Files & Locations
**Windows:**
- `minecraft-java-edition_server.cfg` — Main server configuration
- `config/` — Configuration directory
- `logs/` — Log files directory
- `data/` — Server data and saves

**Linux:**
- `~/minecraft-java-edition/server.cfg` — Main server configuration  
- `~/minecraft-java-edition/config/` — Configuration directory
- `~/minecraft-java-edition/logs/` — Log files directory
- `~/minecraft-java-edition/data/` — Server data and saves

**Key Files:**
- **server.cfg**: Core server settings and game rules
- **admins.cfg**: Administrator configuration (if applicable)
- **banned.cfg**: Banned players list (if applicable)

## Steam Workshop
Not supported by this game.

## Common Mods (curated)
- **EssentialsX**
  - **Purpose**: Core commands, economy, permissions for Bukkit/Spigot/Paper servers.
  - **Install**: Download from GitHub releases, place JAR in `plugins/` directory.
  - **Configure**: Edit `plugins/Essentials/config.yml` for basic settings, `userdata/` for player data.

- **WorldEdit**
  - **Purpose**: In-game world editing and building tools.
  - **Install**: Place WorldEdit JAR in `plugins/` directory.
  - **Configure**: Permissions in `plugins/WorldEdit/config.yml`, user permissions via permission plugin.

- **Vault**
  - **Purpose**: Economy and permissions API for other plugins.
  - **Install**: Download Vault JAR to `plugins/` directory.
  - **Configure**: No direct configuration - provides API for other plugins.

- **LuckPerms**
  - **Purpose**: Advanced permissions management system.
  - **Install**: Download from GitHub, place in `plugins/` directory.
  - **Configure**: Database connection in `plugins/LuckPerms/config.yml`, manage permissions via commands or web editor.

- **Dynmap**
  - **Purpose**: Real-time web-based map of server world.
  - **Install**: Download Dynmap JAR to `plugins/` directory.
  - **Configure**: Web server settings in `plugins/dynmap/configuration.txt`, map rendering options.

- **ProtocolLib**
  - **Purpose**: Packet manipulation library for advanced plugins.
  - **Install**: Required dependency for many plugins, place JAR in `plugins/`.
  - **Configure**: No direct configuration - provides API for packet handling.

## Database
**Engine**: SQLite (default) or MySQL (for large servers)

**SQLite Configuration** (No setup required):
- Database files stored in world directory
- Player data in `playerdata/` folder
- Plugin data varies by plugin (typically in `plugins/PluginName/`)

**MySQL Configuration** (Advanced):
- Configure in `bukkit.yml` or plugin-specific configs
- Example connection string: `jdbc:mysql://localhost:3306/minecraft`
- Required for multi-server networks and large-scale deployments

**Backup Strategy**:
- Regular world folder backups (includes SQLite databases)
- MySQL: Use `mysqldump` for database backups
- Plugin data backup varies by plugin requirements

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
**"java.lang.OutOfMemoryError"**
- **Cause**: Insufficient heap memory allocation
- **Fix**: Increase `-Xmx` parameter (e.g., `-Xmx8G` for 8GB), monitor memory usage

**"Can't keep up! Skipping ticks"**
- **Cause**: Server overload, too many entities, or inefficient plugins
- **Fix**: Reduce entity limits, optimize plugins, increase server resources, use profiling tools

**"Connection timed out"**
- **Cause**: Firewall blocking port 25565 or server not responding
- **Fix**: Open TCP port 25565, verify server is running, check network connectivity

**"Plugin errors on startup"**
- **Cause**: Incompatible plugin versions or missing dependencies
- **Fix**: Update plugins to match server version, install required dependencies, check plugin logs

**"World corruption/rollback"**
- **Cause**: Improper server shutdown or storage issues
- **Fix**: Use proper shutdown commands, implement regular backups, check disk health

**"Low TPS (Ticks Per Second)"**
- **Cause**: Server lag from heavy operations or overloaded chunks
- **Fix**: Use performance monitoring plugins, limit chunk loading, optimize redstone contraptions
