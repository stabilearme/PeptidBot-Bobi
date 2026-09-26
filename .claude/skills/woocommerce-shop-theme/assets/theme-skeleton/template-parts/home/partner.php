<?php
/**
 * Startseite – Research-Partner (Affiliate): Provision, eigener Code, Partner-Dashboard.
 * Texte: config.php → sections.partner
 */
defined( 'ABSPATH' ) || exit;

$cfg = (array) alp_config( 'sections.partner', array() );
if ( ! $cfg ) {
	return;
}
?>
<section class="alp-partner" id="partner" aria-labelledby="alp-partner-title">
	<div class="alp-container alp-partner__grid">
		<div class="alp-partner__copy">
			<p class="alp-eyebrow"><?php echo esc_html( $cfg['eyebrow'] ?? '' ); ?></p>
			<h2 class="alp-h2" id="alp-partner-title"><?php echo esc_html( $cfg['title'] ?? '' ); ?></h2>
			<p class="alp-partner__text"><?php echo esc_html( $cfg['text'] ?? '' ); ?></p>
			<ul class="alp-partner__benefits">
				<?php foreach ( (array) ( $cfg['benefits'] ?? array() ) as $b ) : ?>
					<li>
						<span class="alp-partner__tick" aria-hidden="true"><?php echo alp_icon( 'check', 14 ); // phpcs:ignore ?></span>
						<span><strong><?php echo esc_html( $b['title'] ); ?></strong><?php echo esc_html( $b['text'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( ! empty( $cfg['cta'] ) ) : ?>
				<?php $wa = ! empty( $cfg['cta']['whatsapp'] ) && function_exists( 'alp_whatsapp_url' ) ? alp_whatsapp_url( $cfg['cta']['whatsapp'] ) : ''; ?>
				<?php if ( $wa ) : ?>
					<a class="alp-btn alp-btn--primary alp-btn--lg alp-partner__cta" href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener"><span class="alp-partner__wa"><?php echo alp_whatsapp_icon( 18 ); // phpcs:ignore ?></span><?php echo esc_html( $cfg['cta']['label'] ); ?> <span class="alp-btn__orb"><?php echo alp_icon( 'arrow', 16 ); // phpcs:ignore ?></span></a>
					<p class="alp-partner__hint">Schreib uns direkt auf WhatsApp – Antwort meist am selben Tag.</p>
				<?php else : ?>
					<a class="alp-btn alp-btn--primary alp-btn--lg" href="<?php echo esc_url( alp_link( $cfg['cta']['url'] ) ); ?>"><?php echo esc_html( $cfg['cta']['label'] ); ?> <span class="alp-btn__orb"><?php echo alp_icon( 'arrow', 16 ); // phpcs:ignore ?></span></a>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<div class="alp-partner__visual" aria-hidden="true">
			<div class="alp-partner__card">
				<div class="alp-partner__card-top">
					<span class="alp-partner__brand">amino<b>labs</b>pro</span>
					<span class="alp-partner__chip-label">Research-Partner</span>
				</div>
				<p class="alp-partner__big"><?php echo esc_html( $cfg['commission'] ?? '15 %' ); ?><small>Provision</small></p>
				<div class="alp-partner__code">
					<span>Dein Code</span>
					<b><?php echo esc_html( $cfg['code'] ?? 'DEINCODE' ); ?></b>
				</div>
				<div class="alp-partner__bars">
					<?php foreach ( array( 38, 52, 46, 64, 58, 76, 88 ) as $h ) : ?>
						<i style="height: <?php echo (int) $h; ?>%"></i>
					<?php endforeach; ?>
				</div>
			</div>
			<span class="alp-partner__float alp-partner__float--a"><?php echo alp_icon( 'check', 14 ); // phpcs:ignore ?> Neue Bestellung über deinen Code</span>
			<span class="alp-partner__float alp-partner__float--b">+ 15 % Provision</span>
		</div>
	</div>
</section>
