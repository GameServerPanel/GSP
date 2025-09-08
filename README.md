# Open Game Panel - Enhanced Fork

This repository contains an enhanced version of Open Game Panel (OGP) with AdminLTE theme integration and additional features. Open Game Panel is a comprehensive game server management system that allows hosting providers and users to easily manage game servers across multiple platforms.

## What is Open Game Panel?

Open Game Panel is a distributed system consisting of:
- **Web Panel** (this repository) - PHP-based web interface for server management
- **Remote Agents** - Lightweight applications that run on game servers (Linux/Windows)
- **Game Server Integration** - Support for 100+ games with automated installation and management

## Key Features

### AdminLTE Theme Integration
- [x] Modern responsive interface with AdminLTE framework
- [x] Dark and Light Mode switcher
- [x] User-specific theme settings database
- [x] Custom dashboard with movable widgets
- [x] Enhanced server overview with charts and monitoring
- [x] Improved FTP file manager interface
- [x] Custom shop/billing interface
- [x] User avatars and profile customization
- [x] Maintenance mode notifications
- [x] Custom logo upload capability

### Core Game Server Management
- [x] Multi-platform support (Linux/Windows)
- [x] 100+ game support with automatic installation
- [x] Remote server management through encrypted agents
- [x] Real-time server monitoring and control
- [x] File management with web-based FTP interface
- [x] Configuration file editing
- [x] Backup and restore functionality
- [x] User permission and access control
- [x] Multi-language support (20+ languages)

### Advanced Features
- [x] REST API for external integrations
- [x] Billing and subscription management
- [x] Support ticket system
- [x] Steam Workshop integration
- [x] TeamSpeak 3 server management
- [x] MySQL database management
- [x] RCON (Remote Console) access
- [x] FastDL (Fast Download) server support

## Architecture

```
┌─────────────────┐    XXTEA Encrypted    ┌──────────────────┐
│   Web Panel     │◄──── Communication ──►│   Remote Agent   │
│   (PHP/MySQL)   │                        │   (Linux/Win)    │
│                 │                        │                  │
│ • User Interface│                        │ • Game Servers   │
│ • API Endpoints │                        │ • File Operations│
│ • Agent Manager │                        │ • Process Control│
│ • Module System │                        │ • Monitoring     │
└─────────────────┘                        └──────────────────┘
```

## Quick Start

### Prerequisites
- PHP 7.0+ with extensions: mysqli, openssl, zip, curl
- MySQL/MariaDB database
- Web server (Apache/Nginx)

### Installation
1. Clone this repository to your web directory
2. Configure database settings in `includes/config.inc.php`
3. Run the web-based installer by visiting your domain
4. Install and configure remote agents on your game servers
5. Begin managing game servers through the web interface

## Documentation

- **[ARCHITECTURE.md](ARCHITECTURE.md)** - Complete system architecture overview
- **[DEVELOPMENT.md](DEVELOPMENT.md)** - Developer guide and debugging information
- **[AGENT_INTEGRATION.md](AGENT_INTEGRATION.md)** - Recommendation for including agent code
- **[README_ANALYSIS.md](README_ANALYSIS.md)** - Comprehensive project analysis

## Related Repositories

This project is based on the original Open Game Panel:
- [OpenGamePanel/OGP-Website](https://github.com/OpenGamePanel/OGP-Website) - Original web panel
- [OpenGamePanel/OGP-Agent-Linux](https://github.com/OpenGamePanel/OGP-Agent-Linux) - Linux agent
- [OpenGamePanel/OGP-Agent-Windows](https://github.com/OpenGamePanel/OGP-Agent-Windows) - Windows agent

## Contributing

Please read [DEVELOPMENT.md](DEVELOPMENT.md) for development guidelines and coding standards.

## License

This project is licensed under the GNU General Public License v2.0 - see the [LICENSE](LICENSE) file for details.

## Support

- Original OGP Community: https://opengamepanel.org
- Issues and bug reports: Use GitHub Issues
- Development discussions: See GitHub Discussions

## Screenshots

![AdminLTE Dark Theme](../main/adminlte_dark.png)
![AdminLTE Light Theme](../main/adminlte_light.png)
![Server Overview](../main/adminlte_online-servers.png)
![Shop Interface](../main/adminlte_shop.png)
![Maintenance Mode](../main/adminlte_maintenance.png)
![Support Chat](../main/adminlte_support_chat.png)
