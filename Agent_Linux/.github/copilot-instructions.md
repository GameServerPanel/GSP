# GSP Agent Linux — Copilot Instructions

**Repository purpose:** OpenGamePanel (OGP) Linux agent for managing game servers remotely.  
**Prime directive:** This is a Perl-based server agent that runs on Linux hosts to manage game server instances. It communicates with the GSP panel via secure protocols.

## Architecture Overview

### Core Components
- **`ogp_agent.pl`**: Main Perl agent daemon that handles panel communications
- **`agent_conf.sh`**: Configuration script for agent settings and paths
- **`install.sh`**: Installation script with dependency management
- **Game-specific modules**: In subdirectories for various game engines
- **Screen management**: Uses GNU Screen for game server session management

### Key Directories & Their Purpose
- **`Cfg/`**: Configuration file parsers and game-specific config handlers
- **`Crypt/`**: Encryption and security modules for panel communication
- **`File/`**: File management operations (upload/download/extraction)
- **`FastDownload/`**: HTTP/FTP download acceleration for game content
- **`Frontier/`**: Elite Dangerous server support
- **`Minecraft/`**: Minecraft server management and mod support
- **`ArmaBE/`**: Arma server BattlEye integration
- **`includes/`**: Core Perl modules and helper functions

## Agent Communication Protocol
- **Secure XML-RPC**: Primary communication with GSP panel
- **Authentication**: Certificate-based authentication with panel
- **Port management**: Dynamic port allocation for game servers
- **Status reporting**: Real-time server status and resource monitoring

## Deployment Patterns

### Installation Workflow
1. **Prerequisites**: Install required Perl modules and system packages (see README.md)
2. **Agent setup**: Run `install.sh` to configure agent environment
3. **Panel registration**: Register agent with GSP panel using provided keys
4. **Service startup**: Configure agent as system service (systemd/init.d)

### Game Server Management
- **Server creation**: Panel requests create new game server instance
- **Resource allocation**: Agent allocates ports, directories, and resources  
- **Process management**: Spawn/stop game servers using screen sessions
- **Log monitoring**: Tail game logs and report status to panel
- **File operations**: SFTP/FTP file access for server administration

## Development Guidelines

### Perl Coding Standards
- **Strict mode**: Always use `use strict;` and `use warnings;`
- **Error handling**: Comprehensive error checking with meaningful messages
- **Logging**: Use structured logging for debugging and audit trails
- **Resource cleanup**: Proper cleanup of temporary files and processes

### Security Requirements
- **Input validation**: Sanitize all panel inputs and file paths
- **Process isolation**: Game servers run as separate user processes
- **File permissions**: Strict file permissions for game directories
- **Network security**: Firewall integration for dynamic port management

### Game Engine Integration
- **Modular design**: Each game engine has dedicated handler module
- **Config templating**: Template-based configuration file generation
- **Update management**: Automated game server updates via SteamCMD/etc
- **Mod support**: Plugin/mod installation and management

## Critical Implementation Patterns

### Error Handling Example
```perl
# Always validate inputs from panel
sub validate_server_path {
    my ($path) = @_;
    return 0 unless defined $path;
    return 0 if $path =~ /\.\./;  # Prevent directory traversal
    return 0 unless $path =~ /^\/ogp_user_files\//;  # Ensure proper base path
    return 1;
}
```

### Screen Session Management
```perl
# Standard pattern for game server process management
sub start_server {
    my ($home_id, $startup_cmd) = @_;
    my $screen_id = "OGP_${home_id}";
    system("screen -dmS $screen_id bash -c '$startup_cmd'");
    return check_screen_running($screen_id);
}
```

## Common Issues to Avoid
1. **Directory traversal**: Always validate file paths from panel requests
2. **Resource leaks**: Ensure proper cleanup of screen sessions and temp files
3. **Permission errors**: Game servers must run with appropriate user privileges
4. **Port conflicts**: Check port availability before allocation
5. **Log rotation**: Implement log rotation to prevent disk space issues
6. **Process zombies**: Proper process management and cleanup

## Integration with GSP Panel
- **Database sync**: Agent status stored in panel database
- **Real-time updates**: WebSocket or polling for live server status
- **File browser**: SFTP integration for web-based file management
- **Performance monitoring**: CPU/RAM/disk usage reporting
- **Backup integration**: Automated server backup scheduling

## Testing & Validation
- **Unit tests**: Test individual agent functions
- **Integration tests**: Full panel ↔ agent communication tests
- **Game server tests**: Verify each supported game engine works
- **Load testing**: Multiple concurrent server management
- **Security testing**: Validate input sanitization and privilege separation

## Deployment Environments
- **Production**: Full security hardening and monitoring
- **Development**: Local testing with panel development instance
- **Staging**: Pre-production validation environment
- **Docker**: Containerized deployment for cloud environments