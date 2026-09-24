<?php
/**
 * WooCommerce: Shop, Produktseite, Warenkorb & Kasse.
 * Es werden bewusst KEINE WooCommerce-Templates überschrieben, nur Hooks genutzt –
 * dadurch bleiben Updates von WooCommerce, Flatsome und Germanized problemlos.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/* =========================================================
 * Shop & Kategorien
 * ======================================================= */

/* Kategorie-Leiste über der Produktliste */
add_action( 'woocommerce_before_shop_loop', 'alp_category_pills', 5 );
function alp_category_pills() {
	if ( alp_config( 'features.category_pills' ) && ( is_shop() || is_product_category() ) ) {
		alp_part( 'shop/category-pills' );
	}
}

/* Einleitung + Vorteile über der Produktliste */
add_action( 'woocommerce_before_shop_loop', 'alp_shop_intro', 3 );
function alp_shop_intro() {
	if ( alp_config( 'features.shop_intro' ) && ( is_shop() || is_product_category() ) ) {
		alp_part( 'shop/intro' );
	}
}

/* Kleiner Laborhinweis in jeder Produktkachel */
add_action( 'woocommerce_after_shop_loop_item_title', 'alp_loop_lab_badge', 4 );
function alp_loop_lab_badge() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	$batch = alp_coa_for_sku( $product->get_sku() );
	if ( $batch && $batch['done'] ) {
		printf(
			'<p class="alp-card-lab">%s<span>COA · %s %% HPLC</span></p>',
			alp_icon( 'check', 14 ), // phpcs:ignore WordPress.Security.EscapeOutput
			esc_html( alp_num( $batch['purity'], 0 ) )
		);
	} elseif ( $batch ) {
		printf( '<p class="alp-card-lab is-pending">%s<span>Laborprüfung läuft</span></p>', alp_icon( 'clock', 14 ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/* Ähnliche Produkte: 4 Stück in einer Reihe */
add_filter( 'woocommerce_output_related_products_args', 'alp_related_args' );
function alp_related_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
}

/* =========================================================
 * Produktseite
 * ======================================================= */

/* Merkmale unter dem Titel */
add_action( 'woocommerce_single_product_summary', 'alp_product_chips', 6 );
function alp_product_chips() {
	$chips = (array) alp_config( 'product.chips', array() );
	if ( ! $chips ) {
		return;
	}
	echo '<ul class="alp-chips alp-chips--summary">';
	foreach ( $chips as $chip ) {
		echo '<li class="alp-chip">' . esc_html( $chip ) . '</li>';
	}
	echo '</ul>';
}

/* „Versand heute“-Countdown (Berechnung im Browser, funktioniert auch mit Seiten-Cache) */
add_action( 'woocommerce_single_product_summary', 'alp_shipping_countdown', 29 );
function alp_shipping_countdown() {
	global $product;
	if ( ! $product instanceof WC_Product || ! $product->is_in_stock() ) {
		return;
	}
	printf(
		'<p class="alp-ship" data-alp-cutoff="%d" hidden>%s<span class="alp-ship__text"></span></p>',
		(int) alp_config( 'shipping_cutoff_hour', 14 ),
		alp_icon( 'truck', 18 ) // phpcs:ignore WordPress.Security.EscapeOutput
	);
}

/* Vertrauensliste unter Warenkorb + Chargen-Box */
add_action( 'woocommerce_single_product_summary', 'alp_product_trust', 36 );
function alp_product_trust() {
	alp_part( 'product/trust' );
}

/* Sticky-Kaufleiste */
add_action( 'wp_footer', 'alp_sticky_add_to_cart', 4 );
function alp_sticky_add_to_cart() {
	if ( ! alp_config( 'features.sticky_add_to_cart' ) || ! is_product() ) {
		return;
	}
	$product = wc_get_product( get_queried_object_id() );
	if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return;
	}
	alp_part( 'product/sticky-bar', array( 'product' => $product ) );
}

/* Keine Mobile-Navigation auf Produktseiten (dort erscheint die Kaufleiste) */
add_filter( 'body_class', 'alp_product_body_class', 20 );
function alp_product_body_class( $classes ) {
	if ( function_exists( 'is_product' ) && is_product() && alp_config( 'features.sticky_add_to_cart' ) ) {
		$classes[] = 'alp-has-sticky-buy';
	}
	return $classes;
}

/* =========================================================
 * Warenkorb & Kasse
 * ======================================================= */

add_action( 'woocommerce_before_cart', 'alp_steps_cart', 1 );
function alp_steps_cart() {
	alp_checkout_steps( 1 );
}

add_action( 'woocommerce_before_checkout_form', 'alp_steps_checkout', 1 );
function alp_steps_checkout() {
	alp_checkout_steps( 2 );
}

add_action( 'woocommerce_before_thankyou', 'alp_steps_thankyou', 1 );
function alp_steps_thankyou() {
	alp_checkout_steps( 3 );
}

function alp_checkout_steps( $current ) {
	if ( alp_config( 'features.checkout_steps' ) ) {
		alp_part( 'shop/checkout-steps', array( 'current' => $current ) );
	}
}

/* Fortschritt bis zum kostenlosen Versand */
add_action( 'woocommerce_before_cart_table', 'alp_free_shipping_progress', 5 );
function alp_free_shipping_progress() {
	$threshold = (float) alp_config( 'free_shipping_threshold', 0 );
	if ( $threshold <= 0 || ! WC()->cart ) {
		return;
	}
	$total   = (float) WC()->cart->get_displayed_subtotal();
	$missing = max( 0, $threshold - $total );
	$percent = min( 100, round( $total / $threshold * 100 ) );
	?>
	<div class="alp-freeship">
		<p>
			<?php
			if ( $missing > 0 ) {
				/* translators: %s: amount */
				printf( esc_html__( 'Noch %s bis zum kostenlosen Versand.', 'aminolabspro-pro' ), wp_kses_post( wc_price( $missing ) ) );
			} else {
				esc_html_e( 'Dein Versand ist kostenlos.', 'aminolabspro-pro' );
			}
			?>
		</p>
		<div class="alp-freeship__bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo (int) $percent; ?>"><span style="width:<?php echo (int) $percent; ?>%"></span></div>
	</div>
	<?php
}

/* Vertrauenshinweise unter der Bestellübersicht an der Kasse */
add_action( 'woocommerce_review_order_after_submit', 'alp_checkout_trust' );
function alp_checkout_trust() {
	echo '<ul class="alp-checkout-trust">';
	foreach ( array( 'lock' => 'Verschlüsselte Übertragung', 'truck' => 'Versand aus Deutschland', 'doc' => 'Öffentliche Laborzertifikate' ) as $icon => $text ) {
		echo '<li>' . alp_icon( $icon, 16 ) . esc_html( $text ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</ul>';
}

/**
 * Pflichtangaben zum Preis (Steuer + Versand) für Stellen, an denen das Theme selbst
 * einen Preis zeigt (Kaufleiste). Kommt aus Germanized, damit dort hinterlegte Texte
 * (z. B. Kleinunternehmer nach § 19 UStG) gelten; ohne Germanized aus config.php.
 */
function alp_price_legal_note( $product ) {
	if ( function_exists( 'wc_gzd_get_product' ) ) {
		$gzd   = wc_gzd_get_product( $product );
		$parts = array();
		if ( $gzd && method_exists( $gzd, 'get_tax_info' ) ) {
			$parts[] = wp_strip_all_tags( (string) $gzd->get_tax_info() );
		}
		if ( $gzd && method_exists( $gzd, 'get_shipping_costs_html' ) ) {
			$parts[] = wp_strip_all_tags( (string) $gzd->get_shipping_costs_html() );
		}
		$parts = array_filter( array_map( 'trim', $parts ) );
		if ( $parts ) {
			return esc_html( implode( ', ', $parts ) );
		}
	}
	return esc_html( alp_config( 'product.price_note', 'Kein Ausweis der USt. (Kleinunternehmer, § 19 UStG), zzgl. Versand' ) );
}

/**
 * Bundesland/Kanton als Pflichtfeld für bestimmte Länder (config.php → 'checkout_state_required').
 * Stripe lehnt z. B. Schweizer Zahlungen ohne Kanton ab („Fehlendes Kundenfeld address->state“),
 * WooCommerce hält das Feld für die Schweiz aber für optional.
 */
add_filter( 'woocommerce_get_country_locale', 'alp_require_state_fields' );
function alp_require_state_fields( $locale ) {
	foreach ( (array) alp_config( 'checkout_state_required', array() ) as $country ) {
		$locale[ $country ]['state']['required'] = true;
	}
	return $locale;
}

/*
 * ---------- Übernommen aus dem bisherigen Child Theme („AminoLabs Pro Child“) ----------
 * Damit sich auf aminolabspro.com beim Theme-Wechsel an Kasse und Produktseite nichts ändert.
 * Texte/Schalter: config.php → 'checkout' und 'product.show_short_description'.
 */

// Kurzbeschreibungen bleiben ausgeblendet (wie bisher); die ausführliche Beschreibung steht im Reiter.
if ( ! alp_config( 'product.show_short_description', false ) ) {
	add_filter( 'woocommerce_short_description', '__return_empty_string' );
	add_filter( 'woocommerce_product_get_short_description', '__return_empty_string' );
}

// Feld „Anmerkungen zur Bestellung“ an der Kasse.
add_filter( 'woocommerce_enable_order_notes_field', '__return_true' );

// Bestell-Button mit gesetzlich geforderter Beschriftung („Button-Lösung“, § 312j BGB).
add_filter( 'woocommerce_order_button_text', 'alp_order_button_text' );
function alp_order_button_text( $text ) {
	$custom = (string) alp_config( 'checkout.order_button_text', '' );
	return '' !== $custom ? $custom : $text;
}

// Text der AGB-Checkbox mit Links zu AGB, Datenschutz und Widerruf.
add_filter( 'woocommerce_checkout_terms_and_conditions_checkbox_text', 'alp_terms_checkbox_text' );
function alp_terms_checkbox_text( $text ) {
	$custom = (string) alp_config( 'checkout.terms_text', '' );
	return '' !== $custom ? wp_kses_post( $custom ) : $text;
}

/*
 * Germanized hängt Steuer-/Versandhinweise in der Produktliste hinter die Kachel
 * (woocommerce_after_shop_loop_item). Hier rücken sie direkt unter den Preis in die Kachel –
 * gesetzlich „in unmittelbarer Nähe“ des Preises und optisch Teil der Karte.
 */
add_action( 'wp', 'alp_move_gzd_loop_info', 99 );
function alp_move_gzd_loop_info() {
	global $wp_filter;
	if ( empty( $wp_filter['woocommerce_after_shop_loop_item'] ) ) {
		return;
	}
	foreach ( $wp_filter['woocommerce_after_shop_loop_item']->callbacks as $priority => $callbacks ) {
		foreach ( $callbacks as $callback ) {
			$fn = $callback['function'];
			if ( is_string( $fn ) && 0 === strpos( $fn, 'woocommerce_gzd_template_loop_' ) ) {
				remove_action( 'woocommerce_after_shop_loop_item', $fn, $priority );
				add_action( 'woocommerce_after_shop_loop_item_title', $fn, 20 + (int) $priority );
			}
		}
	}
}
