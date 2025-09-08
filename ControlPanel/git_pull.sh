#!/usr/bin/env bash
set -euo pipefail
# Usage: ./git_pull.sh <repo> [branch]
#  ex:   ./git_pull.sh Gameservers-World/ControlPanel main
#        ./git_pull.sh ControlPanel main   (set ORG_DEFAULT env to your org)
# Overwrites local files to exactly match remote (hard reset + clean).

WORKDIR="/var/www/HTML/panel"
REPO_ARG="${1:-}"; [[ -n "$REPO_ARG" ]] || { echo "Usage: $0 <repo> [branch]"; exit 2; }
BRANCH="${2:-${BRANCH:-main}}"

# Allow "Repo" or "Org/Repo"
ORG_DEFAULT="${ORG_DEFAULT:-YOURORG}"
if [[ "$REPO_ARG" == */* ]]; then
  SLUG="$REPO_ARG"; REPO_NAME="${REPO_ARG##*/}"
else
  SLUG="$ORG_DEFAULT/$REPO_ARG"; REPO_NAME="$REPO_ARG"
fi
REPO_URL="${REPO_URL:-git@github.com:${SLUG}.git}"

# Optional SSH key
if [[ -n "${SSH_KEY:-}" ]]; then export GIT_SSH_COMMAND="ssh -i ${SSH_KEY} -o IdentitiesOnly=yes"; fi

log(){ printf '[%s] %s\n' "$(date +'%F %T')" "$*"; }

cd "$WORKDIR"

# Ensure this is (or becomes) a git repo
if [[ ! -d .git ]]; then
  log "Initializing git repo in $WORKDIR"
  git init
fi

# Configure/refresh origin
if git remote get-url origin >/dev/null 2>&1; then
  git remote set-url origin "$REPO_URL"
else
  git remote add origin "$REPO_URL"
fi

# Fetch and force local to match remote branch exactly
log "Fetching from $REPO_URL"
git fetch origin --prune

# Create/switch branch directly to remote tip
if git show-ref --verify --quiet "refs/remotes/origin/$BRANCH"; then
  log "Checking out branch $BRANCH at origin/$BRANCH"
  git checkout -B "$BRANCH" "origin/$BRANCH"
else
  echo "Remote branch 'origin/$BRANCH' not found. Available branches:" >&2
  git branch -r >&2
  exit 3
fi

# Hard reset + remove untracked/ignored files: overwrite everything locally
log "Resetting work tree to origin/$BRANCH and cleaning untracked files"
git reset --hard "origin/$BRANCH"
git clean -fdx

# If you use submodules, keep them in sync
git submodule update --init --recursive || true

log "Pull/overwrite complete."
