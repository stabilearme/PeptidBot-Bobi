#!/usr/bin/env bash
# Version erhöhen (style.css + functions.php) und ZIP bauen.
# Aufruf: bash build_zip.sh <theme-ordner> <neue-version> [ausgabe-ordner]
set -eu
T="${1:?Theme-Ordner}"; V="${2:?Version, z. B. 1.0.3}"; OUT="${3:-.}"
sed -i -E "s/^Version:[[:space:]]+[0-9.]+/Version:      $V/" "$T/style.css"
sed -i -E "s/(define\( '[A-Z]+_VERSION', ')[0-9.]+(' \);)/\1$V\2/" "$T/functions.php"
bash "$(dirname "$0")/check_theme.sh" "$T"
name=$(basename "$T"); dir=$(cd "$(dirname "$T")" && pwd)
mkdir -p "$OUT"; OUT=$(cd "$OUT" && pwd)
(cd "$dir" && rm -f "$OUT/$name-$V.zip" && zip -qr "$OUT/$name-$V.zip" "$name" -x '*.DS_Store')
echo "ZIP: $OUT/$name-$V.zip"
