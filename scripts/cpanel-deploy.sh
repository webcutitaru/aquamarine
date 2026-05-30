#!/bin/sh
# deploy-v3 — fără set -e; cp -R * (nu cp -Ra) pe acest hosting

DEPLOYPATH="${DEPLOYPATH:-/home/aquamari1/public_html/}"
HTACCESS_PATHS=".htaccess data/.htaccess includes/.htaccess database/.htaccess"

copy_htaccess() {
  src="$1"
  dest="$2"
  if [ -f "$src" ]; then
    mkdir -p "$(dirname "$dest")" 2>/dev/null || true
    cp -f "$src" "$dest" 2>/dev/null || true
    chmod 644 "$dest" 2>/dev/null || true
  fi
}

for rel in $HTACCESS_PATHS; do
  copy_htaccess "$rel" "$DEPLOYPATH$rel"
done

/bin/cp -R * "$DEPLOYPATH" 2>/dev/null || true

for rel in $HTACCESS_PATHS; do
  copy_htaccess "$rel" "$DEPLOYPATH$rel"
done
