<?php
/**
 * Startseite – Hero: Text links, rechts Foto mit schwebender Zertifikats-Karte (echte Charge).
 * Ohne Foto (hero.image leer): gezeichnetes Zertifikat. Texte: config.php → 'hero'.
 */
defined( 'ABSPATH' ) || exit;

$hero  = (array) alp_config( 'hero', array() );
$image = ! empty( $hero['image'] ) ? alp_link( $hero['image'] ) : '';
$batch = alp_coa_find( $hero['featured_batch'] ?? '' );
if ( ! $batch || ! $batch['done'] ) {
	$done  = array_values( array_filter( alp_coa_batches(), fn( $b ) => $b['done'] ) );
	$batch = $done[0] ?? null;
}
?>
<section class="alp-hero<?php echo $image ? ' alp-hero--split' : ''; ?>">
	<div class="alp-container alp-hero__grid">
		<div class="alp-hero__copy">
			<p class="alp-eyebrow"><?php echo esc_html( $hero['eyebrow'] ?? '' ); ?></p>
			<h1 class="alp-hero__title"><?php echo wp_kses( $hero['title'] ?? '', array( 'em' => array(), 'br' => array() ) ); ?></h1>
			<p class="alp-hero__text"><?php echo esc_html( $hero['text'] ?? '' ); ?></p>
			<div class="alp-hero__actions">
				<?php if ( ! empty( $hero['primary'] ) ) : ?>
					<a class="alp-btn alp-btn--primary alp-btn--lg" href="<?php echo esc_url( alp_link( $hero['primary']['url'] ) ); ?>"><?php echo esc_html( $hero['primary']['label'] ); ?> <span class="alp-btn__orb"><?php echo alp_icon( 'arrow', 16 ); // phpcs:ignore ?></span></a>
				<?php endif; ?>
				<?php if ( ! empty( $hero['secondary'] ) ) : ?>
					<a class="alp-btn alp-btn--ghost alp-btn--lg" href="<?php echo esc_url( alp_link( $hero['secondary']['url'] ) ); ?>"><span class="alp-btn__chip"><?php echo alp_icon( 'shield', 16 ); // phpcs:ignore ?></span><?php echo esc_html( $hero['secondary']['label'] ); ?></a>
				<?php endif; ?>
			</div>
			<?php if ( ! empty( $hero['checks'] ) ) : ?>
				<ul class="alp-hero__checks">
					<?php foreach ( (array) $hero['checks'] as $check ) : ?>
						<li><span class="alp-hero__tick"><?php echo alp_icon( 'check', 12 ); // phpcs:ignore ?></span><?php echo esc_html( $check ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( ! $image && ! empty( $hero['stats'] ) ) : ?>
				<dl class="alp-hero__stats">
					<?php foreach ( $hero['stats'] as $stat ) : ?>
						<div><dt><?php echo esc_html( $stat['label'] ); ?></dt><dd><?php echo esc_html( alp_stat_value( $stat['value'] ) ); ?></dd></div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>
		</div>

		<?php if ( $image ) : ?>
			<div class="alp-hero__visual">
				<div class="alp-hero__photo">
					<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $hero['image_alt'] ?? '' ); ?>" width="1671" height="941" fetchpriority="high" decoding="async">
				</div>
				<?php if ( $batch ) : ?>
					<a class="alp-hero__proof" href="<?php echo esc_url( alp_link( '/coa/' ) . '#charge-' . strtolower( $batch['batch'] ) ); ?>" aria-label="Zertifikat der Charge <?php echo esc_attr( $batch['batch'] ); ?> ansehen">
						<span class="alp-hero__proof-head">
							<span class="alp-hero__proof-batch"><?php echo esc_html( $batch['batch'] ); ?></span>
							<span class="alp-hero__proof-ok">Verifiziert</span>
						</span>
						<span class="alp-hero__proof-val"><strong><?php echo esc_html( alp_num( $batch['purity'], 0 ) ); ?></strong><span>%</span><small>Reinheit (HPLC)</small></span>
						<span class="alp-hero__proof-foot"><span><?php echo esc_html( $batch['product'] ); ?> · gemessen</span><span class="alp-mono"><?php echo esc_html( alp_num( $batch['content'] ) ); ?> mg</span></span>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! $image && $batch ) : ?>
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
