#!/usr/bin/env python3
"""
Game Server Guide Generator
Generates comprehensive host-agnostic server guides for all games in CSV.
"""

import csv
import re
import os
from pathlib import Path

class GameGuideGenerator:
    def __init__(self):
        self.csv_path = "all_hostable_games_union.csv"
        self.docs_dir = Path("docs/games")
        
        # Game engine mappings for specific content generation
        self.engine_mapping = {
            # Source Engine games
            'counter-strike': 'Source',
            'counter-strike-2': 'Source 2', 
            'counter-strike-global-offensive': 'Source',
            'counter-strike-source': 'Source',
            'counter-strike-condition-zero': 'GoldSrc',
            'team-fortress-2': 'Source',
            'left-4-dead-2': 'Source',
            'half-life-2-deathmatch': 'Source',
            'garry-s-mod': 'Source',
            'day-of-defeat-source': 'Source',
            
            # Unreal Engine games
            'ark-survival-evolved': 'Unreal Engine 4',
            'ark-survival-ascended': 'Unreal Engine 5',
            'squad': 'Unreal Engine 4',
            'mordhau': 'Unreal Engine 4',
            'hell-let-loose': 'Unreal Engine 4',
            'conan-exiles': 'Unreal Engine 4',
            
            # Unity games  
            'rust': 'Unity',
            '7-days-to-die': 'Unity',
            'hurtworld': 'Unity',
            'unturned': 'Unity',
            'valheim': 'Unity',
            
            # Arma/Real Virtuality
            'arma-3': 'Real Virtuality 4',
            'arma-2': 'Real Virtuality 3',
            'arma-2-operation-arrowhead': 'Real Virtuality 3',
            'arma-reforger': 'Enfusion',
            'dayz': 'Enfusion',
            'dayz-mod-for-arma2co': 'Real Virtuality 3',
            
            # Java games
            'minecraft': 'Java',
            
            # Other engines
            'factorio': 'Custom',
            'terraria': 'XNA/MonoGame',
            'space-engineers': 'VRAGE 2.0',
        }
        
    def slugify(self, name):
        """Convert game name to slug format"""
        slug = re.sub(r'[^a-zA-Z0-9]+', '-', name.lower())
        return slug.strip('-')
    
    def read_games_from_csv(self):
        """Read all games from CSV file"""
        games = []
        with open(self.csv_path, 'r') as f:
            reader = csv.reader(f)
            for i, row in enumerate(reader):
                if i == 0 or not row or row[0] == 'Game':  # Skip header
                    continue
                game_name = row[0].strip()
                if game_name and not game_name.startswith('#'):
                    games.append(game_name)
        return games
    
    def get_engine_for_game(self, slug):
        """Get game engine for specific content generation"""
        return self.engine_mapping.get(slug, 'Unknown')
    
    def generate_startup_parameters(self, game_name, slug, engine):
        """Generate game-specific startup parameters"""
        
        if engine == 'Source':
            return self.generate_source_startup(game_name, slug)
        elif engine == 'Source 2':
            return self.generate_source2_startup(game_name, slug)
        elif engine == 'GoldSrc':
            return self.generate_goldsrc_startup(game_name, slug)
        elif 'Unreal' in engine:
            return self.generate_unreal_startup(game_name, slug)
        elif engine == 'Unity':
            return self.generate_unity_startup(game_name, slug)
        elif 'Real Virtuality' in engine or engine == 'Enfusion':
            return self.generate_arma_startup(game_name, slug)
        elif engine == 'Java' and 'minecraft' in slug:
            return self.generate_minecraft_startup(game_name, slug)
        else:
            return self.generate_generic_startup(game_name, slug)
    
    def generate_source_startup(self, game_name, slug):
        """Source engine specific startup parameters"""
        game_dir = slug.replace('-', '')
        if 'counter-strike' in slug:
            game_dir = 'cstrike' if 'source' not in slug else 'cstrike'
        elif 'team-fortress' in slug:
            game_dir = 'tf'
        elif 'left-4-dead' in slug:
            game_dir = 'left4dead2'
        elif 'garry' in slug:
            game_dir = 'garrysmod'
        elif 'day-of-defeat' in slug:
            game_dir = 'dod'
        elif 'half-life' in slug:
            game_dir = 'hl2mp'
            
        return f"""**Default command line**
```bash
srcds_run -game {game_dir} -console -usercon +hostport 27015 +map de_dust2 +maxplayers 16 +exec server.cfg
```

**Parameters (exhaustive, server-relevant only)**
- `-game <dir>` — Game directory. Required.
- `-console` — Enable server console output.
- `-usercon` — Enable user console commands.
- `+hostport <port>` — Server port (UDP). Default: 27015.
- `+ip <address>` — Bind to specific IP address.
- `+map <mapname>` — Starting map. Required.
- `+maxplayers <num>` — Maximum players (1-64).
- `+exec <file>` — Execute config file on startup.
- `+sv_setsteamaccount <token>` — Game Server Login Token for public servers.
- `+rcon_password <pass>` — RCON password for remote administration.
- `+sv_password <pass>` — Server password for private games.
- `+hostname <name>` — Server name in browser.
- `+sv_lan <0|1>` — LAN mode (0=Internet, 1=LAN only).
- `-tickrate <rate>` — Server tickrate (default: 66, competitive: 128).
- `-port <port>` — Alternative syntax for hostport.
- `-nohltv` — Disable SourceTV.
- `+tv_enable <0|1>` — Enable/disable SourceTV.
- `+tv_port <port>` — SourceTV port (default: hostport + 5).
- `-secure` — Enable VAC (Valve Anti-Cheat).
- `-insecure` — Disable VAC (for testing only).
- `+sv_region <num>` — Server region (255=world, 0-7=specific regions).
- `+fps_max <fps>` — Server FPS limit.
- `-threads <num>` — Number of worker threads.
- `-norestart` — Don't restart server on crash.
- `+log <on|off>` — Enable/disable logging.
- `-condebug` — Log console output to file.
- `+sv_logfile <0|1>` — Enable server logging.
- `+sv_logflush <0|1>` — Flush logs immediately.

**Ports**
- Game: UDP **27015** (primary)
- RCON: TCP **27015** (same as game port)
- SourceTV: UDP **27020** (game port + 5)
- Steam Query: UDP **27016** (game port + 1)"""
        
    def generate_source2_startup(self, game_name, slug):
        """Source 2 engine (CS2) specific startup parameters"""
        return f"""**Default command line**
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
- GOTV: UDP **27020** (game port + 5)"""

    def generate_goldsrc_startup(self, game_name, slug):
        """GoldSrc engine (CS 1.6, etc.) specific startup parameters"""
        return f"""**Default command line**
```bash
hlds_run -game cstrike -port 27015 +map de_dust2 +maxplayers 16 +sv_lan 0 -pingboost 3 -sys_ticrate 1000
```

**Parameters (exhaustive, server-relevant only)**
- `-game <dir>` — Game directory (cstrike, valve, etc.).
- `-port <port>` — Server port. Default: 27015.
- `+map <mapname>` — Starting map.
- `+maxplayers <num>` — Maximum players (1-32).
- `+sv_lan <0|1>` — LAN mode.
- `+rcon_password <pass>` — RCON password.
- `+sv_password <pass>` — Server password.
- `+hostname <name>` — Server name.
- `+exec <file>` — Execute config file.
- `-pingboost <1|2|3>` — Performance optimization (Linux).
- `-sys_ticrate <rate>` — Server FPS (Linux, default: 100).
- `-secure` — Enable VAC.
- `-insecure` — Disable VAC.
- `-noipx` — Disable IPX networking.
- `-norestart` — Don't restart on crash.
- `+log <on|off>` — Enable logging.
- `-condebug` — Console debug logging.
- `+sv_region <num>` — Server region.
- `-zone <bytes>` — Memory allocation.
- `-heapsize <kb>` — Heap size in kilobytes.

**Ports**
- Game/Query: UDP **27015** (primary)
- RCON: TCP **27015** (same as game port)
- HLTV: UDP **27020** (if enabled)"""

    def generate_unreal_startup(self, game_name, slug):
        """Unreal Engine specific startup parameters"""
        if 'ark' in slug:
            return self.generate_ark_startup(game_name, slug)
        elif 'squad' in slug:
            return self.generate_squad_startup(game_name, slug)
        elif 'conan' in slug:
            return self.generate_conan_startup(game_name, slug)
        else:
            return self.generate_generic_unreal_startup(game_name, slug)
    
    def generate_ark_startup(self, game_name, slug):
        """ARK specific startup parameters"""
        executable = "ArkAscendedServer.exe" if "ascended" in slug else "ShooterGameServer.exe"
        return f"""**Default command line**
```bash
{executable} TheIsland?listen?SessionName="My ARK Server"?ServerPassword=""?ServerAdminPassword="adminpass"?Port=7777?QueryPort=27015?MaxPlayers=70
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
- Raw UDP: UDP **7778** (game port + 1)"""

    def generate_squad_startup(self, game_name, slug):
        """Squad specific startup parameters"""
        return f"""**Default command line**
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
- RCON: TCP **21114** (administration)"""

    def generate_conan_startup(self, game_name, slug):
        """Conan Exiles specific startup parameters"""
        return f"""**Default command line**
```bash
ConanSandboxServer.exe -log -Port=7777 -QueryPort=27015 -MaxPlayers=40
```

**Parameters (exhaustive, server-relevant only)**
- `-log` — Enable logging.
- `-Port=<port>` — Game port. Default: 7777.
- `-QueryPort=<port>` — Query port. Default: 27015.
- `-MaxPlayers=<num>` — Maximum players (1-40).
- `-ServerName="<name>"` — Server name.
- `-ServerPassword="<pass>"` — Server password.
- `-AdminPassword="<pass>"` — Admin password.
- `-RconEnabled=<True|False>` — Enable RCON.
- `-RconPort=<port>` — RCON port.
- `-RconPassword="<pass>"` — RCON password.

**Ports**
- Game: UDP **7777** (primary)  
- Query: UDP **27015** (Steam query)
- RCON: TCP **25575** (if enabled)"""

    def generate_generic_unreal_startup(self, game_name, slug):
        """Generic Unreal Engine startup parameters"""
        return f"""**Default command line**
```bash
{game_name}Server.exe -log -Port=7777 -QueryPort=27015
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
- Query: UDP **27015** (Steam query)"""

    def generate_unity_startup(self, game_name, slug):
        """Unity engine specific startup parameters"""
        if 'rust' in slug:
            return self.generate_rust_startup(game_name, slug)
        elif '7-days' in slug:
            return self.generate_7dtd_startup(game_name, slug)
        elif 'valheim' in slug:
            return self.generate_valheim_startup(game_name, slug)
        else:
            return self.generate_generic_unity_startup(game_name, slug)
    
    def generate_7dtd_startup(self, game_name, slug):
        """7 Days to Die specific startup parameters"""
        return f"""**Default command line**
```bash
7DaysToDieServer.exe -configfile=serverconfig.xml -quit -batchmode -nographics -dedicated
```

**Parameters (exhaustive, server-relevant only)**
- `-configfile=<file>` — Server configuration XML file.
- `-quit` — Quit after completing operations.
- `-batchmode` — Run in batch mode without GUI.
- `-nographics` — Disable graphics rendering.
- `-dedicated` — Run as dedicated server.
- `-logfile <path>` — Log file location.
- `-UserDataFolder=<path>` — User data directory.
- `-SaveGameFolder=<path>` — Save game directory.
- `-configfile=<path>` — Configuration file path.

**Ports**
- Game: UDP **26900** (primary)
- Steam Query: UDP **26901** (game port + 1)
- Web Control Panel: TCP **8080** (if enabled)
- Telnet: TCP **8081** (if enabled)"""

    def generate_valheim_startup(self, game_name, slug):
        """Valheim specific startup parameters"""
        return f"""**Default command line**
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
- Additional: UDP **2458** (game port + 2)"""

    def generate_generic_unity_startup(self, game_name, slug):
        """Generic Unity engine startup parameters"""
        return f"""**Default command line**
```bash
{slug}_server.exe -batchmode -nographics -dedicated -port 27015
```

**Parameters (common Unity server flags)**
- `-batchmode` — Run without GUI.
- `-nographics` — Disable graphics rendering.
- `-dedicated` — Dedicated server mode.
- `-port <port>` — Server port.
- `-logFile <path>` — Log file location.
- `-quit` — Quit after operations complete.

**Ports**
- Game: UDP **27015** (typical)
- Query: UDP **27016** (game port + 1)"""
    
    def generate_rust_startup(self, game_name, slug):
        """Rust specific startup parameters"""
        return f"""**Default command line**
```bash
RustDedicated.exe -batchmode -nographics +server.port 28015 +server.identity "my_server" +server.hostname "My Rust Server" +server.maxplayers 100 +server.worldsize 4000 +server.seed 1234567
```

**Parameters (exhaustive, server-relevant only)**
- `-batchmode` — Run in batch mode (no graphics).
- `-nographics` — Disable graphics rendering.
- `+server.port <port>` — Server port. Default: 28015.
- `+server.identity "<name>"` — Server identity folder name.
- `+server.hostname "<name>"` — Server name in browser.
- `+server.maxplayers <num>` — Maximum players (1-500).
- `+server.worldsize <size>` — World size (1000-8000).
- `+server.seed <num>` — World generation seed.
- `+server.level "<map>"` — Map name (Procedural Map, custom maps).
- `+server.tickrate <rate>` — Server tickrate (10-30).
- `+rcon.port <port>` — RCON port.
- `+rcon.password "<pass>"` — RCON password.
- `+rcon.web <0|1>` — Enable web RCON.
- `+server.saveinterval <seconds>` — Save interval.
- `+server.globalchat <true|false>` — Global chat enabled.
- `+server.description "<text>"` — Server description.
- `+server.headerimage "<url>"` — Header image URL.
- `+server.url "<url>"` — Server website URL.
- `+server.pve <true|false>` — PvE mode.
- `+decay.scale <multiplier>` — Decay rate multiplier.
- `+fps.limit <fps>` — FPS limit.
- `-logfile <path>` — Log file path.

**Ports**
- Game: UDP **28015** (primary)
- RCON: TCP **28016** (game port + 1)
- App: TCP **28082** (Rust+ companion app)"""

    def generate_minecraft_startup(self, game_name, slug):
        """Minecraft specific startup parameters"""
        return f"""**Default command line**
```bash
java -Xmx4G -Xms4G -jar server.jar nogui
```

**Parameters (exhaustive, server-relevant only)**
- `-Xmx<size>` — Maximum heap size (e.g., -Xmx4G).
- `-Xms<size>` — Initial heap size (e.g., -Xms4G).
- `-jar <file>` — Server JAR file (server.jar, paper.jar, etc.).
- `nogui` — Run without GUI.
- `-Dfile.encoding=UTF-8` — Set file encoding.
- `-Djava.awt.headless=true` — Headless mode.
- `-XX:+UseG1GC` — Use G1 garbage collector.
- `-XX:+ParallelRefProcEnabled` — Parallel reference processing.
- `-XX:MaxGCPauseMillis=200` — Maximum GC pause time.
- `-XX:+UnlockExperimentalVMOptions` — Unlock experimental VM options.
- `-XX:+DisableExplicitGC` — Disable explicit GC calls.
- `-XX:G1NewSizePercent=30` — G1 new generation size.
- `-XX:G1MaxNewSizePercent=40` — G1 max new generation size.
- `-XX:G1HeapRegionSize=8M` — G1 heap region size.
- `-XX:G1ReservePercent=20` — G1 reserve percent.
- `-Dcom.mojang.eula.agree=true` — Auto-accept EULA.

**Ports**
- Game: TCP **25565** (primary)
- Query: UDP **25565** (same as game port, if enabled)
- RCON: TCP **25575** (if enabled in server.properties)"""

    def generate_arma_startup(self, game_name, slug):
        """Arma/Real Virtuality engine specific startup parameters"""
        if 'dayz' in slug and 'mod' not in slug:
            return self.generate_dayz_startup(game_name, slug)
        elif 'arma-3' in slug:
            return self.generate_arma3_startup(game_name, slug)
        else:
            return self.generate_arma2_startup(game_name, slug)
    
    def generate_dayz_startup(self, game_name, slug):
        """DayZ Standalone specific startup parameters"""
        return f"""**Default command line**
```bash
DayZServer_x64.exe -config=serverDZ.cfg -port=2302 -profiles=ServerProfile -dologs -adminlog -netlog -freezecheck
```

**Parameters (exhaustive, server-relevant only)**
- `-config=<file>` — Server config file (serverDZ.cfg).
- `-port=<port>` — Server port. Default: 2302.
- `-profiles=<dir>` — Profile directory name.
- `-dologs` — Enable logging.
- `-adminlog` — Enable admin logging.
- `-netlog` — Enable network logging.
- `-freezecheck` — Enable freeze detection.
- `-scriptDebug=<true|false>` — Enable script debugging.
- `-filePatching` — Enable file patching (dev mode).
- `-scrAllowFileWrite` — Allow script file writing.
- `-limitFPS=<fps>` — Limit server FPS.
- `-cpuCount=<num>` — CPU core count override.
- `-maxMem=<mb>` — Maximum memory usage.
- `-malloc=<system>` — Memory allocator (system, tbb4malloc_bi).
- `-exThreads=<num>` — Extra threads count.
- `-enableHT` — Enable hyperthreading.
- `-hugepages` — Enable huge pages (Linux).
- `-BEpath=<path>` — BattlEye path.
- `-instanceId=<num>` — Server instance ID.

**Ports**
- Game: UDP **2302** (primary)
- Steam Query: UDP **27016** (fixed)
- BattlEye: UDP **2344** (game port + 42)
- VON: UDP **2303** (game port + 1)"""

    def generate_arma3_startup(self, game_name, slug):
        """Arma 3 specific startup parameters"""
        return f"""**Default command line**
```bash
arma3server_x64.exe -port=2302 -config=server.cfg -cfg=basic.cfg -profiles=ServerProfile -name=server -serverMod=""
```

**Parameters (exhaustive, server-relevant only)**
- `-port=<port>` — Server port. Default: 2302.
- `-config=<file>` — Server config file (server.cfg).
- `-cfg=<file>` — Basic config file (basic.cfg).
- `-profiles=<dir>` — Profile directory.
- `-name=<profilename>` — Server profile name.
- `-serverMod="<mods>"` — Server-side only mods (semicolon separated).
- `-mod="<mods>"` — Client and server mods (semicolon separated).
- `-world=<worldname>` — Empty world for headless client.
- `-autoInit` — Auto-initialize mission.
- `-loadMissionToMemory` — Load mission into memory.
- `-noSound` — Disable sound.
- `-enableHT` — Enable hyperthreading.
- `-hugepages` — Enable huge pages (Linux).
- `-malloc=<allocator>` — Memory allocator.
- `-maxMem=<mb>` — Maximum memory.
- `-cpuCount=<num>` — CPU core count.
- `-exThreads=<num>` — Extra threads.

**Ports**
- Game: UDP **2302** (primary)
- Steam Query: UDP **2303** (game port + 1)
- BattlEye: UDP **2344** (game port + 42)
- VON: UDP **2304** (game port + 2)"""

    def generate_arma2_startup(self, game_name, slug):
        """Arma 2/Operation Arrowhead specific startup parameters"""
        return f"""**Default command line**
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
- BattlEye: UDP **2344** (if enabled)"""

    def generate_config_files(self, game_name, slug, engine):
        """Generate config files and locations section"""
        if engine == 'Source' or engine == 'Source 2':
            return self.generate_source_config(game_name, slug)
        elif engine == 'GoldSrc':
            return self.generate_goldsrc_config(game_name, slug)
        elif 'Unreal' in engine:
            return self.generate_unreal_config(game_name, slug)
        elif engine == 'Unity':
            return self.generate_unity_config(game_name, slug)
        elif 'Real Virtuality' in engine or engine == 'Enfusion':
            return self.generate_arma_config(game_name, slug)
        elif engine == 'Java' and 'minecraft' in slug:
            return self.generate_minecraft_config(game_name, slug)
        else:
            return self.generate_generic_config(game_name, slug)
    
    def generate_source_config(self, game_name, slug):
        """Source engine config files"""
        game_dir = self.get_game_dir_for_source(slug)
        return f"""**Windows:**
- `steamapps/common/{game_name}/cfg/server.cfg` — Main server configuration
- `steamapps/common/{game_name}/cfg/autoexec.cfg` — Auto-executed commands
- `steamapps/common/{game_name}/mapcycle.txt` — Map rotation list
- `steamapps/common/{game_name}/motd.txt` — Message of the day
- `steamapps/common/{game_name}/banned_user.cfg` — Banned users
- `steamapps/common/{game_name}/banned_ip.cfg` — Banned IP addresses
- `steamapps/common/{game_name}/logs/` — Server logs directory

**Linux:**
- `~/.steam/steamapps/common/{game_name}/cfg/server.cfg` — Main server configuration
- `~/.steam/steamapps/common/{game_name}/cfg/autoexec.cfg` — Auto-executed commands
- `~/.steam/steamapps/common/{game_name}/mapcycle.txt` — Map rotation list
- `~/.steam/steamapps/common/{game_name}/motd.txt` — Message of the day
- `~/.steam/steamapps/common/{game_name}/banned_user.cfg` — Banned users
- `~/.steam/steamapps/common/{game_name}/banned_ip.cfg` — Banned IP addresses
- `~/.steam/steamapps/common/{game_name}/logs/` — Server logs directory

**Key Configuration Files:**
- **server.cfg**: Core server settings (rates, game rules, admin settings)
- **autoexec.cfg**: Commands executed on server start
- **mapcycle.txt**: Map rotation configuration
- **motd.txt**: Welcome message displayed to connecting players"""

    def generate_minecraft_config(self, game_name, slug):
        """Minecraft config files"""
        return f"""**Windows:**
- `server.properties` — Main server configuration
- `eula.txt` — End User License Agreement acceptance
- `server.jar` — Server executable
- `world/` — World save directory
- `logs/` — Server logs
- `plugins/` — Plugin directory (Bukkit/Spigot/Paper)
- `mods/` — Mods directory (Forge/Fabric)
- `config/` — Configuration files for mods/plugins
- `banned-players.json` — Banned players list
- `banned-ips.json` — Banned IP addresses
- `ops.json` — Server operators list
- `whitelist.json` — Whitelist (if enabled)

**Linux:**
- `~/minecraft/server.properties` — Main server configuration
- `~/minecraft/eula.txt` — EULA acceptance
- `~/minecraft/server.jar` — Server executable
- `~/minecraft/world/` — World save directory
- `~/minecraft/logs/` — Server logs
- `~/minecraft/plugins/` — Plugin directory
- `~/minecraft/mods/` — Mods directory
- `~/minecraft/config/` — Configuration files

**Key Configuration Files:**
- **server.properties**: Core server settings (port, world name, game mode, difficulty)
- **bukkit.yml/spigot.yml/paper.yml**: Server platform-specific settings
- **plugin configs**: Individual plugin configuration files in plugins/ directory"""

    def generate_goldsrc_config(self, game_name, slug):
        """GoldSrc engine config files"""
        return f"""**Windows:**
- `game_dir/server.cfg` — Main server configuration
- `game_dir/mapcycle.txt` — Map rotation list
- `game_dir/motd.txt` — Message of the day
- `game_dir/banned.cfg` — Banned users list
- `game_dir/listip.cfg` — Banned IP addresses
- `game_dir/logs/` — Server logs directory

**Linux:**
- `~/{slug}/server.cfg` — Main server configuration
- `~/{slug}/mapcycle.txt` — Map rotation list
- `~/{slug}/motd.txt` — Message of the day
- `~/{slug}/banned.cfg` — Banned users list
- `~/{slug}/logs/` — Server logs directory

**Key Files:**
- **server.cfg**: Core server settings (rates, friendly fire, admin commands)
- **mapcycle.txt**: Map rotation configuration
- **motd.txt**: Welcome message for connecting players"""

    def generate_unreal_config(self, game_name, slug):
        """Unreal Engine config files"""
        return f"""**Windows:**
- `WindowsServer/{game_name}/Saved/Config/WindowsServer/` — Configuration directory
- `WindowsServer/{game_name}/Saved/Logs/` — Log files
- `WindowsServer/{game_name}/Saved/SaveGames/` — Save files

**Linux:**
- `/home/{slug}/Saved/Config/LinuxServer/` — Configuration directory
- `/home/{slug}/Saved/Logs/` — Log files
- `/home/{slug}/Saved/SaveGames/` — Save files

**Key Files:**
- **GameUserSettings.ini**: Main server configuration
- **Game.ini**: Advanced game settings
- **Engine.ini**: Engine-specific settings"""

    def generate_unity_config(self, game_name, slug):
        """Unity Engine config files"""
        return f"""**Windows:**
- `Data/` — Game data directory
- `Logs/` — Log files directory
- `ServerConfig/` — Configuration files (varies by game)

**Linux:**
- `~/{slug}/Data/` — Game data directory
- `~/{slug}/Logs/` — Log files directory
- `~/{slug}/ServerConfig/` — Configuration files

**Key Files:**
- Configuration file names and locations vary significantly between Unity games
- Common patterns: server.cfg, config.json, settings.xml
- Check game-specific documentation for exact file locations"""

    def generate_arma_config(self, game_name, slug):
        """Arma/Real Virtuality engine config files"""
        return f"""**Windows:**
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
- **Profile logs**: Server performance and error logs"""

    def generate_workshop_support(self, game_name, slug, engine):
        """Generate Steam Workshop section"""
        workshop_games = [
            'counter-strike-2', 'counter-strike-global-offensive', 'team-fortress-2',
            'garry-s-mod', 'left-4-dead-2', 'ark-survival-evolved', 'ark-survival-ascended',
            'arma-3', 'rust', 'insurgency-sandstorm', 'squad', 'killing-floor-2',
            'space-engineers', 'unturned', 'terraria'
        ]
        
        if any(game in slug for game in workshop_games):
            if 'counter-strike' in slug or 'team-fortress' in slug:
                return self.generate_source_workshop()
            elif 'ark' in slug:
                return self.generate_ark_workshop()
            elif 'arma-3' in slug:
                return self.generate_arma3_workshop()
            elif 'garry' in slug:
                return self.generate_gmod_workshop()
            else:
                return self.generate_generic_workshop()
        else:
            return "Not supported by this game."
    
    def generate_source_workshop(self):
        """Source engine Workshop support"""
        return f"""**Collection Mounting:**
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
- Force update with `workshop_download_item <app_id> <item_id>` console command"""

    def generate_ark_workshop(self):
        """ARK Workshop support"""
        return f"""**Mod Installation:**
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
- Linux: `~/.steam/steamapps/workshop/content/346110/`"""

    def generate_arma3_workshop(self):
        """Arma 3 Workshop support"""
        return f"""**Mod Installation:**
1. Subscribe to mods via Steam Workshop
2. Add mod IDs to server startup: `-mod="@workshop_id_1;@workshop_id_2"`
3. For server-only mods use: `-serverMod="@mod_folder"`

**Workshop Collection:**
- Create Steam collection with all server mods
- Use Arma 3 Server tools or community launchers for bulk downloading
- Verify all clients have same mod versions

**Mod Loading:**
- Order matters for some mods (CBA_A3 should load first)
- Use proper folder structure: `@mod_name/addons/*.pbo`
- Include signature files (.bisign) for key verification

**Cache Location:**
- Windows: `steamapps/workshop/content/107410/`
- Linux: `~/.steam/steamapps/workshop/content/107410/`"""

    def generate_gmod_workshop(self):
        """Garry's Mod Workshop support"""
        return f"""**Collection Mounting:**
1. Create Steam Workshop collection with server content
2. Add to startup: `+host_workshop_collection <collection_id>`
3. Set Steam Web API key: `+sv_setsteamaccount <token>`

**Resource Management:**
- Use `resource.AddWorkshopFile(id)` in Lua for required downloads
- Large workshop collections may cause long loading times
- Consider FastDL for faster content delivery

**Auto-Download:**
- Players automatically download workshop content
- Monitor download progress in server console
- Some content may require manual subscription by players

**Cache Location:**
- Windows: `steamapps/workshop/content/4000/`
- Linux: `~/.steam/steamapps/workshop/content/4000/`"""

    def generate_generic_workshop(self):
        """Generic Workshop support"""
        return f"""**Workshop Integration:**
- Check Steam Workshop for community content
- Subscribe to collections via Steam client  
- Server automatically downloads subscribed content
- Configure workshop content loading in server configuration

**Content Management:**
- Verify all players have access to workshop content
- Monitor content updates and version compatibility
- Test workshop content before adding to production server"""

    def generate_common_mods(self, game_name, slug, engine):
        """Generate common mods section"""
        if 'counter-strike' in slug and '1.6' not in slug and 'source' not in slug and '2' not in slug:
            return self.generate_cs16_mods()
        elif 'counter-strike' in slug and 'source' in slug:
            return self.generate_css_mods()
        elif 'counter-strike-2' in slug or 'counter-strike-global-offensive' in slug:
            return self.generate_csgo_mods()
        elif 'garry' in slug:
            return self.generate_gmod_mods()
        elif 'minecraft' in slug:
            return self.generate_minecraft_mods()
        elif 'rust' in slug:
            return self.generate_rust_mods()
        elif 'arma-3' in slug:
            return self.generate_arma3_mods()
        elif 'dayz' in slug and 'mod' not in slug:
            return self.generate_dayz_mods()
        elif 'dayz' in slug and 'mod' in slug:
            return self.generate_dayz_mod_mods()
        else:
            return self.generate_generic_mods(game_name, slug)
    
    def generate_cs16_mods(self):
        """Counter-Strike 1.6 specific mods"""
        return f"""- **AMX Mod X**
  - **Purpose**: Complete admin and scripting framework for GoldSrc games.
  - **Install**: Download from amxmodx.org, extract to game directory, add `meta load addons/amxmodx/dlls/amxmodx_mm` to `addons/metamod/plugins.ini`.
  - **Configure**: Edit `addons/amxmodx/configs/amxx.cfg` for basic settings, `configs/users.ini` for admin users.

- **Metamod**
  - **Purpose**: Plugin loading framework required by most mods.
  - **Install**: Extract metamod.dll to `addons/metamod/dlls/`, add `gamedll_linux "addons/metamod/dlls/metamod.so"` to liblist.gam.
  - **Configure**: Plugins list in `addons/metamod/plugins.ini`.

- **StatsMe**
  - **Purpose**: Player statistics tracking and ranking system.
  - **Install**: Requires AMX Mod X, install plugin files to `addons/amxmodx/plugins/`.
  - **Configure**: Database settings in plugin configuration files.

- **PodBot MM**
  - **Purpose**: AI bots for offline practice or filling servers.
  - **Install**: Extract to game directory, requires Metamod.
  - **Configure**: Bot skills and behavior in `podbot/podbot.cfg`."""

    def generate_minecraft_mods(self):
        """Minecraft specific mods/plugins"""
        return f"""- **EssentialsX**
  - **Purpose**: Core commands, economy, permissions for Bukkit/Spigot/Paper servers.
  - **Install**: Download from GitHub releases, place JAR in `plugins/` directory.
  - **Configure**: Edit `plugins/Essentials/config.yml` for basic settings, `userdata/` for player data.

- **WorldEdit**
  - **Purpose**: In-game world editing and building tools.
  - **Install**: Place WorldEdit JAR in `plugins/` directory.
  - **Configure**: Permissions in `plugins/WorldEdit/config.yml`, user permissions via permission plugin.

- **Vault**
  - **Purpose**: Economy and permissions API for other plugins.
  - **Install**: Download Vault JAR to `plugins/` directory.
  - **Configure**: No direct configuration - provides API for other plugins.

- **LuckPerms**
  - **Purpose**: Advanced permissions management system.
  - **Install**: Download from GitHub, place in `plugins/` directory.
  - **Configure**: Database connection in `plugins/LuckPerms/config.yml`, manage permissions via commands or web editor.

- **Dynmap**
  - **Purpose**: Real-time web-based map of server world.
  - **Install**: Download Dynmap JAR to `plugins/` directory.
  - **Configure**: Web server settings in `plugins/dynmap/configuration.txt`, map rendering options.

- **ProtocolLib**
  - **Purpose**: Packet manipulation library for advanced plugins.
  - **Install**: Required dependency for many plugins, place JAR in `plugins/`.
  - **Configure**: No direct configuration - provides API for packet handling."""

    def generate_css_mods(self):
        """Counter-Strike Source specific mods"""
        return f"""- **SourceMod**
  - **Purpose**: Admin and scripting framework for Source engine games.
  - **Install**: Download from sourcemod.net, extract to game directory, requires MetaMod:Source.
  - **Configure**: Edit `addons/sourcemod/configs/admins_simple.ini` for admin users.

- **MetaMod:Source**
  - **Purpose**: Plugin loading framework for Source engine.
  - **Install**: Extract to game directory, add to gameinfo.txt.
  - **Configure**: Plugin loading handled automatically.

- **Mani Admin Plugin**
  - **Purpose**: Alternative admin framework with extensive features.
  - **Install**: Extract to game directory, configure via mani_server.cfg.
  - **Configure**: Admin settings in `cfg/mani_server.cfg`."""

    def generate_csgo_mods(self):
        """CS:GO/CS2 specific mods"""
        return f"""- **SourceMod (CS:GO Legacy)**
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
  - **Configure**: Practice commands and features configuration."""

    def generate_gmod_mods(self):
        """Garry's Mod specific mods"""
        return f"""- **DarkRP**
  - **Purpose**: Popular roleplay gamemode framework.
  - **Install**: Download from workshop or GitHub, extract to gamemodes directory.
  - **Configure**: Edit `gamemodes/darkrp/gamemode/config.lua` for server settings.

- **ULX/ULib**
  - **Purpose**: Admin framework with extensive user management.
  - **Install**: Download both ULX and ULib, extract to addons directory.
  - **Configure**: Admin groups and permissions in `data/ulx/` directory.

- **Wiremod**
  - **Purpose**: Advanced contraption building with electronic components.
  - **Install**: Subscribe via Workshop or manual installation to addons.
  - **Configure**: No specific configuration required, workshop auto-download.

- **PAC3**
  - **Purpose**: Player appearance customization system.
  - **Install**: Workshop subscription, auto-downloads to clients.
  - **Configure**: Server settings in `cfg/pac.cfg` if needed."""

    def generate_rust_mods(self):
        """Rust specific mods"""
        return f"""- **Oxide/uMod**
  - **Purpose**: Modding framework for Rust servers with extensive plugin ecosystem.
  - **Install**: Download from umod.org, extract to server directory.
  - **Configure**: Plugin configuration in `oxide/config/` directory.

- **AdminHammer**
  - **Purpose**: Advanced admin tools and server management.
  - **Install**: Install via Oxide plugin manager or manual download.
  - **Configure**: Admin permissions in `oxide/data/AdminHammer.json`.

- **Economics**
  - **Purpose**: Server economy system with currency and rewards.
  - **Install**: Download plugin, place in `oxide/plugins/`.
  - **Configure**: Economy settings in `oxide/config/Economics.json`.

- **Kits**
  - **Purpose**: Predefined item kits for players (starter kits, VIP kits).
  - **Install**: Install via Oxide plugin system.
  - **Configure**: Kit definitions in `oxide/config/Kits.json`."""

    def generate_arma3_mods(self):
        """Arma 3 specific mods"""
        return f"""- **ACE3**
  - **Purpose**: Advanced Combat Environment - realistic medical, ballistics, and logistics.
  - **Install**: Subscribe via Workshop, add `@ace` to startup mods.
  - **Configure**: ACE settings via in-game addon options or mission parameters.

- **CBA_A3**
  - **Purpose**: Community Base Addons - framework required by most Arma 3 mods.
  - **Install**: Subscribe via Workshop, ensure loads before other mods.
  - **Configure**: No direct configuration - provides API for other mods.

- **TFAR (Task Force Radio)**
  - **Purpose**: Realistic radio communication system.
  - **Install**: Workshop subscription, requires TeamSpeak 3 plugin for clients.
  - **Configure**: Radio frequencies and settings in mission files.

- **RHS: Armed Forces**
  - **Purpose**: High-quality modern military units and equipment.
  - **Install**: Subscribe to RHS collections via Workshop.
  - **Configure**: Unit availability configured in mission editor."""

    def generate_dayz_mods(self):
        """DayZ Standalone specific mods"""
        return f"""- **CF (Community Framework)**
  - **Purpose**: Modding framework required by many DayZ mods.
  - **Install**: Subscribe via Workshop, ensure loads first in mod order.
  - **Configure**: No direct configuration - provides API framework.

- **BuildAnywhere**
  - **Purpose**: Allows base building in normally restricted areas.
  - **Install**: Workshop subscription, add to server mods.
  - **Configure**: Settings in mod configuration files.

- **Trader**
  - **Purpose**: NPC traders and economy system.
  - **Install**: Download from modding sites, requires server restart.
  - **Configure**: Trader locations and items in mod config files.

- **DayZ Editor Loader**
  - **Purpose**: Custom spawns, buildings, and map modifications.
  - **Install**: Workshop or manual installation.
  - **Configure**: Map edits and spawn configurations in JSON files."""

    def generate_dayz_mod_mods(self):
        """DayZ Mod (Arma 2 OA) specific mods"""
        return f"""- **DayZ Epoch**
  - **Purpose**: Enhanced DayZ experience with base building and vehicles.
  - **Install**: Download server and client files, place in `@DayZ_Epoch` folder.
  - **Configure**: Database setup and server configuration in `@hive` folder.

- **DayZ Overwatch**
  - **Purpose**: Military-focused DayZ variant with additional weapons.
  - **Install**: Download mod files, combine with Epoch for Overpoch.
  - **Configure**: Mission file configuration for weapon spawns.

- **infiSTAR**
  - **Purpose**: Advanced anti-hack and admin tools.
  - **Install**: Purchase and download, server-side installation only.
  - **Configure**: Admin IDs and settings in infiSTAR configuration files."""

    def generate_database_section(self, game_name, slug, engine):
        """Generate database section"""
        db_games = {
            'dayz-mod-for-arma2co': 'MySQL',
            'minecraft': 'SQLite/MySQL',
            'rust': 'SQLite',
            'garry-s-mod': 'SQLite/MySQL',
            'ark-survival-evolved': 'SQLite',
            'ark-survival-ascended': 'SQLite'
        }
        
        if slug in db_games or any(keyword in slug for keyword in ['dayz', 'minecraft', 'rust', 'garry']):
            if 'dayz' in slug and 'mod' in slug:
                return self.generate_dayz_mod_database()
            elif 'minecraft' in slug:
                return self.generate_minecraft_database()
            elif 'rust' in slug:
                return self.generate_rust_database()
            else:
                return self.generate_generic_database(db_games.get(slug, 'SQLite'))
        else:
            return "Not applicable - this game does not use a database for core functionality."
    
    def generate_dayz_mod_database(self):
        """DayZ Mod specific database configuration"""
        return f"""**Engine**: MySQL (required for character persistence)

**Configuration File**: `@hive/HiveExt.ini`
```ini
[Database]
Type = mysql
Host = localhost
Port = 3306
Database = dayz_epoch
Username = dayz_user
Password = your_password_here

[Characters]
;Enables persistence
LoadCharacter = true
SaveCharacter = true
```

**Database Setup**:
1. Install MySQL server
2. Create database: `CREATE DATABASE dayz_epoch;`
3. Import schema from mod documentation
4. Create user with appropriate permissions
5. Test connection before starting server

**Backup Strategy**:
- Daily automated backups of character and vehicle data
- Retention of 7 daily backups
- Test restore procedures regularly
- Monitor database size and performance"""

    def generate_minecraft_database(self):
        """Minecraft specific database configuration"""
        return f"""**Engine**: SQLite (default) or MySQL (for large servers)

**SQLite Configuration** (No setup required):
- Database files stored in world directory
- Player data in `playerdata/` folder
- Plugin data varies by plugin (typically in `plugins/PluginName/`)

**MySQL Configuration** (Advanced):
- Configure in `bukkit.yml` or plugin-specific configs
- Example connection string: `jdbc:mysql://localhost:3306/minecraft`
- Required for multi-server networks and large-scale deployments

**Backup Strategy**:
- Regular world folder backups (includes SQLite databases)
- MySQL: Use `mysqldump` for database backups
- Plugin data backup varies by plugin requirements"""

    def generate_rust_database(self):
        """Rust specific database configuration"""
        return f"""**Engine**: SQLite (built-in) for basic data, JSON files for Oxide plugins

**Server Data Storage**:
- Player data: `server/my_server_identity/storage/` 
- World saves: `server/my_server_identity/saves/`
- Oxide data: `oxide/data/` directory

**Oxide Plugin Storage**:
- Most plugins use JSON files in `oxide/data/`
- Some plugins support SQLite or MySQL connections
- Configuration in individual plugin config files

**Backup Strategy**:
- Backup entire server identity folder for complete restoration
- Oxide data folder contains all plugin persistent data
- Monitor storage growth on high-population servers"""

    def generate_troubleshooting(self, game_name, slug, engine):
        """Generate game-specific troubleshooting section"""
        if 'counter-strike' in slug:
            return self.generate_cs_troubleshooting(slug)
        elif 'minecraft' in slug:
            return self.generate_minecraft_troubleshooting()
        elif 'rust' in slug:
            return self.generate_rust_troubleshooting()
        elif 'dayz' in slug:
            return self.generate_dayz_troubleshooting(slug)
        elif 'arma' in slug:
            return self.generate_arma_troubleshooting(slug)
        else:
            return self.generate_generic_troubleshooting(game_name, slug)
    
    def generate_cs_troubleshooting(self, slug):
        """Counter-Strike specific troubleshooting"""
        return f"""**"Server not appearing in browser"**
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
- **Fix**: Check internet connectivity, verify Steam services status, adjust timeout settings"""

    def generate_minecraft_troubleshooting(self):
        """Minecraft specific troubleshooting"""
        return f"""**"java.lang.OutOfMemoryError"**
- **Cause**: Insufficient heap memory allocation
- **Fix**: Increase `-Xmx` parameter (e.g., `-Xmx8G` for 8GB), monitor memory usage

**"Can't keep up! Skipping ticks"**
- **Cause**: Server overload, too many entities, or inefficient plugins
- **Fix**: Reduce entity limits, optimize plugins, increase server resources, use profiling tools

**"Connection timed out"**
- **Cause**: Firewall blocking port 25565 or server not responding
- **Fix**: Open TCP port 25565, verify server is running, check network connectivity

**"Plugin errors on startup"**
- **Cause**: Incompatible plugin versions or missing dependencies
- **Fix**: Update plugins to match server version, install required dependencies, check plugin logs

**"World corruption/rollback"**
- **Cause**: Improper server shutdown or storage issues
- **Fix**: Use proper shutdown commands, implement regular backups, check disk health

**"Low TPS (Ticks Per Second)"**
- **Cause**: Server lag from heavy operations or overloaded chunks
- **Fix**: Use performance monitoring plugins, limit chunk loading, optimize redstone contraptions"""

    def generate_rust_troubleshooting(self):
        """Rust specific troubleshooting"""
        return f"""**"Couldn't connect to server" / Connection timeout**
- **Cause**: Firewall blocking ports or server not responding
- **Fix**: Open UDP port 28015 and TCP port 28016, verify server is running and responsive

**"Failed to create directory" on startup**
- **Cause**: Insufficient permissions or disk space
- **Fix**: Run server with proper permissions, ensure adequate disk space, check path validity

**High memory usage / Out of memory crashes**
- **Cause**: Large world size, too many entities, or memory leaks in plugins
- **Fix**: Reduce world size, limit entity spawns, restart server regularly, monitor Oxide plugins

**"Disconnected: EAC Authentication Timeout"**
- **Cause**: Easy Anti-Cheat connectivity issues
- **Fix**: Verify internet connection, check EAC service status, ensure firewall allows EAC traffic

**Server lag / Low FPS**
- **Cause**: High player count, complex bases, or inefficient plugins
- **Fix**: Optimize server settings, limit building complexity, review Oxide plugin performance

**Workshop/Oxide plugins not loading**
- **Cause**: Plugin conflicts, outdated plugins, or configuration errors
- **Fix**: Check plugin compatibility, update plugins, review oxide logs for errors"""

    def generate_dayz_troubleshooting(self, slug):
        """DayZ troubleshooting (Standalone or Mod)"""
        if 'mod' in slug:
            return f"""**"Waiting for host" infinite loop**
- **Cause**: Database connection issues or incorrect mission file
- **Fix**: Verify MySQL connection in HiveExt.ini, check mission PBO integrity, review server RPT logs

**"No message received" / Player disconnect**
- **Cause**: Network timeout or BattlEye issues
- **Fix**: Optimize basic.cfg network settings, update BattlEye filters, check server performance

**Database connection failed**
- **Cause**: MySQL server down or incorrect credentials
- **Fix**: Verify MySQL service running, check HiveExt.ini credentials, test database connectivity

**Script restriction kicks**
- **Cause**: BattlEye script filters blocking legitimate commands
- **Fix**: Update BattlEye filters from mod documentation, merge custom filter exceptions

**Character reset / Database issues**
- **Cause**: Database corruption or incorrect instance configuration
- **Fix**: Verify database integrity, check Instance ID matches between DB and config"""
        else:
            return f"""**"Authentication timeout" on connect**
- **Cause**: Steam authentication server issues or network problems
- **Fix**: Check Steam server status, verify internet connectivity, restart Steam services

**"Session lost" / Frequent disconnects**
- **Cause**: Network instability or server performance issues
- **Fix**: Optimize server performance, check network quality, adjust timeout settings

**Mods not loading / Version mismatch**
- **Cause**: Client-server mod version differences or missing dependencies
- **Fix**: Verify all mods updated, check mod dependencies, ensure Workshop sync

**Character stuck / Can't move**
- **Cause**: Database synchronization issues or server lag
- **Fix**: Character reset via admin tools, server restart, check database performance

**BattlEye initialization failed**
- **Cause**: Missing BattlEye files or configuration issues
- **Fix**: Verify BattlEye installation, check file permissions, update BattlEye client"""

    def generate_arma_troubleshooting(self, slug):
        """Arma series troubleshooting"""
        return f"""**"Bad module info" / Addon errors**
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
- **Fix**: Optimize mission complexity, reduce AI count, upgrade server hardware, tune basic.cfg"""

    def generate_generic_startup(self, game_name, slug):
        """Generic startup parameters for unknown games"""
        return f"""**Default command line**
```bash
./{slug}_server -port 27015 -maxplayers 16 -config server.cfg
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
- Admin/RCON: TCP **varies by game**"""

    def generate_generic_config(self, game_name, slug):
        """Generic config files section"""
        return f"""**Windows:**
- `{slug}_server.cfg` — Main server configuration
- `config/` — Configuration directory
- `logs/` — Log files directory
- `data/` — Server data and saves

**Linux:**
- `~/{slug}/server.cfg` — Main server configuration  
- `~/{slug}/config/` — Configuration directory
- `~/{slug}/logs/` — Log files directory
- `~/{slug}/data/` — Server data and saves

**Key Files:**
- **server.cfg**: Core server settings and game rules
- **admins.cfg**: Administrator configuration (if applicable)
- **banned.cfg**: Banned players list (if applicable)"""

    def generate_generic_mods(self, game_name, slug):
        """Generic mods section for unknown games"""
        return f"""**Admin/Management Mods**
- Check official mod repositories or community sites for {game_name}
- Look for server administration, anti-cheat, and quality-of-life mods
- Install according to game's modding framework (if available)

**Popular Community Mods**
- Search Steam Workshop (if supported) for highly-rated server mods
- Check game's official forums and community sites for recommended mods
- Verify mod compatibility with current server version

**Installation Notes**
- Follow each mod's specific installation instructions
- Some games require mod loading frameworks or special startup parameters
- Test mods individually before combining multiple mods"""

    def generate_generic_database(self, engine_type):
        """Generic database section"""
        return f"""**Engine**: {engine_type}

**Configuration**:
- Database settings typically in main server configuration file
- Connection parameters: host, port, database name, credentials
- Enable persistence features in server configuration

**Setup**:
1. Install database engine if required
2. Create database and user with appropriate permissions
3. Configure connection settings in server config
4. Test connection before starting server
5. Set up automated backups"""

    def generate_generic_troubleshooting(self, game_name, slug):
        """Generic troubleshooting section"""
        return f"""**Server not starting**
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
- **Fix**: Test mods individually, update to compatible versions, check for known conflicts"""

    def get_game_dir_for_source(self, slug):
        """Get game directory for Source engine games"""
        mapping = {
            'counter-strike-source': 'cstrike',
            'counter-strike-global-offensive': 'csgo',
            'team-fortress-2': 'tf',
            'left-4-dead-2': 'left4dead2',
            'garry-s-mod': 'garrysmod',
            'day-of-defeat-source': 'dod',
            'half-life-2-deathmatch': 'hl2mp'
        }
        return mapping.get(slug, slug.replace('-', ''))

    # Additional helper methods for specific games...
    def generate_generic_workshop(self):
        return """**Workshop Integration:**
- Check Steam Workshop for community content
- Subscribe to collections via Steam client
- Server automatically downloads subscribed content
- Configure workshop content loading in server configuration"""

    def generate_administration_section(self, game_name, slug, engine):
        """Generate administration and scripting section"""
        return f"""**Remote Administration:**
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
- Uptime tracking and availability reporting"""

    def generate_complete_guide(self, game_name):
        """Generate complete guide for a game"""
        slug = self.slugify(game_name)
        engine = self.get_engine_for_game(slug)
        
        # Handle special cases for DayZ
        if game_name == "DayZ Mod for Arma2CO":
            game_name = "DayZ Mod (Arma 2 OA)"
            slug = "dayz-mod-arma-2-oa"
        
        startup_params = self.generate_startup_parameters(game_name, slug, engine)
        config_files = self.generate_config_files(game_name, slug, engine)
        workshop_section = self.generate_workshop_support(game_name, slug, engine)
        common_mods = self.generate_common_mods(game_name, slug, engine)
        database_section = self.generate_database_section(game_name, slug, engine)
        admin_section = self.generate_administration_section(game_name, slug, engine)
        troubleshooting = self.generate_troubleshooting(game_name, slug, engine)
        
        guide_content = f"""# {game_name} — Complete Dedicated Server Guide

## Startup Parameters
{startup_params}

## Config Files & Locations
{config_files}

## Steam Workshop
{workshop_section}

## Common Mods (curated)
{common_mods}

## Database
{database_section}

## Administration & Scripting
{admin_section}

## Troubleshooting (game-specific)
{troubleshooting}
"""
        return guide_content

    def generate_all_guides(self):
        """Generate all game guides from CSV"""
        games = self.read_games_from_csv()
        
        # Ensure docs/games directory exists
        self.docs_dir.mkdir(parents=True, exist_ok=True)
        
        print(f"Generating guides for {len(games)} games...")
        
        generated_count = 0
        for i, game_name in enumerate(games, 1):
            slug = self.slugify(game_name)
            
            # Handle special DayZ case - ensure both guides exist
            if game_name == "DayZ Mod for Arma2CO":
                # Generate both DayZ standalone and DayZ Mod guides
                if not (self.docs_dir / "dayz.md").exists():
                    dayz_guide = self.generate_complete_guide("DayZ")
                    with open(self.docs_dir / "dayz.md", 'w', encoding='utf-8') as f:
                        f.write(dayz_guide)
                    print(f"Generated DayZ (Standalone) guide")
                    generated_count += 1
                
                # Generate DayZ Mod guide
                guide_content = self.generate_complete_guide(game_name)
                output_path = self.docs_dir / f"{slug}.md"
            else:
                guide_content = self.generate_complete_guide(game_name)
                output_path = self.docs_dir / f"{slug}.md"
            
            # Write the guide file
            with open(output_path, 'w', encoding='utf-8') as f:
                f.write(guide_content)
            
            print(f"Generated {i:3d}/{len(games)}: {game_name} -> {output_path.name}")
            generated_count += 1
            
            # Commit in batches of 20
            if generated_count % 20 == 0:
                yield generated_count, len(games)
        
        yield generated_count, len(games)
        print(f"Successfully generated {generated_count} game server guides!")

def main():
    generator = GameGuideGenerator()
    batch_count = 0
    
    for generated, total in generator.generate_all_guides():
        batch_count += 1
        print(f"Completed batch {batch_count}: {generated}/{total} guides generated")
        if generated % 20 == 0 or generated == total:
            break  # Return control to main script for progress reporting

if __name__ == "__main__":
    main()