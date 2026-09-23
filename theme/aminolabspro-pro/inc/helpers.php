<?php
/**
 * Hilfsfunktionen, die überall im Theme genutzt werden.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Liest einen Wert aus /inc/config.php. Punkt-Notation: alp_config( 'hero.title' ).
 */
function alp_config( $key = null, $default = null ) {
	static $config = null;
	if ( null === $config ) {
		$config = require ALP_DIR . '/inc/config.php';
		$config = apply_filters( 'alp_config', $config );
	}
	if ( null === $key ) {
		return $config;
	}
	$value = $config;
	foreach ( explode( '.', $key ) as $part ) {
		if ( ! is_array( $value ) || ! array_key_exists( $part, $value ) ) {
			return $default;
		}
		$value = $value[ $part ];
	}
	return $value;
}

/**
 * Lädt eine Datei aus /data/ (z. B. coa-batches, faq).
 */
function alp_data( $name ) {
	static $cache = array();
	if ( ! isset( $cache[ $name ] ) ) {
		$file           = ALP_DIR . '/data/' . sanitize_file_name( $name ) . '.php';
		$cache[ $name ] = file_exists( $file ) ? require $file : array();
	}
	return $cache[ $name ];
}

/**
 * Bindet ein Template-Teil aus /template-parts/ ein und reicht Variablen durch.
 */
function alp_part( $slug, $args = array() ) {
	get_template_part( 'template-parts/' . $slug, null, $args );
}

/**
 * Baut aus einem Seiten-Slug oder einer URL einen Link.
 * 'shop' / 'cart' / 'checkout' / 'myaccount' → WooCommerce-Seiten, '/pfad/' → home_url.
 */
function alp_link( $target ) {
	if ( preg_match( '#^https?://#', $target ) ) {
		return $target;
	}
	if ( in_array( $target, array( 'shop', 'cart', 'checkout', 'myaccount' ), true ) && function_exists( 'wc_get_page_permalink' ) ) {
		return wc_get_page_permalink( $target );
	}
	return home_url( $target );
}

/**
 * Inline-SVG-Icons (Linienstil, 24px Raster). Neue Icons einfach im Array ergänzen.
 */
function alp_icon( $name, $size = 20, $class = '' ) {
	$paths = array(
		'flask'    => '<path d="M9 3h6M10 3v6.2L4.6 18.1A2 2 0 0 0 6.3 21h11.4a2 2 0 0 0 1.7-2.9L14 9.2V3"/><path d="M7.5 15h9"/>',
		'shield'   => '<path d="M12 3 4.5 6v5.6c0 4.4 3.1 8.2 7.5 9.4 4.4-1.2 7.5-5 7.5-9.4V6L12 3Z"/><path d="m9 12 2 2 4-4"/>',
		'truck'    => '<path d="M3 6h11v10H3zM14 10h4l3 3v3h-7"/><circle cx="7" cy="18" r="1.8"/><circle cx="17.5" cy="18" r="1.8"/>',
		'box'      => '<path d="M21 8 12 3 3 8v8l9 5 9-5V8Z"/><path d="m3 8 9 5 9-5M12 13v8"/>',
		'lock'     => '<rect x="4.5" y="10.5" width="15" height="10" rx="2"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3"/>',
		'doc'      => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5Z"/><path d="M14 3v5h5M9 13h6M9 17h4"/>',
		'search'   => '<circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.2-4.2"/>',
		'cart'     => '<path d="M3 4h2.2l2.2 11.2a1.5 1.5 0 0 0 1.5 1.2h8.6a1.5 1.5 0 0 0 1.5-1.1L21 8H6.2"/><circle cx="9.5" cy="20" r="1.3"/><circle cx="17" cy="20" r="1.3"/>',
		'grid'     => '<rect x="4" y="4" width="6.5" height="6.5" rx="1.5"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.5"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.5"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.5"/>',
		'home'     => '<path d="m4 11 8-7 8 7v8.5a1.5 1.5 0 0 1-1.5 1.5H15v-6H9v6H5.5A1.5 1.5 0 0 1 4 19.5V11Z"/>',
		'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'check'    => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
		'clock'    => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
		'book'     => '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15H6.5A2.5 2.5 0 0 0 4 20.5v-15Z"/><path d="M4 20.5A2.5 2.5 0 0 1 6.5 18H20v3H6.5"/>',
		'calc'     => '<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M8.5 7h7M8.5 11h.01M12 11h.01M15.5 11h.01M8.5 14.5h.01M12 14.5h.01M15.5 14.5h.01M8.5 18h.01M12 18h3.5"/>',
		'drop'     => '<path d="M12 3.5s6 6.4 6 10.5a6 6 0 0 1-12 0c0-4.1 6-10.5 6-10.5Z"/>',
		'snow'     => '<path d="M12 3v18M4.2 7.5l15.6 9M4.2 16.5l15.6-9M9.5 4.5 12 7l2.5-2.5M9.5 19.5 12 17l2.5 2.5"/>',
		'external' => '<path d="M14 4h6v6M20 4l-8 8M18 14v4.5A1.5 1.5 0 0 1 16.5 20h-11A1.5 1.5 0 0 1 4 18.5v-11A1.5 1.5 0 0 1 5.5 6H10"/>',
		'plus'     => '<path d="M12 5v14M5 12h14"/>',
		'close'    => '<path d="M6 6l12 12M18 6 6 18"/>',
		'mail'     => '<rect x="3.5" y="5.5" width="17" height="13" rx="2"/><path d="m4 7 8 6 8-6"/>',
		'spark'    => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M6 18l2.5-2.5M15.5 8.5 18 6"/>',
		'layers'   => '<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 13 9 5 9-5"/>',
		'bolt'     => '<path d="M13 3 5 13.5h6L10 21l8-10.5h-6L13 3Z"/>',
		'leaf'     => '<path d="M5 19c0-8 5-14 15-15-1 10-7 15-15 15Z"/><path d="M5 19c3-4 6-7 10-9"/>',
		'pulse'    => '<path d="M3 12h4l2.5-6 5 12 2.5-6H21"/>',
		'atom'     => '<circle cx="12" cy="12" r="1.6"/><ellipse cx="12" cy="12" rx="9" ry="3.8"/><ellipse cx="12" cy="12" rx="9" ry="3.8" transform="rotate(60 12 12)"/><ellipse cx="12" cy="12" rx="9" ry="3.8" transform="rotate(120 12 12)"/>',
		'sun'      => '<circle cx="12" cy="12" r="4"/><path d="M12 2.5v2.5M12 19v2.5M2.5 12H5M19 12h2.5M5.3 5.3l1.8 1.8M16.9 16.9l1.8 1.8M5.3 18.7l1.8-1.8M16.9 7.1l1.8-1.8"/>',
		'user'     => '<circle cx="12" cy="8.5" r="3.8"/><path d="M4.5 20.5c1.2-3.7 4-5.5 7.5-5.5s6.3 1.8 7.5 5.5"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="alp-icon %1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $class ),
		(int) $size,
		$paths[ $name ]
	);
}

/**
 * Deutsche Zahl mit Komma: alp_num( 10.44 ) → "10,44".
 */
function alp_num( $value, $decimals = 2 ) {
	return number_format_i18n( (float) $value, $decimals );
}

/**
 * Aktuelle URL ohne Query-String (für „aktiver Menüpunkt“).
 */
function alp_current_url() {
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? strtok( wp_unslash( $_SERVER['REQUEST_URI'] ), '?' ) : '/'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : wp_parse_url( home_url(), PHP_URL_HOST );
	return ( is_ssl() ? 'https://' : 'http://' ) . $host . $uri;
}
