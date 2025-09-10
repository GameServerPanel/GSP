#!/bin/bash

# Setup script for OGP Resource Monitoring Cron Job
# This script sets up the cron job to collect resource data every 5 minutes

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
OGP_DIR="$(dirname "$SCRIPT_DIR")"
COLLECTOR_SCRIPT="$OGP_DIR/scripts/resource_collector.php"

echo "Setting up OGP Resource Monitoring..."

# Check if the resource collector script exists
if [ ! -f "$COLLECTOR_SCRIPT" ]; then
    echo "Error: Resource collector script not found at $COLLECTOR_SCRIPT"
    exit 1
fi

# Make the script executable
chmod +x "$COLLECTOR_SCRIPT"

# Create the cron job entry
CRON_ENTRY="*/5 * * * * /usr/bin/php $COLLECTOR_SCRIPT >> /var/log/ogp_monitoring.log 2>&1"

# Add to crontab if not already present
if ! crontab -l 2>/dev/null | grep -q "resource_collector.php"; then
    (crontab -l 2>/dev/null; echo "$CRON_ENTRY") | crontab -
    echo "Cron job added successfully!"
    echo "Resource monitoring will run every 5 minutes."
else
    echo "Cron job already exists."
fi

echo ""
echo "To verify the cron job was added, run: crontab -l"
echo "To view monitoring logs, run: tail -f /var/log/ogp_monitoring.log"
echo ""
echo "You can also trigger manual collection by running:"
echo "  php $COLLECTOR_SCRIPT"