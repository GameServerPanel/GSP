# Protocol Upgrade Review (Phase 1, safe path)

## Scope reviewed
- `Panel/protocol/lgsl/`
- `Panel/protocol/GameQ/`
- `Panel/modules/gamemanager/server_monitor.php`
- `Panel/modules/gamemanager/ref_servermonitor.php`
- `Panel/modules/gamemanager/start_server.php`
- `Panel/modules/gamemanager/restart_server.php`
- `Panel/modules/gamemanager/mini_start.php`
- `Panel/modules/dashboard/query_ref.php`
- `Panel/modules/config_games/server_configs/*.xml`

## Current protocol folders
- `Panel/protocol/lgsl/`
  - Legacy LGSL implementation and protocol map (`lgsl_protocol.php`).
  - Monitor helper (`LGSLMonitor.php`).
  - Player list rendering helper (`functions.php`).
- `Panel/protocol/GameQ/`
  - Modern namespaced GameQ implementation with PSR-4 autoloader.
  - Monitor helper (`GameQMonitor.php`) + player list rendering helper (`functions.php`).
  - Very large protocol coverage in `Protocols/`.
  - Also contains legacy-looking `gameq/` subtree alongside modern files (technical debt signal).

## How LGSL is currently called
- Monitor refresh path:
  - `gamemanager/ref_servermonitor.php` loads `protocol/lgsl/LGSLMonitor.php` when XML protocol is `lgsl`.
  - `LGSLMonitor.php` calls `lgsl_query_live(...)`, handles panel query cache (`getServerStatusCache`/`saveServerStatusCache`), and sets `$status/$map/$players/$player_list`.
- Start/restart detection path:
  - `gamemanager/start_server.php` and `restart_server.php` call `lgsl_query_live(..., "sa")` after process launch to decide if server is considered running.
- Quick checks:
  - `gamemanager/mini_start.php` and other helpers call `lgsl_port_conversion(...)` and `lgsl_query_live(...)`.
- Connection links:
  - `server_monitor.php` and dsi/lgsl image flows use `lgsl_port_conversion(...)` and `lgsl_software_link(...)`.

## GameQ status (usable vs incomplete)
- Present and actively used in monitor/start/restart flows (`GameQMonitor.php`, `start_server.php`, `restart_server.php`, `dashboard/query_ref.php`).
- Usable for configured XML entries (`protocol=gameq` + `gameq_query_name`).
- Inconsistencies/risks:
  - Mixed API usage exists in module code (`process()` and `requestData()` patterns).
  - Legacy and modern GameQ structures coexist under `Panel/protocol/GameQ/`.
  - GameQ is integrated, but implementation consistency is incomplete and should be normalized before broad protocol migration.

## Files that map game configs to protocol names
- Primary source of protocol selection:
  - `Panel/modules/config_games/server_configs/*.xml` fields:
    - `<protocol>`
    - `<lgsl_query_name>`
    - `<gameq_query_name>`
- Supporting editor/schema surfaces:
  - `Panel/modules/config_games/config_servers.php` (schema order includes protocol tags).
  - `Panel/modules/config_games/xml_tag_descriptions.php` (documents protocol fields).
  - `Panel/modules/config_games/xml_config_creator.php` (protocol selector and query-name population).

## Where player list parsing happens
- LGSL:
  - Query payload from `lgsl_query_live(...)` in `lgsl_protocol.php`.
  - Player table rendering in `protocol/lgsl/functions.php::print_player_list(...)`.
- GameQ:
  - Query payload from `GameQMonitor.php`.
  - Player table rendering and field normalization in `protocol/GameQ/functions.php::print_player_list_gameq(...)`.

## Where map/player/server status data is returned
- LGSL live payload shape: `b/s/e/p/t` arrays from `lgsl_query_live(...)`:
  - status: `b.status`
  - map/name/player counts/password: `s.*`
  - player list: `p`
  - extras (including some bot fields): `e`
- GameQ normalized payload (after `normalise` filter):
  - status: `server.gq_online`
  - map: `server.gq_mapname`
  - player counts: `server.gq_numplayers`, `server.gq_maxplayers`
  - player list: `server.players`

## Known problems (Phase 1 findings)
- Query invocation is duplicated in multiple modules (monitor/start/restart/dashboard/image modules), increasing drift risk.
- Start detection currently combines process + query checks but does not explicitly represent a `starting` state in monitor output.
- LGSL uses hard exits for invalid parameters (`lgsl_query_live`), which is risky for direct callers without guard logic.
- GameQ integration style is not fully standardized across all call sites.
- Existing cache model is spread across current status cache and module-specific image caches, without one normalized query cache contract.

## New wrapper prepared in this phase
- Added `Panel/protocol/gsp_query.php`.
- Introduces `gsp_query_server($server_info, $options = [])` normalized result contract.
- Keeps default provider on LGSL (`lgsl_legacy`) for safety.
- Adds provider concept placeholders (no broad provider switch in this phase):
  - `lgsl_legacy`
  - `gameq`
  - `xpaw_source_query`
  - `minecraft_query`
  - `custom_script`

## Proposed query cache table (planning only, no migration applied)
```sql
CREATE TABLE IF NOT EXISTS OGP_DB_PREFIXserver_query_cache (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  home_id INT NOT NULL,
  ip VARCHAR(64) NOT NULL,
  port INT NOT NULL,
  query_port INT NULL,
  protocol VARCHAR(64) NULL,
  provider VARCHAR(64) NULL,
  online TINYINT(1) NOT NULL DEFAULT 0,
  server_name VARCHAR(255) NULL,
  map_name VARCHAR(128) NULL,
  players INT NULL,
  max_players INT NULL,
  bots INT NULL,
  passworded TINYINT(1) NULL,
  latency_ms INT NULL,
  player_list_json MEDIUMTEXT NULL,
  raw_json MEDIUMTEXT NULL,
  last_query_at DATETIME NULL,
  last_success_at DATETIME NULL,
  last_error TEXT NULL,
  UNIQUE KEY uniq_home_query (home_id),
  KEY idx_last_query_at (last_query_at),
  KEY idx_online (online)
);
```

Cache TTL target: **60 seconds** default.

## Server start detection improvement plan (no behavior change yet)
- After start command, mark status as **starting**.
- Poll on a short interval until timeout:
  1. Check agent process state.
  2. Check network port open.
  3. Check query response (if protocol supported).
- Final states:
  - Process + query OK: **Online**
  - Process OK but query unavailable: **Running, query unavailable**
  - Process missing at timeout: **Failed to start**

## Game Manager integration plan (`server_monitor.php`)
Future `Live Server Status` panel should include:
- State: Online / Offline / Starting / Running query unavailable
- Server name
- Current map
- Players / max players
- Player list
- Query latency
- Last query time
- Banner preview
- “Get banner code” action

## Admin query debug/test page plan
Future page: `Panel/protocol/query_test.php` (admin-only)
- Inputs: IP, port, query port, protocol, provider, timeout
- Outputs: normalized result, raw result, errors
- Security:
  - admin-only access gate
  - CSRF for submit actions
  - request limits / timeout caps
  - no anonymous/public proxy behavior

## Recommended next phase
1. Switch one low-risk monitor path to read `gsp_query_server()` output in parallel with existing behavior (feature-flag style).
2. Standardize GameQ call style (single API usage pattern) and document supported protocol mappings.
3. Add normalized cache write/read adapter (without removing existing caches yet).
4. Add explicit start-state model and timeout policy constants.
5. Begin unified banner module implementation against normalized cache payloads.

## Safety statement
- No protocol engines were removed.
- LGSL remains in place.
- GameQ remains in place.
- Existing server monitor behavior remains intact in this phase.
