<?php
/*
 * OGP / GSP – Config Games XML tag descriptions
 *
 * Each entry maps an XML element name to an array with:
 *   'desc'    – Short human-readable description of what the element does.
 *   'options' – (optional) array of allowed values with labels.
 *   'example' – (optional) A short illustrative value.
 *
 * Sourced from the OGP XML documentation and the schema in
 * modules/config_games/schema_server_config.xml.
 */

/**
 * Return the description map for all known game-config XML tags.
 *
 * @return array<string, array{desc: string, options?: array<string,string>, example?: string}>
 */
function config_games_tag_descriptions(): array
{
    return [
        'game_key' => [
            'desc'    => 'Unique lowercase identifier for this game configuration. Used internally to link homes, mods, and Workshop profiles.',
            'example' => 'arma3_linux64',
        ],
        'protocol' => [
            'desc'    => 'Query protocol used to monitor server status.',
            'options' => [
                'lgsl'  => 'LGSL – Lightweight Game Server Library',
                'gameq' => 'GameQ – PHP-based multi-protocol server query',
                ''      => 'None (server is not queryable)',
            ],
        ],
        'lgsl_query_name' => [
            'desc'    => 'LGSL type string identifying the game in the LGSL library (only used when protocol is "lgsl").',
            'example' => 'arma3',
        ],
        'gameq_query_name' => [
            'desc'    => 'GameQ protocol name identifying the game (only used when protocol is "gameq").',
            'example' => 'arma3',
        ],
        'installer' => [
            'desc'    => 'Installer/updater helper used to download and update game server files.',
            'options' => [
                'steam'    => 'SteamCMD / HLDSUpdateTool',
                'steamcmd' => 'SteamCMD (explicit)',
                ''         => 'None (manual installation)',
            ],
        ],
        'game_name' => [
            'desc'    => 'Display name shown in the panel UI for this game.',
            'example' => 'Arma 3',
        ],
        'server_exec_name' => [
            'desc'    => 'Filename of the server executable (without path). The panel uses this to detect whether the server process is running.',
            'example' => 'arma3server_x64',
        ],
        'query_port' => [
            'desc'    => 'Port offset added to the main port to obtain the query port used for status monitoring.',
            'example' => '1',
        ],
        'cli_template' => [
            'desc'    => 'Command-line template used to launch the server. Supports placeholder tokens such as %ip%, %port%, %slots%.',
            'example' => '-ip=%ip% -port=%port% -maxPlayers=%slots%',
        ],
        'cli_params' => [
            'desc'    => 'Container for individual <param> child elements that define configurable launch parameters shown in the panel UI.',
        ],
        'reserve_ports' => [
            'desc'    => 'Additional sequential ports (beyond the main port) that must be reserved for this server instance.',
            'example' => '3',
        ],
        'cli_allow_chars' => [
            'desc'    => 'Extra characters that are safe to include in command-line parameter values (extends the default whitelist).',
            'example' => '@_-.',
        ],
        'maps_location' => [
            'desc'    => 'Path inside the server directory where map files are stored. The panel uses this to populate map-selection dropdowns.',
            'example' => 'Maps',
        ],
        'map_list' => [
            'desc'    => 'Hardcoded comma-separated list of map names when maps cannot be read from disk.',
        ],
        'console_log' => [
            'desc'    => 'Relative path to the server log file that the panel displays in the console viewer.',
            'example' => 'logs/console.log',
        ],
        'exe_location' => [
            'desc'    => 'Subdirectory within the server installation where the executable resides. Leave empty if the executable is at the root.',
            'example' => 'Binaries/Win64',
        ],
        'max_user_amount' => [
            'desc'    => 'Maximum player slots the panel will allow for this game type. Enforced in the panel UI when creating or editing a server.',
            'example' => '64',
        ],
        'control_protocol' => [
            'desc'    => 'Protocol used to send RCON/admin commands to the running server.',
            'options' => [
                'rcon'    => 'RCON (remote console)',
                'rconhl'  => 'Half-Life RCON',
                ''        => 'None',
            ],
        ],
        'control_protocol_type' => [
            'desc'    => 'Sub-type or variant that further qualifies the control protocol.',
            'example' => 'rcon_password',
        ],
        'mods' => [
            'desc'    => 'Container element for mod definitions. Child <mod> elements describe each variant of the server configuration.',
        ],
        'replace_texts' => [
            'desc'    => 'Container for text-replacement rules applied to config files on the server.',
        ],
        'server_params' => [
            'desc'    => 'Additional fixed parameters appended verbatim to the server launch command.',
        ],
        'custom_fields' => [
            'desc'    => 'Container for admin-defined extra fields displayed in the server control panel.',
        ],
        'list_players_command' => [
            'desc'    => 'RCON command sent to the server to retrieve the current player list.',
            'example' => 'players',
        ],
        'player_info_regex' => [
            'desc'    => 'Regular expression used to parse each line of the player-list response into name and other fields.',
        ],
        'player_info' => [
            'desc'    => 'Defines which capture groups from player_info_regex map to player attributes (name, score, ping, etc.).',
        ],
        'player_commands' => [
            'desc'    => 'Container for RCON commands that can be executed on individual players (kick, ban, etc.).',
        ],
        'pre_install' => [
            'desc'    => 'Shell/batch script executed on the agent BEFORE the game server files are installed or updated.',
        ],
        'post_install' => [
            'desc'    => 'Shell/batch script executed on the agent AFTER the game server files are installed or updated.',
        ],
        'pre_start' => [
            'desc'    => 'Shell/batch script executed on the agent BEFORE the game server process is started.',
        ],
        'post_start' => [
            'desc'    => 'Shell/batch script executed on the agent AFTER the game server process has started.',
        ],
        'environment_variables' => [
            'desc'    => 'Container for environment variables that are set in the server process environment at startup.',
        ],
        'lock_files' => [
            'desc'    => 'Files that should not be overwritten when updating the server. Paths are relative to the server installation directory.',
        ],
        'configuration_files' => [
            'desc'    => 'Container listing server configuration files that the panel can display and edit via the config-file editor.',
        ],
    ];
}
