# Open Game Panel (OGP) Architecture Documentation

## Overview

Open Game Panel is a distributed game server management system consisting of three main components:

1. **Web Panel** (this repository) - PHP-based web interface for management
2. **Linux Agent** - Remote agent for Linux game servers  
3. **Windows Agent** - Remote agent for Windows game servers

## Architecture Diagram

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Web Panel     │    │   Linux Agent    │    │  Windows Agent  │
│   (PHP/MySQL)   │◄──►│     (PHP)        │    │     (PHP)       │
│                 │    │                  │    │                 │
│ - User Interface│    │ - Game Server    │    │ - Game Server   │
│ - API Endpoints │    │   Management     │    │   Management    │
│ - Agent Manager │    │ - File Operations│    │ - File Operations│
│ - Module System │    │ - Process Control│    │ - Process Control│
└─────────────────┘    └──────────────────┘    └─────────────────┘
```

## Component Details

### Web Panel (Current Repository)

**Purpose**: Central control interface for managing game servers across multiple remote machines.

**Key Files**:
- `index.php` - Main application entry point
- `ogp_api.php` - REST API for external integrations
- `includes/lib_remote.php` - Agent communication library
- `includes/config.inc.php` - Database and core configuration
- `modules/` - Modular functionality (40+ modules)

**Database**: MySQL with configurable table prefix (`ogp_` by default)

**Security**: 
- XXTEA encryption for agent communication
- Session management for web interface
- Token-based API authentication

### Remote Agents

**Purpose**: Execute game server operations on remote machines under control of the web panel.

**Communication**:
- Encrypted TCP connections using XXTEA
- Default port: Configurable per agent
- Request/response protocol for commands

**Operations**:
- Start/stop/restart game servers
- File management (upload/download/edit)
- Server installation and updates
- Resource monitoring
- Log file access

## Module System

The web panel uses a modular architecture with the following core modules:

- **server** - Game server management
- **gamemanager** - Game definitions and templates  
- **user_games** - User server assignments
- **administration** - System administration
- **ftp** - File transfer protocol access
- **mysql** - Database management
- **backup-restore** - Server backup functionality
- **rcon** - Remote console access
- **tickets** - Support ticket system
- **billing** - Payment and subscription management

## Communication Protocol

### Agent Connection
1. Web panel establishes TCP connection to agent
2. Commands are encrypted using XXTEA with shared key
3. Agent processes command and returns encrypted response
4. Connection is maintained or closed based on operation

### API Endpoints
The web panel exposes REST API endpoints for:
- Token management
- Server control (start/stop/restart)
- Server creation and configuration
- User and game management

Example API calls:
```
POST /ogp_api.php?token/create/{user}/{password}
GET /ogp_api.php?server/list (with token)
POST /ogp_api.php?server/restart (with token and server_id)
```

## Development Setup

### Prerequisites
- PHP 7.0+ with extensions: mysqli, openssl, zip
- MySQL/MariaDB database
- Web server (Apache/Nginx)

### Installation
1. Clone repository to web directory
2. Configure database in `includes/config.inc.php`
3. Run database installation via web interface
4. Configure agents on remote servers
5. Add agent connections through web interface

## File Structure

```
panel/
├── index.php              # Main application
├── ogp_api.php           # REST API
├── includes/             # Core libraries
│   ├── config.inc.php   # Configuration
│   ├── lib_remote.php   # Agent communication
│   ├── functions.php    # Utility functions
│   └── database.php     # Database abstraction
├── modules/             # Feature modules
│   ├── server/          # Server management
│   ├── gamemanager/     # Game definitions
│   ├── user_games/      # User assignments
│   └── ...
├── themes/              # UI themes
├── lang/                # Internationalization
├── js/                  # JavaScript libraries
└── css/                 # Stylesheets
```

## Security Considerations

- All agent communication is encrypted using XXTEA
- Database credentials should use restricted MySQL user
- Web panel should run under restricted web server user
- File permissions should prevent unauthorized access
- Regular security updates should be applied

## Legacy Code Considerations

This codebase originated in 2008 and contains legacy PHP patterns:
- Mixed procedural and object-oriented code
- Older security practices (needs modernization)
- Manual SQL query construction (consider prepared statements)
- Global variables and functions
- Limited error handling and logging

## Recommended Improvements

1. **Security Hardening**
   - Implement prepared statements
   - Add CSRF protection
   - Improve input validation
   - Enable detailed error logging

2. **Code Modernization**
   - Adopt PSR standards
   - Implement autoloading
   - Add dependency injection
   - Convert to MVC architecture

3. **Infrastructure**
   - Add Docker support
   - Implement CI/CD pipeline
   - Add automated testing
   - Create development environment

4. **Documentation**
   - API documentation
   - Module development guide
   - Deployment procedures
   - Troubleshooting guides