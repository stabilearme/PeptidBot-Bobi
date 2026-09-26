<?php
/**
 * Anpassungen am Flatsome-Rahmen: Laufleiste, Footer, Mobile-Navigation.
 * Der Flatsome-Header selbst bleibt erhalten (Menüs, Suche, Konto, Warenkorb)
 * und wird per CSS (assets/css/header.css) gestaltet.
 */

defined( 'ABSPATH' ) || exit;

/*
 * Flatsome-Lazy-Load abschalten (config.php → features.flatsome_lazy_load): Dabei stünde im
 * Bild zuerst nur ein Platzhalter, das echte Bild käme erst per Skript – fällt das Skript aus
 * (Vorschau, Minify, Skriptfehler), bleiben Produktbilder leer. WordPress setzt ohnehin
 * loading="lazy", der Browser lädt Bilder also weiterhin erst beim Scrollen.
 */
add_filter( 'theme_mod_lazy_load_images', 'alp_flatsome_lazy_load' );
function alp_flatsome_lazy_load( $value ) {
	return alp_config( 'features.flatsome_lazy_load', false ) ? $value : 0;
}

/* Laufleiste über dem Header */
add_action( 'flatsome_before_header', 'alp_render_announcement', 5 );
function alp_render_announcement() {
	if ( alp_config( 'features.announcement_bar' ) ) {
		alp_part( 'global/announcement' );
	}
}

/* Eigener Footer statt Flatsome-Footer */
add_action( 'wp', 'alp_swap_footer' );
function alp_swap_footer() {
	if ( ! alp_config( 'features.custom_footer' ) ) {
		return;
	}
	remove_action( 'flatsome_footer', 'flatsome_page_footer', 10 );
	add_action( 'flatsome_footer', 'alp_render_footer', 10 );
}

function alp_render_footer() {
	alp_part( 'footer/footer' );
}

/* Mobile-Navigation + Such-Overlay */
add_action( 'wp_footer', 'alp_render_mobile_nav', 5 );
function alp_render_mobile_nav() {
	if ( alp_config( 'features.mobile_bottom_nav' ) ) {
		alp_part( 'global/mobile-nav' );
	}
	alp_part( 'global/search-sheet' );
}

/* Warenkorb-Zähler in der Mobile-Navigation live aktualisieren */
add_filter( 'woocommerce_add_to_cart_fragments', 'alp_cart_count_fragment' );
function alp_cart_count_fragment( $fragments ) {
	$fragments['span.alp-cart-count'] = alp_cart_count_html();
	return $fragments;
}

function alp_cart_count_html() {
	$count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
	return sprintf( '<span class="alp-cart-count" data-count="%1$d">%1$d</span>', (int) $count );
}
