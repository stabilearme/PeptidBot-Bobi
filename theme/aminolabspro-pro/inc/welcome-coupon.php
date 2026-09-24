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

/*
 * ---------- Codes anlegen (einmalig, nur Admins) ----------
 * https://DEINE-SEITE/?alp_welcome_codes=1 aufrufen → Knopf „Codes anlegen“.
 * Legt die Codes aus data/welcome-codes.php an (15 %, einmal nutzbar, nicht für reduzierte
 * Produkte, gültig bis welcome_coupon.expires). Bereits vorhandene Codes werden übersprungen.
 */
add_action( 'template_redirect', 'alp_welcome_codes_tool', 0 );
function alp_welcome_codes_tool() {
	if ( empty( $_GET['alp_welcome_codes'] ) || ! current_user_can( 'manage_woocommerce' ) || ! class_exists( 'WC_Coupon' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$file  = ALP_DIR . '/data/welcome-codes.php';
	$codes = file_exists( $file ) ? (array) include $file : array();
	$done  = array();
	$new   = 0;

	if ( 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) && check_admin_referer( 'alp_welcome_codes' ) ) {
		$start = time();
		foreach ( $codes as $code ) {
			if ( wc_get_coupon_id_by_code( $code ) ) {
				continue;
			}
			$coupon = new WC_Coupon();
			$coupon->set_code( $code );
			$coupon->set_discount_type( 'percent' );
			$coupon->set_amount( (string) alp_config( 'welcome_coupon.amount', 15 ) );
			$coupon->set_individual_use( true );
			$coupon->set_exclude_sale_items( true );
			$coupon->set_usage_limit( 1 );
			$coupon->set_usage_limit_per_user( 1 );
			$coupon->set_date_expires( alp_config( 'welcome_coupon.expires', '2027-09-24 23:59:59' ) );
			$coupon->set_description( 'Newsletter-Willkommensrabatt (Brevo), Paket 1 – nur erste Bestellung' );
			$coupon->save();
			$new++;
			if ( time() - $start > 20 ) {
				break; // Rest beim nächsten Klick – so bleibt der Aufruf unter Server-Zeitlimits
			}
		}
	}
	foreach ( $codes as $code ) {
		if ( wc_get_coupon_id_by_code( $code ) ) {
			$done[] = $code;
		}
	}
	$missing = count( $codes ) - count( $done );

	nocache_headers();
	echo '<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Willkommenscodes</title>';
	echo '<div style="max-width:560px;margin:48px auto;padding:28px;font:16px/1.5 system-ui,sans-serif;border:1px solid #ddd;border-radius:12px">';
	echo '<h1 style="font-size:22px;margin:0 0 12px">15-%-Willkommenscodes</h1>';
	if ( $new ) {
		echo '<p style="color:#1F7A5C"><b>' . (int) $new . ' Codes neu angelegt.</b></p>';
	}
	echo '<p>Angelegt: <b>' . count( $done ) . ' von ' . count( $codes ) . '</b></p>';
	if ( $missing > 0 ) {
		echo '<form method="post">';
		wp_nonce_field( 'alp_welcome_codes' );
		echo '<button style="padding:12px 20px;font-size:16px;border:0;border-radius:8px;background:#111;color:#fff;cursor:pointer">' . ( $done ? 'Weiter anlegen (' . (int) $missing . ' fehlen)' : 'Codes anlegen (' . (int) $missing . ')' ) . '</button></form>';
		echo '<p style="font-size:13px;color:#666">Je Klick höchstens ca. 20 Sekunden – bei Bedarf einfach erneut klicken, bis alle angelegt sind.</p>';
	} else {
		echo '<p style="color:#1F7A5C"><b>Fertig – alle Codes sind angelegt.</b> Zu sehen unter Marketing → Gutscheine.</p>';
	}
	echo '<p style="font-size:13px;color:#666">Regeln: 15 %, einmal nutzbar, nur erste Bestellung, nicht für reduzierte Produkte, gültig bis ' . esc_html( alp_config( 'welcome_coupon.expires', '2027-09-24 23:59:59' ) ) . '.</p></div>';
	exit;
}
