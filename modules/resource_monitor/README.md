# OGP Resource Monitoring System

This module provides comprehensive resource monitoring for Open Game Panel (OGP) installations, including real-time monitoring, alerting, and historical data visualization.

## Features

- **System-wide Monitoring**: CPU, RAM, and disk usage for entire servers
- **Per-Game-Server Monitoring**: Individual resource usage for each game instance
- **Discord Alerts**: Configurable threshold-based alerts sent to Discord channels
- **Historical Data**: 30-day retention with trend visualization
- **Automated Collection**: 5-minute interval data collection via cron jobs
- **Web Dashboard**: Real-time status with color-coded indicators

## Installation

1. **Database Setup**: The resource monitoring tables are automatically created when applying the main database schema, or you can apply them individually:
   ```bash
   mysql -u username -p database_name < db/resource_monitoring_schema.sql
   ```

2. **Agent Enhancement**: The monitoring functions are added to the OGP agent (`_agent-linux/ogp_agent.pl`). The agent must be restarted for the new functions to be available.

3. **Cron Job Setup**: Set up automated data collection:
   ```bash
   # Run the setup script
   ./scripts/setup_monitoring_cron.sh
   
   # Or manually add to crontab:
   */5 * * * * /usr/bin/php /path/to/GSP/scripts/resource_collector.php >> /var/log/ogp_monitoring.log 2>&1
   ```

4. **PHP Requirements**: Ensure the following PHP extensions are installed:
   - mysqli (for database access)
   - curl (for Discord webhooks)
   - xmlrpc (for agent communication)

## Usage

### Dashboard Access

Navigate to the Resource Monitor module in your OGP panel:
- **Dashboard**: Real-time resource status for all servers
- **History**: Historical data and trend analysis
- **Alerts**: Configure Discord alerts and thresholds
- **Configuration**: Setup instructions and system status

### Setting Up Alerts

1. **Create Discord Webhook**:
   - In your Discord server, go to Server Settings → Integrations → Webhooks
   - Create a new webhook and copy the webhook URL

2. **Configure Alert Thresholds**:
   - Access the "Alert Configuration" page
   - Set the default Discord webhook URL
   - Add alerts for specific servers and resource types
   - Configure threshold percentages and duration requirements

3. **Alert Parameters**:
   - **Threshold**: Percentage at which to trigger alerts (default: 80%)
   - **Duration**: Minutes the threshold must be exceeded (default: 30)
   - **Cooldown**: Minimum time between identical alerts (default: 60 minutes)

### Data Collection

The system collects the following metrics:

**System-wide (per server)**:
- CPU usage percentage
- Memory usage percentage and absolute values
- Disk usage percentage and absolute values
- Network traffic (cumulative)

**Per-game-server**:
- CPU usage by game processes
- Memory usage by game processes
- Process count

### API Endpoints

The system provides API endpoints for data collection:
- `?m=resource_monitor&type=api_collect` - Trigger manual collection

## Technical Details

### Database Schema

- `ogp_resource_monitoring`: Timestamped resource data
- `ogp_resource_alerts`: Alert configurations
- `ogp_resource_alert_history`: Alert trigger history
- `ogp_discord_settings`: Discord integration settings

### Agent Functions

New XML-RPC functions added to the agent:
- `get_system_resource_usage()`: System-wide metrics
- `get_gameserver_resource_usage(home_id)`: Game-specific metrics

### Data Retention

- Resource monitoring data: 30 days
- Alert history: 90 days
- Automatic cleanup via scheduled database operations

## Configuration Examples

### Basic Alert Setup
```
Server: My Game Server
Resource Type: CPU
Threshold: 80%
Duration: 30 minutes
```

### Discord Webhook Format
```json
{
  "username": "OGP Resource Monitor",
  "embeds": [{
    "title": "🚨 Resource Alert Triggered",
    "color": 15158332,
    "fields": [
      {"name": "Server", "value": "My Server", "inline": true},
      {"name": "Resource", "value": "CPU", "inline": true},
      {"name": "Usage", "value": "85.2%", "inline": true}
    ]
  }]
}
```

## Troubleshooting

### Common Issues

1. **No Data Appearing**:
   - Verify cron job is running: `crontab -l`
   - Check agent connectivity and authentication
   - Review log files: `tail -f /var/log/ogp_monitoring.log`

2. **Agent Connection Errors**:
   - Ensure agent is running and accessible
   - Verify encryption keys match between panel and agent
   - Check firewall settings for agent port

3. **Discord Alerts Not Sending**:
   - Verify webhook URL is correct and active
   - Check Discord server permissions
   - Review PHP curl extension availability

### Log Files

- Monitoring collection: `/var/log/ogp_monitoring.log`
- PHP errors: Check your web server's error log
- Agent logs: `_agent-linux/ogp_agent.log`

## Performance Considerations

- Data collection occurs every 5 minutes per server
- Database queries are optimized with proper indexing
- Historical data is automatically pruned after 30 days
- Dashboard queries are limited to recent data for performance

## Security

- All agent communication uses XXTEA encryption
- Discord webhooks use HTTPS
- Database queries use proper escaping to prevent injection
- Alert cooldowns prevent webhook spam

## Future Enhancements

- Advanced charting and graphing
- Email alert options
- Mobile-responsive dashboard improvements
- Custom alert conditions and thresholds
- Integration with external monitoring systems

## Support

For issues and questions:
1. Check the OGP documentation
2. Review log files for error messages
3. Verify all dependencies are installed
4. Test individual components (database, agent, webhooks)