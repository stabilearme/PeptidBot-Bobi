<?php
/**
 * Vertrauensliste + Forschungshinweis auf der Produktseite. Texte: config.php → 'product'.
 */
defined( 'ABSPATH' ) || exit;

$items  = (array) alp_config( 'product.trust', array() );
$notice = alp_config( 'product.ruo_notice' );
?>
<?php if ( $items ) : ?>
	<ul class="alp-ptrust">
		<?php foreach ( $items as $item ) : ?>
			<li><?php echo alp_icon( $item['icon'], 18 ); // phpcs:ignore ?><span><?php echo esc_html( $item['text'] ); ?></span></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
<?php if ( $notice ) : ?>
	<p class="alp-ruo"><strong>Research Use Only.</strong> <?php echo esc_html( $notice ); ?></p>
<?php endif; ?>
