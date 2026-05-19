#!/usr/bin/env bash
set -euo pipefail

MANIFEST_PATH="${1:-}"
if [[ -z "$MANIFEST_PATH" ]]; then
  echo "Usage: $0 <manifest_path>"
  exit 1
fi

if [[ ! -f "$MANIFEST_PATH" ]]; then
  echo "Manifest not found: $MANIFEST_PATH"
  exit 1
fi

MANIFEST_DIR="$(dirname "$MANIFEST_PATH")"
WORKSHOP_DIR="${MANIFEST_DIR}/workshop"
REMOVED_DIR="${WORKSHOP_DIR}/removed"
LOG_FILE="${MANIFEST_DIR}/workshop_phase1.log"

mkdir -p "$WORKSHOP_DIR" "$REMOVED_DIR"

ACTION="$(python3 - <<'PY' "$MANIFEST_PATH"
import json,sys
with open(sys.argv[1], "r", encoding="utf-8") as f:
    data=json.load(f)
print(data.get("action",""))
PY
)"

ITEMS="$(python3 - <<'PY' "$MANIFEST_PATH"
import json,sys
with open(sys.argv[1], "r", encoding="utf-8") as f:
    data=json.load(f)
items=data.get("items",[])
print(",".join(str(x) for x in items if str(x).isdigit()))
PY
)"

{
  echo "[$(date '+%Y-%m-%d %H:%M:%S')] workshop_phase1 action=${ACTION} manifest=${MANIFEST_PATH}"
  echo "[$(date '+%Y-%m-%d %H:%M:%S')] workshop_phase1 items=${ITEMS}"
} >> "$LOG_FILE"

case "$ACTION" in
  install|update)
    # TODO: Replace with game-specific SteamCMD workshop install/update logic.
    # Example flow:
    #   1) Use workshop_app_id + item IDs from the manifest.
    #   2) Download/refresh content into a controlled staging folder.
    #   3) Copy/sync approved files into the game server content path.
    ;;
  remove)
    # Phase 1 safety behavior: avoid destructive delete.
    # TODO: move/disable per-item content using game-specific mapping rules.
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] remove requested; preserving files (non-destructive phase 1)." >> "$LOG_FILE"
    ;;
  *)
    echo "Unknown workshop action: ${ACTION}" >> "$LOG_FILE"
    exit 1
    ;;
esac

exit 0
