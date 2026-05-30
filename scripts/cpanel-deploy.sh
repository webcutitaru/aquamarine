#!/bin/sh
set -e

DEPLOYPATH="${DEPLOYPATH:-/home/aquamari1/public_html/}"

HTACCESS_PATHS=".htaccess data/.htaccess includes/.htaccess database/.htaccess"

copy_htaccess() {
  src="$1"
  dest="$2"
  if [ -f "$src" ]; then
    mkdir -p "$(dirname "$dest")"
    cp -f "$src" "$dest"
    chmod 644 "$dest"
  fi
}

for rel in $HTACCESS_PATHS; do
  copy_htaccess "$rel" "$DEPLOYPATH$rel"
done

cp -Ra . "$DEPLOYPATH"

rm -rf "$DEPLOYPATH.git" "$DEPLOYPATHnode_modules" 2>/dev/null || true

for rel in $HTACCESS_PATHS; do
  if [ -f "$DEPLOYPATH$rel" ]; then
    chmod 644 "$DEPLOYPATH$rel"
  fi
done
