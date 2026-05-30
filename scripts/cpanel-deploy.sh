#!/bin/sh
set -e

DEPLOYPATH="${DEPLOYPATH:-/home/aquamari1/public_html/}"

cp -R * "$DEPLOYPATH"

if [ -f .htaccess ]; then
  cp -f .htaccess "$DEPLOYPATH"
fi

if [ -f data/.htaccess ]; then
  mkdir -p "$DEPLOYPATH/data"
  cp -f data/.htaccess "$DEPLOYPATH/data/"
fi
