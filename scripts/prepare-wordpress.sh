#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
TMP_DIR="$(mktemp -d)"
ARCHIVE="$TMP_DIR/wordpress.tar.gz"
trap 'rm -rf "$TMP_DIR"' EXIT

if [[ -f "$ROOT_DIR/wp-settings.php" || -d "$ROOT_DIR/wp-admin" ]]; then
  printf '%s\n' 'WordPress core already appears to be installed.'
  exit 0
fi

command -v curl >/dev/null || { printf '%s\n' 'curl is required.' >&2; exit 1; }
command -v tar >/dev/null || { printf '%s\n' 'tar is required.' >&2; exit 1; }

printf '%s\n' 'Downloading the official WordPress package…'
curl -fsSL --retry 3 --connect-timeout 10 https://wordpress.org/latest.tar.gz -o "$ARCHIVE"
mkdir -p "$TMP_DIR/extracted"
tar -xzf "$ARCHIVE" -C "$TMP_DIR/extracted"

cp -R "$TMP_DIR/extracted/wordpress/wp-admin" "$ROOT_DIR/"
cp -R "$TMP_DIR/extracted/wordpress/wp-includes" "$ROOT_DIR/"
find "$TMP_DIR/extracted/wordpress" -maxdepth 1 -type f -exec cp {} "$ROOT_DIR/" \;

printf '%s\n' 'WordPress core is ready. Run scripts/serve-wordpress.sh and open /wp-admin/install.php.'

