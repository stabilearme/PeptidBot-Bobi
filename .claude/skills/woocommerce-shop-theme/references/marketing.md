# Marketing-Bausteine

## Brevo
- **Formular:** Brevo → Formulare → Teilen → HTML → `<form action="…sibforms…">` in config.
  Liste merken (z. B. „early acces“), Double-Opt-in an.
- **Willkommensmail:** Automatisierung „Kontakt zur Liste hinzugefügt“ → E-Mail (Vorlage) →
  Wiedereintritt aus. Automatisierungen lassen sich **nicht per API** anlegen – Nutzer klickt.
- **Gutscheinsammlungen** (Einzelcodes pro Kontakt) nur im Pro/Enterprise-Tarif. Free: gemeinsamer
  Code + Theme-Regel „nur erste Bestellung“.
- **API-Zugang:** Schlüssel beginnt mit `xkeysib-` (nicht `xsmtpsib-`). IP-Sperre (Sicherheit →
  Autorisierte IPs) blockiert Cloud-Anfragen mit wechselnden IPs → kurz deaktivieren, danach
  wieder an, Schlüssel löschen.
- **Kampagnen-Entwürfe per API** (`POST /v3/emailCampaigns`, `recipients.listIds`, `sender.id`),
  Testmail `POST /emailCampaigns/{id}/sendTest` (max. wenige pro Stunde). Versenden/Planen macht der
  Nutzer. Geplante Kampagnen sind gesperrt; „pausiert“ lässt sich per API ändern.
- **Mail-Bilder:** kein WebP (Outlook/Gmail) → JPG. Ein fertiges Bild pro Mail (PIL, 1120×760,
  dunkle Bühne, Produktfotos als Reihe, Badge) nach `assets/mail/` im Theme, per URL einbinden.
  Brevo lädt Bilder beim Versand auf `img.<branding-domain>` um – ist dort das SSL-Zertifikat
  abgelaufen, fehlen **alle** Bilder: Branding in Brevo prüfen/entfernen oder Support.
- Kampagnen-Texte ohne Euro-Beträge, wenn Preise sich noch ändern können („20 % günstiger als
  einzeln“); feste Preise nur bei festen Angebotspreisen.

## Wochenangebote bewerben
- Eine Mail pro Woche, **Dienstag 10 Uhr** (Angebot läuft schon, Klick führt zum aktiven Angebot).
- Button-Link `https://<shop>/?alp_deal=<YYYYMMDD>-<index>` legt den Stack in den Warenkorb
  (Schlüssel = Wochenbeginn + Listenindex; abgelaufen → Hinweis + Startseite).
- WhatsApp-Status/Kanal am Montagabend mit dem Stack-Bild.

## Rabatt-Logik zusammen
- Wochenangebot/Stack gilt als „reduziert“ → Willkommens- und Affiliate-Codes (exclude_sale_items)
  wirken nur auf die übrigen Produkte. So addieren sich Rabatte nicht.
