# Hell Let Loose — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
```bash
Hell Let LooseServer.exe -log -Port=7777 -QueryPort=27015
```

**Parameters (common Unreal server flags)**
- `-log` — Enable logging.
- `-Port=<port>` — Game port.
- `-QueryPort=<port>` — Steam query port.
- `-MaxPlayers=<num>` — Maximum players.
- `-ServerName="<name>"` — Server name.
- `-ServerPassword="<pass>"` — Server password.

**Ports**
- Game: UDP **7777** (typical)
- Query: UDP **27015** (Steam query)

## Config Files & Locations
**Windows:**
- `WindowsServer/Hell Let Loose/Saved/Config/WindowsServer/` — Configuration directory
- `WindowsServer/Hell Let Loose/Saved/Logs/` — Log files
- `WindowsServer/Hell Let Loose/Saved/SaveGames/` — Save files

**Linux:**
- `/home/hell-let-loose/Saved/Config/LinuxServer/` — Configuration directory
- `/home/hell-let-loose/Saved/Logs/` — Log files
- `/home/hell-let-loose/Saved/SaveGames/` — Save files

**Key Files:**
- **GameUserSettings.ini**: Main server configuration
- **Game.ini**: Advanced game settings
- **Engine.ini**: Engine-specific settings

## Steam Workshop
Not supported by this game.

## Common Mods (curated)
**Admin/Management Mods**
- Check official mod repositories or community sites for Hell Let Loose
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
