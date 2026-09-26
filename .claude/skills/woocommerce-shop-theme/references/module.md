# Module im Detail – Einstellen für einen neuen Shop

## Wochenangebot (weekly-deal.php, config `weekly_deals`)
- Liste `deals`: je Angebot `title`, `text`, `items` (SKUs; Menge `qty`, fester Preis `price`),
  `percent` (Rabatt auf den aktuellen Verkaufspreis). 1 Produkt = Einzelangebot (überall reduziert),
  mehrere = **Stack** (Rabatt im Warenkorb je vollständigem Satz, Button legt alles hinein).
- `first_week` = Datum in der Woche, in der Eintrag 1 läuft; Wechsel `switch_day` + `switch_time`
  (Europe/Berlin, Sommer-/Winterzeit wird korrekt behandelt); Liste rotiert endlos.
- Startseite zeigt Stacks als „Vial-Reihe“ aus den Produktfotos + „Du sparst … €“ – keine
  generierten Bilder nötig. Countdown „Neues Angebot in …“, beim Ablauf Neuladen ohne Cache.
- Beim Wechsel: Seiten-Cache (WP-Optimize) leeren, Aktionspreise der Vorwoche im Preisverlauf merken.
- Admin: WooCommerce → Wochenangebote (ganzer Durchlauf mit Preisen, Warnung bei fehlender SKU).
- Einzelprodukt höchstens alle 5 Wochen, sonst entfällt der Streichpreis (30-Tage-Regel).

## Preise vereinheitlichen (pricing.php)
- WooCommerce → Preise vereinheitlichen: Dauer-Angebotspreise auflösen, neuer Normalpreis in der
  Mitte mit gleicher Cent-Endung (,95/,90). Vorschau → Übernehmen → Rückgängig.
- Merkt alte Angebotspreise 30 Tage als Referenz (Meta `_alp_price_log`).

## Newsletter + Einblendung (newsletter.php, config `newsletter_form`, `welcome_popup`)
- Brevo-Formular-URL (`…sibforms.com/serve/…`) in `newsletter_form.action`. Kein Brevo-Plugin nötig.
- Einblendung: nach X Sekunden / Exit-Intent, 1× pro Besucher, nie in Warenkorb/Kasse/Konto,
  nicht über Kaufleiste/Newsletter-Abschnitt. Schalter `features.welcome_popup`.

## Willkommenscode (welcome-coupon.php, config `welcome_coupon`)
- Präfix-Regel „nur erste Bestellung“ (geprüft in Warenkorb, Kasse, Store-API/Apple Pay).
- `shared_code` = ein gemeinsamer Code für Brevo Free (Einzelcodes brauchen Brevo Pro).
- Admin-Knopf `?alp_welcome_codes=1` legt Codes an (auch Einzelcodes aus data/welcome-codes.php).

## Affiliate-Wunschcode (affiliate-coupon.php, nur mit YITH Affiliates)
- Feld im YITH-Formular, Prüfung „vergeben/reserviert“, Gutschein erst bei Genehmigung
  (`yith_wcaf_affiliate_enabled`), Zuordnung über Coupon-Meta `coupon_referrer`; deaktiviert → Entwurf.

## Benachrichtigen (notify.php, `sections.news.teaser.notify`)
- E-Mail-Feld in der „Demnächst“-Karte, Speicherung in WordPress (Option), Admin-Liste + CSV.
- Kein Nonce (Startseite ist gecacht), stattdessen Falle, Zeitprüfung, Limit je IP.

## Shop-Kacheln & Produktseite (woocommerce.php)
- HPLC-Etikett auf dem Bild + Charge/Gehalt/COA-Link (nur mit coa-Daten).
- Kaufleiste beim Scrollen, Germanized-Hinweise an Preis/Karte/Kaufleiste, Kasse-Texte aus config.

## Bestellnummern (order-numbers.php)
- `order_numbers => 'auto'`: passiv, wenn schon ein anderes Plugin/Snippet Nummern vergibt.
  Neuer Shop: Startwert `order_numbers_start`.
