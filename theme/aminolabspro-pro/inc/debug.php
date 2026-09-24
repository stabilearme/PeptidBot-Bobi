<?php
/**
 * Diagnose für Admins: Seite mit ?alp_debug=1 aufrufen (nur eingeloggt als Administrator).
 * Zeigt oben einen Kasten mit den Werten, die für Produktbilder und Theme-Einstellungen
 * entscheidend sind. Für Besucher passiert nichts.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_body_open', 'alp_debug_box', 1 );
add_action( 'flatsome_before_header', 'alp_debug_box', 1 );
function alp_debug_box() {
	static $done = false;
	if ( $done || empty( $_GET['alp_debug'] ) || ! current_user_can( 'manage_options' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$done = true;

	$rows = array(
		'Theme-Version'               => defined( 'ALP_VERSION' ) ? ALP_VERSION : '?',
		'Aktives Theme (Option)'      => get_option( 'stylesheet' ) . ' / geladen: ' . get_stylesheet(),
		'Customizer-Vorschau'         => is_customize_preview() ? 'ja' : 'nein',
		'lazy_load_images (Mod)'      => var_export( get_theme_mod( 'lazy_load_images' ), true ),
		'lazy_load_images (Liste)'    => var_export( ( get_theme_mods() ?: array() )['lazy_load_images'] ?? null, true ),
		'product_hover'               => var_export( get_theme_mod( 'product_hover' ), true ),
		'grid_style'                  => var_export( get_theme_mod( 'grid_style' ), true ),
		'alp_mods_migrated'           => var_export( get_option( 'alp_mods_migrated' ), true ),
	);

	foreach ( array( 'flatsome_woocommerce_shop_loop_images', 'woocommerce_before_shop_loop_item_title', 'wp_get_attachment_image_src', 'wp_get_attachment_image_attributes', 'post_thumbnail_html', 'woocommerce_product_get_image', 'wp_get_attachment_url' ) as $hook ) {
		$rows[ 'Hook: ' . $hook ] = alp_debug_callbacks( $hook );
	}

	$html = '';
	if ( function_exists( 'wc_get_products' ) ) {
		$ids = wc_get_products( array( 'limit' => 1, 'status' => 'publish', 'return' => 'ids', 'orderby' => 'date', 'order' => 'DESC' ) );
		if ( $ids ) {
			global $post, $product;
			$post    = get_post( $ids[0] ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			$product = wc_get_product( $ids[0] ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			setup_postdata( $post );
			$img_id = $product->get_image_id();
			$src    = wp_get_attachment_image_src( $img_id, 'woocommerce_thumbnail' );
			$size   = function_exists( 'wc_get_image_size' ) ? wc_get_image_size( 'woocommerce_thumbnail' ) : array();
			$file   = get_attached_file( $img_id );
			$meta   = wp_get_attachment_metadata( $img_id );

			$rows['Produkt']              = $product->get_name() . ' (#' . $ids[0] . '), Bild-ID ' . $img_id;
			$rows['Bilddatei vorhanden']  = $file && file_exists( $file ) ? 'ja: ' . $file : 'NEIN: ' . $file;
			$rows['woocommerce_thumbnail'] = wp_json_encode( $size );
			$rows['Bildquelle (src)']     = wp_json_encode( $src );
			$rows['Größen in Metadaten']  = $meta && ! empty( $meta['sizes'] ) ? implode( ', ', array_map( fn( $k, $v ) => $k . ' ' . $v['width'] . 'x' . $v['height'], array_keys( $meta['sizes'] ), $meta['sizes'] ) ) : 'keine';

			ob_start();
			do_action( 'flatsome_woocommerce_shop_loop_images' );
			$html = ob_get_clean();
			if ( '' === trim( $html ) ) {
				$html = '(flatsome_woocommerce_shop_loop_images gibt nichts aus) ' . woocommerce_get_product_thumbnail();
			}
			wp_reset_postdata();
		}
	}

	echo '<div style="position:relative;z-index:99999;margin:0;padding:14px 18px;background:#fff8e1;border-bottom:3px solid #f0b400;font:12px/1.45 monospace;color:#222;text-align:left;max-height:70vh;overflow:auto">';
	echo '<strong style="font:700 14px sans-serif">AminoLabs Pro – Diagnose (nur für Admins sichtbar)</strong><table style="margin-top:8px;border-collapse:collapse">';
	foreach ( $rows as $k => $v ) {
		echo '<tr><td style="padding:2px 12px 2px 0;vertical-align:top;white-space:nowrap"><b>' . esc_html( $k ) . '</b></td><td style="padding:2px 0;word-break:break-all">' . esc_html( (string) $v ) . '</td></tr>';
	}
	echo '</table><div style="margin-top:8px"><b>HTML des ersten Produktbilds:</b><pre style="white-space:pre-wrap;word-break:break-all;background:#fff;padding:8px;border:1px solid #ddd">' . esc_html( $html ) . '</pre></div></div>';
}

function alp_debug_callbacks( $hook ) {
	global $wp_filter;
	if ( empty( $wp_filter[ $hook ] ) ) {
		return '–';
	}
	$out = array();
	foreach ( $wp_filter[ $hook ]->callbacks as $priority => $callbacks ) {
		foreach ( $callbacks as $cb ) {
			$fn = $cb['function'];
			if ( is_string( $fn ) ) {
				$name = $fn;
			} elseif ( is_array( $fn ) ) {
				$name = ( is_object( $fn[0] ) ? get_class( $fn[0] ) : $fn[0] ) . '::' . $fn[1];
			} else {
				$name = 'closure';
			}
			$out[] = $priority . ': ' . $name;
		}
	}
	return implode( ' | ', $out );
}
