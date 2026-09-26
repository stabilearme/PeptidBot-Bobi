<?php
/**
 * ============================================================
 *  ZENTRALE EINSTELLUNGEN & TEXTE
 * ============================================================
 *  Fast alle sichtbaren Texte, Links und Schalter des Themes
 *  stehen hier. Nach dem Ändern: Datei speichern, fertig.
 *
 *  Links:  '/pfad/'  → Seite deiner Website
 *          'shop' | 'cart' | 'checkout' | 'myaccount' → WooCommerce-Seiten
 *          'https://…' → externer Link
 *
 *  Chargen / COAs  → /data/coa-batches.php
 *  FAQ             → /data/faq.php
 * ============================================================
 */

defined( 'ABSPATH' ) || exit;

return array(

	/* ---------- Marke ---------- */
	'brand' => array(
		'name'    => 'AminoLabs Pro',
		'tagline' => 'Forschungspeptide in Research-Grade-Qualität',
		'email'   => 'support@aminolabspro.com',
		'country' => 'Versand aus Deutschland',
		// Logo im Header, falls im Customizer (Flatsome → Header → Logo) keins gewählt ist.
		// Pfad aus der Mediathek oder volle URL. '' = nur das Customizer-Logo verwenden.
		'logo'    => '/wp-content/uploads/2026/09/aminolabspro-logo_4.svg',
	),

	/* ---------- Schalter ---------- */
	'features' => array(
		'announcement_bar'   => true,  // Laufleiste ganz oben
		'custom_footer'      => true,  // Eigener Footer statt Flatsome-Footer
		'mobile_bottom_nav'  => true,  // App-artige Leiste unten auf dem Handy
		'sticky_add_to_cart' => true,  // Kaufleiste auf Produktseiten beim Scrollen
		'product_coa_box'    => true,  // Chargen-Box auf Produktseiten
		'product_coa_tab'    => true,  // Tab „Laborbericht“ mit Zertifikat auf Produktseiten
		'clean_descriptions' => true,  // Einheitliches Design für Produktbeschreibungen
		'clean_pages'        => true,  // Einheitliches Design für Seiten & Beiträge (alte Inline-Styles entfernen)
		'checkout_steps'     => true,  // Fortschrittsanzeige Warenkorb → Kasse
		'shop_intro'         => true,  // Einleitung + Vorteile über der Produktliste
		'category_pills'     => true,  // Kategorie-Filter über dem Shop
		'faq_schema'         => true,  // FAQ-Rich-Snippets auf der Startseite
		'whatsapp_float'     => true,  // Runder WhatsApp-Button unten rechts (nur mit hinterlegter Nummer)
		'animations'         => true,  // Dezente Animationen (Einblenden, hochzählende Werte). Bei „Bewegung reduzieren“ automatisch aus.
		'card_hplc_tag'      => true,  // Reinheit als Etikett auf dem Produktbild + Charge/Gehalt/COA-Link in der Kachel
		'welcome_popup'      => true,  // Kleine 15-%-Einblendung (unten rechts / am Handy unten), siehe 'welcome_popup'
		'flatsome_lazy_load' => false, // Flatsome-Lazy-Load per Skript. Aus = Bilder sofort im HTML, der Browser lädt sie selbst verzögert (robuster, besser für Google).
	),

	/* Versandkostenfrei ab (in €). 0 = Fortschrittsbalken im Warenkorb ausblenden. */
	'free_shipping_threshold' => 99, // laut Seite „Versand & Zahlung“: kostenloser Versand ab 99 €

	/*
	 * Bilder, die noch nicht in der Mediathek dieser Seite liegen, werden von hier geladen
	 * (z. B. nach dem Umzug von aminolabspro.com). '' = abschalten.
	 */
	'media_fallback_host' => 'https://aminolabspro.com',

	/* Gesamt-Design: 'editorial' (Fraunces/Manrope, Haarlinien, ruhig) oder '' (bisheriger Look mit Mint-Verläufen). */
	'design' => 'editorial',

	/* Aussehen der Karten auf der COA-Seite: 'certificate' (Urkunde mit Stempel) oder 'report' (kompakter Laborbefund). */
	'coa_card_style' => 'certificate',

	/*
	 * Banner-Bild der Early-Access-/Newsletter-Seite (Pfad aus der Mediathek oder volle URL).
	 * '' = gestalteter Navy-Banner mit 15-%-Gutschein statt Foto.
	 */
	'early_access_image' => '/wp-content/uploads/2026/09/hf_20260923_200208_fba7d7cf-88eb-481b-bbf8-ba504115d06f-1536x860.png',

	/*
	 * WhatsApp Business. Die Nummer am besten im Customizer eintragen
	 * (Design → Customizer → „AminoLabs Pro: Kontakt“); 'number' hier ist nur die Reserve.
	 */
	'whatsapp' => array(
		'number'  => '015124193155', // WhatsApp Business (wird zu +49 151 24193155)
		'message' => 'Hallo aminolabspro, ich habe eine Frage:',
		'title'   => 'Fragen? Schreib uns direkt auf WhatsApp.',
		'text'    => 'Persönliche Antwort vom Team zu Produkten, Chargen, Bestellung und Versand – schnell und unkompliziert.',
		'button'  => 'Chat auf WhatsApp starten',
	),

	/*
	 * Newsletter-Anmeldung (Brevo) – dasselbe Formular wie bisher auf aminolabspro.com.
	 * 'action': Formularadresse aus Brevo (Formulare → Formular → Teilen → HTML-Code → <form action="…">).
	 * Leer lassen = auf der Startseite erscheint stattdessen ein Button zur Seite /early-access/.
	 * 'thanks': Seite nach dem Absenden (dort steht „Bitte bestätige deine Anmeldung per E-Mail“).
	 */
	'newsletter_form' => array(
		'action'      => 'https://74ab9d24.sibforms.com/serve/MUIFAFVWCAsdLzTdPRPeQiTcD6gkTxpVFS1Efk1EmYD96MpjWFjKLWWFcV7i5UdjDshRnQ2PxOUSjdSJ5lpQYnej6D571OgFShuWdQVFr_KekoPOdtlsR9u1KWAaFqA0Z7AlzZxz-Q13vjyQ2ZVQCF826Ktl2IKhd6Aa9dDHp8J41G36-_m97uZm6DD76iUVPpWodzFxbnMYszQXRg==',
		'thanks'      => '/newsletter-vielen-dank/',
		'placeholder' => 'deine@email.de',
		'button'      => 'Rabatt sichern',
		'note'        => 'Kein Spam. Abmeldung jederzeit. Mit der Anmeldung akzeptierst du unsere <a href="/datenschutzerklaerung/">Datenschutzerklärung</a>.',
	),

	/*
	 * 15-%-Einblendung (inc/newsletter.php + theme.js): erscheint nach 'delay' Sekunden
	 * oder am PC, wenn die Maus die Seite verlassen will. Nie in Warenkorb, Kasse und Konto.
	 * Geschlossen → 'snooze_days' Tage Ruhe. Angemeldet → nie wieder.
	 */
	'welcome_popup' => array(
		'delay'       => 30,
		'snooze_days' => 30,
		'amount'      => '15 %',
		'title'       => 'auf deine erste Bestellung',
		'text'        => 'Trag deine E-Mail ein. Nach der Bestätigung bekommst du deinen persönlichen Code.',
		'button'      => 'Code sichern',
		'fine'        => 'Einmal pro Kunde, nicht auf reduzierte Produkte.',
	),

	/*
	 * Wochenangebot (inc/weekly-deal.php): Das Angebot auf der Startseite wechselt automatisch
	 * jeden Montag um 22 Uhr zum nächsten Eintrag in 'deals' und fängt am Ende wieder von vorn an.
	 * Kontrolle mit Datum und Preisen: WooCommerce → Wochenangebote.
	 *
	 * Pro Angebot:
	 *   'title'   Überschrift auf der Startseite
	 *   'text'    kurzer Satz darunter (optional)
	 *   'items'   Artikelnummern (SKU). Ein Eintrag = Einzelprodukt, mehrere = Stack.
	 *             Menge: array( 'sku' => 'ALP-BAC-10ML', 'qty' => 2 ). Fester Preis: 'price' => 29.90
	 *   'percent' Rabatt in % auf den aktuellen Verkaufspreis (0 = aktueller Preis bleibt)
	 * Durchgestrichen wird beim Einzelprodukt der niedrigste Preis der letzten 30 Tage (§ 11 PAngV);
	 * liegt der Aktionspreis nicht darunter, steht dort nur „Wochenpreis“ ohne Streichpreis.
	 * Ein Produkt höchstens alle 5 Wochen ins Angebot nehmen, sonst gibt es keinen Streichpreis.
	 * Einzelprodukt: in der Woche überall reduziert. Stack: reduziert im Warenkorb, wenn alle Produkte drin sind.
	 * Der Button legt alle Produkte des Angebots in den Warenkorb.
	 */
	'weekly_deals' => array(
		'enabled'     => true,
		'switch_day'  => 'monday',
		'switch_time' => '22:00',
		'timezone'    => 'Europe/Berlin',
		'first_week'  => '2026-09-22', // In dieser Woche läuft das erste Angebot der Liste (Mo 21.09. 22 Uhr bis Mo 28.09. 22 Uhr).
		'deals'       => array(
			// 1 · ab Mo 21.09.
			array(
				'title' => 'Retatrutide (Triple G) 10 mg',
				'text'  => 'Der Triple-Agonist zum Wochenpreis – dieselbe geprüfte Charge, dasselbe öffentliche COA.',
				'items' => array( array( 'sku' => 'ALP-RETA-10', 'price' => 54.90 ) ),
			),
			// 2 · ab Mo 28.09.
			array(
				'title'   => 'Recovery-Stack',
				'text'    => 'BPC-157, TB-500 und GHK-Cu im Set – 20 % unter dem Einzelpreis.',
				'items'   => array( 'ALP-BPC157-10', 'ALP-TB500-10', 'ALP-GHKCU-50' ),
				'percent' => 20,
			),
			// 3 · ab Mo 05.10.
			array(
				'title'   => 'Metabolic-Stack XL',
				'text'    => 'Retatrutide 30 mg und Cagrilintide 5 mg – die große Kombination für längere Versuchsreihen, 20 % unter dem Einzelpreis.',
				'items'   => array( 'ALP-RETA-30', 'ALP-CAGRI-5' ),
				'percent' => 20,
			),
			// 4 · ab Mo 12.10.
			array(
				'title'   => 'Nootropic-Stack',
				'text'    => 'Semax und Selank zusammen – 20 % unter dem Einzelpreis.',
				'items'   => array( 'ALP-SEMAX-10', 'ALP-SELANK-10' ),
				'percent' => 20,
			),
			// 5 · ab Mo 19.10.
			array(
				'title'   => 'Mitochondrien-Stack',
				'text'    => 'MOTS-c 10 mg und 5-Amino-1MQ 50 mg im Set – 20 % unter dem Einzelpreis.',
				'items'   => array( 'ALP-MOTSC-10', 'ALP-5A1MQ-50' ),
				'percent' => 20,
			),
			// 6 · ab Mo 26.10.
			array(
				'title'   => 'Metabolic-Stack',
				'text'    => 'Retatrutide 10 mg und Cagrilintide 5 mg – der Einstieg in die Kombination, 20 % unter dem Einzelpreis.',
				'items'   => array( 'ALP-RETA-10', 'ALP-CAGRI-5' ),
				'percent' => 20,
			),
			// 7 · ab Mo 02.11.
			array(
				'title'   => 'GH-Stack',
				'text'    => 'Ipamorelin und Tesamorelin im Set – 20 % unter dem Einzelpreis.',
				'items'   => array( 'ALP-IPA-10', 'ALP-TESA-10' ),
				'percent' => 20,
			),
			// 8 · ab Mo 09.11.
			array(
				'title'   => 'Longevity-Stack',
				'text'    => 'MOTS-c 40 mg und Epithalon 50 mg – die großen Einheiten zusammen, 20 % unter dem Einzelpreis.',
				'items'   => array( 'ALP-MOTSC-40', 'ALP-EPI-50' ),
				'percent' => 20,
			),
			// 9 · ab Mo 16.11.
			array(
				'title' => 'Retatrutide (Triple G) 30 mg',
				'text'  => 'Die große Einheit für längere Versuchsreihen – diese Woche besonders günstig.',
				'items' => array( array( 'sku' => 'ALP-RETA-30', 'price' => 119.90 ) ),
			),
			// 10 · ab Mo 23.11.
			array(
				'title'   => 'Skin-Stack',
				'text'    => 'GHK-Cu 100 mg und SNAP-8 im Set – 20 % unter dem Einzelpreis.',
				'items'   => array( 'ALP-GHKCU-100', 'ALP-SNAP8-10' ),
				'percent' => 20,
			),
			// 11 · ab Mo 30.11.
			array(
				'title'   => 'Immun- & Darm-Stack',
				'text'    => 'KPV, Thymosin Alpha-1 und BPC-157 im Set – 20 % unter dem Einzelpreis.',
				'items'   => array( 'ALP-KPV-10', 'ALP-TA1-10', 'ALP-BPC157-10' ),
				'percent' => 20,
			),
			// 12 · ab Mo 07.12. – danach beginnt die Liste wieder mit 1
			array(
				'title'   => 'Melanocortin-Stack',
				'text'    => 'Melanotan 2 und PT-141 im Set – 20 % unter dem Einzelpreis.',
				'items'   => array( 'ALP-MT2-10', 'ALP-PT141-10' ),
				'percent' => 20,
			),
		),
	),

	/*
	 * Affiliate-Wunschcode (inc/affiliate-coupon.php, YITH WooCommerce Affiliates):
	 * Feld im Affiliate-Anmeldeformular, Prüfung „schon vergeben“, Gutschein wird erst
	 * nach Genehmigung angelegt und dem Affiliate zugeordnet (Provision).
	 * Rabatt usw. gelten für neu angelegte Codes; bestehende unter Marketing → Gutscheine ändern.
	 */
	'affiliate_coupon' => array(
		'enabled'              => true,
		'label'                => 'Wunsch-Gutscheincode',
		'placeholder'          => 'z. B. MAX10',
		'hint'                 => 'Diesen Code können deine Kunden an der Kasse eingeben. Er wird aktiv, sobald wir deine Anmeldung freigegeben haben.',
		'min_length'           => 4,
		'max_length'           => 20,
		'reserved_prefixes'    => array( 'NEU15' ), // für Shop-Codes reserviert
		'discount_type'        => 'percent', // percent = Prozent, fixed_cart = fester Betrag
		'amount'               => 10,        // Rabatt für Kunden, die den Code einlösen
		'individual_use'       => true,      // nicht mit anderen Gutscheinen kombinierbar
		'exclude_sale_items'   => true,      // nicht auf reduzierte Produkte
		'usage_limit_per_user' => 0,         // 0 = beliebig oft pro Kunde
	),

	/*
	 * 15-%-Willkommenscode (inc/welcome-coupon.php): Gutscheine, deren Code so beginnt,
	 * gelten nur für die erste Bestellung einer E-Mail-Adresse.
	 */
	'welcome_coupon' => array(
		'prefix'  => 'NEU15-',
		'shared_code' => 'NEU15-WILLKOMMEN', // steht in der Brevo-Willkommensmail; für alle gleich, gilt je E-Mail-Adresse nur für die erste Bestellung
		'message' => 'Dieser Willkommenscode gilt nur für deine erste Bestellung.',
		'amount'  => 15,
		'expires' => '2027-09-24 23:59:59', // Paket 1: für Anmeldungen bis ca. März 2027 verwenden, dann neues Paket
	),

	/*
	 * Fortlaufende Bestellnummern (inc/order-numbers.php), kompatibel zu den bisherigen Nummern.
	 * 'auto' = nur aktiv, wenn kein anderer Code (z. B. Code Snippet) schon Nummern vergibt.
	 */
	'order_numbers'       => 'auto',
	'order_numbers_start' => 1000, // nur für einen ganz neuen Shop ohne bisherige Nummern

	/*
	 * Abgeschaltete Seiten: Aufruf leitet dauerhaft um, Menüeinträge werden ausgeblendet.
	 * Format: 'seiten-slug' => 'ziel' (z. B. '/' für die Startseite).
	 */
	'retired_pages' => array(
		'gluecksrad'     => '/',
		'kundenwuensche' => '/',
		'early-access'   => '/#newsletter', // Early Access ist vorbei – Anmeldung + 15-%-Code jetzt auf der Startseite
	),

	/* Kasse – Texte wie bisher auf aminolabspro.com (altes Child Theme). */
	'checkout' => array(
		'order_button_text' => 'Zahlungspflichtig bestellen',
		'terms_text'        => 'Ich habe die <a href="/agb/" target="_blank">Allgemeinen Geschäftsbedingungen</a>, die <a href="/datenschutzerklaerung/" target="_blank">Datenschutzerklärung</a> und die <a href="/widerrufsbelehrung/" target="_blank">Widerrufsbelehrung</a> gelesen und akzeptiert.',
	),

	/* Länder, in denen Bundesland/Kanton an der Kasse Pflicht ist (Stripe braucht ihn z. B. für die Schweiz). */
	'checkout_state_required' => array( 'CH' ),

	/* Bestellschluss für „Versand heute“ (24h-Format, Mo–Fr). */
	'shipping_cutoff_hour' => 14,

	/* ---------- Laufleiste oben ---------- */
	'announcement' => array(
		array( 'icon' => 'clock', 'text' => 'Bestellung bis 14 Uhr – Versand am selben Werktag' ),
		array( 'icon' => 'flask', 'text' => 'HPLC-Analysen aus unabhängigem Drittlabor' ),
		array( 'icon' => 'truck', 'text' => 'Versand aus Deutschland · diskret verpackt' ),
	),

	/* ---------- Startseite: Hero ---------- */
	'hero' => array(
		'eyebrow'   => 'Research Grade · Drittlabor-geprüft',
		/* SEO-Überschrift (H1) der Startseite – ersetzt die kleine Zeile darüber. Leer = die große Headline ist die H1. */
		'seo_h1'    => 'Forschungspeptide kaufen · Research Grade, drittlabor-geprüft',
		'title'     => 'Reinheit, die du <em>nachprüfen</em> kannst.',
		'text'      => 'Getestete Chargen werden von einem unabhängigen Labor per HPLC analysiert. Das vollständige Zertifikat findest du öffentlich über die Chargennummer auf deinem Vial.',
		'primary'   => array( 'label' => 'Peptide entdecken', 'url' => 'shop' ),
		'secondary' => array( 'label' => 'Charge prüfen', 'url' => '/#charge-pruefen' ),
		/* Kurze Häkchen-Zeile unter den Buttons. */
		'checks'    => array( 'Öffentliche COAs', 'Versand aus DE', 'Karte, Apple Pay & Vorkasse' ),
		/*
		 * Kennzahlen: mit Hero-Bild als schwebende „Labor-Anzeige“ unter dem Hero, sonst im Hero.
		 * {coa} = Zahl der veröffentlichten Zertifikate.
		 * 'visual': meter (Balken bis 'fill' %), ring (Kreis bis 'fill' %), docs (ein Kästchen je COA), timeline (Schritte aus 'steps').
		 */
		'stats'     => array(
			array( 'value' => '≥ 98 %', 'label' => 'Reinheit laut HPLC', 'icon' => 'flask', 'visual' => 'meter', 'fill' => 98 ),
			array( 'value' => 'HPLC', 'label' => 'Analyse im Drittlabor', 'icon' => 'shield', 'visual' => 'ring', 'fill' => 100 ),
			array( 'value' => '{coa} COAs', 'label' => 'öffentlich einsehbar', 'icon' => 'doc', 'visual' => 'docs' ),
			array( 'value' => '2–4 Tage', 'label' => 'Lieferzeit in Deutschland', 'icon' => 'truck', 'visual' => 'timeline', 'steps' => array( 'Bestellt', 'Versendet', 'Da' ) ),
		),
		/*
		 * Hero-Bild (Vials + Zertifikat) aus der Mediathek. Pfad ab /wp-content/ oder volle URL.
		 * Leer lassen ('') = stattdessen die gezeichnete Zertifikats-Karte anzeigen.
		 * Das Foto steht rechts in einer Karte; darauf schwebt die Karte der Charge aus 'featured_batch'.
		 */
		'image'     => '/wp-content/uploads/2026/09/aminolabspro-hero-2.webp',
		'image_alt' => 'Forschungspeptide kaufen – Research Peptide Vials mit COA bei aminolabspro',
		/* Welche Charge in der Zertifikats-Karte gezeigt wird (Chargennummer aus /data/coa-batches.php). */
		'featured_batch' => 'BPC-0726-01',
	),

	/* ---------- Startseite: Vertrauensleiste ---------- */
	'trust' => array(
		array( 'icon' => 'flask',  'title' => 'Unabhängiges Labor',  'text' => 'HPLC-Analyse durch ein unabhängiges Drittlabor' ),
		array( 'icon' => 'doc',    'title' => 'Öffentliche COAs',    'text' => 'Zertifikat per Chargennummer abrufbar' ),
		array( 'icon' => 'truck',  'title' => 'Versand aus DE',      'text' => 'Schnell und diskret verpackt' ),
		array( 'icon' => 'lock',   'title' => 'Sicher bezahlen',     'text' => 'Kreditkarte, Apple Pay oder Vorkasse – verschlüsselt über Stripe' ),
	),

	/* ---------- Startseite: Abschnitte ---------- */
	'sections' => array(
		'categories' => array(
			'eyebrow' => 'Sortiment',
			'title'   => 'Nach Forschungsgebiet sortiert',
			'text'    => 'Finde Peptide nach ihrem Forschungsschwerpunkt – jede Kategorie mit dokumentierten Chargen.',
			/* Kategorien, die nicht angezeigt werden (Slugs). */
			'exclude' => array( 'unkategorisiert', 'uncategorized' ),
		),
		'products' => array(
			'eyebrow' => 'Meistgekauft',
			'title'   => 'Beliebt in der Forschung.',
			'limit'   => 8,
			/* Anzahl Kategorie-Filter über der Liste (0 = stattdessen Link „Alle Produkte“). */
			'pills'   => 4,
			'orderby' => 'popularity', // popularity | date | price | rand | menu_order
			'cta'     => array( 'label' => 'Alle Produkte', 'url' => 'shop' ),
		),
		/*
		 * Neu & demnächst: Teaser für eine kommende Neuheit + umschaltbare Listen
		 * „Neu im Shop“ (neueste Produkte) und „Angebote“ (reduzierte Produkte).
		 * Teaser ausblenden: 'teaser' => array(). Erscheint das Produkt, bei 'cta' den Produktlink eintragen.
		 */
		'news' => array(
			'eyebrow' => 'Neu & demnächst',
			'title'   => 'Frisch im Labor.',
			'teaser'  => array(
				'badge'    => 'Demnächst',
				'title'    => 'Transportabler Vial-Kühlschrank',
				'text'     => 'Kompakte Kühlbox für deine Vials – mit Akku, per USB aufladbar und mit konstanter Kühltemperatur. Ideal für Transport und Laborwechsel.',
				// Produktbild mit eingebauten Produktangaben → die Merkmal-Liste wird ausgeblendet, das Bild ganz gezeigt.
				// 'tone' => 'dark' (dunkle Karte) oder 'light' (helle Karte, z. B. für aminolabspro-kuehlbox-hero.webp).
				'image'          => '/wp-content/uploads/2026/09/Mobiler-Vial-Kuehlschrank-im-Studio-1024x683.png',
				'image_has_text' => true,
				'tone'           => 'dark',
				'features' => array( 'Akkubetrieb 4–6 Stunden', 'USB-Aufladung', 'Kompakt & transportabel', 'Konstante Kühltemperatur' ),
				// E-Mail-Feld direkt in der Karte. Adressen landen in WordPress unter WooCommerce → Benachrichtigungen
				// (CSV-Export für Brevo). Ohne 'notify' erscheint stattdessen der Button aus 'cta'.
				'notify'   => array(
					'list'        => 'kuehlschrank',
					'button'      => 'Benachrichtigen lassen',
					'placeholder' => 'deine@email.de',
					'consent'     => 'Wir schreiben dir einmalig, sobald der Kühlschrank verfügbar ist. Kein Newsletter. <a href="/datenschutzerklaerung/">Datenschutz</a>',
					'success'     => 'Danke! Wir schreiben dir, sobald der Kühlschrank verfügbar ist.',
				),
				'cta'      => array( 'label' => 'Benachrichtigen lassen', 'url' => '/#newsletter' ),
				'note'     => 'Wir informieren dich per E-Mail, sobald er verfügbar ist.',
			),
			'limit'   => 4,
			'tabs'    => array(
				'new'  => 'Neu im Shop',
				'sale' => 'Angebote',
			),
		),
		'coa' => array(
			'eyebrow' => 'Transparenz',
			'title'   => 'Prüf deine Charge in Sekunden.',
			'text'    => 'Gib die Chargennummer von deinem Etikett ein und sieh das vollständige Analysezertifikat: gemessener Wirkstoffgehalt, HPLC-Reinheit, Prüflabor und Datum.',
			'cta'     => array( 'label' => 'Alle Laborergebnisse', 'url' => '/coa/' ),
		),
		/*
		 * Aktionsprodukt: großes Produkt mit Rabatt-Hinweis und Countdown.
		 * 'sku' leer = automatisch das Produkt mit dem höchsten Rabatt.
		 * Countdown: Enddatum aus dem Produkt („Angebot bis“ in WooCommerce), sonst 'ends' (JJJJ-MM-TT).
		 * Ist nichts reduziert, wird der Abschnitt ausgeblendet.
		 */
		'deal' => array(
			'eyebrow' => 'Wochenangebot',
			'title'   => 'Jeden Montag ein neues Angebot.',
			'text'    => 'Jede Woche ein Research-Peptid zum Aktionspreis – dieselbe geprüfte Charge, dasselbe öffentliche COA.',
			'sku'     => '',
			'ends'    => '',
		),
		/* Research-Partner / Affiliate */
		'partner' => array(
			'eyebrow'    => 'Research-Partner',
			'title'      => 'Wir suchen ständig neue Research-Partner.',
			'text'       => 'Du forschst, schreibst oder hast eine Community rund um Peptide? Empfiehl aminolabspro und verdiene an jeder Bestellung mit.',
			'commission' => '15 %',
			'code'       => 'DEINCODE',
			'benefits'   => array(
				array( 'title' => '15 % Provision von Anfang an', 'text' => 'Auf jede Bestellung über deinen Code – ohne Mindestumsatz.' ),
				array( 'title' => 'Eigener Partner-Code', 'text' => 'Jede Bestellung mit deinem Code wird dir automatisch zugeordnet.' ),
				array( 'title' => 'Eigenes Partner-Dashboard', 'text' => 'Klicks, Bestellungen und Provision jederzeit im Blick.' ),
			),
			// Button öffnet WhatsApp mit diesem Text (ohne WhatsApp-Nummer: Kontaktseite).
			'cta'        => array( 'label' => 'Research-Partner werden', 'whatsapp' => 'Hey, ich möchte Partner werden.', 'url' => '/contakt/' ),
		),
		'process' => array(
			'eyebrow' => 'Ablauf',
			'title'   => 'Von der Synthese bis zu dir.',
			'steps'   => array(
				array( 'title' => 'Synthese & Lyophilisierung', 'text' => 'Research-Grade-Peptide, gefriergetrocknet für maximale Stabilität.' ),
				array( 'title' => 'Unabhängige Laborprüfung', 'text' => 'Getestete Chargen werden per HPLC auf Reinheit und Wirkstoffgehalt analysiert.' ),
				array( 'title' => 'Zertifikat veröffentlicht', 'text' => 'Das COA wird der Chargennummer zugeordnet und öffentlich gestellt.' ),
				array( 'title' => 'Versand aus Deutschland', 'text' => 'Bis 14 Uhr bestellt, am selben Werktag verschickt – diskret verpackt.' ),
			),
		),
		'knowledge' => array(
			'eyebrow' => 'Wissen',
			'title'   => 'Fundiert statt Hörensagen.',
			/* Neueste Artikel mit Titelbild (0 = stattdessen die Kacheln unten). */
			'posts'   => 3,
			'cta'     => array( 'label' => 'Alle Artikel', 'url' => '/wissen/' ),
			'items'   => array(
				array( 'icon' => 'doc',  'title' => 'COAs richtig lesen', 'text' => 'Was HPLC, Massenspektrometrie und Wirkstoffgehalt wirklich aussagen.', 'url' => '/coas-richtig-lesen-verstehen/' ),
				array( 'icon' => 'calc', 'title' => 'Rekonstitutionsrechner', 'text' => 'Konzentration und Volumen für dein Laborprotokoll exakt berechnen.', 'url' => '/dosierungsrechner/' ),
				array( 'icon' => 'book', 'title' => 'Wissensdatenbank', 'text' => 'Lagerung, Rekonstitution, Analytik und Grundlagen der Peptidforschung.', 'url' => '/wissen/' ),
			),
		),
		'faq' => array(
			'eyebrow' => 'FAQ',
			'title'   => 'Häufige Fragen.',
		),
		'newsletter' => array(
			'title' => '15 % auf deine erste Bestellung.',
			'text'  => 'Dein Willkommensrabatt: Melde dich an und erhalte nach der Bestätigung per E-Mail deinen persönlichen 15-%-Code für die erste Bestellung. Dazu neue Chargen, Laborergebnisse und Angebote – Abmeldung jederzeit.',
			'cta'   => array( 'label' => 'Rabatt sichern', 'url' => '/#newsletter' ), // nur ohne Brevo-Formular (newsletter_form.action) als Button
		),
	),

	/* ---------- Shop & Kategorien ---------- */
	'shop' => array(
		/* Text über der Produktliste (Kategorien zeigen stattdessen ihre Beschreibung aus WooCommerce). */
		'intro' => 'Research-Grade-Peptide, lyophilisiert. Zu getesteten Chargen gibt es ein öffentliches Laborzertifikat.',
		/* Vorteile als kleine Chips. „Versandkostenfrei ab …“ kommt automatisch aus free_shipping_threshold. */
		'chips' => array(
			array( 'icon' => 'flask', 'text' => 'HPLC-geprüft, ≥ 98 %' ),
			array( 'icon' => 'doc',   'text' => 'Öffentliche COAs' ),
			array( 'icon' => 'truck', 'text' => 'Versand aus DE, 2–4 Werktage' ),
		),
	),

	/* ---------- Produktseite ---------- */
	'product' => array(
		/* Kleine Merkmale unter dem Produkttitel. */
		'chips'  => array( '≥ 98 % Reinheit', 'HPLC-verifiziert', 'Research Use Only' ),
		/* Preis-Hinweis in Kaufleiste & Aktionsprodukt, nur falls Germanized fehlt (sonst gelten dessen Texte). */
		'price_note' => 'Kein Ausweis der USt. (Kleinunternehmer, § 19 UStG), zzgl. Versand',
		/* Kurzbeschreibung auf der Produktseite zeigen? Bisher auf .com ausgeblendet. */
		'show_short_description' => false,
		/* Liste unter dem Warenkorb-Button. */
		'trust'  => array(
			array( 'icon' => 'truck', 'text' => 'Versand aus Deutschland, diskret verpackt' ),
			array( 'icon' => 'doc',   'text' => 'Chargenzertifikat öffentlich einsehbar' ),
			array( 'icon' => 'snow',  'text' => 'Spezifikation & Lagerhinweise je Charge' ),
			array( 'icon' => 'lock',  'text' => 'Verschlüsselte, sichere Zahlung' ),
		),
		/* Produkte ohne Chargen-Box (Artikelnummern), z. B. Zubehör. */
		'coa_exclude_skus' => array( 'ALP-BAC-10ML' ),
		'ruo_notice' => 'Ausschließlich für wissenschaftliche Forschungszwecke. Nicht für den menschlichen oder tierischen Verzehr, die Diagnose oder Behandlung bestimmt.',
	),

	/* ---------- Footer ---------- */
	'footer' => array(
		'about'   => 'Research-Grade-Peptide mit öffentlich einsehbaren Laborzertifikaten. Unabhängig im Drittlabor geprüft, Versand aus Deutschland.',
		'columns' => array(
			'Shop' => array(
				'Alle Produkte'        => 'shop',
				'Laborergebnisse & COAs' => '/coa/',
				'Mein Konto'           => 'myaccount',
			),
			'Wissen' => array(
				'Wissensdatenbank'     => '/wissen/',
				'COAs richtig lesen'   => '/coas-richtig-lesen-verstehen/',
				'Rekonstitutionsrechner' => '/dosierungsrechner/',
				'Echtheit von Bewertungen' => '/echtheit-von-bewertungen/',
			),
			'Service' => array(
				'Kontakt'              => '/contakt/',
				'Versand & Zahlung'    => '/versandarten/',
				'Partnerprogramm'      => '/affiliate-dashboard/',
				'15 % Willkommensrabatt' => '/#newsletter',
			),
		),
		'legal' => array(
			'Impressum'          => '/impressum/',
			'Datenschutz'        => '/datenschutzerklaerung/',
			'AGB'                => '/agb/',
			'Widerrufsbelehrung' => '/widerrufsbelehrung/',
		),
		'payments'   => array( 'Kreditkarte', 'Apple Pay', 'Vorkasse' ), // wie im Shop auf aminolabspro.com (Stripe + Überweisung)
		'disclaimer' => 'Alle Produkte sind ausschließlich für Forschungs- und Laborzwecke bestimmt. Sie sind keine Arzneimittel, Lebensmittel oder Kosmetika und nicht zur Anwendung am Menschen oder Tier vorgesehen. Abgabe nur an Personen ab 18 Jahren.',
	),

	/* ---------- Mobile-Navigation (unten) ---------- */
	'mobile_nav' => array(
		array( 'icon' => 'home',   'label' => 'Start',   'url' => '/' ),
		array( 'icon' => 'grid',   'label' => 'Shop',    'url' => 'shop' ),
		array( 'icon' => 'search', 'label' => 'Suche',   'url' => '#alp-search' ),
		array( 'icon' => 'doc',    'label' => 'COAs',    'url' => '/coa/' ),
		array( 'icon' => 'cart',   'label' => 'Korb',    'url' => 'cart' ),
	),

	/* Icons für Kategorien (Slug → Icon-Name aus inc/helpers.php). */
	'category_icons' => array(
		'regeneration'            => 'pulse',
		'anti-aging'              => 'leaf',
		'glp-1-gip-glukagon'      => 'layers',
		'fettfreisetzung'         => 'bolt',
		'hungerunterdrueckung'    => 'drop',
		'mitochondriale-funktion' => 'atom',
		'performance'             => 'spark',
		'skin'                    => 'sun',
		'stimulierend'            => 'bolt',
		'reconstitution'          => 'flask',
		'hair-anti-aging'         => 'leaf',
	),

	/*
	 * Flatsome-Einstellungen, die beim Aktivieren des Themes gesetzt werden
	 * (einmalig, zusätzlich zu deinen übernommenen alten Einstellungen).
	 * Leer lassen = nichts überschreiben.
	 */
	'flatsome_mods' => array(
		'color_primary'             => '#2E8B6E',
		'color_secondary'           => '#1A1F2E',
		'color_success'             => '#2E8B6E',
		'logo_position'             => 'left',
		'logo_width'                => '200',
		'header_height'             => '76',
		'header_height_mobile'      => '60',
		'header_elements_left'      => array( 'nav' ),
		'header_elements_right'     => array( 'search', 'account', 'cart' ),
		'header_mobile_elements_left'  => array( 'menu-icon' ),
		'header_mobile_elements_right' => array( 'cart' ),
		'nav_uppercase'             => false,
		'nav_size'                  => 'default',
		'nav_spacing'               => 'large',
		'header_bg'                 => 'rgba(255,255,255,0.88)',
		'header_search_style'       => 'dropdown',
		'site_width'                => '1280',
		'product_layout'            => 'no-sidebar',
		'category_sidebar'          => 'none',
		'category_row_count'        => '4',
		'category_row_count_tablet' => '3',
		'category_row_count_mobile' => '2',
		'add_to_cart_style'         => 'flat',
		'short_description_in_grid' => false,
		'product_zoom'              => false,
		'breadcrumb_size'           => 'small',
		'back_to_top_mobile'        => false,
		/* Ab hier: Werte, die auf aminolabspro.com anders gespeichert sind und das Design sonst verschieben. */
		'grid_style'                => 'grid1',      // Produktkacheln linksbündig (grid2 = zentriert)
		'sale_bubble_percentage'    => true,         // Plakette „-31 %“ statt „Angebot!“
		'header_width'              => 'container',
		'nav_height'                => '16',         // .com: 107 → Menü/WhatsApp-Button zu hoch
		'nav_push'                  => '0',
		'topbar_show'               => false,        // Flatsome-Topbar aus – die Theme-Laufleiste ersetzt sie
		'header_mobile_elements_top' => array(),
		'header_account_title'      => false,        // nur Symbol statt „Mein Konto“
		'account_icon_style'        => 'plain',      // Konto-Symbol anzeigen (ohne Stil zeigt Flatsome nur Text)
		'header_cart_title'         => false,        // nur Betrag + Symbol statt „Warenkorb /“
		'product_display'           => 'tabs',       // .com: Akkordeon → Reiter „Beschreibung / Laborbericht“
		'product_info_align'        => 'left',
		'product_tabs_align'        => 'left',
		'category_title_style'      => 'normal',     // .com: „featured“ → grauer Balken mit weißem Titel-Kasten über dem Shop
		'header_shop_bg_featured'   => false,
	),
	/* true = die Werte oben gelten immer (auch wenn im Customizer etwas anderes gespeichert ist). */
	'force_flatsome_mods' => true,

	/*
	 * Flatsome-Einstellungen wie auf aminolabspro.de (data/flatsome-design.php): Alles, was auf der
	 * Seite davon abweicht, wird beim Anzeigen durch den .de-Wert bzw. den Flatsome-Standard ersetzt.
	 * Ausnahme: die Inhalte/Funktionen unten ('flatsome_keep') – dort gilt der Wert der Seite.
	 */
	'flatsome_mirror' => true,
	'flatsome_keep'   => array(
		'nav_menu_locations',
		'site_logo',
		'site_logo_dark',
		'sidebars_widgets',
		'backups',
		'smof_init',
		'flatsome_version',
		'flatsome_db_version',
		'activated_before',
		'of_backup',
		'of_transfer',
		'release_channel',
		'search_placeholder',
		'social_icons',
		'follow_email',
		'follow_facebook',
		'follow_instagram',
		'follow_twitter',
		'follow_whatsapp',
		'follow_phone',
		'header_newsletter_label',
		'google_map_api',
		'facebook_accounts',
		'maintenance_mode',
		'maintenance_mode_admin_notice',
		'maintenance_mode_bypass_key',
		'maintenance_mode_excluded_roles',
		'maintenance_mode_page',
		'maintenance_mode_text',
		'html_scripts_header',
		'html_scripts_footer',
		'html_scripts_after_body',
		'html_scripts_before_body',
		'404_block',
		'html_thank_you',
		'html_shop_page',
		'tab_title',
		'tab_content',
		'html_before_add_to_cart',
		'html_after_add_to_cart',
		'catalog_mode',
		'catalog_mode_prices',
		'catalog_mode_sale_badge',
		'catalog_mode_header',
		'catalog_mode_product',
		'catalog_mode_lightbox',
		'rank_math_primary_term',
		'rank_math_manages_product_layout_priority',
		'rank_math_breadcrumb',
		'fl_portfolio',
		'flatsome_studio',
		'swatches',
		'additional_variation_images',
		'disable_reviews',
		'product_buy_now',          // „Jetzt kaufen“-Button auf der Produktseite (wie bisher auf .com)
		'product_buy_now_redirect',
		'add_to_cart_icon',         // „In den Warenkorb“-Button in den Produktkacheln (wie auf .com)
	),
);
