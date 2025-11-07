# Left 4 Dead 2 — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
```bash
srcds_run -game left4dead2 -console -usercon +hostport 27015 +map de_dust2 +maxplayers 16 +exec server.cfg
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
- `steamapps/common/Left 4 Dead 2/cfg/server.cfg` — Main server configuration
- `steamapps/common/Left 4 Dead 2/cfg/autoexec.cfg` — Auto-executed commands
- `steamapps/common/Left 4 Dead 2/mapcycle.txt` — Map rotation list
- `steamapps/common/Left 4 Dead 2/motd.txt` — Message of the day
- `steamapps/common/Left 4 Dead 2/banned_user.cfg` — Banned users
- `steamapps/common/Left 4 Dead 2/banned_ip.cfg` — Banned IP addresses
- `steamapps/common/Left 4 Dead 2/logs/` — Server logs directory

**Linux:**
- `~/.steam/steamapps/common/Left 4 Dead 2/cfg/server.cfg` — Main server configuration
- `~/.steam/steamapps/common/Left 4 Dead 2/cfg/autoexec.cfg` — Auto-executed commands
- `~/.steam/steamapps/common/Left 4 Dead 2/mapcycle.txt` — Map rotation list
- `~/.steam/steamapps/common/Left 4 Dead 2/motd.txt` — Message of the day
- `~/.steam/steamapps/common/Left 4 Dead 2/banned_user.cfg` — Banned users
- `~/.steam/steamapps/common/Left 4 Dead 2/banned_ip.cfg` — Banned IP addresses
- `~/.steam/steamapps/common/Left 4 Dead 2/logs/` — Server logs directory

**Key Configuration Files:**
- **server.cfg**: Core server settings (rates, game rules, admin settings)
- **autoexec.cfg**: Commands executed on server start
- **mapcycle.txt**: Map rotation configuration
- **motd.txt**: Welcome message displayed to connecting players

## Steam Workshop
**Workshop Integration:**
- Check Steam Workshop for community content
- Subscribe to collections via Steam client
- Server automatically downloads subscribed content
- Configure workshop content loading in server configuration

## Common Mods (curated)
**Admin/Management Mods**
- Check official mod repositories or community sites for Left 4 Dead 2
- Look for server administration, anti-cheat, and quality-of-life mods
- Install according to game's modding framework (if available)

**Popular Community Mods**
- Search Steam Workshop (if supported) for highly-rated server mods
- Check game's official forums and community sites for recommended mods
- Verify mod compatibility with current server version

**Installation Notes**
- Follow each mod's specific installation instructions
- Some games require mod loading frameworks or special startup parameters
- Test mods individually before combining multiple mods

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
