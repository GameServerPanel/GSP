# DayZ Mod (Arma 2 OA) — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
arma2oaserver.exe -port=2302 -config=server.cfg -cfg=basic.cfg -profiles=profiles -name=server -world=empty -mod="@DayZ;@DayZ_Epoch" -serverMod="@DayZ_Server;@hive" -cpuCount=4 -exThreads=7 -nosplash -noPause -noSound -malloc=system -hugepages

**Parameters**
- `-port=<P>` — Game UDP port. Default 2302.
- `-config=server.cfg` — gameplay, passwords, mission rotation.
- `-cfg=basic.cfg` — networking (MaxMsgSend, packet sizes), timestamps.
- `-profiles=<dir>` — logs (`.RPT`), netlogs, dumps.
- `-name=<profile>` — creates `<profile>.ArmA2OAProfile` (difficulty, view distance).
- `-mod=` — client+server mods; load order matters.
- `-serverMod=` — server-only mods (Hive/antihack).
- `-cpuCount`, `-exThreads`, `-malloc`, `-hugepages`, `-world=empty`, `-noPause`, `-noSound`, `-nosplash`, `-logFile`, `-pid`, `-ranking`.
- `-BEpath=<dir>` — BattlEye directory.

**Ports**
- Game: **P** UDP (default 2302)
- Query: **P+1** UDP
- Steam master: **27016** UDP
- BattlEye: **2344–2345** UDP, RCon TCP as per `BEServer.cfg`

## Config Files & Locations
- Root:
  - `server.cfg` — hostname, verifySignatures, vote rules, MOTD, missions.
  - `basic.cfg` — bandwidth & network tuning.
- Profiles (`-profiles`):
  - `<name>.ArmA2OAProfile` — difficulty profile.
  - `*.RPT` — main runtime log.
- BattlEye (`Expansion/BattlEye` or `-BEpath`):
  - `BEServer.cfg` — `RConPassword`, `RConPort`.
  - `scripts.txt`, `remoteexec.txt` — filter lists.
- Keys: `keys/*.bikey` (required when `verifySignatures=2`).
- DayZ/Epoch server component:
  - `@DayZ_Server` / `@hive` with `HiveExt.dll`.
  - Mission PBO: `MPMissions/DayZ_Epoch_11.Chernarus.pbo` (example).

## Steam Workshop
Not natively used for Arma 2 OA; install `@Mod` folders manually and copy `.bikey` to `keys/`.

## Common Mods
- **DayZ Epoch**
  - **Install:** place `@DayZ_Epoch` and `@DayZ_Server` in root; add to `-mod` and `-serverMod`.
  - **Configure:** `@DayZ_Server\config.cfg` and `HiveExt.ini` (DB). Ensure server and client versions match.
- **Overwatch / Overpoch**
  - Install `@DayZOverwatch`, adjust mission PBO to Overpoch variant.
  - Merge/add `.bikey` keys.
- **Anti-hack/admin (e.g., infiSTAR)**  
  - Server-side only in `-serverMod`; follow vendor config.

## Database
- **MySQL** (port 3306). Connection is in `HiveExt.ini`:
  - `Host = <ip_or_dns>`
  - `Database = dayz`
  - `Username = dayzuser`
  - `Password = ********`
  - `Instance = 11` (matches mission instance)
- Create schema using mod-provided SQL. Index cleanup & periodic maintenance recommended.

## Administration & Scripting
**Remote Administration:**
- BattlEye RCON for server management and player control
- In-game admin tools via infiSTAR or similar anti-hack solutions
- Database admin tools for character/vehicle management

**Backup Strategy:**
- Automated daily backups of MySQL database (character data, vehicles, bases)
- Configuration file backups (server.cfg, basic.cfg, mission files)
- Rotate backups (keep 7 daily, 4 weekly, 12 monthly)
- Test database restore procedures regularly

**Auto-Update:**
- Monitor mod releases for client/server version synchronization
- Schedule updates during low-traffic periods
- Backup database and configs before applying updates
- Test mod compatibility before deploying to production

**Monitoring:**
- Database performance monitoring (connection counts, query performance)
- Server performance via .RPT logs and BattlEye reports
- Player statistics and anti-cheat logging
- Uptime and restart scheduling for stability

## Troubleshooting
- **HiveExt.dll fails / DB won’t connect:** wrong creds or MySQL not reachable; VC++ redists missing.
- **“Waiting for host” loop:** wrong `Instance` vs DB content; mission PBO mismatch; see `.RPT`.
- **Script restriction kicks:** update/merge BE filters with mod’s recommended entries.
- **Signature mismatch:** missing `.bikey` for a loaded mod; ensure `keys/` contains all and clients use same versions.
- **Severe desync:** tune `basic.cfg` bandwidth settings; reduce AI/vehicles; lower player cap.
