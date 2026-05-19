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
        'file_download'      => 'Downloadable Mod',
        'workshop_item'      => 'Steam Workshop Item',
        'config_edit'        => 'Configuration Package',
        'scripted_installer' => 'Scripted Installer',
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

function scm_get_addon_type_from_install_method($install_method)
{
    $install_method = trim((string)$install_method);
    $map = array(
        'download_zip'   => 'file_download',
        'steam_workshop' => 'workshop_item',
        'config_edit'    => 'config_edit',
        'post_script'    => 'scripted_installer',
    );
    return isset($map[$install_method]) ? $map[$install_method] : 'file_download';
}

function scm_normalize_addon_type($addon_type, $install_method = '')
{
    $addon_type = trim((string)$addon_type);
    $categories = get_server_content_categories();
    if (isset($categories[$addon_type])) {
        return $addon_type;
    }
    if ($addon_type === 'workshop') {
        return 'workshop_item';
    }
    if ($addon_type === 'script') {
        return 'scripted_installer';
    }
    if ($addon_type === 'config') {
        return 'config_edit';
    }
    return scm_get_addon_type_from_install_method($install_method);
}
