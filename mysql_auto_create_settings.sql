-- MySQL Auto-Create Settings Configuration
-- Execute these SQL statements to configure MySQL auto-creation for game servers

-- Enable MySQL auto-creation (set to '1' to enable, '0' to disable)
INSERT INTO ogp_settings (setting, value) VALUES ('mysql_auto_create', '1')
ON DUPLICATE KEY UPDATE value = '1';

-- MySQL connection settings (REQUIRED - update these with your actual MySQL server details)
INSERT INTO ogp_settings (setting, value) VALUES ('mysql_root_user', 'remoteuser')
ON DUPLICATE KEY UPDATE value = 'remoteuser';

INSERT INTO ogp_settings (setting, value) VALUES ('mysql_root_password', 'Pkloyn7yvpht!')
ON DUPLICATE KEY UPDATE value = 'Pkloyn7yvpht!';

INSERT INTO ogp_settings (setting, value) VALUES ('mysql_host', 'mysql.iaregamer.com')
ON DUPLICATE KEY UPDATE value = 'mysql.iaregamer.com';

-- Optional: MySQL port (defaults to 3306 if not set)
INSERT INTO ogp_settings (setting, value) VALUES ('mysql_port', '3306')
ON DUPLICATE KEY UPDATE value = '3306';

-- Optional: Special user that gets access to all created databases (like dayzhivemind in original script)
INSERT INTO ogp_settings (setting, value) VALUES ('mysql_special_user', 'dayzhivemind')
ON DUPLICATE KEY UPDATE value = 'dayzhivemind';

INSERT INTO ogp_settings (setting, value) VALUES ('mysql_special_password', 'Pkloyn7yvpht!')
ON DUPLICATE KEY UPDATE value = 'Pkloyn7yvpht!';

-- Optional: Path to SQL file to import into each new database (like 1.9.0_fresh.sql)
-- INSERT INTO ogp_settings (setting, value) VALUES ('mysql_init_sql_file', '/path/to/1.9.0_fresh.sql')
-- ON DUPLICATE KEY UPDATE value = '/path/to/1.9.0_fresh.sql';

-- Optional: MySQL server ID from ogp_mysql_servers table to track databases in OGP
-- (Set this to the ID of your MySQL server in the mysql module if you want OGP to track the databases)
INSERT INTO ogp_settings (setting, value) VALUES ('mysql_default_server_id', '1')
ON DUPLICATE KEY UPDATE value = '1';

-- To verify settings, run:
-- SELECT * FROM ogp_settings WHERE setting LIKE 'mysql_%';