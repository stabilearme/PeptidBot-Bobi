<?php
/**
 * Startseite – Vertrauensleiste. Texte: config.php → 'trust'.
 */
defined( 'ABSPATH' ) || exit;

$items = (array) alp_config( 'trust', array() );
if ( ! $items ) {
	return;
}
?>
<section class="alp-trust" aria-label="Unsere Standards">
	<div class="alp-container">
		<ul class="alp-trust__list">
			<?php foreach ( $items as $item ) : ?>
				<li class="alp-trust__item">
					<span class="alp-trust__icon"><?php echo alp_icon( $item['icon'], 22 ); // phpcs:ignore ?></span>
					<span>
						<strong><?php echo esc_html( $item['title'] ); ?></strong>
						<span><?php echo esc_html( $item['text'] ); ?></span>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
