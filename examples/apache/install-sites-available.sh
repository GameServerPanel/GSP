#!/usr/bin/env bash
set -euo pipefail

# Installs example Apache vhost configs for GSP Panel + Website.
#
# Usage (run as root or with sudo):
#   sudo ./install-sites-available.sh \
#     --source-dir /path/to/repo/examples/apache \
#     --dest-dir /etc/apache2/sites-available \
#     --panel-conf panel.example.com.conf \
#     --website-conf website.example.com.conf \
#     --enable-mods \
#     --enable-sites \
#     --reload
#
# Defaults are suitable for Debian/Ubuntu Apache layout.

SOURCE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DEST_DIR="/etc/apache2/sites-available"
PANEL_CONF="panel.example.com.conf"
WEBSITE_CONF="website.example.com.conf"
ENABLE_MODS=0
ENABLE_SITES=0
RELOAD=0
FORCE=0
RUN_CERTBOT=0
PANEL_DOMAINS=""
WEBSITE_DOMAINS=""

print_help() {
  cat <<'EOF'
Install GSP example Apache vhost files.

Options:
  --source-dir <path>     Directory containing *.conf files.
  --dest-dir <path>       Apache sites-available directory.
  --panel-conf <file>     Panel conf filename in source-dir.
  --website-conf <file>   Website conf filename in source-dir.
  --enable-mods           Run: a2enmod ssl rewrite headers
  --enable-sites          Run: a2ensite <panel-conf> <website-conf>
  --reload                Run apache2ctl configtest and reload apache2
  --run-certbot           Run certbot --apache for provided domains
  --panel-domains <list>  Comma-separated domains for panel certbot run
  --website-domains <list> Comma-separated domains for website certbot run
  --force                 Overwrite destination files if they exist
  -h, --help              Show this help text

Examples:
  sudo ./install-sites-available.sh --enable-mods --enable-sites --reload
  sudo ./install-sites-available.sh --force --enable-sites --reload
  sudo ./install-sites-available.sh --enable-mods --enable-sites --run-certbot \
    --panel-domains panel.example.com,www.panel.example.com \
    --website-domains gsp.example.com,www.gsp.example.com --reload
EOF
}

while [[ $# -gt 0 ]]; do
  case "$1" in
    --source-dir)
      SOURCE_DIR="$2"
      shift 2
      ;;
    --dest-dir)
      DEST_DIR="$2"
      shift 2
      ;;
    --panel-conf)
      PANEL_CONF="$2"
      shift 2
      ;;
    --website-conf)
      WEBSITE_CONF="$2"
      shift 2
      ;;
    --enable-mods)
      ENABLE_MODS=1
      shift
      ;;
    --enable-sites)
      ENABLE_SITES=1
      shift
      ;;
    --reload)
      RELOAD=1
      shift
      ;;
    --run-certbot)
      RUN_CERTBOT=1
      shift
      ;;
    --panel-domains)
      PANEL_DOMAINS="$2"
      shift 2
      ;;
    --website-domains)
      WEBSITE_DOMAINS="$2"
      shift 2
      ;;
    --force)
      FORCE=1
      shift
      ;;
    -h|--help)
      print_help
      exit 0
      ;;
    *)
      echo "Unknown option: $1" >&2
      print_help
      exit 1
      ;;
  esac
done

if [[ $EUID -ne 0 ]]; then
  echo "Run this script as root (or via sudo)." >&2
  exit 1
fi

PANEL_SRC="$SOURCE_DIR/$PANEL_CONF"
WEBSITE_SRC="$SOURCE_DIR/$WEBSITE_CONF"
PANEL_DST="$DEST_DIR/$PANEL_CONF"
WEBSITE_DST="$DEST_DIR/$WEBSITE_CONF"

if [[ ! -f "$PANEL_SRC" ]]; then
  echo "Panel conf not found: $PANEL_SRC" >&2
  exit 1
fi
if [[ ! -f "$WEBSITE_SRC" ]]; then
  echo "Website conf not found: $WEBSITE_SRC" >&2
  exit 1
fi
if [[ ! -d "$DEST_DIR" ]]; then
  echo "Destination directory does not exist: $DEST_DIR" >&2
  exit 1
fi

if [[ "$RUN_CERTBOT" -eq 1 ]]; then
  if [[ -z "$PANEL_DOMAINS" || -z "$WEBSITE_DOMAINS" ]]; then
    echo "--run-certbot requires both --panel-domains and --website-domains" >&2
    exit 1
  fi
  if ! command -v certbot >/dev/null 2>&1; then
    echo "certbot not found in PATH. Install certbot first." >&2
    exit 1
  fi
fi

copy_file() {
  local src="$1"
  local dst="$2"

  if [[ -f "$dst" && "$FORCE" -ne 1 ]]; then
    echo "Skipping existing file: $dst (use --force to overwrite)"
    return
  fi

  install -m 0644 "$src" "$dst"
  echo "Installed: $dst"
}

copy_file "$PANEL_SRC" "$PANEL_DST"
copy_file "$WEBSITE_SRC" "$WEBSITE_DST"

build_domain_args() {
  local list="$1"
  local normalized
  local domain
  normalized="${list//,/ }"
  for domain in $normalized; do
    if [[ -n "$domain" ]]; then
      printf '%s\n' "-d" "$domain"
    fi
  done
}

if [[ "$ENABLE_MODS" -eq 1 ]]; then
  echo "Enabling Apache modules: ssl rewrite headers"
  a2enmod ssl rewrite headers
fi

if [[ "$ENABLE_SITES" -eq 1 ]]; then
  echo "Enabling Apache sites: $PANEL_CONF $WEBSITE_CONF"
  a2ensite "$PANEL_CONF" "$WEBSITE_CONF"
fi

if [[ "$RELOAD" -eq 1 ]]; then
  echo "Validating Apache config"
  apache2ctl configtest
  echo "Reloading Apache"
  systemctl reload apache2
fi

if [[ "$RUN_CERTBOT" -eq 1 ]]; then
  echo "Running Certbot for panel domains: $PANEL_DOMAINS"
  mapfile -t panel_domain_args < <(build_domain_args "$PANEL_DOMAINS")
  certbot --apache "${panel_domain_args[@]}"

  echo "Running Certbot for website domains: $WEBSITE_DOMAINS"
  mapfile -t website_domain_args < <(build_domain_args "$WEBSITE_DOMAINS")
  certbot --apache "${website_domain_args[@]}"

  echo "Re-validating Apache config after Certbot changes"
  apache2ctl configtest
  echo "Reloading Apache after Certbot changes"
  systemctl reload apache2
fi

echo "Done."
echo "Next steps:"
echo "  1) Edit ServerName/ServerAlias/DocumentRoot values in $PANEL_DST and $WEBSITE_DST"
if [[ "$RUN_CERTBOT" -eq 1 ]]; then
  echo "  2) Certbot has been run for the supplied domains."
else
  echo "  2) Run Certbot for real domains, e.g.:"
  echo "     certbot --apache -d panel.example.com -d www.panel.example.com"
  echo "     certbot --apache -d gsp.example.com -d www.gsp.example.com"
fi
