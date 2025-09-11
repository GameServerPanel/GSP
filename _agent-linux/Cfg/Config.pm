%Cfg::Config = (
	logfile => '/home/gameserver/OGP/ogp_agent.log',
	listen_port  => '12679',
	listen_ip => '0.0.0.0',
	version => 'v1.4',
	key => 'Mvemjsu9p',
	steam_license => 'Accept',
	sudo_password => 'Inc0rrect!',
	web_admin_api_key => '{your_admin_ogp_web_api_key_here}',
	web_api_url => '{your_url_to_ogp_api.php}',
	steam_dl_limit => '0',
	# Resource stats database configuration
	stats_db_host => '127.0.0.1',
	stats_db_user => 'panel_user',
	stats_db_pass => 'REPLACE_ME',
	stats_db_name => 'panel_database',
	stats_table_prefix => 'gsp_',
	stats_frequency_minutes => '5',
	);
