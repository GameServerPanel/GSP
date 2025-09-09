# MySQL Auto-Create Feature

This feature automatically creates MySQL databases for each new game server created through the billing system.

## Required Settings

Add the following settings to your OGP settings table to enable MySQL auto-creation:

### Required Settings:
- `mysql_auto_create` - Set to '1' to enable, '0' to disable
- `mysql_root_user` - MySQL root username for creating databases (e.g., 'remoteuser')
- `mysql_root_password` - MySQL root password 
- `mysql_host` - MySQL server hostname (e.g., 'mysql.iaregamer.com')

### Optional Settings:
- `mysql_port` - MySQL server port (defaults to '3306' if not set)
- `mysql_special_user` - Additional user to grant access (like 'dayzhivemind' in original script)
- `mysql_special_password` - Password for the special user
- `mysql_init_sql_file` - Path to SQL file to import into new databases (e.g., '1.9.0_fresh.sql')
- `mysql_default_server_id` - MySQL server ID from mysql_servers table to track databases in OGP

## How it works:

1. When a new server is created, the system generates:
   - Database name: `server_<home_id>` (e.g., `server_1745`)
   - Database user: Same as database name (e.g., `server_1745`)
   - Random 12-character password

2. The system creates the database and grants privileges:
   - Full privileges to the database user from localhost and any host (%)
   - If mysql_special_user is set, grants full privileges to that user too
   - Flushes privileges

3. If mysql_init_sql_file is specified, imports that SQL file into the new database

4. If mysql_default_server_id is set, adds the database to OGP's mysql_databases table for tracking

## Example Settings SQL:

```sql
INSERT INTO ogp_settings (setting, value) VALUES ('mysql_auto_create', '1');
INSERT INTO ogp_settings (setting, value) VALUES ('mysql_root_user', 'remoteuser');
INSERT INTO ogp_settings (setting, value) VALUES ('mysql_root_password', 'Pkloyn7yvpht!');
INSERT INTO ogp_settings (setting, value) VALUES ('mysql_host', 'mysql.iaregamer.com');
INSERT INTO ogp_settings (setting, value) VALUES ('mysql_port', '3306');
INSERT INTO ogp_settings (setting, value) VALUES ('mysql_special_user', 'dayzhivemind');
INSERT INTO ogp_settings (setting, value) VALUES ('mysql_special_password', 'Pkloyn7yvpht!');
INSERT INTO ogp_settings (setting, value) VALUES ('mysql_init_sql_file', '/path/to/1.9.0_fresh.sql');
INSERT INTO ogp_settings (setting, value) VALUES ('mysql_default_server_id', '1');
```

## Logging

The system logs MySQL database creation events:
- Success: "MYSQL DB CREATED - Database server_<id> created for server <id>"
- Failure: "MYSQL DB CREATION FAILED - <error details>"