<?php
/**
 * Wochenangebot: jeden Montag um 22 Uhr (einstellbar) wechselt das Angebot auf der Startseite
 * automatisch zum nächsten aus der Liste in config.php → 'weekly_deals'.
 *
 * Ein Angebot ist ein Einzelprodukt oder ein Stack aus mehreren Produkten.
 * - Einzelprodukt: in der Angebotswoche überall zum Aktionspreis (Shop, Produktseite, Warenkorb).
 * - Stack: der Aktionspreis gilt im Warenkorb, sobald alle Produkte des Stacks drin sind
 *   (je vollständigem Satz). Einzeln gekauft kosten die Produkte den normalen Preis.
 * Der Button „Angebot sichern“ legt alle Produkte des Angebots auf einmal in den Warenkorb.
 *
 * Rabatt: 'percent' auf den aktuellen Verkaufspreis (also zusätzlich zu einer bestehenden
 * Reduzierung) oder 'price' als fester Aktionspreis pro Produkt. 'percent' => 0 = aktueller Preis.
 * Preise in WooCommerce müssen dafür NICHT geändert werden – das Theme rechnet nur während
 * der Angebotswoche. Nach Ablauf gilt automatisch wieder der normale Preis.
 *
 * Kontrolle: WooCommerce → Wochenangebote (Plan der nächsten Wochen mit Preisen).
 */

defined( 'ABSPATH' ) || exit;

function alp_wd_on() {
	return (bool) alp_config( 'weekly_deals.enabled', false ) && function_exists( 'wc_get_product_id_by_sku' );
}

function alp_wd_tz() {
	return new DateTimeZone( (string) alp_config( 'weekly_deals.timezone', 'Europe/Berlin' ) );
}

/**
 * Beginn der Angebotswoche, in der $time liegt (letzter Wechsel-Zeitpunkt ≤ $time).
 */
function alp_wd_week_start( $time = null ) {
	$time  = null === $time ? time() : (int) $time;
	$tz    = alp_wd_tz();
	$day   = (string) alp_config( 'weekly_deals.switch_day', 'monday' );
	$clock = (string) alp_config( 'weekly_deals.switch_time', '22:00' );
	$now   = ( new DateTimeImmutable( '@' . $time ) )->setTimezone( $tz );
	$start = $now->modify( 'this week ' . $day . ' ' . $clock ); // Wechsel-Tag dieser Kalenderwoche
	if ( $start > $now ) {
		$start = $start->modify( '-1 week' );
	}
	return $start;
}

/** Angebotswoche Nummer $offset ab jetzt (0 = aktuell): Beginn, Ende, Angebot. */
function alp_wd_week( $offset = 0, $time = null ) {
	$deals = array_values( (array) alp_config( 'weekly_deals.deals', array() ) );
	if ( ! $deals ) {
		return null;
	}
	$start  = alp_wd_week_start( $time )->modify( ( $offset >= 0 ? '+' : '' ) . (int) $offset . ' week' );
	$anchor = alp_wd_week_start( strtotime( (string) alp_config( 'weekly_deals.first_week', '2026-09-22' ) . ' 12:00 ' . alp_wd_tz()->getName() ) );
	$weeks  = (int) round( ( $start->getTimestamp() - $anchor->getTimestamp() ) / WEEK_IN_SECONDS );
	$index  = ( ( $weeks % count( $deals ) ) + count( $deals ) ) % count( $deals );
	return array(
		'start' => $start->getTimestamp(),
		'end'   => $start->modify( '+1 week' )->getTimestamp(),
		'index' => $index,
		'key'   => $start->format( 'Ymd' ) . '-' . $index,
		'deal'  => alp_wd_normalize( $deals[ $index ] ),
	);
}

/** Angebot vereinheitlichen: items = [ [product_id, sku, qty, price] ] */
function alp_wd_normalize( $deal ) {
	$deal  = (array) $deal;
	$items = array();
	foreach ( (array) ( $deal['items'] ?? array() ) as $item ) {
		$item = is_array( $item ) ? $item : array( 'sku' => $item );
		$id   = ! empty( $item['sku'] ) ? (int) wc_get_product_id_by_sku( $item['sku'] ) : (int) ( $item['id'] ?? 0 );
		if ( $id ) {
			$items[ $id ] = array(
				'id'    => $id,
				'sku'   => (string) ( $item['sku'] ?? '' ),
				'qty'   => max( 1, (int) ( $item['qty'] ?? 1 ) ),
				'price' => isset( $item['price'] ) ? (float) $item['price'] : null,
			);
		}
	}
	$deal['items']   = $items;
	$deal['percent'] = (float) ( $deal['percent'] ?? 0 );
	$deal['bundle']  = count( $items ) > 1;
	return $deal;
}

/** Aktuelles Angebot (einmal pro Aufruf berechnet). */
function alp_wd_current() {
	static $current = false;
	if ( false === $current ) {
		$current = alp_wd_on() ? alp_wd_week( 0 ) : null;
		if ( $current && ! $current['deal']['items'] ) {
			$current = null;
		}
	}
	return $current;
}

/**
 * Aktionspreis für ein Produkt im Angebot. $base = Preis, den Kunden sonst zahlen.
 */
function alp_wd_price( $deal, $product_id, $base ) {
	$item = $deal['items'][ $product_id ] ?? null;
	if ( ! $item || $base <= 0 ) {
		return (float) $base;
	}
	if ( null !== $item['price'] ) {
		return min( (float) $base, $item['price'] );
	}
	return $deal['percent'] > 0 ? round( $base * ( 1 - $deal['percent'] / 100 ), 2 ) : (float) $base;
}

/* ---------- Einzelprodukt: Aktionspreis überall ---------- */

/** Wochenangebot-Daten für ein Einzelprodukt im aktuellen Angebot, sonst null: [ Aktionspreis, Referenzpreis ]. */
function alp_wd_single( $product ) {
	$week = alp_wd_current();
	if ( ! $week || $week['deal']['bundle'] || ! is_object( $product ) || ! isset( $week['deal']['items'][ $product->get_id() ] ) ) {
		return null;
	}
	$base = (float) $product->get_price( 'edit' ); // Preis ohne Wochenangebot
	$deal = alp_wd_price( $week['deal'], $product->get_id(), $base );
	return array( $deal, alp_wd_ref_price( $product ) );
}

/**
 * Referenz für den durchgestrichenen Preis: der niedrigste Preis der letzten 30 Tage
 * (höchstens der Normalpreis). Liegt der Aktionspreis nicht darunter, wird nichts durchgestrichen.
 */
function alp_wd_ref_price( $product ) {
	$base    = (float) $product->get_price( 'edit' );
	$regular = (float) $product->get_regular_price( 'edit' );
	$ref     = $regular > 0 ? min( $regular, $base > 0 ? $base : $regular ) : $base;
	return function_exists( 'alp_price_low30' ) ? alp_price_low30( $product->get_id(), $ref ) : $ref;
}

add_filter( 'woocommerce_product_get_price', 'alp_wd_filter_price', 50, 2 );
function alp_wd_filter_price( $value, $product ) {
	$single = alp_wd_single( $product );
	return $single && $single[0] < (float) $product->get_price( 'edit' ) ? (string) $single[0] : $value;
}

add_filter( 'woocommerce_product_get_sale_price', 'alp_wd_filter_sale_price', 50, 2 );
function alp_wd_filter_sale_price( $value, $product ) {
	$single = alp_wd_single( $product );
	return $single && $single[0] < $single[1] ? (string) $single[0] : $value;
}

/* Durchgestrichen wird der Referenzpreis (niedrigster Preis der letzten 30 Tage), nicht ein höherer Normalpreis. */
add_filter( 'woocommerce_product_get_regular_price', 'alp_wd_filter_regular_price', 50, 2 );
function alp_wd_filter_regular_price( $value, $product ) {
	$single = alp_wd_single( $product );
	return $single && $single[0] < $single[1] ? (string) $single[1] : $value;
}

add_filter( 'woocommerce_product_is_on_sale', 'alp_wd_filter_on_sale', 50, 2 );
function alp_wd_filter_on_sale( $on_sale, $product ) {
	if ( ! is_object( $product ) ) {
		return $on_sale;
	}
	if ( $product->get_meta( '_alp_wd_bundle', true ) ) {
		return true; // Stack-Artikel im Warenkorb gelten als reduziert (z. B. für „nicht auf reduzierte Produkte“)
	}
	$single = alp_wd_single( $product );
	return $single ? ( $on_sale || $single[0] < $single[1] ) : $on_sale;
}

/* Angebote-Liste auf der Startseite („Angebote“) kennt auch das Wochenangebot. */
add_filter( 'transient_wc_products_onsale', 'alp_wd_onsale_ids' );
function alp_wd_onsale_ids( $ids ) {
	$week = alp_wd_current();
	if ( ! $week || $week['deal']['bundle'] || ! is_array( $ids ) ) {
		return $ids;
	}
	return array_values( array_unique( array_merge( $ids, array_keys( $week['deal']['items'] ) ) ) );
}

/* ---------- Stack: Aktionspreis im Warenkorb, sobald alle Produkte drin sind ---------- */
add_action( 'woocommerce_before_calculate_totals', 'alp_wd_bundle_prices', 50 );
// Auch direkt nach dem Laden des Warenkorbs, damit Gutscheine den Stack sofort als reduziert erkennen.
add_action( 'woocommerce_cart_loaded_from_session', 'alp_wd_bundle_prices', 50 );
function alp_wd_bundle_prices( $cart ) {
	$week = alp_wd_current();
	if ( ! $week || ! $week['deal']['bundle'] || ! is_object( $cart ) ) {
		return;
	}
	$deal = $week['deal'];
	$have = array();
	foreach ( $cart->get_cart() as $line ) {
		$pid          = (int) $line['product_id'];
		$have[ $pid ] = ( $have[ $pid ] ?? 0 ) + (int) $line['quantity'];
	}
	// Anzahl vollständiger Sätze.
	$sets = PHP_INT_MAX;
	foreach ( $deal['items'] as $pid => $item ) {
		$sets = min( $sets, intdiv( $have[ $pid ] ?? 0, $item['qty'] ) );
	}
	if ( $sets < 1 ) {
		return;
	}
	$left = array();
	foreach ( $deal['items'] as $pid => $item ) {
		$left[ $pid ] = $sets * $item['qty']; // so viele Stück je Produkt bekommen den Aktionspreis
	}
	foreach ( $cart->get_cart() as $key => $line ) {
		$pid = (int) $line['product_id'];
		if ( empty( $left[ $pid ] ) ) {
			continue;
		}
		$product = $line['data'];
		// Ursprungspreis merken – die Berechnung läuft pro Aufruf mehrmals, sonst würde doppelt reduziert.
		$base = $product->get_meta( '_alp_wd_base', true );
		if ( '' === $base ) {
			$base = (float) $product->get_price( 'edit' );
			$product->update_meta_data( '_alp_wd_base', $base );
		}
		$base = (float) $base;
		$qty     = (int) $line['quantity'];
		$n       = min( $qty, $left[ $pid ] );
		$left[ $pid ] -= $n;
		$price   = ( $n * alp_wd_price( $deal, $pid, $base ) + ( $qty - $n ) * $base ) / $qty;
		$product->set_price( round( $price, 4 ) );
		$product->update_meta_data( '_alp_wd_bundle', 1 ); // nur im Speicher, wird nicht gespeichert
		$cart->cart_contents[ $key ]['alp_wd_bundle'] = $deal['title'] ?? 'Wochenangebot';
	}
}

add_filter( 'woocommerce_get_item_data', 'alp_wd_cart_item_label', 10, 2 );
function alp_wd_cart_item_label( $data, $line ) {
	if ( ! empty( $line['alp_wd_bundle'] ) ) {
		$data[] = array( 'key' => 'Wochenangebot', 'value' => esc_html( $line['alp_wd_bundle'] ) );
	}
	return $data;
}

/* ---------- „Angebot sichern“: alle Produkte in den Warenkorb ---------- */
function alp_wd_add_url( $week ) {
	return add_query_arg( 'alp_deal', rawurlencode( $week['key'] ), home_url( '/' ) );
}

add_action( 'wp_loaded', 'alp_wd_add_to_cart', 20 );
function alp_wd_add_to_cart() {
	if ( empty( $_GET['alp_deal'] ) || ! function_exists( 'WC' ) || ! WC()->cart ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$week = alp_wd_current();
	$key  = sanitize_text_field( wp_unslash( $_GET['alp_deal'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	if ( ! $week || $week['key'] !== $key ) {
		wc_add_notice( 'Dieses Angebot ist abgelaufen – schau dir das neue Wochenangebot an.', 'notice' );
		wp_safe_redirect( home_url( '/#aktion' ) );
		exit;
	}
	$added = 0;
	foreach ( $week['deal']['items'] as $pid => $item ) {
		// Schon im Warenkorb? Dann nur auffüllen, nicht verdoppeln.
		$in_cart = 0;
		foreach ( WC()->cart->get_cart() as $line ) {
			if ( (int) $line['product_id'] === $pid ) {
				$in_cart += (int) $line['quantity'];
			}
		}
		$need = $item['qty'] - $in_cart;
		if ( $need > 0 && WC()->cart->add_to_cart( $pid, $need ) ) {
			$added++;
		}
	}
	if ( $added ) {
		wc_add_notice( sprintf( '%s ist im Warenkorb – der Aktionspreis ist schon abgezogen.', esc_html( $week['deal']['title'] ?? 'Das Wochenangebot' ) ), 'success' );
	}
	wp_safe_redirect( wc_get_cart_url() );
	exit;
}

/* ---------- Beim Wechsel: Seiten-Cache leeren, damit die Startseite das neue Angebot zeigt ---------- */
add_action( 'init', 'alp_wd_check_switch', 20 );
add_action( 'alp_wd_switch', 'alp_wd_check_switch' );
function alp_wd_check_switch() {
	$week = alp_wd_current();
	if ( ! $week ) {
		return;
	}
	if ( get_option( 'alp_wd_key' ) !== $week['key'] ) {
		update_option( 'alp_wd_key', $week['key'], true );
		// Aktionspreise der abgelaufenen Woche merken (Referenz „niedrigster Preis der letzten 30 Tage“).
		$prev = alp_wd_week( -1 );
		if ( $prev && ! $prev['deal']['bundle'] && function_exists( 'alp_price_log_add' ) ) {
			foreach ( $prev['deal']['items'] as $pid => $item ) {
				$p = wc_get_product( $pid );
				if ( $p ) {
					alp_price_log_add( $pid, alp_wd_price( $prev['deal'], $pid, (float) $p->get_price( 'edit' ) ), $prev['end'] );
				}
			}
		}
		alp_wd_purge_cache( $week );
	}
	// Nächsten Wechsel vormerken (WP-Cron läuft beim nächsten Seitenaufruf nach diesem Zeitpunkt).
	if ( ! wp_next_scheduled( 'alp_wd_switch' ) ) {
		wp_schedule_single_event( $week['end'] + 30, 'alp_wd_switch' );
	}
}

function alp_wd_purge_cache( $week = null ) {
	if ( function_exists( 'WP_Optimize' ) && is_object( WP_Optimize() ) && method_exists( WP_Optimize(), 'get_page_cache' ) ) {
		$cache = WP_Optimize()->get_page_cache();
		if ( is_object( $cache ) && method_exists( $cache, 'purge' ) ) {
			$cache->purge();
		}
	}
	if ( function_exists( 'wc_delete_product_transients' ) ) {
		wc_delete_product_transients();
	}
	do_action( 'alp_weekly_deal_changed', $week );
}

/* ---------- Admin: WooCommerce → Wochenangebote ---------- */
add_action( 'admin_menu', 'alp_wd_admin_menu', 61 );
function alp_wd_admin_menu() {
	add_submenu_page( 'woocommerce', 'Wochenangebote', 'Wochenangebote', 'manage_woocommerce', 'alp-weekly-deals', 'alp_wd_admin_page' );
}

function alp_wd_admin_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}
	$fmt = function ( $ts ) {
		return wp_date( 'D, d.m.Y H:i', $ts, alp_wd_tz() );
	};
	echo '<div class="wrap"><h1>Wochenangebote</h1>';
	if ( ! alp_wd_on() ) {
		echo '<p>Ausgeschaltet (config.php → weekly_deals → enabled).</p></div>';
		return;
	}
	echo '<p>Wechsel automatisch jeden ' . esc_html( ucfirst( (string) alp_config( 'weekly_deals.switch_day', 'monday' ) ) ) . ' um ' . esc_html( (string) alp_config( 'weekly_deals.switch_time', '22:00' ) ) . ' Uhr. Angebote ändern: <code>inc/config.php → weekly_deals</code>.</p>';
	echo '<table class="widefat striped" style="max-width:980px"><thead><tr><th>Zeitraum</th><th>Angebot</th><th>Produkte</th><th>Normal</th><th>Aktion</th></tr></thead><tbody>';
	for ( $i = 0; $i < 8; $i++ ) {
		$week   = alp_wd_week( $i );
		$deal   = $week['deal'];
		$lines  = array();
		$normal = 0;
		$promo  = 0;
		foreach ( $deal['items'] as $pid => $item ) {
			$p = wc_get_product( $pid );
			if ( ! $p ) {
				continue;
			}
			$base    = (float) $p->get_price( 'edit' );
			$normal += $base * $item['qty'];
			$promo  += alp_wd_price( $deal, $pid, $base ) * $item['qty'];
			$lines[] = ( $item['qty'] > 1 ? $item['qty'] . '× ' : '' ) . $p->get_name() . ( $p->is_in_stock() ? '' : ' <b style="color:#b32d2e">(nicht vorrätig)</b>' );
		}
		if ( count( $lines ) < count( (array) ( alp_config( 'weekly_deals.deals' )[ $week['index'] ]['items'] ?? array() ) ) ) {
			$lines[] = '<b style="color:#b32d2e">Artikelnummer nicht gefunden – bitte prüfen</b>';
		}
		printf(
			'<tr%s><td>%s<br>bis %s</td><td><strong>%s</strong>%s</td><td>%s</td><td>%s</td><td><strong>%s</strong></td></tr>',
			0 === $i ? ' style="background:#f0fbf7"' : '',
			esc_html( $fmt( $week['start'] ) ),
			esc_html( $fmt( $week['end'] ) ),
			esc_html( $deal['title'] ?? '' ),
			0 === $i ? ' <em>(läuft jetzt)</em>' : '',
			wp_kses_post( implode( '<br>', $lines ) ),
			wp_kses_post( wc_price( $normal ) ),
			wp_kses_post( wc_price( $promo ) )
		);
	}
	echo '</tbody></table></div>';
}
