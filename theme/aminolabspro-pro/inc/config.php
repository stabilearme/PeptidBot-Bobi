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

	/* Bestellschluss für „Versand heute“ (24h-Format, Mo–Fr). */
	'shipping_cutoff_hour' => 14,

	/* ---------- Laufleiste oben ---------- */
	'announcement' => array(
		array( 'icon' => 'clock', 'text' => 'Bestellung bis 14 Uhr – Versand am selben Werktag' ),
		array( 'icon' => 'flask', 'text' => 'Jede Charge unabhängig per HPLC geprüft' ),
		array( 'icon' => 'truck', 'text' => 'Versand aus Deutschland · diskret verpackt' ),
	),

	/* ---------- Startseite: Hero ---------- */
	'hero' => array(
		'eyebrow'   => 'Research Grade · Drittlabor-geprüft',
		'title'     => 'Reinheit, die du <em>nachprüfen</em> kannst.',
		'text'      => 'Jede Charge wird von einem unabhängigen Labor per HPLC analysiert. Das vollständige Zertifikat findest du über die Chargennummer auf deinem Vial.',
		'primary'   => array( 'label' => 'Peptide entdecken', 'url' => 'shop' ),
		'secondary' => array( 'label' => 'Charge prüfen', 'url' => '/#charge-pruefen' ),
		/* Kurze Häkchen-Zeile unter den Buttons. */
		'checks'    => array( 'Öffentliche COAs', 'Versand aus DE', 'Vorkasse per Überweisung' ),
		/*
		 * Kennzahlen: mit Hero-Bild als schwebende „Labor-Anzeige“ unter dem Hero, sonst im Hero.
		 * {coa} = Zahl der veröffentlichten Zertifikate.
		 * 'visual': meter (Balken bis 'fill' %), ring (Kreis bis 'fill' %), docs (ein Kästchen je COA), timeline (Schritte aus 'steps').
		 */
		'stats'     => array(
			array( 'value' => '≥ 98 %', 'label' => 'Reinheit laut HPLC', 'icon' => 'flask', 'visual' => 'meter', 'fill' => 98 ),
			array( 'value' => '100 %', 'label' => 'der Chargen getestet', 'icon' => 'shield', 'visual' => 'ring', 'fill' => 100 ),
			array( 'value' => '{coa} COAs', 'label' => 'öffentlich einsehbar', 'icon' => 'doc', 'visual' => 'docs' ),
			array( 'value' => '2–4 Tage', 'label' => 'Lieferzeit in Deutschland', 'icon' => 'truck', 'visual' => 'timeline', 'steps' => array( 'Bestellt', 'Versendet', 'Da' ) ),
		),
		/*
		 * Hero-Bild (Vials + Zertifikat) aus der Mediathek. Pfad ab /wp-content/ oder volle URL.
		 * Leer lassen ('') = stattdessen die gezeichnete Zertifikats-Karte anzeigen.
		 * Das Foto steht rechts in einer Karte; darauf schwebt die Karte der Charge aus 'featured_batch'.
		 */
		'image'     => '/wp-content/uploads/2026/09/aminolabspro-hero-2.webp',
		'image_alt' => 'Research-Peptide-Vials von aminolabspro mit Laborzertifikat von Analiza Białek',
		/* Welche Charge in der Zertifikats-Karte gezeigt wird (Chargennummer aus /data/coa-batches.php). */
		'featured_batch' => 'BPC-0726-01',
	),

	/* ---------- Startseite: Vertrauensleiste ---------- */
	'trust' => array(
		array( 'icon' => 'flask',  'title' => 'Unabhängiges Labor',  'text' => 'HPLC-Analyse jeder Charge durch ein Drittlabor' ),
		array( 'icon' => 'doc',    'title' => 'Öffentliche COAs',    'text' => 'Zertifikat per Chargennummer abrufbar' ),
		array( 'icon' => 'truck',  'title' => 'Versand aus DE',      'text' => 'Schnell und diskret verpackt' ),
		array( 'icon' => 'lock',   'title' => 'Sicher bezahlen',     'text' => 'Vorkasse per Banküberweisung, verschlüsselt übertragen' ),
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
				'cta'      => array( 'label' => 'Benachrichtigen lassen', 'url' => '/early-access/' ),
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
			'eyebrow' => 'Aktionsprodukt',
			'title'   => 'Nur für kurze Zeit reduziert.',
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
				array( 'title' => 'Unabhängige Laborprüfung', 'text' => 'Jede Charge wird per HPLC auf Reinheit und Wirkstoffgehalt analysiert.' ),
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
			'text'  => 'Trag dich für den Newsletter ein: neue Chargen, Laborergebnisse und exklusive Angebote. Abmeldung jederzeit.',
			'cta'   => array( 'label' => 'Rabatt sichern', 'url' => '/early-access/' ),
		),
	),

	/* ---------- Shop & Kategorien ---------- */
	'shop' => array(
		/* Text über der Produktliste (Kategorien zeigen stattdessen ihre Beschreibung aus WooCommerce). */
		'intro' => 'Research-Grade-Peptide, lyophilisiert und einzeln chargengeprüft. Zu jeder Charge gibt es ein öffentliches Laborzertifikat.',
		/* Vorteile als kleine Chips. „Versandkostenfrei ab …“ kommt automatisch aus free_shipping_threshold. */
		'chips' => array(
			array( 'icon' => 'flask', 'text' => 'HPLC-geprüft, ≥ 98 %' ),
			array( 'icon' => 'doc',   'text' => 'COA zu jeder Charge' ),
			array( 'icon' => 'truck', 'text' => 'Versand aus DE, 2–4 Werktage' ),
		),
	),

	/* ---------- Produktseite ---------- */
	'product' => array(
		/* Kleine Merkmale unter dem Produkttitel. */
		'chips'  => array( '≥ 98 % Reinheit', 'HPLC-verifiziert', 'Research Use Only' ),
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
		'about'   => 'Research-Grade-Peptide mit öffentlich einsehbaren Laborzertifikaten. Jede Charge unabhängig geprüft, Versand aus Deutschland.',
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
				'Newsletter'           => '/early-access/',
			),
		),
		'legal' => array(
			'Impressum'          => '/impressum/',
			'Datenschutz'        => '/datenschutzerklaerung/',
			'AGB'                => '/agb/',
			'Widerrufsbelehrung' => '/widerrufsbelehrung/',
		),
		'payments'   => array( 'Vorkasse', 'Banküberweisung' ), // laut Seite „Versand & Zahlung“: nur Vorkasse per Banküberweisung
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
	),
);
