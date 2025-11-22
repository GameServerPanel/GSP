#!/usr/bin/env python3
"""
Comprehensive Game Server Documentation Generator for GSP
Generates PHP documentation files for all games in the "todo" category
Based on the Minecraft template structure
"""

import os
import sys
import json
import yaml
import re
from pathlib import Path
from datetime import datetime
import xml.etree.ElementTree as ET

class GameDocGenerator:
    def __init__(self, docs_dir, config_dir, knowledgepack_path):
        self.docs_dir = Path(docs_dir)
        self.config_dir = Path(config_dir)
        self.knowledgepack_path = Path(knowledgepack_path)
        self.knowledgepack_data = None
        self.xml_configs = {}
        
    def load_knowledgepack(self):
        """Load the YAML knowledgepack with game information"""
        try:
            with open(self.knowledgepack_path, 'r', encoding='utf-8') as f:
                data = yaml.safe_load(f)
                self.knowledgepack_data = data.get('games', [])
                print(f"Loaded knowledgepack with {len(self.knowledgepack_data)} games")
                return True
        except Exception as e:
            print(f"Error loading knowledgepack: {e}")
            return False
    
    def load_xml_configs(self):
        """Load all XML configuration files"""
        xml_files = list(self.config_dir.glob("*.xml"))
        for xml_file in xml_files:
            try:
                tree = ET.parse(xml_file)
                root = tree.getroot()
                game_key = root.find('game_key')
                if game_key is not None and game_key.text:
                    self.xml_configs[game_key.text] = {
                        'file': xml_file.name,
                        'tree': root
                    }
            except Exception as e:
                print(f"Error parsing {xml_file}: {e}")
        print(f"Loaded {len(self.xml_configs)} XML configurations")
    
    def get_game_info_from_knowledgepack(self, game_name):
        """Find game info in knowledgepack by name"""
        if not self.knowledgepack_data:
            return None
        
        # Try exact match first
        for game in self.knowledgepack_data:
            if game.get('name', '').lower() == game_name.lower():
                return game
        
        # Try partial match
        for game in self.knowledgepack_data:
            if game_name.lower() in game.get('name', '').lower():
                return game
        
        return None
    
    def get_xml_config(self, folder_name):
        """Find matching XML config for a folder"""
        # Try exact match
        for key, config in self.xml_configs.items():
            if key.lower() == folder_name.lower() or key.lower().replace('_', '') == folder_name.lower():
                return config['tree']
        
        # Try partial match
        for key, config in self.xml_configs.items():
            if folder_name.lower() in key.lower() or key.lower() in folder_name.lower():
                return config['tree']
        
        return None
    
    def extract_ports_from_xml(self, xml_root):
        """Extract port information from XML config"""
        ports = []
        
        # Look for replace_texts with port keys
        replace_texts = xml_root.find('replace_texts')
        if replace_texts is not None:
            for text in replace_texts.findall('text'):
                key = text.get('key', '')
                if 'port' in key.lower():
                    filepath = text.find('filepath')
                    if filepath is not None:
                        ports.append({
                            'key': key,
                            'file': filepath.text
                        })
        
        # Look for custom_fields with port information
        custom_fields = xml_root.find('custom_fields')
        if custom_fields is not None:
            for field in custom_fields.findall('field'):
                key = field.get('key', '')
                if 'port' in key.lower():
                    default_value = field.find('default_value')
                    desc = field.find('desc')
                    ports.append({
                        'key': key,
                        'default': default_value.text if default_value is not None else None,
                        'description': desc.text if desc is not None else None
                    })
        
        return ports
    
    def extract_config_files_from_xml(self, xml_root):
        """Extract configuration file paths from XML"""
        config_files = []
        
        config_files_elem = xml_root.find('configuration_files')
        if config_files_elem is not None:
            for file_elem in config_files_elem.findall('file'):
                desc = file_elem.get('description', 'Configuration file')
                path = file_elem.text if file_elem.text else ''
                config_files.append({
                    'description': desc,
                    'path': path
                })
        
        return config_files
    
    def generate_php_doc(self, folder_name, metadata):
        """Generate comprehensive PHP documentation for a game"""
        game_name = metadata.get('name', folder_name.replace('_', ' ').title())
        
        # Get additional data
        kb_info = self.get_game_info_from_knowledgepack(game_name)
        xml_config = self.get_xml_config(folder_name)
        
        # Extract ports and configs
        ports_info = []
        config_files = []
        if xml_config is not None:
            ports_info = self.extract_ports_from_xml(xml_config)
            config_files = self.extract_config_files_from_xml(xml_config)
        
        # Build the PHP document
        php_content = self.build_php_content(game_name, folder_name, kb_info, xml_config, ports_info, config_files)
        
        return php_content
    
    def build_php_content(self, game_name, folder_name, kb_info, xml_config, ports_info, config_files):
        """Build the complete PHP documentation content"""
        
        # Extract data from various sources
        default_port = "Check server configuration"
        protocol = "TCP/UDP"
        min_ram = "1GB"
        engine = "Various"
        startup_cmd = ""
        
        if kb_info:
            network = kb_info.get('network', {})
            default_ports = network.get('default_ports', [])
            if default_ports:
                port_info = default_ports[0]
                port_str = port_info.get('port', '')
                if '/' in port_str:
                    default_port = port_str.split('/')[0]
                    protocol = port_str.split('/')[1].upper()
                else:
                    default_port = port_str
            
            requirements = kb_info.get('requirements', {})
            min_ram = requirements.get('ram', '1GB')
            engine = kb_info.get('engine', 'Various')
            
            startup = kb_info.get('typical_startup', {})
            startup_cmd = startup.get('linux', '') or startup.get('windows', '')
        
        php_doc = '''<?php
/**
 * ''' + game_name + ''' Server Documentation - Comprehensive Guide
 * General game server hosting information (not platform-specific)
 */
?>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">📚 Quick Navigation</h3>
    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
        <a href="#quick-info" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Quick Info</a>
        <a href="#ports" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">🔌 Ports</a>
        <a href="#installation" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Installation</a>
        <a href="#configuration" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Configuration</a>
        <a href="#parameters" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">⚙️ Startup Parameters</a>
        <a href="#troubleshooting" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">🔧 Troubleshooting</a>
        <a href="#performance" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Performance</a>
        <a href="#security" style="background: #0f172a; padding: 8px 16px; border-radius: 4px; color: #a5b4fc; text-decoration: none;">Security</a>
    </div>
</div>

<h1>''' + game_name + ''' Server Hosting Guide</h1>

<h2>Overview</h2>
<p>''' + game_name + ''' is a multiplayer game server that can be hosted on a VPS or dedicated server. This comprehensive guide covers everything you need to know about hosting a ''' + game_name + ''' server for your community.</p>

<h2 id="quick-info">Quick Info</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <ul style="color: #e5e7eb; line-height: 1.8; margin: 0;">
        <li><strong style="color: #ffffff;">Default Port:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">''' + default_port + '''</code></li>
        <li><strong style="color: #ffffff;">Protocol:</strong> ''' + protocol + '''</li>
        <li><strong style="color: #ffffff;">Minimum RAM:</strong> ''' + min_ram + '''</li>
        <li><strong style="color: #ffffff;">Engine:</strong> ''' + engine + '''</li>
        <li><strong style="color: #ffffff;">Recommended OS:</strong> Linux (Ubuntu/Debian) or Windows Server</li>
'''

        # Add config files if available
        if config_files:
            php_doc += '        <li><strong style="color: #ffffff;">Configuration Files:</strong><ul style="margin-top: 8px;">\n'
            for cf in config_files:
                php_doc += f'            <li><code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">{cf["path"]}</code> - {cf["description"]}</li>\n'
            php_doc += '        </ul></li>\n'
        
        php_doc += '''    </ul>
</div>

<h2 id="ports">🔌 Network Ports</h2>
<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Required Ports</h3>
'''

        # Add port information
        if kb_info and kb_info.get('network', {}).get('default_ports'):
            php_doc += '''    <table style="width: 100%; color: #e5e7eb; border-collapse: collapse;">
        <thead>
            <tr style="background: #0f172a;">
                <th style="padding: 10px; text-align: left; color: #ffffff;">Port</th>
                <th style="padding: 10px; text-align: left; color: #ffffff;">Protocol</th>
                <th style="padding: 10px; text-align: left; color: #ffffff;">Purpose</th>
            </tr>
        </thead>
        <tbody>
'''
            for port_info in kb_info['network']['default_ports']:
                port_str = port_info.get('port', '')
                purpose = port_info.get('purpose', 'Game server')
                port_num = port_str.split('/')[0] if '/' in port_str else port_str
                proto = port_str.split('/')[1].upper() if '/' in port_str else 'TCP/UDP'
                
                php_doc += f'''            <tr style="border-bottom: 1px solid #374151;">
                <td style="padding: 10px;"><code style="background: #0f172a; padding: 2px 6px; border-radius: 3px;">{port_num}</code></td>
                <td style="padding: 10px;">{proto}</td>
                <td style="padding: 10px;">{purpose}</td>
            </tr>
'''
            
            # Add additional ports if available
            additional_ports = kb_info['network'].get('additional_ports', [])
            for port_info in additional_ports:
                port_str = port_info.get('port', '')
                purpose = port_info.get('purpose', 'Additional functionality')
                port_num = port_str.split('/')[0] if '/' in port_str else port_str
                proto = port_str.split('/')[1].upper() if '/' in port_str else 'TCP/UDP'
                
                php_doc += f'''            <tr style="border-bottom: 1px solid #374151;">
                <td style="padding: 10px;"><code style="background: #0f172a; padding: 2px 6px; border-radius: 3px;">{port_num}</code></td>
                <td style="padding: 10px;">{proto}</td>
                <td style="padding: 10px;">{purpose} <span style="color: #f59e0b;">(Optional)</span></td>
            </tr>
'''
            
            php_doc += '''        </tbody>
    </table>
'''
        else:
            php_doc += '''    <p style="color: #e5e7eb;">The ''' + game_name + ''' server typically uses a configurable port. Check your server configuration files for the specific port settings.</p>
'''
        
        php_doc += '''    
    <h3 style="color: #ffffff; margin-top: 20px;">Firewall Configuration</h3>
    <p style="color: #e5e7eb;">Allow server ports through your firewall:</p>
    <pre><code style="color: #a5b4fc;"># UFW (Ubuntu/Debian)
sudo ufw allow [PORT]/tcp
sudo ufw allow [PORT]/udp
sudo ufw reload

# FirewallD (CentOS/RHEL)
sudo firewall-cmd --permanent --add-port=[PORT]/tcp
sudo firewall-cmd --permanent --add-port=[PORT]/udp
sudo firewall-cmd --reload

# Windows Firewall
netsh advfirewall firewall add rule name="''' + game_name + ''' Server" dir=in action=allow protocol=TCP localport=[PORT]
netsh advfirewall firewall add rule name="''' + game_name + ''' Server" dir=in action=allow protocol=UDP localport=[PORT]
</code></pre>

    <h3 style="color: #ffffff; margin-top: 20px;">⚠️ Port Security Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Only open ports that are necessary for the game server to function</li>
        <li>Consider using non-standard ports to reduce automated attacks</li>
        <li>If using cloud hosting, configure security groups properly</li>
        <li>Monitor connection attempts and unusual traffic patterns</li>
    </ul>
</div>

<h2 id="installation">Installation & Setup</h2>

<h3>System Requirements</h3>
<ul>
    <li><strong>OS:</strong> Linux (Ubuntu 20.04+ or Debian 11+ recommended) or Windows Server 2019+</li>
    <li><strong>CPU:</strong> 2+ cores recommended (single-threaded performance important for most game servers)</li>
    <li><strong>RAM:</strong> ''' + min_ram + ''' minimum (more for larger player counts)</li>
    <li><strong>Storage:</strong> 5GB+ for server files (SSD recommended for better performance)</li>
    <li><strong>Network:</strong> Stable internet connection with low latency</li>
</ul>
'''

        # Add dependencies if available
        if kb_info and kb_info.get('requirements', {}).get('dependencies'):
            dependencies = kb_info['requirements']['dependencies']
            php_doc += '''
<h3>Required Dependencies</h3>
<ul>
'''
            for dep in dependencies:
                php_doc += f'    <li>{dep}</li>\n'
            php_doc += '''</ul>
'''

        php_doc += '''
<h3>Installation Steps</h3>

<h4>Linux (Ubuntu/Debian)</h4>
<pre><code># Update system packages
sudo apt update && sudo apt upgrade -y

# Create server directory
mkdir -p ~/gameserver
cd ~/gameserver

# Download server files (method varies by game)
# Check official documentation for download links
</code></pre>
'''

        if startup_cmd:
            php_doc += f'''
<h4>Starting the Server</h4>
<pre><code>{startup_cmd}
</code></pre>
'''

        php_doc += '''
<h4>Windows Server</h4>
<p>Download the server files from the official game website or through Steam (if applicable). Extract to a dedicated folder and run the server executable.</p>

<h3>Using SteamCMD (if applicable)</h3>
<p>Many game servers can be installed via SteamCMD:</p>
<pre><code># Install SteamCMD (Ubuntu/Debian)
sudo apt install lib32gcc-s1 steamcmd

# Run SteamCMD
steamcmd

# Login and download (use your Steam credentials or anonymous)
login anonymous
force_install_dir /path/to/server
app_update [APP_ID] validate
quit
</code></pre>

<h2 id="configuration">Server Configuration</h2>

<p>After installation, configure your server through the configuration files typically located in the server directory.</p>

<h3>Essential Settings</h3>
<ul>
    <li><strong>Server Name:</strong> Set a descriptive name for your server</li>
    <li><strong>Max Players:</strong> Configure based on your server's resources</li>
    <li><strong>Password:</strong> Optional password protection for private servers</li>
    <li><strong>Admin/RCON Password:</strong> Set a strong password for remote administration</li>
    <li><strong>Game Mode:</strong> Configure game-specific modes and settings</li>
</ul>
'''

        # Add config files section if available
        if config_files:
            php_doc += '''
<h3>Configuration Files</h3>
<p>Important configuration files for this server:</p>
<ul>
'''
            for cf in config_files:
                php_doc += f'    <li><strong><code>{cf["path"]}</code></strong> - {cf["description"]}</li>\n'
            php_doc += '''</ul>
'''

        php_doc += '''
<h3>Server Commands</h3>
<p>Common administrative commands (access via console or RCON):</p>
<pre><code># Kick player
kick [player_name]

# Ban player
ban [player_name]

# Change map/level (syntax varies by game)
changelevel [map_name]

# Set admin password (if supported)
setadminpassword [password]
</code></pre>

<h2 id="parameters">⚙️ Startup Parameters</h2>

<h3>Basic Startup</h3>
'''

        if startup_cmd:
            php_doc += f'''<pre><code>{startup_cmd}
</code></pre>
'''
        else:
            php_doc += '''<pre><code># Generic startup command structure
./server_executable [parameters]
</code></pre>
'''

        php_doc += '''
<h3>Common Parameters</h3>
<ul>
    <li><code>-port [number]</code> - Set the server port</li>
    <li><code>-maxplayers [number]</code> - Maximum player slots</li>
    <li><code>-map [name]</code> - Starting map/level</li>
    <li><code>-console</code> - Enable console output</li>
    <li><code>-nographics</code> - Run without graphics (headless mode)</li>
</ul>

<h3>Creating a Start Script</h3>

<p><strong>Linux (start.sh):</strong></p>
<pre><code>#!/bin/bash
cd /path/to/server
./server_executable [parameters] 2>&1 | tee server.log
</code></pre>
<pre><code>chmod +x start.sh
./start.sh
</code></pre>

<p><strong>Windows (start.bat):</strong></p>
<pre><code>@echo off
cd /d "%~dp0"
server_executable.exe [parameters]
pause
</code></pre>

<h3>Running as a Service</h3>

<p><strong>Linux (systemd):</strong></p>
<pre><code># Create service file: /etc/systemd/system/gameserver.service
[Unit]
Description=''' + game_name + ''' Server
After=network.target

[Service]
Type=simple
User=gameserver
WorkingDirectory=/home/gameserver/server
ExecStart=/home/gameserver/server/start.sh
Restart=on-failure
RestartSec=10

[Install]
WantedBy=multi-user.target
</code></pre>

<pre><code># Enable and start service
sudo systemctl daemon-reload
sudo systemctl enable gameserver
sudo systemctl start gameserver
sudo systemctl status gameserver
</code></pre>

<h2 id="troubleshooting">🔧 Troubleshooting</h2>

<h3>Server Won't Start</h3>
'''

        # Add troubleshooting from knowledgepack if available
        if kb_info and kb_info.get('troubleshooting', {}).get('common_issues'):
            issues = kb_info['troubleshooting']['common_issues']
            for issue_item in issues:
                issue = issue_item.get('issue', '')
                fix = issue_item.get('fix', '')
                php_doc += f'''
<h4>{issue}</h4>
<p>{fix}</p>
'''
        else:
            php_doc += '''
<h4>Check Server Logs</h4>
<pre><code># View recent log entries
tail -f server.log

# Or check system logs
journalctl -u gameserver -f
</code></pre>

<h4>Port Already in Use</h4>
<pre><code># Find what's using the port
sudo lsof -i :[PORT]
sudo netstat -tulpn | grep [PORT]

# Kill the process or change server port
</code></pre>

<h4>Missing Dependencies</h4>
<p>Ensure all required dependencies are installed. Check the error messages for missing libraries or packages.</p>
'''

        php_doc += '''
<h3>Connection Issues</h3>

<h4>Can't Connect to Server</h4>
<ol>
    <li><strong>Verify server is running:</strong> <code>ps aux | grep server</code></li>
    <li><strong>Check port is listening:</strong> <code>netstat -an | grep [PORT]</code></li>
    <li><strong>Verify firewall rules</strong> (see Ports section above)</li>
    <li><strong>Check server IP:</strong> Use external IP, not localhost</li>
    <li><strong>Router/NAT:</strong> Ensure port forwarding is configured</li>
</ol>

<h4>High Latency/Lag</h4>
<ul>
    <li>Check server resource usage (CPU, RAM, disk I/O)</li>
    <li>Verify network bandwidth is adequate</li>
    <li>Consider server location relative to players</li>
    <li>Check for background processes consuming resources</li>
</ul>

<h3>Performance Issues</h3>

<h4>Server Lag</h4>
<ol>
    <li><strong>Monitor resources:</strong> Use <code>htop</code> or <code>top</code></li>
    <li><strong>Check disk I/O:</strong> Use <code>iotop</code></li>
    <li><strong>Review server logs</strong> for errors or warnings</li>
    <li><strong>Reduce player count</strong> or increase server resources</li>
    <li><strong>Optimize configuration</strong> based on server capacity</li>
</ol>

<h4>Memory Leaks</h4>
<pre><code># Monitor memory usage
free -h
top -p $(pgrep -f server)

# Restart server regularly via cron if needed
0 4 * * * /home/gameserver/restart.sh
</code></pre>

<h2 id="performance">Performance Optimization</h2>

<h3>Server Tuning</h3>
<ul>
    <li><strong>CPU:</strong> Ensure adequate CPU allocation; most game servers are single-threaded</li>
    <li><strong>RAM:</strong> Allocate sufficient memory; monitor usage and adjust as needed</li>
    <li><strong>Disk:</strong> Use SSD storage for better I/O performance</li>
    <li><strong>Network:</strong> Ensure stable, low-latency connection</li>
</ul>

<h3>Operating System Optimization</h3>
<pre><code># Increase file descriptor limits
echo "* soft nofile 65536" >> /etc/security/limits.conf
echo "* hard nofile 65536" >> /etc/security/limits.conf

# Network tuning
sysctl -w net.core.rmem_max=16777216
sysctl -w net.core.wmem_max=16777216
sysctl -w net.ipv4.tcp_rmem="4096 87380 16777216"
sysctl -w net.ipv4.tcp_wmem="4096 87380 16777216"
</code></pre>

<h3>Monitoring</h3>
<p>Set up monitoring to track server health:</p>
<ul>
    <li>CPU and memory usage</li>
    <li>Network traffic and latency</li>
    <li>Player count and activity</li>
    <li>Error rates and crash logs</li>
</ul>

<h3>Backup Strategy</h3>
<pre><code>#!/bin/bash
# backup.sh - Run via cron
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/gameserver"
SERVER_DIR="/home/gameserver/server"

# Create backup
tar -czf $BACKUP_DIR/backup_$DATE.tar.gz -C $SERVER_DIR .

# Keep only last 7 days
find $BACKUP_DIR -name "backup_*.tar.gz" -mtime +7 -delete
</code></pre>

<h2 id="security">Security Best Practices</h2>

<h3>Firewall Configuration</h3>
<pre><code># Minimal firewall - only allow necessary ports
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow [SERVER_PORT]/tcp
sudo ufw allow [SERVER_PORT]/udp
sudo ufw allow 22/tcp  # SSH
sudo ufw enable
</code></pre>

<h3>Strong Passwords</h3>
<ul>
    <li>Use strong, unique passwords for admin/RCON access</li>
    <li>Never use default passwords</li>
    <li>Change passwords regularly</li>
    <li>Don't share admin credentials unnecessarily</li>
</ul>

<h3>Regular Updates</h3>
<ul>
    <li>Keep server software updated to the latest stable version</li>
    <li>Update operating system and dependencies regularly</li>
    <li>Subscribe to security advisories for your game</li>
    <li>Test updates on a staging server before production deployment</li>
</ul>

<h3>Access Control</h3>
<ul>
    <li>Limit SSH access to specific IPs if possible</li>
    <li>Use SSH keys instead of passwords</li>
    <li>Disable root login via SSH</li>
    <li>Implement fail2ban or similar intrusion prevention</li>
</ul>

<h3>DDoS Protection</h3>
<ul>
    <li>Consider DDoS protection services (Cloudflare, OVH, etc.)</li>
    <li>Implement rate limiting where supported</li>
    <li>Monitor for unusual traffic patterns</li>
    <li>Have an incident response plan</li>
</ul>

<h2>Additional Resources</h2>
<ul>
    <li>Official ''' + game_name + ''' documentation and forums</li>
    <li>Community wikis and guides</li>
    <li>Game-specific Discord or Reddit communities</li>
    <li>Server hosting provider documentation</li>
</ul>
'''

        # Add references from knowledgepack if available
        if kb_info and kb_info.get('references'):
            php_doc += '''
<h3>External References</h3>
<ul>
'''
            for ref in kb_info['references']:
                php_doc += f'    <li><a href="{ref}" target="_blank">{ref}</a></li>\n'
            php_doc += '''</ul>
'''

        php_doc += '''
<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8; margin: 0;">
        <li>Always make backups before making configuration changes</li>
        <li>Keep your server and dependencies updated</li>
        <li>Monitor server resources and player activity</li>
        <li>Follow the game's End User License Agreement (EULA) and Terms of Service</li>
        <li>Join community forums for support and best practices</li>
    </ul>
</div>

<p style="text-align: center; margin-top: 30px; color: #666;">
    <em>Last updated: ''' + datetime.now().strftime("%B %Y") + ''' | For ''' + game_name + ''' server hosting</em>
</p>
'''

        return php_doc
    
    def process_todo_folders(self):
        """Process all folders with category 'todo' """
        processed = 0
        errors = []
        
        # Find all todo folders
        for folder in self.docs_dir.iterdir():
            if not folder.is_dir():
                continue
            
            metadata_file = folder / 'metadata.json'
            index_file = folder / 'index.php'
            
            if not metadata_file.exists():
                continue
            
            try:
                # Read metadata
                with open(metadata_file, 'r', encoding='utf-8') as f:
                    content = f.read()
                    # Remove BOM if present
                    content = content.lstrip('\ufeff')
                    metadata = json.loads(content)
                
                # Check if it's a todo category
                if metadata.get('category', '').lower() != 'todo':
                    continue
                
                print(f"Processing: {folder.name}")
                
                # Generate new documentation
                php_content = self.generate_php_doc(folder.name, metadata)
                
                # Write the new index.php
                with open(index_file, 'w', encoding='utf-8') as f:
                    f.write(php_content)
                
                # Update metadata category from 'todo' to 'game'
                metadata['category'] = 'game'
                with open(metadata_file, 'w', encoding='utf-8') as f:
                    json.dump(metadata, f, indent=4, ensure_ascii=False)
                
                processed += 1
                print(f"  ✓ Generated documentation for {folder.name}")
                
            except Exception as e:
                error_msg = f"Error processing {folder.name}: {e}"
                print(f"  ✗ {error_msg}")
                errors.append(error_msg)
        
        return processed, errors

def main():
    docs_dir = "/home/runner/work/GSP/GSP/modules/billing/docs"
    config_dir = "/home/runner/work/GSP/GSP/modules/config_games/server_configs"
    knowledgepack = "/home/runner/work/GSP/GSP/modules/billing/docs/gameserver_knowledgepack_v2.yaml"
    
    generator = GameDocGenerator(docs_dir, config_dir, knowledgepack)
    
    print("Loading data sources...")
    generator.load_knowledgepack()
    generator.load_xml_configs()
    
    print("\nProcessing TODO folders...")
    processed, errors = generator.process_todo_folders()
    
    print(f"\n{'='*60}")
    print(f"Documentation generation complete!")
    print(f"  Total processed: {processed}")
    print(f"  Errors: {len(errors)}")
    
    if errors:
        print("\nErrors encountered:")
        for error in errors[:10]:  # Show first 10 errors
            print(f"  - {error}")
    
    return 0 if not errors else 1

if __name__ == "__main__":
    sys.exit(main())
