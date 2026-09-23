<?php
/**
 * COA-Center: Chargendaten, Chargen-Prüfer, Zertifikatsbox auf Produktseiten.
 * Daten pflegen: /data/coa-batches.php
 *
 * Shortcodes für beliebige Seiten:
 *   [alp_coa_lookup]  – Eingabefeld „Charge prüfen“
 *   [alp_coa_grid]    – Übersicht aller Zertifikate
 */

defined( 'ABSPATH' ) || exit;

/**
 * Alle Chargen, bereinigt und mit vollständigen URLs.
 */
function alp_coa_batches() {
	static $batches = null;
	if ( null !== $batches ) {
		return $batches;
	}
	$batches = array();
	foreach ( (array) alp_data( 'coa-batches' ) as $row ) {
		if ( empty( $row['batch'] ) ) {
			continue;
		}
		$row = wp_parse_args(
			$row,
			array(
				'sku'      => '',
				'product'  => '',
				'status'   => 'pending',
				'content'  => null,
				'purity'   => null,
				'report'   => '',
				'cert'     => '',
				'lab'      => '',
				'tested'   => '',
				'photo'    => '',
				'file'     => '',
				'expected' => '',
			)
		);
		$row['batch'] = strtoupper( trim( $row['batch'] ) );
		$row['photo'] = $row['photo'] ? alp_link( $row['photo'] ) : '';
		$row['file']  = $row['file'] ? alp_link( $row['file'] ) : '';
		$row['done']  = 'done' === $row['status'];
		$batches[]    = $row;
	}
	return $batches;
}

function alp_coa_find( $batch ) {
	$batch = strtoupper( trim( (string) $batch ) );
	foreach ( alp_coa_batches() as $row ) {
		if ( $row['batch'] === $batch ) {
			return $row;
		}
	}
	return null;
}

/**
 * Neueste Charge zu einer Artikelnummer (erste Übereinstimmung in der Datei).
 */
function alp_coa_for_sku( $sku ) {
	if ( ! $sku ) {
		return null;
	}
	foreach ( alp_coa_batches() as $row ) {
		if ( 0 === strcasecmp( $row['sku'], $sku ) ) {
			return $row;
		}
	}
	return null;
}

/**
 * Produkt-URL zu einer Artikelnummer (für „Zum Produkt“-Links).
 */
function alp_coa_product_url( $sku ) {
	if ( ! $sku || ! function_exists( 'wc_get_product_id_by_sku' ) ) {
		return '';
	}
	$id = wc_get_product_id_by_sku( $sku );
	return $id ? get_permalink( $id ) : '';
}

/**
 * Daten für den Chargen-Prüfer im Browser (assets/js/coa.js).
 */
function alp_coa_public_data() {
	$list = array();
	foreach ( alp_coa_batches() as $row ) {
		$list[ $row['batch'] ] = array(
			'product'  => $row['product'],
			'done'     => $row['done'],
			'content'  => null !== $row['content'] ? alp_num( $row['content'] ) . ' mg' : '',
			'purity'   => null !== $row['purity'] ? alp_num( $row['purity'], 0 ) : '',
			'lab'      => $row['lab'],
			'tested'   => $row['tested'],
			'report'   => $row['report'],
			'cert'     => $row['cert'],
			'file'     => $row['file'],
			'expected' => $row['expected'],
			'url'      => alp_coa_product_url( $row['sku'] ),
		);
	}
	return array(
		'batches' => $list,
		'coaUrl'  => alp_link( '/coa/' ),
	);
}

/* ---------- Shortcodes ---------- */

add_shortcode( 'alp_coa_lookup', 'alp_coa_lookup_shortcode' );
function alp_coa_lookup_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'tone' => 'light' ), $atts );
	ob_start();
	alp_part( 'coa/lookup', array( 'tone' => $atts['tone'] ) );
	return ob_get_clean();
}

add_shortcode( 'alp_coa_grid', 'alp_coa_grid_shortcode' );
function alp_coa_grid_shortcode() {
	ob_start();
	alp_part( 'coa/grid' );
	return ob_get_clean();
}

/* ---------- Produktseite ---------- */

add_action( 'woocommerce_single_product_summary', 'alp_product_coa_box', 32 );
function alp_product_coa_box() {
	if ( ! alp_config( 'features.product_coa_box' ) ) {
		return;
	}
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	if ( in_array( $product->get_sku(), (array) alp_config( 'product.coa_exclude_skus', array() ), true ) ) {
		return;
	}
	alp_part( 'product/coa-box', array( 'batch' => alp_coa_for_sku( $product->get_sku() ) ) );
}

/**
 * Chromatogramm-Grafik (SVG) – stilisierte HPLC-Kurve mit Hauptpeak.
 * Rein illustrativ; die echten Werte stehen im verlinkten Zertifikat.
 */
function alp_chromatogram( $seed = 'alp', $purity = 99 ) {
	$w      = 320;
	$h      = 110;
	$base   = $h - 14;
	$rand   = crc32( (string) $seed );
	$main_x = 150 + ( $rand % 40 );
	$peaks  = array(
		array( $main_x, 78, 5.5 ),
		array( 60 + ( $rand % 30 ), max( 2, ( 100 - (float) $purity ) * 3 ), 4 ),
		array( 240 + ( $rand % 25 ), max( 1.5, ( 100 - (float) $purity ) * 2 ), 4.5 ),
	);
	$points = array();
	for ( $x = 0; $x <= $w; $x += 2 ) {
		$y = 0;
		foreach ( $peaks as $p ) {
			$y += $p[1] * exp( -pow( $x - $p[0], 2 ) / ( 2 * $p[2] * $p[2] ) );
		}
		$y       += 0.8 * sin( $x / 7 + $rand );
		$points[] = $x . ',' . round( $base - $y, 1 );
	}
	$line = 'M' . implode( ' L', $points );
	$area = $line . " L{$w},{$base} L0,{$base} Z";

	ob_start();
	?>
	<svg class="alp-chroma" viewBox="0 0 <?php echo (int) $w; ?> <?php echo (int) $h; ?>" role="img" aria-label="Stilisiertes HPLC-Chromatogramm mit Hauptpeak">
		<g class="alp-chroma__grid">
			<?php for ( $gx = 40; $gx < $w; $gx += 40 ) : ?>
				<line x1="<?php echo (int) $gx; ?>" y1="6" x2="<?php echo (int) $gx; ?>" y2="<?php echo (int) $base; ?>"/>
			<?php endfor; ?>
			<line x1="0" y1="<?php echo (int) $base; ?>" x2="<?php echo (int) $w; ?>" y2="<?php echo (int) $base; ?>" class="alp-chroma__axis"/>
		</g>
		<path class="alp-chroma__area" d="<?php echo esc_attr( $area ); ?>"/>
		<path class="alp-chroma__line" d="<?php echo esc_attr( $line ); ?>"/>
		<g class="alp-chroma__label" transform="translate(<?php echo (int) min( $main_x + 10, $w - 80 ); ?>,18)">
			<text x="0" y="0">Hauptpeak</text>
			<text x="0" y="13" class="alp-chroma__value"><?php echo esc_html( alp_num( $purity, 0 ) ); ?> % Fläche</text>
		</g>
		<text x="0" y="<?php echo (int) $h - 1; ?>" class="alp-chroma__tick">0 min</text>
		<text x="<?php echo (int) $w; ?>" y="<?php echo (int) $h - 1; ?>" class="alp-chroma__tick" text-anchor="end">Retentionszeit</text>
	</svg>
	<?php
	return ob_get_clean();
}
