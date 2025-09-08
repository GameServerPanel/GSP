# Open Game Panel (OGP) ControlPanel
Open Game Panel is a PHP-based web application for managing game servers with Linux/Windows agents and Python documentation tools.

Always reference these instructions first and fallback to search or bash commands only when you encounter unexpected information that does not match the info here.

## Working Effectively
- Bootstrap and test the repository:
  - `sudo apt-get update && sudo apt-get install -y php mysql-server libxml-parser-perl libpath-class-perl libarchive-extract-perl libdbi-perl libdbd-mysql-perl libxml-simpleobject-perl libproc-daemon-perl liblinux-inotify2-perl libarchive-zip-perl pandoc texlive-xetex python3 python3-yaml`
  - `sudo service mysql start`
  - `sudo mysql --defaults-file=/etc/mysql/debian.cnf -e "CREATE DATABASE IF NOT EXISTS panel; CREATE USER IF NOT EXISTS 'localuser'@'localhost' IDENTIFIED BY 'testpass'; GRANT ALL PRIVILEGES ON panel.* TO 'localuser'@'localhost'; FLUSH PRIVILEGES;"`
  - Edit `/home/runner/work/ControlPanel/ControlPanel/includes/config.inc.php` to set database password to `testpass`
- Test Python guide generation tools:
  - `cd /home/runner/work/ControlPanel/ControlPanel && ./tools/generate_all_guides.sh` -- takes 3-10 seconds. NEVER CANCEL. Set timeout to 30+ seconds.
  - `python3 tools/validate_guides.py` -- takes under 1 second
- Test PHP web application:
  - `cd /home/runner/work/ControlPanel/ControlPanel && php -S localhost:8080 &` 
  - `curl -I http://localhost:8080` (will show 500 error due to missing database schema - this is expected)
  - `pkill -f "php -S"` to stop server
- Test Perl agent syntax:
  - `cd /home/runner/work/ControlPanel/ControlPanel/_agent-linux && perl -c ogp_agent.pl` -- should show "syntax OK"

## Validation
- Always test the Python guide generation workflow when making changes to tools/ directory
- ALWAYS run `./tools/generate_all_guides.sh` to validate guide generation changes  
- Test PHP syntax with `php -l <file.php>` for any PHP file changes
- Test Perl agent syntax with `perl -c ogp_agent.pl` when modifying agent files
- The web application requires a complete database schema to run properly, but basic connectivity can be tested

## CRITICAL Build & Test Timing
- **Guide generation: 3-10 seconds** - NEVER CANCEL. Set timeout to 30+ seconds minimum
- **Guide validation: Under 1 second** - Set timeout to 15+ seconds  
- **Package installation: 5-15 minutes** - NEVER CANCEL. Set timeout to 20+ minutes
- **PDF generation (with LaTeX): 2-5 seconds per document** - NEVER CANCEL
- **Database setup: Under 10 seconds** - Set timeout to 30+ seconds

## Common Tasks
The following are outputs from frequently run commands. Reference them instead of viewing, searching, or running bash commands to save time.

### Repository Structure
```
/home/runner/work/ControlPanel/ControlPanel/
├── index.php                    # Main web application entry point
├── includes/                    # PHP includes and configuration
│   ├── config.inc.php          # Database configuration  
│   ├── database_mysqli.php     # Database abstraction layer
│   └── helpers.php             # Utility functions
├── modules/                     # Web application modules
├── themes/                      # UI themes
├── tools/                       # Python guide generation tools
│   ├── generate_all_guides.sh  # Main guide generation script
│   ├── generate_server_guides.py # Python guide generator
│   └── validate_guides.py      # Guide validation script
├── _agent-linux/               # Linux agent (Perl)
│   ├── ogp_agent.pl           # Main agent script
│   └── ogp_agent_run          # Agent wrapper script
├── _agent-windows/             # Windows agent
├── data/games/                  # YAML game definitions for guides
└── dist/pdfs/                  # Generated PDF guides
```

### Dependencies Status  
- **PHP 8.3.6**: Available, with MySQLi extension
- **MySQL 8.0**: Available, can be started with `sudo service mysql start`
- **Perl 5.38**: Available, with all required OGP modules installed
- **Python 3.12**: Available, with PyYAML installed  
- **Pandoc + XeLaTeX**: Available, for PDF generation

### Guide Generation Workflow
```bash
# Complete workflow (3-10 seconds total)
cd /home/runner/work/ControlPanel/ControlPanel
./tools/generate_all_guides.sh

# Individual steps
python3 tools/generate_server_guides.py  # Generate markdown + PDFs
python3 tools/validate_guides.py         # Validate output

# Expected output structure:
# docs/games/<game-slug>/index.md         # Markdown guides
# dist/pdfs/<game-slug>__Server_Admin_Guide_v1.pdf # PDF guides  
# docs/games/_index.md                    # Index page
# dist/pdfs/manifest.json                 # Metadata manifest
```

### Agent Testing
```bash
# Test Perl agent syntax (agent requires special permissions to run)
cd /home/runner/work/ControlPanel/ControlPanel/_agent-linux
perl -c ogp_agent.pl
# Expected: "ogp_agent.pl syntax OK"
```

### Database Setup for Testing
```bash
# Basic database setup (already configured)
sudo service mysql start
mysql -u localuser -ptestpass -e "SHOW DATABASES;"
# Should show: information_schema, panel, performance_schema
```

### Web Application Testing
```bash
# Start development server (will show 500 errors without complete DB schema)
cd /home/runner/work/ControlPanel/ControlPanel  
php -S localhost:8080 &
curl -I http://localhost:8080  # Test connectivity
pkill -f "php -S"              # Stop server
```

## Key Projects in Codebase
1. **Web Panel** (`index.php`, `modules/`, `themes/`): PHP web application for game server management
2. **Linux Agent** (`_agent-linux/`): Perl-based agent for Linux game server management  
3. **Windows Agent** (`_agent-windows/`): Windows version of server agent
4. **Guide Tools** (`tools/`): Python scripts for generating comprehensive server admin documentation
5. **Game Definitions** (`data/games/`): YAML files defining game server configurations for guide generation

## Important Notes
- The web application requires complete database schema initialization to run fully (beyond basic connectivity testing)
- Agents require special system permissions and users to run properly in production
- Guide generation tools work independently and can be tested without the web application
- PDF generation requires both pandoc and XeLaTeX (texlive-xetex package)
- All basic syntax checking and connectivity testing works in this environment
- Focus testing on the components that can be fully validated: guide tools, PHP syntax, Perl syntax, and basic connectivity