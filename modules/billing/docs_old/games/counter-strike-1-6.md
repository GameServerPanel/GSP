# Counter-Strike 1.6 (HLDS) — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
hlds_run -game cstrike -port 27015 +map de_dust2 +maxplayers 16 +sv_lan 0 +sv_region 255 +log on +exec server.cfg -pingboost 3 -sys_ticrate 1000 -secure

**Parameters (HLDS)**
- `-game cstrike` — game dir.
- `-port=<P>` — game/query on UDP P (default 27015).
- `+map`, `+maxplayers`, `+sv_lan`, `+sv_region`, `+rcon_password`, `+sv_password`.
- `-sys_ticrate <n>` — server FPS cap (Linux).
- `-pingboost {1|2|3}` — scheduling tweak (Linux).
- `-secure` — VAC on.
- `-noipx`, `-insecure` (testing only), `-norestart`, `-debug`.
- Logs: `+log on`, `-condebug`.
- Auto-update (SteamCMD wrapper): `-steam_dir`, `-steamcmd_script`.

**Ports**
- Game/Query: UDP **P** (default 27015)
- RCON: TCP on same P
- HLTV (if used): UDP **27020**

## Config Files & Locations
- `cstrike/server.cfg` — core cvars (rates, friendlyfire, mp_*, etc.)
- `cstrike/mapcycle.txt`, `cstrike/motd.txt`, `cstrike/banned.cfg`
- Logs: `cstrike/logs/`

## Steam Workshop
Not applicable (pre-Workshop). Use legacy map packs in `cstrike/maps/`.

## Common Mods
- **AMX Mod X (admin & plugins)**
  - **Install:**
    - Place AMXX files under `cstrike/addons/amxmodx/`
    - Ensure `metamod` is installed: `cstrike/addons/metamod/dlls/metamod_i386.so` (Linux) or `.dll` (Windows)
    - Edit `cstrike/liblist.gam` to point to Metamod:
      - Replace `gamedll` with `gamedll "addons/metamod/dlls/metamod.dll"` (Windows) or `gamedll_linux "addons/metamod/dlls/metamod_i386.so"`
    - Add AMXX to Metamod: `cstrike/addons/metamod/plugins.ini`:
      - `win32 addons/amxmodx/dlls/amxmodx_mm.dll`
      - `linux addons/amxmodx/dlls/amxmodx_mm_i386.so`
  - **Configure:**
    - Admins: `cstrike/addons/amxmodx/configs/users.ini`
    - Plugins: `cstrike/addons/amxmodx/configs/plugins.ini`
    - Modules: `cstrike/addons/amxmodx/configs/modules.ini`
  - **Database (optional for SQLX):**
    - `cstrike/addons/amxmodx/configs/sql.cfg` — MySQL host/db/user/pass and table prefixes.

## Database
- **Optional** for AMXX plugins (Bans, StatsX SQL, etc.) via MySQL. Configure `sql.cfg`.

## Administration & Scripting
**Remote Administration:**
- RCON (Remote Console) access for server management
- AMX Mod X admin commands and plugin system
- In-game admin commands via AMXX

**Backup Strategy:**
- Automated daily backups of save files and configuration
- Rotate backups (keep 7 daily, 4 weekly, 12 monthly)
- Test backup restoration procedures regularly
- Store backups in separate location/drive

**Auto-Update:**
- Use SteamCMD for automatic server updates
- Schedule updates during low-traffic periods
- Backup before applying updates
- Monitor for update announcements and patch notes

**Monitoring:**
- Server performance monitoring (CPU, memory, network)
- Player connection logs and statistics
- Error log monitoring and alerting
- Uptime tracking and availability reporting

## Troubleshooting
- **“Bad load” for AMXX:** wrong Metamod path or architecture; check `meta list` in server console.
- **Players kicked for “Consistency”:** `sv_consistency` and custom models/sounds; adjust whitelist or remove conflicting files.
- **High choke/lag:** tune `sv_maxrate`, `sv_minrate`, `sv_maxupdaterate`; ensure sufficient upstream bandwidth; avoid outrageous `sys_ticrate`.
