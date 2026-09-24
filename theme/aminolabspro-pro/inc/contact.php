<?php
/**
 * WhatsApp-Kontakt & Sprachumschalter.
 *
 * WhatsApp-Nummer eintragen: Design → Customizer → „AminoLabs Pro: Kontakt“
 * (ohne Nummer werden alle WhatsApp-Buttons ausgeblendet).
 * Erscheint: im Hauptmenü (Header), als Abschnitt auf der Startseite, als runder Button unten rechts.
 *
 * Sprachumschalter: erscheint automatisch im Menü, sobald TranslatePress oder GTranslate aktiv ist.
 * Styling: assets/css/components.css → „WhatsApp“ / „Sprachumschalter“.
 */

defined( 'ABSPATH' ) || exit;

/* ---------- Einstellungen: als WordPress-Option (Customizer + REST-API /wp/v2/settings) ---------- */
add_action( 'init', 'alp_contact_register_settings' );
function alp_contact_register_settings() {
	register_setting( 'general', 'alp_whatsapp_number', array( 'type' => 'string', 'default' => '', 'sanitize_callback' => 'alp_sanitize_phone', 'show_in_rest' => true, 'description' => 'WhatsApp-Business-Nummer mit Ländervorwahl' ) );
	register_setting( 'general', 'alp_whatsapp_message', array( 'type' => 'string', 'default' => '', 'sanitize_callback' => 'sanitize_text_field', 'show_in_rest' => true, 'description' => 'Vorausgefüllte WhatsApp-Nachricht' ) );
}

/* ---------- Customizer-Felder ---------- */
add_action( 'customize_register', 'alp_contact_customizer' );
function alp_contact_customizer( $wp_customize ) {
	$wp_customize->add_section(
		'alp_contact',
		array(
			'title'       => 'AminoLabs Pro: Kontakt',
			'description' => 'WhatsApp-Business-Nummer mit Ländervorwahl, z. B. 49 151 12345678 (ohne +, ohne führende 0). Leer lassen = WhatsApp-Buttons ausblenden.',
			'priority'    => 30,
		)
	);
	$wp_customize->add_setting( 'alp_whatsapp_number', array( 'type' => 'option', 'default' => '', 'sanitize_callback' => 'alp_sanitize_phone' ) );
	$wp_customize->add_control( 'alp_whatsapp_number', array( 'label' => 'WhatsApp-Nummer', 'section' => 'alp_contact', 'type' => 'text' ) );
	$wp_customize->add_setting( 'alp_whatsapp_message', array( 'type' => 'option', 'default' => alp_config( 'whatsapp.message', '' ), 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'alp_whatsapp_message', array( 'label' => 'Vorausgefüllte Nachricht', 'section' => 'alp_contact', 'type' => 'text' ) );
}

function alp_sanitize_phone( $value ) {
	return preg_replace( '/[^\d+ ]/', '', (string) $value );
}

/**
 * Link zum WhatsApp-Chat (wa.me) oder '' ohne hinterlegte Nummer.
 * $message: eigener vorausgefüllter Text (sonst der aus Customizer/config).
 */
function alp_whatsapp_url( $message = null ) {
	$number = (string) ( get_option( 'alp_whatsapp_number', '' ) ?: get_theme_mod( 'alp_whatsapp_number', '' ) ?: '' );
	if ( '' === trim( $number ) ) {
		$number = (string) alp_config( 'whatsapp.number', '' );
	}
	if ( '' === trim( $number ) && defined( 'ALP_PREVIEW_THEME' ) ) {
		$number = '49 000 0000000'; // nur für die Theme-Vorschau
	}
	$digits = preg_replace( '/\D/', '', $number );
	if ( strlen( $digits ) < 8 ) {
		return '';
	}
	if ( 0 === strpos( $number, '00' ) ) {
		$digits = substr( $digits, 2 );
	} elseif ( 0 === strpos( $digits, '0' ) ) {
		$digits = '49' . substr( $digits, 1 ); // deutsche Nummer ohne Ländervorwahl
	}
	if ( null === $message ) {
		$message = (string) ( get_option( 'alp_whatsapp_message', '' ) ?: get_theme_mod( 'alp_whatsapp_message', '' ) ?: '' );
	}
	if ( '' === $message ) {
		$message = (string) alp_config( 'whatsapp.message', '' );
	}
	return 'https://wa.me/' . $digits . ( $message ? '?text=' . rawurlencode( $message ) : '' );
}

/**
 * WhatsApp-Logo (gefüllt, currentColor).
 */
function alp_whatsapp_icon( $size = 20 ) {
	return sprintf(
		'<svg class="alp-icon alp-icon--wa" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91A9.85 9.85 0 0 0 12.04 2Zm0 18.15h-.01a8.23 8.23 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24a8.2 8.2 0 0 1 8.24 8.25c0 4.54-3.7 8.23-8.24 8.23Zm4.52-6.17c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.24-.64.8-.78.97-.15.16-.29.18-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.01-.38.11-.5.11-.11.25-.29.37-.43.13-.15.17-.25.25-.42.08-.16.04-.31-.02-.43-.06-.13-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.42h-.48a.92.92 0 0 0-.66.31c-.23.25-.87.85-.87 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.1-.23-.16-.48-.29Z"/></svg>',
		(int) $size
	);
}

/**
 * Sprachumschalter eines Übersetzungs-Plugins (TranslatePress / GTranslate) oder ''.
 */
function alp_language_switcher() {
	if ( shortcode_exists( 'language-switcher' ) ) {
		return do_shortcode( '[language-switcher]' );
	}
	if ( shortcode_exists( 'gtranslate' ) ) {
		return do_shortcode( '[gtranslate]' );
	}
	return '';
}

/* ---------- Header: WhatsApp-Button + Sprachumschalter am Ende des Hauptmenüs ---------- */
add_filter( 'wp_nav_menu_items', 'alp_menu_extras', 20, 2 );
function alp_menu_extras( $items, $args ) {
	$location = is_object( $args ) ? ( $args->theme_location ?? '' ) : '';
	if ( ! in_array( $location, array( 'primary', 'primary_mobile' ), true ) ) {
		return $items;
	}
	$lang = alp_language_switcher();
	if ( $lang ) {
		$items .= '<li class="menu-item alp-menu-lang">' . $lang . '</li>';
	}
	$url = alp_whatsapp_url();
	if ( $url ) {
		$items .= alp_whatsapp_menu_item( $url );
	}
	return $items;
}

function alp_whatsapp_menu_item( $url ) {
	return '<li class="menu-item alp-menu-wa"><a class="alp-wa-pill" href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . alp_whatsapp_icon( 18 ) . '<span>WhatsApp</span></a></li>';
}

/* ---------- Runder WhatsApp-Button unten rechts ---------- */
add_action( 'wp_footer', 'alp_render_whatsapp_float', 6 );
function alp_render_whatsapp_float() {
	$url = alp_whatsapp_url();
	if ( ! $url || ! alp_config( 'features.whatsapp_float' ) || ( function_exists( 'is_checkout' ) && is_checkout() ) ) {
		return;
	}
	echo '<a class="alp-wa-float" href="' . esc_url( $url ) . '" target="_blank" rel="noopener" aria-label="Chat auf WhatsApp starten">' . alp_whatsapp_icon( 30 ) . '<span class="alp-wa-float__label">Fragen? Schreib uns</span></a>';
}
