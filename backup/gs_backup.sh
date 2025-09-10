#!/usr/bin/env bash
# Mirror /home/gameserver to EITHER:
#   remote: bak-kc-01.iaregamer.com:/sdb1/backup/<host>/gameserver
#   local : <LOCAL_BASE>/<host>/gameserver
#
# Usage:
#   gs-backup.sh remote [--dry-run] [--verify] [--no-delete]
#   gs-backup.sh local  [--dry-run] [--verify] [--no-delete]
#
# --dry-run : simulate only (no writes)
# --verify  : report differences; exit 0 if in-sync, 3 if drift detected
# --no-delete : do not delete extraneous destination files
#
# Env you can override:
#   REMOTE_HOST=bak-kc-01.iaregamer.com  REMOTE_USER=gameserver  REMOTE_BASE=/sdb1/backup
#   LOCAL_BASE=/backup  SRC_DIR=/home/gameserver
#   BW_PEAK_MBIT=100  BW_OFFPEAK_MBIT=300  PEAK_START=16  PEAK_END=23
#   SSH_KEY=/home/gameserver/.ssh/id_rsa
#   VERIFY_STRICT=0 (set 1 to use --checksum in verify)

set -euo pipefail

# ---------- MODE ----------
MODE="${1:-}"
shift || true
if [[ "$MODE" != "remote" && "$MODE" != "local" ]]; then
  echo "Usage: $0 {remote|local} [--dry-run] [--verify] [--no-delete]" >&2
  exit 2
fi

# ---------- FLAGS ----------
DRY_RUN=0
VERIFY=0
DELETE=1
while [[ $# -gt 0 ]]; do
  case "$1" in
    --dry-run) DRY_RUN=1 ;;
    --verify)  VERIFY=1 ; DRY_RUN=1 ;;   # verify implies dry-run
    --no-delete) DELETE=0 ;;
    *) echo "Unknown option: $1" >&2; exit 2 ;;
  esac
  shift
done

# ---------- DESTS / SOURCE ----------
REMOTE_HOST="${REMOTE_HOST:-bak-kc-01.iaregamer.com}"
REMOTE_USER="${REMOTE_USER:-gameserver}"
REMOTE_BASE="${REMOTE_BASE:-/sdb1/backup}"
LOCAL_BASE="${LOCAL_BASE:-/backup}"
SRC_DIR="${SRC_DIR:-/home/gameserver}"

SSH_KEY="${SSH_KEY:-/home/gameserver/.ssh/id_rsa}"
SSH_OPTS=( -o BatchMode=yes -o IdentitiesOnly=yes -i "$SSH_KEY" )

# ---------- Throttle caps (Mb/s) by time-of-day ----------
BW_PEAK_MBIT="${BW_PEAK_MBIT:-100}"
BW_OFFPEAK_MBIT="${BW_OFFPEAK_MBIT:-300}"
PEAK_START="${PEAK_START:-16}"
PEAK_END="${PEAK_END:-23}"

# ---------- Misc ----------
LOCK_FILE="${LOCK_FILE:-/var/lock/gs-backup.lock}"
LOG_FILE="${LOG_FILE:-/var/log/gs-backup.log}"
PARTIAL_DIR=".rsync-partial"
VERIFY_STRICT="${VERIFY_STRICT:-0}"

# Optional excludes (uncomment any you want)
EXCLUDES=(
  # "--exclude=/home/gameserver/**/logs/**"
  # "--exclude=/home/gameserver/**/*.log"
  # "--exclude=/home/gameserver/**/cache/**"
  # "--exclude=/home/gameserver/**/steamapps/downloading/**"
  # "--exclude=/home/gameserver/**/steamapps/temp/**"
)

log(){ printf '[%s] %s\n' "$(date +'%F %T')" "$*" | tee -a "$LOG_FILE" ; }

# ---------- Pre-flight ----------
[[ -d "$SRC_DIR" ]] || { echo "Missing SRC_DIR: $SRC_DIR" >&2; exit 1; }
command -v rsync >/dev/null || { echo "rsync not found" >&2; exit 1; }
command -v ionice >/dev/null || { echo "ionice not found" >&2; exit 1; }

HOST_SHORT="$(hostname -s)"

# Cap selector
hour="$(date +%H)"
if [[ "$hour" -ge "$PEAK_START" && "$hour" -le "$PEAK_END" ]]; then
  CAP_MBIT="$BW_PEAK_MBIT"
else
  CAP_MBIT="$BW_OFFPEAK_MBIT"
fi
BW_KBPS="$(( CAP_MBIT * 1000 / 8 ))"   # rsync expects KB/s

# Build rsync base flags
RSYNC_BASE=( rsync -aHAX --numeric-ids --info=stats2 )
# Deletions (skip if --no-delete or verify w/o strict cleanup)
if [[ $DELETE -eq 1 ]]; then
  RSYNC_BASE+=( --delete-delay )
fi

# Dry-run?
if [[ $DRY_RUN -eq 1 ]]; then
  RSYNC_BASE+=( -n )
fi

# Itemize changes always in dry-run/verify so we can see drift
if [[ $DRY_RUN -eq 1 ]]; then
  RSYNC_BASE+=( --itemize-changes --out-format='%i %n' )
fi

# Verify strict? (checksum-based comparison is heavier; size+mtime otherwise)
if [[ $VERIFY -eq 1 && $VERIFY_STRICT -eq 1 ]]; then
  RSYNC_BASE+=( --checksum )
fi

# Performance / niceness
RSYNC_BASE+=( --partial --partial-dir="$PARTIAL_DIR" --bwlimit="$BW_KBPS" )

# Excludes
RSYNC_BASE+=( "${EXCLUDES[@]}" )

# Ensure local partial dir exists
mkdir -p "$SRC_DIR/$PARTIAL_DIR" || true

# Compose destination
if [[ "$MODE" == "remote" ]]; then
  DEST="${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_BASE}/${HOST_SHORT}/gameserver/"
  DEST_DESC="REMOTE ${REMOTE_HOST}:${REMOTE_BASE}/${HOST_SHORT}/gameserver"
else
  DEST="${LOCAL_BASE%/}/${HOST_SHORT}/gameserver/"
  DEST_DESC="LOCAL ${LOCAL_BASE%/}/${HOST_SHORT}/gameserver"
fi

# Ensure destination path exists
if [[ "$MODE" == "remote" ]]; then
  ssh "${SSH_OPTS[@]}" "${REMOTE_USER}@${REMOTE_HOST}" "mkdir -p '${REMOTE_BASE}/${HOST_SHORT}/gameserver'" || true
else
  mkdir -p "$DEST"
fi

# ----------------- RUN -----------------
{
  flock -n 9 || { log "Another run is active; exiting."; exit 0; }

  log "MODE=$MODE  DRY_RUN=$DRY_RUN  VERIFY=$VERIFY  DELETE=$DELETE"
  log "SRC=$SRC_DIR/  ->  $DEST_DESC"
  log "Cap: ${CAP_MBIT} Mb/s (bwlimit=${BW_KBPS} KB/s)"

  # Execute rsync
  if [[ "$MODE" == "remote" ]]; then
    CMD=( "${RSYNC_BASE[@]}" -e "ssh ${SSH_OPTS[*]}" "$SRC_DIR/" "$DEST" )
  else
    CMD=( "${RSYNC_BASE[@]}" "$SRC_DIR/" "$DEST" )
  fi

  # In verify mode, capture output to detect drift
  CHANGES=0
  if [[ $VERIFY -eq 1 ]]; then
    TMP="$(mktemp)"
    set +e
    ionice -c3 nice -n 19 "${CMD[@]}" >"$TMP" 2>&1
    RC=$?
    set -e
    # Count itemized change lines (exclude rsync headers/footers)
    if grep -qE '^[<>ch\*\.][^ ]{9} ' "$TMP"; then
      CHANGES=1
    fi
    cat "$TMP" | tee -a "$LOG_FILE"
    rm -f "$TMP"

    if [[ $RC -ne 0 ]]; then
      log "Verify run encountered rsync errors (rc=$RC)."
      exit $RC
    fi

    if [[ $CHANGES -eq 1 ]]; then
      log "VERIFY: Drift detected between source and destination."
      exit 3
    else
      log "VERIFY: Source and destination are in sync."
      exit 0
    fi
  else
    # Normal (or dry-run) run without drift check exit code
    ionice -c3 nice -n 19 "${CMD[@]}"
    RC=$?
    if [[ $RC -eq 0 ]]; then
      log "Completed successfully."
    else
      log "Rsync exited with code $RC."
    fi
    exit $RC
  fi

} 9>"$LOCK_FILE"
