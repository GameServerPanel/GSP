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

require_once("server_config_parser.php");

function config_games_normalize_path($path)
{
    $clean = preg_replace('/[^A-Za-z0-9_\\[\\]\\/\\-]/', '', (string)$path);
    return ltrim($clean, '/');
}

function config_games_next_form_key(): string
{
    static $counter = 0;
    $counter++;
    return 'node_' . $counter;
}

function config_games_print_editor_css()
{
    static $printed = false;
    if ($printed) {
        return;
    }
    $printed = true;
    echo <<<CSS
<style>
.xml-editor-wrapper{margin:20px 0;padding:12px;background:#111;border:1px solid #222;border-radius:8px}
.xml-node{border:1px solid #333;border-radius:6px;padding:12px;margin-bottom:10px;background:#181818}
.xml-node__header{display:flex;justify-content:space-between;align-items:center;gap:12px;border-bottom:1px solid #2a2a2a;padding-bottom:6px;margin-bottom:8px}
.xml-node__title{font-weight:600;color:#f5f5f5}
.xml-node__path{font-size:0.85rem;color:#989898}
.xml-node__body label{font-size:0.85rem;color:#bbb;display:block;margin-bottom:4px}
.xml-node__body input[type="text"], .xml-node__body textarea, .xml-node__body select{width:100%;padding:8px;border:1px solid #3a3a3a;border-radius:4px;background:#101010;color:#fff;font-family:monospace}
.xml-node__body textarea{min-height:120px}
.xml-node__attributes{margin-top:8px}
.xml-node__attributes .attr-row{display:flex;gap:8px;align-items:center;margin-bottom:6px}
.xml-node__attributes .attr-row input[type="text"]{flex:1}
.xml-children{margin-top:10px;border-left:2px solid #2a2a2a;padding-left:12px}
.xml-actions{text-align:right;margin-top:16px}
.xml-node__actions{display:flex;gap:8px;align-items:center}
.xml-node__apply{background:#1c6dd0;border:1px solid #114b99;color:#fff;padding:6px 12px;border-radius:4px;cursor:pointer}
.xml-node__apply:hover{background:#1f7aec}
.xml-hint{font-size:0.85rem;color:#999;margin-top:4px}
</style>
CSS;
}

function config_games_render_node(SimpleXMLElement $node, array $ancestors, array &$counters, int $depth = 0)
{
    $name = $node->getName();
    $pathKey = implode('/', $ancestors) === '' ? $name : implode('/', $ancestors) . '/' . $name;
    $counters[$pathKey] = ($counters[$pathKey] ?? 0) + 1;
    $index = $counters[$pathKey];
    $pathParts = array_merge($ancestors, ["{$name}[{$index}]"]);
    $rawPath = implode('/', $pathParts);
    $path = config_games_normalize_path($rawPath);
    $hasChildren = count($node->children()) > 0;
    $value = (string)$node;
    $safeLabel = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $safePath = htmlspecialchars($path, ENT_QUOTES, 'UTF-8');
    $nodeKey = config_games_next_form_key();
    $safeNodeKey = htmlspecialchars($nodeKey, ENT_QUOTES, 'UTF-8');
    $displayPath = htmlspecialchars(str_replace('[', '[', $rawPath), ENT_QUOTES, 'UTF-8');
    $isScript = in_array(strtolower($name), ['pre_install','post_install','precmd','postcmd','cli_template']);

    $html = "<div class='xml-node depth-{$depth}'>";
    $actionId = 'node_action_' . substr(md5($safePath . $index), 0, 8);
    $html .= "<div class='xml-node__header'><div><div class='xml-node__title'>{$safeLabel}</div><div class='xml-node__path'>{$displayPath}</div></div>";
    $html .= "<div class='xml-node__actions'><label for=\"{$actionId}\">Action</label>";
    $html .= "<select id=\"{$actionId}\" name=\"nodes[{$safeNodeKey}][action]\"><option value='keep'>Save Changes</option><option value='remove'>Remove Node</option></select>";
    $html .= "<button type='submit' name='save_xml' value='1' class='xml-node__apply'>Apply</button></div></div>";
    $html .= "<div class='xml-node__body'>";
    $html .= "<input type='hidden' name=\"nodes[{$safeNodeKey}][path]\" value=\"{$safePath}\">";
    $html .= "<input type='hidden' name=\"nodes[{$safeNodeKey}][has_children]\" value=\"" . ($hasChildren ? '1' : '0') . "\">";

    if (!$hasChildren || $isScript) {
        $safeValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        if ($isScript || strlen($value) > 120) {
            $html .= "<label>Value</label><textarea name=\"nodes[{$safeNodeKey}][value]\">{$safeValue}</textarea>";
        } else {
            $html .= "<label>Value</label><input type='text' name=\"nodes[{$safeNodeKey}][value]\" value=\"{$safeValue}\">";
        }
    } elseif (trim($value) !== '') {
        $safeValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $html .= "<label>Inner Text</label><textarea name=\"nodes[{$safeNodeKey}][value]\">{$safeValue}</textarea>";
        $html .= "<p class='xml-hint'>This element contains nested tags; clearing the text does not remove children.</p>";
    }

    $attributes = $node->attributes();
    if ($attributes && count($attributes) > 0) {
        $html .= "<div class='xml-node__attributes'><strong>Attributes</strong>";
        foreach ($attributes as $attrName => $attrValue) {
            $attrSafe = htmlspecialchars($attrName, ENT_QUOTES, 'UTF-8');
            $valSafe = htmlspecialchars((string)$attrValue, ENT_QUOTES, 'UTF-8');
            $html .= "<div class='attr-row'><span>{$attrSafe}</span><input type='text' name=\"nodes[{$safeNodeKey}][attributes][{$attrSafe}]\" value=\"{$valSafe}\" placeholder='Leave blank to remove'></div>";
        }
        $html .= "<div class='attr-row'><input type='text' name=\"nodes[{$safeNodeKey}][new_attribute][name]\" placeholder='New attribute name'><input type='text' name=\"nodes[{$safeNodeKey}][new_attribute][value]\" placeholder='New attribute value'></div>";
        $html .= "</div>";
    } else {
        $html .= "<div class='xml-node__attributes'><div class='attr-row'><input type='text' name=\"nodes[{$safeNodeKey}][new_attribute][name]\" placeholder='Attribute name'><input type='text' name=\"nodes[{$safeNodeKey}][new_attribute][value]\" placeholder='Attribute value'></div></div>";
    }

    if ($hasChildren) {
        $html .= "<div class='xml-children'>";
        foreach ($node->children() as $child) {
            $html .= config_games_render_node($child, array_merge($ancestors, ["{$name}[{$index}]"]), $counters, $depth + 1);
        }
        $html .= "</div>";
    }

    $html .= "</div></div>";
    return $html;
}

function config_games_render_editor(SimpleXMLElement $xml)
{
    config_games_print_editor_css();
    $rootName = $xml->getName();
    $html = "<div class='xml-editor-wrapper'>";
    $counters = [];
    foreach ($xml->children() as $child) {
        $html .= config_games_render_node($child, [$rootName], $counters);
    }
    $html .= "</div>";
    return $html;
}

function config_games_save_xml($db, $home_cfg_id, array $nodesPayload)
{
    $cfg_info = $db->getGameCfg($home_cfg_id);
    if ($cfg_info === FALSE) {
        return false;
    }
    $config_file = SERVER_CONFIG_LOCATION . $cfg_info['home_cfg_file'];
    if (!file_exists($config_file) || !is_readable($config_file)) {
        return false;
    }
    $nodes = [];
    foreach ($nodesPayload as $key => $data) {
        $rawPath = isset($data['path']) ? (string)$data['path'] : (string)$key;
        $cleanPath = config_games_normalize_path($rawPath);
        if ($cleanPath === '') {
            continue;
        }
        $data['path'] = $cleanPath;
        $nodes[$cleanPath] = $data;
    }
    if (empty($nodes)) {
        return false;
    }
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    if (@$dom->load($config_file) === false) {
        return false;
    }
    $xpath = new DOMXPath($dom);
    uksort($nodes, function ($a, $b) {
        return substr_count($b, '/') <=> substr_count($a, '/');
    });
    foreach ($nodes as $path => $nodeData) {
        $query = '/' . $path;
        $nodeList = @$xpath->query($query);
        if (!$nodeList || $nodeList->length === 0) {
            continue;
        }
        $domNode = $nodeList->item(0);
        $action = $nodeData['action'] ?? 'keep';
        if ($action === 'remove') {
            if ($domNode->parentNode) {
                $domNode->parentNode->removeChild($domNode);
            }
            continue;
        }
        $hasChildren = !empty($nodeData['has_children']);
        if (array_key_exists('value', $nodeData)) {
            while ($domNode->firstChild) {
                $domNode->removeChild($domNode->firstChild);
            }
            if ($nodeData['value'] !== '') {
                $domNode->appendChild($dom->createTextNode($nodeData['value']));
            }
        } elseif (!$hasChildren) {
            while ($domNode->firstChild) {
                $domNode->removeChild($domNode->firstChild);
            }
        }
        if (isset($nodeData['attributes']) && is_array($nodeData['attributes'])) {
            foreach ($nodeData['attributes'] as $attrName => $attrValue) {
                $attrNameClean = preg_replace('/[^A-Za-z0-9_\\-:]/', '', (string)$attrName);
                if ($attrNameClean === '') {
                    continue;
                }
                $attrValue = trim((string)$attrValue);
                if ($attrValue === '') {
                    $domNode->removeAttribute($attrNameClean);
                } else {
                    $domNode->setAttribute($attrNameClean, $attrValue);
                }
            }
        }
        if (isset($nodeData['new_attribute']['name']) && $nodeData['new_attribute']['name'] !== '') {
            $newName = preg_replace('/[^A-Za-z0-9_\\-:]/', '', (string)$nodeData['new_attribute']['name']);
            $newValue = (string)($nodeData['new_attribute']['value'] ?? '');
            if ($newName !== '' && $newValue !== '') {
                $domNode->setAttribute($newName, $newValue);
            }
        }
    }
    if ($dom->save($config_file) === false) {
        return false;
    }
    $config = read_server_config($config_file);
    if ($config !== FALSE) {
        $db->addGameCfg($config);
    }
    return true;
}

function exec_ogp_module() {

    global $db,$view;
    $game_cfgs = $db->getGameCfgs();
    echo "<h2>".get_lang('game_config_setup')."</h2>\n
          <p>".get_lang_f("modify_configs_info",SERVER_CONFIG_LOCATION)."</p>\n
		  <form action='?m=config_games' method='post'>\n
          <p><input id='reset_old_configs' type='checkbox' name='clear_old' value='yes' /><label for='reset_old_configs'>".get_lang('reset_old_configs')."</label></p>\n
          <p class='note'>".get_lang('note').": ".get_lang('config_reset_warning')."</p>\n
		  <p><input type='submit' name='reconfig' value='".get_lang('update_configs')."' /></p>\n
          </form>\n";

    if ( isset($_REQUEST['reconfig']) )
    {
		// Remove any old config files that may have been renamed or removed by developers
		// Function is defined in helpers.php (add entries to array there)
		removeOldGameConfigs();
		
        $files = glob(SERVER_CONFIG_LOCATION."*.xml");

        if ( empty($files) )
        {
            print_failure(get_lang_f("no_configs_found",SERVER_CONFIG_LOCATION));
            return;
        }

        /// \todo remove the clear_old hack when the update on duplicate is completed to database.
        $clear_old = FALSE;

        if ( isset( $_REQUEST['clear_old']) && $_REQUEST['clear_old'] === 'yes' )
        {
            echo "<p class='info'>".get_lang('resetting_configs').":</p>";
            $clear_old = TRUE;
        }
        else
        {
            echo "<p class='info'>".get_lang('updating_configs').":</p>";
        }
        
        $oldStructure = $db->getCurrentHomeConfigMods();

        $db->clearGameCfgs($clear_old);

        foreach ( $files as $config_file )
        {
            $config = read_server_config($config_file);
            
            if ( empty($config) )
            {
                print_failure(get_lang_f("error_when_handling_file",$config_file));
                continue;
            }
            echo "<p class='info'>".get_lang_f("updating_config_from_file",$config_file)."</p>";
            if ( !$db->addGameCfg($config) )
            {
                print_failure(get_lang_f("error_while_adding_cfg_to_db",$config_file));
                continue;
            }
        }
        
        // Update and remove invalid old game mod ids
        if($clear_old){
			$db->updateOGPGameModsWithNewIDs($oldStructure);
		}

        print_success(get_lang('configs_updated_ok'));
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_xml']) && isset($_POST['home_cfg_id'])) {
        $edit_id = (int)$_POST['home_cfg_id'];
        $nodesPayload = isset($_POST['nodes']) && is_array($_POST['nodes']) ? $_POST['nodes'] : [];
        if (config_games_save_xml($db, $edit_id, $nodesPayload)) {
            print_success(get_lang('configs_updated_ok'));
        } else {
            print_failure('Failed to save XML configuration.');
        }
        $_GET['home_cfg_id'] = $edit_id;
    }
	
	$game_cfgs = $db->getGameCfgs();
	echo "<table class='center'>\n
		  <form action='' method='GET'>\n
		  <input type='hidden' name='m' value='config_games'/>
		  <tr>\n
		  <td class='left'>\n
		  <select name='home_cfg_id' onchange=".'"this.form.submit()"'.">\n
		  <option style='background:black;color:white;' value=''>".get_lang('select_game')."</option>\n";	  
	foreach ( $game_cfgs as $row )
	{
		if ( preg_match( "/_win/", $row['game_key'] ) )
			$os = "(Windows)";
		if (preg_match( "/_linux/", $row['game_key'] ) )
			$os = "(Linux)";
		if (preg_match( "/64/", $row['game_key'] ) )
			$arch = "(64bit)";
		else
			$arch = "";
		if ( isset($_GET['home_cfg_id']) AND $row['home_cfg_id'] == $_GET['home_cfg_id'])
			$selected = "selected='selected'";
		else
			$selected = "";

		echo "<option value='".$row['home_cfg_id']."' $selected >".$row['game_name']." $os $arch</option>\n";
		unset ($os,$arch);
	}
	echo "</select>\n
		  </td>\n
		  </tr>\n
		  </form>\n
		  </table>\n";
	
	if ( isset($_GET['home_cfg_id']) )
    {
		$home_cfg_id = trim($_GET['home_cfg_id']);
		
		$cfg_info = $db->getGameCfg($home_cfg_id);
		
		if($cfg_info !== FALSE)
		{
			$config_file = SERVER_CONFIG_LOCATION.$cfg_info['home_cfg_file'];
			
			if ( preg_match( "/_win/", $cfg_info['game_key'] ) )
					$os = "(Windows)";
			if (preg_match( "/_linux/", $cfg_info['game_key'] ) )
				$os = "(Linux)";
			if (preg_match( "/64/", $cfg_info['game_key'] ) )
				$arch = "(64bit)";
			else
				$arch = "";
			
			if( isset($_GET['delete']) )
			{				
				if( $db->delGameCfgAndMods($home_cfg_id) === FALSE )
				{
					print_failure(get_lang_f('failed_to_delete_config_from_db',$cfg_info['game_name']));
					$view->refresh('?m=config_games&home_cfg_id='.$home_cfg_id,3);
				}
				elseif( unlink($config_file) === FALSE )
				{
					print_failure(get_lang_f('failed_removing_file',$config_file));
					$view->refresh('?m=config_games&home_cfg_id='.$home_cfg_id,3);
				}
				else
				{
					print_success(get_lang_f('removed_game_cfg_from_disk_and_datbase',$cfg_info['game_name']." $os $arch"));
					$view->refresh('?m=config_games',3);
				}
			}
			else
			{
				echo "<a href='?m=config_games&home_cfg_id=".$home_cfg_id."&delete'>".get_lang_f('delete_game_config_for',$cfg_info['game_name']." $os $arch")."</a><br>";
				
				$xml = @simplexml_load_file($config_file);
				if ($xml === false) {
					print_failure(get_lang_f("error_when_handling_file",$config_file));
				} else {
					echo "<form action='?m=config_games&amp;home_cfg_id=".$home_cfg_id."' method='post'>";
					echo "<input type='hidden' name='home_cfg_id' value='".(int)$home_cfg_id."'>";
					echo "<button type='submit' name='save_xml' value='1' style='float:right;margin-bottom:10px;'>".get_lang('save')."</button>";
					echo "<div style='clear:both'></div>";
					echo config_games_render_editor($xml);
					echo "<div class='xml-actions'><button type='submit' name='save_xml' value='1'>".get_lang('save')."</button></div>";
					echo "</form>";
					echo "<p class='note'>Use the action dropdown to remove entire sections. Attribute values left blank will be removed. Script sections such as post_install are fully editable.</p>";
				}
			}
		}
	}
	if(isset($_GET['xml_config_creator']))
	{
		echo "<iframe style='width:100%;height:600px;' frameBorder='0' src='home.php?m=config_games&p=xml_config_creator&type=cleared' ></iframe>";
	}
	else
	{
		echo "<br><form action='' method='GET'><input type='hidden' name='m' value='config_games' /><input type='submit' name='xml_config_creator' value='".get_lang('create_xml_configs')."'/></form>";
	}
}
?>
