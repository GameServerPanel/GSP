# Game Documentation TODO System - Quick Reference

## System Overview
All game documentation folders now have a "complete" status field. Incomplete documentation displays with "TODO: " prefix on the docs.php page for easy visual identification.

## Current Status (December 19, 2024)

### ✅ Complete Documentation (1 game)
- **Minecraft Server** - Full comprehensive documentation with all sections

### ❌ Incomplete Documentation (146 games)
All other games display with "TODO: " prefix and need comprehensive research

## Priority Order for Completion

### PHASE 2: ARMA Family + DayZ (NEXT - HIGH PRIORITY)
1. **Arma 3** - Modern ARMA platform, highly popular
2. **Arma 2: Operation Arrowhead** - Required for DayZ Mod
3. **Arma 2** - Base game (if separate from OA)
4. **Arma 2: Combined Operations** - ARMA2 + OA combo for DayZ Mod
5. **DayZ Standalone** - Standalone survival game
6. **DayZ Mod** (if exists) - Original mod version

**Research Sources for ARMA/DayZ:**
- Bohemia Interactive Wiki (https://community.bistudio.com/wiki)
- LGSM scripts (LinuxGSM game configs)
- r/arma, r/dayzservers Reddit communities
- BI Forums (https://forums.bohemia.net/)
- DayZ Forums (https://forums.dayz.com/)
- Steam Community Guides (highly-rated)

### PHASE 3: Popular Multiplayer Games
**Batch 1 (Counter-Strike Family):**
- Counter-Strike 1.6
- Counter-Strike: Source
- Counter-Strike 2
- Counter-Strike: Global Offensive

**Batch 2 (Survival/Building Games):**
- Rust
- Terraria  
- Valheim
- Garry's Mod
- ARK: Survival Evolved
- 7 Days to Die

**Batch 3 (Co-op Shooters):**
- Left 4 Dead
- Left 4 Dead 2
- Killing Floor
- Killing Floor 2
- Team Fortress 2

**Batch 4 (Tactical Shooters):**
- Insurgency
- Insurgency: Sandstorm
- Squad

### PHASE 4: Remaining Games (50+ games)
All other game folders in alphabetical order

## Documentation Template Requirements

Each game must include (following Minecraft template):

### Required Sections:
1. **Navigation Box** - Quick links to all sections with emoji icons
2. **Quick Info** - Game overview and key details in styled box
3. **Comprehensive Ports Table:**
   - Port number
   - Protocol (TCP/UDP)
   - Purpose/Description
   - Required or Optional status
4. **Firewall Configuration Examples:**
   - UFW (Ubuntu/Debian)
   - FirewallD (CentOS/RHEL)
   - Windows Firewall
   - iptables (generic Linux)
5. **Startup Parameters Section:**
   - Command syntax
   - Parameter explanations
   - Common configurations
   - Examples with descriptions
6. **Troubleshooting Section:**
   - Server won't start
   - Connection issues
   - Performance problems
   - Mod/plugin conflicts (if applicable)
   - Common error messages with solutions
7. **Performance Optimization**
8. **Security Best Practices**
9. **Additional Resources** - Links to official docs, wikis, community guides
10. **Important Notes** - Warning box with critical information

### Research Requirements:
- Search official game wikis
- Check LGSM scripts for accurate port/parameter info
- Review Steam Community guides (highly-rated)
- Check Reddit communities (r/gameservers, game-specific subs)
- Look for GitHub repos with server configs
- Include user-contributed solutions from forums
- Cite all sources used

## How to Mark Documentation Complete

When a game's documentation is finished:

1. **Edit metadata.json** in the game folder:
   ```json
   {
     "name": "Game Name",
     "description": "Description",
     "category": "game",
     "order": 10,
     "complete": true
   }
   ```

2. **Change** `"complete": false` to `"complete": true`

3. **Verify** on docs.php - game name should no longer show "TODO: " prefix

## Estimated Time Per Game
- **Research:** 15-30 minutes (official docs, wikis, LGSM, Reddit, Steam)
- **Writing:** 20-30 minutes (following template structure)
- **Testing/Review:** 5-10 minutes
- **Total:** 40-70 minutes per game for comprehensive documentation

## Files Modified in TODO System Implementation
- `modules/billing/docs.php` - Added TODO prefix logic
- `modules/billing/docs/*/metadata.json` - Added complete field to 146 files
- `update_metadata_complete.ps1` - Batch update script
- `RECENT_FIXES_SUMMARY.md` - Updated with TODO system details
- `GAME_DOCS_TODO_REFERENCE.md` - This reference file

## Next Immediate Action
Begin Phase 2: Research and complete ARMA family + DayZ documentation (6 games total)
