# Theme testen, umziehen, zurück

## Vorher
1. Sicherung: All-in-One WP Migration → Exportieren → Datei (Import-Größenlimit prüfen!) +
   Hoster-Backup. Zusätzliches CSS des alten Themes sichern.
2. `functions.php` des alten Child-Themes und Snippets (Code Snippets) sichten → Nötiges in Module.
3. WooCommerce → Status → Vorlagen: Template-Überschreibungen des alten Themes prüfen.

## Testen ohne Kunden-Risiko
- ZIP hochladen, **nicht aktivieren**. Plugin „Theme Switcha“: nur Admins sehen das neue Theme.
- Das Theme schützt die Einstellungen des aktiven Themes während der Vorschau
  (`alp_protect_active_theme_mods`) – ohne diesen Schutz schrieb Flatsome Werte ins alte Theme
  und zerstörte den Live-Header. Nie entfernen.
- Durchklicken: Startseite, Produkt, Warenkorb, Kasse (nicht bestellen), Konto, Rechtstexte, Handy.

## Umschalten
- Werktag früh morgens. Theme aktivieren → Cache leeren → Inkognito + Handy prüfen →
  **Testbestellung Vorkasse** (Nummer fortlaufend? Mails + Rechnung?) → stornieren.
- Menüs zuweisen, alte Snippets (Laufband o. Ä.) abschalten.

## Rückweg
- Altes Theme aktivieren (Sekunden, keine Datenverluste). Backup-Import nur im Notfall – setzt auch
  Bestellungen seit der Sicherung zurück.

## Nach dem Umzug
- Preise vereinheitlichen, Seite „Versand & Zahlung“ prüfen, Apple Pay: Domain im Zahlungs-Plugin
  registrieren (Apple Pay erscheint nur in Safari/iOS, Admin-Ansicht zeigt Vorschau aller Buttons).
- Zugangsschlüssel (WooCommerce REST, Brevo) löschen, die im Chat standen.
