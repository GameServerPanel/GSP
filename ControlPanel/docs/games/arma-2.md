# Arma 2 — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
```bash
arma2oaserver.exe -port=2302 -config=server.cfg -cfg=basic.cfg -profiles=ServerProfile -mod=@mod1;@mod2
```

**Parameters (exhaustive, server-relevant only)**
- `-port=<port>` — Server port. Default: 2302.
- `-config=<file>` — Server config file (server.cfg).
- `-cfg=<file>` — Basic config file (basic.cfg).
- `-profiles=<dir>` — Profile directory.
- `-mod=<mods>` — Mod folders (semicolon separated).
- `-serverMod=<mods>` — Server-side only mods.
- `-name=<profilename>` — Server profile name.
- `-world=<worldname>` — Empty world name.
- `-noSound` — Disable sound processing.
- `-nosplash` — Skip intro videos.
- `-noPause` — Don't pause when not focused.
- `-cpuCount=<num>` — CPU core count override.
- `-maxMem=<mb>` — Maximum memory usage.

**Ports**
- Game: UDP **2302** (primary)
- Query: UDP **2303** (game port + 1)
- BattlEye: UDP **2344** (if enabled)

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
**Admin/Management Mods**
- Check official mod repositories or community sites for Arma 2
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
