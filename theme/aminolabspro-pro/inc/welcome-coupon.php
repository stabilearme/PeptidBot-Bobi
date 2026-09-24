<?php
/**
 * 15-%-Willkommenscode für Newsletter-Anmelder: nur für die erste Bestellung.
 *
 * Die Codes selbst sind normale WooCommerce-Gutscheine (Marketing → Gutscheine),
 * je Code: 15 %, einmal nutzbar, nicht für reduzierte Produkte, mit Ablaufdatum.
 * Brevo verschickt sie nach der Newsletter-Bestätigung (Brevo → Gutscheine).
 *
 * Dieses Modul ergänzt nur die Regel, die WooCommerce selbst nicht kann:
 * Codes mit dem Präfix aus config.php → 'welcome_coupon' → 'prefix' gelten nur,
 * wenn es zur E-Mail-Adresse (bzw. zum Kundenkonto) noch keine Bestellung gibt.
 * Geprüft wird beim Einlösen im Warenkorb und noch einmal beim Absenden der Bestellung.
 */

defined( 'ABSPATH' ) || exit;

/** true, wenn $code ein Willkommenscode ist. */
function alp_is_welcome_coupon( $code ) {
	$prefix = (string) alp_config( 'welcome_coupon.prefix', '' );
	return '' !== $prefix && 0 === stripos( (string) $code, $prefix );
}

/** true, wenn es zu E-Mail-Adresse oder Kundenkonto schon eine (bezahlte/offene) Bestellung gibt. */
function alp_customer_has_orders( $email, $user_id = 0 ) {
	if ( ! function_exists( 'wc_get_orders' ) ) {
		return false;
	}
	$statuses = array( 'wc-processing', 'wc-completed', 'wc-on-hold', 'wc-refunded' );
	$email    = sanitize_email( (string) $email );
	if ( $email && wc_get_orders( array( 'billing_email' => $email, 'status' => $statuses, 'limit' => 1, 'return' => 'ids' ) ) ) {
		return true;
	}
	if ( $user_id && wc_get_orders( array( 'customer_id' => (int) $user_id, 'status' => $statuses, 'limit' => 1, 'return' => 'ids' ) ) ) {
		return true;
	}
	return false;
}

/** E-Mail-Adresse des aktuellen Kunden (Konto oder bereits eingegebene Rechnungsadresse). */
function alp_current_customer_email() {
	$email = '';
	if ( function_exists( 'WC' ) && WC()->customer ) {
		$email = WC()->customer->get_billing_email();
	}
	if ( ! $email && is_user_logged_in() ) {
		$email = wp_get_current_user()->user_email;
	}
	return $email;
}

/* Beim Einlösen (Warenkorb / Kasse). */
add_filter( 'woocommerce_coupon_is_valid', 'alp_welcome_coupon_is_valid', 20, 2 );
function alp_welcome_coupon_is_valid( $valid, $coupon ) {
	if ( ! $valid || ! is_object( $coupon ) || ! alp_is_welcome_coupon( $coupon->get_code() ) ) {
		return $valid;
	}
	if ( alp_customer_has_orders( alp_current_customer_email(), get_current_user_id() ) ) {
		throw new Exception( esc_html( alp_config( 'welcome_coupon.message', 'Dieser Code gilt nur für deine erste Bestellung.' ) ), 100 );
	}
	return $valid;
}

/* Beim Absenden: mit der endgültigen Rechnungs-E-Mail prüfen (Gäste). */
add_action( 'woocommerce_after_checkout_validation', 'alp_welcome_coupon_checkout_check', 20, 2 );
function alp_welcome_coupon_checkout_check( $data, $errors ) {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return;
	}
	foreach ( WC()->cart->get_applied_coupons() as $code ) {
		if ( alp_is_welcome_coupon( $code ) && alp_customer_has_orders( $data['billing_email'] ?? '', get_current_user_id() ) ) {
			WC()->cart->remove_coupon( $code );
			$errors->add( 'alp_welcome_coupon', esc_html( alp_config( 'welcome_coupon.message', 'Dieser Code gilt nur für deine erste Bestellung.' ) ) );
		}
	}
}

/* Block-Kasse / Express-Zahlungen (Store-API): mit der E-Mail der Bestellung prüfen. */
add_action( 'woocommerce_store_api_checkout_update_order_from_request', 'alp_welcome_coupon_store_api_check', 20, 1 );
function alp_welcome_coupon_store_api_check( $order ) {
	if ( ! is_object( $order ) || ! method_exists( $order, 'get_coupon_codes' ) ) {
		return;
	}
	foreach ( $order->get_coupon_codes() as $code ) {
		if ( alp_is_welcome_coupon( $code ) && alp_customer_has_orders( $order->get_billing_email(), (int) $order->get_customer_id() ) ) {
			$message = alp_config( 'welcome_coupon.message', 'Dieser Code gilt nur für deine erste Bestellung.' );
			if ( class_exists( '\\Automattic\\WooCommerce\\StoreApi\\Exceptions\\RouteException' ) ) {
				throw new \Automattic\WooCommerce\StoreApi\Exceptions\RouteException( 'alp_welcome_coupon', esc_html( $message ), 400 );
			}
			throw new Exception( esc_html( $message ) );
		}
	}
}
