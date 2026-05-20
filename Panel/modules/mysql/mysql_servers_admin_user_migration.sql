-- GSP MySQL module migration: support configurable admin user and longer admin password.
-- Execute against the panel database (gsp_ prefix shown below).

ALTER TABLE `gsp_mysql_servers`
  ADD COLUMN `mysql_admin_user` varchar(64) NOT NULL DEFAULT 'root' AFTER `mysql_port`;

ALTER TABLE `gsp_mysql_servers`
  MODIFY COLUMN `mysql_root_passwd` varchar(255) NULL;

-- Optional: set current rows to your preferred admin user (example: remoteuser)
-- UPDATE `gsp_mysql_servers` SET `mysql_admin_user` = 'remoteuser' WHERE `mysql_admin_user` = 'root' OR `mysql_admin_user` = '';
