# Architektur des Themes

## Ordner

```
<slug>/
├── style.css            Theme-Kopf (Name, Template: flatsome, Version) – sonst leer
├── functions.php        Konstanten (<P>_VERSION/_DIR/_URI) + Modul-Liste, sonst nichts
├── front-page.php       Startseite: Liste der Abschnitte (Reihenfolge = Anzeige)
├── page-coa.php         Eigene Seitenvorlage (Beispiel für Sonderseiten)
├── inc/                 PHP-Module (je ein Thema, Kopfkommentar „was / wo einstellen“)
├── template-parts/      Ausgabe-Bausteine: home/, product/, shop/, global/, footer/, coa/
├── data/                Inhalte als PHP-Arrays: faq.php, coa-batches.php, welcome-codes.php …
├── assets/css/          CSS in Schichten (siehe unten)
├── assets/js/           theme.js (alle Kleinfunktionen, ohne jQuery), coa.js
├── assets/fonts/        Schriften lokal (DSGVO – keine Google-Server)
└── assets/mail/         Fertige JPG-Bilder für Newsletter (optional)
```

## Konventionen

- **Präfix** für alles Eigene: Funktionen `alp_*`, Konstanten `ALP_*`, CSS-Klassen `.alp-*`,
  CSS-Variablen `--alp-*`, Body-Klasse `.alp`, Meta-Schlüssel `_alp_*`. Neuer Shop: eigenes Kürzel
  (`scripts/new_theme.py --prefix`).
- `alp_config( 'a.b.c', $default )` liest verschachtelt aus `inc/config.php`.
- `alp_part( 'home/hero' )` lädt `template-parts/home/hero.php`.
- `alp_data( 'faq' )` lädt `data/faq.php`.
- `alp_link( '/pfad/' | 'shop' | 'cart' )` baut Links; `alp_icon( 'name', 18 )` liefert SVG-Icons.
- Texte **du**-Form, deutsch, keine Heilversprechen.
- Cache-Busting: Dateiversion = Änderungszeit (`alp_asset_version`), Theme-Version in style.css
  und functions.php bei jeder ZIP erhöhen.

## Module (functions.php, Reihenfolge)

| Modul | Aufgabe |
|---|---|
| helpers | config/part/data/link/icon/num, alte Seiten umleiten (retired_pages) |
| setup | CSS/JS laden (als **ein** Bündel in uploads/alp-cache, Rückfall Einzeldateien), Schriften vorladen, Body-Klassen, Übernahme alter Flatsome-Einstellungen, Vorschau-Schutz |
| flatsome | Header-Leiste, eigener Footer, Handy-Navigation unten, Flatsome-Lazy-Load aus |
| coa | Chargen/Laborzertifikate, Chargen-Prüfer, COA-Box/-Tab am Produkt (nur Labor-Shops) |
| description / content | Einheitliches Design für Produktbeschreibungen und Seiten (alte Inline-Styles neutralisieren) |
| woocommerce | Shop-Kacheln (HPLC-Etikett, Charge), Produktseite, Kaufleiste, Warenkorb/Kasse-Texte, Pflichtfelder |
| contact | WhatsApp-Buttons (Nummer im Customizer) |
| newsletter | Brevo-Formular (direkt an sibforms, ohne Plugin) + 15-%-Einblendung |
| welcome-coupon | Willkommenscode nur für erste Bestellung (Kasse, Store-API, Express-Zahlungen) + Admin-Knopf zum Anlegen |
| pricing | Werkzeug „Preise vereinheitlichen“ + Preisverlauf (30-Tage-Regel) |
| weekly-deal | Wochenangebot/Stacks mit automatischem Wechsel, Warenkorb-Button, Admin-Übersicht |
| affiliate-coupon | Wunsch-Gutscheincode bei YITH-Affiliate-Anmeldung, aktiv erst nach Genehmigung |
| notify | „Benachrichtigen lassen“ (E-Mails in WordPress, CSV-Export) |
| order-numbers | Fortlaufende Bestellnummern, kompatibel zu bestehenden Nummern/Snippets |
| schema | FAQ-Rich-Snippets (ergänzt Rank Math) |

## config.php – Blöcke (von oben nach unten ausfüllen)

`brand` → `features` (Schalter) → `free_shipping_threshold` → `media_fallback_host` → `design`
→ `whatsapp` → `newsletter_form` → `welcome_popup` → `weekly_deals` → `affiliate_coupon`
→ `welcome_coupon` → `order_numbers` → `retired_pages` → `checkout` → `announcement` → `hero`
→ `trust` → `sections.*` (Startseite) → `shop` → `product` → `footer` → `mobile_nav`
→ `category_icons` → `flatsome_mods` / `flatsome_mirror` / `flatsome_keep`.

**Achtung `flatsome_mirror`:** nur `true`, wenn `data/flatsome-design.php` ein vollständiger
Export ist – sonst werden Flatsome-Einstellungen bis auf die `flatsome_keep`-Liste ausgeblendet.

## CSS-Schichten (setup.php → alp_styles, Reihenfolge wichtig)

tokens (Farben/Schriften/Abstände) → base → header → footer → components → shop → content →
product → pages → checkout → home → coa → polish → buttons (nach allem, vereinheitlicht Buttons)
→ home-mobile → editorial (Design-Variante, überschreibt Optik) → motion → legacy (leer, für Altlasten).
Jede Datei lädt nur auf den Seiten, die sie brauchen (`alp_style_needed`). Handy-Regeln für die
Startseite gehören in home-mobile.css, nicht verstreut.
