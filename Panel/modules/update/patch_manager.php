<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2018 The OGP Development Team
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 */

function gsp_patch_state_fallback_file()
{
if (defined('GSP_ROOT_DIR')) {
return GSP_ROOT_DIR . '/logs/update_patch_state.json';
}
if (defined('GSP_PANEL_DIR')) {
return dirname(GSP_PANEL_DIR) . '/logs/update_patch_state.json';
}
return dirname(__FILE__) . '/../../logs/update_patch_state.json';
}

function gsp_patch_state_load_local($state_file)
{
if (!file_exists($state_file)) {
return [];
}
$payload = json_decode(@file_get_contents($state_file), true);
if (!is_array($payload) || !isset($payload['patches']) || !is_array($payload['patches'])) {
return [];
}
return $payload['patches'];
}

function gsp_patch_state_save_local($state_file, array $patches)
{
$dir = dirname($state_file);
if (!is_dir($dir)) {
@mkdir($dir, 0755, true);
}
$payload = [
'updated_at' => date('Y-m-d H:i:s'),
'patches' => $patches,
];
@file_put_contents($state_file, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function gsp_patch_load_definitions($patch_dir)
{
$patches = [];
if (!is_dir($patch_dir)) {
return $patches;
}
$files = glob(rtrim($patch_dir, '/') . '/*.php') ?: [];
sort($files, SORT_NATURAL);
foreach ($files as $file) {
$def = include $file;
if (!is_array($def) || empty($def['id']) || empty($def['runner'])) {
continue;
}
$def['file'] = $file;
$patches[] = $def;
}
return $patches;
}

function gsp_patch_get_applied_map($db, $state_file)
{
$map = [];
if (isset($db) && is_object($db)) {
$rows = $db->resultQuery("SELECT patch_id, status, details, applied_at FROM `OGP_DB_PREFIXupdate_patches`");
if (is_array($rows)) {
foreach ($rows as $row) {
$map[$row['patch_id']] = [
'status' => $row['status'],
'details' => $row['details'],
'applied_at' => $row['applied_at'],
];
}
}
}
foreach (gsp_patch_state_load_local($state_file) as $id => $row) {
if (!isset($map[$id])) {
$map[$id] = $row;
}
}
return $map;
}

function gsp_patch_record($db, $state_file, $patch_id, $status, $details, $updater_version)
{
$patch_id = (string)$patch_id;
$status = (string)$status;
$details = (string)$details;
$updater_version = (string)$updater_version;
$applied_at = date('Y-m-d H:i:s');

if (isset($db) && is_object($db)) {
$pid = $db->real_escape_string($patch_id);
$st = $db->real_escape_string($status);
$dt = $db->real_escape_string($details);
$uv = $db->real_escape_string($updater_version);
$at = $db->real_escape_string($applied_at);
$db->query(
"INSERT INTO `OGP_DB_PREFIXupdate_patches` (patch_id, status, details, updater_version, applied_at) "
. "VALUES ('{$pid}','{$st}','{$dt}','{$uv}','{$at}') "
. "ON DUPLICATE KEY UPDATE status=VALUES(status), details=VALUES(details), updater_version=VALUES(updater_version), applied_at=VALUES(applied_at)"
);
}

$state = gsp_patch_state_load_local($state_file);
$state[$patch_id] = [
'status' => $status,
'details' => $details,
'applied_at' => $applied_at,
'updater_version' => $updater_version,
];
gsp_patch_state_save_local($state_file, $state);
}

function gsp_patch_run_all($db, $patch_dir, callable $logger, $updater_version)
{
$state_file = gsp_patch_state_fallback_file();
$definitions = gsp_patch_load_definitions($patch_dir);
$applied = gsp_patch_get_applied_map($db, $state_file);
$result = [
'success' => true,
'patches_available' => count($definitions),
'applied' => [],
'skipped' => [],
'failed_patch' => null,
'error' => null,
];

foreach ($definitions as $patch) {
$id = (string)$patch['id'];
$title = !empty($patch['title']) ? (string)$patch['title'] : $id;
if (isset($applied[$id]) && $applied[$id]['status'] === 'applied') {
$result['skipped'][] = $id;
$logger("Patch {$id} ({$title}) already applied; skipping.");
continue;
}

$runner = $patch['runner'];
if (!is_callable($runner)) {
$msg = "Patch {$id} runner is not callable.";
gsp_patch_record($db, $state_file, $id, 'failed', $msg, $updater_version);
$result['success'] = false;
$result['failed_patch'] = $id;
$result['error'] = $msg;
$logger($msg);
break;
}

$logger("Running patch {$id} ({$title}).");
$run = call_user_func($runner, [
'root_dir' => defined('GSP_ROOT_DIR') ? GSP_ROOT_DIR : null,
'panel_dir' => defined('GSP_PANEL_DIR') ? GSP_PANEL_DIR : null,
'website_dir' => defined('GSP_WEBSITE_DIR') ? GSP_WEBSITE_DIR : null,
]);
if (!is_array($run)) {
$run = ['success' => false, 'details' => 'Patch runner returned invalid result.'];
}
$ok = !empty($run['success']);
$details = !empty($run['details']) ? (string)$run['details'] : ($ok ? 'Applied.' : 'Failed.');
gsp_patch_record($db, $state_file, $id, $ok ? 'applied' : 'failed', $details, $updater_version);

if ($ok) {
$result['applied'][] = $id;
$logger("Patch {$id} applied: {$details}");
} else {
$result['success'] = false;
$result['failed_patch'] = $id;
$result['error'] = $details;
$logger("Patch {$id} failed: {$details}");
break;
}
}

return $result;
}
