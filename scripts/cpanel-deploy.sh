#!/bin/sh
# deploy-v5 — copiere whitelist în public_html (fără database/, meta-dev)

DEPLOYPATH="${DEPLOYPATH:-/home/aquamari1/public_html/}"
HTACCESS_PATHS=".htaccess data/.htaccess includes/.htaccess"
DEPLOY_DIRS="admin assets data includes lang ru"

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

for dir in $DEPLOY_DIRS; do
  if [ -d "$dir" ]; then
    /bin/cp -R "$dir" "$DEPLOYPATH" 2>/dev/null || true
  fi
done

for f in ./*.php; do
  [ -f "$f" ] || continue
  /bin/cp -f "$f" "$DEPLOYPATH" 2>/dev/null || true
done

for f in robots.txt composer.json composer.lock; do
  if [ -f "$f" ]; then
    /bin/cp -f "$f" "$DEPLOYPATH" 2>/dev/null || true
  fi
done

for f in ./*.pdf; do
  [ -f "$f" ] || continue
  /bin/cp -f "$f" "$DEPLOYPATH" 2>/dev/null || true
done

for rel in $HTACCESS_PATHS; do
  copy_htaccess "$rel" "$DEPLOYPATH$rel"
done

echo "$(date -u +%Y-%m-%dT%H:%M:%SZ) deploy-v5" > "$DEPLOYPATH.deploy-marker"
chmod 644 "$DEPLOYPATH.deploy-marker"
