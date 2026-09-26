<?php
/**
 * Einheitliche Darstellung von Seiten und Beiträgen (Wissen, Kontakt, Versand, Rechtstexte …).
 *
 * Viele Seiten enthalten eigene <style>-Blöcke und Inline-Styles aus dem alten Theme
 * (andere Schriften, feste Farben, teils kaputtes CSS). Dieses Modul entfernt sie beim
 * Anzeigen und vergibt stattdessen Theme-Klassen. In der Datenbank wird NICHTS verändert –
 * Schalter aus (config.php → features.clean_pages) = alter Zustand.
 *
 * Styling: assets/css/content.css (Textformatierung) und assets/css/pages.css (Seitentypen).
 */

defined( 'ABSPATH' ) || exit;

/** Seiten, die ihre eigenen Theme-Vorlagen haben oder von WooCommerce stammen. */
function alp_content_is_managed() {
	if ( ! alp_config( 'features.clean_pages' ) || ! in_the_loop() ) {
		return false;
	}
	if ( is_front_page() || is_page( 'coa' ) ) {
		return false;
	}
	if ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
		return false;
	}
	return is_page() || is_single();
}

add_filter( 'the_content', 'alp_clean_page_content', 21 );
function alp_clean_page_content( $html ) {
	if ( ! alp_content_is_managed() ) {
		return $html;
	}

	// Alte <style>-Blöcke entfernen – auch einen nicht geschlossenen am Ende des Inhalts.
	$html = preg_replace( '#<style\b[^>]*>.*?(?:</style>|$)#is', '', $html );

	// COA-Leitfaden: leeren Platzhalter durch das Chromatogramm des Themes ersetzen.
	$html = preg_replace( '#(<div class="alp3-chromatogram"[^>]*>)\s*</div>#', '$1' . alp_chromatogram( 'coa-guide', 98.6 ) . '</div>', $html );

	if ( class_exists( 'DOMDocument' ) && false !== strpos( $html, 'style=' ) ) {
		$html = alp_normalize_description_html( $html );
	}

	$slug = get_post_field( 'post_name', get_the_ID() );
	return '<div class="alp-desc alp-rich alp-rich--' . sanitize_html_class( $slug ) . '">' . $html . '</div>';
}

/*
 * Seiten-Auszug nicht über dem Inhalt anzeigen (Flatsome gibt ihn sonst als lose Zeile aus,
 * z. B. auf „Wissen“). Nur im sichtbaren Bereich der aufgerufenen Seite – im <head> (SEO,
 * Rank Math) und für andere Beiträge bleibt der Auszug unverändert.
 */
add_filter( 'get_the_excerpt', 'alp_hide_page_excerpt_in_body', 99, 2 );
function alp_hide_page_excerpt_in_body( $excerpt, $post = null ) {
	if ( is_admin() || ! is_page() || ! did_action( 'wp_body_open' ) || doing_action( 'wp_head' ) ) {
		return $excerpt;
	}
	$post = get_post( $post );
	return ( $post && (int) $post->ID === (int) get_queried_object_id() ) ? '' : $excerpt;
}
