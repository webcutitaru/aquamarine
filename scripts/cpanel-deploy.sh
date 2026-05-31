#!/bin/sh
# deploy-v4 — cp -R *; htaccess + chmod strict; marker în public_html

DEPLOYPATH="${DEPLOYPATH:-/home/aquamari1/public_html/}"
HTACCESS_PATHS=".htaccess data/.htaccess includes/.htaccess database/.htaccess"

chmod 755 "$DEPLOYPATH" 2>/dev/null || true

copy_htaccess() {
  src="$1"
  dest="$2"
  if [ -f "$src" ]; then
    mkdir -p "$(dirname "$dest")"
    /bin/cp -f "$src" "$dest"
    /bin/chmod 644 "$dest"
  fi
}

for rel in $HTACCESS_PATHS; do
  copy_htaccess "$rel" "$DEPLOYPATH$rel"
done

/bin/cp -R * "$DEPLOYPATH" 2>/dev/null || true

for rel in $HTACCESS_PATHS; do
  copy_htaccess "$rel" "$DEPLOYPATH$rel"
done

echo "$(date -u +%Y-%m-%dT%H:%M:%SZ) deploy-v4" > "$DEPLOYPATH.deploy-marker"
chmod 644 "$DEPLOYPATH.deploy-marker"
