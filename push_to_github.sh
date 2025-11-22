#!/usr/bin/env bash
set -Eeuo pipefail
umask 022

# ---------- HARD-CODED PROJECT SETTINGS ----------
PANEL_DIR="/var/www/html/panel"
TOKEN_FILE="/home/gameserver/git.token"

UPSTREAM_REPO="GameServerPanel/GSP"                 # owner/repo
UPSTREAM_URL="https://github.com/${UPSTREAM_REPO}.git"

BR_PREFIX="panel-sync"                              # branch prefix for PRs
GIT_USER_NAME="Server Sync Bot"
GIT_USER_EMAIL="server-sync@local"

log(){ printf '[%s] %s\n' "$(date +'%F %T')" "$*"; }
die(){ echo "ERROR: $*" >&2; exit 1; }

# ---------- PRECHECKS ----------
[[ -d "$PANEL_DIR" ]] || die "Panel dir not found: $PANEL_DIR"
[[ -s "$TOKEN_FILE" ]] || die "Token file missing/empty: $TOKEN_FILE"
TOKEN="$(<"$TOKEN_FILE")"
[[ ${#TOKEN} -ge 10 ]] || die "Token in $TOKEN_FILE looks invalid"

cd "$PANEL_DIR"
if [[ ! -d ".git" ]]; then
  log "No .git found here; initializing a repo."
  git init
fi

git config --global --add safe.directory "$PANEL_DIR" || true
git config user.name  "$GIT_USER_NAME"
git config user.email "$GIT_USER_EMAIL"

# ---------- SAFEGUARDS: ignore secrets/runtime ----------
ensure_ignore(){ grep -qxF "$1" .gitignore 2>/dev/null || echo "$1" >> .gitignore; }
touch .gitignore
ensure_ignore "includes/config.inc.php"
ensure_ignore "modules/billing/includes/config.inc.php"
ensure_ignore "templates_c/"
ensure_ignore "cache/"
ensure_ignore "logs/"
ensure_ignore "uploads/"
ensure_ignore "storage/"
ensure_ignore "tmp/"
ensure_ignore "modules/*/templates_c/"
ensure_ignore "modules/*/cache/"
ensure_ignore "modules/*/logs/"
ensure_ignore "modules/*/uploads/"
ensure_ignore "themes/*/dist/php/uploads/"
ensure_ignore "*.log"
ensure_ignore "*.bak"
ensure_ignore "*.orig"
ensure_ignore ".DS_Store"
ensure_ignore "*.zip"
ensure_ignore "*.tar"
ensure_ignore "*.tar.gz"
ensure_ignore "*.7z"
ensure_ignore "vendor/"
ensure_ignore "node_modules/"

# If secrets were ever tracked, stop tracking (keep files on disk)
git rm --cached -f includes/config.inc.php modules/billing/includes/config.inc.php 2>/dev/null || true

# ---------- XXL file check (>100MB rejected by GitHub) ----------
LARGE=$(find . -type f -not -path './.git/*' -size +100M -printf '%s\t%p\n' || true)
if [[ -n "$LARGE" ]]; then
  echo "The following files exceed 100 MB and will be rejected by GitHub:"
  echo "$LARGE" | awk '{printf " - %.2f MB  %s\n", $1/1024/1024, $2}'
  die "Remove or Git LFS these files before pushing."
fi

# ---------- Commit changes on a fresh branch ----------
git add -A
if git diff --cached --quiet; then
  log "No changes to commit."
else
  git commit -m "Panel sync: $(date +'%F %T %Z')"
fi

STAMP="$(date +%Y%m%d-%H%M%S)"
NEW_BRANCH="${BR_PREFIX}-${STAMP}"
git checkout -B "$NEW_BRANCH"

# ---------- Discover upstream default branch via API ----------
API_REPO_JSON="$(curl -fsSL -H "Authorization: token ${TOKEN}" \
  "https://api.github.com/repos/${UPSTREAM_REPO}")" || die "Cannot reach GitHub API for ${UPSTREAM_REPO}"
BASE_BRANCH="$(printf '%s\n' "$API_REPO_JSON" | sed -n 's/.*"default_branch"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p')"
[[ -n "$BASE_BRANCH" ]] || BASE_BRANCH="main"

# ---------- Try pushing branch directly to UPSTREAM ----------
AUTH_UPSTREAM_URL="https://${TOKEN}@github.com/${UPSTREAM_REPO}.git"
log "Attempting to push ${NEW_BRANCH} to upstream ${UPSTREAM_REPO} ..."
if git push "$AUTH_UPSTREAM_URL" "$NEW_BRANCH"; then
  log "Pushed to upstream. Creating PR (same-repo head=${NEW_BRANCH} → base=${BASE_BRANCH}) ..."
  PR_BODY="Automated push from ${HOSTNAME} at ${STAMP}."
  PR_PAYLOAD=$(printf '{ "title":"%s","head":"%s","base":"%s","body":"%s" }' \
                        "Panel sync: ${STAMP}" "${NEW_BRANCH}" "${BASE_BRANCH}" "${PR_BODY}")
  PR_RESP=$(curl -fsS -H "Authorization: token ${TOKEN}" \
                 -H "Accept: application/vnd.github+json" \
                 -X POST "https://api.github.com/repos/${UPSTREAM_REPO}/pulls" \
                 -d "$PR_PAYLOAD") || die "PR creation failed."
  PR_URL=$(echo "$PR_RESP" | sed -n 's/.*"html_url":[[:space:]]*"\([^"]*\)".*/\1/p')
  [[ -n "$PR_URL" ]] || { echo "$PR_RESP"; die "Could not parse PR URL."; }
  echo "Pull Request created: $PR_URL"
  exit 0
fi

# ---------- No push rights → fork to your account, push there, open PR ----------
log "No upstream push rights. Using your fork."

# Who am I?
ME_JSON="$(curl -fsSL -H "Authorization: token ${TOKEN}" https://api.github.com/user)" || die "Cannot fetch /user"
ME_LOGIN="$(printf '%s\n' "$ME_JSON" | sed -n 's/.*"login"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p')"
[[ -n "$ME_LOGIN" ]] || die "Could not determine user login from token."

# Ensure fork exists (create if needed)
FORK_REPO="${ME_LOGIN}/GSP"
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" -H "Authorization: token ${TOKEN}" "https://api.github.com/repos/${FORK_REPO}")
if [[ "$HTTP_CODE" != "200" ]]; then
  log "Creating fork ${FORK_REPO} ..."
  curl -fsS -H "Authorization: token ${TOKEN}" \
       -X POST "https://api.github.com/repos/${UPSTREAM_REPO}/forks" >/dev/null
  # Wait for GitHub to finish fork
  for i in {1..15}; do
    sleep 2
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" -H "Authorization: token ${TOKEN}" "https://api.github.com/repos/${FORK_REPO}")
    [[ "$HTTP_CODE" == "200" ]] && break
  done
  [[ "$HTTP_CODE" == "200" ]] || die "Fork did not become available."
fi

AUTH_FORK_URL="https://${TOKEN}@github.com/${FORK_REPO}.git"
log "Pushing branch ${NEW_BRANCH} to fork ${FORK_REPO} ..."
git push -u "$AUTH_FORK_URL" "$NEW_BRANCH"

# Create PR from fork: yourlogin:branch -> upstream:base
PR_BODY="Automated push from ${HOSTNAME} at ${STAMP}."
PR_PAYLOAD=$(printf '{ "title":"%s","head":"%s:%s","base":"%s","body":"%s" }' \
                      "Panel sync: ${STAMP}" "${ME_LOGIN}" "${NEW_BRANCH}" "${BASE_BRANCH}" "${PR_BODY}")
PR_RESP=$(curl -fsS -H "Authorization: token ${TOKEN}" \
               -H "Accept: application/vnd.github+json" \
               -X POST "https://api.github.com/repos/${UPSTREAM_REPO}/pulls" \
               -d "$PR_PAYLOAD") || die "PR creation failed."

PR_URL=$(echo "$PR_RESP" | sed -n 's/.*"html_url":[[:space:]]*"\([^"]*\)".*/\1/p')
[[ -n "$PR_URL" ]] || { echo "$PR_RESP"; die "Could not parse PR URL."; }

echo "Pull Request created: $PR_URL"
log "Done."
