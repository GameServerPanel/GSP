#!/usr/bin/env bash
#
# GSP Deployment Script
# =====================
# This script deploys the Game Server Panel (GSP) from GitHub to a web server.
#
# HOW IT WORKS:
# 1. Clones/updates the GSP repository to a staging directory
# 2. Syncs files to the web root using rsync (preserving configs)
# 3. Sets proper permissions for OGP panel operation
#
# CONFIGURATION:
# All settings can be configured via environment variables or by editing
# the defaults in the "Config" section below.
#
# ENVIRONMENT VARIABLES:
# - REPO_URL: Git repository URL (default: https://github.com/GameServerPanel/GSP.git)
# - STAGE_DIR: Staging directory for git clone (default: $HOME/gsp_stage)
# - WEB_ROOT: Live web server directory (default: /var/www/html/panel)
# - OWNER: File owner user (default: www-data)
# - GROUP: File owner group (default: www-data)
# - SUDO: Command prefix for privilege escalation (default: sudo, set empty to skip)
# - DRY_RUN: Set to 1 to test without making changes (default: 0)
#
# EXAMPLE USAGE:
#   # Use defaults:
#   ./deploy_gsp.sh
#
#   # Custom web root:
#   WEB_ROOT=/home/panel/public_html ./deploy_gsp.sh
#
#   # Dry run to test:
#   DRY_RUN=1 ./deploy_gsp.sh
#
#   # Different user/group:
#   OWNER=apache GROUP=apache ./deploy_gsp.sh
#
set -Eeuo pipefail
umask 022

# ---------- Config (override via env if you like) ----------
REPO_URL="${REPO_URL:-https://github.com/GameServerPanel/GSP.git}"
STAGE_DIR="${STAGE_DIR:-$HOME/gsp_stage}"        # keeps clone in your home folder
WEB_ROOT="${WEB_ROOT:-/var/www/html/panel}"      # live site root
OWNER="${OWNER:-www-data}"
GROUP="${GROUP:-www-data}"
SUDO="${SUDO:-sudo}"                             # set SUDO= to skip sudo if not needed
DRY_RUN="${DRY_RUN:-0}"                          # set DRY_RUN=1 to test without writing

# Never overwrite these:
EXCLUDES=(
  ".git/"
  "includes/config.inc.php"
  "modules/billing/includes/config.inc.php"
)

# ---------- Helpers ----------
log(){ printf '[%s] %s\n' "$(date +'%F %T')" "$*"; }
trap 'rc=$?; log "ERROR on line $LINENO (exit $rc)"; exit $rc' ERR

# ---------- Requirements ----------
if ! command -v git >/dev/null 2>&1; then
  log "Installing git + rsync..."
  if command -v apt-get >/dev/null 2>&1; then
    $SUDO apt-get update && $SUDO apt-get install -y git rsync
  elif command -v dnf >/dev/null 2>&1; then
    $SUDO dnf install -y git rsync
  elif command -v yum >/dev/null 2>&1; then
    $SUDO yum install -y git rsync
  else
    log "git/rsync required; please install manually."
    exit 1
  fi
fi

# ---------- Prepare stage clone in home folder ----------
log "Stage dir: $STAGE_DIR"
mkdir -p "$STAGE_DIR"
if [[ ! -d "$STAGE_DIR/.git" ]]; then
  log "Cloning $REPO_URL ..."
  git clone --depth 1 "$REPO_URL" "$STAGE_DIR"
else
  log "Fetching latest from origin..."
  git -C "$STAGE_DIR" fetch --all --prune
fi

# Determine default branch (origin/HEAD), fallback to main/master
DEFAULT_BRANCH="$(git -C "$STAGE_DIR" symbolic-ref --quiet --short refs/remotes/origin/HEAD 2>/dev/null || true)"
DEFAULT_BRANCH="${DEFAULT_BRANCH#origin/}"
if [[ -z "${DEFAULT_BRANCH:-}" ]]; then
  if git -C "$STAGE_DIR" ls-remote --exit-code --heads origin main >/dev/null 2>&1; then
    DEFAULT_BRANCH="main"
  else
    DEFAULT_BRANCH="master"
  fi
fi
log "Default branch: $DEFAULT_BRANCH"

# Reset stage to remote HEAD
git -C "$STAGE_DIR" checkout -B "$DEFAULT_BRANCH" "origin/$DEFAULT_BRANCH"
git -C "$STAGE_DIR" reset --hard "origin/$DEFAULT_BRANCH"
git -C "$STAGE_DIR" clean -fdx
COMMIT="$(git -C "$STAGE_DIR" rev-parse --short HEAD)"
log "Prepared commit: $COMMIT"

# ---------- Rsync to webroot (preserve configs) ----------
RSYNC_ARGS=(-a --delete --omit-dir-times --human-readable --progress --itemize-changes)
for e in "${EXCLUDES[@]}"; do RSYNC_ARGS+=(--exclude="$e"); done
if [[ "$DRY_RUN" == "1" ]]; then
  RSYNC_ARGS+=(--dry-run)
  log "DRY RUN enabled — no changes will be written."
fi

log "Syncing to $WEB_ROOT ..."
$SUDO mkdir -p "$WEB_ROOT"
$SUDO rsync "${RSYNC_ARGS[@]}" "$STAGE_DIR"/ "$WEB_ROOT"/

# ---------- Permissions tuned for OGP panel ----------
WEB_USER="${OWNER:-www-data}"
WEB_GROUP="${GROUP:-www-data}"

log "Setting base permissions (OGP-safe)…"
# Base ownership
$SUDO chown -R "$OWNER:$GROUP" "$WEB_ROOT"

# Safe defaults: dirs 755, files 644  (batched; no “arg list too long”)
$SUDO find "$WEB_ROOT" -type d -exec chmod 755 {} +
$SUDO find "$WEB_ROOT" -type f -exec chmod 644 {} +

# Writable dirs for OGP
WRITABLE_NAMES="templates_c cache logs uploads storage tmp"
for name in $WRITABLE_NAMES; do
  $SUDO find "$WEB_ROOT" -type d -name "$name" -print0 | while IFS= read -r -d '' d; do
    log "Making writable dir: $d"
    $SUDO chown -R "$OWNER:$GROUP" "$d"
    $SUDO chmod -R 2775 "$d"
    if command -v setfacl >/dev/null 2>&1; then
      $SUDO setfacl -R -m g:$GROUP:rwx -m d:g:$GROUP:rwx "$d" || true
    fi
  done
done

# Keep your configs tight (and preserved from rsync by the script’s excludes)
# If the panel needs to write them via web UI, relax to 660 and owner www-data.
CFG1="$WEB_ROOT/includes/config.inc.php"
CFG2="$WEB_ROOT/modules/billing/includes/config.inc.php"
for cfg in "$CFG1" "$CFG2"; do
  if [[ -f "$cfg" ]]; then
    $SUDO chown "$WEB_USER:$WEB_GROUP" "$cfg"
    $SUDO chmod 640 "$cfg"
  fi
done

# Ensure billing folder itself is Apache-friendly (readable/executable)
$SUDO find "$WEB_ROOT/modules/billing" -type d -print0 | xargs -0 -r $SUDO chmod 755
$SUDO find "$WEB_ROOT/modules/billing" -type f -print0 | xargs -0 -r $SUDO chmod 644

log "Permissions set for OGP panel + billing."
