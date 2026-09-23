<?php
/**
 * Fortschrittsanzeige Warenkorb → Kasse → Bestätigung.
 */
defined( 'ABSPATH' ) || exit;

$current = (int) ( $args['current'] ?? 1 );
$steps   = array(
	1 => array( 'Warenkorb', wc_get_cart_url() ),
	2 => array( 'Kasse', wc_get_checkout_url() ),
	3 => array( 'Bestätigung', '' ),
);
?>
<ol class="alp-steps" aria-label="Bestellfortschritt">
	<?php foreach ( $steps as $n => $step ) : ?>
		<?php
		$state = $n < $current ? 'is-done' : ( $n === $current ? 'is-current' : '' );
		$label = '<span class="alp-steps__n">' . ( $n < $current ? alp_icon( 'check', 14 ) : (int) $n ) . '</span><span class="alp-steps__label">' . esc_html( $step[0] ) . '</span>';
		?>
		<li class="alp-steps__item <?php echo esc_attr( $state ); ?>"<?php echo $n === $current ? ' aria-current="step"' : ''; ?>>
			<?php if ( $n < $current && $step[1] && $current < 3 ) : ?>
				<a href="<?php echo esc_url( $step[1] ); ?>"><?php echo $label; // phpcs:ignore ?></a>
			<?php else : ?>
				<?php echo $label; // phpcs:ignore ?>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ol>
