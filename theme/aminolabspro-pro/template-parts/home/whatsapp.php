<?php
/**
 * Startseite – WhatsApp-Kontakt als auffälliges Band.
 * Texte: config.php → 'whatsapp'. Nummer: Customizer → „AminoLabs Pro: Kontakt“.
 */
defined( 'ABSPATH' ) || exit;

$url = alp_whatsapp_url();
if ( ! $url ) {
	return;
}
$cfg = (array) alp_config( 'whatsapp', array() );
?>
<section class="alp-wa-band" aria-label="WhatsApp-Kontakt">
	<div class="alp-container alp-wa-band__inner">
		<span class="alp-wa-band__icon" aria-hidden="true"><?php echo alp_whatsapp_icon( 38 ); // phpcs:ignore ?></span>
		<div class="alp-wa-band__copy">
			<p class="alp-wa-band__kicker"><span class="alp-wa-band__live" aria-hidden="true"></span>WhatsApp Business</p>
			<h2 class="alp-wa-band__title"><?php echo esc_html( $cfg['title'] ?? '' ); ?></h2>
			<p class="alp-wa-band__text"><?php echo esc_html( $cfg['text'] ?? '' ); ?></p>
		</div>
		<a class="alp-wa-band__btn" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener">
			<?php echo alp_whatsapp_icon( 22 ); // phpcs:ignore ?>
			<span><?php echo esc_html( $cfg['button'] ?? 'Chat starten' ); ?></span>
		</a>
	</div>
</section>
