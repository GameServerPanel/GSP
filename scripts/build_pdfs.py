#!/usr/bin/env python3
"""
Game Guide PDF Generator

Reads all_hostable_games_union.csv and generates comprehensive self-hosting guides
for each game in both Markdown and PDF formats.

Features:
- No provider mentions (hosting-agnostic)
- Exhaustive startup parameters and troubleshooting
- Complete port mappings and configuration details
- Steam Workshop integration where applicable
- Professional PDF output with TOC and styling

Output Structure:
- out/md/<sanitized-game>.md - Markdown source
- out/pdfs/<sanitized-game>.pdf - PDF output
"""

import os
import sys
import csv
import re
import subprocess
import json
from pathlib import Path
from datetime import datetime
import pandas as pd

class GameGuideGenerator:
    def __init__(self, csv_path="all_hostable_games_union.csv", 
                 output_md="out/md", output_pdf="out/pdfs", template_path="templates/game_guide.md"):
        self.csv_path = Path(csv_path)
        self.output_md = Path(output_md)
        self.output_pdf = Path(output_pdf)
        self.template_path = Path(template_path)
        
        # Ensure output directories exist
        self.output_md.mkdir(parents=True, exist_ok=True)
        self.output_pdf.mkdir(parents=True, exist_ok=True)
        
        # Forbidden provider mentions (must be filtered out)
        self.forbidden_terms = [
            'OGP', 'LinuxGSM', 'Nitrado', 'GameServers.com', 'G-Portal',
            'OpenGamePanel'
        ]
        
        # Common game engines for enhanced data
        self.engine_mapping = {
            'Counter-Strike': 'Source Engine', 'Counter-Strike 2': 'Source 2',
            'Counter-Strike: Source': 'Source Engine', 'Counter-Strike: Global Offensive': 'Source Engine',
            'Team Fortress 2': 'Source Engine', 'Left 4 Dead 2': 'Source Engine',
            'Garry\'s Mod': 'Source Engine', 'Half-Life 2: Deathmatch': 'Source Engine',
            'ARK: Survival Evolved': 'Unreal Engine 4', 'ARK: Survival Ascended': 'Unreal Engine 5',
            'Squad': 'Unreal Engine 4', 'Insurgency: Sandstorm': 'Unreal Engine 4',
            'Mordhau': 'Unreal Engine 4', 'Killing Floor 2': 'Unreal Engine 3',
            'Rust': 'Unity', 'Valheim': 'Unity', '7 Days to Die': 'Unity',
            'Unturned': 'Unity', 'Escape from Tarkov': 'Unity',
            'ARMA 3': 'Real Virtuality 4', 'ARMA 2': 'Real Virtuality 3',
            'DayZ': 'Enfusion', 'DayZ Standalone': 'Enfusion',
            'Minecraft': 'Java', 'Minecraft: Java Edition': 'Java',
            'Terraria': 'XNA/MonoGame', 'Starbound': 'Custom C++',
            'Factorio': 'Custom C++', 'Space Engineers': 'VRAGE 2.0',
            'Project Zomboid': 'Java', 'Don\'t Starve Together': 'Custom',
            'Palworld': 'Unreal Engine 5', 'Satisfactory': 'Unreal Engine 4',
            'Conan Exiles': 'Unreal Engine 4', 'The Forest': 'Unity',
            'Sons of the Forest': 'Unity', 'Green Hell': 'Unity',
            'V Rising': 'Unity', 'Core Keeper': 'Unity',
            'Enshrouded': 'Custom', 'Palworld': 'Unreal Engine 5'
        }
        
        # Common Steam App IDs
        self.appid_mapping = {
            'Counter-Strike 2': '730', 'Counter-Strike: Global Offensive': '730',
            'Team Fortress 2': '440', 'Left 4 Dead 2': '550',
            'Garry\'s Mod': '4000', 'Rust': '258550',
            'ARK: Survival Evolved': '376030', 'Squad': '393380', 
            'Valheim': '892970', '7 Days to Die': '294420',
            'Unturned': '304930', 'ARMA 3': '107410',
            'DayZ': '221100', 'Terraria': '105600',
            'Project Zomboid': '108600', 'Don\'t Starve Together': '322330',
            'Space Engineers': '244850', 'Conan Exiles': '440900',
            'The Forest': '242760', 'Killing Floor 2': '232090',
            'Insurgency: Sandstorm': '581320', 'Mordhau': '629760'
        }
        
    def load_template(self):
        """Load the Markdown template"""
        try:
            with open(self.template_path, 'r', encoding='utf-8') as f:
                return f.read()
        except FileNotFoundError:
            print(f"Error: Template file not found: {self.template_path}")
            sys.exit(1)
            
    def read_csv_games(self):
        """Read games from CSV file"""
        if not self.csv_path.exists():
            print(f"Error: CSV file not found: {self.csv_path}")
            sys.exit(1)
            
        games = []
        try:
            df = pd.read_csv(self.csv_path)
            
            # Find the game name column (could be 'Game', 'Title', etc.)
            game_column = None
            for col in df.columns:
                if col.lower() in ['game', 'title', 'name']:
                    game_column = col
                    break
                    
            if not game_column:
                # Use first column as fallback
                game_column = df.columns[0]
                print(f"Warning: Using first column '{game_column}' as game name column")
                
            # Extract unique games (remove duplicates and empty entries)
            for _, row in df.iterrows():
                game_name = row[game_column]
                if pd.notna(game_name) and game_name.strip():
                    # Remove numbering prefix if present (e.g., "1.Game" -> "Game")
                    game_name = re.sub(r'^\d+\.', '', str(game_name)).strip()
                    if game_name and game_name not in [g['name'] for g in games]:
                        games.append({'name': game_name})
                        
            print(f"Loaded {len(games)} unique games from {self.csv_path}")
            return sorted(games, key=lambda x: x['name'])
            
        except Exception as e:
            print(f"Error reading CSV file: {e}")
            sys.exit(1)
            
    def sanitize_filename(self, name):
        """Create a safe filename from game name"""
        # Remove special characters, replace spaces with hyphens
        sanitized = re.sub(r'[^\w\s-]', '', name)
        sanitized = re.sub(r'[-\s]+', '-', sanitized).strip('-').lower()
        return sanitized
        
    def detect_engine(self, game_name):
        """Detect game engine based on name patterns"""
        # Direct mapping first
        if game_name in self.engine_mapping:
            return self.engine_mapping[game_name]
            
        # Pattern-based detection
        name_lower = game_name.lower()
        
        if any(x in name_lower for x in ['source', 'half-life', 'counter-strike']):
            return 'Source Engine'
        elif any(x in name_lower for x in ['unreal', 'squad', 'insurgency']):
            return 'Unreal Engine 4'
        elif any(x in name_lower for x in ['unity', 'rust', 'cities']):
            return 'Unity'
        elif 'arma' in name_lower:
            return 'Real Virtuality 4'
        elif any(x in name_lower for x in ['minecraft', 'java']):
            return 'Java'
        elif any(x in name_lower for x in ['quake', 'doom', 'id tech']):
            return 'id Tech'
        else:
            return 'Custom/Unknown'
            
    def get_app_id(self, game_name):
        """Get Steam App ID for the game"""
        return self.appid_mapping.get(game_name, 'N/A')
        
    def supports_workshop(self, game_name):
        """Determine if game supports Steam Workshop"""
        # Games known to support Workshop
        workshop_games = [
            'Counter-Strike 2', 'Counter-Strike: Global Offensive', 'Team Fortress 2',
            'Garry\'s Mod', 'ARK: Survival Evolved', 'Rust', 'Squad',
            'DayZ', 'ARMA 3', 'Killing Floor 2', 'Insurgency: Sandstorm',
            'Cities: Skylines', 'Civilization VI', 'Europa Universalis IV',
            'Stellaris', 'Hearts of Iron IV', 'Prison Architect'
        ]
        
        return game_name in workshop_games
        
    def generate_port_table(self, game_name, engine):
        """Generate port mapping table based on game type"""
        ports = []
        
        # Common patterns based on engine/game type
        if 'Source' in engine:
            ports = [
                {'feature': 'Game Port', 'port': '27015', 'protocol': 'UDP', 'relation': 'Primary', 'notes': 'Main game traffic'},
                {'feature': 'RCON', 'port': '27015', 'protocol': 'TCP', 'relation': 'Same as game', 'notes': 'Remote administration'},
                {'feature': 'SourceTV', 'port': '27020', 'protocol': 'UDP', 'relation': 'Game + 5', 'notes': 'Spectator broadcasting'},
                {'feature': 'Steam Query', 'port': '27016', 'protocol': 'UDP', 'relation': 'Game + 1', 'notes': 'Server browser queries'},
                {'feature': 'Steam Master', 'port': '27011', 'protocol': 'UDP', 'relation': 'Fixed', 'notes': 'Steam master server communication'}
            ]
        elif 'Unity' in engine:
            ports = [
                {'feature': 'Game Port', 'port': '7777', 'protocol': 'UDP', 'relation': 'Primary', 'notes': 'Main game traffic'},
                {'feature': 'Query Port', 'port': '7778', 'protocol': 'UDP', 'relation': 'Game + 1', 'notes': 'Server status queries'},
                {'feature': 'Steam Port', 'port': '8766', 'protocol': 'UDP', 'relation': 'Fixed', 'notes': 'Steam integration'},
                {'feature': 'RCON', 'port': '8080', 'protocol': 'TCP', 'relation': 'Configurable', 'notes': 'Web-based admin interface'}
            ]
        elif 'Unreal' in engine:
            ports = [
                {'feature': 'Game Port', 'port': '7777', 'protocol': 'UDP', 'relation': 'Primary', 'notes': 'Main game traffic'},
                {'feature': 'Query Port', 'port': '27015', 'protocol': 'UDP', 'relation': 'Configurable', 'notes': 'Server browser queries'},
                {'feature': 'RCON', 'port': '27020', 'protocol': 'TCP', 'relation': 'Configurable', 'notes': 'Remote console access'},
                {'feature': 'Beacon Port', 'port': '15000', 'protocol': 'UDP', 'relation': 'Fixed', 'notes': 'LAN discovery'}
            ]
        else:
            # Generic ports for unknown engines
            ports = [
                {'feature': 'Game Port', 'port': '25565', 'protocol': 'UDP', 'relation': 'Primary', 'notes': 'Main game traffic'},
                {'feature': 'Query Port', 'port': '25566', 'protocol': 'UDP', 'relation': 'Game + 1', 'notes': 'Server status queries'},
                {'feature': 'RCON', 'port': '25575', 'protocol': 'TCP', 'relation': 'Game + 10', 'notes': 'Remote administration'}
            ]
            
        # Format as table
        table_rows = []
        for port in ports:
            table_rows.append(f"| {port['feature']} | {port['port']} | {port['protocol']} | {port['relation']} | {port['notes']} |")
            
        return '\n'.join(table_rows)
        
    def generate_startup_flags(self, game_name, engine):
        """Generate exhaustive startup parameters based on game engine"""
        flags = []
        
        if 'Source' in engine:
            flags = [
                ('+map', '', 'string', 'Starting map name', '+map de_dust2'),
                ('+maxplayers', '16', 'integer 1-64', 'Maximum player count', '+maxplayers 32'),
                ('+sv_password', '', 'string', 'Server password for private games', '+sv_password mypass123'),
                ('+hostname', '', 'string', 'Server name in browser', '+hostname "My Server"'),
                ('+rcon_password', '', 'string', 'RCON administration password', '+rcon_password adminpass'),
                ('+sv_setsteamaccount', '', 'string', 'Game Server Login Token', '+sv_setsteamaccount TOKEN'),
                ('-port', '27015', 'integer 1024-65535', 'Primary server port', '-port 27015'),
                ('-tickrate', '64', 'integer 33-128', 'Server update frequency', '-tickrate 128'),
                ('-threads', '0', 'integer 0-8', 'Thread count (0=auto)', '-threads 4'),
                ('+fps_max', '300', 'integer 33-1000', 'Maximum server FPS', '+fps_max 500'),
                ('-nohltv', '', 'flag', 'Disable SourceTV', '-nohltv'),
                ('-insecure', '', 'flag', 'Disable VAC (NOT recommended)', '-insecure'),
                ('-console', '', 'flag', 'Enable console output', '-console'),
                ('-condebug', '', 'flag', 'Log console to file', '-condebug'),
                ('+log', 'on', 'on/off', 'Enable server logging', '+log on'),
                ('+sv_lan', '0', 'integer 0-1', 'LAN only mode', '+sv_lan 0'),
                ('+sv_cheats', '0', 'integer 0-1', 'Enable cheat commands', '+sv_cheats 0'),
                ('+mp_autoteambalance', '1', 'integer 0-1', 'Auto-balance teams', '+mp_autoteambalance 1'),
                ('+mp_limitteams', '2', 'integer 0-30', 'Team size difference limit', '+mp_limitteams 1'),
                ('+sv_pure', '1', 'integer 0-2', 'File consistency checking', '+sv_pure 1')
            ]
        elif 'Unity' in engine or 'Rust' in game_name:
            flags = [
                ('+server.port', '28015', 'integer 1024-65535', 'Server port', '+server.port 28015'),
                ('+server.maxplayers', '100', 'integer 1-300', 'Maximum players', '+server.maxplayers 200'),
                ('+server.hostname', '', 'string', 'Server name', '+server.hostname "My Server"'),
                ('+server.description', '', 'string', 'Server description', '+server.description "Welcome!"'),
                ('+server.password', '', 'string', 'Server password', '+server.password secretpass'),
                ('+server.worldsize', '4000', 'integer 1000-8000', 'World map size', '+server.worldsize 3000'),
                ('+server.seed', '0', 'integer', 'World generation seed', '+server.seed 12345'),
                ('+server.saveinterval', '300', 'integer 60-3600', 'Auto-save interval (seconds)', '+server.saveinterval 600'),
                ('+rcon.port', '28016', 'integer 1024-65535', 'RCON port', '+rcon.port 28016'),
                ('+rcon.password', '', 'string', 'RCON password', '+rcon.password rconpass'),
                ('+rcon.web', '1', 'integer 0-1', 'Enable web RCON', '+rcon.web 1'),
                ('+server.encryption', '2', 'integer 0-2', 'Anti-cheat level', '+server.encryption 2'),
                ('+fps.limit', '256', 'integer 60-1000', 'Server FPS limit', '+fps.limit 120'),
                ('+gc.buffer', '256', 'integer 64-2048', 'Garbage collection buffer (MB)', '+gc.buffer 512'),
                ('+server.pve', 'false', 'true/false', 'Player vs Environment mode', '+server.pve false'),
                ('+decay.scale', '1.0', 'float 0.0-10.0', 'Building decay multiplier', '+decay.scale 0.5'),
                ('+craft.instant', 'false', 'true/false', 'Instant crafting', '+craft.instant false'),
                ('+gather.rate', '1.0', 'float 0.1-50.0', 'Resource gather multiplier', '+gather.rate 2.0'),
                ('+server.tickrate', '30', 'integer 10-60', 'Server tick rate', '+server.tickrate 30'),
                ('+server.stability', 'true', 'true/false', 'Building stability checks', '+server.stability true')
            ]
        elif 'Unreal' in engine:
            flags = [
                ('Port', '7777', 'integer 1024-65535', 'Server port', 'Port=7777'),
                ('QueryPort', '27015', 'integer 1024-65535', 'Query port', 'QueryPort=27015'),
                ('MaxPlayers', '80', 'integer 1-100', 'Maximum players', 'MaxPlayers=50'),
                ('ServerName', '', 'string', 'Server name in browser', 'ServerName="My Server"'),
                ('ServerPassword', '', 'string', 'Password for private server', 'ServerPassword=pass123'),
                ('AdminPassword', '', 'string', 'Admin console password', 'AdminPassword=adminpass'),
                ('Random', 'NONE', 'NONE/FULL/LIMITED', 'Random map selection', 'Random=LIMITED'),
                ('NumReservedSlots', '0', 'integer 0-20', 'Reserved admin slots', 'NumReservedSlots=2'),
                ('ShouldAdvertise', 'true', 'true/false', 'Advertise on server browser', 'ShouldAdvertise=true'),
                ('IsLANMatch', 'false', 'true/false', 'LAN only mode', 'IsLANMatch=false'),
                ('AllowCheats', 'false', 'true/false', 'Enable cheat commands', 'AllowCheats=false'),
                ('RecordDemos', 'false', 'true/false', 'Record demo files', 'RecordDemos=true'),
                ('PublicQueue', 'true', 'true/false', 'Use public matchmaking', 'PublicQueue=true'),
                ('AllowTeamChange', 'true', 'true/false', 'Allow players to switch teams', 'AllowTeamChange=true'),
                ('TKAutoKick', 'true', 'true/false', 'Auto-kick team killers', 'TKAutoKick=true'),
                ('PreventTeamKill', 'true', 'true/false', 'Prevent team killing', 'PreventTeamKill=false'),
                ('UnlimitedAmmo', 'false', 'true/false', 'Infinite ammunition', 'UnlimitedAmmo=false'),
                ('MapRotation', '', 'string', 'Map rotation list', 'MapRotation=Map1,Map2,Map3'),
                ('RoundTime', '300', 'integer 60-3600', 'Round duration (seconds)', 'RoundTime=600'),
                ('LogToFile', 'true', 'true/false', 'Enable file logging', 'LogToFile=true')
            ]
        else:
            # Generic flags for other engines
            flags = [
                ('--port', '25565', 'integer 1024-65535', 'Server port', '--port 25565'),
                ('--max-players', '20', 'integer 1-1000', 'Maximum players', '--max-players 50'),
                ('--server-name', '', 'string', 'Server name', '--server-name "My Server"'),
                ('--password', '', 'string', 'Server password', '--password mypass'),
                ('--admin-password', '', 'string', 'Admin password', '--admin-password adminpass'),
                ('--world-size', 'medium', 'small/medium/large', 'World generation size', '--world-size large'),
                ('--difficulty', 'normal', 'easy/normal/hard', 'Game difficulty', '--difficulty hard'),
                ('--pvp', 'true', 'true/false', 'Player vs Player enabled', '--pvp false'),
                ('--auto-save', '300', 'integer 60-3600', 'Auto-save interval', '--auto-save 600'),
                ('--log-level', 'info', 'debug/info/warn/error', 'Logging verbosity', '--log-level debug'),
                ('--console', '', 'flag', 'Enable console interface', '--console'),
                ('--dedicated', '', 'flag', 'Run as dedicated server', '--dedicated'),
                ('--no-pause', '', 'flag', 'Disable pause when unfocused', '--no-pause'),
                ('--headless', '', 'flag', 'Run without graphics', '--headless'),
                ('--threads', '0', 'integer 0-16', 'Worker thread count', '--threads 4')
            ]
            
        # Format as table
        table_rows = []
        for flag_data in flags:
            flag, default, type_info, desc, example = flag_data
            table_rows.append(f"| {flag} | {default} | {type_info} | {desc} | {example} |")
                
        return '\n'.join(table_rows)
        
    def generate_config_files_section(self, game_name, engine):
        """Generate configuration files section"""
        configs = []
        
        if 'Source' in engine:
            configs = [
                "### Core Configuration Files",
                "",
                "**Windows Paths:**",
                "- `server.cfg` - Main server configuration",
                "- `autoexec.cfg` - Auto-executed commands on startup", 
                "- `banned_user.cfg` - Banned Steam IDs",
                "- `banned_ip.cfg` - Banned IP addresses",
                "- `listip.cfg` - Reserved slot IP addresses",
                "- `mapcycle.txt` - Map rotation list",
                "- `motd.txt` - Message of the day",
                "- `admins_simple.ini` - Simple admin system (SourceMod)",
                "",
                "**Linux Paths:**",
                "- `/opt/steamcmd/servers/{}/cfg/server.cfg`",
                "- `/opt/steamcmd/servers/{}/cfg/autoexec.cfg`",
                "- `/opt/steamcmd/servers/{}/addons/sourcemod/configs/`",
                ""
            ]
        elif 'Unity' in engine or 'Rust' in game_name:
            configs = [
                "### Core Configuration Files", 
                "",
                "**Windows Paths:**",
                "- `server.cfg` - Main server settings",
                "- `users.cfg` - User permissions and data",
                "- `bans.cfg` - Banned players list",
                "- `oxide/config/` - Plugin configurations",
                "- `oxide/data/` - Plugin data storage",
                "- `oxide/logs/` - Server and plugin logs",
                "",
                "**Linux Paths:**",
                "- `~/.steam/steamapps/common/rust_dedicated/server.cfg`",
                "- `~/.steam/steamapps/common/rust_dedicated/oxide/`",
                ""
            ]
        else:
            configs = [
                "### Configuration Files",
                "",
                "**Windows Paths:**",
                "- `server.properties` - Main server configuration",
                "- `whitelist.json` - Allowed players list", 
                "- `ops.json` - Server operators list",
                "- `banned-players.json` - Banned players",
                "- `banned-ips.json` - Banned IP addresses",
                "- `server-icon.png` - Server icon (64x64)",
                "",
                "**Linux Paths:**",
                "- `/opt/gameserver/server.properties`",
                "- `/opt/gameserver/world/` - World save files",
                ""
            ]
            
        return '\n'.join(configs)
        
    def generate_workshop_section(self, game_name, supports_workshop):
        """Generate Steam Workshop integration section"""
        if not supports_workshop:
            return "This game does not support Steam Workshop integration."
            
        return """### Steam Workshop Setup

**Authentication Requirements:**
- Valid Steam account with game ownership
- Game Server Login Token (GSLT) from Steam
- Proper Steam API authentication

**Workshop Collection Management:**
1. Create Workshop collection in Steam client
2. Note the collection ID from the URL
3. Configure server to subscribe to collection
4. Set up automatic updates for Workshop content

**File Locations:**

**Windows:**
- Workshop cache: `%USERPROFILE%/.steam/steamapps/workshop/content/<appid>/`
- Server workshop: `steamapps/workshop/content/<appid>/`

**Linux:**
- Workshop cache: `~/.steam/steamapps/workshop/content/<appid>/`
- Server workshop: `steamapps/workshop/content/<appid>/`

**Common Workshop Commands:**
```bash
# Subscribe to workshop item
workshop.download <item_id>

# Update all subscribed items  
workshop.update

# List subscribed items
workshop.list

# Unsubscribe from item
workshop.unsubscribe <item_id>
```

**Troubleshooting Workshop Issues:**
- Verify GSLT is valid and not expired
- Check Steam API connectivity
- Clear workshop cache if downloads fail
- Ensure sufficient disk space for content
- Monitor workshop update logs for errors"""

    def generate_sample_configs(self, game_name, engine):
        """Generate sample configuration files"""
        if 'Source' in engine:
            return """### server.cfg Example
```
// Server identification
hostname "My Self-Hosted Server"
sv_password ""
rcon_password "secure_rcon_password_here"

// Game settings  
mp_maxrounds 30
mp_timelimit 0
mp_roundtime 115
mp_buytime 15
mp_startmoney 800

// Server behavior
sv_cheats 0
sv_pure 1
sv_lan 0
sv_region 255

// Network optimization
sv_maxcmdrate 128
sv_maxupdaterate 128
sv_minrate 20000
sv_maxrate 100000

// Logging
log on
sv_logbans 1
sv_logecho 1
sv_logfile 1
sv_log_onefile 0
```"""
        else:
            return """### server.properties Example
```
# Basic server settings
server-name=My Self-Hosted Server
server-port=25565
max-players=20
gamemode=survival
difficulty=normal

# World settings
level-name=world
level-seed=
level-type=default
spawn-protection=16

# Network settings
enable-query=true
query.port=25565
enable-rcon=true
rcon.port=25575
rcon.password=secure_password_here

# Performance
view-distance=10
simulation-distance=10
max-tick-time=60000
```"""

    def generate_game_guide(self, game_name):
        """Generate complete guide for a single game"""
        template = self.load_template()
        
        # Gather game information
        engine = self.detect_engine(game_name)
        app_id = self.get_app_id(game_name)
        workshop_support = self.supports_workshop(game_name)
        
        # Generate content sections
        port_table = self.generate_port_table(game_name, engine)
        startup_flags_table = self.generate_startup_flags(game_name, engine)
        config_files_section = self.generate_config_files_section(game_name, engine)
        workshop_section = self.generate_workshop_section(game_name, workshop_support)
        sample_configs = self.generate_sample_configs(game_name, engine)
        
        # Template replacements
        replacements = {
            '{game_name}': game_name,
            '{engine_type}': engine,
            '{workshop_support_text}': 'supports' if workshop_support else 'does not support',
            '{port_table}': port_table,
            '{startup_flags_table}': startup_flags_table,
            '{config_files_section}': config_files_section,
            '{workshop_section}': workshop_section,
            '{sample_configs}': sample_configs,
            '{windows_command}': f'server.exe +map de_dust2 +maxplayers 16',
            '{linux_command}': f'./server +map de_dust2 +maxplayers 16',
            '{basic_command}': f'./server --dedicated --console',
            '{production_command}': f'./server --dedicated --console --max-players 50 --auto-save 300',
            '{performance_command}': f'./server --dedicated --headless --threads 4 --max-players 100',
            '{windows_batch_example}': f'server.exe --dedicated --console\npause',
            '{linux_service_example}': f'/opt/gameserver/server --dedicated --console'
        }
        
        # Apply replacements
        content = template
        for placeholder, replacement in replacements.items():
            content = content.replace(placeholder, replacement)
            
        return content
        
    def convert_to_pdf(self, markdown_file, pdf_file):
        """Convert Markdown to PDF using Pandoc"""
        try:
            cmd = [
                'pandoc',
                str(markdown_file),
                '-o', str(pdf_file),
                '--from=gfm',
                '--toc',
                '--toc-depth=3',
                '--number-sections',
                '--pdf-engine=wkhtmltopdf',
                f'--metadata=title:{markdown_file.stem.replace("-", " ").title()} Server Guide',
                '--metadata=author:Self-Hosting Guide',
                f'--metadata=date:{datetime.now().strftime("%Y-%m-%d")}'
            ]
            
            result = subprocess.run(cmd, capture_output=True, text=True, timeout=120)
            
            if result.returncode == 0:
                print(f"✓ PDF generated: {pdf_file.name}")
                return True
            else:
                print(f"✗ PDF generation failed for {markdown_file.name}")
                print(f"  Error: {result.stderr}")
                return False
                
        except subprocess.TimeoutExpired:
            print(f"✗ PDF generation timed out for {markdown_file.name}")
            return False
        except Exception as e:
            print(f"✗ PDF generation error for {markdown_file.name}: {e}")
            return False
            
    def validate_content(self, content, game_name):
        """Validate content for forbidden terms and requirements"""
        issues = []
        
        # Check for forbidden provider mentions
        content_lower = content.lower()
        for term in self.forbidden_terms:
            if term.lower() in content_lower:
                issues.append(f"Contains forbidden term: {term}")
                
        # Check for HTML entity escapes
        if '&lt;' in content or '&gt;' in content:
            issues.append("Contains HTML entity escapes (&lt;, &gt;)")
            
        # Check for required sections
        required_sections = [
            '## Overview', '## System Requirements', '## Ports & Networking',
            '## Startup Parameters', '## Configuration Files', '## Troubleshooting'
        ]
        
        for section in required_sections:
            if section not in content:
                issues.append(f"Missing required section: {section}")
                
        if issues:
            print(f"⚠️  Validation issues for {game_name}:")
            for issue in issues:
                print(f"    - {issue}")
            return False
            
        return True
        
    def generate_all_guides(self):
        """Generate guides for all games in CSV"""
        games = self.read_csv_games()
        
        if not games:
            print("No games found in CSV file")
            return False
            
        print(f"\nGenerating guides for {len(games)} games...")
        print("=" * 50)
        
        generated_md = 0
        generated_pdf = 0
        failed_games = []
        
        for game in games:
            game_name = game['name']
            sanitized_name = self.sanitize_filename(game_name)
            
            print(f"\nProcessing: {game_name}")
            
            try:
                # Generate Markdown content
                content = self.generate_game_guide(game_name)
                
                # Validate content
                if not self.validate_content(content, game_name):
                    failed_games.append(f"{game_name} (validation failed)")
                    continue
                
                # Write Markdown file
                md_file = self.output_md / f"{sanitized_name}.md"
                with open(md_file, 'w', encoding='utf-8') as f:
                    f.write(content)
                print(f"  ✓ Markdown: {md_file.name}")
                generated_md += 1
                
                # Generate PDF
                pdf_file = self.output_pdf / f"{sanitized_name}.pdf"
                if self.convert_to_pdf(md_file, pdf_file):
                    generated_pdf += 1
                else:
                    failed_games.append(f"{game_name} (PDF generation failed)")
                    
            except Exception as e:
                print(f"  ✗ Error processing {game_name}: {e}")
                failed_games.append(f"{game_name} (processing error)")
                
        # Generate summary
        print("\n" + "=" * 50)
        print("GENERATION SUMMARY")
        print("=" * 50)
        print(f"Games processed: {len(games)}")
        print(f"Markdown files generated: {generated_md}")
        print(f"PDF files generated: {generated_pdf}")
        print(f"Failed games: {len(failed_games)}")
        
        if failed_games:
            print(f"\nFailed games:")
            for failed in failed_games:
                print(f"  - {failed}")
                
        # Save generation manifest
        manifest = {
            "generated_at": datetime.now().isoformat(),
            "total_games": len(games),
            "markdown_generated": generated_md,
            "pdfs_generated": generated_pdf,
            "failed_games": failed_games,
            "games": [{"name": g["name"], "sanitized": self.sanitize_filename(g["name"])} for g in games]
        }
        
        manifest_file = self.output_pdf / "manifest.json"
        with open(manifest_file, 'w', encoding='utf-8') as f:
            json.dump(manifest, f, indent=2)
        print(f"\nManifest saved: {manifest_file}")
        
        return len(failed_games) == 0

if __name__ == "__main__":
    generator = GameGuideGenerator()
    success = generator.generate_all_guides()
    sys.exit(0 if success else 1)