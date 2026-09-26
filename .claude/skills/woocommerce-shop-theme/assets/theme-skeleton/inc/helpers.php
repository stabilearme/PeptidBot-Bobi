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
	if ( 0 === strpos( $target, '/wp-content/uploads/' ) ) {
		return alp_upload_url( $target );
	}
	return home_url( $target );
}

/**
 * false, wenn ein Link auf eine WordPress-Seite zeigt, die es zwar gibt, die aber nicht
 * veröffentlicht ist (Entwurf/privat) – z. B. „Partnerprogramm“, solange das Affiliate-Plugin fehlt.
 */
function alp_link_is_live( $target ) {
	if ( ! preg_match( '#^/([a-z0-9-]+(?:/[a-z0-9-]+)*)/?$#i', (string) $target, $m ) || ! function_exists( 'get_page_by_path' ) ) {
		return true;
	}
	$page = get_page_by_path( $m[1], OBJECT, 'page' );
	return ! $page || 'publish' === $page->post_status;
}

/**
 * URL einer Datei aus der Mediathek. Liegt sie nicht unter dem angegebenen Pfad
 * (z. B. nach einem Umzug: anderer Monatsordner oder „-1“ am Dateinamen),
 * wird sie in der Mediathek über den Dateinamen gesucht.
 */
function alp_upload_url( $path ) {
	static $cache = array();
	if ( isset( $cache[ $path ] ) ) {
		return $cache[ $path ];
	}
	$dir = wp_get_upload_dir();
	$rel = substr( $path, strlen( '/wp-content/uploads/' ) );
	$url = home_url( $path );

	if ( ! file_exists( trailingslashit( $dir['basedir'] ) . $rel ) ) {
		$found = get_transient( 'alp_upl_' . md5( $rel ) );
		if ( false === $found ) {
			global $wpdb;
			$info  = pathinfo( $rel );
			$name  = $info['filename'];
			$ext   = isset( $info['extension'] ) ? '.' . $info['extension'] : '';
			$rows  = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s",
					'%' . $wpdb->esc_like( $name ) . '%' . $wpdb->esc_like( $ext )
				)
			);
			$found = '';
			foreach ( $rows as $file ) {
				if ( preg_match( '#(^|/)' . preg_quote( $name, '#' ) . '(-\d+)?' . preg_quote( $ext, '#' ) . '$#', $file ) ) {
					$found = $file;
					break;
				}
			}
			set_transient( 'alp_upl_' . md5( $rel ), $found, $found ? WEEK_IN_SECONDS : HOUR_IN_SECONDS );
		}
		if ( $found ) {
			$url = trailingslashit( $dir['baseurl'] ) . $found;
		} elseif ( alp_config( 'media_fallback_host' ) ) {
			// Noch nicht in dieser Mediathek (z. B. nach dem Umzug): Bild von der alten Seite laden.
			$url = rtrim( alp_config( 'media_fallback_host' ), '/' ) . $path;
		}
	}
	return $cache[ $path ] = $url;
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

/**
 * Kennzahl mit Platzhalter: {coa} = Anzahl veröffentlichter Laborzertifikate.
 */
function alp_stat_value( $value ) {
	if ( false !== strpos( (string) $value, '{coa}' ) && function_exists( 'alp_coa_batches' ) ) {
		$count = count( array_filter( alp_coa_batches(), fn( $b ) => $b['done'] ) );
		$value = str_replace( '{coa}', (string) $count, $value );
	}
	return $value;
}

/**
 * Illustrierte Icons mit Tiefe (Verlauf, Lichtkante, Bodenschatten) für Kennzahlen & Hervorhebungen.
 * Verfügbar: flask, shield, docs, truck. Kachel-Styles: components.css → .alp-icon3d
 */
function alp_icon3d( $name, $size = 40 ) {
	static $n = 0;
	$n++;
	$m = 'alpi' . $n; // eindeutige Verlauf-IDs je Icon
	$defs = '<defs>'
		. '<linearGradient id="' . $m . 'm" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#9AF0CF"/><stop offset=".55" stop-color="#5FD3A9"/><stop offset="1" stop-color="#2E8B6E"/></linearGradient>'
		. '<linearGradient id="' . $m . 'g" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#FFFFFF" stop-opacity=".95"/><stop offset="1" stop-color="#DCEFE8" stop-opacity=".75"/></linearGradient>'
		. '<linearGradient id="' . $m . 'w" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FFFFFF"/><stop offset="1" stop-color="#E6EDF2"/></linearGradient>'
		. '</defs>';
	$floor = '<ellipse cx="24" cy="44" rx="13" ry="2.4" fill="#1A1F2E" opacity=".13"/>';
	$ink   = 'stroke="#1A1F2E" stroke-width="1.4" stroke-linejoin="round"';

	switch ( $name ) {
		case 'flask':
			$body = $floor
				. '<path d="M19 5.5h10" ' . $ink . ' stroke-linecap="round" fill="none"/>'
				. '<path d="M20.5 6v11.2l-9 15a5 5 0 0 0 4.3 7.6h16.4a5 5 0 0 0 4.3-7.6l-9-15V6z" fill="url(#' . $m . 'g)" ' . $ink . '/>'
				. '<path d="M14.9 29h18.2l2.6 4.3a3.6 3.6 0 0 1-3.1 5.4H15.4a3.6 3.6 0 0 1-3.1-5.4z" fill="url(#' . $m . 'm)"/>'
				. '<ellipse cx="24" cy="29" rx="9.1" ry="1.3" fill="#C4F7E4"/>'
				. '<circle cx="20.5" cy="34" r="1.4" fill="#fff" opacity=".85"/><circle cx="25.5" cy="32" r=".95" fill="#fff" opacity=".8"/><circle cx="28" cy="35.6" r="1.15" fill="#fff" opacity=".75"/>'
				. '<path d="M22.8 9.5v8.2" stroke="#fff" stroke-width="1.6" stroke-linecap="round" opacity=".95"/>'
				. '<path d="M16.2 32.5l1.8-3" stroke="#fff" stroke-width="1.2" stroke-linecap="round" opacity=".7"/>';
			break;
		case 'shield':
			$body = $floor
				. '<path d="M25.5 7.5l14 5v10c0 9.5-6 16.5-14 19.5-8-3-14-10-14-19.5v-10z" fill="#1F6B53" opacity=".28"/>'
				. '<path d="M24 5l14 5v10c0 9.5-6 16.5-14 19.5C16 36.5 10 29.5 10 20V10z" fill="url(#' . $m . 'm)" ' . $ink . '/>'
				. '<path d="M24 8.6l10.8 3.9V20c0 7.6-4.6 13.3-10.8 16z" fill="#fff" opacity=".22"/>'
				. '<path d="M13.2 12.4 24 8.6" stroke="#fff" stroke-width="1.4" stroke-linecap="round" opacity=".85"/>'
				. '<path d="M17.8 22.6l4.3 4.3 8.6-9" stroke="#1A1F2E" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round" fill="none" opacity=".22" transform="translate(.6 1)"/>'
				. '<path d="M17.8 22.6l4.3 4.3 8.6-9" stroke="#fff" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>';
			break;
		case 'docs':
			$body = $floor
				. '<rect x="15" y="5.5" width="21" height="28" rx="2.5" fill="#D4F3E8" ' . $ink . ' transform="rotate(9 25 20)"/>'
				. '<rect x="13" y="6.5" width="21" height="28" rx="2.5" fill="#F0FBF7" ' . $ink . ' transform="rotate(-5 23 20)"/>'
				. '<path d="M11 10.5A2.5 2.5 0 0 1 13.5 8H26l7 7v22.5a2.5 2.5 0 0 1-2.5 2.5h-17A2.5 2.5 0 0 1 11 37.5z" fill="url(#' . $m . 'w)" ' . $ink . '/>'
				. '<path d="M26 8v5a2 2 0 0 0 2 2h5z" fill="#D4F3E8" ' . $ink . '/>'
				. '<path d="M15 19h9M15 23h13M15 27h7" stroke="#9AA9B8" stroke-width="1.5" stroke-linecap="round"/>'
				. '<circle cx="29.5" cy="33" r="6.2" fill="url(#' . $m . 'm)" ' . $ink . '/>'
				. '<path d="M26.9 33.1l1.8 1.8 3.5-3.7" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none"/>'
				. '<path d="M26.3 30.6a3.8 3.8 0 0 1 2.4-1.6" stroke="#fff" stroke-width="1" stroke-linecap="round" opacity=".8"/>';
			break;
		case 'truck':
			$body = $floor
				. '<path d="M1.5 17h4M.5 21.5h5M2 26h3" stroke="#5FD3A9" stroke-width="1.8" stroke-linecap="round"/>'
				. '<rect x="7" y="11" width="22" height="20" rx="2.5" fill="url(#' . $m . 'w)" ' . $ink . '/>'
				. '<rect x="11" y="16" width="10" height="5.5" rx="1.2" fill="url(#' . $m . 'm)"/>'
				. '<path d="M8.5 13.2h18" stroke="#fff" stroke-width="1.4" stroke-linecap="round"/>'
				. '<path d="M29 17h7.6l5.4 6.5V31H29z" fill="url(#' . $m . 'm)" ' . $ink . '/>'
				. '<path d="M31 19.2h4.6l3.5 4.3H31z" fill="#D8F8EC" stroke="#1A1F2E" stroke-width="1" stroke-linejoin="round"/>'
				. '<circle cx="14" cy="32" r="4.3" fill="#1A1F2E"/><circle cx="14" cy="32" r="1.7" fill="#C9D3DC"/>'
				. '<circle cx="35" cy="32" r="4.3" fill="#1A1F2E"/><circle cx="35" cy="32" r="1.7" fill="#C9D3DC"/>';
			break;
		default:
			return alp_icon( $name, 22 );
	}
	return sprintf(
		'<span class="alp-icon3d" aria-hidden="true"><svg width="%1$d" height="%1$d" viewBox="0 0 48 48" focusable="false">%2$s%3$s</svg></span>',
		(int) $size,
		$defs,
		$body
	);
}

/**
 * Shortcodes aus dem alten Child-Theme, deren Funktionen bewusst weggefallen sind
 * (Glücksrad, Kundenwünsche). Sie geben nichts aus, statt als Rohtext „[alp_…]“ auf der Seite zu stehen.
 */
add_action( 'init', 'alp_retired_shortcodes', 20 );
function alp_retired_shortcodes() {
	foreach ( array( 'alp_gluecksrad', 'alp_wunsch_formular', 'alp_wunschliste' ) as $tag ) {
		if ( ! shortcode_exists( $tag ) ) {
			add_shortcode( $tag, '__return_empty_string' );
		}
	}
}

/**
 * Abgeschaltete Seiten (config.php → 'retired_pages'): leiten dauerhaft (301) weiter
 * und verschwinden automatisch aus allen Menüs – ohne die Seiten löschen zu müssen.
 */
function alp_retired_page_target( $slug ) {
	$pages = (array) alp_config( 'retired_pages', array() );
	return isset( $pages[ $slug ] ) ? $pages[ $slug ] : null;
}

add_action( 'template_redirect', 'alp_redirect_retired_pages', 1 );
function alp_redirect_retired_pages() {
	if ( ! is_page() ) {
		return;
	}
	$target = alp_retired_page_target( get_post_field( 'post_name', get_queried_object_id() ) );
	if ( null !== $target ) {
		wp_safe_redirect( alp_link( $target ), 301 );
		exit;
	}
}

add_filter( 'wp_nav_menu_objects', 'alp_hide_retired_menu_items' );
function alp_hide_retired_menu_items( $items ) {
	$retired = array_keys( (array) alp_config( 'retired_pages', array() ) );
	if ( ! $retired ) {
		return $items;
	}
	return array_filter(
		$items,
		function ( $item ) use ( $retired ) {
			if ( 'page' === ( $item->object ?? '' ) && in_array( get_post_field( 'post_name', (int) $item->object_id ), $retired, true ) ) {
				return false;
			}
			$path = trim( (string) wp_parse_url( (string) $item->url, PHP_URL_PATH ), '/' );
			return ! in_array( $path, $retired, true );
		}
	);
}
