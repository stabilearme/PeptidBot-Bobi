<?php
/**
 * Setup: Styles, Scripts, Schriften, Body-Klassen, Theme-Aktivierung.
 */

defined( 'ABSPATH' ) || exit;

/**
 * CSS-Dateien in /assets/css/. Reihenfolge = Lade-Reihenfolge.
 * 'when' bestimmt, auf welchen Seiten die Datei geladen wird.
 */
function alp_styles() {
	return array(
		'tokens'     => 'all',        // Farben, Schriften, Abstände (Design-System)
		'base'       => 'all',        // Typografie, Buttons, Formulare, Seiten
		'header'     => 'all',        // Kopfbereich, Laufleiste, Navigation
		'footer'     => 'all',        // Fußbereich, Mobile-Navigation
		'components' => 'all',        // Wiederverwendbare Bausteine (Karten, Chips, Sektionen)
		'shop'       => 'shop',       // Shop, Kategorien, Produktkacheln
		'content'    => 'content',    // Formatierte Inhalte: Produktbeschreibungen, Seiten, Beiträge
		'product'    => 'product',    // Einzelne Produktseite
		'pages'      => 'pages',      // Inhaltsseiten & Beiträge (Wissen, Kontakt, Versand, Rechtliches …)
		'checkout'   => 'checkout',   // Warenkorb, Kasse, Konto
		'home'       => 'home',       // Startseite
		'coa'        => 'coa',        // COA-Center + Chargen-Prüfer
		'polish'     => 'all',        // Politur: weiche Übergänge, breite Balken, einheitliche Tiefe
		'buttons'    => 'all',        // Einheitliche Buttons & Auswahlfelder (auch WooCommerce/Flatsome) – nach allen anderen
		'home-mobile' => 'home',      // Startseite am Handy: rausgezoomt, mehr nebeneinander
		'editorial'  => 'editorial',  // Design „Editorial“ (config.php → 'design'), überschreibt die Optik
		'motion'     => 'all',        // Animationen (Schalter: features.animations)
		'legacy'     => 'all',        // Platz für Übernahmen aus altem Zusatz-CSS
	);
}

function alp_style_needed( $when ) {
	if ( 'all' === $when ) {
		return true;
	}
	$is_wc = function_exists( 'is_woocommerce' );
	switch ( $when ) {
		case 'editorial':
			return 'editorial' === alp_config( 'design' );
		case 'home':
			return is_front_page();
		case 'product':
			return $is_wc && is_product();
		case 'shop':
			// Produktkacheln erscheinen auch auf Startseite und Produktseiten (ähnliche Produkte).
			return $is_wc && ( is_woocommerce() || is_front_page() || is_product() || is_cart() );
		case 'checkout':
			return $is_wc && ( is_cart() || is_checkout() || is_account_page() );
		case 'content':
			return ( $is_wc && is_product() ) || alp_style_needed( 'pages' );
		case 'pages':
			return ( is_page() && ! is_front_page() && ! is_page( 'coa' ) && ! ( $is_wc && ( is_cart() || is_checkout() || is_account_page() ) ) ) || ( is_single() && ! ( $is_wc && is_product() ) );
		case 'coa':
			return is_front_page() || is_page( 'coa' ) || ( $is_wc && is_product() );
	}
	return false;
}

add_action( 'wp_enqueue_scripts', 'alp_enqueue_assets', 120 );
function alp_enqueue_assets() {
	$deps = array();
	foreach ( alp_styles() as $name => $when ) {
		if ( ! alp_style_needed( $when ) ) {
			continue;
		}
		$file = '/assets/css/' . $name . '.css';
		if ( ! file_exists( ALP_DIR . $file ) ) {
			continue;
		}
		$handle = 'alp-' . $name;
		wp_enqueue_style( $handle, ALP_URI . $file, $deps, alp_asset_version( $file ) );
		$deps = array( $handle );
	}

	// Banner-Bild der Early-Access-/Newsletter-Seite (Klasse alp-ea-has-image über alp_ea_body_class) (ersetzt das Foto aus dem Seiteninhalt).
	$ea_image = alp_config( 'early_access_image' );
	if ( $ea_image && wp_style_is( 'alp-pages', 'enqueued' ) ) {
		wp_add_inline_style( 'alp-pages', '.alp-rich .alp-ea-hero{--alp-ea-image:url("' . esc_url( alp_link( $ea_image ) ) . '")}' );
	}

	wp_enqueue_script( 'alp-theme', ALP_URI . '/assets/js/theme.js', array(), alp_asset_version( '/assets/js/theme.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );

	if ( alp_style_needed( 'coa' ) ) {
		wp_enqueue_script( 'alp-coa', ALP_URI . '/assets/js/coa.js', array(), alp_asset_version( '/assets/js/coa.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
		wp_localize_script( 'alp-coa', 'ALP_COA', alp_coa_public_data() );
	}

	// DSGVO: Flatsome lädt Schriften sonst von Google-Servern. Das Theme bringt eigene, lokal gehostete Schriften mit.
	wp_dequeue_style( 'flatsome-googlefonts' );
	wp_deregister_style( 'flatsome-googlefonts' );
}

/**
 * Cache-Busting: Versionsnummer = Änderungsdatum der Datei.
 */
function alp_asset_version( $file ) {
	$path = ALP_DIR . $file;
	return file_exists( $path ) ? (string) filemtime( $path ) : ALP_VERSION;
}

/**
 * Schriften vorladen (schnellerer Seitenaufbau, kein Layout-Springen).
 */
add_action( 'wp_head', 'alp_preload_fonts', 1 );
function alp_preload_fonts() {
	foreach ( array( 'inter-latin-var.woff2', 'space-grotesk-latin-var.woff2' ) as $font ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( ALP_URI . '/assets/fonts/' . $font ) );
	}
	echo '<meta name="theme-color" content="#1A1F2E">' . "\n";
}

/**
 * Body-Klasse als Anker für alle Theme-Styles.
 */
add_filter( 'body_class', 'alp_body_class' );
function alp_body_class( $classes ) {
	$classes[] = 'alp';
	if ( is_page() ) {
		$classes[] = 'alp-page-' . sanitize_html_class( get_post_field( 'post_name', get_queried_object_id() ) );
	}
	if ( alp_config( 'features.mobile_bottom_nav' ) ) {
		$classes[] = 'alp-has-bottom-nav';
	}
	if ( alp_config( 'features.animations' ) ) {
		$classes[] = 'alp-motion';
	}
	return $classes;
}

/**
 * Beim Aktivieren: alte Flatsome-Einstellungen (Logo, Menüs, Header …) vom
 * bisherigen Child Theme übernehmen und danach die Design-Vorgaben aus config.php setzen.
 * Das alte „Zusätzliche CSS“ wird bewusst NICHT übernommen.
 */
add_action( 'after_switch_theme', 'alp_on_activate', 10, 2 );
function alp_on_activate( $old_name, $old_theme = null ) {
	$current = get_option( 'stylesheet' );
	if ( get_option( 'alp_mods_migrated' ) ) {
		return;
	}
	// Vorschau-Einblendung abschalten, damit unten die echten (gespeicherten) Werte gelesen und geschrieben werden.
	alp_preview_mods_off();

	$candidates = array();
	if ( $old_theme instanceof WP_Theme ) {
		$candidates[] = $old_theme->get_stylesheet();
	}
	$candidates = array_merge( $candidates, array( 'flatsome child', 'flatsome-child', 'flatsome' ) );

	$mods = array();
	foreach ( array_unique( $candidates ) as $slug ) {
		if ( $slug === $current ) {
			continue;
		}
		$found = get_option( 'theme_mods_' . $slug );
		if ( is_array( $found ) && ! empty( $found ) ) {
			$mods = $found;
			update_option( 'alp_mods_source', $slug, false );
			break;
		}
	}

	unset( $mods['custom_css_post_id'] );
	$mods = array_merge( $mods, (array) alp_config( 'flatsome_mods', array() ) );

	foreach ( $mods as $key => $value ) {
		set_theme_mod( $key, $value );
	}
	update_option( 'alp_mods_migrated', ALP_VERSION, false );
}

/**
 * Vorschau vor dem Aktivieren (z. B. mit dem Plugin „Theme Switcha“ nur für Admins):
 * Solange die Übernahme oben noch nicht gelaufen ist, werden Menüs, Logo usw. des
 * bisherigen Child Themes nur im Speicher eingeblendet – es wird nichts gespeichert,
 * und Besucher (die das alte Theme sehen) sind nicht betroffen.
 */
alp_preview_mods_boot();
function alp_preview_mods_boot() {
	if ( get_option( 'alp_mods_migrated' ) ) {
		return;
	}
	$ours = get_stylesheet();
	$src  = '';
	$old  = array();
	foreach ( array_unique( array( (string) get_option( 'stylesheet' ), 'flatsome child', 'flatsome-child' ) ) as $slug ) {
		if ( '' === $slug || $slug === $ours ) {
			continue;
		}
		$found = get_option( 'theme_mods_' . $slug );
		if ( is_array( $found ) && ! empty( $found ) ) {
			$src = $slug;
			$old = $found;
			break;
		}
	}
	if ( ! $src ) {
		return;
	}
	unset( $old['custom_css_post_id'] );
	$merged = array_merge( $old, (array) alp_config( 'flatsome_mods', array() ) );

	$GLOBALS['alp_preview_mod_filters'] = array(
		'option_theme_mods_' . $ours         => function ( $mods ) use ( $merged ) {
			return array_merge( $merged, is_array( $mods ) ? $mods : array() );
		},
		'default_option_theme_mods_' . $ours => function () use ( $merged ) {
			return $merged;
		},
		'option_theme_mods_' . $src          => function () use ( $merged ) {
			return $merged;
		},
	);
	foreach ( $GLOBALS['alp_preview_mod_filters'] as $hook => $callback ) {
		add_filter( $hook, $callback );
	}
}

function alp_preview_mods_off() {
	foreach ( (array) ( $GLOBALS['alp_preview_mod_filters'] ?? array() ) as $hook => $callback ) {
		remove_filter( $hook, $callback );
	}
	$GLOBALS['alp_preview_mod_filters'] = array();
}

/**
 * Logo-Fallback: Ist in Flatsome kein Logo gewählt, das Logo aus config.php (brand.logo) nehmen.
 * Flatsome akzeptiert statt einer Bild-ID auch eine URL (nötig für SVG-Logos).
 */
add_filter( 'theme_mod_site_logo', 'alp_default_logo' );
function alp_default_logo( $logo ) {
	// Flatsome liefert ohne gewähltes Logo sein eigenes Platzhalter-Logo (…/flatsome/assets/img/logo.png).
	$is_placeholder = is_string( $logo ) && ! is_numeric( $logo ) && false !== strpos( $logo, '/assets/img/logo' ) && false !== strpos( $logo, get_template_directory_uri() );
	if ( ! empty( $logo ) && ! $is_placeholder ) {
		return $logo;
	}
	$fallback = alp_config( 'brand.logo' );
	return $fallback ? alp_link( $fallback ) : $logo;
}

/**
 * Bildgrößen für Produktkacheln (quadratisch, scharf auf Retina).
 */
add_action( 'after_setup_theme', 'alp_theme_setup', 20 );
function alp_theme_setup() {
	add_theme_support( 'responsive-embeds' );
	add_image_size( 'alp-card', 640, 640, true );
}

add_filter( 'body_class', 'alp_ea_body_class' );
function alp_ea_body_class( $classes ) {
	if ( alp_config( 'early_access_image' ) ) {
		$classes[] = 'alp-ea-has-image';
	}
	return $classes;
}

add_filter( 'body_class', 'alp_design_body_class' );
function alp_design_body_class( $classes ) {
	if ( 'editorial' === alp_config( 'design' ) ) {
		$classes[] = 'alp-ed';
	}
	return $classes;
}
