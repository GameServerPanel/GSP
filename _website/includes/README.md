# Website Includes Directory

This directory contains configuration and shared files for the standalone _website folder.

## config.inc.php

Central database configuration file for the website. This file contains the database connection settings that are used by all website PHP files through the `db.php` file.

**Important:** The values in this file should match the panel's database configuration in `/includes/config.inc.php` to ensure the website can access the same database as the panel.

### Configuration Variables

- `$db_host` - Database server hostname
- `$db_user` - Database username
- `$db_pass` - Database password
- `$db_name` - Database name
- `$table_prefix` - Table prefix (default: "ogp_")
- `$db_type` - Database type (default: "mysql")

### Usage

The website files include `db.php`, which in turn loads this configuration file:

```php
require_once('db.php');  // db.php loads includes/config.inc.php
```

This centralizes database credentials in one place, making the website easier to configure and maintain as a standalone site.
