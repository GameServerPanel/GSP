#!/usr/bin/env bash
set -euo pipefail

if [[ $# -lt 2 ]]; then
  echo "Usage: $0 <appid> <query> [page] [limit]" >&2
  exit 2
fi

appid="$1"
query="$2"
page="${3:-1}"
limit="${4:-0}"

if ! [[ "$appid" =~ ^[0-9]+$ ]]; then
  echo "AppID must be numeric." >&2
  exit 3
fi

if ! [[ "$page" =~ ^[0-9]+$ ]]; then
  page=1
fi

if ! [[ "$limit" =~ ^[0-9]+$ ]]; then
  limit=0
fi

user_agent='Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36'

curl -s --compressed -A "$user_agent" \
  --get "https://steamcommunity.com/workshop/browse/" \
  --data-urlencode "appid=${appid}" \
  --data-urlencode "browsesort=textsearch" \
  --data-urlencode "section=readytouseitems" \
  --data-urlencode "searchtext=${query}" \
  --data-urlencode "p=${page}" \
| grep -oE 'sharedfiles/filedetails/\?id=[0-9]+' \
| sed -E 's/.*id=//' \
| sort -u \
| {
    count=0
    while IFS= read -r id; do
      [[ -z "$id" ]] && continue
      ((count++))
      if [[ "$limit" -gt 0 && "$count" -gt "$limit" ]]; then
        break
      fi
      title=$(curl -s --compressed -A "$user_agent" "https://steamcommunity.com/sharedfiles/filedetails/?id=${id}" \
        | tr '\n' ' ' \
        | sed -nE 's/.*<title>([^<]+)<\/title>.*/\1/p' \
        | sed -E 's/ - Steam (Community|Workshop).*//' \
        | sed -E 's/^\s+|\s+$//g')
      if [[ -z "$title" ]]; then
        title="(title parse failed)"
      fi
      printf '%s\t%s\n' "$id" "$title"
    done
  }
