#!/bin/sh
set -e

DEPLOYPATH="${DEPLOYPATH:-/home/aquamari1/public_html/}"

cp -R * "$DEPLOYPATH"

copy_htaccess() {
  src="$1"
  dest="$2"
  if [ -f "$src" ]; then
    mkdir -p "$(dirname "$dest")"
    cp -f "$src" "$dest"
    chmod 644 "$dest"
  fi
}

copy_htaccess .htaccess "$DEPLOYPATH.htaccess"
copy_htaccess data/.htaccess "$DEPLOYPATH/data/.htaccess"
copy_htaccess includes/.htaccess "$DEPLOYPATH/includes/.htaccess"
copy_htaccess database/.htaccess "$DEPLOYPATH/database/.htaccess"
