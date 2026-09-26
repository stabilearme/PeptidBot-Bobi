<?php
/**
 * Footer. Inhalte: inc/config.php → 'footer'.
 */
defined( 'ABSPATH' ) || exit;

$footer  = (array) alp_config( 'footer', array() );
$brand   = (array) alp_config( 'brand', array() );
$logo_id = (int) get_theme_mod( 'site_logo_dark' ) ?: (int) get_theme_mod( 'site_logo' );
?>
<div class="alp-footer">
	<div class="alp-container">
		<div class="alp-footer__top">
			<div class="alp-footer__brand">
				<a class="alp-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( $brand['name'] ?? get_bloginfo( 'name' ) ); ?> – Startseite">
					<?php
					if ( $logo_id && wp_attachment_is_image( $logo_id ) ) {
						echo wp_get_attachment_image( $logo_id, 'medium', false, array( 'loading' => 'lazy', 'alt' => $brand['name'] ?? '' ) );
					} else {
						// Wortmarke wie im Logo: amino·labs·pro
						echo '<span class="alp-wordmark">amino<span>labs</span><em>pro</em></span>';
					}
					?>
				</a>
				<p><?php echo esc_html( $footer['about'] ?? '' ); ?></p>
				<?php if ( ! empty( $brand['email'] ) ) : ?>
					<a class="alp-footer__mail" href="mailto:<?php echo esc_attr( $brand['email'] ); ?>">
						<?php echo alp_icon( 'mail', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo esc_html( $brand['email'] ); ?>
					</a>
				<?php endif; ?>
			</div>

			<?php foreach ( (array) ( $footer['columns'] ?? array() ) as $title => $links ) : ?>
				<nav class="alp-footer__col" aria-label="<?php echo esc_attr( $title ); ?>">
					<h2 class="alp-footer__title"><?php echo esc_html( $title ); ?></h2>
					<ul>
						<?php foreach ( $links as $label => $target ) : ?>
							<?php if ( ! alp_link_is_live( $target ) ) { continue; } ?>
							<li><a href="<?php echo esc_url( alp_link( $target ) ); ?>"><?php echo esc_html( $label ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endforeach; ?>
		</div>

		<?php if ( ! empty( $footer['disclaimer'] ) ) : ?>
			<p class="alp-footer__disclaimer">
				<?php echo alp_icon( 'flask', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span><?php echo esc_html( $footer['disclaimer'] ); ?></span>
			</p>
		<?php endif; ?>

		<div class="alp-footer__bottom">
			<p class="alp-footer__copy">© <?php echo esc_html( wp_date( 'Y' ) . ' ' . ( $brand['name'] ?? get_bloginfo( 'name' ) ) ); ?></p>
			<nav class="alp-footer__legal" aria-label="Rechtliches">
				<?php foreach ( (array) ( $footer['legal'] ?? array() ) as $label => $target ) : ?>
					<a href="<?php echo esc_url( alp_link( $target ) ); ?>"><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
			</nav>
			<ul class="alp-footer__pay" aria-label="Zahlungsarten">
				<?php foreach ( (array) ( $footer['payments'] ?? array() ) as $pay ) : ?>
					<li><?php echo esc_html( $pay ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>
