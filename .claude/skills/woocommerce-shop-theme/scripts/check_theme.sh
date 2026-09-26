#!/usr/bin/env bash
# Schnellprüfung eines Theme-Ordners: PHP-Syntax, CSS-Klammern/Kommentare, JS-Syntax, übrig gebliebene Fremd-Marken.
# Aufruf: bash check_theme.sh <theme-ordner> [marke-die-nicht-vorkommen-darf …]
set -u
T="${1:?Theme-Ordner angeben}"; shift || true
fail=0
while IFS= read -r f; do out=$(php -l "$f" 2>&1) || { echo "PHP-FEHLER: $out"; fail=1; }; done < <(find "$T" -name '*.php')
for f in "$T"/assets/css/*.css; do
  python3 - "$f" <<'PY' || fail=1
import re,sys
s=open(sys.argv[1]).read()
if s.count('/*')!=s.count('*/'): print('CSS-Kommentar offen:',sys.argv[1]); sys.exit(1)
t=re.sub(r'/\*.*?\*/','',s,flags=re.S); t=re.sub(r'"(?:\\.|[^"\\])*"|\'(?:\\.|[^\'\\])*\'','""',t)
if t.count('{')!=t.count('}'): print('CSS-Klammern ungleich:',sys.argv[1]); sys.exit(1)
PY
done
for f in "$T"/assets/js/*.js; do node -e "new Function(require('fs').readFileSync('$f','utf8'))" 2>/dev/null || { echo "JS-FEHLER: $f"; fail=1; }; done
for brand in "$@"; do hits=$(grep -rIl --exclude-dir=fonts -i "$brand" "$T" | head -20); [ -n "$hits" ] && { echo "Noch '$brand' in:"; echo "$hits"; }; done
[ $fail = 0 ] && echo "OK: PHP, CSS und JS ohne Fehler." || { echo "Es gibt Fehler (siehe oben)."; exit 1; }
