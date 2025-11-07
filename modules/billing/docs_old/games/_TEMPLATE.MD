# <Game Name> — Complete Dedicated Server Guide

## Startup Parameters
**Default command line**
<Path/Executable> <all required flags here> # exact for this game, not generic

**Parameters (exhaustive, server-relevant only)**
- `-flagA=<value>` — What it does. Default: `<default>`. Notes / constraints.
- `-flagB` — …
- (List every valid server flag, 10–40+ as applicable. Include networking, logging, perf, mod-loading, VAC/BE/EAC, RCON, headless clients, etc.)

**Ports**
- Game UDP: **P** (assigned)  
- Query/Steam sockets/etc: **P+1**, **P+2**, …  
- RCON / BE / SourceTV / etc: exact numbers or relative math  
(Spell out all auxiliary ports, protocols, fixed vs relative.)

## Config Files & Locations
- `<path>/server.cfg` — description
- `<path>/basic.cfg` — …
- `<path>/logs/*.log` — …
- **Mods**: where mod files go, keys/signatures, load order files.
- **Workshop cache** (if relevant).
- Include common paths for Windows & Linux.

## Steam Workshop (if supported)
- How to mount collections / API keys / start map IDs.
- Where files cache on disk.
- Any special caveats.

## Common Mods (curated)
- **Mod A (e.g., AMX Mod X / ACE3 / Exile / RocketMod / Oxide / uMod / BepInEx / DarkRP / EssentialsX)**  
  - What it’s for.
  - **Install**: exact folder paths, files to edit, any keys/signatures, load order.
  - **Configure**: main config files + common options.
  - (Repeat for each widely used mod for this game.)

## Database (if used)
- Engine (e.g., **MySQL**, SQLite, PostgreSQL).
- Connection file & keys to edit (e.g., `HiveExt.ini`, `database.json`, `config.yml`).
- Schema notes, migrations, indexes, backup/restore steps.

## Administration & Scripting (if applicable)
- RCON tools / admin plugins.
- Backups, rotation, auto-update strategy.

## Troubleshooting (game-specific)
- Symptom → likely cause → resolution, with file/flag names.
- Include all common errors seen in forums/issue trackers for this game.
- Networking, signature/anticheat, mod version mismatches, perf, crashes, persistence, etc.
