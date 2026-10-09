#!/usr/bin/env bash
# Build an installable plugin ZIP: dist/xlights-elementor-widgets-<version>.zip
# The archive contains a single top-level folder "xlights-elementor-widgets/".
# Requires: bash, zip. PHP (optional) is used to lint every file first.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SLUG="xlights-elementor-widgets"
SRC="$ROOT/$SLUG"
VERSION="$(grep -m1 -E '^[[:space:]]*\*[[:space:]]*Version:' "$SRC/$SLUG.php" | awk '{print $NF}')"
DIST="$ROOT/dist"
ZIP="$DIST/$SLUG-$VERSION.zip"

echo "Building $SLUG $VERSION"

if command -v php >/dev/null 2>&1; then
	while IFS= read -r -d '' f; do
		php -l "$f" >/dev/null || { echo "Syntax error in $f"; exit 1; }
	done < <(find "$SRC" -name '*.php' -print0)
	echo "PHP lint passed"
fi

# Translate .distignore entries into zip exclusion patterns.
EXCLUDES=()
if [[ -f "$SRC/.distignore" ]]; then
	while IFS= read -r line || [[ -n "$line" ]]; do
		line="${line%%#*}"; line="$(echo "$line" | xargs)"
		[[ -z "$line" ]] && continue
		if [[ "$line" == *"*"* ]]; then
			EXCLUDES+=("*$line")
		else
			EXCLUDES+=("$SLUG/$line" "$SLUG/$line/*" "*/$line" "*/$line/*")
		fi
	done < "$SRC/.distignore"
fi

mkdir -p "$DIST"
rm -f "$ZIP"
( cd "$ROOT" && zip -qr "$ZIP" "$SLUG" -x "${EXCLUDES[@]}" )
echo "Created $ZIP ($(du -h "$ZIP" | cut -f1), $(unzip -Z1 "$ZIP" | wc -l | xargs) files)"
