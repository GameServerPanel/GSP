# Comprehensive Server Admin Guide System

This directory contains the complete server admin guide generation system that creates exhaustive documentation for all supported games.

## Quick Start

Generate all guides and PDFs:
```bash
./tools/generate_all_guides.sh
```

## Components

### Core Scripts
- **`generate_server_guides.py`** - Main generator that creates Markdown guides and PDFs
- **`validate_guides.py`** - Quality validation ensuring guides meet exhaustive standards  
- **`generate_all_guides.sh`** - Complete workflow automation script

### Data Sources
- **`data/games/*.yml`** - YAML files containing exhaustive game data
- **`game_titles.txt`** - Reference file listing all supported games

### Generated Output
- **`docs/games/<slug>/index.md`** - Comprehensive Markdown guide for each game
- **`dist/pdfs/<slug>__Server_Admin_Guide_v1.pdf`** - PDF version of each guide
- **`docs/games/_index.md`** - Index page listing all games with links
- **`dist/pdfs/manifest.json`** - Machine-readable metadata manifest

## Features

### Exhaustive Coverage
Each guide includes:
- **Complete startup parameters** (minimum 10 flags with defaults, types, descriptions, examples)
- **Full port mapping** with protocols and relationships to base Game Port
- **All configuration files** (minimum 8 entries with paths for Windows/Linux)
- **Steam Workshop integration** (where supported)
- **Deep troubleshooting** with specific fixes and file/flag references
- **Management procedures** (RCON, backups, updates, performance tuning)

### Quality Gates
- Validates required H2 sections: Quick Start, Port Map, Startup Parameters (EXHAUSTIVE), Configuration Files (ALL), Steam Workshop, Management, Troubleshooting (Deep), Appendices
- Ensures minimum content standards (10+ startup flags, 8+ config files)
- Checks for placeholder content (TODO, TBD, etc.)
- Validates file structure and cross-references

### Current Status
- ✅ **14 games processed** with comprehensive guides
- ✅ **14 PDF files generated** (60-70KB each, comprehensive content)
- ✅ **All quality gates passing** (no critical errors)
- ✅ **Zero placeholder content** (all TODO items resolved)

### Enhanced Examples
- **Counter-Strike: Global Offensive**: 30 startup flags, 17 config files, complete SourceMod integration
- **7 Days to Die**: 15 startup flags, 10 config files, Unity engine specifics

## Extending the System

### Adding New Games
1. Create `data/games/new-game.yml` following the schema
2. Include minimum 10 startup flags and 8 config entries
3. Run `./tools/generate_all_guides.sh` to generate and validate

### Schema Requirements
```yaml
name: "Official Game Name"
supports_workshop: true/false
appid: 123456
engine: "Engine Name"
linuxgsm_support: true
ogp_support: true

startup:
  default_command: 'server.exe -flags'
  ports: [...]  # Complete port mapping
  flags: [...]  # Minimum 10 flags with examples

configs: [...]   # Minimum 8 config files
troubleshooting: [...]  # Deep technical issues
workshop: {...}  # If workshop supported
```

## Dependencies

- Python 3.6+
- Pandoc with XeLaTeX
- PyYAML

Install with:
```bash
sudo apt install pandoc texlive-xetex
pip install pyyaml
```

This system fulfills the requirement for "exhaustive" server admin guides that serve as complete "one-stop" documentation for every game we host.