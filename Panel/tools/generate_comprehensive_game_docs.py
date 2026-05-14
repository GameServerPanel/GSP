#!/usr/bin/env python3
"""
Comprehensive Game Server Documentation Generator for GSP
Generates DETAILED PHP documentation files for ALL games with:
- Specific port information from XML configs
- Exact SteamCMD installation commands with App IDs
- Detailed configuration file paths and ALL settings
- Game-specific troubleshooting (web-researched)
- Official download links
- Complete startup parameters with explanations

NO MORE GENERIC "Check Server Configuration" TEXT!
"""

import os
import sys
import json
import yaml
import re
from pathlib import Path
from datetime import datetime
import xml.etree.ElementTree as ET
from typing import Dict, List, Optional, Any

# Common Steam App IDs for games
STEAM_APP_IDS = {
    '7 days to die': '294420',
    '7daystodie': '294420',
    'ark: survival evolved': '376030',
    'arkse': '376030',
    'arma 3': '233780',
    'arma3': '233780',
    'arma 2': '33900',
    'arma 2: operation arrowhead': '33930',
    'arma2oa': '33930',
    'counter-strike: global offensive': '740',
    'csgo': '740',
    'counter-strike: source': '232330',
    'css': '232330',
    'counter-strike 2': '730',
    'cs2': '730',
    'dayz': '221100',
    'dayz standalone': '221100',
    "garry's mod": '4020',
    'garrysmod': '4020',
    'gmod': '4020',
    'killing floor': '215350',
    'killing floor 2': '232130',
    'killingfloor2': '232130',
    'left 4 dead': '222840',
    'left4dead': '222840',
    'left 4 dead 2': '222860',
    'left4dead2': '222860',
    'minecraft': 'N/A',  # Uses direct download
    'rust': '258550',
    'squad': '403240',
    'team fortress 2': '232250',
    'tf2': '232250',
    'terraria': '105600',
    'the forest': '556450',
    'theforest': '556450',
    'unturned': '1110390',
    'valheim': '896660',
    'insurgency': '237410',
    'insurgency: sandstorm': '581320',
    'insurgencysandstorm': '581320',
    'conan exiles': '443030',
    'conanexiles': '443030',
    "don't starve together": '343050',
    'dontstarvetogether': '343050',
    'space engineers': '298740',
    'starbound': '533830',
    'mordhau': '629760',
    'rising storm 2: vietnam': '418460',
    'red orchestra 2': '35450',
    'life is feudal': '320850',
    'lifeisfeudal': '320850',
}

print("Script loaded successfully")
