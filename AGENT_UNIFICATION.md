# OGP Agent Unification

## Overview
This project unifies the Linux and Windows OGP (Open Game Panel) agent files into a single cross-platform agent that works on both Linux and Windows/Cygwin systems.

## Key Platform-Specific Differences Addressed

### 1. SteamCMD Binary Paths
- **Linux**: Uses `steamcmd.sh`
- **Windows**: Uses `steamcmd.exe`
- **Solution**: Platform-conditional constant `STEAMCMD_CLIENT_BIN`

### 2. Sudo Functionality
- **Linux**: Full sudo support with password-based authentication
- **Windows**: No sudo functionality (different permission model)
- **Solution**: Conditional sudo functions that are no-ops on Windows

### 3. User Management
- **Linux**: Uses `LINUX_USER_PER_GAME_SERVER` and `SERVER_RUNNER_USER`
- **Windows**: Uses `USER_RUNNING_SCRIPT` with Cygwin-specific fallbacks
- **Solution**: Platform-conditional user constants

### 4. Log Rotation
- **Linux**: Automated log rotation functionality
- **Windows**: Static log handling
- **Solution**: Conditional log rotation code block

### 5. Screen Configuration
- **Linux**: Dynamic screenrc file modification with temporary files
- **Windows**: Static screenrc usage with basic screen session cleanup
- **Solution**: Conditional screen configuration logic

### 6. File Permissions
- **Linux**: Complex permission management using chmod, setfacl, chattr
- **Windows**: Simpler permission model
- **Solution**: Linux-only permission functions

### 7. Process Execution
- **Linux**: Bash scripts with respawn logic and Wine environment variables
- **Windows**: Batch files with priority/affinity control and Cygwin path conversion
- **Solution**: Platform-specific `create_screen_cmd` and `create_screen_cmd_loop` functions

### 8. Path Handling
- **Linux**: Standard Unix paths
- **Windows**: Cygwin path conversion using `cygpath -wa`
- **Solution**: Platform-specific path handling in `replace_OGP_Env_Vars`

## Implementation Strategy

The unified agent uses:
1. **Platform Detection**: `$^O` variable to detect Windows/Cygwin vs Linux
2. **Conditional Constants**: Platform-specific values set at compile time
3. **Conditional Logic**: Runtime platform checks for different behaviors
4. **Function Overloading**: Same function names with platform-specific implementations

## Files

- `ogp_agent_unified.pl`: The unified agent file
- `AGENT_UNIFICATION.md`: This documentation
- `_agent-linux/`: Original Linux agent (preserved)
- `_agent-windows/`: Original Windows agent (preserved)

## Testing

The unified agent can be tested on both platforms:
- Linux: Standard Perl environment
- Windows: Cygwin with Perl environment

## Deployment

To use the unified agent:
1. Copy `ogp_agent_unified.pl` to the target system
2. Ensure all required Perl modules are installed
3. Run with same command-line options as original agents
4. The agent will automatically detect the platform and behave appropriately

## Benefits

1. **Single Codebase**: Easier maintenance and updates
2. **Reduced Duplication**: No need to maintain two separate files
3. **Consistent Features**: New features automatically work on both platforms
4. **Simplified Deployment**: One file works everywhere