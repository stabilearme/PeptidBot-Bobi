<?php
/**
 * Minimaler WordPress/WooCommerce-Ersatz, damit die ECHTEN Template-Teile des
 * Child-Themes ohne WordPress statisch gerendert werden können.
 * Nur für die Vorschau – nichts davon gehört ins Theme.
 */

define( 'ABSPATH', __DIR__ . '/' );

/* ---------- Seitenkontext (wird vom Build pro Seite gesetzt) ---------- */
$GLOBALS['alp_ctx'] = array( 'page' => 'home', 'product' => null );

function alp_preview_ctx( $page, $product = null, $slug = '' ) {
	$GLOBALS['alp_ctx'] = array( 'page' => $page, 'product' => $product, 'slug' => $slug );
	$GLOBALS['product'] = $product;
}

function is_front_page() { return 'home' === $GLOBALS['alp_ctx']['page']; }
function is_product() { return 'product' === $GLOBALS['alp_ctx']['page']; }
function alp_preview_slug() { return $GLOBALS['alp_ctx']['slug'] ?? ''; }
function is_page( $slug = '' ) {
	$page = $GLOBALS['alp_ctx']['page'];
	if ( 'coa' === $page ) {
		return ! $slug || 'coa' === $slug;
	}
	return 'page' === $page && ( ! $slug || alp_preview_slug() === $slug );
}
function is_woocommerce() { return in_array( $GLOBALS['alp_ctx']['page'], array( 'product', 'shop' ), true ); }
function is_shop() { return 'shop' === $GLOBALS['alp_ctx']['page']; }
function is_product_category() { return false; }
function is_cart() { return 'warenkorb' === alp_preview_slug(); }
function is_checkout() { return 'kasse' === alp_preview_slug(); }
function is_account_page() { return 'mein-konto' === alp_preview_slug(); }
function in_the_loop() { return true; }
function have_posts() { return false; }
function the_post() {}
function get_the_title() { return 'Laborergebnisse & COAs'; }
function get_queried_object_id() { return 0; }
function is_single() { return 'post' === $GLOBALS['alp_ctx']['page']; }
function get_the_ID() { return 0; }
function get_post_field( $field, $id = 0 ) { return 'post_name' === $field ? preg_replace( '/^artikel-/', '', alp_preview_slug() ) : ''; }

/* ---------- Hooks: No-ops (die Vorschau ruft die Theme-Funktionen direkt auf) ---------- */
function add_action() {}
function add_filter() {}
function remove_action() {}
function add_shortcode() {}
function do_shortcode() { return ''; } // [products] wird im Build durch Kacheln ersetzt
function do_action() {}
function apply_filters( $tag, $value ) { return $value; }

/* ---------- Escaping (wie WordPress: keine doppelte Kodierung) ---------- */
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8', false ); }
function esc_attr( $s ) { return esc_html( $s ); }
function esc_url( $s ) { return esc_html( $s ); }
function esc_html__( $s ) { return esc_html( $s ); }
function esc_html_e( $s ) { echo esc_html( $s ); }
function wp_kses( $s ) { return (string) $s; }
function wp_kses_post( $s ) { return (string) $s; }
function wp_strip_all_tags( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_file_name( $s ) { return preg_replace( '/[^a-z0-9._-]/i', '', $s ); }
function sanitize_html_class( $s ) { return preg_replace( '/[^a-z0-9_-]/i', '', $s ); }
function sanitize_text_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function wp_unslash( $s ) { return $s; }
function untrailingslashit( $s ) { return rtrim( $s, '/' ); }
function is_ssl() { return true; }
function wp_parse_url( $u, $c = -1 ) { return parse_url( $u, $c ); }
function is_wp_error( $x ) { return false; }
function wp_date( $f ) { return date( $f ); }
function get_option( $k ) { return 0; }
function get_theme_mod( $k ) { return 0; }
function get_bloginfo() { return 'AminoLabs Pro'; }
function wp_attachment_is_image() { return false; }

function number_format_i18n( $n, $d = 0 ) { return number_format( (float) $n, $d, ',', '.' ); }
function wp_parse_args( $a, $d ) { return array_merge( $d, (array) $a ); }
function shortcode_atts( $d, $a ) { return array_merge( $d, array_intersect_key( (array) $a, $d ) ); }
function wp_list_pluck( $list, $field ) { return array_map( fn( $r ) => $r[ $field ], array_values( $list ) ); }
function wp_unique_id( $prefix = '' ) { static $i = 0; return $prefix . ( ++$i ); }

/* ---------- Theme-Pfade ---------- */
function get_stylesheet_directory() { return ALP_PREVIEW_THEME; }
function get_stylesheet_directory_uri() { return '.'; }

function get_template_part( $slug, $name = null, $args = array() ) {
	$file = ALP_PREVIEW_THEME . '/' . $slug . '.php';
	if ( file_exists( $file ) ) {
		( static function () use ( $file, $args ) {
			include $file;
		} )();
	}
}

/* ---------- Links: statische Vorschau-Seiten statt WordPress-URLs ---------- */

/** Seiten, die es in der Vorschau gibt (Slug → Datei). */
function alp_preview_pages() {
	static $pages = null;
	if ( null === $pages ) {
		$pages = array(
			'coa'        => 'coa.html',
			'shop'       => 'shop.html',
			'warenkorb'  => 'warenkorb.html',
			'kasse'      => 'kasse.html',
			'mein-konto' => 'mein-konto.html',
		);
		foreach ( glob( ALP_PREVIEW_DATA . '/pages/*.json' ) as $f ) {
			$pages[ basename( $f, '.json' ) ] = basename( $f, '.json' ) . '.html';
		}
	}
	return $pages;
}

function home_url( $path = '/' ) {
	$full  = '/' . ltrim( (string) $path, '/' );
	$hash  = '';
	if ( false !== ( $i = strpos( $full, '#' ) ) ) {
		$hash = substr( $full, $i );
		$full = substr( $full, 0, $i );
	}
	$path = strtok( $full, '?' );
	if ( '/' === $path ) {
		return 'index.html' . $hash;
	}
	if ( 0 === strpos( $path, '/wp-content/uploads/' ) ) {
		$file = basename( $path );
		foreach ( array( 'coa', 'products', 'media' ) as $dir ) {
			if ( file_exists( ALP_PREVIEW_OUT . "/img/$dir/$file" ) ) {
				return "img/$dir/$file";
			}
		}
		return 'https://aminolabspro.com' . $full;
	}
	$parts = array_values( array_filter( explode( '/', $path ) ) );
	$pages = alp_preview_pages();
	if ( 1 === count( $parts ) && isset( $pages[ $parts[0] ] ) ) {
		return $pages[ $parts[0] ] . $hash;
	}
	// Artikel, die ohne Datum verlinkt sind (WordPress leitet /slug/ auf den Beitrag weiter).
	if ( 1 === count( $parts ) && file_exists( ALP_PREVIEW_DATA . '/posts/' . $parts[0] . '.json' ) ) {
		return 'artikel-' . $parts[0] . '.html' . $hash;
	}
	if ( 2 === count( $parts ) && 'produkt' === $parts[0] && file_exists( ALP_PREVIEW_DATA . '/products.json' ) ) {
		foreach ( alp_preview_products() as $p ) {
			if ( $p->get_slug() === $parts[1] ) {
				return $p->get_permalink() . $hash;
			}
		}
	}
	if ( 2 === count( $parts ) && 'produkt-kategorie' === $parts[0] ) {
		return 'shop.html#kategorie-' . $parts[1];
	}
	// Blogartikel: /2026/09/10/slug/
	if ( 4 === count( $parts ) && ctype_digit( $parts[0] ) && file_exists( ALP_PREVIEW_DATA . '/posts/' . $parts[3] . '.json' ) ) {
		return 'artikel-' . $parts[3] . '.html' . $hash;
	}
	// Alles andere gibt es nur im Live-Shop.
	return 'https://aminolabspro.com' . $full . $hash;
}

/** Schreibt Links auf aminolabspro.com (absolut oder ab „/“) auf die Vorschau-Seiten um. */
function alp_preview_localize( $html ) {
	$html = preg_replace( '/\s(?:srcset|sizes)="[^"]*"/', '', $html );
	return preg_replace_callback(
		'#(\s(?:href|src|action)=)(["\'])(?:https?://(?:www\.)?aminolabspro\.com)?(/[^"\'\s]*)?\2#',
		function ( $m ) {
			if ( ! isset( $m[3] ) || '' === $m[3] ) {
				return $m[0] === $m[1] . $m[2] . $m[2] ? $m[0] : $m[1] . $m[2] . 'index.html' . $m[2];
			}
			if ( 0 === strpos( $m[3], '//' ) ) {
				return $m[0];
			}
			return $m[1] . $m[2] . home_url( $m[3] ) . $m[2];
		},
		$html
	);
}

function wc_get_page_permalink( $page ) {
	$map = array(
		'shop'      => 'shop.html',
		'cart'      => 'warenkorb.html',
		'checkout'  => 'kasse.html',
		'myaccount' => 'mein-konto.html',
	);
	return $map[ $page ] ?? 'index.html';
}
function wc_get_cart_url() { return 'warenkorb.html'; }
function wc_get_checkout_url() { return 'kasse.html'; }

function wc_price( $amount ) {
	return '<span class="woocommerce-Price-amount amount"><bdi>' . number_format_i18n( $amount, 2 ) . '&nbsp;<span class="woocommerce-Price-currencySymbol">€</span></bdi></span>';
}

/* ---------- Beispiel-Warenkorb (nur Vorschau) ---------- */
function alp_preview_cart_items() {
	return array( array( 'ALP-BPC157-10', 1 ), array( 'ALP-BAC-10ML', 1 ) );
}

class ALP_Preview_Cart {
	public function lines() {
		$out = array();
		foreach ( alp_preview_cart_items() as $row ) {
			$p     = alp_preview_products()[ wc_get_product_id_by_sku( $row[0] ) ];
			$out[] = array( $p, $row[1], (float) $p->data['price'] * $row[1] );
		}
		return $out;
	}
	public function get_cart_contents_count() { return array_sum( array_column( alp_preview_cart_items(), 1 ) ); }
	public function get_displayed_subtotal() { return array_sum( array_column( $this->lines(), 2 ) ); }
}

function WC() {
	static $wc = null;
	if ( null === $wc ) {
		$wc = (object) array( 'cart' => new ALP_Preview_Cart() );
	}
	return $wc;
}

/* ---------- Produkte & Kategorien aus products.json ---------- */
function alp_preview_products() {
	static $list = null;
	if ( null === $list ) {
		$list = array();
		foreach ( json_decode( file_get_contents( ALP_PREVIEW_DATA . '/products.json' ), true ) as $row ) {
			$list[ $row['id'] ] = new WC_Product( $row );
		}
	}
	return $list;
}

function wc_get_product_id_by_sku( $sku ) {
	foreach ( alp_preview_products() as $p ) {
		if ( 0 === strcasecmp( $p->get_sku(), $sku ) ) {
			return $p->get_id();
		}
	}
	return 0;
}

function get_permalink( $id ) {
	$p = alp_preview_products()[ $id ] ?? null;
	return $p ? $p->get_permalink() : '';
}

function get_terms( $args = array() ) {
	$terms = array();
	foreach ( alp_preview_products() as $p ) {
		foreach ( $p->data['categories'] as $c ) {
			if ( ! isset( $terms[ $c['slug'] ] ) ) {
				$terms[ $c['slug'] ] = (object) array( 'term_id' => $c['id'], 'slug' => $c['slug'], 'name' => $c['name'], 'count' => 0 );
			}
			$terms[ $c['slug'] ]->count++;
		}
	}
	$terms = array_values( $terms );
	usort( $terms, fn( $a, $b ) => $b->count <=> $a->count ?: strcmp( $a->name, $b->name ) );
	if ( ! empty( $args['number'] ) ) {
		$terms = array_slice( $terms, 0, (int) $args['number'] );
	}
	return $terms;
}

function get_term_link( $term ) {
	return 'shop.html#kategorie-' . $term->slug;
}

function wp_get_attachment_image( $file, $size = 'thumbnail', $icon = false, $attr = array() ) {
	return sprintf( '<img src="img/products/%s" alt="%s" loading="lazy" width="150" height="150">', esc_attr( $file ), esc_attr( $attr['alt'] ?? '' ) );
}

class WooCommerce {}

class WC_Product {
	public $data;

	public function __construct( $data ) { $this->data = $data; }
	public function get_id() { return $this->data['id']; }
	public function get_sku() { return $this->data['sku']; }
	public function get_name() { return $this->data['name']; }
	public function get_slug() { return $this->data['slug']; }
	public function get_price_html() { return $this->data['price_html']; }
	public function get_image_id() { return $this->data['images'][0]['file'] ?? ''; }
	public function is_type( $t ) { return $this->data['type'] === $t; }
	public function is_in_stock() { return 'outofstock' !== $this->data['stock_status']; }
	public function is_purchasable() { return true; }
	public function is_on_sale() { return (bool) $this->data['on_sale']; }
	public function get_permalink() { return 'produkt-' . $this->data['slug'] . '.html'; }
}
