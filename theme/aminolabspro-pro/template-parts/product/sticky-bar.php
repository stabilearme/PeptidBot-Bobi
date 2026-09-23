<?php
/**
 * Kaufleiste, die beim Scrollen am unteren Rand erscheint (Logik: assets/js/theme.js).
 */
defined( 'ABSPATH' ) || exit;

/** @var WC_Product $product */
$product = $args['product'];
$thumb   = $product->get_image_id() ? wp_get_attachment_image( $product->get_image_id(), 'thumbnail', false, array( 'loading' => 'lazy', 'alt' => '' ) ) : '';
?>
<div class="alp-buybar" data-alp-buybar aria-hidden="true">
	<div class="alp-buybar__inner">
		<?php if ( $thumb ) : ?>
			<span class="alp-buybar__thumb"><?php echo $thumb; // phpcs:ignore ?></span>
		<?php endif; ?>
		<div class="alp-buybar__info">
			<p class="alp-buybar__name"><?php echo esc_html( $product->get_name() ); ?></p>
			<p class="alp-buybar__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
		</div>
		<button type="button" class="alp-btn alp-btn--primary alp-buybar__btn" data-alp-buybar-btn tabindex="-1">
			<?php echo alp_icon( 'cart', 18 ); // phpcs:ignore ?>
			<span><?php echo $product->is_type( 'simple' ) ? esc_html__( 'In den Warenkorb', 'aminolabspro-pro' ) : esc_html__( 'Optionen wählen', 'aminolabspro-pro' ); ?></span>
		</button>
	</div>
</div>
