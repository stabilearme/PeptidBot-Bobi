# Lektionen aus dem aminolabspro-Projekt

| Problem | Ursache | Lösung / Regel |
|---|---|---|
| Live-Header kaputt während Theme-Vorschau | Flatsome/WP schrieb gefilterte Mods ins aktive Theme | Schreibschutz `alp_protect_active_theme_mods` behalten |
| Produktbilder unsichtbar | Flatsome-Lazy-Load, `has-equal-box-heights` gab 0 px Breite | Lazy-Load aus (`features.flatsome_lazy_load`), Bild-CSS absichern |
| Mobile Startseite springt (CLS 0,48) | Flex-Spalte mit `align-items:end` → Bild 0 px bis Laden | `align-items: stretch`, Hero-Bild ohne Einblend-Animation (LCP) |
| Text springt nach Schriftwechsel | falsche Schriften vorgeladen | nur Schriften des aktiven Designs vorladen |
| Seite langsam am Handy | ~20 CSS-Dateien, Brevo-Plugin (WebPush 100 KB) | CSS-Bündel, Brevo-WordPress-Plugin deaktivieren (Brevo-WooCommerce-Plugin bleibt) |
| Formular-Fehler auf gecachter Startseite | Nonce veraltet im Cache | öffentliche Formulare ohne Nonce, dafür Falle + Zeit + IP-Limit |
| „Inkl. MwSt.“ falsch | Kleinunternehmer | Germanized-Texte verwenden |
| Streichpreis-Abmahnrisiko | Dauer-Rabatte, 30-Tage-Regel | Preise vereinheitlichen, Referenz = 30-Tage-Tiefstpreis |
| Doppelter Rabatt im Stack | `before_calculate_totals` läuft mehrfach | Ursprungspreis im Produkt-Meta merken, nicht vom geänderten Preis rechnen |
| Gutschein auf Stack | Stack-Artikel nicht „reduziert“ | Bundle-Flag → `is_on_sale` true, auch bei `cart_loaded_from_session` |
| Mail-Bilder fehlen | WebP; Brevo-Branding-Domain mit abgelaufenem SSL | JPG; Branding prüfen |
| Brevo-Gutscheine „Forbidden“ | Feature nur Pro-Tarif | gemeinsamer Code |
| Versand von Kampagnen blockiert | Sicherheitsregel: keine Nachrichten an echte Kunden auslösen | Entwürfe + Testmail, Nutzer plant |
| Geplante Mails mit falschem Datum | Nutzer-Fehler beim Planen | nach dem Planen Termine per API gegenprüfen |
| MCP-Verbindung weg | Token abgelaufen | Admin-Werkzeuge im Theme statt Fernsteuerung (Vorschau/Rückgängig) |
| Apple Pay „fehlt“ | Test auf Android | nur Safari/iOS; Admin sieht Vorschau |
| Registrierung ohne Passwort verwirrt | WooCommerce generiert Passwort + Germanized-DOI | Passwortfeld im Formular aktivieren |
| Leere `flatsome-design.php` + Mirror | Mirror blendet alles außer keep-Liste aus | `flatsome_mirror` nur mit vollständigem Export |
| Falsche Behauptungen im Text | „100 % Chargen getestet“ | nur belegbare Aussagen |

Allgemein: Bei Unklarheit erst messen/prüfen (curl, Store-API, Screenshot), dann ändern.
Jede Änderung abschaltbar, jede ZIP versioniert, jede Datenänderung mit Rückweg.
