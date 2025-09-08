# OGP Agent Unified - Deployment Guide

## Overview

The unified OGP agent (`ogp_agent_unified.pl`) replaces both the Linux and Windows agent files with a single cross-platform solution that automatically detects the operating system and applies the appropriate platform-specific logic.

## Requirements

### Linux Systems
- Perl 5.x or higher
- Required Perl modules:
  - Frontier::Daemon::OGP::Forking
  - Crypt::XXTEA
  - All standard modules (File::Copy, Path::Class, etc.)
- Screen utility
- Sudo access (if using sudo functionality)

### Windows/Cygwin Systems
- Cygwin environment with Perl
- Same Perl modules as Linux
- Screen utility (available in Cygwin)
- Administrative privileges may be required

## Installation

### Step 1: Backup Existing Agents
```bash
# Backup existing agents
cp _agent-linux/ogp_agent.pl _agent-linux/ogp_agent.pl.bak
cp _agent-windows/ogp_agent.pl _agent-windows/ogp_agent.pl.bak
```

### Step 2: Deploy Unified Agent
```bash
# Copy unified agent to both directories
cp ogp_agent_unified.pl _agent-linux/ogp_agent.pl
cp ogp_agent_unified.pl _agent-windows/ogp_agent.pl
```

### Step 3: Verify Configuration Files
Ensure the following configuration files exist and are properly configured:
- `Cfg/Config.pm` - Main configuration
- `Cfg/Preferences.pm` - Preferences
- `ogp_screenrc` - Screen configuration

## Usage

The unified agent uses the same command-line interface as the original agents:

```bash
# Standard startup
perl ogp_agent.pl

# With options
perl ogp_agent.pl --no-startups --log-stdout
```

### Command Line Options
- `--no-startups`: Don't start any games on agent startup
- `--clear-startups`: Clear all startup configurations
- `--log-stdout`: Log to standard output instead of file

## Platform Detection

The agent automatically detects the platform using Perl's `$^O` variable:
- Linux: `$^O` matches `linux`
- Windows/Cygwin: `$^O` matches `MSWin32` or `cygwin`

You can verify platform detection by calling the `what_os` XML-RPC method.

## Platform-Specific Behaviors

### Linux-Specific Features
- Full sudo functionality with password authentication
- Automated log rotation
- Dynamic screen configuration modification
- Complex file permissions (chmod, setfacl, chattr)
- Bash script execution with respawn logic
- Wine environment variable support

### Windows/Cygwin-Specific Features
- No sudo functionality (uses Windows permission model)
- Static log handling
- Basic screen session cleanup
- Simple file permissions
- Batch file execution with priority/affinity control
- Cygwin path conversion for Windows paths

## Troubleshooting

### Common Issues

1. **Module Not Found Errors**
   ```
   Can't locate Frontier/Daemon/OGP/Forking.pm
   ```
   Solution: Install missing Perl modules or ensure the module path is correct.

2. **Permission Denied**
   ```
   Permission denied accessing steamcmd.sh
   ```
   Solution: On Linux, ensure execute permissions are set. The agent will attempt to set them automatically.

3. **Platform Detection Issues**
   Test platform detection with:
   ```bash
   perl test_unification.pl
   ```

4. **Path Conversion Issues (Windows)**
   Ensure Cygwin is properly installed and `cygpath` utility is available.

### Debug Mode
Enable debug logging by setting `--log-stdout` option:
```bash
perl ogp_agent.pl --log-stdout
```

### Log Files
- Linux: Standard log file location as configured
- Windows: Same as Linux, but no automatic rotation

## Migration from Separate Agents

### For Linux Systems
1. Stop existing agent: `killall ogp_agent.pl`
2. Replace with unified agent
3. Start unified agent: `perl ogp_agent.pl`

### For Windows Systems
1. Stop existing agent in Cygwin
2. Replace with unified agent  
3. Start unified agent: `perl ogp_agent.pl`

## Validation

After deployment, validate the unified agent is working correctly:

1. **Check Platform Detection**
   ```bash
   perl test_unification.pl
   ```

2. **Verify XML-RPC Service**
   Test that the agent responds to XML-RPC calls on the configured port.

3. **Test Game Server Operations**
   Verify that game servers can be started, stopped, and managed normally.

## Rollback Procedure

If issues occur, rollback to the original agents:

```bash
# Linux rollback
cp _agent-linux/ogp_agent.pl.bak _agent-linux/ogp_agent.pl

# Windows rollback  
cp _agent-windows/ogp_agent.pl.bak _agent-windows/ogp_agent.pl
```

## Support

For issues with the unified agent:
1. Check the troubleshooting section above
2. Review log files for error messages
3. Test with the validation scripts provided
4. Compare behavior with original agents if needed

## Benefits of Unified Agent

- **Single Codebase**: Easier maintenance and updates
- **Automatic Platform Detection**: No manual configuration needed
- **Consistent Feature Set**: New features work on both platforms
- **Reduced Deployment Complexity**: One file for all environments
- **Better Testing**: Single codebase easier to test and validate