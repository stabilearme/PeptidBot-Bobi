<?php
/**
 * App-artige Navigation unten auf Smartphones. Einträge: inc/config.php → 'mobile_nav'.
 */
defined( 'ABSPATH' ) || exit;

$items = (array) alp_config( 'mobile_nav', array() );
?>
<nav class="alp-bottom-nav" aria-label="Schnellnavigation">
	<?php
	foreach ( $items as $item ) :
		$is_search = '#alp-search' === $item['url'];
		$is_cart   = 'cart' === $item['url'];
		$href      = $is_search ? '#alp-search' : alp_link( $item['url'] );
		$current   = ! $is_search && untrailingslashit( $href ) === untrailingslashit( alp_current_url() );
		?>
		<a class="alp-bottom-nav__item<?php echo $current ? ' is-current' : ''; ?>" href="<?php echo esc_url( $href ); ?>"<?php echo $is_search ? ' data-alp-open-search aria-controls="alp-search"' : ''; ?><?php echo $current ? ' aria-current="page"' : ''; ?>>
			<span class="alp-bottom-nav__icon">
				<?php echo alp_icon( $item['icon'], 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php
				if ( $is_cart ) {
					echo alp_cart_count_html(); // phpcs:ignore WordPress.Security.EscapeOutput
				}
				?>
			</span>
			<span class="alp-bottom-nav__label"><?php echo esc_html( $item['label'] ); ?></span>
		</a>
	<?php endforeach; ?>
</nav>
