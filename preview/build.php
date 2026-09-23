<?php
/**
 * Baut eine klickbare, statische Vorschau des Child-Themes „AminoLabs Pro 2“.
 *
 *   php preview/build.php
 *
 * Gerendert werden die echten Template-Teile aus theme/aminolabspro-pro/ – nur der
 * Flatsome-Rahmen (Header, Produktkacheln, Produktgalerie) wird hier nachgebaut,
 * weil Flatsome und WooCommerce in der Vorschau nicht laufen.
 * Ausgabe: preview/site/ (index.html, shop.html, coa.html, produkt-*.html)
 */

define( 'ALP_PREVIEW_THEME', dirname( __DIR__ ) . '/theme/aminolabspro-pro' );
define( 'ALP_PREVIEW_DATA', __DIR__ . '/data' );
define( 'ALP_PREVIEW_OUT', __DIR__ . '/site' );

require __DIR__ . '/lib/wp-stubs.php';
require ALP_PREVIEW_THEME . '/functions.php';

/* Produkt, auf das „Produktseite“ in der Vorschau-Leiste verlinkt (Slug). */
const ALP_PREVIEW_FEATURED = 'bpc-157-10mg';

/* ---------------------------------------------------------------------
 * Flatsome-Nachbau
 * ------------------------------------------------------------------- */

function pv_price_extra() {
	return '<p class="wc-gzd-additional-info tax-info">inkl. MwSt.</p><p class="wc-gzd-additional-info shipping-costs-info">zzgl. <a href="https://aminolabspro.com/versandarten/">Versandkosten</a></p>';
}

function pv_sale_badge( WC_Product $p ) {
	if ( ! $p->is_on_sale() || ! (float) $p->data['regular_price'] ) {
		return '';
	}
	$pct = round( ( 1 - (float) $p->data['sale_price'] / (float) $p->data['regular_price'] ) * 100 );
	return '<div class="badge-container absolute left top z-1"><div class="callout badge badge-square"><div class="badge-inner secondary on-sale"><span class="onsale">-' . (int) $pct . '%</span></div></div></div>';
}

function pv_card( WC_Product $p ) {
	$prev               = $GLOBALS['product'] ?? null;
	$GLOBALS['product'] = $p;
	$img                = $p->data['images'][0] ?? null;
	$cat                = html_entity_decode( $p->data['categories'][0]['name'] ?? '' );
	ob_start();
	?>
	<div class="product-small col has-hover product" data-cats="<?php echo esc_attr( implode( ' ', array_map( fn( $c ) => 'kategorie-' . $c['slug'], $p->data['categories'] ) ) ); ?>">
		<div class="col-inner">
			<div class="product-small box">
				<div class="box-image">
					<?php echo pv_sale_badge( $p ); ?>
					<a href="<?php echo esc_url( $p->get_permalink() ); ?>" aria-label="<?php echo esc_attr( $p->get_name() ); ?>">
						<?php if ( $img ) : ?>
							<img src="img/products/<?php echo esc_attr( $img['file'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ); ?>" width="768" height="768" loading="lazy">
						<?php endif; ?>
					</a>
				</div>
				<div class="box-text box-text-products">
					<div class="title-wrapper">
						<p class="category uppercase is-smaller no-text-overflow product-cat op-7"><?php echo esc_html( $cat ); ?></p>
						<p class="name product-title woocommerce-loop-product__title"><a href="<?php echo esc_url( $p->get_permalink() ); ?>"><?php echo esc_html( $p->get_name() ); ?></a></p>
					</div>
					<?php alp_loop_lab_badge(); ?>
					<div class="price-wrapper">
						<span class="price"><?php echo $p->get_price_html(); ?></span>
						<?php echo pv_price_extra(); ?>
					</div>
					<div class="add-to-cart-button"><a href="<?php echo esc_url( $p->get_permalink() ); ?>" class="primary is-small mb-0 button product_type_simple add_to_cart_button is-flat" data-pv-cart>In den Warenkorb</a></div>
				</div>
			</div>
		</div>
	</div>
	<?php
	$GLOBALS['product'] = $prev;
	return ob_get_clean();
}

function pv_grid( array $products, $cols = 4 ) {
	$out = '<div class="woocommerce columns-' . (int) $cols . '"><div class="products row row-small large-columns-' . (int) $cols . ' medium-columns-3 small-columns-2">';
	foreach ( $products as $p ) {
		$out .= pv_card( $p );
	}
	return $out . '</div></div>';
}

function pv_bestsellers( $limit ) {
	$list = array_values( alp_preview_products() );
	usort( $list, fn( $a, $b ) => $b->data['total_sales'] <=> $a->data['total_sales'] );
	return array_slice( $list, 0, $limit );
}

function pv_header() {
	$nav = array(
		'Shop'           => 'shop.html',
		'Laborergebnisse' => 'coa.html',
		'Wissen'         => 'https://aminolabspro.com/wissen/',
		'Rechner'        => 'https://aminolabspro.com/dosierungsrechner/',
		'Kontakt'        => 'https://aminolabspro.com/contakt/',
	);
	$page = $GLOBALS['alp_ctx']['page'];
	ob_start();
	?>
	<header id="header" class="header has-sticky sticky-jump">
		<div class="header-wrapper">
			<?php alp_render_announcement(); ?>
			<div id="masthead" class="header-main">
				<div class="header-inner flex-row container logo-left">
					<div class="flex-col show-for-medium flex-left">
						<ul class="mobile-nav nav nav-left">
							<li class="nav-icon"><a href="#alp-search" data-alp-open-search aria-label="Menü"><?php echo alp_icon( 'grid', 22 ); ?></a></li>
						</ul>
					</div>
					<div id="logo" class="flex-col logo">
						<a href="index.html" title="AminoLabs Pro – Startseite" rel="home"><img width="260" height="60" src="img/brand/aminolabspro-logo_4.svg" class="header_logo header-logo" alt="AminoLabs Pro"></a>
					</div>
					<div class="flex-col hide-for-medium flex-left flex-grow">
						<ul class="header-nav header-nav-main nav nav-left">
							<?php foreach ( $nav as $label => $href ) : ?>
								<?php $active = ( 'shop.html' === $href && in_array( $page, array( 'shop', 'product' ), true ) ) || ( 'coa.html' === $href && 'coa' === $page ); ?>
								<li class="menu-item<?php echo $active ? ' active' : ''; ?>"><a href="<?php echo esc_url( $href ); ?>" class="nav-top-link"><?php echo esc_html( $label ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<div class="flex-col hide-for-medium flex-right">
						<ul class="header-nav header-nav-main nav nav-right">
							<li class="header-search"><a href="#alp-search" data-alp-open-search aria-label="Suche"><?php echo alp_icon( 'search', 21 ); ?></a></li>
							<li class="account-item"><a href="https://aminolabspro.com/mein-konto/" aria-label="Mein Konto"><?php echo alp_icon( 'user', 21 ); ?></a></li>
							<li class="cart-item"><a href="#" class="header-cart-link" data-pv-cart aria-label="Warenkorb"><span class="header-cart-title">0,00&nbsp;€</span> <span class="cart-icon image-icon"><strong>0</strong></span></a></li>
						</ul>
					</div>
					<div class="flex-col show-for-medium flex-right">
						<ul class="mobile-nav nav nav-right">
							<li class="cart-item"><a href="#" data-pv-cart aria-label="Warenkorb"><span class="cart-icon image-icon"><strong>0</strong></span></a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</header>
	<?php
	return ob_get_clean();
}

function pv_preview_bar( $page ) {
	$links = array(
		'home'    => array( 'Startseite', 'index.html' ),
		'shop'    => array( 'Shop', 'shop.html' ),
		'product' => array( 'Produktseite', 'produkt-' . ALP_PREVIEW_FEATURED . '.html' ),
		'coa'     => array( 'COA-Seite', 'coa.html' ),
	);
	$out = '<nav class="pv-bar" aria-label="Vorschau-Seiten"><span class="pv-bar__tag">Theme-Vorschau</span>';
	foreach ( $links as $key => $l ) {
		$out .= sprintf( '<a href="%s"%s>%s</a>', $l[1], $key === $page ? ' class="is-current" aria-current="page"' : '', $l[0] );
	}
	return $out . '</nav>';
}

function pv_page( $file, $page, $title, $body_class, $content, $product = null ) {
	alp_preview_ctx( $page, $product );

	$styles = '<link rel="stylesheet" href="flatsome-shim.css">' . "\n";
	foreach ( alp_styles() as $name => $when ) {
		if ( alp_style_needed( $when ) ) {
			$styles .= '<link rel="stylesheet" href="assets/css/' . $name . '.css">' . "\n";
		}
	}
	$styles .= '<link rel="stylesheet" href="preview.css">' . "\n";

	$classes = array_merge( array( 'alp' ), $body_class, array( 'alp-has-bottom-nav' ) );
	if ( 'product' === $page ) {
		$classes[] = 'alp-has-sticky-buy';
	}

	ob_start();
	alp_preload_fonts();
	$head = ob_get_clean();

	ob_start();
	alp_render_footer();
	$footer = ob_get_clean();

	ob_start();
	if ( 'product' === $page ) {
		alp_part( 'product/sticky-bar', array( 'product' => $product ) );
	}
	alp_render_mobile_nav();
	$after = ob_get_clean();

	$scripts = '<script src="assets/js/theme.js" defer></script>' . "\n";
	if ( alp_style_needed( 'coa' ) ) {
		$scripts = '<script>var ALP_COA = ' . json_encode( alp_coa_public_data(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . ';</script>' . "\n" . $scripts;
		$scripts .= '<script src="assets/js/coa.js" defer></script>' . "\n";
	}
	$scripts .= '<script src="preview.js" defer></script>' . "\n";

	$html = '<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>' . esc_html( $title ) . ' – AminoLabs Pro (Vorschau)</title>
<meta name="robots" content="noindex">
<link rel="icon" href="img/brand/aminolabspro-logo_4.svg" type="image/svg+xml">
' . $head . $styles . '</head>
<body class="' . esc_attr( implode( ' ', $classes ) ) . '">
' . pv_preview_bar( $page ) . '
<div id="wrapper">
' . pv_header() . '
<main id="main">
' . $content . '
</main>
<footer id="footer" class="footer-wrapper">
' . $footer . '
</footer>
</div>
' . $after . $scripts . '</body>
</html>
';

	// Aktiven Punkt der Mobile-Navigation markieren (in WordPress über die aktuelle URL).
	$current = array( 'home' => 'index.html', 'shop' => 'shop.html', 'coa' => 'coa.html' )[ $page ] ?? null;
	if ( $current ) {
		$html = str_replace( 'class="alp-bottom-nav__item" href="' . $current . '"', 'class="alp-bottom-nav__item is-current" href="' . $current . '" aria-current="page"', $html );
	}

	file_put_contents( ALP_PREVIEW_OUT . '/' . $file, $html );
	echo "  {$file}\n";
}

/* ---------------------------------------------------------------------
 * Seiten
 * ------------------------------------------------------------------- */

function pv_render_home() {
	alp_preview_ctx( 'home' );
	ob_start();
	echo '<div id="alp-home" class="alp-home">';
	foreach ( array( 'home/hero', 'home/trust', 'home/categories', 'home/products', 'home/coa', 'home/process', 'home/knowledge', 'home/faq', 'home/newsletter' ) as $section ) {
		if ( 'home/products' === $section ) {
			// Wie im Theme – nur dass der [products]-Shortcode hier durch nachgebaute Flatsome-Kacheln ersetzt wird.
			$cfg  = (array) alp_config( 'sections.products', array() );
			$html = alp_capture( 'home/products' );
			$html = preg_replace( '#(<div class="alp-products">)\s*(</div>)#', '$1' . pv_grid( pv_bestsellers( (int) ( $cfg['limit'] ?? 8 ) ) ) . '$2', $html );
			echo $html;
			continue;
		}
		alp_part( $section );
	}
	echo '</div>';
	pv_page( 'index.html', 'home', 'Startseite', array( 'home', 'page' ), ob_get_clean() );
}

function alp_capture( $slug, $args = array() ) {
	ob_start();
	alp_part( $slug, $args );
	return ob_get_clean();
}

function pv_render_shop() {
	alp_preview_ctx( 'shop' );
	$products = array_values( alp_preview_products() );
	usort( $products, fn( $a, $b ) => $b->data['total_sales'] <=> $a->data['total_sales'] );
	ob_start();
	?>
	<div class="shop-page-title category-page-title page-title">
		<div class="page-title-inner flex-row medium-flex-wrap container">
			<div class="flex-col flex-grow medium-text-center">
				<nav class="woocommerce-breadcrumb breadcrumbs"><a href="index.html">Startseite</a> <span class="divider">/</span> Shop</nav>
				<h1 class="shop-page-title is-xlarge">Shop</h1>
			</div>
			<div class="flex-col medium-text-center"><p class="woocommerce-result-count"><?php echo count( $products ); ?> Ergebnisse</p></div>
		</div>
	</div>
	<div class="row category-page-row">
		<div class="col large-12">
			<div class="shop-container">
				<?php alp_part( 'shop/category-pills' ); ?>
				<div data-pv-shop>
					<?php echo pv_grid( $products ); ?>
				</div>
			</div>
		</div>
	</div>
	<?php
	pv_page( 'shop.html', 'shop', 'Shop', array( 'woocommerce', 'archive', 'post-type-archive-product' ), ob_get_clean() );
}

function pv_related( WC_Product $p ) {
	$cats = array_column( $p->data['categories'], 'slug' );
	$same = $other = array();
	foreach ( pv_bestsellers( 99 ) as $q ) {
		if ( $q->get_id() === $p->get_id() ) {
			continue;
		}
		if ( array_intersect( $cats, array_column( $q->data['categories'], 'slug' ) ) ) {
			$same[] = $q;
		} else {
			$other[] = $q;
		}
	}
	return array_slice( array_merge( $same, $other ), 0, 4 );
}

function pv_render_product( WC_Product $p ) {
	alp_preview_ctx( 'product', $p );
	$cat = $p->data['categories'][0] ?? null;
	ob_start();
	?>
	<div class="shop-container">
		<div class="product type-product">
			<div class="product-container">
				<div class="product-main">
					<div class="row content-row mb-0">
						<div class="product-gallery large-6 col">
							<div class="product-images relative mb-half has-hover woocommerce-product-gallery">
								<?php echo pv_sale_badge( $p ); ?>
								<div class="woocommerce-product-gallery__wrapper">
									<?php foreach ( $p->data['images'] as $i => $img ) : ?>
										<div class="woocommerce-product-gallery__image slide<?php echo 0 === $i ? ' is-selected' : ''; ?>" data-pv-slide="<?php echo (int) $i; ?>"<?php echo 0 === $i ? '' : ' hidden'; ?>>
											<a href="img/products/<?php echo esc_attr( $img['file'] ); ?>" class="lightbox"><img src="img/products/<?php echo esc_attr( $img['file'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ); ?>" width="768" height="768"></a>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
							<?php if ( count( $p->data['images'] ) > 1 ) : ?>
								<div class="product-thumbnails thumbnails row row-small small-columns-4">
									<?php foreach ( $p->data['images'] as $i => $img ) : ?>
										<div class="col<?php echo 0 === $i ? ' is-nav-selected' : ''; ?>"><a href="#" data-pv-thumb="<?php echo (int) $i; ?>"><img src="img/products/<?php echo esc_attr( $img['file'] ); ?>" alt="" width="150" height="150"></a></div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="product-info summary col-fit col entry-summary product-summary">
							<nav class="woocommerce-breadcrumb breadcrumbs"><a href="index.html">Startseite</a> <span class="divider">/</span> <a href="shop.html">Shop</a><?php if ( $cat ) : ?> <span class="divider">/</span> <a href="shop.html#kategorie-<?php echo esc_attr( $cat['slug'] ); ?>"><?php echo esc_html( html_entity_decode( $cat['name'] ) ); ?></a><?php endif; ?></nav>
							<h1 class="product-title product_title entry-title"><?php echo esc_html( $p->get_name() ); ?></h1>
							<div class="is-divider small"></div>
							<?php alp_product_chips(); ?>
							<div class="price-wrapper"><p class="price product-page-price<?php echo $p->is_on_sale() ? ' price-on-sale' : ''; ?>"><?php echo $p->get_price_html(); ?></p></div>
							<div class="legal-price-info"><p class="wc-gzd-additional-info"><span class="wc-gzd-additional-info tax-info">inkl. 19 % MwSt.</span> <span class="wc-gzd-additional-info shipping-costs-info">zzgl. <a href="https://aminolabspro.com/versandarten/">Versandkosten</a></span></p></div>
							<?php if ( trim( strip_tags( $p->data['short_description'] ) ) ) : ?>
								<div class="product-short-description"><?php echo $p->data['short_description']; ?></div>
							<?php endif; ?>
							<?php alp_shipping_countdown(); ?>
							<form class="cart" action="#" method="post" data-pv-cart-form>
								<div class="quantity buttons_added">
									<input type="button" value="-" class="minus button is-form" aria-label="Menge verringern">
									<label class="screen-reader-text" for="qty">Menge</label>
									<input type="number" id="qty" class="input-text qty text" value="1" min="1" step="1" inputmode="numeric">
									<input type="button" value="+" class="plus button is-form" aria-label="Menge erhöhen">
								</div>
								<button type="submit" class="single_add_to_cart_button button alt">In den Warenkorb</button>
							</form>
							<?php alp_product_coa_box(); ?>
							<?php alp_product_trust(); ?>
						</div>
					</div>
				</div>

				<div class="product-footer">
					<div class="container">
						<div class="woocommerce-tabs wc-tabs-wrapper container tabbed-content">
							<ul class="tabs wc-tabs product-tabs small-nav-collapse nav nav-line nav-left" role="tablist">
								<li class="description_tab active"><a href="#tab-description">Beschreibung</a></li>
							</ul>
							<div class="tab-panels">
								<div class="woocommerce-Tabs-panel panel entry-content active" id="tab-description">
									<?php echo alp_clean_product_description( $p->data['description'] ); ?>
								</div>
							</div>
						</div>
						<div class="related related-products-wrapper product-section">
							<h3 class="product-section-title container-width product-section-title-related">Ähnliche Produkte</h3>
							<?php echo pv_grid( pv_related( $p ) ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	pv_page( $p->get_permalink(), 'product', $p->get_name(), array( 'product-template-default', 'single', 'single-product', 'woocommerce' ), ob_get_clean(), $p );
}

function pv_render_coa() {
	alp_preview_ctx( 'coa' );
	ob_start();
	( static function () {
		include ALP_PREVIEW_THEME . '/page-coa.php';
	} )();
	pv_page( 'coa.html', 'coa', 'Laborergebnisse & COAs', array( 'page', 'page-template-default', 'alp-page-coa' ), ob_get_clean() );
}

/* get_header()/get_footer() in page-coa.php: Rahmen kommt aus pv_page(). */
function get_header() {}
function get_footer() {}

/* ---------------------------------------------------------------------
 * Build
 * ------------------------------------------------------------------- */

function pv_copy_dir( $from, $to ) {
	@mkdir( $to, 0777, true );
	foreach ( scandir( $from ) as $f ) {
		if ( '.' === $f[0] ) {
			continue;
		}
		is_dir( "$from/$f" ) ? pv_copy_dir( "$from/$f", "$to/$f" ) : copy( "$from/$f", "$to/$f" );
	}
}

@mkdir( ALP_PREVIEW_OUT, 0777, true );
foreach ( glob( ALP_PREVIEW_OUT . '/*.html' ) as $old ) { // inkl. desktop.html, wird unten neu kopiert
	unlink( $old );
}
pv_copy_dir( ALP_PREVIEW_THEME . '/assets', ALP_PREVIEW_OUT . '/assets' );
foreach ( array( 'flatsome-shim.css', 'preview.css', 'preview.js', 'desktop.html' ) as $f ) {
	copy( __DIR__ . '/lib/' . $f, ALP_PREVIEW_OUT . '/' . $f );
}

echo "Vorschau wird gebaut:\n";
pv_render_home();
pv_render_shop();
pv_render_coa();
foreach ( alp_preview_products() as $p ) {
	pv_render_product( $p );
}
echo "Fertig → preview/site/index.html\n";
