<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2018 The OGP Development Team
 *
 * http://www.opengamepanel.org/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */

$lang_modules = array();

// Some modules do not follow the established pattern and therefore don't have the functions loaded :(
if(file_exists('includes/functions.php'))
	require_once('includes/functions.php');

if(file_exists('functions.php'))
	require_once('functions.php');


function add_lang_module($lang_module)
{
    global $lang_modules;
    array_push($lang_modules,$lang_module);
    // Need to reload langs if module is added after the first ogpLang call.
    ogpLang();
}

function ogpLang()
{
    global $lang_modules;
	$locale_files = makefilelist("lang/", ".|..|.svn", true, "folders");
	if( isset($_REQUEST['lang']) and !in_array($_REQUEST['lang'],$locale_files) )
		unset($_REQUEST['lang'], $_GET['lang'], $_POST['lang']);
    // For debugging purposes we allow lang change from url.
    if ( isset($_REQUEST['lang']) && is_dir("lang/".$_REQUEST['lang']) )
        define("LANG_DIR","lang/".$_REQUEST['lang']);
    // Check that the language exists
    else if ( !empty($_SESSION['users_lang']) && is_dir("lang/".$_SESSION['users_lang']) )
        define("LANG_DIR","lang/".$_SESSION['users_lang']);
    // ... if lang does not exist lets use english.
    else if ( isset($GLOBALS['panel_language']) && is_dir("lang/".$GLOBALS['panel_language']) )
        define("LANG_DIR","lang/".$GLOBALS['panel_language']);
    else
        define("LANG_DIR","lang/English/");

    $files = glob(LANG_DIR."/*.php");

    // If we are inside some module also the modules language file
    // needs to be loaded.
    if ( isset( $_REQUEST['m'] ) )
	{
        if ( preg_match('[/|\\|;|\.]',$_REQUEST['m']) !== 0 )
			return;
        array_push($lang_modules,$_REQUEST['m']);
	}

    $modules = preg_grep("/.*/",get_included_files());

    foreach ((array)$lang_modules as $lang_module)
    {
        $lang_file = LANG_DIR."/modules/".$lang_module.".php";
        if ( is_file($lang_file) )
            array_push($files,$lang_file);
    }

    foreach ((array)$files as $file_name)
    {
        // Load the actual language files.
        include_once($file_name);
    }
}

function ogp_load_english_fallbacks()
{
    static $coreLoaded = false;
    static $loadedModules = array();
    global $lang_modules;

    $englishDir = "lang/English";
    if (!is_dir($englishDir)) {
        return;
    }

    if ($coreLoaded === false) {
        $coreFiles = glob($englishDir . "/*.php");
        if (is_array($coreFiles)) {
            foreach ($coreFiles as $coreFile) {
                ogp_include_lang_file_safely($coreFile);
            }
        }
        $coreLoaded = true;
    }

    $modulesToLoad = array();
    if (isset($_REQUEST['m']) && $_REQUEST['m'] !== '') {
        $modulesToLoad[] = $_REQUEST['m'];
    }
    foreach ((array)$lang_modules as $moduleName) {
        $modulesToLoad[] = $moduleName;
    }

    foreach (array_unique($modulesToLoad) as $moduleName) {
        if (!preg_match('/^([a-z]|[0-9]|_|-)+$/i', (string)$moduleName)) {
            continue;
        }
        if (isset($loadedModules[$moduleName])) {
            continue;
        }
        $moduleFile = $englishDir . "/modules/" . $moduleName . ".php";
        if (is_file($moduleFile)) {
            ogp_include_lang_file_safely($moduleFile);
        }
        $loadedModules[$moduleName] = true;
    }
}

function ogp_include_lang_file_safely($filePath)
{
    set_error_handler(function ($severity, $message) {
        $isConstantRedefinition = (bool)preg_match('/^Constant\\s+.+\\s+already\\s+defined$/i', trim((string)$message));
        if ($severity === E_WARNING && $isConstantRedefinition) {
            return true;
        }
        return false;
    });
    include_once($filePath);
    restore_error_handler();
}

function get_lang($lang_index)
{
	global $OGPLangPre;
	
    if (defined($lang_index))
    {
        return constant($lang_index);
    }
    
	if(!startsWith($lang_index, $OGPLangPre)){
		$newLangIndex = $OGPLangPre . $lang_index;
		if (defined($newLangIndex))
		{
			return constant($newLangIndex);
		}
        ogp_load_english_fallbacks();
        if (defined($newLangIndex))
        {
            return constant($newLangIndex);
        }
	}
    else
    {
        ogp_load_english_fallbacks();
        if (defined($lang_index))
        {
            return constant($lang_index);
        }
    }

    // Any other case is error.
    return "_".$lang_index."_";
}

function get_lang_f()
{
	global $OGPLangPre;
    $args = func_get_args();
    $lang_index = array_shift($args);

    if (defined($lang_index))
    {
        return vsprintf(constant($lang_index),$args);
    }
    
	if(!startsWith($lang_index, $OGPLangPre)){
		$newLangIndex = $OGPLangPre . $lang_index;
		if (defined($newLangIndex))
		{
			return vsprintf(constant($newLangIndex),$args);
		}
        ogp_load_english_fallbacks();
        if (defined($newLangIndex))
        {
            return vsprintf(constant($newLangIndex),$args);
        }
	}
    else
    {
        ogp_load_english_fallbacks();
        if (defined($lang_index))
        {
            return vsprintf(constant($lang_index),$args);
        }
    }

    return "_".$lang_index."_".implode("_",$args)."_";
}

function print_lang($lang_index)
{
    print get_lang($lang_index);
}

?>
