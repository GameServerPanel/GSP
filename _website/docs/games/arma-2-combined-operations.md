# Arma 2 Combined Operations — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
```bash
./arma-2-combined-operations_server -port 27015 -maxplayers 16 -config server.cfg
```

**Parameters (common server flags)**
- `-port <port>` — Server port (default varies by game)
- `-maxplayers <num>` — Maximum player count
- `-config <file>` — Configuration file to load
- `-log` — Enable logging
- `-console` — Enable console output
- `-dedicated` — Run as dedicated server
- `-name "<name>"` — Server name
- `-password "<pass>"` — Server password

**Ports**
- Game: UDP **27015** (typical default)
- Query: UDP **27016** (game port + 1)
- Admin/RCON: TCP **varies by game**

## Config Files & Locations
**Windows:**
- `arma-2-combined-operations_server.cfg` — Main server configuration
- `config/` — Configuration directory
- `logs/` — Log files directory
- `data/` — Server data and saves

**Linux:**
- `~/arma-2-combined-operations/server.cfg` — Main server configuration  
- `~/arma-2-combined-operations/config/` — Configuration directory
- `~/arma-2-combined-operations/logs/` — Log files directory
- `~/arma-2-combined-operations/data/` — Server data and saves

**Key Files:**
- **server.cfg**: Core server settings and game rules
- **admins.cfg**: Administrator configuration (if applicable)
- **banned.cfg**: Banned players list (if applicable)

## Steam Workshop
Not supported by this game.

## Common Mods (curated)
**Admin/Management Mods**
- Check official mod repositories or community sites for Arma 2 Combined Operations
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
