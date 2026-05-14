#!/usr/bin/env python3
"""
FAQ RSS Generator
Generates FAQ.RSS from YAML game documentation files.
Produces clean HTML-escaped content without CDATA, using <br> for line breaks.
"""

import os
import sys
import yaml
import html
import xml.etree.ElementTree as ET
from datetime import datetime
import shutil
from pathlib import Path

class FAQRSSGenerator:
    def __init__(self, data_dir="data/games", output_file="FAQ.RSS"):
        self.data_dir = Path(data_dir)
        self.output_file = Path(output_file)
        self.games = []
        
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
                        self.games.append(game_data)
                        print(f"Loaded: {game_data['name']}")
            except Exception as e:
                print(f"Error loading {yaml_file}: {e}")
                
        return True
        
    def validate_game_data(self, data, filename):
        """Validate game YAML data structure"""
        required_fields = ['name', 'supports_workshop', 'startup', 'configs', 'troubleshooting']
        
        for field in required_fields:
            if field not in data:
                print(f"Error in {filename}: Missing required field '{field}'")
                return False
                
        # Validate startup section
        startup = data['startup']
        if 'default_command' not in startup:
            print(f"Error in {filename}: Missing 'default_command' in startup section")
            return False
            
        # Validate workshop section if supported
        if data['supports_workshop'] and 'workshop' not in data:
            print(f"Error in {filename}: supports_workshop is true but 'workshop' section missing")
            return False
            
        return True
        
    def escape_html_content(self, text):
        """Escape HTML content and convert newlines to <br> tags"""
        if not text:
            return ""
        # Escape HTML entities
        escaped = html.escape(str(text))
        # Convert newlines to <br> tags  
        escaped = escaped.replace('\n', '&lt;br&gt;')
        return escaped
        
    def generate_config_files_content(self, game):
        """Generate Config Files section content"""
        content = "&lt;strong&gt;Configuration Files&lt;/strong&gt;&lt;br&gt;"
        
        for config in game['configs']:
            file_name = config['file']
            paths = config.get('paths', [])
            desc = config.get('desc', '')
            
            content += "&lt;br&gt;- " + self.escape_html_content(file_name)
            if paths:
                path_str = ", ".join(paths)
                content += " — " + self.escape_html_content(desc) + ". Paths: " + self.escape_html_content(path_str)
            elif desc:
                content += " — " + self.escape_html_content(desc)
            content += "&lt;br&gt;"
            
        return content
        
    def generate_startup_parameters_content(self, game):
        """Generate Startup Parameters section content"""
        startup = game['startup']
        content = "&lt;strong&gt;Default Command Line&lt;/strong&gt;&lt;br&gt;"
        content += "&lt;br&gt;" + self.escape_html_content(startup['default_command']) + "&lt;br&gt;"
        
        # Port scheme
        if 'ports' in startup and startup['ports']:
            content += "&lt;br&gt;&lt;strong&gt;Port Scheme&lt;/strong&gt;&lt;br&gt;"
            for port in startup['ports']:
                label = port.get('label', '')
                port_num = port.get('port', '')
                proto = port.get('proto', '')
                relative = port.get('relative', '')
                content += "&lt;br&gt;- " + self.escape_html_content(f"{label} ({proto}) — {relative} (default {port_num})") + "&lt;br&gt;"
            
        # Command line flags
        if 'flags' in startup and startup['flags']:
            content += "&lt;br&gt;&lt;strong&gt;Command Line Flags&lt;/strong&gt;&lt;br&gt;"
            for flag in startup['flags']:
                flag_name = flag.get('flag', '')
                default = flag.get('default', '')
                flag_type = flag.get('type', '')
                desc = flag.get('desc', '')
                content += "&lt;br&gt;" + self.escape_html_content(f"{flag_name} — {desc}. Type: {flag_type}, Default: {default}") + "&lt;br&gt;"
                
        return content
        
    def generate_troubleshooting_content(self, game):
        """Generate Troubleshooting section content"""
        content = "&lt;strong&gt;Common Issues and Solutions&lt;/strong&gt;&lt;br&gt;"
        
        for issue in game['troubleshooting']:
            content += "&lt;br&gt;- " + self.escape_html_content(issue) + "&lt;br&gt;"
            
        return content
        
    def generate_workshop_content(self, game):
        """Generate Steam Workshop section content"""
        if not game.get('supports_workshop', False):
            return None
            
        workshop = game.get('workshop', {})
        content = "&lt;strong&gt;Steam Workshop Configuration&lt;/strong&gt;&lt;br&gt;"
        
        notes = workshop.get('notes', [])
        for note in notes:
            content += "&lt;br&gt;- " + self.escape_html_content(note) + "&lt;br&gt;"
            
        return content
        
    def create_rss_item(self, title, category, content):
        """Create RSS item element"""
        item = ET.Element('item')
        
        title_elem = ET.SubElement(item, 'title')
        title_elem.text = title
        
        category_elem = ET.SubElement(item, 'category')
        category_elem.text = category
        
        # Handle namespaced element properly
        content_elem = ET.SubElement(item, '{http://purl.org/rss/1.0/modules/content/}encoded')
        content_elem.text = content
        
        return item
        
    def generate_rss(self):
        """Generate the complete RSS file"""
        # Create backup if file exists
        if self.output_file.exists():
            backup_file = self.output_file.with_suffix('.bak')
            shutil.copy2(self.output_file, backup_file)
            print(f"Created backup: {backup_file}")
            
        # Create RSS root
        rss = ET.Element('rss', version='2.0')
        rss.set('xmlns:content', 'http://purl.org/rss/1.0/modules/content/')
        rss.set('xmlns:dc', 'http://purl.org/dc/elements/1.1/')
        
        channel = ET.SubElement(rss, 'channel')
        
        # Channel metadata
        title = ET.SubElement(channel, 'title')
        title.text = 'Game Server FAQ'
        
        link = ET.SubElement(channel, 'link')
        link.text = 'https://gameservers.world/faq'
        
        description = ET.SubElement(channel, 'description')
        description.text = 'Comprehensive game server configuration and troubleshooting guide'
        
        language = ET.SubElement(channel, 'dc:language')
        language.text = 'en'
        
        pubdate = ET.SubElement(channel, 'pubDate')
        # Fix datetime deprecation warning
        pubdate.text = datetime.now().strftime('%a, %d %b %Y %H:%M:%S GMT')
        
        # Sort games alphabetically by name, then items by section order
        sorted_games = sorted(self.games, key=lambda x: x['name'])
        
        # Define section order
        section_order = ['Config Files', 'Startup Parameters', 'Troubleshooting', 'Steam Workshop']
        
        # Generate items for each game
        all_items = []
        for game in sorted_games:
            game_name = game['name']
            
            # Required sections in order
            sections = [
                ('Config Files', self.generate_config_files_content(game)),
                ('Startup Parameters', self.generate_startup_parameters_content(game)),
                ('Troubleshooting', self.generate_troubleshooting_content(game))
            ]
            
            # Optional workshop section
            workshop_content = self.generate_workshop_content(game)
            if workshop_content:
                sections.append(('Steam Workshop', workshop_content))
                
            # Create RSS items
            for title, content in sections:
                item = self.create_rss_item(title, game_name, content)
                all_items.append((game_name, title, item))
                
        # Sort all items by category (game name) then by section order
        all_items.sort(key=lambda x: (x[0], section_order.index(x[1]) if x[1] in section_order else 999))
                
        # Add all items to channel (no longer needed since we're writing manually)
        # for game_name, title, item in all_items:
        #     channel.append(item)
            
        # Write RSS file manually to avoid namespace issues
        with open(self.output_file, 'w', encoding='utf-8') as f:
            f.write('<?xml version="1.0" encoding="utf-8" ?>\n')
            f.write('<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/">\n')
            f.write('  <channel>\n')
            f.write('    <title>Game Server FAQ</title>\n')
            f.write('    <link>https://gameservers.world/faq</link>\n')
            f.write('    <description>Comprehensive game server configuration and troubleshooting guide</description>\n')
            f.write('    <dc:language>en</dc:language>\n')
            f.write(f'    <pubDate>{datetime.now().strftime("%a, %d %b %Y %H:%M:%S GMT")}</pubDate>\n')
            
            # Write all items
            for game_name, title, item in all_items:
                title_text = item.find('title').text
                category_text = item.find('category').text
                content_elem = item.find('{http://purl.org/rss/1.0/modules/content/}encoded')
                content = content_elem.text if content_elem is not None else ""
                
                f.write('    <item>\n')
                f.write(f'      <title>{html.escape(title_text)}</title>\n')
                f.write(f'      <category>{html.escape(category_text)}</category>\n')
                f.write(f'      <content:encoded>{content}</content:encoded>\n')
                f.write('    </item>\n')
                
            f.write('  </channel>\n')
            f.write('</rss>\n')
            
        print(f"Generated RSS with {len(all_items)} items for {len(sorted_games)} games")
        print(f"Output: {self.output_file}")
        
    def run(self):
        """Main execution method"""
        print("FAQ RSS Generator")
        print("================")
        
        if not self.load_games():
            return False
            
        if not self.games:
            print("No valid games found. RSS file will be empty.")
            
        self.generate_rss()
        return True

def main():
    if len(sys.argv) > 1:
        data_dir = sys.argv[1]
    else:
        data_dir = "data/games"
        
    if len(sys.argv) > 2:
        output_file = sys.argv[2]
    else:
        output_file = "FAQ.RSS"
        
    generator = FAQRSSGenerator(data_dir, output_file)
    success = generator.run()
    sys.exit(0 if success else 1)

if __name__ == "__main__":
    main()