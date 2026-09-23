<?php
/**
 * Startseite – Chargen-Prüfer (dunkler Abschnitt). Texte: config.php → sections.coa
 */
defined( 'ABSPATH' ) || exit;

$cfg    = (array) alp_config( 'sections.coa', array() );
$recent = array_slice( array_values( array_filter( alp_coa_batches(), fn( $b ) => $b['done'] ) ), 0, 4 );
?>
<section class="alp-section alp-section--dark" id="charge-pruefen">
	<div class="alp-container alp-coa-teaser">
		<div class="alp-coa-teaser__copy">
			<p class="alp-eyebrow alp-eyebrow--light"><?php echo esc_html( $cfg['eyebrow'] ?? '' ); ?></p>
			<h2 class="alp-h2"><?php echo esc_html( $cfg['title'] ?? '' ); ?></h2>
			<p class="alp-lead"><?php echo esc_html( $cfg['text'] ?? '' ); ?></p>
			<?php alp_part( 'coa/lookup', array( 'tone' => 'dark' ) ); ?>
		</div>
		<div class="alp-coa-teaser__list">
			<p class="alp-coa-teaser__label">Zuletzt veröffentlicht</p>
			<ul>
				<?php foreach ( $recent as $b ) : ?>
					<li>
						<a href="<?php echo esc_url( alp_link( '/coa/' ) . '#charge-' . strtolower( $b['batch'] ) ); ?>">
							<span class="alp-coa-teaser__name"><?php echo esc_html( $b['product'] ); ?><small><?php echo esc_html( $b['batch'] ); ?></small></span>
							<span class="alp-coa-teaser__val"><?php echo esc_html( alp_num( $b['purity'], 0 ) ); ?> %<small>HPLC</small></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( ! empty( $cfg['cta'] ) ) : ?>
				<a class="alp-link alp-link--light" href="<?php echo esc_url( alp_link( $cfg['cta']['url'] ) ); ?>"><?php echo esc_html( $cfg['cta']['label'] ); ?> <?php echo alp_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
