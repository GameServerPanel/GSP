# Counter-Strike: Condition Zero — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
```bash
hlds_run -game cstrike -port 27015 +map de_dust2 +maxplayers 16 +sv_lan 0 -pingboost 3 -sys_ticrate 1000
```

**Parameters (exhaustive, server-relevant only)**
- `-game <dir>` — Game directory (cstrike, valve, etc.).
- `-port <port>` — Server port. Default: 27015.
- `+map <mapname>` — Starting map.
- `+maxplayers <num>` — Maximum players (1-32).
- `+sv_lan <0|1>` — LAN mode.
- `+rcon_password <pass>` — RCON password.
- `+sv_password <pass>` — Server password.
- `+hostname <name>` — Server name.
- `+exec <file>` — Execute config file.
- `-pingboost <1|2|3>` — Performance optimization (Linux).
- `-sys_ticrate <rate>` — Server FPS (Linux, default: 100).
- `-secure` — Enable VAC.
- `-insecure` — Disable VAC.
- `-noipx` — Disable IPX networking.
- `-norestart` — Don't restart on crash.
- `+log <on|off>` — Enable logging.
- `-condebug` — Console debug logging.
- `+sv_region <num>` — Server region.
- `-zone <bytes>` — Memory allocation.
- `-heapsize <kb>` — Heap size in kilobytes.

**Ports**
- Game/Query: UDP **27015** (primary)
- RCON: TCP **27015** (same as game port)
- HLTV: UDP **27020** (if enabled)

## Config Files & Locations
**Windows:**
- `game_dir/server.cfg` — Main server configuration
- `game_dir/mapcycle.txt` — Map rotation list
- `game_dir/motd.txt` — Message of the day
- `game_dir/banned.cfg` — Banned users list
- `game_dir/listip.cfg` — Banned IP addresses
- `game_dir/logs/` — Server logs directory

**Linux:**
- `~/counter-strike-condition-zero/server.cfg` — Main server configuration
- `~/counter-strike-condition-zero/mapcycle.txt` — Map rotation list
- `~/counter-strike-condition-zero/motd.txt` — Message of the day
- `~/counter-strike-condition-zero/banned.cfg` — Banned users list
- `~/counter-strike-condition-zero/logs/` — Server logs directory

**Key Files:**
- **server.cfg**: Core server settings (rates, friendly fire, admin commands)
- **mapcycle.txt**: Map rotation configuration
- **motd.txt**: Welcome message for connecting players

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
