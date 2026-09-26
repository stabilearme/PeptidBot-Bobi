<?php
/**
 * Startseite – Newsletter mit 15 % Willkommensrabatt.
 * Mit Brevo-Formular (config.php → newsletter_form) direkt zum Eintragen,
 * sonst Button zur Anmeldeseite. Texte: config.php → sections.newsletter
 */
defined( 'ABSPATH' ) || exit;

$cfg = (array) alp_config( 'sections.newsletter', array() );
if ( empty( $cfg['title'] ) ) {
	return;
}
$form = alp_newsletter_form();
?>
<section class="alp-section alp-section--tight" id="newsletter">
	<div class="alp-container">
		<div class="alp-cta<?php echo $form ? ' alp-cta--form' : ''; ?>">
			<div class="alp-cta__copy">
				<h2 class="alp-h2"><?php echo esc_html( $cfg['title'] ); ?></h2>
				<p><?php echo esc_html( $cfg['text'] ?? '' ); ?></p>
			</div>
			<?php if ( $form ) : ?>
				<?php echo $form; // phpcs:ignore WordPress.Security.EscapeOutput -- in alp_newsletter_form() escaped ?>
			<?php elseif ( ! empty( $cfg['cta'] ) ) : ?>
				<a class="alp-btn alp-btn--light alp-btn--lg" href="<?php echo esc_url( alp_link( $cfg['cta']['url'] ) ); ?>"><?php echo alp_icon( 'mail', 18 ); // phpcs:ignore ?> <?php echo esc_html( $cfg['cta']['label'] ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
