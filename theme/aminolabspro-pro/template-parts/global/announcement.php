<?php
/**
 * Laufleiste ganz oben. Texte: inc/config.php → 'announcement'.
 */
defined( 'ABSPATH' ) || exit;

$items = (array) alp_config( 'announcement', array() );
if ( ! $items ) {
	return;
}
?>
<div class="alp-announce" role="region" aria-label="Hinweise">
	<div class="alp-announce__track">
		<?php foreach ( $items as $i => $item ) : ?>
			<p class="alp-announce__item<?php echo 0 === $i ? ' is-active' : ''; ?>">
				<?php echo alp_icon( $item['icon'] ?? 'check', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span><?php echo esc_html( $item['text'] ); ?></span>
			</p>
		<?php endforeach; ?>
	</div>
</div>
