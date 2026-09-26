<?php
/**
 * Newsletter-Anmeldung über Brevo (wie bisher auf aminolabspro.com).
 *
 * Das Formular schickt die E-Mail-Adresse direkt an das Brevo-Formular
 * (config.php → 'newsletter_form' → 'action') und leitet danach auf die
 * Danke-Seite weiter. Brevo verschickt die Bestätigungsmail (Double-Opt-in)
 * und nach der Bestätigung den 15-%-Code.
 *
 * Erscheint: Startseite (Abschnitt „15 % auf deine erste Bestellung“)
 * und überall per Shortcode [alp_newsletter] (z. B. Seite „Early Access“).
 * Ohne Brevo-Adresse zeigt die Startseite stattdessen einen Button zur Anmeldeseite.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Brevo-Formularadresse (sibforms.com/serve/…) oder ''.
 */
function alp_newsletter_action() {
	$action = (string) alp_config( 'newsletter_form.action', '' );
	return preg_match( '#^https://[a-z0-9-]+\.sibforms\.com/serve/#i', $action ) ? $action : '';
}

/**
 * Anmeldeformular. $args: button, placeholder, class, id.
 */
function alp_newsletter_form( $args = array() ) {
	$action = alp_newsletter_action();
	if ( ! $action ) {
		return '';
	}
	$cfg  = (array) alp_config( 'newsletter_form', array() );
	$args = wp_parse_args(
		$args,
		array(
			'button'      => $cfg['button'] ?? 'Anmelden',
			'placeholder' => $cfg['placeholder'] ?? 'deine@email.de',
			'class'       => '',
			'id'          => 'alp-nl-' . wp_unique_id(),
		)
	);
	$thanks = alp_link( $cfg['thanks'] ?? '/newsletter-vielen-dank/' );

	ob_start();
	?>
	<form class="alp-nl-form <?php echo esc_attr( $args['class'] ); ?>" action="<?php echo esc_url( $action ); ?>" method="post" data-alp-newsletter data-thanks="<?php echo esc_url( $thanks ); ?>">
		<div class="alp-nl-form__row">
			<label class="screen-reader-text" for="<?php echo esc_attr( $args['id'] ); ?>">E-Mail-Adresse</label>
			<input class="alp-nl-form__input" id="<?php echo esc_attr( $args['id'] ); ?>" type="email" name="EMAIL" required autocomplete="email" inputmode="email" placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>">
			<button class="alp-btn alp-btn--light alp-nl-form__btn" type="submit"><?php echo alp_icon( 'mail', 18 ); // phpcs:ignore ?> <span><?php echo esc_html( $args['button'] ); ?></span></button>
		</div>
		<?php /* Felder, die Brevo erwartet: Spam-Falle (muss leer bleiben), Sprache, Formulartyp. */ ?>
		<input class="alp-nl-form__trap" type="text" name="email_address_check" value="" tabindex="-1" autocomplete="off" aria-hidden="true">
		<input type="hidden" name="locale" value="de">
		<input type="hidden" name="html_type" value="simple">
		<?php if ( ! empty( $cfg['note'] ) ) : ?>
			<p class="alp-nl-form__note"><?php echo wp_kses( $cfg['note'], array( 'a' => array( 'href' => array() ) ) ); ?></p>
		<?php endif; ?>
	</form>
	<?php
	return ob_get_clean();
}

add_shortcode( 'alp_newsletter', 'alp_newsletter_shortcode' );
function alp_newsletter_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'button' => '', 'placeholder' => '' ), $atts, 'alp_newsletter' );
	return alp_newsletter_form( array_filter( $atts ) + array( 'class' => 'is-standalone' ) );
}

/*
 * ---------- 15-%-Einblendung ----------
 * Versteckt im Footer ausgegeben, theme.js (initOffer) blendet sie ein:
 * nach config 'welcome_popup.delay' Sekunden oder am PC beim Verlassen der Seite.
 * Wer schließt, hat 'snooze_days' Tage Ruhe; wer sich anmeldet, sieht sie nie wieder.
 */
add_action( 'wp_footer', 'alp_render_welcome_popup', 7 );
function alp_render_welcome_popup() {
	if ( ! alp_config( 'features.welcome_popup' ) || ! alp_newsletter_action() || is_customize_preview() ) {
		return;
	}
	if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
		return;
	}
	$cfg    = (array) alp_config( 'welcome_popup', array() );
	$thanks = (string) alp_config( 'newsletter_form.thanks', '/newsletter-vielen-dank/' );
	if ( is_page( trim( $thanks, '/' ) ) ) {
		return;
	}
	?>
	<div class="alp-offer" id="alp-offer" role="dialog" aria-labelledby="alp-offer-title" data-delay="<?php echo (int) ( $cfg['delay'] ?? 30 ); ?>" data-snooze="<?php echo (int) ( $cfg['snooze_days'] ?? 30 ); ?>" hidden>
		<div class="alp-offer__top">
			<p class="alp-offer__amount"><?php echo esc_html( $cfg['amount'] ?? '15 %' ); ?></p>
			<button type="button" class="alp-offer__close" data-alp-offer-close aria-label="Schließen"><?php echo alp_icon( 'close', 18 ); // phpcs:ignore ?></button>
		</div>
		<p class="alp-offer__title" id="alp-offer-title"><?php echo esc_html( $cfg['title'] ?? '' ); ?></p>
		<p class="alp-offer__text"><?php echo esc_html( $cfg['text'] ?? '' ); ?></p>
		<?php
		echo alp_newsletter_form( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'button' => $cfg['button'] ?? 'Code sichern',
				'class'  => 'is-standalone is-offer',
				'id'     => 'alp-offer-email',
			)
		);
		?>
		<?php if ( ! empty( $cfg['fine'] ) ) : ?>
			<p class="alp-offer__fine"><?php echo esc_html( $cfg['fine'] ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}
