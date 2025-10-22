# Website Configuration Guide

## Overview

The `_website` folder is now a standalone site with centralized database configuration. All database connection settings are managed in a single location: `includes/config.inc.php`.

## Directory Structure

```
_website/
├── includes/
│   ├── config.inc.php    # Central database configuration
│   └── README.md         # Documentation for includes directory
├── db.php                # Database connection (loads config.inc.php)
├── login.php             # Uses db.php
├── logout.php            # Uses db.php
├── cart.php              # Uses db.php
├── order.php             # Uses db.php
├── serverlist.php        # Uses db.php
└── ...other files
```

## Configuration File

### Location
`_website/includes/config.inc.php`

### Contents
```php
<?php
$db_host="localhost";        // Database server hostname
$db_user="localuser";        // Database username
$db_pass="password";         // Database password
$db_name="panel";            // Database name
$table_prefix="ogp_";        // Table prefix
$db_type="mysql";            // Database type
?>
```

## How It Works

1. **Configuration Loading**
   - Website files include `db.php`
   - `db.php` loads `includes/config.inc.php`
   - Configuration variables are available to all files

2. **Configuration Flow**
   ```
   includes/config.inc.php → db.php → website files
   ```

3. **Database Connection**
   - `db.php` uses the configuration variables to establish a connection
   - Returns `$db` variable containing the mysqli connection

## Setup Instructions

### For Standalone Use

1. **Copy the _website folder** to your web server
2. **Edit configuration**:
   ```bash
   nano _website/includes/config.inc.php
   ```
3. **Update database credentials**:
   - Set `$db_host` to your database server
   - Set `$db_user` to your database username
   - Set `$db_pass` to your database password
   - Set `$db_name` to your database name
4. **Verify permissions**:
   ```bash
   chmod 600 _website/includes/config.inc.php
   ```

### For Panel Integration

The configuration in `_website/includes/config.inc.php` should match the panel's configuration in `/includes/config.inc.php` to ensure both the website and panel access the same database.

## Security Best Practices

1. **File Permissions**: Set `config.inc.php` to read-only for the web server user
   ```bash
   chmod 600 includes/config.inc.php
   ```

2. **Web Server Configuration**: Ensure the `includes/` directory is not directly accessible via HTTP
   ```apache
   <Directory "/path/to/_website/includes">
       Require all denied
   </Directory>
   ```

3. **Backup Configuration**: Keep a secure backup of your configuration file

## Troubleshooting

### Connection Errors

If you see database connection errors:

1. **Verify credentials** in `includes/config.inc.php`
2. **Check database server** is running
3. **Verify database exists**
4. **Check user permissions** in the database

### File Not Found Errors

If you see errors about missing `config.inc.php`:

1. **Verify the file exists** at `_website/includes/config.inc.php`
2. **Check file permissions** are readable by the web server
3. **Verify path** in `db.php` uses `__DIR__` for relative paths

### Include Errors

If website files can't include `db.php`:

1. **Check file paths** are correct
2. **Verify `db.php`** exists in the `_website/` root
3. **Check PHP include paths** in php.ini if needed

## Migration from Old Configuration

The old `db.php` had hardcoded credentials:
```php
// OLD (hardcoded)
$servername = "panel.iaregamer.com";
$username = "remoteuser";
```

The new `db.php` uses centralized config:
```php
// NEW (centralized)
require_once(__DIR__ . '/includes/config.inc.php');
$servername = $db_host;
$username = $db_user;
```

**No changes needed** to files that include `db.php` - they work automatically with the new configuration.

## Files Using Database Connection

The following files include `db.php` and use the centralized configuration:
- `login.php` - User authentication
- `logout.php` - Session termination
- `cart.php` - Shopping cart
- `order.php` - Order processing
- `serverlist.php` - Server listings
- `adminserverlist.php` - Admin server management
- `test_db_connection.php` - Database testing

## Benefits

1. **Single Source of Truth**: All database settings in one file
2. **Easy Configuration**: Change settings in one place
3. **Portable**: Copy folder and update one config file
4. **Secure**: Configuration separate from code
5. **Maintainable**: Easy to update and manage

## Support

For issues or questions about the configuration, please refer to:
- `includes/README.md` - Detailed information about includes directory
- Main project documentation
- Panel configuration at `/includes/config.inc.php`
