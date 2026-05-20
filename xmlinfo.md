# GSP XML Variable and Path Modernization Notes

## Scope reviewed
- Panel XML schema: `Panel/modules/config_games/schema_server_config.xml`
- Panel XML parser: `Panel/modules/config_games/server_config_parser.php`
- Panel CLI resolver: `Panel/includes/api_functions.php` (`get_start_cmd`, `gsp_get_home_layout_paths`, `gsp_convert_layout_for_cli`)
- API provisioning paths: `Panel/ogp_api.php` (`api_user_games` create/clone)
- Billing provisioning path: `Panel/modules/billing/create_servers.php`
- Linux agent replacements: `Agent_Linux/ogp_agent.pl` (`replace_OGP_Env_Vars`)
- Windows agent replacements: `Agent-Windows/ogp_agent.pl` (`replace_OGP_Env_Vars`)
- XML variable helper UI: `Panel/modules/config_games/cli-params.php`

## Canonical layout model
- `HOME_ID`: numeric home ID
- `HOME_PATH`: agent-owned server root (e.g. `/home/gameserver/1518`)
- `GAME_PATH`: customer/game files root (new layout: `/home/gameserver/1518/gamefiles`; legacy: same as `HOME_PATH`)
- `GAME_ROOT`: alias of `GAME_PATH`
- `CONTROL_PATH`: private runtime root (e.g. `/home/gameserver/1518/gsp_control`)
- `GSP_CONTROL_PATH`: alias of `CONTROL_PATH`
- `PID_DIR`: `${CONTROL_PATH}/pids`
- `LOG_DIR`: `${CONTROL_PATH}/logs`
- `BACKUP_PATH`: `${CONTROL_PATH}/backups`

## Layout detection behavior
- New layout detection: `gamefiles` directory exists under `HOME_PATH`.
- Legacy layout detection: `gamefiles` missing; game and control behavior fall back for compatibility.
- No automatic migration for old homes.

## Variable inventory

| Variable | Example | Type | Scope | Old/New | Resolver Location | Notes |
|---|---|---|---|---|---|---|
| GAME_TYPE | `default` | string | Panel | Old | `Panel/includes/api_functions.php` | XML `cli_params` substitution |
| HOSTNAME | `My Server` | string | Panel | Old | `Panel/includes/api_functions.php` | From home name |
| IP | `203.0.113.10` | string | Panel | Old | `Panel/includes/api_functions.php` | Server bind/listen IP |
| MAP | `chernarusplus` | string | Panel | Old | `Panel/includes/api_functions.php` | From saved params |
| PID_FILE | `ogp_game_startup.pid` | string | Panel | Old | `Panel/includes/api_functions.php` | Filename token |
| PLAYERS | `60` | int/string | Panel | Old | `Panel/includes/api_functions.php` | From saved/default max players |
| PORT | `2302` | int/string | Panel | Old | `Panel/includes/api_functions.php` | Main game port |
| QUERY_PORT | `2306` | int/string | Panel | Old | `Panel/includes/api_functions.php` | Protocol-derived |
| BASE_PATH | `/home/gameserver/1518` | path | Panel | Old compat | `Panel/includes/api_functions.php` | Kept as HOME_PATH-compatible base |
| HOME_PATH | `/home/gameserver/1518` | path | Panel+Agent | Old compat | Panel resolver + both agents | Canonical agent home root |
| SAVE_PATH | `/home/gameserver/1518/gamefiles` | path | Panel+Agent | Old compat | Panel resolver + both agents | New layout maps to GAME_PATH |
| OUTPUT_PATH | `/home/gameserver/1518/gsp_control/logs` | path | Panel+Agent | Old compat | Panel resolver + both agents | New layout maps to LOG_DIR |
| USER_PATH | `/home/gameserver/1518/gamefiles` | path | Panel | Old compat | `Panel/includes/api_functions.php` | New layout maps to GAME_PATH |
| CONTROL_PASSWORD | `rcon-pass` | string | Panel | Old | `Panel/includes/api_functions.php` | RCON/control token |
| HOME_ID | `1518` | int/string | Panel+Agent | New | Panel resolver + both agents | Added in schema enum + runtime substitutions |
| GAME_PATH | `/home/gameserver/1518/gamefiles` | path | Panel+Agent | New | Panel resolver + both agents | Legacy fallback to HOME_PATH |
| GAME_ROOT | `/home/gameserver/1518/gamefiles` | path | Panel+Agent | New | Panel resolver + both agents | Alias of GAME_PATH |
| CONTROL_PATH | `/home/gameserver/1518/gsp_control` | path | Panel+Agent | New | Panel resolver + both agents | Private runtime root |
| GSP_CONTROL_PATH | `/home/gameserver/1518/gsp_control` | path | Panel+Agent | New | Panel resolver + both agents | Alias of CONTROL_PATH |
| PID_DIR | `/home/gameserver/1518/gsp_control/pids` | path | Panel+Agent | New | Panel resolver + both agents | Runtime pid folder |
| LOG_DIR | `/home/gameserver/1518/gsp_control/logs` | path | Panel+Agent | New | Panel resolver + both agents | Runtime logs folder |
| BACKUP_PATH | `/home/gameserver/1518/gsp_control/backups` | path | Panel+Agent | New | Panel resolver + both agents | Runtime backup folder |

## XML schema usage
- `schema_server_config.xml` now allows new `cli_param` IDs:
  - `HOME_ID`, `GAME_PATH`, `GAME_ROOT`, `CONTROL_PATH`, `GSP_CONTROL_PATH`, `PID_DIR`, `LOG_DIR`, `BACKUP_PATH`
- Existing IDs remain valid.
- Added optional `companion_programs` section:
  - `<companion_programs><program ... /></companion_programs>`
  - Non-breaking, optional, and intended for future sidecar tools.

## `cli-params.php` status
- Updated variable UI list to include new path variables.
- Added compatibility guidance:
  - `HOME_PATH` = server root
  - `GAME_PATH/GAME_ROOT` = gamefiles root
  - `CONTROL_PATH/GSP_CONTROL_PATH` = private runtime root

## Server params / pre_start / post_install behavior
- `server_params`: resolved in Panel (`get_start_cmd`) during `%VAR%` substitutions.
- `pre_start` and runtime command fragments: passed to agents and additionally processed by `replace_OGP_Env_Vars`.
- `post_install/post_update` scripts: mostly install/update pipeline driven; path modernization now provides stable control/log dirs for script targeting.
- `custom_fields` / managed file options: unaffected structurally; continue to rely on parser + replace-text flows.

## Agent substitution behavior
- Linux and Windows agents now replace:
  - `%HOME_ID%`, `%HOME_PATH%`, `%BASE_PATH%`, `%GAME_PATH%`, `%GAME_ROOT%`
  - `%CONTROL_PATH%`, `%GSP_CONTROL_PATH%`, `%PID_DIR%`, `%LOG_DIR%`, `%BACKUP_PATH%`
  - `%SAVE_PATH%`, `%OUTPUT_PATH%`
- Existing `{OGP_HOME_DIR}` remains supported.
- Added aliases: `{OGP_GAME_DIR}`, `{OGP_GAME_ROOT}`, `{OGP_CONTROL_DIR}`.

## Layout examples
### Legacy home (no `gamefiles/`)
- `HOME_PATH=/home/gameserver/1518`
- `GAME_PATH=/home/gameserver/1518`
- `CONTROL_PATH=/home/gameserver/1518/gsp_control`
- `SAVE_PATH=/home/gameserver/1518`
- `OUTPUT_PATH=/home/gameserver/1518`

### New layout home (`gamefiles/` present)
- `HOME_PATH=/home/gameserver/1518`
- `GAME_PATH=/home/gameserver/1518/gamefiles`
- `CONTROL_PATH=/home/gameserver/1518/gsp_control`
- `PID_DIR=/home/gameserver/1518/gsp_control/pids`
- `LOG_DIR=/home/gameserver/1518/gsp_control/logs`
- `BACKUP_PATH=/home/gameserver/1518/gsp_control/backups`
- `SAVE_PATH=/home/gameserver/1518/gamefiles`
- `OUTPUT_PATH=/home/gameserver/1518/gsp_control/logs`

## Provisioning updates
- `ogp_api.php` create/clone now ensures:
  - `gamefiles/`
  - `gsp_control/pids`
  - `gsp_control/logs`
  - `gsp_control/backups`
- Billing provisioning flow ensures same layout and prefers `GAME_PATH` as FTP root when `gamefiles/` exists.

## Steam Workshop module notes
- Standalone `steam_workshop` navigation routes are disabled.
- Legacy monitor button now routes users to Server Content Manager (`addonsmanager`).
- Compatibility DB/helpers retained; unified workshop UX is SCM.

## DayZ XML review (safe-change preparation)
### Files found
- `Panel/modules/config_games/server_configs/dayz_win64.xml`
- `Panel/modules/config_games/server_configs/dayz_epoch_mod_win32.xml`
- `Panel/modules/config_games/server_configs/dayz_arma2co_win32.xml`
- `Panel/modules/config_games/server_configs/dayz_arma2co_linux.xml`

### Current path variable usage
- `dayz_win64.xml`: `cli_param id="HOME_PATH"`
- `dayz_epoch_mod_win32.xml`: replace text key `home_path` for BEC `BePath`
- `dayz_arma2co_win32.xml`: replace text key `home_path` for BEC `BePath`
- `dayz_arma2co_linux.xml`: no direct HOME_PATH/BASE_PATH/SAVE_PATH/OUTPUT_PATH usage in current file

### Safe update guidance
- Keep DayZ replace-text key mappings unchanged unless corresponding replacement key support is confirmed for `game_path` in all call sites.
- Favor template updates that use schema-backed CLI IDs (`GAME_PATH`, `CONTROL_PATH`) where the XML currently uses CLI params.
- For BEC `BePath`, validate runtime replacement source first (legacy `home_path` key path still expected in existing flows).

## Migration plan (DayZ-first)
1. Add temporary diagnostics for replace-text key resolution (`home_path` vs `game_path`) on DayZ/BEC flows.
2. Validate both legacy and new-layout homes in staging.
3. Update DayZ XMLs in small, reversible patches.
4. Roll out same strategy to non-DayZ XMLs once replacement-key coverage is confirmed.
