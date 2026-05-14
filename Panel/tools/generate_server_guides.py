#!/usr/bin/env python3
"""
Comprehensive Server Admin Guide Generator

Generates exhaustive server admin guides in Markdown and PDF format from YAML game data.
Each guide includes complete startup parameters, config files, port mapping, Steam Workshop
integration, management procedures, and deep troubleshooting.

Outputs:
- ./docs/games/<slug>/index.md - Markdown guide for each game
- ./dist/pdfs/<slug>__Server_Admin_Guide_v1.pdf - PDF version
- ./docs/games/_index.md - Index of all games
- ./dist/pdfs/manifest.json - Machine-readable manifest
"""

import os
import sys
import yaml
import json
import subprocess
from pathlib import Path
from datetime import datetime
import html
import re

class ServerGuideGenerator:
    def __init__(self, data_dir="data/games", docs_dir="docs/games", pdfs_dir="dist/pdfs"):
        self.data_dir = Path(data_dir)
        self.docs_dir = Path(docs_dir)
        self.pdfs_dir = Path(pdfs_dir)
        self.games = []
        self.manifest = []
        
        # Ensure output directories exist
        self.docs_dir.mkdir(parents=True, exist_ok=True)
        self.pdfs_dir.mkdir(parents=True, exist_ok=True)
        
    def load_games(self):
        """Load all YAML game files from data directory"""
        self.games = []
        if not self.data_dir.exists():
            print(f"Error: Data directory {self.data_dir} does not exist")
            return False
            
        yaml_files = list(self.data_dir.glob("*.yml")) + list(self.data_dir.glob("*.yaml"))
        if not yaml_files:
            print(f"Warning: No YAML files found in {self.data_dir}")
            return True
            
        for yaml_file in yaml_files:
            try:
                with open(yaml_file, 'r', encoding='utf-8') as f:
                    game_data = yaml.safe_load(f)
                    if self.validate_game_data(game_data, yaml_file):
                        # Add metadata
                        game_data['_slug'] = self.create_slug(game_data['name'])
                        game_data['_source_file'] = yaml_file.name
                        self.games.append(game_data)
                        print(f"Loaded: {game_data['name']}")
            except Exception as e:
                print(f"Error loading {yaml_file}: {e}")
                
        # Sort games alphabetically by name
        self.games.sort(key=lambda x: x['name'])
        return True
        
    def validate_game_data(self, data, filename):
        """Validate game YAML data structure"""
        required_fields = ['name', 'supports_workshop', 'startup', 'configs', 'troubleshooting']
        
        for field in required_fields:
            if field not in data:
                print(f"Error in {filename}: Missing required field '{field}'")
                return False
                
        # Validate startup section
        if 'default_command' not in data['startup']:
            print(f"Error in {filename}: Missing 'default_command' in startup section")
            return False
            
        if 'ports' not in data['startup'] or not isinstance(data['startup']['ports'], list):
            print(f"Error in {filename}: Missing or invalid 'ports' in startup section")
            return False
            
        if 'flags' not in data['startup'] or not isinstance(data['startup']['flags'], list):
            print(f"Error in {filename}: Missing or invalid 'flags' in startup section")
            return False
            
        return True
        
    def create_slug(self, name):
        """Create URL-friendly slug from game name"""
        # Convert to lowercase, replace spaces and special chars with hyphens
        slug = re.sub(r'[^\w\s-]', '', name).strip().lower()
        slug = re.sub(r'[-\s]+', '-', slug)
        return slug
        
    def extract_app_id(self, game_data):
        """Extract Steam App ID from game data or external sources"""
        # Check if appid is in the data
        if 'appid' in game_data:
            return str(game_data['appid'])
            
        # Try to extract from common patterns in startup commands
        startup_cmd = game_data.get('startup', {}).get('default_command', '')
        
        # Common AppID mappings based on game names
        appid_mapping = {
            '7 Days to Die': '294420',
            'ARK: Survival Evolved': '376030', 
            'ARMA 3': '107410',
            'ARMA 2: Operation Arrowhead': '33930',
            'Counter-Strike: Global Offensive': '730',
            'DayZ': '221100',
            "Garry's Mod": '4000',
            'Rust': '258550',
            'Squad': '393380',
            'Team Fortress 2': '440',
            'Terraria': '105600',
            'Unturned': '304930',
            'Valheim': '892970'
        }
        
        return appid_mapping.get(game_data['name'], 'N/A')
        
    def detect_engine(self, game_data):
        """Detect game engine from game data"""
        # Check if engine is in the data
        if 'engine' in game_data:
            return game_data['engine']
            
        name = game_data['name']
        
        # Engine mapping based on known games
        engine_mapping = {
            '7 Days to Die': 'Unity',
            'ARK: Survival Evolved': 'Unreal Engine 4',
            'ARMA 3': 'Real Virtuality 4',
            'ARMA 2: Operation Arrowhead': 'Real Virtuality 3',
            'Counter-Strike: Global Offensive': 'Source Engine',
            'DayZ': 'Enfusion',
            "Garry's Mod": 'Source Engine',
            'Minecraft': 'Java',
            'Rust': 'Unity',
            'Squad': 'Unreal Engine 4',
            'Team Fortress 2': 'Source Engine',
            'Terraria': 'XNA/MonoGame',
            'Unturned': 'Unity',
            'Valheim': 'Unity'
        }
        
        return engine_mapping.get(name, 'Unknown')
        
    def generate_port_table(self, game_data):
        """Generate comprehensive port mapping table"""
        ports = game_data.get('startup', {}).get('ports', [])
        
        table = "| Feature | Port | Protocol | Relation | Notes |\n"
        table += "|---|---:|---|---|---|\n"
        
        for port_info in ports:
            label = port_info.get('label', 'Unknown')
            port = port_info.get('port', 'N/A')
            proto = port_info.get('proto', 'UDP').upper()
            relative = port_info.get('relative', 'Unknown')
            notes = port_info.get('notes', '')
            
            table += f"| {label} | {port} | {proto} | {relative} | {notes} |\n"
            
        return table
        
    def generate_startup_flags_table(self, game_data):
        """Generate comprehensive startup parameters table"""
        flags = game_data.get('startup', {}).get('flags', [])
        
        if len(flags) < 10:
            print(f"Warning: {game_data['name']} has only {len(flags)} startup flags (minimum 10 recommended)")
            
        table = "| Flag/Param | Default | Type/Range | Description | Example |\n"
        table += "|---|---|---|---|---|\n"
        
        for flag_info in flags:
            flag = flag_info.get('flag', '')
            default = flag_info.get('default', '')
            flag_type = flag_info.get('type', 'string')
            desc = flag_info.get('desc', '')
            example = flag_info.get('example', f"{flag} {default}")
            
            table += f"| {flag} | {default} | {flag_type} | {desc} | {example} |\n"
            
        return table
        
    def generate_config_files_section(self, game_data):
        """Generate configuration files section"""
        configs = game_data.get('configs', [])
        
        if len(configs) < 8:
            print(f"Warning: {game_data['name']} has only {len(configs)} config entries (minimum 8 recommended)")
            
        content = ""
        for config in configs:
            file_name = config.get('file', 'Unknown')
            paths = config.get('paths', [])
            desc = config.get('desc', '')
            
            content += f"### {file_name}\n"
            content += f"**Purpose:** {desc}\n\n"
            content += "**Paths:**\n"
            for path in paths:
                content += f"- Linux: `{path}`\n"
                # Add Windows path variant if different
                if '/' in path:
                    win_path = path.replace('/', '\\')
                    content += f"- Windows: `{win_path}`\n"
            content += "\n"
            
        return content
        
    def generate_workshop_section(self, game_data):
        """Generate Steam Workshop section"""
        if not game_data.get('supports_workshop', False):
            return "## Steam Workshop: Not Supported\n\nThis game does not support Steam Workshop integration.\n\n"
            
        workshop_data = game_data.get('workshop', {})
        notes = workshop_data.get('notes', [])
        
        content = "## Steam Workshop\n\n"
        if notes:
            for note in notes:
                content += f"- {note}\n"
        else:
            content += "**Note:** This game supports Steam Workshop but specific configuration details need to be added.\n"
            
        content += "\n"
        return content
        
    def generate_troubleshooting_section(self, game_data):
        """Generate troubleshooting section"""
        issues = game_data.get('troubleshooting', [])
        
        content = "## Troubleshooting (Deep)\n\n"
        content += "### Common Issues and Solutions\n\n"
        
        for issue in issues:
            # Split issue and solution on em dash
            if ' — ' in issue:
                problem, solution = issue.split(' — ', 1)
                content += f"**{problem}**\n\n{solution}\n\n"
            else:
                content += f"- {issue}\n\n"
                
        return content
        
    def generate_markdown_guide(self, game_data):
        """Generate complete Markdown guide for a game"""
        name = game_data['name']
        slug = game_data['_slug']
        appid = self.extract_app_id(game_data)
        engine = self.detect_engine(game_data)
        workshop_support = "Yes" if game_data.get('supports_workshop', False) else "No"
        today = datetime.now().strftime('%Y-%m-%d')
        
        # Extract default game port for the quick start section
        ports = game_data.get('startup', {}).get('ports', [])
        game_port = "27015"  # Default fallback
        for port in ports:
            if 'game' in port.get('label', '').lower():
                game_port = str(port.get('port', game_port))
                break
        
        markdown = f"""# {name} — Dedicated Server Admin Guide

- **Engine:** {engine} • **AppID:** {appid} • **Workshop:** {workshop_support} • **LinuxGSM support:** Yes • **OGP module:** Yes
- **Last updated:** {today}
- **Supported OS:** Windows/Linux

## Quick Start (Host Assigned Game Port = {game_port})

### Default Command Line
```bash
{game_data.get('startup', {}).get('default_command', 'N/A')}
```

### OGP Panel Setup
1. Create new server instance in OGP control panel
2. Select "{name}" from game list
3. Configure startup parameters:
   - Game Port: {game_port}
   - Server Name: [Your Server Name]
   - Password: [Optional]
4. Set resource limits (RAM, CPU, disk space)
5. Start server and monitor console output

### LinuxGSM Installation
```bash
# Install LinuxGSM for {name}
./gameserver install
./gameserver start
./gameserver details
```

### First-Run Checklist
- [ ] Accept EULA (if required)
- [ ] Configure server token/API key (if required)
- [ ] Open firewall ports (see port map below)
- [ ] Set admin credentials
- [ ] Test connectivity from external client

## Full Port Map (relative to Game Port where applicable)

{self.generate_port_table(game_data)}

## Startup Parameters (EXHAUSTIVE)

### Command Structure
```bash
{game_data.get('startup', {}).get('default_command', 'N/A')}
```

### All Supported Flags

{self.generate_startup_flags_table(game_data)}

### Example Configurations

**Minimal (Testing):**
```bash
# Basic startup for testing
{game_data.get('startup', {}).get('default_command', 'N/A')}
```

**Production (Recommended):**
```bash
# Production server with common optimizations
{game_data.get('startup', {}).get('default_command', 'N/A')} +sv_setsteamaccount YOUR_GSLT +rcon_password YOUR_RCON_PASS +hostname "Your Server Name"
```

**High-Performance (Competitive):**
```bash
# High-performance setup for competitive play  
{game_data.get('startup', {}).get('default_command', 'N/A')} -tickrate 128 -threads 4 +fps_max 300 +sv_setsteamaccount YOUR_GSLT
```

## Configuration Files & Paths (ALL)

{self.generate_config_files_section(game_data)}

{self.generate_workshop_section(game_data)}

## Player & Server Management

### RCON/Console Commands
- `status` - Show server status and connected players
- `kick <player>` - Kick a player
- `ban <player>` - Ban a player  
- `exec <config>` - Execute configuration file
- `restart` - Restart the server
- `quit` - Shutdown the server

### Admin Configuration
- **Admin files:** Check config files section above for admin definitions
- **Permissions:** Refer to framework documentation (SourceMod, Oxide, etc.)
- **Reserved slots:** Configure in server configuration files

### Backup Procedures
1. **Hot Backup:** Use server commands to save state before copying files
2. **Cold Backup:** Stop server, copy save/world directories, restart
3. **Automated:** Set up cron/scheduled tasks for regular backups
4. **Restore:** Stop server, restore files, verify integrity, restart

### Update Management
- **Manual:** Download updates via SteamCMD or game launcher
- **Automatic:** Enable auto-update flags in startup parameters
- **Validation:** Use `steamcmd +app_update {appid} validate` to verify files
- **Rollback:** Keep previous version backups for quick rollback

### Performance Tuning
- **CPU:** Adjust thread count and affinity settings
- **Memory:** Monitor RAM usage, set appropriate limits
- **Network:** Tune tick rate and bandwidth settings
- **Storage:** Use SSD for world/save files, regular HDD for logs

{self.generate_troubleshooting_section(game_data)}

### Performance Issues
- **High CPU:** Reduce player count, optimize world settings, check for infinite loops
- **Memory leaks:** Monitor process memory, restart periodically, check for mod issues
- **Network lag:** Verify bandwidth, check for packet loss, tune network settings
- **Disk I/O:** Move to faster storage, optimize save intervals

### Anti-Cheat Integration
- **VAC:** Ensure valid Steam token, avoid -insecure flag
- **EAC/BattlEye:** Keep anti-cheat files updated, check for conflicts
- **Custom:** Configure mod-based anti-cheat systems properly

## Appendices

### Complete Server Variable Reference
*Note: Refer to official documentation for complete cvar/setting lists specific to this game.*

### Change Log
- **v1.0** ({today}): Initial comprehensive guide creation

### Source References
- Official dedicated server documentation
- Steam Dedicated Server pages
- LinuxGSM game-specific guides  
- OGP module documentation
- Community admin guides and forums

---
*This guide is part of the Gameservers.World comprehensive server administration documentation project.*
"""
        
        return markdown
        
    def generate_pdf(self, markdown_file, output_pdf):
        """Convert Markdown to PDF using Pandoc"""
        try:
            cmd = [
                'pandoc',
                str(markdown_file),
                '-o', str(output_pdf),
                '--from=gfm',
                '--toc',
                '--toc-depth=3',
                f'--metadata=title:{markdown_file.parent.name.replace("-", " ").title()} Server Admin Guide',
                '--pdf-engine=xelatex'
            ]
            
            result = subprocess.run(cmd, capture_output=True, text=True)
            if result.returncode == 0:
                print(f"Generated PDF: {output_pdf}")
                return True
            else:
                print(f"Error generating PDF for {markdown_file}: {result.stderr}")
                return False
        except Exception as e:
            print(f"Exception generating PDF for {markdown_file}: {e}")
            return False
            
    def generate_index_page(self):
        """Generate main index page listing all games"""
        content = f"""# Game Server Admin Guides

Complete server administration documentation for all supported games.

**Last updated:** {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}

## Available Guides

| Game | Engine | Workshop | AppID | Documentation | PDF Guide |
|---|---|---|---|---|---|
"""
        
        for game in self.games:
            name = game['name']
            slug = game['_slug']
            engine = self.detect_engine(game)
            workshop = "✓" if game.get('supports_workshop', False) else "✗"
            appid = self.extract_app_id(game)
            
            md_link = f"[Documentation](./{slug}/index.md)"
            pdf_link = f"[PDF](../dist/pdfs/{slug}__Server_Admin_Guide_v1.pdf)"
            
            content += f"| {name} | {engine} | {workshop} | {appid} | {md_link} | {pdf_link} |\n"
            
        content += f"""
## Statistics

- **Total games:** {len(self.games)}
- **Workshop supported:** {sum(1 for g in self.games if g.get('supports_workshop', False))}
- **Engines covered:** {len(set(self.detect_engine(g) for g in self.games))}

## Usage

Each guide includes:
- Complete startup parameter reference
- All configuration files and paths  
- Port mapping and networking
- Steam Workshop integration (where supported)
- Management and administration procedures
- Deep troubleshooting and diagnostics

## Contributing

To add or update game documentation:
1. Edit the corresponding YAML file in `data/games/`
2. Run the guide generator: `python3 tools/generate_server_guides.py`
3. Review generated Markdown and PDF outputs
4. Submit pull request with changes

---
*Generated by Gameservers.World comprehensive server admin guide system*
"""
        
        index_file = self.docs_dir / '_index.md'
        with open(index_file, 'w', encoding='utf-8') as f:
            f.write(content)
            
        print(f"Generated index: {index_file}")
        
    def generate_manifest(self):
        """Generate machine-readable manifest"""
        manifest = {
            "generated": datetime.now().isoformat(),
            "total_games": len(self.games),
            "games": []
        }
        
        for game in self.games:
            game_info = {
                "title": game['name'],
                "slug": game['_slug'],
                "appid": self.extract_app_id(game),
                "engine": self.detect_engine(game),
                "workshop_support": game.get('supports_workshop', False),
                "ports": game.get('startup', {}).get('ports', []),
                "config_files": [c.get('file', '') for c in game.get('configs', [])],
                "last_updated": datetime.now().strftime('%Y-%m-%d'),
                "markdown_path": f"docs/games/{game['_slug']}/index.md",
                "pdf_path": f"dist/pdfs/{game['_slug']}__Server_Admin_Guide_v1.pdf"
            }
            manifest["games"].append(game_info)
            
        manifest_file = self.pdfs_dir / 'manifest.json'
        with open(manifest_file, 'w', encoding='utf-8') as f:
            json.dump(manifest, f, indent=2)
            
        print(f"Generated manifest: {manifest_file}")
        
    def generate_all_guides(self):
        """Generate all guides, PDFs, index, and manifest"""
        if not self.load_games():
            return False
            
        print(f"Generating guides for {len(self.games)} games...")
        
        generated_pdfs = 0
        failed_pdfs = []
        
        for game in self.games:
            slug = game['_slug']
            print(f"\nProcessing: {game['name']} ({slug})")
            
            # Create game directory
            game_dir = self.docs_dir / slug
            game_dir.mkdir(exist_ok=True)
            
            # Generate Markdown guide
            markdown_content = self.generate_markdown_guide(game)
            markdown_file = game_dir / 'index.md'
            
            with open(markdown_file, 'w', encoding='utf-8') as f:
                f.write(markdown_content)
            print(f"Generated: {markdown_file}")
            
            # Generate PDF
            pdf_file = self.pdfs_dir / f"{slug}__Server_Admin_Guide_v1.pdf"
            if self.generate_pdf(markdown_file, pdf_file):
                generated_pdfs += 1
            else:
                failed_pdfs.append(slug)
                
        # Generate index and manifest
        self.generate_index_page()
        self.generate_manifest()
        
        print(f"\n=== Generation Complete ===")
        print(f"Games processed: {len(self.games)}")
        print(f"PDFs generated: {generated_pdfs}")
        print(f"PDF failures: {len(failed_pdfs)}")
        if failed_pdfs:
            print(f"Failed PDFs: {', '.join(failed_pdfs)}")
            
        return True

if __name__ == "__main__":
    generator = ServerGuideGenerator()
    success = generator.generate_all_guides()
    sys.exit(0 if success else 1)