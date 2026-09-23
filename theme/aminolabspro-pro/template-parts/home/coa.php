<?php
/**
 * Startseite – Chargen-Prüfer als dunkle Karte direkt unter den Kennzahlen.
 * Texte: config.php → sections.coa
 */
defined( 'ABSPATH' ) || exit;

$cfg = (array) alp_config( 'sections.coa', array() );
?>
<section class="alp-section alp-section--tint alp-section--snug" id="charge-pruefen">
	<div class="alp-container">
		<div class="alp-check-card">
			<div class="alp-check-card__copy">
				<p class="alp-eyebrow alp-eyebrow--light"><?php echo esc_html( $cfg['eyebrow'] ?? '' ); ?></p>
				<h2 class="alp-h2"><?php echo esc_html( $cfg['title'] ?? '' ); ?></h2>
				<p class="alp-lead"><?php echo esc_html( $cfg['text'] ?? '' ); ?></p>
				<?php if ( ! empty( $cfg['cta'] ) ) : ?>
					<a class="alp-link alp-link--light" href="<?php echo esc_url( alp_link( $cfg['cta']['url'] ) ); ?>"><?php echo esc_html( $cfg['cta']['label'] ); ?> <?php echo alp_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
				<?php endif; ?>
			</div>
			<div class="alp-check-card__form">
				<?php alp_part( 'coa/lookup', array( 'tone' => 'dark' ) ); ?>
			</div>
		</div>
	</div>
</section>
