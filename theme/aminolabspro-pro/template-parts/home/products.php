<?php
/**
 * Startseite – Produktauswahl (nutzt die Flatsome/WooCommerce-Produktkacheln).
 * Einstellungen: config.php → sections.products
 */
defined( 'ABSPATH' ) || exit;

$cfg = (array) alp_config( 'sections.products', array() );
$sc  = sprintf(
	'[products limit="%d" columns="4" orderby="%s" order="DESC" visibility="visible"]',
	(int) ( $cfg['limit'] ?? 8 ),
	esc_attr( $cfg['orderby'] ?? 'popularity' )
);
?>
<section class="alp-section alp-section--tint" id="produkte">
	<div class="alp-container">
		<header class="alp-section__head">
			<div>
				<p class="alp-eyebrow"><?php echo esc_html( $cfg['eyebrow'] ?? '' ); ?></p>
				<h2 class="alp-h2"><?php echo esc_html( $cfg['title'] ?? '' ); ?></h2>
			</div>
			<?php if ( ! empty( $cfg['cta'] ) ) : ?>
				<a class="alp-link" href="<?php echo esc_url( alp_link( $cfg['cta']['url'] ) ); ?>"><?php echo esc_html( $cfg['cta']['label'] ); ?> <?php echo alp_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</header>
		<div class="alp-products">
			<?php echo do_shortcode( $sc ); // phpcs:ignore ?>
		</div>
	</div>
</section>
