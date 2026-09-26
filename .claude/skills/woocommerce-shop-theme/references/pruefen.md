# Prüfen vor jeder Auslieferung

1. `bash scripts/check_theme.sh <ordner> <alte-marke>` – PHP-Syntax, CSS-Klammern/Kommentare,
   JS-Syntax, Reste fremder Marken.
2. **PHP-Attrappe** für Logik (Preise, Wochenwechsel, Gutscheinregeln): Modul mit `require` laden,
   WordPress-Funktionen minimal nachbauen (add_action/add_filter leer, get_option/update_option
   über Arrays, WC_Coupon/WC_Product als kleine Klassen). Randfälle testen: Sommer/Winterzeit,
   Sonntag vor Wechsel, doppelte Berechnung (kein doppelter Rabatt), ungültige Eingaben.
3. **Live-Vergleich per Playwright** (Chromium: `/opt/pw-browsers/chromium`, Proxy aus
   `$HTTPS_PROXY`): Seite laden, per `page.route` geänderte CSS/JS einspielen, Screenshots
   Handy (Pixel 5) + PC vorher/nachher, Pixel-Differenz mit PIL. Altersabfrage/Popups per CSS
   ausblenden, Animationen aus, Countdown entfernen – sonst Fehlalarme.
   Für CSS-Bündel: Anzahl CSS-Regeln vorher = nachher (`document.styleSheets[..].cssRules.length`).
4. **Layout-Springen (CLS)** messen: PerformanceObserver `layout-shift` mit Quellen.
5. **Geschwindigkeit:** `lighthouse <url> --quiet --chrome-flags="--headless=new --no-sandbox
   --proxy-server=$HTTPS_PROXY" --only-categories=performance --output=json` (PageSpeed-API hat
   Tageslimit). Zwei Läufe, Werte melden: Wertung, FCP, LCP, TBT, CLS.
6. Nach Upload: `curl` der Startseite → `style.css?ver=` zeigt neue Version; Store-API
   `/wp-json/wc/store/v1/products` für Preise/Rabatte; Bilder/Dateien per HTTP-Status prüfen.
