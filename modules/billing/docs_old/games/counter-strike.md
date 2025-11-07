# Counter-Strike — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
```bash
srcds_run -game cstrike -console -usercon +hostport 27015 +map de_dust2 +maxplayers 16 +exec server.cfg
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
- `steamapps/common/Counter-Strike/cfg/server.cfg` — Main server configuration
- `steamapps/common/Counter-Strike/cfg/autoexec.cfg` — Auto-executed commands
- `steamapps/common/Counter-Strike/mapcycle.txt` — Map rotation list
- `steamapps/common/Counter-Strike/motd.txt` — Message of the day
- `steamapps/common/Counter-Strike/banned_user.cfg` — Banned users
- `steamapps/common/Counter-Strike/banned_ip.cfg` — Banned IP addresses
- `steamapps/common/Counter-Strike/logs/` — Server logs directory

**Linux:**
- `~/.steam/steamapps/common/Counter-Strike/cfg/server.cfg` — Main server configuration
- `~/.steam/steamapps/common/Counter-Strike/cfg/autoexec.cfg` — Auto-executed commands
- `~/.steam/steamapps/common/Counter-Strike/mapcycle.txt` — Map rotation list
- `~/.steam/steamapps/common/Counter-Strike/motd.txt` — Message of the day
- `~/.steam/steamapps/common/Counter-Strike/banned_user.cfg` — Banned users
- `~/.steam/steamapps/common/Counter-Strike/banned_ip.cfg` — Banned IP addresses
- `~/.steam/steamapps/common/Counter-Strike/logs/` — Server logs directory

**Key Configuration Files:**
- **server.cfg**: Core server settings (rates, game rules, admin settings)
- **autoexec.cfg**: Commands executed on server start
- **mapcycle.txt**: Map rotation configuration
- **motd.txt**: Welcome message displayed to connecting players

## Steam Workshop
Not supported by this game.

## Common Mods (curated)
- **AMX Mod X**
  - **Purpose**: Complete admin and scripting framework for GoldSrc games.
  - **Install**: Download from amxmodx.org, extract to game directory, add `meta load addons/amxmodx/dlls/amxmodx_mm` to `addons/metamod/plugins.ini`.
  - **Configure**: Edit `addons/amxmodx/configs/amxx.cfg` for basic settings, `configs/users.ini` for admin users.

- **Metamod**
  - **Purpose**: Plugin loading framework required by most mods.
  - **Install**: Extract metamod.dll to `addons/metamod/dlls/`, add `gamedll_linux "addons/metamod/dlls/metamod.so"` to liblist.gam.
  - **Configure**: Plugins list in `addons/metamod/plugins.ini`.

- **StatsMe**
  - **Purpose**: Player statistics tracking and ranking system.
  - **Install**: Requires AMX Mod X, install plugin files to `addons/amxmodx/plugins/`.
  - **Configure**: Database settings in plugin configuration files.

- **PodBot MM**
  - **Purpose**: AI bots for offline practice or filling servers.
  - **Install**: Extract to game directory, requires Metamod.
  - **Configure**: Bot skills and behavior in `podbot/podbot.cfg`.

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
**"Server not appearing in browser"**
- **Cause**: Missing Game Server Login Token or firewall blocking ports
- **Fix**: Add `+sv_setsteamaccount <token>` to startup, verify ports 27015 UDP/TCP are open

**"VAC Unable to verify"**
- **Cause**: Modified game files or outdated server binaries
- **Fix**: Verify server files integrity via SteamCMD, remove custom plugins temporarily

**"Map change crashes server"**
- **Cause**: Invalid map file or insufficient memory
- **Fix**: Verify map file integrity, increase server memory allocation, check map compatibility

**"High CPU usage/lag"**
- **Cause**: Incorrect tickrate settings or too many plugins
- **Fix**: Adjust `-tickrate` parameter, disable unnecessary plugins, optimize server.cfg rates

**"RCON not working"**
- **Cause**: Incorrect password or blocked TCP port
- **Fix**: Verify `rcon_password` setting, ensure TCP port (same as game port) is accessible

**"Players getting kicked for 'Authentication timeout'"**
- **Cause**: Steam authentication issues or network problems
- **Fix**: Check internet connectivity, verify Steam services status, adjust timeout settings
