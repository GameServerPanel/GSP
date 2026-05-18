<?php
/*
 *
 * GSP - Game Server Panel (a heavily customized fork of OGP maintained by WDS)
 *
 * Server Content Category Map
 * ─────────────────────────────────────────────────────────────────────────────
 * This file is the single source of truth for all Server Content types.
 * It maps internal addon_type DB values to human-readable display labels.
 *
 * BACKWARD COMPATIBILITY:
 *   The three original types (plugin, mappack, config) are preserved exactly
 *   as they exist in the addons table.  Do not rename or remove them.
 *
 * ADDING NEW TYPES:
 *   1. Add the key/label pair below.
 *   2. Ensure the DB column addon_type is VARCHAR(32) (migration db_version 2
 *      in module.php handles this automatically on the next module update).
 *   3. No other code changes are required for the type to appear in the admin
 *      "Create Server Content Item" form.
 *
 * PLANNED INSTALL METHODS (see SERVER_CONTENT_ROADMAP.md for details):
 *   download_zip   – download a .zip/.tar.gz and extract into the server path
 *   download_file  – download a single file into the server path
 *   post_script    – run only a post-install bash script (no download)
 *   steam_workshop – install/update via Steam Workshop item IDs through agent
 *   minecraft_jar  – download a Minecraft server jar and update startup config
 *   profile_copy   – copy a profile directory tree into the server home
 *
 */

/**
 * Returns the full Server Content category map.
 *
 * Keys   : addon_type values stored in OGP_DB_PREFIXaddons.addon_type
 * Values : Human-readable display label shown in admin and user UI
 *
 * @return array<string,string>
 */
function get_server_content_categories()
{
    return array(
        // ── Original types (must remain for backward compatibility) ──────────
        'plugin'   => 'Plugins / Mods',
        'mappack'  => 'Map Packs',
        'config'   => 'Config Packs',

        // ── Extended types (require addon_type VARCHAR(32) – db_version 2) ──
        'version'  => 'Server Versions',     // e.g. Minecraft jar switcher
        'modpack'  => 'Modpacks',            // e.g. CurseForge / ATLauncher packs
        'workshop' => 'Workshop Content',    // Steam Workshop item bundles
        'script'   => 'Scripted Installer',  // Admin-defined install-only scripts
        'profile'  => 'Server Profiles',     // Full profile: configs + mods + scripts
    );
}

/**
 * Returns only the original three types that existed before db_version 2.
 * Use this when you need to restrict to legacy values, e.g. for
 * installs that have not yet run the VARCHAR(32) migration.
 *
 * @return array<string,string>
 */
function get_legacy_addon_types()
{
    return array(
        'plugin'  => 'Plugins / Mods',
        'mappack' => 'Map Packs',
        'config'  => 'Config Packs',
    );
}

/**
 * Returns an ordered list of addon_type keys only (no labels).
 * Useful as a whitelist for input validation.
 *
 * @return string[]
 */
function get_server_content_type_keys()
{
    return array_keys(get_server_content_categories());
}
