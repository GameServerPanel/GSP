# Squad — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
```bash
SquadGameServer.exe Port=7787 QueryPort=27165 RCON=21114 FIXEDMAXPLAYERS=80 RANDOM=NONE
```

**Parameters (exhaustive, server-relevant only)**
- `Port=<port>` — Game port. Default: 7787.
- `QueryPort=<port>` — Steam query port. Default: 27165.
- `RCON=<port>` — RCON port. Default: 21114.
- `FIXEDMAXPLAYERS=<num>` — Maximum players (1-100).
- `RANDOM=<NONE|FULL|LIMITED>` — Random map selection.
- `FIXEDVRFILLTHRESHOLD=<num>` — VR fill threshold.
- `FIXEDMAXTICKRATE=<rate>` — Maximum tick rate.

**Ports**
- Game: UDP **7787** (primary)
- Query: UDP **27165** (Steam query)
- RCON: TCP **21114** (administration)

## Config Files & Locations
**Windows:**
- `WindowsServer/Squad/Saved/Config/WindowsServer/` — Configuration directory
- `WindowsServer/Squad/Saved/Logs/` — Log files
- `WindowsServer/Squad/Saved/SaveGames/` — Save files

**Linux:**
- `/home/squad/Saved/Config/LinuxServer/` — Configuration directory
- `/home/squad/Saved/Logs/` — Log files
- `/home/squad/Saved/SaveGames/` — Save files

**Key Files:**
- **GameUserSettings.ini**: Main server configuration
- **Game.ini**: Advanced game settings
- **Engine.ini**: Engine-specific settings

## Steam Workshop
**Workshop Integration:**
- Check Steam Workshop for community content
- Subscribe to collections via Steam client
- Server automatically downloads subscribed content
- Configure workshop content loading in server configuration

## Common Mods (curated)
**Admin/Management Mods**
- Check official mod repositories or community sites for Squad
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
