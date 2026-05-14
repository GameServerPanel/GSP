# Game Server Documentation Generation

## Overview

This directory contains comprehensive game server hosting documentation for 143+ games. The documentation follows a consistent template structure based on the Minecraft server guide.

## Generated Documentation

In November 2024, we generated comprehensive documentation for 98 game servers that were previously in the "todo" category. Each game now has:

- **Quick Navigation Menu** - Easy access to all sections
- **Quick Info** - Default ports, protocols, RAM requirements, engine info
- **Network Ports** - Detailed port tables with firewall configuration examples
- **Installation & Setup** - System requirements and installation steps
- **Server Configuration** - Configuration files and essential settings
- **Startup Parameters** - Command-line parameters and service setup
- **Troubleshooting** - Common issues and solutions
- **Performance Optimization** - Tuning and monitoring tips
- **Security Best Practices** - Firewall, passwords, updates, DDoS protection
- **Additional Resources** - External references and community links

## Documentation Structure

Each game documentation folder contains:

```
gamename/
├── index.php          - Main documentation content
├── metadata.json      - Category, name, description, order
└── icon.png/jpg       - Game icon (optional)
```

### metadata.json Format

```json
{
    "name": "Game Name",
    "description": "Brief description for the game",
    "category": "game",
    "order": 1
}
```

## Categories

Documentation is organized into categories:

- **game** - Game server documentation (143+ servers)
- **mods** - Mod/plugin documentation
- **panel** - Panel-specific documentation
- **troubleshooting** - General troubleshooting guides
- **other** - Other documentation

## Generation Tool

The documentation was generated using the `generate_game_docs.py` script located in `/tools/`.

### Data Sources

The generator uses multiple data sources:

1. **XML Configurations** (`/modules/config_games/server_configs/*.xml`)
   - Port configurations
   - Configuration file paths
   - Custom fields and parameters

2. **YAML Knowledgepack** (`/modules/billing/docs/gameserver_knowledgepack_v2.yaml`)
   - Network port details
   - System requirements
   - Startup commands
   - Troubleshooting tips
   - External references

3. **Template Structure** (Based on Minecraft documentation)
   - Consistent formatting
   - Comprehensive coverage
   - User-friendly navigation

### Running the Generator

```bash
cd /home/runner/work/GSP/GSP
python3 tools/generate_game_docs.py
```

The script will:
1. Load XML configurations and YAML knowledgepack
2. Find all folders with `category: "todo"` in metadata.json
3. Generate comprehensive PHP documentation for each game
4. Update metadata.json to change category to "game"

## Games Documented

The following games now have comprehensive hosting documentation:

### Action/FPS Games
- Aliens vs Predator, Call of Duty series (COD, COD2, COD4, MW2, MW3, WAW, Black Ops)
- Counter-Strike variants (CS 1.6, CS:CZ, CS:S, CS:GO, CS:Promod, CS 2D)
- Battlefield 2, Battlefield Bad Company 2
- Half-Life variants (HLDM, HL2DM, HLTV)
- Insurgency, Medal of Honor series (MOHAA, MOHBR, MOHSP, MOHSPDEMO)
- Quake 3, Quake 4, Sniper Elite V2

### Source Engine Games
- Dystopia, Hidden: Source, Natural Selection 2, Nuclear Dawn
- Pirates Vikings and Knights II, Zombie Panic Source, Synergy
- Brain Bread 2, Day of Defeat: Source

### Open World/Survival
- Atlas, Hurtworld, Life is Feudal, Miscreated
- Reign of Kings, The Forest, Space Engineers
- Wurm Unlimited, PixArk

### Racing/Simulation
- Assetto Corsa, Euro Truck Simulator 2
- Trackmania Nations, Trackmania Forever
- Wreckfest

### Multiplayer Mods
- FiveM (GTA V), Multi Theft Auto (GTA SA/VC)
- IV:MP (GTA IV), JC:MP (Just Cause 2)
- Mafia II Online, Epoch Mod

### Strategy/Building
- Avorion, Colony Survival, Eco
- FreeCol, OpenTTD, Empyrion Galactic Survival

### Arena/Combat
- Jedi Knight 2, Jedi Knight: Jedi Academy
- Mount & Blade: Warband, Mordhau
- Soldat, Smashball, Blood Frontier
- Citadel: Forged with Fire, Red Orchestra 2, Rising Storm 2
- Arma Reforger, Homefront

### Voice/Communication
- TeamSpeak 2, TeamSpeak 3, Mumble, Ventrilo
- SinusBot, Shoutcast, Shoutcast Bot

### Classic/Retro
- Unreal Tournament 99, UT2004, UT3
- Serious Sam HD TFE, Serious Sam HD TSE
- Roadkill, Wolfenstein: Return to Castle Wolfenstein
- Enemy Territory, Warsow, Nexuiz, Xonotic
- IL-2 Sturmovik, Halo: Combat Evolved

### Other
- Feed the Beast (Minecraft modpack)
- Spigot MC (Minecraft server software)
- Rigs of Rods, Flight Gear Multiplayer Server
- Virtual Box, Smokinguns, DMC, Gearbox, ESMod
- SpunkyBot, AoC, SMS

## Viewing Documentation

Access the documentation through the billing website:

```
/modules/billing/docs.php
```

Or view a specific game:

```
/modules/billing/docs.php?action=view&doc=gamename
```

## Maintenance

To update or regenerate documentation:

1. Update data sources (XML configs, YAML knowledgepack)
2. Modify the generator script if needed
3. Run the generator script
4. Commit changes to the repository

## Template Customization

To customize the documentation template, edit the `build_php_content()` method in `generate_game_docs.py`.

The template includes:
- Inline CSS styling matching the site theme
- Responsive design for mobile/desktop
- Color-coded information boxes
- Syntax-highlighted code blocks
- Professional formatting

## Contributing

When adding new game documentation:

1. Create a folder with the game's slug name
2. Add metadata.json with game information
3. Add icon.png or icon.jpg (optional)
4. Either manually create index.php or add to "todo" category and run generator
5. Update this README if adding new categories

## License

Documentation follows the same license as the GSP project. See main repository LICENSE file.
