<?php
/**
 * Preise: dauerhafte Reduzierungen auflösen + Preisverlauf für durchgestrichene Preise.
 *
 * 1) Werkzeug „Preise vereinheitlichen“ (WooCommerce → Preise vereinheitlichen):
 *    Für jedes Produkt mit dauerhaftem Angebotspreis wird ein neuer Normalpreis zwischen
 *    altem Normalpreis und Angebotspreis vorgeschlagen (gleiche Cent-Endung, z. B. ,95).
 *    Vorschau → Übernehmen (Angebotspreis wird entfernt) → bei Bedarf Rückgängig.
 *
 * 2) Preisverlauf (Meta '_alp_price_log'): gespeichert werden Preise, zu denen ein Produkt
 *    zuletzt verkauft wurde (alter Angebotspreis, Wochenangebot). Daraus ergibt sich der
 *    niedrigste Preis der letzten 30 Tage. Ein durchgestrichener „statt“-Preis wird nur gezeigt,
 *    wenn der Aktionspreis darunter liegt – und dann mit diesem Referenzpreis (§ 11 PAngV).
 */

defined( 'ABSPATH' ) || exit;

define( 'ALP_PRICE_LOG_META', '_alp_price_log' );

/** Preis im Verlauf vermerken (Zeitpunkt, bis zu dem dieser Preis galt). */
function alp_price_log_add( $product_id, $price, $time = null ) {
	$price = (float) $price;
	if ( $price <= 0 ) {
		return;
	}
	$log   = (array) get_post_meta( $product_id, ALP_PRICE_LOG_META, true );
	$log[] = array( (int) ( $time ? $time : time() ), $price );
	// Nur die letzten 60 Tage aufheben.
	$log = array_values( array_filter( $log, fn( $row ) => is_array( $row ) && $row[0] > time() - 60 * DAY_IN_SECONDS ) );
	update_post_meta( $product_id, ALP_PRICE_LOG_META, $log );
}

/**
 * Niedrigster Preis der letzten 30 Tage (ohne das laufende Wochenangebot).
 * $current = aktueller Normalpreis ohne Aktion.
 */
function alp_price_low30( $product_id, $current ) {
	$low = (float) $current;
	foreach ( (array) get_post_meta( $product_id, ALP_PRICE_LOG_META, true ) as $row ) {
		if ( is_array( $row ) && $row[0] >= time() - 30 * DAY_IN_SECONDS && $row[1] > 0 ) {
			$low = min( $low, (float) $row[1] );
		}
	}
	return $low;
}

/** Neuer Normalpreis zwischen $regular und $sale mit derselben Cent-Endung wie $regular. */
function alp_price_middle( $regular, $sale ) {
	$regular = (float) $regular;
	$sale    = (float) $sale;
	$mid     = ( $regular + $sale ) / 2;
	$cents   = round( $regular - floor( $regular ), 2 );
	$best    = null;
	foreach ( array( floor( $mid ) - 1, floor( $mid ), floor( $mid ) + 1 ) as $euro ) {
		$candidate = $euro + $cents;
		if ( $candidate <= $sale || $candidate >= $regular ) {
			continue;
		}
		// Nächster Wert zur Mitte; bei Gleichstand der günstigere.
		if ( null === $best || abs( $candidate - $mid ) < abs( $best - $mid ) - 0.001 ) {
			$best = $candidate;
		}
	}
	return round( null === $best ? $mid : $best, 2 );
}

/** Produkte mit dauerhaftem Angebotspreis (einfache Produkte). */
function alp_price_candidates() {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}
	$rows = array();
	foreach ( wc_get_products( array( 'limit' => -1, 'status' => array( 'publish', 'private' ), 'type' => 'simple' ) ) as $p ) {
		$regular = (float) $p->get_regular_price( 'edit' );
		$sale    = $p->get_sale_price( 'edit' );
		if ( '' === (string) $sale || (float) $sale <= 0 || (float) $sale >= $regular ) {
			continue;
		}
		$rows[] = array(
			'product' => $p,
			'regular' => $regular,
			'sale'    => (float) $sale,
			'new'     => alp_price_middle( $regular, (float) $sale ),
		);
	}
	return $rows;
}

/* ---------- Admin: WooCommerce → Preise vereinheitlichen ---------- */
add_action( 'admin_menu', 'alp_price_admin_menu', 62 );
function alp_price_admin_menu() {
	add_submenu_page( 'woocommerce', 'Preise vereinheitlichen', 'Preise vereinheitlichen', 'manage_woocommerce', 'alp-prices', 'alp_price_admin_page' );
}

function alp_price_admin_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}
	$message = '';
	if ( isset( $_POST['alp_price_action'] ) && check_admin_referer( 'alp_prices' ) ) {
		$action = sanitize_key( wp_unslash( $_POST['alp_price_action'] ) );
		if ( 'apply' === $action ) {
			$backup = array();
			$count  = 0;
			foreach ( alp_price_candidates() as $row ) {
				$p = $row['product'];
				$backup[ $p->get_id() ] = array( $row['regular'], $row['sale'] );
				alp_price_log_add( $p->get_id(), $row['sale'] ); // bisheriger Verkaufspreis zählt 30 Tage als Referenz
				$p->set_regular_price( (string) $row['new'] );
				$p->set_sale_price( '' );
				$p->set_date_on_sale_from( null );
				$p->set_date_on_sale_to( null );
				$p->save();
				$count++;
			}
			if ( $backup ) {
				update_option( 'alp_price_backup', array( 'time' => time(), 'rows' => $backup ), false );
			}
			$message = $count . ' Preise umgestellt. Angebotspreise entfernt.';
		} elseif ( 'undo' === $action ) {
			$backup = (array) get_option( 'alp_price_backup', array() );
			$count  = 0;
			foreach ( (array) ( $backup['rows'] ?? array() ) as $id => $prices ) {
				$p = wc_get_product( $id );
				if ( $p ) {
					$p->set_regular_price( (string) $prices[0] );
					$p->set_sale_price( (string) $prices[1] );
					$p->save();
					$count++;
				}
			}
			delete_option( 'alp_price_backup' );
			$message = $count . ' Preise auf den alten Stand zurückgesetzt.';
		}
		if ( function_exists( 'alp_wd_purge_cache' ) ) {
			alp_wd_purge_cache();
		}
	}

	echo '<div class="wrap"><h1>Preise vereinheitlichen</h1>';
	if ( $message ) {
		echo '<div class="notice notice-success"><p>' . esc_html( $message ) . '</p></div>';
	}
	echo '<p>Dauerhafte Angebotspreise werden aufgelöst: Jedes Produkt bekommt einen neuen Normalpreis zwischen altem Normalpreis und Angebotspreis. Danach gibt es keine durchgestrichenen Preise mehr – außer im Wochenangebot.</p>';
	echo '<p>Der bisherige Angebotspreis wird 30 Tage lang als „niedrigster Preis“ gemerkt. Ein Wochenangebot zeigt einen durchgestrichenen Preis nur, wenn es darunter liegt (Preisangabenverordnung).</p>';

	$rows = alp_price_candidates();
	if ( $rows ) {
		echo '<table class="widefat striped" style="max-width:860px"><thead><tr><th>Produkt</th><th>Artikelnr.</th><th>Normal bisher</th><th>Angebot bisher</th><th>Neuer Preis</th></tr></thead><tbody>';
		foreach ( $rows as $row ) {
			printf(
				'<tr><td>%s</td><td>%s</td><td><del>%s</del></td><td>%s</td><td><strong>%s</strong></td></tr>',
				esc_html( $row['product']->get_name() ),
				esc_html( $row['product']->get_sku() ),
				wp_kses_post( wc_price( $row['regular'] ) ),
				wp_kses_post( wc_price( $row['sale'] ) ),
				wp_kses_post( wc_price( $row['new'] ) )
			);
		}
		echo '</tbody></table><form method="post" style="margin-top:1em">';
		wp_nonce_field( 'alp_prices' );
		echo '<button class="button button-primary" name="alp_price_action" value="apply">Diese ' . count( $rows ) . ' Preise übernehmen</button></form>';
	} else {
		echo '<p><strong>Keine Produkte mit dauerhaftem Angebotspreis gefunden.</strong></p>';
	}

	$backup = (array) get_option( 'alp_price_backup', array() );
	if ( ! empty( $backup['rows'] ) ) {
		echo '<hr><form method="post">';
		wp_nonce_field( 'alp_prices' );
		printf( '<p>Letzte Umstellung: %s (%d Produkte). ', esc_html( wp_date( 'd.m.Y H:i', (int) $backup['time'] ) ), count( $backup['rows'] ) );
		echo '<button class="button" name="alp_price_action" value="undo">Rückgängig machen</button></p></form>';
	}
	echo '</div>';
}
