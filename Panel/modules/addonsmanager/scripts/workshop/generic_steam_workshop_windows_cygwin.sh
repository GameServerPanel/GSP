#!/usr/bin/env bash
set -euo pipefail

MANIFEST_PATH="${1:-}"
if [[ -z "$MANIFEST_PATH" ]]; then
  echo "Usage: $0 <manifest_path>"
  exit 1
fi

python3 - "$MANIFEST_PATH" <<'PY'
import json
import os
import shutil
import subprocess
import sys
from datetime import datetime

manifest_path = os.path.abspath(sys.argv[1])
if not os.path.isfile(manifest_path):
    print(f"Manifest not found: {manifest_path}")
    sys.exit(1)

manifest_dir = os.path.dirname(manifest_path)
home_root = os.path.dirname(manifest_dir)
log_file = os.path.join(manifest_dir, 'workshop_install_windows.log')
removed_dir = os.path.join(manifest_dir, 'workshop', 'removed')
os.makedirs(removed_dir, exist_ok=True)


def log(message, status=None):
    line = f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}]"
    if status:
        line += f" [{status}]"
    line += f" {message}"
    print(line)
    with open(log_file, 'a', encoding='utf-8') as handle:
        handle.write(line + "\n")


def fail(message):
    log(message, 'Failed')
    raise RuntimeError(message)


def uniq_numeric_items(raw_items):
    seen = []
    for value in raw_items:
        text = str(value).strip()
        if text.isdigit() and text not in seen:
            seen.append(text)
    return seen


def render_template(template, values):
    rendered = str(template or '')
    for key, value in values.items():
        rendered = rendered.replace('{' + key + '}', str(value))
    return rendered


def ensure_under_home(path_value):
    target = os.path.abspath(path_value)
    try:
        common = os.path.commonpath([home_root, target])
    except ValueError:
        common = ''
    if common != os.path.abspath(home_root):
        fail(f"Refusing to write outside server home: {target}")
    return target


def resolve_steamcmd(explicit_path=''):
    candidates = []
    explicit_path = str(explicit_path or '').strip()
    if explicit_path:
        candidates.append(explicit_path)
    env_value = os.environ.get('STEAMCMD_PATH', '').strip()
    if env_value:
        candidates.append(env_value)
    for path_value in (
        '/home/gameserver/steamcmd/steamcmd.sh',
        shutil.which('steamcmd'),
        shutil.which('steamcmd.exe'),
        shutil.which('steamcmd.sh'),
    ):
        if path_value:
            candidates.append(path_value)
    for candidate in candidates:
        if candidate and os.path.isfile(candidate):
            return candidate
    fail('SteamCMD is missing on the agent host.')


def sync_copy(src, dst):
    if not os.path.isdir(src):
        fail(f"Workshop download source was not found: {src}")
    os.makedirs(dst, exist_ok=True)
    for entry in os.listdir(src):
        source_entry = os.path.join(src, entry)
        target_entry = os.path.join(dst, entry)
        if os.path.isdir(source_entry):
            sync_copy(source_entry, target_entry)
        else:
            os.makedirs(os.path.dirname(target_entry), exist_ok=True)
            shutil.copy2(source_entry, target_entry)


try:
    with open(manifest_path, 'r', encoding='utf-8') as handle:
        manifest = json.load(handle)

    extra = manifest.get('extra') or {}
    action = str(manifest.get('action', '')).strip()
    raw_items = manifest.get('items', [])
    if isinstance(raw_items, dict):
        raw_items = raw_items.get('workshop_item_ids', [])
    items = uniq_numeric_items(raw_items)
    if not items:
        fail('No Workshop IDs were found in the manifest.')

    workshop_app_id = str(extra.get('workshop_app_id') or manifest.get('workshop_app_id') or '').strip()
    steam_app_id = str(extra.get('steam_app_id') or manifest.get('steam_app_id') or '').strip()
    server_root = ensure_under_home(extra.get('server_root') or home_root)
    steamcmd_path = resolve_steamcmd(extra.get('steamcmd_path') or '')
    post_install_script = str(extra.get('post_install_script') or '').strip()
    default_download_dir = extra.get('workshop_download_dir') or os.path.join(server_root, 'steamapps', 'workshop', 'content', workshop_app_id or steam_app_id)

    action_label = 'Queued' if action in ('install', 'update', 'check_updates') else action
    log(f"action={action} manifest={manifest_path} steam_app_id={steam_app_id or 'n/a'} workshop_app_id={workshop_app_id or 'n/a'}", action_label)

    for workshop_id in items:
        folder_name = str(extra.get('optional_folder_name') or '').strip() or ('@' + workshop_id)
        template_values = {
            'HOME_ID': manifest.get('home_id', ''),
            'SERVER_ROOT': server_root,
            'GAME_ROOT': server_root,
            'WORKSHOP_ID': workshop_id,
            'WORKSHOP_APP_ID': workshop_app_id,
            'STEAM_APP_ID': steam_app_id,
            'FOLDER_NAME': folder_name,
            'MOD_FOLDER': folder_name,
        }
        target_template = str(extra.get('target_path_template') or '{SERVER_ROOT}/{MOD_FOLDER}')
        target_path = str(extra.get('target_path_resolved') or '').strip()
        if len(items) != 1 or not target_path:
            target_path = render_template(target_template, template_values)
        target_path = ensure_under_home(target_path)

        download_dir = ensure_under_home(render_template(default_download_dir, template_values))
        source_path = os.path.join(download_dir, workshop_id)

        if action in ('install', 'update', 'check_updates'):
            if not workshop_app_id:
                fail(f"Workshop App ID is missing for Workshop item {workshop_id}.")
            command = [
                steamcmd_path,
                '+force_install_dir', server_root,
                '+login', 'anonymous',
                '+workshop_download_item', workshop_app_id, workshop_id, 'validate',
                '+quit',
            ]
            log(f"workshop_id={workshop_id} steamcmd={' '.join(command)}", 'Downloading Workshop Item')
            result = subprocess.run(command, stdout=subprocess.PIPE, stderr=subprocess.STDOUT, text=True, cwd=server_root)
            if result.stdout:
                for line in result.stdout.splitlines():
                    log(f"steamcmd[{workshop_id}] {line}")
            if result.returncode != 0:
                fail(f"SteamCMD failed for Workshop item {workshop_id} with exit code {result.returncode}.")
            if not os.path.isdir(source_path):
                fail(f"SteamCMD did not create the expected Workshop cache path: {source_path}")

            if action != 'check_updates':
                log(f"workshop_id={workshop_id} install_path={target_path}", 'Extracting/Copying')
                sync_copy(source_path, target_path)
                log(f"workshop_id={workshop_id} final_folder_path={target_path}", 'Applying Folder Name')
                if post_install_script:
                    log(f"workshop_id={workshop_id} cwd={server_root}", 'Running Post-install Script')
                    post_result = subprocess.run(['bash', '-lc', post_install_script], stdout=subprocess.PIPE, stderr=subprocess.STDOUT, text=True, cwd=server_root)
                    if post_result.stdout:
                        for line in post_result.stdout.splitlines():
                            log(f"post_install[{workshop_id}] {line}")
                    if post_result.returncode != 0:
                        fail(f"Post-install script failed for Workshop item {workshop_id} with exit code {post_result.returncode}.")
            log(f"workshop_id={workshop_id} install_path={target_path}", 'Completed')
        elif action == 'remove':
            if os.path.exists(target_path):
                timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
                removed_path = os.path.join(removed_dir, f"{workshop_id}_{timestamp}")
                shutil.move(target_path, removed_path)
                log(f"workshop_id={workshop_id} removed_path={removed_path}", 'Completed')
            else:
                log(f"workshop_id={workshop_id} target_path_missing={target_path}", 'Completed')
        else:
            fail(f"Unknown workshop action: {action}")
except RuntimeError:
    sys.exit(1)
PY
