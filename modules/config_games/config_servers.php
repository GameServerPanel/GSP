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
require_once(__DIR__ . "/xml_tag_descriptions.php");

/**
 * Safely convert any config value (string, NULL, or array from SimpleXML) to a
 * plain PHP string without triggering "Array to string conversion" notices.
 *
 * - string / int / float → cast directly
 * - NULL                 → empty string
 * - SimpleXMLElement     → (string) cast (its __toString returns node text)
 * - simple flat array    → comma-separated list of leaf values
 * - nested/complex array → JSON (pretty-printed for readability in admin forms)
 */
function gsp_normalize_config_value($value): string
{
    if ($value === null) {
        return '';
    }
    if ($value instanceof SimpleXMLElement) {
        return (string)$value;
    }
    if (!is_array($value)) {
        return (string)$value;
    }
    // Flat array → comma-separated list of scalar/castable items
    $isFlat = true;
    foreach ($value as $item) {
        if (is_array($item) || ($item instanceof SimpleXMLElement && count($item->children()) > 0)) {
            $isFlat = false;
            break;
        }
    }
    if ($isFlat) {
        return implode(',', array_map(function ($item) {
            return (string)$item;
        }, $value));
    }
    return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

/**
 * Return an HTML-safe string suitable for display (echo) in the editor.
 * Always wraps the result in htmlspecialchars.
 */
function gsp_value_to_display_string($value): string
{
    return htmlspecialchars(gsp_normalize_config_value($value), ENT_QUOTES, 'UTF-8');
}

/**
 * Return an HTML-safe string suitable for use as an HTML form field value.
 * Identical to gsp_value_to_display_string — kept as a distinct function so
 * callers can signal intent and future formatting rules can differ.
 */
function gsp_value_to_editable_string($value): string
{
    return htmlspecialchars(gsp_normalize_config_value($value), ENT_QUOTES, 'UTF-8');
}

function config_games_normalize_path($path)
{
    $clean = preg_replace('/[^A-Za-z0-9_\\[\\]\\/\\-]/', '', (string)$path);
    return ltrim($clean, '/');
}

function config_games_normalize_newlines($text)
{
    return preg_replace("/\\r\\n?/", "\\n", (string)$text);
}

function config_games_next_form_key(): string
{
    static $counter = 0;
    $counter++;
    return 'node_' . $counter;
}

// Schema-defined element order and required/optional flags for game_config root.
// Source: modules/config_games/schema_server_config.xml (server_config_type sequence).
function config_games_schema_order(): array
{
    return [
        'game_key'              => true,
        'protocol'              => false,
        'lgsl_query_name'       => false,
        'gameq_query_name'      => false,
        'installer'             => false,
        'game_name'             => true,
        'server_exec_name'      => true,
        'query_port'            => false,
        'cli_template'          => false,
        'cli_params'            => false,
        'reserve_ports'         => false,
        'cli_allow_chars'       => false,
        'maps_location'         => false,
        'map_list'              => false,
        'console_log'           => false,
        'exe_location'          => false,
        'max_user_amount'       => false,
        'control_protocol'      => false,
        'control_protocol_type' => false,
        'mods'                  => true,
        'replace_texts'         => false,
        'server_params'         => false,
        'custom_fields'         => false,
        'list_players_command'  => false,
        'player_info_regex'     => false,
        'player_info'           => false,
        'player_commands'       => false,
        'pre_install'           => false,
        'post_install'          => false,
        'pre_start'             => false,
        'post_start'            => false,
        'environment_variables' => false,
        'lock_files'            => false,
        'configuration_files'   => false,
    ];
}

/**
 * Validate an XML file against the game config schema.
 * Returns an empty array on success, or an array of error strings on failure.
 */
function config_games_validate_xml_file(string $config_file): array
{
    if (!file_exists($config_file) || !is_readable($config_file)) {
        return ['Configuration file not found or unreadable: ' . htmlspecialchars($config_file, ENT_QUOTES, 'UTF-8')];
    }
    $prev = libxml_use_internal_errors(true);
    libxml_clear_errors();
    $dom = new DOMDocument();
    if ($dom->load($config_file) === false) {
        $errors = array_map(function ($e) { return trim($e->message) . ' (line ' . $e->line . ')'; }, libxml_get_errors());
        libxml_clear_errors();
        libxml_use_internal_errors($prev);
        return $errors ?: ['XML is not well-formed.'];
    }
    if ($dom->schemaValidate(XML_SCHEMA) !== true) {
        $errors = array_map(function ($e) { return trim($e->message) . ' (line ' . $e->line . ')'; }, libxml_get_errors());
        libxml_clear_errors();
        libxml_use_internal_errors($prev);
        return $errors ?: ['XML failed schema validation.'];
    }
    libxml_clear_errors();
    libxml_use_internal_errors($prev);
    return [];
}

/**
 * Script-like element names whose text content is shell/batch code.
 * These nodes should be stored as CDATA sections so that characters such as
 * '<', '>', '&', etc. survive round-trips through the XML parser unchanged.
 */
function config_games_script_node_names(): array
{
    return ['pre_install', 'post_install', 'pre_start', 'post_start', 'precmd', 'postcmd'];
}

/**
 * Auto-sanitize raw XML text: for every script-like element whose text content
 * contains a bare '<' outside an existing CDATA block, wrap that content in a
 * CDATA section so the file becomes well-formed.  Non-script elements are left
 * untouched.
 *
 * Assumptions / limitations:
 *  - Script elements are treated as leaf nodes (no child elements expected).
 *    Nested XML tags inside a script block are not supported and the regex
 *    will not handle them correctly.
 *  - The detection of an already-present CDATA section relies on the opening
 *    tag being immediately followed by '<![CDATA[' (with optional whitespace).
 *    If a script block mixes text and CDATA it is left unchanged.
 *
 * This is applied to raw XML submitted through the editor's "Raw XML" path
 * before the validation step, giving a best-effort fix rather than a hard
 * rejection for the most common authoring mistake.
 *
 * @param  string $xml  Raw XML string (may be malformed).
 * @return string       XML string with script content wrapped in CDATA where needed.
 */
function config_games_sanitize_xml_scripts(string $xml): string
{
    $tags = config_games_script_node_names();
    foreach ($tags as $tag) {
        $xml = preg_replace_callback(
            '/<' . preg_quote($tag, '/') . '(\s[^>]*)?>(?!\s*<!\[CDATA\[)(.*?)<\/' . preg_quote($tag, '/') . '>/si',
            function ($m) use ($tag) {
                $attrs   = $m[1];
                $content = $m[2];
                // Only wrap if the content contains a raw '<' character.  XML
                // entities such as &lt; do not contain a literal '<' so they
                // are not matched here and do not trigger CDATA wrapping.
                if (strpos($content, '<') === false) {
                    return $m[0];
                }
                return '<' . $tag . $attrs . '><![CDATA[' . $content . ']]></' . $tag . '>';
            },
            $xml
        );
    }
    return $xml;
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
.xml-node--required{border-left:3px solid #1c6dd0}
.xml-node__header{display:flex;justify-content:space-between;align-items:center;gap:12px;border-bottom:1px solid #2a2a2a;padding-bottom:6px;margin-bottom:8px}
.xml-node__title{font-weight:600;color:#f5f5f5}
.xml-node__title--required::after{content:" *";color:#e06c75;font-size:0.8rem}
.xml-node__path{font-size:0.85rem;color:#989898}
.xml-node__badge{font-size:0.72rem;padding:2px 6px;border-radius:3px;text-transform:uppercase;letter-spacing:0.05em;margin-left:6px}
.xml-node__badge--required{background:#1c3a6d;color:#7eb3f0}
.xml-node__badge--optional{background:#2a2a2a;color:#888}
.xml-node__body label{font-size:0.85rem;color:#bbb;display:block;margin-bottom:4px}
.xml-node__body input[type="text"], .xml-node__body textarea, .xml-node__body select{width:100%;padding:8px;border:1px solid #3a3a3a;border-radius:4px;background:#101010;color:#fff;font-family:monospace}
.xml-node__body textarea{min-height:120px}
.xml-node__attributes{margin-top:8px}
.xml-node__attributes .attr-row{display:flex;gap:8px;align-items:center;margin-bottom:6px}
.xml-node__attributes .attr-row input[type="text"]{flex:1}
.xml-children{margin-top:10px;border-left:2px solid #2a2a2a;padding-left:12px}
.xml-actions{display:flex;justify-content:flex-end;margin-top:16px;padding:8px 18px 0}
.xml-node__actions{display:flex;gap:8px;align-items:center}
.xml-node__apply{background:#1c6dd0;border:1px solid #114b99;color:#fff;padding:6px 12px;border-radius:4px;cursor:pointer}
.xml-node__apply:hover{background:#1f7aec}
.xml-global-save{background:#1c6dd0;border:1px solid #114b99;color:#fff;padding:10px 28px;border-radius:4px;font-weight:600;text-transform:uppercase;letter-spacing:0.03em;cursor:pointer;transition:background 0.2s ease,transform 0.2s ease;box-shadow:0 2px 6px rgba(0,0,0,0.35)}
.xml-global-save:hover{background:#1f7aec;transform:translateY(-1px)}
.xml-global-save--top{float:right;margin:0 18px 12px 0}
.xml-hint{font-size:0.85rem;color:#999;margin-top:4px}
.xml-validation-errors{background:#2d0f0f;border:1px solid #8b1c1c;border-radius:6px;padding:12px 16px;margin-bottom:14px;color:#f88}
.xml-validation-errors ul{margin:6px 0 0 16px;padding:0}
.xml-raw-toggle{margin:8px 0 4px;color:#7eb3f0;cursor:pointer;font-size:0.9rem;text-decoration:underline;background:none;border:none;padding:0}
.xml-raw-section{margin-top:10px;display:none}
.xml-raw-section textarea{width:100%;min-height:300px;font-family:monospace;font-size:0.85rem;background:#0c0c0c;color:#eee;border:1px solid #3a3a3a;border-radius:4px;padding:8px}
.xml-raw-warning{background:#2d2200;border:1px solid #7a5a00;border-radius:4px;padding:8px 12px;color:#f0c050;font-size:0.85rem;margin-bottom:6px}
.xml-section-header{margin:20px 0 4px;font-size:0.8rem;color:#888;text-transform:uppercase;letter-spacing:0.1em;border-bottom:1px solid #2a2a2a;padding-bottom:4px}
.xml-node__desc{font-size:0.82rem;color:#aaa;background:#0e0e0e;border-left:3px solid #2a4a7a;padding:6px 10px;margin:6px 0 8px;border-radius:0 4px 4px 0}
.xml-node__options{margin:4px 0 4px 12px;padding:0;list-style:disc inside}
.xml-node__options li{margin-bottom:2px}
.xml-node__options code{color:#7eb3f0;background:rgba(30,100,200,0.12);padding:1px 4px;border-radius:3px}
.xml-node__example{display:block;margin-top:4px;color:#888}
.xml-node__example code{color:#a0d0a0;background:rgba(30,150,50,0.1);padding:1px 4px;border-radius:3px}
.xml-jump-link{display:inline-block;margin-bottom:12px;padding:6px 14px;background:#1c6dd0;color:#fff;border-radius:4px;text-decoration:none;font-size:0.9rem}
.xml-jump-link:hover{background:#1f7aec;text-decoration:none}
.xml-section-grid{display:flex;flex-direction:column;gap:14px;margin-bottom:18px}
.xml-section-block{border:1px solid #303030;border-radius:6px;background:#141414;padding:12px}
.xml-section-block__head{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:8px}
.xml-section-block__title{font-size:1.02rem;color:#f0f0f0;font-weight:600}
.xml-section-block__meta{font-size:0.8rem;color:#9f9f9f}
.xml-section-block__desc{font-size:0.86rem;color:#b0b0b0;margin:0 0 10px}
.xml-section-block textarea{width:100%;min-height:170px;background:#0f0f0f;border:1px solid #3c3c3c;border-radius:4px;color:#f7f7f7;padding:8px;font-family:monospace;font-size:0.84rem}
.xml-section-actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}
.xml-btn{border:1px solid #3f3f3f;background:#222;color:#fff;padding:6px 10px;border-radius:4px;cursor:pointer}
.xml-btn:hover{background:#2a2a2a}
.xml-btn--primary{background:#1c6dd0;border-color:#114b99}
.xml-btn--primary:hover{background:#1f7aec}
.xml-btn--danger{background:#6b1f1f;border-color:#8d2d2d}
.xml-btn--danger:hover{background:#8d2d2d}
.xml-add-section{border:1px dashed #3a3a3a;border-radius:6px;padding:10px;margin-bottom:16px}
.xml-add-section select{min-width:260px}
</style>
CSS;
}

function config_games_render_node(SimpleXMLElement $node, array $ancestors, array &$counters, int $depth = 0, ?bool $isRequired = null)
{
    $schemaOrder = config_games_schema_order();
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

    // Determine required status: use passed value, fall back to schema lookup for top-level nodes
    if ($isRequired === null) {
        $isRequired = $depth === 1 && array_key_exists($name, $schemaOrder) ? $schemaOrder[$name] : false;
    }
    $nodeClass = 'xml-node depth-' . $depth . ($isRequired ? ' xml-node--required' : '');
    $badge = $isRequired
        ? "<span class='xml-node__badge xml-node__badge--required'>required</span>"
        : "<span class='xml-node__badge xml-node__badge--optional'>optional</span>";

    // Look up per-tag description from the descriptions helper.
    $tagDescriptions = config_games_tag_descriptions();
    $tagDesc = $tagDescriptions[$name] ?? null;

    $html = "<div class='{$nodeClass}'>";
    $actionId = 'node_action_' . substr(md5($safePath . $index), 0, 8);
    $html .= "<div class='xml-node__header'><div><div class='xml-node__title'>{$safeLabel}{$badge}</div><div class='xml-node__path'>{$displayPath}</div></div>";
    $html .= "<div class='xml-node__actions'><label for=\"{$actionId}\">Action</label>";
    $html .= "<select id=\"{$actionId}\" name=\"nodes[{$safeNodeKey}][action]\"><option value='keep'>Save Changes</option><option value='remove'>Remove Node</option></select>";
    $html .= "<button type='submit' name='save_xml' value='1' class='xml-node__apply'>Apply</button></div></div>";
    if ($tagDesc !== null) {
        $safeDesc = htmlspecialchars($tagDesc['desc'], ENT_QUOTES, 'UTF-8');
        $html .= "<div class='xml-node__desc'>{$safeDesc}";
        if (!empty($tagDesc['options'])) {
            $html .= "<ul class='xml-node__options'>";
            foreach ($tagDesc['options'] as $optVal => $optLabel) {
                $safeOptVal   = htmlspecialchars((string)$optVal, ENT_QUOTES, 'UTF-8');
                $safeOptLabel = htmlspecialchars($optLabel, ENT_QUOTES, 'UTF-8');
                $html .= "<li><code>{$safeOptVal}</code> &ndash; {$safeOptLabel}</li>";
            }
            $html .= "</ul>";
        }
        if (!empty($tagDesc['example'])) {
            $safeExample = htmlspecialchars($tagDesc['example'], ENT_QUOTES, 'UTF-8');
            $html .= "<span class='xml-node__example'>Example: <code>{$safeExample}</code></span>";
        }
        $html .= "</div>";
    }
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
            $valSafe = gsp_value_to_editable_string($attrValue);
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
    $schemaOrder = config_games_schema_order();
    $rootName = $xml->getName();
    $html = "<div class='xml-editor-wrapper'>";
    $counters = [];

    // Sort top-level children by schema order; unknown elements follow at the end.
    $children = iterator_to_array($xml->children(), false);
    usort($children, function ($a, $b) use ($schemaOrder) {
        $nameA = $a->getName();
        $nameB = $b->getName();
        $orderKeys = array_keys($schemaOrder);
        $posA = ($idx = array_search($nameA, $orderKeys)) !== false ? $idx : PHP_INT_MAX;
        $posB = ($idx = array_search($nameB, $orderKeys)) !== false ? $idx : PHP_INT_MAX;
        return $posA <=> $posB;
    });

    $lastSection = null;
    foreach ($children as $child) {
        $cName = $child->getName();
        $isRequired = $schemaOrder[$cName] ?? null;
        // Print section dividers between required and optional groups
        $section = ($isRequired === true) ? 'required' : (($isRequired === false) ? 'optional' : 'custom');
        if ($section !== $lastSection) {
            if ($section === 'required') {
                $html .= "<div class='xml-section-header'>&#x2605; Required Fields</div>";
            } elseif ($section === 'optional') {
                $html .= "<div class='xml-section-header'>Optional Fields</div>";
            } else {
                $html .= "<div class='xml-section-header'>Custom / Unknown Fields</div>";
            }
            $lastSection = $section;
        }
        $html .= config_games_render_node($child, [$rootName], $counters, 0, $isRequired);
    }

    $html .= "</div>";
    return $html;
}

function config_games_get_config_file_path($db, $home_cfg_id)
{
    $cfgInfo = $db->getGameCfg((int)$home_cfg_id);
    if ($cfgInfo === FALSE) {
        return false;
    }
    return SERVER_CONFIG_LOCATION . $cfgInfo['home_cfg_file'];
}

function config_games_parse_section_payload($sectionName, $sectionXml)
{
    $sectionName = trim((string)$sectionName);
    if ($sectionName === '' || !preg_match('/^[A-Za-z0-9_\\-]+$/', $sectionName)) {
        return array(false, 'Invalid section name.');
    }
    $sectionXml = trim((string)$sectionXml);
    if ($sectionXml === '') {
        return array(false, 'Section XML cannot be empty.');
    }

    $tmpDom = new DOMDocument();
    $tmpDom->preserveWhiteSpace = true;
    $tmpDom->formatOutput = false;
    $wrapped = '<wrapper>' . $sectionXml . '</wrapper>';
    $prev = libxml_use_internal_errors(true);
    libxml_clear_errors();
    $ok = $tmpDom->loadXML($wrapped);
    $errors = libxml_get_errors();
    libxml_clear_errors();
    libxml_use_internal_errors($prev);
    if (!$ok) {
        $msg = 'Section XML is not well-formed.';
        if (!empty($errors)) {
            $msg = trim($errors[0]->message) . ' (line ' . $errors[0]->line . ')';
        }
        return array(false, $msg);
    }

    $elements = array();
    foreach ($tmpDom->documentElement->childNodes as $child) {
        if ($child instanceof DOMElement) {
            $elements[] = $child;
        }
    }
    if (count($elements) !== 1) {
        return array(false, 'Section XML must contain exactly one top-level element.');
    }
    if ($elements[0]->tagName !== $sectionName) {
        return array(false, 'Section XML root tag must be <' . htmlspecialchars($sectionName, ENT_QUOTES, 'UTF-8') . '>.');
    }
    return array($elements[0], '');
}

function config_games_get_top_level_sections($configFile)
{
    $sections = array();
    if (!file_exists($configFile)) {
        return $sections;
    }
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = true;
    $dom->formatOutput = false;
    if (!$dom->load($configFile)) {
        return $sections;
    }
    $schema = config_games_schema_order();
    $descriptions = config_games_tag_descriptions();
    foreach ($dom->documentElement->childNodes as $child) {
        if (!($child instanceof DOMElement)) {
            continue;
        }
        $name = $child->tagName;
        $sections[] = array(
            'name' => $name,
            'required' => ($schema[$name] ?? null) === true,
            'optional' => ($schema[$name] ?? null) === false,
            'xml' => $dom->saveXML($child),
            'description' => $descriptions[$name]['desc'] ?? 'Top-level configuration section.',
        );
    }
    return $sections;
}

function config_games_validate_document_or_errors(DOMDocument $dom)
{
    $tmp = tempnam(sys_get_temp_dir(), 'gsp_cfg_section_');
    if ($tmp === false) {
        return array('Could not create temporary file for validation.');
    }
    $dom->save($tmp);
    $errors = config_games_validate_xml_file($tmp);
    @unlink($tmp);
    return $errors;
}

function config_games_validate_section_update($db, $home_cfg_id, $sectionName, $sectionXml)
{
    $configFile = config_games_get_config_file_path($db, $home_cfg_id);
    if ($configFile === false || !file_exists($configFile)) {
        return array(false, array('Configuration file not found.'));
    }

    list($sectionNode, $parseError) = config_games_parse_section_payload($sectionName, $sectionXml);
    if ($sectionNode === false) {
        return array(false, array($parseError));
    }

    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = true;
    $dom->formatOutput = false;
    if ($dom->load($configFile) === false) {
        return array(false, array('Could not parse configuration XML.'));
    }
    $root = $dom->documentElement;
    $import = $dom->importNode($sectionNode, true);
    $replaced = false;
    foreach ($root->childNodes as $child) {
        if ($child instanceof DOMElement && $child->tagName === $sectionName) {
            $root->replaceChild($import, $child);
            $replaced = true;
            break;
        }
    }
    if (!$replaced) {
        $root->appendChild($import);
    }

    $errors = config_games_validate_document_or_errors($dom);
    if (!empty($errors)) {
        return array(false, $errors);
    }
    return array(true, array());
}

function config_games_save_dom_and_refresh_cfg($db, $configFile, DOMDocument $dom)
{
    if ($dom->save($configFile) === false) {
        return array(false, array('Failed to write configuration file.'));
    }
    $config = read_server_config($configFile);
    if ($config !== FALSE) {
        $db->addGameCfg($config);
    }
    return array(true, array());
}

function config_games_upsert_top_level_section($db, $home_cfg_id, $sectionName, $sectionXml)
{
    $configFile = config_games_get_config_file_path($db, $home_cfg_id);
    if ($configFile === false || !file_exists($configFile)) {
        return array(false, array('Configuration file not found.'));
    }

    list($sectionNode, $parseError) = config_games_parse_section_payload($sectionName, $sectionXml);
    if ($sectionNode === false) {
        return array(false, array($parseError));
    }

    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = true;
    $dom->formatOutput = true;
    if ($dom->load($configFile) === false) {
        return array(false, array('Could not parse configuration XML.'));
    }
    $import = $dom->importNode($sectionNode, true);
    $root = $dom->documentElement;
    $replaced = false;
    foreach ($root->childNodes as $child) {
        if ($child instanceof DOMElement && $child->tagName === $sectionName) {
            $root->replaceChild($import, $child);
            $replaced = true;
            break;
        }
    }

    if (!$replaced) {
        $schemaKeys = array_keys(config_games_schema_order());
        $targetIndex = array_search($sectionName, $schemaKeys, true);
        $inserted = false;
        if ($targetIndex !== false) {
            foreach ($root->childNodes as $child) {
                if (!($child instanceof DOMElement)) {
                    continue;
                }
                $childIndex = array_search($child->tagName, $schemaKeys, true);
                if ($childIndex !== false && $childIndex > $targetIndex) {
                    $root->insertBefore($import, $child);
                    $inserted = true;
                    break;
                }
            }
        }
        if (!$inserted) {
            $root->appendChild($import);
        }
    }

    $errors = config_games_validate_document_or_errors($dom);
    if (!empty($errors)) {
        return array(false, $errors);
    }
    return config_games_save_dom_and_refresh_cfg($db, $configFile, $dom);
}

function config_games_remove_optional_section($db, $home_cfg_id, $sectionName)
{
    $schema = config_games_schema_order();
    if (($schema[$sectionName] ?? null) === true) {
        return array(false, array('Required sections cannot be removed: ' . $sectionName));
    }

    $configFile = config_games_get_config_file_path($db, $home_cfg_id);
    if ($configFile === false || !file_exists($configFile)) {
        return array(false, array('Configuration file not found.'));
    }

    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = true;
    $dom->formatOutput = true;
    if ($dom->load($configFile) === false) {
        return array(false, array('Could not parse configuration XML.'));
    }
    $root = $dom->documentElement;
    $removed = false;
    foreach ($root->childNodes as $child) {
        if ($child instanceof DOMElement && $child->tagName === $sectionName) {
            $root->removeChild($child);
            $removed = true;
            break;
        }
    }
    if (!$removed) {
        return array(false, array('Section not found: ' . $sectionName));
    }

    $errors = config_games_validate_document_or_errors($dom);
    if (!empty($errors)) {
        return array(false, $errors);
    }
    return config_games_save_dom_and_refresh_cfg($db, $configFile, $dom);
}

function config_games_render_top_level_editor($home_cfg_id, $configFile)
{
    $sections = config_games_get_top_level_sections($configFile);
    $schema = config_games_schema_order();
    $presentNames = array_map(function ($section) {
        return $section['name'];
    }, $sections);
    $optionalMissing = array();
    foreach ($schema as $name => $required) {
        if ($required === false && !in_array($name, $presentNames, true)) {
            $optionalMissing[] = $name;
        }
    }

    echo "<h3>Section Editor</h3>";
    $sectionEditorNote = "Edit one top-level section at a time. Validate a block before saving. Required sections cannot be removed. Optional sections can be added or removed safely.";
    echo "<p class='note'>{$sectionEditorNote}</p>";

    if (!empty($optionalMissing)) {
        echo "<form class='xml-add-section' action='?m=config_games&amp;home_cfg_id=" . (int)$home_cfg_id . "' method='post'>";
        echo "<input type='hidden' name='home_cfg_id' value='" . (int)$home_cfg_id . "'>";
        echo "<label for='new_optional_section'>Add optional section:</label> ";
        echo "<select id='new_optional_section' name='section_name'>";
        foreach ($optionalMissing as $missingName) {
            echo "<option value='" . htmlspecialchars($missingName, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($missingName, ENT_QUOTES, 'UTF-8') . "</option>";
        }
        echo "</select> ";
        echo "<button class='xml-btn' type='submit' name='add_optional_section' value='1'>Add Section</button>";
        echo "</form>";
    }

    echo "<div class='xml-section-grid'>";
    foreach ($sections as $section) {
        $safeName = htmlspecialchars($section['name'], ENT_QUOTES, 'UTF-8');
        $safeXml = htmlspecialchars((string)$section['xml'], ENT_QUOTES, 'UTF-8');
        $safeDesc = htmlspecialchars((string)$section['description'], ENT_QUOTES, 'UTF-8');
        $requiredText = $section['required'] ? 'Required' : 'Optional/Custom';

        echo "<form class='xml-section-block' action='?m=config_games&amp;home_cfg_id=" . (int)$home_cfg_id . "' method='post'>";
        echo "<input type='hidden' name='home_cfg_id' value='" . (int)$home_cfg_id . "'>";
        echo "<input type='hidden' name='section_name' value='{$safeName}'>";
        echo "<div class='xml-section-block__head'><div><div class='xml-section-block__title'>{$safeName}</div><div class='xml-section-block__meta'>{$requiredText}</div></div></div>";
        echo "<p class='xml-section-block__desc'>{$safeDesc}</p>";
        echo "<textarea name='section_xml'>{$safeXml}</textarea>";
        echo "<div class='xml-section-actions'>";
        echo "<button class='xml-btn' type='submit' name='validate_section' value='1'>Validate Section</button>";
        echo "<button class='xml-btn xml-btn--primary' type='submit' name='save_section' value='1'>Save Section</button>";
        echo "<button class='xml-btn' type='submit' name='reset_section' value='1'>Reset Section</button>";
        if (!$section['required']) {
            echo "<button class='xml-btn xml-btn--danger' type='submit' name='remove_section' value='1' onclick=\"return confirm('Remove optional section {$safeName}?');\">Remove Section</button>";
        }
        echo "</div>";
        echo "</form>";
    }
    echo "</div>";
}

/**
 * Save XML from structured form nodes payload.
 * Validates against the schema before writing.
 * Returns true on success, or an array of error strings on failure.
 */
function config_games_save_xml($db, $home_cfg_id, array $nodesPayload)
{
    $cfg_info = $db->getGameCfg($home_cfg_id);
    if ($cfg_info === FALSE) {
        return ['Configuration record not found in database.'];
    }
    $config_file = SERVER_CONFIG_LOCATION . $cfg_info['home_cfg_file'];
    if (!file_exists($config_file) || !is_readable($config_file)) {
        return ['Configuration file not found or not readable: ' . htmlspecialchars($config_file, ENT_QUOTES, 'UTF-8')];
    }
    $nodes = [];
    foreach ((array)$nodesPayload as $key => $data) {
        $rawPath = isset($data['path']) ? (string)$data['path'] : (string)$key;
        $cleanPath = config_games_normalize_path($rawPath);
        if ($cleanPath === '') {
            continue;
        }
        $data['path'] = $cleanPath;
        $nodes[$cleanPath] = $data;
    }
    if (empty($nodes)) {
        return ['No node data was submitted.'];
    }
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    if (@$dom->load($config_file) === false) {
        return ['The configuration file could not be parsed as XML. It may be malformed.'];
    }
    // Keep a backup of the original content so we can restore on validation failure.
    $backup = file_get_contents($config_file);
    $xpath = new DOMXPath($dom);
    uksort($nodes, function ($a, $b) {
        return substr_count($b, '/') <=> substr_count($a, '/');
    });
    foreach ((array)$nodes as $path => $nodeData) {
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
        $nodeName    = strtolower(($slashPos = strrpos($path, '/')) !== false ? substr($path, $slashPos + 1) : $path);
        $isScriptNode = in_array($nodeName, config_games_script_node_names(), true);
        if (array_key_exists('value', (array)$nodeData)) {
            $normalizedValue = config_games_normalize_newlines($nodeData['value']);
            while ($domNode->firstChild) {
                $domNode->removeChild($domNode->firstChild);
            }
            if ($normalizedValue !== '') {
                if ($isScriptNode) {
                    $domNode->appendChild($dom->createCDATASection($normalizedValue));
                } else {
                    $domNode->appendChild($dom->createTextNode($normalizedValue));
                }
            }
        } elseif (!$hasChildren) {
            while ($domNode->firstChild) {
                $domNode->removeChild($domNode->firstChild);
            }
        }
        if (isset($nodeData['attributes']) && is_array($nodeData['attributes'])) {
            foreach ((array)$nodeData['attributes'] as $attrName => $attrValue) {
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
        // Restore backup on write failure
        if (isset($backup)) {
            file_put_contents($config_file, $backup);
        }
        return ['Failed to write the configuration file. Check file permissions.'];
    }
    // Validate the saved file against the schema.
    $errors = config_games_validate_xml_file($config_file);
    if (!empty($errors)) {
        // Restore original on schema failure
        if (isset($backup)) {
            file_put_contents($config_file, $backup);
        }
        return $errors;
    }
    $savedContents = @file_get_contents($config_file);
    if ($savedContents !== false) {
        $normalizedContents = config_games_normalize_newlines($savedContents);
        if ($normalizedContents !== $savedContents) {
            file_put_contents($config_file, $normalizedContents);
        }
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
          <p><a href='https://gameservers.world/docs/xml_notes.php' target='_blank' rel='noopener noreferrer' class='xml-jump-link'>&#x1F4D6; XML Config Reference Guide</a></p>\n
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

        foreach ((array)$files as $config_file)
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
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['home_cfg_id']) &&
        (isset($_POST['validate_section']) || isset($_POST['save_section']) || isset($_POST['remove_section']) || isset($_POST['add_optional_section']) || isset($_POST['reset_section']))) {
        $edit_id = (int)$_POST['home_cfg_id'];
        $sectionName = trim((string)($_POST['section_name'] ?? ''));
        $sectionXml = (string)($_POST['section_xml'] ?? '');

        if (isset($_POST['reset_section'])) {
            print_success('Section reset. No changes were saved.');
        } elseif (isset($_POST['validate_section'])) {
            list($ok, $errors) = config_games_validate_section_update($db, $edit_id, $sectionName, $sectionXml);
            if ($ok) {
                print_success('Section XML is valid.');
            } else {
                echo "<div class='xml-validation-errors'><strong>&#x26A0; Section validation failed:</strong><ul>";
                foreach ($errors as $err) {
                    echo "<li>" . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . "</li>";
                }
                echo "</ul></div>";
            }
        } elseif (isset($_POST['save_section'])) {
            list($ok, $errors) = config_games_upsert_top_level_section($db, $edit_id, $sectionName, $sectionXml);
            if ($ok) {
                print_success(get_lang('configs_updated_ok'));
            } else {
                echo "<div class='xml-validation-errors'><strong>&#x26A0; Section save failed:</strong><ul>";
                foreach ($errors as $err) {
                    echo "<li>" . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . "</li>";
                }
                echo "</ul></div>";
            }
        } elseif (isset($_POST['remove_section'])) {
            list($ok, $errors) = config_games_remove_optional_section($db, $edit_id, $sectionName);
            if ($ok) {
                print_success('Optional section removed.');
            } else {
                echo "<div class='xml-validation-errors'><strong>&#x26A0; Could not remove section:</strong><ul>";
                foreach ($errors as $err) {
                    echo "<li>" . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . "</li>";
                }
                echo "</ul></div>";
            }
        } elseif (isset($_POST['add_optional_section'])) {
            $schema = config_games_schema_order();
            if (($schema[$sectionName] ?? null) !== false) {
                print_failure('Only schema-defined optional sections can be added from this menu.');
            } else {
                $newXml = "<{$sectionName}></{$sectionName}>";
                list($ok, $errors) = config_games_upsert_top_level_section($db, $edit_id, $sectionName, $newXml);
                if ($ok) {
                    print_success('Optional section added.');
                } else {
                    echo "<div class='xml-validation-errors'><strong>&#x26A0; Could not add section:</strong><ul>";
                    foreach ($errors as $err) {
                        echo "<li>" . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . "</li>";
                    }
                    echo "</ul></div>";
                }
            }
        }
        $_GET['home_cfg_id'] = $edit_id;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_xml']) && isset($_POST['home_cfg_id'])) {
        $edit_id = (int)$_POST['home_cfg_id'];

        // Raw XML save path
        if (isset($_POST['raw_xml_content'])) {
            $cfg_info = $db->getGameCfg($edit_id);
            if ($cfg_info !== FALSE) {
                $config_file = SERVER_CONFIG_LOCATION . $cfg_info['home_cfg_file'];
                $raw_content = $_POST['raw_xml_content'];
                // Apply best-effort auto-fix: wrap bare '<' chars in script blocks with CDATA.
                $sanitized_content = config_games_sanitize_xml_scripts($raw_content);
                // Write to a temp file for validation
                $tmp = tempnam(sys_get_temp_dir(), 'gsp_xml_');
                file_put_contents($tmp, $sanitized_content);
                $xmlErrors = config_games_validate_xml_file($tmp);
                @unlink($tmp);
                if (!empty($xmlErrors)) {
                    echo "<div class='xml-validation-errors'><strong>&#x26A0; XML validation failed &mdash; file was NOT saved:</strong><ul>";
                    foreach ($xmlErrors as $err) {
                        echo "<li>" . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . "</li>";
                    }
                    echo "</ul></div>";
                } else {
                    if (file_put_contents($config_file, $sanitized_content) !== false) {
                        print_success(get_lang('configs_updated_ok'));
                        $config = read_server_config($config_file);
                        if ($config !== FALSE) {
                            $db->addGameCfg($config);
                        }
                    } else {
                        print_failure('Failed to write configuration file. Check permissions.');
                    }
                }
            } else {
                print_failure('Configuration record not found.');
            }
        } else {
            $nodesPayload = isset($_POST['nodes']) && is_array($_POST['nodes']) ? $_POST['nodes'] : [];
            $result = config_games_save_xml($db, $edit_id, $nodesPayload);
            if ($result === true) {
                print_success(get_lang('configs_updated_ok'));
            } else {
                $errors = is_array($result) ? $result : ['Failed to save XML configuration.'];
                echo "<div class='xml-validation-errors'><strong>&#x26A0; XML validation failed &mdash; file was NOT saved:</strong><ul>";
                foreach ($errors as $err) {
                    echo "<li>" . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . "</li>";
                }
                echo "</ul></div>";
            }
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
	foreach ((array)$game_cfgs as $row)
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
		echo "<p><a class='xml-jump-link' href='#xml-editor-section' aria-label='Jump to XML Editor section below'>&#x2193; Jump to XML Editor</a></p>";
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
				// Also show any schema validation errors on the current file (informational, before editing)
				$existingErrors = config_games_validate_xml_file($config_file);
				if (!empty($existingErrors)) {
					echo "<div class='xml-validation-errors'><strong>&#x26A0; This file currently fails schema validation:</strong><ul>";
					foreach ($existingErrors as $err) {
						echo "<li>" . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . "</li>";
					}
					echo "</ul></div>";
				}
				if ($xml === false) {
					print_failure(get_lang_f("error_when_handling_file",$config_file));
				} else {
					$raw_xml_content = htmlspecialchars(file_get_contents($config_file), ENT_QUOTES, 'UTF-8');
					echo "<div id='xml-editor-section'>";
					config_games_render_top_level_editor($home_cfg_id, $config_file);

					echo "<details style='margin:18px 0'><summary style='cursor:pointer;color:#9dc7ff'>Open legacy detailed node editor</summary>";
					echo "<form action='?m=config_games&amp;home_cfg_id=".$home_cfg_id."' method='post'>";
					echo "<input type='hidden' name='home_cfg_id' value='".(int)$home_cfg_id."'>";
					echo "<button type='submit' name='save_xml' value='1' class='xml-global-save xml-global-save--top'>".get_lang('save')."</button>";
					echo "<div style='clear:both'></div>";
					echo config_games_render_editor($xml);
					echo "<div class='xml-actions'><button type='submit' name='save_xml' value='1' class='xml-global-save'>".get_lang('save')."</button></div>";
					echo "<p class='note'>&#x2605; = required field. Use the action dropdown to remove entire sections. Attribute values left blank will be removed. Script sections such as post_install are fully editable. Changes are validated against the schema before saving.</p>";
					echo "</form>";
					echo "</details>";

					// Raw XML editor
					echo "<hr style='margin:24px 0;border-color:#333'>";
					echo "<h3 style='margin-bottom:8px'>Full Raw XML Editor</h3>";
					echo "<div class='xml-raw-warning'>&#x26A0; <strong>Warning:</strong> Saving raw XML bypasses the guided editor. The file will be validated against the schema before saving. Invalid XML will be rejected.</div>";
					echo "<button type='button' class='xml-raw-toggle' onclick=\"var s=document.getElementById('raw_xml_section');s.style.display=s.style.display==='none'?'block':'none'\">Toggle Raw XML Editor</button>";
					echo "<div id='raw_xml_section' class='xml-raw-section'>";
					echo "<form action='?m=config_games&amp;home_cfg_id=".$home_cfg_id."' method='post'>";
					echo "<input type='hidden' name='home_cfg_id' value='".(int)$home_cfg_id."'>";
					echo "<textarea name='raw_xml_content'>{$raw_xml_content}</textarea>";
					echo "<div class='xml-actions' style='margin-top:8px'><button type='submit' name='save_xml' value='1' class='xml-global-save'>Save Raw XML</button></div>";
					echo "</form>";
					echo "</div>";
					echo "</div>"; // #xml-editor-section
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
