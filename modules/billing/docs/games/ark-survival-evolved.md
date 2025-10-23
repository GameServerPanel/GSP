# ARK: Survival Evolved — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
```bash
ShooterGameServer.exe TheIsland?listen?SessionName="My ARK Server"?ServerPassword=""?ServerAdminPassword="adminpass"?Port=7777?QueryPort=27015?MaxPlayers=70
```

**Parameters (exhaustive, server-relevant only)**
- Map name (TheIsland, Ragnarok, Extinction, etc.) — Starting map.
- `?listen` — Enable server listening.
- `?SessionName="<name>"` — Server name in browser.
- `?ServerPassword="<pass>"` — Server password.
- `?ServerAdminPassword="<pass>"` — Admin password.
- `?Port=<port>` — Game port. Default: 7777.
- `?QueryPort=<port>` — Query port. Default: 27015.
- `?MaxPlayers=<num>` — Maximum players (1-200).
- `?PVE=<true|false>` — PvE mode.
- `?RCONEnabled=<true|false>` — Enable RCON.
- `?RCONPort=<port>` — RCON port.
- `?DifficultyOffset=<0.0-1.0>` — Difficulty level.
- `?OverrideOfficialDifficulty=<1.0-10.0>` — Override difficulty.
- `?TamingSpeedMultiplier=<1.0+>` — Taming speed.
- `?XPMultiplier=<1.0+>` — Experience multiplier.
- `?HarvestAmountMultiplier=<1.0+>` — Harvest amount.
- `?AllowFlyerCarryPvE=<true|false>` — Allow flyer carry in PvE.
- `?AlwaysAllowStructurePickup=<true|false>` — Allow structure pickup.
- `?BattlEye=<true|false>` — Enable BattlEye anti-cheat.
- `-log` — Enable logging.
- `-NoHangDetection` — Disable hang detection.
- `-UseDynamicConfig` — Use dynamic configuration.
- `-ConfigSubDir=<dir>` — Config subdirectory.

**Ports**
- Game: UDP **7777** (primary)
- Query: UDP **27015** (Steam query)
- RCON: TCP **27020** (if enabled)
- Raw UDP: UDP **7778** (game port + 1)

## Config Files & Locations
**Windows:**
- `WindowsServer/ARK: Survival Evolved/Saved/Config/WindowsServer/` — Configuration directory
- `WindowsServer/ARK: Survival Evolved/Saved/Logs/` — Log files
- `WindowsServer/ARK: Survival Evolved/Saved/SaveGames/` — Save files

**Linux:**
- `/home/ark-survival-evolved/Saved/Config/LinuxServer/` — Configuration directory
- `/home/ark-survival-evolved/Saved/Logs/` — Log files
- `/home/ark-survival-evolved/Saved/SaveGames/` — Save files

**Key Files:**
- **GameUserSettings.ini**: Main server configuration
- **Game.ini**: Advanced game settings
- **Engine.ini**: Engine-specific settings

## Steam Workshop
**Mod Installation:**
1. Subscribe to mods via Steam Workshop or ARK server manager
2. Add mod IDs to server startup parameters or GameUserSettings.ini
3. Server downloads mods automatically on startup

**Configuration:**
- Windows: `ShooterGame/Saved/Config/WindowsServer/GameUserSettings.ini`
- Linux: `ShooterGame/Saved/Config/LinuxServer/GameUserSettings.ini`
- Add line: `ActiveMods=mod_id_1,mod_id_2,mod_id_3`

**Mod Loading:**
- Mods load in the order specified in ActiveMods
- Some mods have dependencies that must load first
- Server must restart after mod changes

**Cache Location:**
- Windows: `steamapps/workshop/content/346110/`
- Linux: `~/.steam/steamapps/workshop/content/346110/`

## Common Mods (curated)
**Admin/Management Mods**
- Check official mod repositories or community sites for ARK: Survival Evolved
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
**Engine**: SQLite

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
