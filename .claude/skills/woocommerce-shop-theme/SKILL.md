---
name: woocommerce-shop-theme
description: Baut und pflegt WooCommerce-Shops als Flatsome-Child-Theme nach dem bewährten Aufbau des aminolabspro-Themes – modulare /inc/-Dateien, zentrale config.php, /data/-Dateien, Design-Tokens, Startseiten-Abschnitte, Wochenangebote/Stacks, Newsletter (Brevo), Willkommenscode, Affiliate-Codes, Preislogik nach deutscher 30-Tage-Regel, ZIP-Auslieferung und sicherer Umzug. Verwende diesen Skill immer, wenn der Nutzer einen neuen Online-Shop, ein neues WordPress/WooCommerce-Theme, einen Shop-Relaunch, eine neue Marke oder neue Produkte „genauso aufgebaut wie aminolabspro“ umsetzen will – auch wenn er nur „neuer Shop“, „Theme bauen“, „Child Theme“, „Shop-Design“ oder „wie bei unserer Seite“ sagt, und ebenso für Erweiterungen eines so gebauten Themes (neues Modul, neuer Startseiten-Abschnitt, Wochenangebote, Brevo-Mails).
---

# WooCommerce-Shop-Theme (Flatsome-Child, aminolabspro-Bauweise)

Dieser Skill hält fest, wie das Theme für aminolabspro.com gebaut wurde, damit ein neuer Shop
mit anderen Produkten und anderem Design genauso sauber entsteht. Er enthält ein lauffähiges
**Grundgerüst** (`assets/theme-skeleton/`, ohne Marken-Daten) und Werkzeuge in `scripts/`.

Der Nutzer ist Shop-Betreiber, kein Entwickler: Antworten auf Deutsch, kurz, in Schritten mit
Klickpfaden („WooCommerce → Einstellungen → …“). Fachbegriffe kurz erklären.

## Grundprinzipien (warum das Theme so gebaut ist)

1. **Alles Pflegbare an einer Stelle.** Texte, Schalter, Farben, Angebote stehen in `inc/config.php`
   bzw. `data/*.php` – nicht verstreut in Templates. So kann man ändern, ohne Code zu verstehen.
2. **Kleine Module statt einer riesigen functions.php.** `functions.php` lädt nur die Liste aus
   `/inc/`. Jedes Modul hat einen Zweck und einen Kopfkommentar „was, wo einstellen“.
3. **Jede Funktion hat einen Schalter** (`features.*` oder `enabled` im eigenen Config-Block).
   Neue Funktionen immer abschaltbar bauen – das ist der Notausgang, falls etwas live stört.
4. **WooCommerce-/Plugin-Daten nicht anfassen, sondern filtern.** Preise, Rabatte, Gutscheinregeln
   entstehen über Hooks zur Laufzeit (rückgängig durch Abschalten). Dauerhafte Änderungen an
   Daten nur über Admin-Werkzeuge mit Vorschau + „Rückgängig“.
5. **Nichts Live-Schädliches ohne Test.** Vor jeder ZIP: `scripts/check_theme.sh`; bei Layout-
   oder CSS-Änderungen Vorher/Nachher-Screenshot der Live-Seite (siehe references/pruefen.md).
6. **Rechtssicher by default** (Deutschland): Germanized-Hinweise, Streichpreise nur nach 30-Tage-
   Regel, Double-Opt-in, keine Heilversprechen. Details: references/recht-de.md.

## Ablauf: neuen Shop bauen

1. **Klären** (kurz nachfragen, was nicht aus dem Gespräch hervorgeht):
   Marke/Domain, Produkte & Kategorien, Eltern-Theme (Standard Flatsome), Kleinunternehmer ja/nein,
   Zahlungsarten, Newsletter-Tool (Standard Brevo), Affiliate-Plugin (YITH?), Farben/Logo,
   ob es COA/Laborzertifikate gibt (sonst COA-Module abschalten).
2. **Grundgerüst anlegen:**
   `python3 scripts/new_theme.py --target <repo>/theme/<slug> --name "<Name>" --slug <slug> --prefix <kürzel> --url <https://…>`
   Das Kürzel (2–5 Buchstaben) ersetzt `alp_`/`alp-` überall – eindeutig wählen.
3. **Marke einsetzen:** `inc/config.php` Block für Block durchgehen (Reihenfolge in
   references/architektur.md). Farben/Schriften in `assets/css/tokens.css` (bzw. Design-Datei
   `editorial.css`). Danach `scripts/check_theme.sh <ordner> aminolabs` – findet Reste der alten Marke.
4. **Module wählen:** In `functions.php` nur laden, was der Shop braucht; Schalter in `features`.
   Shop-fremde Module (z. B. `coa`) entfernen statt tot mitzuschleppen.
5. **Startseite:** Abschnitte in `front-page.php` (Reihenfolge = Liste), Texte in `sections.*`.
6. **Daten:** `data/faq.php`, ggf. `data/coa-batches.php`, Wochenangebote in `weekly_deals.deals`
   (echte Artikelnummern/SKUs aus dem Shop!).
7. **Prüfen, ZIP bauen:** `bash scripts/build_zip.sh <ordner> <version> <ausgabe>` (erhöht Version in
   style.css + functions.php, prüft, packt). Jede ausgelieferte ZIP bekommt eine neue Versionsnummer.
8. **Ausliefern mit Anleitung:** „Design → Themes → Hinzufügen → Theme hochladen → ZIP → Aktuelle
   Version ersetzen → Cache leeren“. Bei Erst-Installation Umzug nach references/umzug.md.
9. **Committen & pushen** (falls Git-Repo), dann live nachprüfen (Seite abrufen, Version im
   `style.css?ver=` kontrollieren, Screenshot Handy + PC).

## Ablauf: bestehendes Theme erweitern

- Neues Feature → neues Modul `inc/<name>.php` mit Kopfkommentar, in `functions.php` eintragen,
  Config-Block mit `enabled`, Ausgabe über ein Template in `template-parts/`.
- Neuer Startseiten-Abschnitt → `template-parts/home/<name>.php` + Eintrag in `front-page.php`
  + Texte in `sections.<name>` + CSS in `home.css` (Handy-Anpassung in `home-mobile.css`).
- Styles nie inline in Templates; Farben nur über Tokens (`var(--alp-…)`).
- Vorhandene Muster wiederverwenden (siehe references/module.md) statt neu zu erfinden:
  Zeitsteuerung (weekly-deal), Admin-Werkzeug mit Vorschau/Rückgängig (pricing),
  Formular ohne Nonce für gecachte Seiten (notify), Admin-Liste + CSV-Export (notify).

## Was der Nutzer selbst klicken muss (nicht selbst auslösen)

- Mails/Kampagnen an echte Kunden **versenden oder planen** – nur Entwürfe anlegen, Testmail an
  den Nutzer ist ok. Planen macht der Nutzer.
- Preise im Live-Shop dauerhaft ändern – über das eingebaute Werkzeug mit Vorschau.
- Theme aktivieren/hochladen, Plugins (de)aktivieren, Zugangsdaten anlegen.
Zugangsschlüssel (API-Keys), die im Chat landen: nach Gebrauch löschen lassen und das sagen.

## Referenzen (bei Bedarf lesen)

- `references/architektur.md` – Ordner, Module, Config-Blöcke, CSS-Schichten, Konventionen.
- `references/module.md` – was jedes Modul kann und wie man es für einen neuen Shop einstellt.
- `references/recht-de.md` – Germanized, 30-Tage-Streichpreis, Kleinunternehmer, DOI, Affiliate.
- `references/marketing.md` – Brevo (Formular, Automatisierung, Kampagnen-Entwürfe per API,
  Mail-Bilder als JPG), Wochenangebote/Stacks, Willkommenscode.
- `references/umzug.md` – Theme sicher testen (Theme Switcha), Umzug, Rückweg, Checkliste.
- `references/pruefen.md` – Tests: PHP-Attrappe, Playwright-Screenshots, Lighthouse, Vorher/Nachher.
- `references/lektionen.md` – Fehler, die schon passiert sind, und wie man sie vermeidet. **Vor
  größeren Änderungen lesen.**
