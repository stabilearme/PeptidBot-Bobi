<?php
/**
 * Startseite – Hero mit Live-Zertifikat. Texte: config.php → 'hero'.
 */
defined( 'ABSPATH' ) || exit;

$hero  = (array) alp_config( 'hero', array() );
$batch = alp_coa_find( $hero['featured_batch'] ?? '' );
if ( ! $batch || ! $batch['done'] ) {
	$done  = array_values( array_filter( alp_coa_batches(), fn( $b ) => $b['done'] ) );
	$batch = $done[0] ?? null;
}
?>
<section class="alp-hero">
	<div class="alp-container alp-hero__grid">
		<div class="alp-hero__copy">
			<p class="alp-eyebrow"><span class="alp-dot" aria-hidden="true"></span><?php echo esc_html( $hero['eyebrow'] ?? '' ); ?></p>
			<h1 class="alp-hero__title"><?php echo wp_kses( $hero['title'] ?? '', array( 'em' => array(), 'br' => array() ) ); ?></h1>
			<p class="alp-hero__text"><?php echo esc_html( $hero['text'] ?? '' ); ?></p>
			<div class="alp-hero__actions">
				<?php if ( ! empty( $hero['primary'] ) ) : ?>
					<a class="alp-btn alp-btn--primary alp-btn--lg" href="<?php echo esc_url( alp_link( $hero['primary']['url'] ) ); ?>"><?php echo esc_html( $hero['primary']['label'] ); ?> <?php echo alp_icon( 'arrow', 18 ); // phpcs:ignore ?></a>
				<?php endif; ?>
				<?php if ( ! empty( $hero['secondary'] ) ) : ?>
					<a class="alp-btn alp-btn--ghost alp-btn--lg" href="<?php echo esc_url( alp_link( $hero['secondary']['url'] ) ); ?>"><?php echo esc_html( $hero['secondary']['label'] ); ?></a>
				<?php endif; ?>
			</div>
			<?php if ( ! empty( $hero['stats'] ) ) : ?>
				<dl class="alp-hero__stats">
					<?php foreach ( $hero['stats'] as $stat ) : ?>
						<div><dt><?php echo esc_html( $stat['label'] ); ?></dt><dd><?php echo esc_html( $stat['value'] ); ?></dd></div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>
		</div>

		<?php if ( $batch ) : ?>
			<figure class="alp-cert" aria-label="Beispiel eines veröffentlichten Laborzertifikats">
				<div class="alp-cert__head">
					<div>
						<p class="alp-cert__kicker">Certificate of Analysis</p>
						<p class="alp-cert__product"><?php echo esc_html( $batch['product'] ); ?></p>
					</div>
					<span class="alp-status alp-status--ok"><?php echo alp_icon( 'check', 14 ); // phpcs:ignore ?> Verifiziert</span>
				</div>
				<div class="alp-cert__chart">
					<?php echo alp_chromatogram( $batch['batch'], $batch['purity'] ); // phpcs:ignore ?>
				</div>
				<dl class="alp-cert__data">
					<div><dt>Reinheit</dt><dd class="is-accent"><?php echo esc_html( alp_num( $batch['purity'], 0 ) ); ?> %</dd></div>
					<div><dt>Gehalt</dt><dd><?php echo esc_html( alp_num( $batch['content'] ) ); ?> mg</dd></div>
					<div><dt>Charge</dt><dd class="is-mono"><?php echo esc_html( $batch['batch'] ); ?></dd></div>
				</dl>
				<figcaption class="alp-cert__foot">
					<span><?php echo esc_html( $batch['lab'] ); ?> · <?php echo esc_html( $batch['tested'] ); ?></span>
					<a href="<?php echo esc_url( alp_link( '/coa/' ) . '#charge-' . strtolower( $batch['batch'] ) ); ?>">Zertifikat <?php echo alp_icon( 'arrow', 14 ); // phpcs:ignore ?></a>
				</figcaption>
				<p class="alp-cert__note">Kurve stilisiert · Originaldaten im Zertifikat</p>
			</figure>
		<?php endif; ?>
	</div>
</section>
