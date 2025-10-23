# Valheim — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
```bash
valheim_server.exe -name "My Server" -port 2456 -world "Dedicated" -password "secret" -public 1
```

**Parameters (exhaustive, server-relevant only)**
- `-name "<name>"` — Server name visible in browser.
- `-port <port>` — Server port. Default: 2456.
- `-world "<worldname>"` — World name to load/create.
- `-password "<pass>"` — Server password.
- `-public <0|1>` — Advertise on public server list.
- `-savedir "<path>"` — Save directory location.
- `-logFile "<path>"` — Log file location.
- `-saveinterval <seconds>` — World save interval.
- `-backups <num>` — Number of world backups to keep.
- `-backupshort <num>` — Short interval backup count.
- `-backuplong <num>` — Long interval backup count.
- `-crossplay` — Enable crossplay between platforms.

**Ports**
- Game: UDP **2456** (primary)
- Steam Query: UDP **2457** (game port + 1)  
- Additional: UDP **2458** (game port + 2)

## Config Files & Locations
**Windows:**
- `Data/` — Game data directory
- `Logs/` — Log files directory
- `ServerConfig/` — Configuration files (varies by game)

**Linux:**
- `~/valheim/Data/` — Game data directory
- `~/valheim/Logs/` — Log files directory
- `~/valheim/ServerConfig/` — Configuration files

**Key Files:**
- Configuration file names and locations vary significantly between Unity games
- Common patterns: server.cfg, config.json, settings.xml
- Check game-specific documentation for exact file locations

## Steam Workshop
Not supported by this game.

## Common Mods (curated)
**Admin/Management Mods**
- Check official mod repositories or community sites for Valheim
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
