# Vorschau: Child-Theme „AminoLabs Pro 2“

Klickbare, statische Vorschau des Child-Themes aus `theme/aminolabspro-pro/`, mit den echten Produktbildern und COA-Zertifikaten von aminolabspro.com.

**Öffnen:** `preview/site/index.html` im Browser (Doppelklick genügt, kein Server nötig).
Die gelbe Leiste oben wechselt zwischen **Startseite**, **Shop**, **Produktseite** und **COA-Seite**. Alle Produktkacheln führen zu eigenen Produktseiten (26 Produkte).

## Was echt ist, was nachgebaut ist

| Teil | Herkunft |
|---|---|
| Startseiten-Abschnitte, COA-Seite, Chargen-Box, Chargen-Prüfer, Footer, Mobile-Navigation, Kaufleiste, Laufleiste | **echte Template-Teile und CSS/JS des Themes**, per PHP gerendert |
| Produktbeschreibungen | echte Texte aus WooCommerce, durch den Theme-Filter `alp_clean_product_description()` bereinigt |
| Produktbilder, COA-Bilder, Logo | aus der Mediathek von aminolabspro.com (`site/img/`) |
| Preise, Kategorien, Artikelnummern, Bestseller-Reihenfolge | WooCommerce-Daten (`data/products.json`, Stand 23.09.2026) |
| Header-Aufbau, Produktkachel-Gerüst, Galerie, Tabs | **nachgebaut** (`lib/flatsome-shim.css`). Im Live-Shop kommt das von Flatsome. Die Menüpunkte im Header sind Platzhalter. |
| Warenkorb, Suche, Seiten wie Wissen/Kontakt | nicht aktiv. Warenkorb zeigt einen Hinweis, externe Links führen in den Live-Shop. |

## Neu bauen

Nach Änderungen am Theme (z. B. `inc/config.php`, `data/coa-batches.php`, CSS):

```bash
php preview/build.php
```

- `lib/wp-stubs.php`: minimaler WordPress/WooCommerce-Ersatz, damit die Theme-Templates ohne WordPress laufen
- `lib/preview.js` / `lib/preview.css`: Vorschau-Leiste, Lightbox, Kategorie-Filter im Shop, Warenkorb-Hinweis
