#!/usr/bin/env bash
set -euo pipefail
# Usage: ./git_force_backup.sh <repo> [branch]
#  ex:   ./git_force_backup.sh ControlPanel backup
#        ./git_force_backup.sh Gameservers-World/ControlPanel main

REPO_ARG="${1:-}"; [[ -n "$REPO_ARG" ]] || { echo "Usage: $0 <repo> [branch]"; exit 2; }
BRANCH="${2:-${BRANCH:-backup}}"

# If you pass just "Repo", we'll prepend your org; passing "Org/Repo" also works
ORG_DEFAULT="${ORG_DEFAULT:-Gameservers-World}"
if [[ "$REPO_ARG" == */* ]]; then
  SLUG="$REPO_ARG"; REPO_NAME="${REPO_ARG##*/}"
else
  SLUG="$ORG_DEFAULT/$REPO_ARG"; REPO_NAME="$REPO_ARG"
fi
REPO_URL="${REPO_URL:-git@github.com:${SLUG}.git}"

# Where your web/panel files live (work tree); change if needed
WORK_TREE="${WORK_TREE:-/var/www/html/panel}"

# Keep .git data outside the webroot (safer)
GIT_DIR_BASE="${GIT_DIR_BASE:-/home/gameserver/.git-panels}"
GIT_DIR_PATH="${GIT_DIR_PATH:-$GIT_DIR_BASE/${REPO_NAME}.git}"

# Git identity + SSH key
GIT_NAME="${GIT_NAME:-GSW Backup Bot}"
GIT_EMAIL="${GIT_EMAIL:-iaretechnician@gmail.com}"
SSH_KEY="${SSH_KEY:-/home/gameserver/.ssh/gh_backup}"
export GIT_SSH_COMMAND="ssh -i ${SSH_KEY} -o IdentitiesOnly=yes"

log(){ printf '[%s] %s\n' "$(date +'%F %T')" "$*"; }
mkdir -p "$GIT_DIR_BASE"

# Init a separate git-dir linked to your work tree (first run only)
if [[ ! -d "$GIT_DIR_PATH" ]]; then
  log "Initializing repo dir: $GIT_DIR_PATH → worktree: $WORK_TREE"
  git init --bare "$GIT_DIR_PATH"
  git --git-dir="$GIT_DIR_PATH" config core.worktree "$WORK_TREE"
  git --git-dir="$GIT_DIR_PATH" config --bool core.bare false
  git --git-dir="$GIT_DIR_PATH" config user.name  "$GIT_NAME"
  git --git-dir="$GIT_DIR_PATH" config user.email "$GIT_EMAIL"
  git --git-dir="$GIT_DIR_PATH" symbolic-ref HEAD "refs/heads/$BRANCH" || true
  git config --global --add safe.directory "$WORK_TREE" || true
  if git --git-dir="$GIT_DIR_PATH" remote get-url origin >/dev/null 2>&1; then
    git --git-dir="$GIT_DIR_PATH" remote set-url origin "$REPO_URL"
  else
    git --git-dir="$GIT_DIR_PATH" remote add origin "$REPO_URL"
  fi
fi

# Stage everything and always create a commit (even if empty)
log "Staging all changes from $WORK_TREE"
git --git-dir="$GIT_DIR_PATH" --work-tree="$WORK_TREE" add -A
MSG="backup($(hostname -s)): $(date -u '+%F %T UTC')"
log "Committing: $MSG"
git --git-dir="$GIT_DIR_PATH" --work-tree="$WORK_TREE" commit -m "$MSG" --allow-empty

# Force-push branch (CAUTION: rewrites remote branch history)
log "Force pushing $BRANCH to $REPO_URL"
git --git-dir="$GIT_DIR_PATH" --work-tree="$WORK_TREE" push origin "$BRANCH" --force

# Optional daily tag (forced)
if [[ "${TAG_DAILY:-true}" =~ ^(1|y|Y|true|TRUE)$ ]]; then
  TAG="daily-$(date +%F)"
  log "Updating daily tag: $TAG"
  git --git-dir="$GIT_DIR_PATH" tag -f "$TAG" || true
  git --git-dir="$GIT_DIR_PATH" push origin "refs/tags/$TAG" --force || true
fi

log "Done."

