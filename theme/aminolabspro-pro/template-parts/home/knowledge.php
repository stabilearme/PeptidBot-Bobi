<?php
/**
 * Startseite – Wissen & Tools. Texte: config.php → sections.knowledge
 */
defined( 'ABSPATH' ) || exit;

$cfg = (array) alp_config( 'sections.knowledge', array() );
if ( empty( $cfg['items'] ) ) {
	return;
}
?>
<section class="alp-section alp-section--tint" id="wissen">
	<div class="alp-container">
		<header class="alp-section__head">
			<div>
				<p class="alp-eyebrow"><?php echo esc_html( $cfg['eyebrow'] ?? '' ); ?></p>
				<h2 class="alp-h2"><?php echo esc_html( $cfg['title'] ?? '' ); ?></h2>
			</div>
		</header>
		<ul class="alp-know">
			<?php foreach ( $cfg['items'] as $item ) : ?>
				<li>
					<a class="alp-know__card" href="<?php echo esc_url( alp_link( $item['url'] ) ); ?>">
						<span class="alp-know__icon"><?php echo alp_icon( $item['icon'], 24 ); // phpcs:ignore ?></span>
						<h3 class="alp-h4"><?php echo esc_html( $item['title'] ); ?></h3>
						<p><?php echo esc_html( $item['text'] ); ?></p>
						<span class="alp-link">Öffnen <?php echo alp_icon( 'arrow', 16 ); // phpcs:ignore ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
