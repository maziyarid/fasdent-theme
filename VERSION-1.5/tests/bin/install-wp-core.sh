#!/usr/bin/env bash
# Download the WordPress core files the unit tests load for real sanitization behaviour.
#
# The tests only require wp-includes/{compat,plugin,formatting,kses}.php, so a plain
# WordPress tarball is enough — no database, no wp-config.php, no test suite install.
#
# Override the location with WP_CORE_DIR when WordPress is already available locally.
set -euo pipefail

WP_VERSION="${WP_VERSION:-6.7.1}"
TESTS_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
TARGET="${WP_CORE_DIR:-$TESTS_DIR/.wp-core}"

if [ -f "$TARGET/wp-includes/kses.php" ]; then
	echo "WordPress core already present at $TARGET"
	exit 0
fi

TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT

echo "Downloading WordPress $WP_VERSION ..."
curl -fsSL -o "$TMP/wordpress.tar.gz" "https://wordpress.org/wordpress-${WP_VERSION}.tar.gz"
tar -xzf "$TMP/wordpress.tar.gz" -C "$TMP"

mkdir -p "$TARGET"
cp -R "$TMP/wordpress/." "$TARGET/"

echo "WordPress core installed at $TARGET"
