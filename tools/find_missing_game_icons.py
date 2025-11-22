#!/usr/bin/env python3
"""
Game Icon Finder for GSP
Identifies games that are missing icons and provides URLs to download them from SteamDB or official sources.

This script does NOT automatically download icons to avoid copyright issues.
Instead, it generates a report of missing icons with suggested sources.
"""

import os
import sys
import json
from pathlib import Path

def check_missing_icons():
    """Check which games are missing icons"""
    docs_dir = Path("/home/runner/work/GSP/GSP/modules/billing/docs")
    images_dir = Path("/home/runner/work/GSP/GSP/images/games")
    
    missing_icons = []
    
    print("="*70)
    print("GAME ICON CHECKER")
    print("="*70)
    print()
    
    # Check each game folder
    for folder in sorted(docs_dir.iterdir()):
        if not folder.is_dir():
            continue
        
        # Skip special folders
        if folder.name.startswith('.') or folder.name.startswith('_') or folder.name in ['common-issues', 'getting-started']:
            continue
        
        # Check for icon in docs folder
        has_icon_in_docs = (folder / 'icon.png').exists() or (folder / 'icon.jpg').exists()
        
        # Check for icon in images/games (various naming conventions)
        possible_names = [
            f"{folder.name}.jpg",
            f"{folder.name}.png",
            f"{folder.name.replace('_', ' ')}.jpg",
            f"{folder.name.replace('_', '')}.jpg",
        ]
        
        has_icon_in_images = any((images_dir / name).exists() for name in possible_names)
        
        if not has_icon_in_docs:
            # Get game name from metadata
            metadata_file = folder / 'metadata.json'
            game_name = folder.name
            if metadata_file.exists():
                try:
                    with open(metadata_file, 'r', encoding='utf-8') as f:
                        metadata = json.load(f)
                        game_name = metadata.get('name', folder.name)
                except:
                    pass
            
            missing_icons.append({
                'folder': folder.name,
                'name': game_name,
                'has_in_images': has_icon_in_images
            })
    
    # Print report
    print(f"Total games checked: {len([f for f in docs_dir.iterdir() if f.is_dir() and not f.name.startswith('.')]) - 2}")
    print(f"Games missing icons: {len(missing_icons)}")
    print()
    
    if missing_icons:
        print("="*70)
        print("MISSING ICONS REPORT")
        print("="*70)
        print()
        print("The following games need icons in their docs folder:")
        print("(modules/billing/docs/GAME/icon.png or icon.jpg)")
        print()
        
        for item in missing_icons:
            status = "✓ in images/" if item['has_in_images'] else "✗ not found"
            print(f"  {item['folder']:30s} - {item['name']:40s} {status}")
        
        print()
        print("="*70)
        print("HOW TO ADD ICONS:")
        print("="*70)
        print()
        print("1. Search for the game on SteamDB: https://steamdb.info/")
        print("2. Find the game's official icon/logo")
        print("3. Download as PNG or JPG (60x60 or larger recommended)")
        print("4. Save to: modules/billing/docs/GAMEFOLDER/icon.png")
        print("5. Optionally copy to: images/games/GAMENAME.jpg for panel use")
        print()
        print("For games not on Steam:")
        print("- Check the official game website")
        print("- Look for press kits or media assets")
        print("- Ensure you have rights to use the image")
        print()
    else:
        print("✓ All games have icons!")
    
    return len(missing_icons)

if __name__ == "__main__":
    missing_count = check_missing_icons()
    sys.exit(0 if missing_count == 0 else 1)
