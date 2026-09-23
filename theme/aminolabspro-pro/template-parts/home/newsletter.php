<?php
/**
 * Startseite – Newsletter-Aufruf (führt zur bestehenden Brevo-Anmeldeseite).
 * Texte: config.php → sections.newsletter
 */
defined( 'ABSPATH' ) || exit;

$cfg = (array) alp_config( 'sections.newsletter', array() );
if ( empty( $cfg['title'] ) ) {
	return;
}
?>
<section class="alp-section alp-section--tight">
	<div class="alp-container">
		<div class="alp-cta">
			<div class="alp-cta__copy">
				<h2 class="alp-h2"><?php echo esc_html( $cfg['title'] ); ?></h2>
				<p><?php echo esc_html( $cfg['text'] ?? '' ); ?></p>
			</div>
			<?php if ( ! empty( $cfg['cta'] ) ) : ?>
				<a class="alp-btn alp-btn--light alp-btn--lg" href="<?php echo esc_url( alp_link( $cfg['cta']['url'] ) ); ?>"><?php echo alp_icon( 'mail', 18 ); // phpcs:ignore ?> <?php echo esc_html( $cfg['cta']['label'] ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
