
srvID=${PWD##*/}
dbID=server_${srvID}



mysql -uremoteuser -pPkloyn7yvpht! -hmysql.iaregamer.com -e "CREATE DATABASE IF NOT EXISTS ${dbID}"
mysql -uremoteuser -pPkloyn7yvpht! -hmysql.iaregamer.com -e "GRANT ALL ON ${dbID}.* TO '${dbID}'@'localhost' IDENTIFIED BY '${dbPass}'"
mysql -uremoteuser -pPkloyn7yvpht! -hmysql.iaregamer.com -e "GRANT ALL ON ${dbID}.* TO '${dbID}'@'%' IDENTIFIED BY '${dbPass}'"
mysql -uremoteuser -pPkloyn7yvpht! -hmysql.iaregamer.com -e "FLUSH PRIVILEGES;"

mysql -uremoteuser -pPkloyn7yvpht! -hmysql.iaregamer.com -e "DELETE FROM panel.ogp_mysql_databases WHERE db_user = '${dbID}'"
mysql -uremoteuser -pPkloyn7yvpht! -hmysql.iaregamer.com -e "INSERT INTO panel.ogp_mysql_databases(mysql_server_id, home_id, db_user, db_passwd, db_name, enabled) VALUES (1,${srvID},'${dbID}','${dbPass}','${dbID}',1)"
