<?php
/**
 * Wunsch-Gutscheincode für Affiliates (YITH WooCommerce Affiliates).
 *
 * - Im Affiliate-Anmeldeformular (und bei „Affiliate werden“ für eingeloggte Kunden)
 *   gibt es ein Feld „Wunsch-Gutscheincode“.
 * - Ist der Code schon vergeben (als Gutschein oder von einem anderen Affiliate reserviert),
 *   erscheint ein Hinweis und die Anmeldung wird nicht abgeschickt.
 * - Der Code wird erst als WooCommerce-Gutschein angelegt, wenn der Affiliate genehmigt ist,
 *   und dem Affiliate zugeordnet (YITH-Feld „coupon_referrer“ → Provision bei Einlösung).
 * - Wird der Affiliate später deaktiviert, wird der Gutschein auf Entwurf gesetzt (ungültig);
 *   bei erneuter Freigabe wieder aktiv.
 *
 * Einstellungen: config.php → 'affiliate_coupon'.
 */

defined( 'ABSPATH' ) || exit;

define( 'ALP_AFF_WISH_META', 'alp_aff_coupon_wish' );
define( 'ALP_AFF_COUPON_META', 'alp_aff_coupon_id' );

function alp_aff_coupon_on() {
	return (bool) alp_config( 'affiliate_coupon.enabled', false );
}

/** Code vereinheitlichen: Großbuchstaben, keine Leerzeichen. */
function alp_aff_normalize_code( $code ) {
	return strtoupper( preg_replace( '/\s+/', '', sanitize_text_field( (string) $code ) ) );
}

/** true, wenn es einen Gutschein mit diesem Code gibt (auch Entwurf/ausstehend, nicht Papierkorb). */
function alp_aff_coupon_exists( $code ) {
	global $wpdb;
	return (bool) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'shop_coupon' AND post_status <> 'trash' AND LOWER(post_title) = %s LIMIT 1",
			strtolower( $code )
		)
	);
}

/** true, wenn ein anderer Nutzer sich den Code schon gewünscht hat (Anmeldung wartet auf Genehmigung). */
function alp_aff_code_reserved( $code, $except_user = 0 ) {
	$users = get_users(
		array(
			'meta_key'   => ALP_AFF_WISH_META, // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value' => $code, // phpcs:ignore WordPress.DB.SlowDBQuery
			'fields'     => 'ID',
			'number'     => 2,
		)
	);
	return (bool) array_diff( array_map( 'intval', $users ), array( (int) $except_user ) );
}

/**
 * Prüft einen Wunschcode. Gibt '' zurück, wenn alles passt, sonst die Fehlermeldung.
 */
function alp_aff_code_error( $code, $user_id = 0 ) {
	$min = (int) alp_config( 'affiliate_coupon.min_length', 4 );
	$max = (int) alp_config( 'affiliate_coupon.max_length', 20 );
	if ( '' === $code ) {
		return 'Bitte gib deinen Wunsch-Gutscheincode ein.';
	}
	if ( ! preg_match( '/^[A-Z0-9][A-Z0-9\-]*$/', $code ) || strlen( $code ) < $min || strlen( $code ) > $max ) {
		return sprintf( 'Der Gutscheincode darf nur Buchstaben, Zahlen und Bindestriche enthalten und muss %d bis %d Zeichen lang sein.', $min, $max );
	}
	foreach ( (array) alp_config( 'affiliate_coupon.reserved_prefixes', array() ) as $prefix ) {
		if ( $prefix && 0 === stripos( $code, $prefix ) ) {
			return sprintf( 'Codes, die mit „%s“ beginnen, sind für den Shop reserviert. Bitte wähle einen anderen.', strtoupper( $prefix ) );
		}
	}
	if ( alp_aff_coupon_exists( $code ) || alp_aff_code_reserved( $code, $user_id ) ) {
		return sprintf( 'Der Gutscheincode „%s“ ist leider schon vergeben. Bitte wähle einen anderen.', $code );
	}
	return '';
}

/* ---------- Feld im Formular ---------- */
add_action( 'yith_wcaf_register_form', 'alp_aff_coupon_field' );
add_action( 'yith_wcaf_become_an_affiliate_form', 'alp_aff_coupon_field' );
function alp_aff_coupon_field() {
	if ( ! alp_aff_coupon_on() || ! function_exists( 'woocommerce_form_field' ) ) {
		return;
	}
	$value = isset( $_POST['alp_aff_coupon'] ) ? alp_aff_normalize_code( wp_unslash( $_POST['alp_aff_coupon'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	woocommerce_form_field(
		'alp_aff_coupon',
		array(
			'type'              => 'text',
			'label'             => alp_config( 'affiliate_coupon.label', 'Wunsch-Gutscheincode' ),
			'description'       => alp_config( 'affiliate_coupon.hint', '' ),
			'placeholder'       => alp_config( 'affiliate_coupon.placeholder', 'z. B. MAX10' ),
			'required'          => true,
			'maxlength'         => (int) alp_config( 'affiliate_coupon.max_length', 20 ),
			'custom_attributes' => array(
				'autocomplete'   => 'off',
				'autocapitalize' => 'characters',
				'spellcheck'     => 'false',
				'pattern'        => '[A-Za-z0-9\-]+',
			),
			'input_class'       => array( 'alp-aff-coupon-input' ),
		),
		$value
	);
}

/* ---------- Prüfen: neue Anmeldung (WooCommerce-Registrierung mit Affiliate-Formular) ---------- */
add_filter( 'woocommerce_process_registration_errors', 'alp_aff_coupon_validate_registration', 20 );
function alp_aff_coupon_validate_registration( $errors ) {
	if ( ! alp_aff_coupon_on() || ! isset( $_POST['register_affiliate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return $errors;
	}
	$code  = alp_aff_normalize_code( wp_unslash( $_POST['alp_aff_coupon'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification
	$error = alp_aff_code_error( $code );
	if ( $error && is_wp_error( $errors ) ) {
		$errors->add( 'alp_aff_coupon', $error );
	}
	return $errors;
}

/* ---------- Prüfen: „Affiliate werden“ (eingeloggter Kunde) – vor der Verarbeitung durch YITH ---------- */
add_action( 'wp_loaded', 'alp_aff_coupon_validate_become', 5 );
function alp_aff_coupon_validate_become() {
	if ( ! alp_aff_coupon_on() || empty( $_POST['become_an_affiliate'] ) || ! is_user_logged_in() ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$code  = alp_aff_normalize_code( wp_unslash( $_POST['alp_aff_coupon'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification
	$error = alp_aff_code_error( $code, get_current_user_id() );
	if ( $error ) {
		if ( function_exists( 'wc_add_notice' ) ) {
			wc_add_notice( $error, 'error' );
		}
		// YITH verarbeitet das Formular nur mit diesem Feld – ohne es bleibt der Kunde auf der Seite.
		unset( $_POST['become_an_affiliate'], $_REQUEST['become_an_affiliate'] );
	}
}

/* ---------- Speichern, sobald YITH den Affiliate angelegt hat ---------- */
add_action( 'yith_wcaf_new_affiliate', 'alp_aff_coupon_store_wish', 10, 2 );
function alp_aff_coupon_store_wish( $affiliate_id, $affiliate = null ) {
	if ( ! alp_aff_coupon_on() || ! isset( $_POST['alp_aff_coupon'] ) || ! is_object( $affiliate ) || ! method_exists( $affiliate, 'get_user_id' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$user_id = (int) $affiliate->get_user_id();
	$code    = alp_aff_normalize_code( wp_unslash( $_POST['alp_aff_coupon'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	if ( ! $user_id || alp_aff_code_error( $code, $user_id ) ) {
		return;
	}
	update_user_meta( $user_id, ALP_AFF_WISH_META, $code );
	// Wird automatisch freigeschaltet (YITH-Einstellung), gibt es keinen Statuswechsel → gleich anlegen.
	if ( method_exists( $affiliate, 'has_status' ) && $affiliate->has_status( 'enabled' ) ) {
		alp_aff_coupon_activate( $affiliate_id, $affiliate );
	}
}

/* ---------- Genehmigt → Gutschein anlegen bzw. wieder aktivieren ---------- */
add_action( 'yith_wcaf_affiliate_enabled', 'alp_aff_coupon_activate', 10, 2 );
function alp_aff_coupon_activate( $affiliate_id, $affiliate = null ) {
	if ( ! alp_aff_coupon_on() || ! class_exists( 'WC_Coupon' ) ) {
		return;
	}
	if ( ! is_object( $affiliate ) && class_exists( 'YITH_WCAF_Affiliate_Factory' ) ) {
		$affiliate = YITH_WCAF_Affiliate_Factory::get_affiliate_by_id( $affiliate_id );
	}
	if ( ! is_object( $affiliate ) || ! method_exists( $affiliate, 'get_user_id' ) ) {
		return;
	}
	$user_id = (int) $affiliate->get_user_id();

	// Gab es schon einen Gutschein (Affiliate war zwischendurch deaktiviert)? → wieder veröffentlichen.
	$existing = (int) get_user_meta( $user_id, ALP_AFF_COUPON_META, true );
	if ( $existing && 'shop_coupon' === get_post_type( $existing ) ) {
		if ( 'publish' !== get_post_status( $existing ) ) {
			wp_update_post( array( 'ID' => $existing, 'post_status' => 'publish' ) );
		}
		alp_aff_coupon_clear_cache();
		return;
	}

	$code = (string) get_user_meta( $user_id, ALP_AFF_WISH_META, true );
	if ( '' === $code ) {
		return;
	}
	// Falls der Code inzwischen doch vergeben wurde (z. B. von Hand angelegt): Zahl anhängen.
	$final = $code;
	for ( $i = 2; alp_aff_coupon_exists( $final ) && $i < 50; $i++ ) {
		$final = $code . '-' . $i;
	}

	$user   = get_userdata( $user_id );
	$cfg    = (array) alp_config( 'affiliate_coupon', array() );
	$coupon = new WC_Coupon();
	$coupon->set_code( $final );
	$coupon->set_discount_type( $cfg['discount_type'] ?? 'percent' );
	$coupon->set_amount( (string) ( $cfg['amount'] ?? 10 ) );
	$coupon->set_individual_use( ! empty( $cfg['individual_use'] ) );
	$coupon->set_exclude_sale_items( ! empty( $cfg['exclude_sale_items'] ) );
	if ( ! empty( $cfg['usage_limit_per_user'] ) ) {
		$coupon->set_usage_limit_per_user( (int) $cfg['usage_limit_per_user'] );
	}
	$coupon->set_description( sprintf( 'Affiliate-Code von %s (%s), automatisch angelegt nach Genehmigung.', $user ? $user->display_name : '#' . $user_id, $user ? $user->user_email : '' ) );
	$coupon->update_meta_data( 'coupon_referrer', (int) $affiliate_id ); // YITH: Provision für diesen Affiliate
	$coupon_id = $coupon->save();

	if ( $coupon_id ) {
		update_user_meta( $user_id, ALP_AFF_COUPON_META, (int) $coupon_id );
		delete_user_meta( $user_id, ALP_AFF_WISH_META );
		if ( $final !== $code ) {
			update_user_meta( $user_id, 'alp_aff_coupon_note', sprintf( 'Wunschcode %s war vergeben, angelegt als %s.', $code, $final ) );
		}
		alp_aff_coupon_clear_cache();
	}
}

/* ---------- Deaktiviert → Gutschein ungültig (Entwurf) ---------- */
add_action( 'yith_wcaf_affiliate_disabled', 'alp_aff_coupon_deactivate', 10, 2 );
function alp_aff_coupon_deactivate( $affiliate_id, $affiliate = null ) {
	if ( ! is_object( $affiliate ) && class_exists( 'YITH_WCAF_Affiliate_Factory' ) ) {
		$affiliate = YITH_WCAF_Affiliate_Factory::get_affiliate_by_id( $affiliate_id );
	}
	if ( ! is_object( $affiliate ) || ! method_exists( $affiliate, 'get_user_id' ) ) {
		return;
	}
	$coupon_id = (int) get_user_meta( (int) $affiliate->get_user_id(), ALP_AFF_COUPON_META, true );
	if ( $coupon_id && 'publish' === get_post_status( $coupon_id ) ) {
		wp_update_post( array( 'ID' => $coupon_id, 'post_status' => 'draft' ) );
		alp_aff_coupon_clear_cache();
	}
}

function alp_aff_coupon_clear_cache() {
	if ( class_exists( 'WC_Data_Store' ) ) {
		try {
			WC_Data_Store::load( 'affiliate_coupon' )->clear_cache();
		} catch ( Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement
			// YITH-Gutscheinverwaltung nicht aktiv – nichts zu tun.
		}
	}
}

/* ---------- Admin: Wunschcode im Benutzerprofil anzeigen ---------- */
add_action( 'show_user_profile', 'alp_aff_coupon_profile' );
add_action( 'edit_user_profile', 'alp_aff_coupon_profile' );
function alp_aff_coupon_profile( $user ) {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}
	$wish      = (string) get_user_meta( $user->ID, ALP_AFF_WISH_META, true );
	$coupon_id = (int) get_user_meta( $user->ID, ALP_AFF_COUPON_META, true );
	$note      = (string) get_user_meta( $user->ID, 'alp_aff_coupon_note', true );
	if ( ! $wish && ! $coupon_id ) {
		return;
	}
	echo '<h2>Affiliate-Gutscheincode</h2><table class="form-table"><tr><th>Status</th><td>';
	if ( $coupon_id ) {
		printf(
			'Gutschein <a href="%s"><strong>%s</strong></a> (%s)',
			esc_url( admin_url( 'post.php?post=' . $coupon_id . '&action=edit' ) ),
			esc_html( strtoupper( get_the_title( $coupon_id ) ) ),
			'publish' === get_post_status( $coupon_id ) ? 'aktiv' : 'inaktiv'
		);
	} else {
		printf( 'Wunschcode <strong>%s</strong> – wird angelegt, sobald der Affiliate genehmigt ist.', esc_html( $wish ) );
	}
	if ( $note ) {
		echo '<br><em>' . esc_html( $note ) . '</em>';
	}
	echo '</td></tr></table>';
}
