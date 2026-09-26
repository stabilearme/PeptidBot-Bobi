# AminoLabs Pro 2 – Child Theme für Flatsome

Premium-Design für aminolabspro.com: neue Startseite, COA-Center mit Chargen-Prüfer, Produktseiten mit Chargen-Box und Sticky-Kaufleiste, App-artige Mobile-Navigation, eigener Footer, lokal gehostete Schriften (DSGVO), keine Page-Builder-Abhängigkeit.

---

## 1. Installation (ca. 5 Minuten)

> **Vorher unbedingt:** Backup der Website machen (z. B. über deinen Hoster oder UpdraftPlus).

1. **WordPress → Design → Themes → Theme hinzufügen → Theme hochladen**
   ZIP-Datei `aminolabspro-pro.zip` auswählen → *Jetzt installieren*.
2. **Noch NICHT aktivieren.** Zuerst auf **„Live-Vorschau“** klicken und durch die Seiten klicken
   (Startseite, Shop, ein Produkt, Warenkorb, COA-Seite).
3. Wenn alles passt: **Aktivieren**.
   Beim Aktivieren werden automatisch übernommen:
   Logo, Menüs, Header-Einstellungen und alle Flatsome-Optionen deines bisherigen Child Themes.
4. **Cache leeren** (WP-Optimize → Cache leeren) und die Seite am Handy prüfen.

**Zurück zum alten Design:** Design → Themes → altes „AminoLabs Pro Child“ aktivieren. Es wird nichts gelöscht.

### Was beim Aktivieren NICHT übernommen wird
- Das alte **„Zusätzliche CSS“** aus dem Customizer. Das ist Absicht – das neue Design bringt alles mit.
  Falls eine einzelne Seite (z. B. Rekonstitutionsrechner, Glücksrad, Kundenwünsche) danach komisch aussieht:
  die nötigen Regeln aus dem alten Zusatz-CSS in **`assets/css/legacy.css`** kopieren.
- Code, der im **alten Child Theme** (`functions.php`) stand. Prüfe vor dem Wechsel, ob dort eigene Funktionen liegen
  (z. B. Glücksrad-Logik). Diese dann in `inc/` als eigene Datei übernehmen oder ins Plugin „Code Snippets“ verschieben.

---

## 2. Wo ändere ich was?

| Ich möchte …                                   | Datei                                   |
|------------------------------------------------|-----------------------------------------|
| Texte der Startseite, Laufleiste, Footer ändern | `inc/config.php`                         |
| Funktionen ein-/ausschalten                     | `inc/config.php` → `features`            |
| Neue Charge / neues COA eintragen               | `data/coa-batches.php`                   |
| FAQ bearbeiten                                  | `data/faq.php`                           |
| Farben, Schriften, Abstände, Radien             | `assets/css/tokens.css`                  |
| Reihenfolge der Startseiten-Abschnitte          | `front-page.php`                         |
| Aussehen der Startseite                         | `assets/css/home.css`                    |
| Aussehen Shop / Produktkacheln                  | `assets/css/shop.css`                    |
| Aussehen Produktseite                           | `assets/css/product.css`                 |
| Formatierung Beschreibungen, Seiten, Beiträge   | `assets/css/content.css`                 |
| Aussehen Inhaltsseiten (Wissen, Kontakt, Versand, Rechtliches …) | `assets/css/pages.css`  |
| Shop-Einleitung & Vorteils-Chips                | `inc/config.php` → `shop`                |
| „Neu & demnächst“ (Teaser, Neu im Shop, Angebote) | `inc/config.php` → `sections.news`     |
| Aussehen Warenkorb / Kasse / Konto              | `assets/css/checkout.css`                |
| Aussehen COA-Seite & Chargen-Box                | `assets/css/coa.css`                     |
| Header / Navigation                             | `assets/css/header.css` (Aufbau: Customizer → Header) |
| Footer / Mobile-Navigation                      | `template-parts/footer/footer.php`, `assets/css/footer.css` |
| Einmalige Übergangs-Styles                      | `assets/css/legacy.css`                  |

**Faustregel:** Inhalte → `inc/config.php` oder `data/`. Aussehen → `assets/css/`. Aufbau → `template-parts/`.
Das Customizer-Feld „Zusätzliches CSS“ möglichst leer lassen.

---

## 3. Neue Charge eintragen (Beispiel)

In `data/coa-batches.php` einen Block kopieren und **ganz oben** einfügen (die oberste Charge eines Produkts wird auf der Produktseite angezeigt):

```php
array( 'batch' => 'BPC-1026-01', 'sku' => 'ALP-BPC157-10', 'product' => 'BPC-157 10 mg', 'status' => 'done',
       'content' => 10.21, 'purity' => 99, 'report' => 'ABC12345', 'cert' => '100019999',
       'lab' => $lab, 'tested' => '01.–07.10.2026',
       'photo' => $uploads . 'coa-result-ABC12345.webp', 'file' => $uploads . 'COA-ABC12345-scaled.webp' ),
```

- `sku` = Artikelnummer aus WooCommerce → verbindet die Charge automatisch mit der Produktseite.
- Test läuft noch? `'status' => 'pending', 'expected' => 'Mitte Oktober'`.
- Bilder liegen in einem anderen Monatsordner? Den Pfad in `'photo'` / `'file'` direkt angeben, z. B. `'/wp-content/uploads/2026/10/COA-XYZ.webp'`.

Die Chargennummer ist danach sofort im **Chargen-Prüfer** (Startseite + COA-Seite) auffindbar.

---

## 4. Was das Theme macht

**Startseite** (`front-page.php`) – Hero mit Live-Zertifikat, Vertrauensleiste, Kategorien (automatisch aus WooCommerce), Bestseller, „Neu & demnächst“ (Teaser für kommende Neuheiten + umschaltbar „Neu im Shop“ / „Angebote“), Chargen-Prüfer, Ablauf, Wissen/Tools, FAQ (mit Google-Rich-Snippet), Newsletter-Aufruf (führt zu `/early-access/`).
Der Editor-Inhalt der Seite „Home“ wird nicht mehr angezeigt; SEO-Titel/Beschreibung aus Rank Math bleiben aktiv.

**COA-Seite** (`page-coa.php`, greift automatisch für die Seite mit dem Slug `coa`) – Chargen-Prüfer, filterbare Zertifikatsübersicht aus `data/coa-batches.php`, Erklärung zum Lesen eines COAs.

**Produktseite** – Merkmal-Chips, „Versand heute“-Countdown (Mo–Fr, Bestellschluss in `config.php`), Chargen-Box mit Reinheit/Gehalt/Labor/Zertifikat, Tab „Laborbericht“ mit dem Zertifikat als Bild (Schalter `product_coa_tab`), Vertrauensliste, Research-Use-Only-Hinweis, Sticky-Kaufleiste beim Scrollen.
Die vorhandenen Produktbeschreibungen werden **beim Anzeigen** einheitlich gestaltet (Inline-Styles entfernt). In der Datenbank wird nichts verändert; Schalter `clean_descriptions` in `config.php`.

**Shop** – Einleitung mit Vorteils-Chips, Kategorie-Leiste, neu gestaltete Produktkacheln mit COA-Hinweis, 2 Spalten auf dem Handy.

**Inhaltsseiten & Beiträge** – Wissen, Kontakt, Versand & Zahlung, Rechner, Rechtstexte, Leitfäden und Wissensartikel erscheinen im Theme-Design. Alte `<style>`-Blöcke und Inline-Styles aus dem früheren Design werden **beim Anzeigen** entfernt und in Theme-Klassen übersetzt (`inc/content.php`). In der Datenbank wird nichts verändert; Schalter `clean_pages` in `config.php`.

**Warenkorb & Kasse** – Fortschrittsanzeige, aufgeräumte Formulare, Zahlarten als Karten, Vertrauenshinweise. Optional Balken „Noch X € bis versandkostenfrei“ (`free_shipping_threshold`).

**Global** – Laufleiste, eigener Footer, Mobile-Navigation unten (Start, Shop, Suche, COAs, Warenkorb mit Live-Zähler), Such-Overlay.

**Technik**
- Keine WooCommerce-Templates überschrieben → Updates von WooCommerce, Flatsome und Germanized bleiben unproblematisch.
- CSS wird nur dort geladen, wo es gebraucht wird (z. B. `checkout.css` nur in Warenkorb/Kasse).
- Schriften lokal (Bricolage Grotesque, Hanken Grotesk, JetBrains Mono – OFL-Lizenz). Flatsomes Google-Fonts-Einbindung wird abgeschaltet.
- Kein jQuery für eigene Skripte, alle Skripte mit `defer`.

---

## 5. Empfohlene Einstellungen nach dem Aktivieren

- **Customizer → Header:** Logo prüfen (für die schmale Kopfzeile ist ein Querformat-Logo ideal, Höhe max. 44 px).
- **Customizer → Style → Colors:** Primärfarbe `#13795B` wird beim Aktivieren automatisch gesetzt.
- **Customizer → WooCommerce → Product Page:** Layout „No sidebar“ ist bereits gesetzt; „Tabs/Accordion“ nach Geschmack.
- **Rank Math:** Breadcrumbs aktiv lassen; FAQ-Schema der Startseite kommt vom Theme.

Einstellungen aus `config.php → flatsome_mods` werden nur **einmal** beim ersten Aktivieren gesetzt.
Erneut anwenden: in der Datenbank die Option `alp_mods_migrated` löschen und das Theme neu aktivieren.

---

## 6. Ordnerstruktur

```
aminolabspro-pro/
├── style.css                 Theme-Kopf (kein CSS)
├── functions.php             lädt nur die Module aus /inc
├── front-page.php            Startseite
├── page-coa.php              COA-Seite
├── inc/
│   ├── config.php            ★ Texte, Links, Schalter
│   ├── helpers.php           Hilfsfunktionen + Icons
│   ├── setup.php             Assets, Schriften, Aktivierung
│   ├── flatsome.php          Laufleiste, Footer, Mobile-Navigation
│   ├── coa.php               Chargen-Logik, Shortcodes
│   ├── description.php       Bereinigung der Produktbeschreibungen (Normalizer)
│   ├── content.php           Bereinigung von Seiten & Beiträgen
│   ├── woocommerce.php       Shop, Produkt, Warenkorb, Kasse (+ Preis-Pflichtangaben, Kanton-Pflicht CH)
│   ├── contact.php           WhatsApp-Buttons, Sprachumschalter
│   ├── newsletter.php        Brevo-Newsletterformular (Startseite, [alp_newsletter])
│   ├── welcome-coupon.php    15-%-Willkommenscode nur für die erste Bestellung
│   ├── order-numbers.php     Fortlaufende Bestellnummern (auch Apple/Google Pay)
│   └── schema.php            FAQ-Rich-Snippet
├── data/
│   ├── coa-batches.php       ★ Chargen & Zertifikate
│   ├── flatsome-design.php   Flatsome-Einstellungen von .de (gelten auch auf .com)
│   └── faq.php               ★ FAQ
├── template-parts/           HTML-Bausteine (home, coa, product, shop, footer, global)
└── assets/
    ├── css/                  tokens, base, header, footer, components, shop, content, product, pages, checkout, home, coa, legacy
    ├── js/                   theme.js, coa.js
    └── fonts/                lokale Schriften
```

**Shortcodes** für beliebige Seiten: `[alp_coa_lookup]` (Chargen-Prüfer), `[alp_coa_grid]` (Zertifikatsübersicht), `[alp_newsletter]` (Newsletter-Anmeldung über Brevo).
