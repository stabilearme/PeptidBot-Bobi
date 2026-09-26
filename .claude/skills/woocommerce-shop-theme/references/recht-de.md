# Rechtliches (Deutschland) – was das Theme berücksichtigt

Keine Rechtsberatung; bei Unsicherheit auf Händlerbund/IT-Recht-Kanzlei verweisen.

- **Germanized** liefert Preis-Hinweise (MwSt./Kleinunternehmer § 19 UStG, Versand, Lieferzeit).
  Theme gibt `alp_price_legal_note()` überall aus, wo ein Preis steht (Karte, Kaufleiste, Angebot).
  Nie „inkl. MwSt.“ fest ins Theme schreiben – Kleinunternehmer weisen keine MwSt. aus.
  Germanized-Pro-Hinweis „Anpassungen freischalten“ ist Werbung – nicht nötig.
- **Streichpreise (§ 11 PAngV):** Bei Preisermäßigung nur den **niedrigsten Preis der letzten
  30 Tage** durchstreichen. Theme: `alp_wd_ref_price()` + Preisverlauf; ist der Aktionspreis nicht
  niedriger, steht „Wochenpreis“ ohne Streichpreis. Keine Dauer-„Angebote“ mit Mondpreisen.
  Stacks dürfen mit der Summe der Einzelpreise verglichen werden.
- **Newsletter:** Double-Opt-in (Brevo), Abmeldelink, Impressum im Mail-Fuß. Kontakte nie ohne
  Einwilligung synchronisieren („Sync my users“ nicht klicken).
- **„Benachrichtigen lassen“:** zweckgebunden (nur diese eine Info), kein Newsletter daraus machen.
- **Willkommensrabatt/Codes:** Bedingungen nennen (erste Bestellung, nicht auf reduzierte Ware,
  Ablaufdatum).
- **Checkout:** Button „Zahlungspflichtig bestellen“, AGB/Widerruf-Checkbox (Germanized),
  Seite „Versand & Zahlung“ muss zu den echten Zahlungsarten/Ländern passen.
- **Aussagen:** Keine erfundenen Qualitätsversprechen („100 % aller Chargen getestet“), keine
  Heil-/Wirkversprechen. Forschungs-Produkte: Hinweis „nur für Forschungszwecke“.
- **Nahrungsergänzung** (falls anderer Shop): BVL-Anzeige § 5 NemV, LMIV-Etikett, Health-Claims-VO,
  Novel-Food-Status prüfen, keine verbotenen Stimulanzien.
