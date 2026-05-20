# GSP Agent Windows — Copilot Instructions

**Repository purpose:** OpenGamePanel (OGP) Windows agent for managing game servers on Windows hosts.  
**Prime directive:** This is a Perl-based server agent that runs on Windows to manage game server instances. It communicates with the GSP panel and handles Windows-specific game server management.

## Architecture Overview

### Core Components
- **`ogp_agent.pl`**: Main Perl agent daemon (same core as Linux version)
- **`agent_conf.sh`**: Configuration script adapted for Windows paths/services
- **`Install/`**: Windows-specific installation scripts and dependencies
- **Game-specific modules**: Windows game engine handlers and integrations
- **Windows Service integration**: Can run as Windows service

### Key Directories & Their Purpose
- **`Cfg/`**: Configuration file parsers (Windows path handling)
- **`Crypt/`**: Encryption and security modules for panel communication
- **`File/`**: File management with Windows filesystem support
- **`FastDownload/`**: Download acceleration for Windows game content
- **`Minecraft/`**: Minecraft server management (Windows .bat files)
- **`ArmaBE/`**: Arma server BattlEye integration for Windows
- **`ServerFiles/`**: Windows game server executables and libraries
- **`Schedule/`**: Windows Task Scheduler integration
- **`Install/`**: Windows-specific installation components

## Windows-Specific Considerations

### Path Management
- **Drive letters**: Handle C:\, D:\ drive specifications properly
- **Path separators**: Use Windows backslash paths internally
- **UNC paths**: Support network share paths for distributed storage
- **Long paths**: Handle Windows long path limitations (>260 characters)

### Process Management
- **Windows Services**: Integration with Windows Service Control Manager
- **Process isolation**: Run game servers in separate Windows sessions
- **Registry access**: Read Windows registry for game installation paths
- **UAC handling**: Proper User Account Control integration

### Windows Game Engines
- **Steam games**: SteamCMD integration on Windows
- **Windows-only games**: Support for .exe-based game servers
- **DirectX dependencies**: Manage Visual C++ redistributables
- **.NET Framework**: Handle .NET game server requirements

## Development Guidelines

### Windows Perl Environment
- **Strawberry Perl**: Recommended Perl distribution for Windows
- **ActivePerl compatibility**: Support both major Perl distributions
- **Windows modules**: Use Win32:: modules for Windows-specific operations
- **Path handling**: Use File::Spec for cross-platform path operations

### Security Requirements
- **Windows Defender**: Whitelist agent and game executables
- **Firewall integration**: Windows Firewall rule management
- **User privileges**: Run with appropriate Windows user rights
- **File permissions**: NTFS permission management for game directories

### Service Integration
- **Windows Services**: Install agent as proper Windows service
- **Event logging**: Use Windows Event Log for system integration
- **Startup types**: Support automatic/manual service startup
- **Service recovery**: Configure service recovery options

## Critical Implementation Patterns

### Windows Path Handling
```perl
# Always use proper Windows path handling
use File::Spec;
use Cwd;

sub normalize_windows_path {
    my ($path) = @_;
    $path = File::Spec->canonpath($path);
    return $path;
}

# Convert panel paths to Windows format
sub panel_to_windows_path {
    my ($unix_path) = @_;
    $unix_path =~ s|/|\\|g;  # Convert forward slashes
    return $unix_path;
}
```

### Process Management on Windows
```perl
# Windows-specific process spawning
sub start_windows_server {
    my ($home_id, $executable, $args) = @_;
    
    # Use proper Windows process creation
    my $cmd = qq{"$executable" $args};
    my $pid = system(1, $cmd);  # Non-blocking system call
    
    return $pid;
}
```

### Registry Access Example
```perl
# Read game installation paths from Windows Registry
use Win32::Registry;

sub get_steam_path {
    my $steam_key;
    $HKEY_LOCAL_MACHINE->Open("SOFTWARE\\Valve\\Steam", $steam_key) or return undef;
    
    my $install_path;
    $steam_key->QueryValueEx("InstallPath", my $type, $install_path);
    $steam_key->Close();
    
    return $install_path;
}
```

## Windows-Specific Features

### Service Installation
- **sc.exe integration**: Use Windows Service Control for service management
- **NSSM support**: Non-Sucking Service Manager for complex services
- **Service dependencies**: Handle service dependency chains
- **Service accounts**: Run under appropriate service accounts

### Windows Firewall Management
- **Port rules**: Automatically create firewall rules for game ports
- **Program exceptions**: Add game executables to firewall exceptions
- **Profile management**: Handle different firewall profiles (domain/private/public)

### Game Server Specifics
- **Windows game paths**: Support typical Windows game installation locations
- **DLL dependencies**: Handle game DLL requirements and redistributables
- **Windows-only features**: Support Windows-specific game server features
- **Performance counters**: Use Windows performance monitoring

## Common Windows Issues to Avoid
1. **Path length limits**: Handle Windows 260-character path limitation
2. **Permission escalation**: Avoid unnecessary UAC prompts
3. **Service isolation**: Ensure proper service isolation and security
4. **Registry pollution**: Clean registry entries on uninstall
5. **DLL hell**: Manage game DLL conflicts and dependencies
6. **Windows Updates**: Handle Windows Update service interactions
7. **Antivirus conflicts**: Manage antivirus software interactions

## Integration with GSP Panel
- **Same API**: Uses identical panel communication as Linux agent
- **Windows paths**: Translate Unix paths from panel to Windows format
- **File uploads**: Handle Windows-specific file upload scenarios
- **Process monitoring**: Windows-specific process and resource monitoring

## Installation Requirements
- **Perl environment**: Strawberry Perl or ActivePerl
- **Windows dependencies**: Visual C++ redistributables, .NET Framework
- **Network access**: Outbound HTTPS for panel communication
- **Admin privileges**: Initial installation requires administrator rights
- **Firewall configuration**: Inbound rules for game server ports

## Testing on Windows
- **Windows versions**: Test on Windows Server 2019/2022 and Windows 10/11
- **Permission scenarios**: Test with different user privilege levels
- **Antivirus testing**: Validate with common antivirus software
- **Game compatibility**: Test with Windows-specific game servers
- **Service reliability**: Long-running service stability testing