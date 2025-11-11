# GameServers.World - Billing Module

## Overview
The billing module is a complete standalone website for selling game servers. It can be deployed on the same machine as the GSP panel or on a completely separate external web host.

## Documentation System

### Visual TODO System ✅
As of December 19, 2024, all game documentation includes completion tracking:

- **Complete Documentation:** Displays with normal name (e.g., "Minecraft Server")
- **Incomplete Documentation:** Displays with "TODO: " prefix (e.g., "TODO: Arma 3")

### Current Status
- **Complete:** 1 game (Minecraft - comprehensive template)
- **Incomplete:** 146 games (marked with TODO prefix)

### Documentation Template Standard
All complete documentation should match the Minecraft template:
- Comprehensive ports table (ALL ports with purposes)
- Firewall configurations (UFW, FirewallD, Windows, iptables)
- Startup parameters with detailed explanations
- Troubleshooting sections with specific solutions
- Performance optimization tips
- Security best practices
- Resource links with citations

### Viewing Documentation
- Browse to `docs.php` to see all game documentation
- Games with "TODO: " prefix need comprehensive research and writing
- Click any game to view its documentation page

### Marking Documentation Complete
When game documentation is finished:
1. Edit `docs/{game}/metadata.json`
2. Change `"complete": false` to `"complete": true`
3. The TODO prefix will automatically disappear

## Recent Updates

### December 19, 2024 - Visual TODO System
- ✅ Implemented completion tracking for all 148 game folders
- ✅ Created PowerShell automation script (`update_metadata_complete.ps1`)
- ✅ Updated docs.php with automatic TODO prefix display
- ✅ Minecraft documentation completed as template example

### November 10, 2025 - Critical Fixes
- ✅ Fixed PayPal payment capture session issue
- ✅ Removed cart debug logging code
- ✅ Fixed cart page header/footer consistency
- ✅ Implemented AJAX invoice removal with Font Awesome icons

## Key Files

### Documentation System
- `docs.php` - Documentation browser with TODO system
- `docs/*/index.php` - Individual game documentation pages
- `docs/*/metadata.json` - Game metadata with completion status
- `update_metadata_complete.ps1` - Batch metadata update script

### Reference Documents
- `PHASE1_COMPLETE_SUMMARY.md` - Phase 1 implementation summary
- `GAME_DOCS_TODO_REFERENCE.md` - Complete reference for documentation system
- `RECENT_FIXES_SUMMARY.md` - All recent fixes and enhancements

### Payment Integration
- `api/capture_order.php` - PayPal payment capture (fixed session handling)
- `payment_success.php` - Payment success redirect
- `payment_cancel.php` - Payment cancellation handler

### Shopping Cart
- `cart.php` - Shopping cart UI with PayPal integration (cleaned up)
- `add_to_cart.php` - Add items to cart
- `remove_from_cart.php` - AJAX removal endpoint

## Development Guidelines

### Adding New Game Documentation
1. Create folder: `docs/{game-slug}/`
2. Create `metadata.json`:
   ```json
   {
     "name": "Game Name",
     "description": "Brief description",
     "category": "game",
     "order": 100,
     "complete": false
   }
   ```
3. Create `index.php` following Minecraft template
4. Add optional `icon.png` or `icon.jpg`
5. When complete, set `"complete": true` in metadata

### Research Sources for Game Documentation
- Official game wikis and documentation
- LGSM (LinuxGSM) scripts and configuration files
- Steam Community Guides (highly-rated)
- Reddit communities (r/gameservers, game-specific)
- GitHub repositories with server configurations
- Official game forums
- User-contributed solutions and fixes

### Documentation Quality Standards
- **Comprehensive Ports:** List ALL ports with purposes (TCP/UDP)
- **Startup Parameters:** Full parameter explanations with examples
- **Troubleshooting:** Specific common issues with tested solutions
- **Firewall Configs:** Multiple platform examples (Linux + Windows)
- **Citations:** Link to all sources used in research
- **Testing:** Verify all commands and configurations are accurate

## Next Priorities

### Phase 2: ARMA Family + DayZ (HIGH PRIORITY)
1. Arma 3
2. Arma 2: Operation Arrowhead
3. Arma 2: Combined Operations
4. DayZ Standalone
5. DayZ Mod

### Phase 3: Popular Multiplayer Games
- Counter-Strike family (1.6, Source, CS2, CS:GO)
- Survival/Building (Rust, Terraria, Valheim, ARK)
- Co-op Shooters (L4D series, Killing Floor series, TF2)
- Tactical Shooters (Insurgency series, Squad)

### Phase 4: Remaining Games
- All other 50+ game folders in alphabetical order

## Testing Checklist

### PayPal Integration
- [ ] User can add servers to cart
- [ ] PayPal checkout button works
- [ ] Payment completes successfully
- [ ] Success page displays correctly
- [ ] No `NO_USER_SESSION` errors in logs

### Documentation System
- [ ] docs.php displays all games correctly
- [ ] TODO prefix shows for incomplete docs
- [ ] Complete docs show without TODO prefix
- [ ] Individual game pages load correctly
- [ ] Navigation links work within documentation

### Cart Functionality
- [ ] Cart displays all items correctly
- [ ] Remove button deletes items (AJAX)
- [ ] Header and footer display consistently
- [ ] Fonts match other billing pages

## Technical Notes

### Session Management
- **CRITICAL:** Always use `session_name("gameservers_website")` before `session_start()`
- Sessions are separate from panel sessions
- User authentication stored in `$_SESSION['website_user_id']`

### Database Connection
- Uses mysqli with credentials from `includes/config.inc.php`
- All database operations use native mysqli functions
- Never use panel-specific functions

### Standalone Design
- Module must work on external hosting
- No dependencies on panel files
- All paths use `__DIR__` relative references
- MySQL connection direct (not through panel)

## Support & Resources
- Main project: GameServerPanel/GSP
- Branch: Panel-unstable
- Documentation: Browse `docs.php` for game-specific guides
- Issues: Check logs in `modules/billing/logs/`

---
**Last Updated:** December 19, 2024  
**Version:** 2.0 (with Visual TODO System)
