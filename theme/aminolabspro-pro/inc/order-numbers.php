<?php
/**
 * Fortlaufende Bestellnummern (#1088, #1089 …) – kompatibel zu den bisherigen
 * Nummern auf aminolabspro.com (Feld „_alp_sequential_number“).
 *
 * Schaltet sich nur ein, wenn kein anderer Code (z. B. ein Code Snippet) die
 * Bestellnummer schon setzt – so wird nie doppelt gezählt. Ein-/Ausschalten:
 * config.php → 'order_numbers' ('auto' | true | false).
 *
 * Nummeriert werden normale Kasse UND Express-Zahlungen (Apple Pay / Google Pay
 * über die Store-API), die bisher nur die interne ID bekamen.
 */

defined( 'ABSPATH' ) || exit;

defined( 'ALP_SEQ_META' ) || define( 'ALP_SEQ_META', '_alp_sequential_number' );
defined( 'ALP_SEQ_OPTION' ) || define( 'ALP_SEQ_OPTION', 'alp_order_seq_last' );

add_action( 'wp_loaded', 'alp_order_numbers_boot', 1 );
function alp_order_numbers_boot() {
	$mode = alp_config( 'order_numbers', 'auto' );
	if ( false === $mode || ! function_exists( 'wc_get_orders' ) ) {
		return;
	}
	// 'auto': nur übernehmen, wenn niemand sonst die Bestellnummer filtert.
	if ( 'auto' === $mode && has_filter( 'woocommerce_order_number' ) ) {
		return;
	}
	add_filter( 'woocommerce_order_number', 'alp_order_number_display', 10, 2 );
	add_action( 'woocommerce_checkout_order_processed', 'alp_order_number_assign_id', 5, 1 );
	add_action( 'woocommerce_store_api_checkout_order_processed', 'alp_order_number_assign', 5, 1 );
	add_filter( 'woocommerce_shop_order_search_fields', 'alp_order_number_search_field' );
}

function alp_order_number_display( $number, $order ) {
	$seq = is_object( $order ) ? $order->get_meta( ALP_SEQ_META ) : '';
	return '' !== (string) $seq ? (string) $seq : $number;
}

function alp_order_number_assign_id( $order_id ) {
	alp_order_number_assign( wc_get_order( $order_id ) );
}

function alp_order_number_assign( $order ) {
	if ( ! $order instanceof WC_Order || '' !== (string) $order->get_meta( ALP_SEQ_META ) ) {
		return;
	}
	$order->update_meta_data( ALP_SEQ_META, (string) alp_order_number_next() );
	$order->save();
}

/** Nächste Nummer, atomar hochgezählt (auch bei zwei gleichzeitigen Bestellungen eindeutig). */
function alp_order_number_next() {
	global $wpdb;
	if ( false === get_option( ALP_SEQ_OPTION ) ) {
		add_option( ALP_SEQ_OPTION, (string) alp_order_number_seed(), '', false );
	}
	$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->options} SET option_value = LAST_INSERT_ID(option_value + 1) WHERE option_name = %s", ALP_SEQ_OPTION ) );
	$next = (int) $wpdb->get_var( 'SELECT LAST_INSERT_ID()' );
	wp_cache_delete( ALP_SEQ_OPTION, 'options' );
	wp_cache_delete( 'notoptions', 'options' );
	return $next > 0 ? $next : time();
}

/** Startwert: höchste bisher vergebene Nummer (aus den letzten 200 Bestellungen), sonst config-Start. */
function alp_order_number_seed() {
	$max = (int) alp_config( 'order_numbers_start', 1000 );
	$ids = wc_get_orders( array( 'limit' => 200, 'orderby' => 'date', 'order' => 'DESC', 'status' => array_keys( wc_get_order_statuses() ), 'return' => 'ids' ) );
	foreach ( (array) $ids as $id ) {
		$order = wc_get_order( $id );
		$max   = max( $max, $order ? (int) $order->get_meta( ALP_SEQ_META ) : 0 );
	}
	return $max;
}

function alp_order_number_search_field( $fields ) {
	$fields[] = ALP_SEQ_META;
	return $fields;
}
