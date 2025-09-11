# OGP Agent Resource Stats Collection

This document describes the integrated resource statistics collection system in the OGP agents that replaces the standalone Python collector.py script.

## Overview

The OGP agents now include built-in resource monitoring that collects:
- **Machine-wide statistics:** CPU usage, memory, disk space, network activity, load averages
- **Per-server process statistics:** Memory usage, CPU usage, I/O statistics, listening ports, folder sizes

Data is automatically inserted into the same MySQL database tables used by the web panel for display.

## Configuration

### Database Configuration

Update the agent configuration files with your database connection details:

**Linux Agent:** `_agent-linux/Cfg/Config.pm`
**Windows Agent:** `_agent-windows/Cfg/Config.pm`

```perl
# Resource stats database configuration
stats_db_host => '127.0.0.1',           # Database server hostname/IP
stats_db_user => 'panel_user',          # Database username
stats_db_pass => 'your_password_here',  # Database password
stats_db_name => 'panel_database',      # Database name (same as your web panel)
stats_table_prefix => 'gsp_',           # Table prefix (must match collector.py)
stats_frequency_minutes => '5',         # Collection frequency in minutes
```

### Database Tables

The system uses the same database tables as the original collector.py:

```sql
-- Machine catalog
gsp_machines

-- Machine-wide samples (CPU, memory, disk, network)
gsp_machine_samples

-- Per-process/per-server samples
gsp_process_samples
```

Run the SQL schema from `modules/resource_stats/mysql_query.sql` to create these tables if they don't exist.

### Required Perl Modules

The agents require these Perl modules for database connectivity:

```bash
# Ubuntu/Debian
sudo apt-get install libdbi-perl libdbd-mysql-perl

# CentOS/RHEL
sudo yum install perl-DBI perl-DBD-MySQL

# Or via CPAN
cpan DBI DBD::mysql
```

## Features

### Automatic Integration
- Runs automatically as part of the agent scheduler
- No separate cron jobs required
- Starts when the agent starts
- Stops when the agent stops

### Platform Support

**Linux Agent:**
- Uses `/proc` filesystem for system stats
- Native command-line tools (`df`, `netstat`, etc.)
- Full CPU, memory, disk, and network monitoring
- Process association via working directory and command line analysis

**Windows Agent:**
- Uses `wmic` for system information
- Native Windows commands (`dir`, `netstat`)
- CPU, memory, disk monitoring (no load averages on Windows)
- Process association via executable path and command line analysis

### Process Detection

The system automatically detects game server processes by:
1. Scanning for subdirectories in the agent directory
2. Finding processes whose working directory, executable path, or command line references these directories
3. Collecting detailed metrics for each associated process

### Data Collection

**Machine-wide metrics:**
- Load averages (Linux only)
- CPU percentage
- Memory usage (used/total/percentage)
- Swap usage
- Disk usage for agent directory
- Network interface statistics
- Network throughput

**Process-specific metrics:**
- Process ID and name
- Command line
- CPU percentage
- Memory usage (RSS/VMS)
- I/O statistics (read/write bytes)
- Open file descriptors
- Listening network ports
- Server directory size

## Monitoring and Troubleshooting

### Log Messages

The agent logs resource collection activity:

```
Starting resource stats collection
Resource stats collection completed
```

### Error Handling

If database modules are not available:
```
DBD::mysql not available - resource stats collection disabled
```

If database connection fails:
```
Failed to connect to stats database: [error details]
```

### Verification

To verify the system is working:

1. Check agent logs for collection messages
2. Query the database tables:
   ```sql
   SELECT COUNT(*) FROM gsp_machine_samples WHERE ts >= NOW() - INTERVAL 1 HOUR;
   SELECT COUNT(*) FROM gsp_process_samples WHERE ts >= NOW() - INTERVAL 1 HOUR;
   ```

### Performance Impact

- Collection runs every 5 minutes by default (configurable)
- Minimal performance overhead during collection
- Uses native system tools for maximum efficiency
- Database operations are optimized with prepared statements

## Migration from collector.py

To migrate from the standalone Python collector:

1. **Stop the cron job** running collector.py
2. **Install Perl database modules** on agent machines
3. **Update agent configuration** with database details
4. **Restart OGP agents** to enable collection
5. **Verify data collection** is working
6. **Remove collector.py and Python dependencies**

The new system produces identical data to collector.py and uses the same database schema, so existing dashboards and reports will continue to work without changes.

## Frequency Configuration

The collection frequency can be adjusted by changing `stats_frequency_minutes` in the config:

- `stats_frequency_minutes => '1'` - Every minute (high frequency)
- `stats_frequency_minutes => '5'` - Every 5 minutes (default)
- `stats_frequency_minutes => '15'` - Every 15 minutes (low frequency)

Note: Very high frequencies (every minute) may impact performance on busy servers.

## Security Considerations

- Database credentials are stored in agent configuration files
- Use dedicated database user with minimal privileges
- Consider firewall rules if database is on separate server
- Monitor database connections and prevent connection leaks

## Troubleshooting Common Issues

**Collection not working:**
1. Check if DBD::mysql is installed
2. Verify database connection details
3. Check database user permissions
4. Review agent logs for error messages

**Missing process data:**
1. Verify game servers are running from subdirectories
2. Check process detection logic matches your server layout
3. Review process association in agent logs

**Performance issues:**
1. Reduce collection frequency
2. Check database performance
3. Monitor agent resource usage during collection