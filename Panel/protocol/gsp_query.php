<?php
/*
 * GSP query wrapper (Phase 1 scaffolding)
 *
 * Normalizes server query results while keeping LGSL as the default provider.
 * This file intentionally avoids changing existing monitor paths in this phase.
 */

if (!function_exists('gsp_query_provider_names')) {
	function gsp_query_provider_names()
	{
		return array(
			// LGSL remains default for legacy/older game coverage.
			'lgsl_legacy',
			// TODO: Use for games where current GameQ support is proven reliable.
			'gameq',
			// TODO: Prefer for modern Source/Steam query games in a later phase.
			'xpaw_source_query',
			// TODO: Prefer dedicated Minecraft query handling in a later phase.
			'minecraft_query',
			// TODO: Allow custom scripts for unusual game protocols.
			'custom_script',
		);
	}
}

if (!function_exists('gsp_query_default_result')) {
	function gsp_query_default_result()
	{
		return array(
			'success' => false,
			'online' => false,
			'provider' => 'lgsl_legacy',
			'protocol' => '',
			'game' => '',
			'server_name' => '',
			'map' => '',
			'players' => 0,
			'max_players' => 0,
			'bots' => 0,
			'passworded' => false,
			'latency_ms' => null,
			'address' => '',
			'port' => 0,
			'query_port' => 0,
			'player_list' => array(),
			'raw' => array(),
			'error' => '',
		);
	}
}

if (!function_exists('gsp_query_normalize_player_list')) {
	function gsp_query_normalize_player_list($players)
	{
		$normalized = array();
		foreach ((array)$players as $player) {
			$normalized[] = array(
				'name' => isset($player['name']) ? (string)$player['name'] : '',
				'score' => isset($player['score']) ? (int)$player['score'] : 0,
				'time' => isset($player['time']) ? $player['time'] : '',
				'ping' => isset($player['ping']) ? (int)$player['ping'] : 0,
				'raw' => (array)$player,
			);
		}
		return $normalized;
	}
}

if (!function_exists('gsp_query_server')) {
	function gsp_query_server($server_info, $options = array())
	{
		$result = gsp_query_default_result();
		$server = (array)$server_info;
		$options = (array)$options;

		$provider = isset($options['provider']) ? (string)$options['provider'] : (isset($server['query_provider']) ? (string)$server['query_provider'] : 'lgsl_legacy');
		$result['provider'] = $provider;

		$ip = isset($server['ip']) ? trim((string)$server['ip']) : '';
		$port = isset($server['port']) ? (int)$server['port'] : 0;
		$query_ip = $ip;
		if (!empty($server['use_nat']) && !empty($server['agent_ip'])) {
			$query_ip = trim((string)$server['agent_ip']);
		}

		$result['address'] = ($ip !== '' && $port > 0) ? $ip . ':' . $port : '';
		$result['port'] = $port;

		if ($provider !== 'lgsl_legacy') {
			$result['error'] = "Query provider not implemented yet: {$provider}";
			return $result;
		}

		$query_name = '';
		if (isset($server['lgsl_query_name'])) {
			$query_name = (string)$server['lgsl_query_name'];
		} elseif (isset($server['query_name'])) {
			$query_name = (string)$server['query_name'];
		}
		$query_name = trim($query_name);
		$result['protocol'] = $query_name;

		if ($query_name === '') {
			$result['error'] = 'Missing LGSL query name.';
			return $result;
		}

		if ($query_ip === '' || preg_match("/[^0-9a-z\\.\\-\\[\\]\\:]/i", $query_ip)) {
			$result['error'] = 'Invalid query IP/hostname.';
			return $result;
		}

		if ($port <= 0) {
			$result['error'] = 'Invalid server port.';
			return $result;
		}

		require_once __DIR__ . '/lgsl/lgsl_protocol.php';
		$protocols = lgsl_protocol_list();
		if (!isset($protocols[$query_name])) {
			$result['error'] = "Unsupported LGSL protocol type: {$query_name}";
			return $result;
		}

		list($c_port, $default_q_port, $s_port) = lgsl_port_conversion($query_name, $port, "", "");
		$q_port = isset($server['query_port']) && (int)$server['query_port'] > 0 ? (int)$server['query_port'] : (int)$default_q_port;
		$result['query_port'] = $q_port;

		if ($q_port <= 0) {
			$result['error'] = 'Invalid query port for LGSL query.';
			return $result;
		}

		$raw = lgsl_query_live($query_name, $query_ip, $c_port, $q_port, $s_port, "sep");
		if (!is_array($raw) || !isset($raw['b']) || !isset($raw['b']['status'])) {
			$result['error'] = 'LGSL query returned an invalid payload.';
			return $result;
		}

		$result['raw'] = $raw;
		$result['success'] = true;
		$result['online'] = ((string)$raw['b']['status'] === '1' || (int)$raw['b']['status'] === 1);
		$result['game'] = isset($raw['s']['game']) ? (string)$raw['s']['game'] : '';
		$result['server_name'] = isset($raw['s']['name']) ? (string)$raw['s']['name'] : '';
		$result['map'] = isset($raw['s']['map']) ? (string)$raw['s']['map'] : '';
		$result['players'] = isset($raw['s']['players']) ? (int)$raw['s']['players'] : 0;
		$result['max_players'] = isset($raw['s']['playersmax']) ? (int)$raw['s']['playersmax'] : 0;
		$result['bots'] = isset($raw['e']['bots']) ? (int)$raw['e']['bots'] : 0;
		$result['passworded'] = !empty($raw['s']['password']);
		$result['latency_ms'] = isset($raw['t']['ping']) ? (int)$raw['t']['ping'] : null;
		$result['player_list'] = isset($raw['p']) ? gsp_query_normalize_player_list($raw['p']) : array();

		return $result;
	}
}
?>
