# Counter-Strike 2 — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
```bash
cs2 -dedicated +map de_dust2 +maxplayers 10 +sv_setsteamaccount YOUR_GSLT +game_type 0 +game_mode 1
```

**Parameters (exhaustive, server-relevant only)**
- `-dedicated` — Run as dedicated server.
- `+map <mapname>` — Starting map. Required.
- `+maxplayers <num>` — Maximum players (2-64).
- `+sv_setsteamaccount <token>` — Game Server Login Token. Required for public servers.
- `+game_type <num>` — Game type (0=Classic Casual, 1=Classic Competitive).
- `+game_mode <num>` — Game mode (0=Casual, 1=Competitive, 2=Wingman, 3=Arms Race, 4=Demolition).
- `+hostname <name>` — Server name in browser.
- `+sv_password <pass>` — Server password.
- `+rcon_password <pass>` — RCON password.
- `-port <port>` — Server port. Default: 27015.
- `-ip <address>` — Bind IP address.
- `+sv_lan <0|1>` — LAN mode.
- `+exec <file>` — Execute config file.
- `-usercon` — Enable user console.
- `-console` — Enable console output.
- `+sv_region <num>` — Server region.
- `+sv_cheats <0|1>` — Enable cheats (for practice).
- `+mp_autokick <0|1>` — Auto-kick idle players.
- `+mp_autoteambalance <0|1>` — Auto team balance.
- `+sv_logfile <0|1>` — Enable logging.
- `-tickrate <rate>` — Server tickrate (64 or 128).

**Ports**
- Game: UDP **27015** (primary)
- RCON: TCP **27015** (same as game port)
- Steam Query: UDP **27016** (game port + 1)
- GOTV: UDP **27020** (game port + 5)

## Config Files & Locations
**Windows:**
- `steamapps/common/Counter-Strike 2/cfg/server.cfg` — Main server configuration
- `steamapps/common/Counter-Strike 2/cfg/autoexec.cfg` — Auto-executed commands
- `steamapps/common/Counter-Strike 2/mapcycle.txt` — Map rotation list
- `steamapps/common/Counter-Strike 2/motd.txt` — Message of the day
- `steamapps/common/Counter-Strike 2/banned_user.cfg` — Banned users
- `steamapps/common/Counter-Strike 2/banned_ip.cfg` — Banned IP addresses
- `steamapps/common/Counter-Strike 2/logs/` — Server logs directory

**Linux:**
- `~/.steam/steamapps/common/Counter-Strike 2/cfg/server.cfg` — Main server configuration
- `~/.steam/steamapps/common/Counter-Strike 2/cfg/autoexec.cfg` — Auto-executed commands
- `~/.steam/steamapps/common/Counter-Strike 2/mapcycle.txt` — Map rotation list
- `~/.steam/steamapps/common/Counter-Strike 2/motd.txt` — Message of the day
- `~/.steam/steamapps/common/Counter-Strike 2/banned_user.cfg` — Banned users
- `~/.steam/steamapps/common/Counter-Strike 2/banned_ip.cfg` — Banned IP addresses
- `~/.steam/steamapps/common/Counter-Strike 2/logs/` — Server logs directory

**Key Configuration Files:**
- **server.cfg**: Core server settings (rates, game rules, admin settings)
- **autoexec.cfg**: Commands executed on server start
- **mapcycle.txt**: Map rotation configuration
- **motd.txt**: Welcome message displayed to connecting players

## Steam Workshop
**Collection Mounting:**
1. Create Steam Workshop collection with desired maps/content
2. Add collection ID to server startup: `+host_workshop_collection <collection_id>`
3. Add Steam Web API key: `+sv_setsteamaccount <game_server_token>`

**Map Starting:**
- Use workshop map IDs: `+map workshop/<map_id>`
- Example: `+map workshop/125438255`

**Cache Location:**
- Windows: `steamapps/workshop/content/<app_id>/`
- Linux: `~/.steam/steamapps/workshop/content/<app_id>/`

**API Key Setup:**
1. Get Game Server Login Token from: https://steamcommunity.com/dev/managegameservers
2. Add to startup parameters: `+sv_setsteamaccount <token>`

**Workshop Content Updates:**
- Content updates automatically when server restarts
- Force update with `workshop_download_item <app_id> <item_id>` console command

## Common Mods (curated)
- **SourceMod (CS:GO Legacy)**
  - **Purpose**: Admin framework for CS:GO servers.
  - **Install**: Download SourceMod for CS:GO, requires MetaMod:Source.
  - **Configure**: Admin configuration in `addons/sourcemod/configs/`.

- **CS2 Server Manager (CS2)**
  - **Purpose**: Modern admin framework for Counter-Strike 2.
  - **Install**: Follow CS2-specific installation guides.
  - **Configure**: Configuration varies by chosen admin system.

- **Practice Mode Plugins**
  - **Purpose**: Enable practice configurations for competitive training.
  - **Install**: Various practice plugins available for both CS:GO and CS2.
  - **Configure**: Practice commands and features configuration.

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
