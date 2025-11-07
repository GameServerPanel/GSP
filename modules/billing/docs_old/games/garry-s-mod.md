# Garry’s Mod — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
```bash
srcds_run -game garrysmod -console -usercon +hostport 27015 +map de_dust2 +maxplayers 16 +exec server.cfg
```

**Parameters (exhaustive, server-relevant only)**
- `-game <dir>` — Game directory. Required.
- `-console` — Enable server console output.
- `-usercon` — Enable user console commands.
- `+hostport <port>` — Server port (UDP). Default: 27015.
- `+ip <address>` — Bind to specific IP address.
- `+map <mapname>` — Starting map. Required.
- `+maxplayers <num>` — Maximum players (1-64).
- `+exec <file>` — Execute config file on startup.
- `+sv_setsteamaccount <token>` — Game Server Login Token for public servers.
- `+rcon_password <pass>` — RCON password for remote administration.
- `+sv_password <pass>` — Server password for private games.
- `+hostname <name>` — Server name in browser.
- `+sv_lan <0|1>` — LAN mode (0=Internet, 1=LAN only).
- `-tickrate <rate>` — Server tickrate (default: 66, competitive: 128).
- `-port <port>` — Alternative syntax for hostport.
- `-nohltv` — Disable SourceTV.
- `+tv_enable <0|1>` — Enable/disable SourceTV.
- `+tv_port <port>` — SourceTV port (default: hostport + 5).
- `-secure` — Enable VAC (Valve Anti-Cheat).
- `-insecure` — Disable VAC (for testing only).
- `+sv_region <num>` — Server region (255=world, 0-7=specific regions).
- `+fps_max <fps>` — Server FPS limit.
- `-threads <num>` — Number of worker threads.
- `-norestart` — Don't restart server on crash.
- `+log <on|off>` — Enable/disable logging.
- `-condebug` — Log console output to file.
- `+sv_logfile <0|1>` — Enable server logging.
- `+sv_logflush <0|1>` — Flush logs immediately.

**Ports**
- Game: UDP **27015** (primary)
- RCON: TCP **27015** (same as game port)
- SourceTV: UDP **27020** (game port + 5)
- Steam Query: UDP **27016** (game port + 1)

## Config Files & Locations
**Windows:**
- `steamapps/common/Garry’s Mod/cfg/server.cfg` — Main server configuration
- `steamapps/common/Garry’s Mod/cfg/autoexec.cfg` — Auto-executed commands
- `steamapps/common/Garry’s Mod/mapcycle.txt` — Map rotation list
- `steamapps/common/Garry’s Mod/motd.txt` — Message of the day
- `steamapps/common/Garry’s Mod/banned_user.cfg` — Banned users
- `steamapps/common/Garry’s Mod/banned_ip.cfg` — Banned IP addresses
- `steamapps/common/Garry’s Mod/logs/` — Server logs directory

**Linux:**
- `~/.steam/steamapps/common/Garry’s Mod/cfg/server.cfg` — Main server configuration
- `~/.steam/steamapps/common/Garry’s Mod/cfg/autoexec.cfg` — Auto-executed commands
- `~/.steam/steamapps/common/Garry’s Mod/mapcycle.txt` — Map rotation list
- `~/.steam/steamapps/common/Garry’s Mod/motd.txt` — Message of the day
- `~/.steam/steamapps/common/Garry’s Mod/banned_user.cfg` — Banned users
- `~/.steam/steamapps/common/Garry’s Mod/banned_ip.cfg` — Banned IP addresses
- `~/.steam/steamapps/common/Garry’s Mod/logs/` — Server logs directory

**Key Configuration Files:**
- **server.cfg**: Core server settings (rates, game rules, admin settings)
- **autoexec.cfg**: Commands executed on server start
- **mapcycle.txt**: Map rotation configuration
- **motd.txt**: Welcome message displayed to connecting players

## Steam Workshop
**Collection Mounting:**
1. Create Steam Workshop collection with server content
2. Add to startup: `+host_workshop_collection <collection_id>`
3. Set Steam Web API key: `+sv_setsteamaccount <token>`

**Resource Management:**
- Use `resource.AddWorkshopFile(id)` in Lua for required downloads
- Large workshop collections may cause long loading times
- Consider FastDL for faster content delivery

**Auto-Download:**
- Players automatically download workshop content
- Monitor download progress in server console
- Some content may require manual subscription by players

**Cache Location:**
- Windows: `steamapps/workshop/content/4000/`
- Linux: `~/.steam/steamapps/workshop/content/4000/`

## Common Mods (curated)
- **DarkRP**
  - **Purpose**: Popular roleplay gamemode framework.
  - **Install**: Download from workshop or GitHub, extract to gamemodes directory.
  - **Configure**: Edit `gamemodes/darkrp/gamemode/config.lua` for server settings.

- **ULX/ULib**
  - **Purpose**: Admin framework with extensive user management.
  - **Install**: Download both ULX and ULib, extract to addons directory.
  - **Configure**: Admin groups and permissions in `data/ulx/` directory.

- **Wiremod**
  - **Purpose**: Advanced contraption building with electronic components.
  - **Install**: Subscribe via Workshop or manual installation to addons.
  - **Configure**: No specific configuration required, workshop auto-download.

- **PAC3**
  - **Purpose**: Player appearance customization system.
  - **Install**: Workshop subscription, auto-downloads to clients.
  - **Configure**: Server settings in `cfg/pac.cfg` if needed.

## Database
**Engine**: SQLite/MySQL

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
**Server not starting**
- **Cause**: Missing dependencies, incorrect configuration, or port conflicts
- **Fix**: Check server logs, verify all required files are present, ensure ports are available

**Players cannot connect**
- **Cause**: Firewall blocking server port or incorrect network configuration
- **Fix**: Open required ports in firewall, verify server is binding to correct IP address

**Performance issues/lag**
- **Cause**: Insufficient server resources or suboptimal configuration
- **Fix**: Monitor CPU/memory usage, optimize server settings, reduce player/entity limits

**Configuration not loading**
- **Cause**: Syntax errors in config files or incorrect file paths
- **Fix**: Validate configuration file syntax, check file permissions, review server logs

**Mod/plugin conflicts**
- **Cause**: Incompatible mods or plugin version mismatches
- **Fix**: Test mods individually, update to compatible versions, check for known conflicts
