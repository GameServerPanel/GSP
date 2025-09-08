$test_id = 1362;
				$db->query( "DROP USER 'server_" .$test_id ."'@localhost'");
mysql -uremoteuser -pDrV75Uyyxr9VFVVt -hmysql.iaregamer.com -e "DROP USER server_'${test_id}'"
