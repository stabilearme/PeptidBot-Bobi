<?php
/**
 * Tab „Laborbericht“ auf der Produktseite: Chargendaten + Zertifikat als Bild.
 * Daten: /data/coa-batches.php (Verknüpfung über die Artikelnummer).
 */
defined( 'ABSPATH' ) || exit;

$b = $args['batch'] ?? null;
if ( ! $b ) {
	return;
}
?>
<div class="alp-ptab-coa<?php echo $b['done'] ? '' : ' is-pending'; ?>">
	<?php if ( $b['done'] ) : ?>
		<?php if ( $b['photo'] || $b['file'] ) : ?>
			<figure class="alp-ptab-coa__doc">
				<a class="lightbox" href="<?php echo esc_url( $b['file'] ?: $b['photo'] ); ?>" target="_blank" rel="noopener" aria-label="Vollständiges Zertifikat öffnen">
					<img src="<?php echo esc_url( $b['photo'] ?: $b['file'] ); ?>" alt="Analysezertifikat <?php echo esc_attr( $b['product'] ); ?>, Charge <?php echo esc_attr( $b['batch'] ); ?>" loading="lazy" width="900" height="1273">
				</a>
				<figcaption><?php echo alp_icon( 'search', 14 ); // phpcs:ignore ?> Antippen für das vollständige Zertifikat</figcaption>
			</figure>
		<?php endif; ?>
		<div class="alp-ptab-coa__info">
			<p class="alp-eyebrow">Charge <?php echo esc_html( $b['batch'] ); ?></p>
			<h3 class="alp-h3">Unabhängig geprüft von <?php echo esc_html( $b['lab'] ); ?></h3>
			<div class="alp-ptab-coa__figures">
				<div><span>Reinheit (HPLC)</span><strong><?php echo esc_html( alp_num( $b['purity'], 0 ) ); ?> %</strong></div>
				<div><span>Wirkstoffgehalt</span><strong><?php echo esc_html( alp_num( $b['content'] ) ); ?> mg</strong></div>
			</div>
			<dl class="alp-specs alp-specs--compact">
				<div><dt>Getestet</dt><dd><?php echo esc_html( $b['tested'] ); ?></dd></div>
				<?php if ( $b['cert'] ) : ?>
					<div><dt>Zertifikat-Nr.</dt><dd class="is-mono"><?php echo esc_html( $b['cert'] ); ?></dd></div>
				<?php endif; ?>
				<?php if ( $b['report'] ) : ?>
					<div><dt>Report-ID</dt><dd class="is-mono"><?php echo esc_html( $b['report'] ); ?></dd></div>
				<?php endif; ?>
			</dl>
			<p class="alp-ptab-coa__text">Die Chargennummer steht auf deinem Vial. Mit der Report-ID lässt sich das Zertifikat direkt beim Labor verifizieren.</p>
			<div class="alp-ptab-coa__actions">
				<?php if ( $b['file'] ) : ?>
					<a class="alp-btn alp-btn--primary alp-btn--sm lightbox" href="<?php echo esc_url( $b['file'] ); ?>" target="_blank" rel="noopener"><?php echo alp_icon( 'doc', 16 ); // phpcs:ignore ?> Zertifikat öffnen</a>
				<?php endif; ?>
				<a class="alp-link" href="<?php echo esc_url( alp_link( '/coa/' ) . '#charge-' . strtolower( $b['batch'] ) ); ?>">Alle Laborergebnisse <?php echo alp_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
			</div>
		</div>
	<?php else : ?>
		<div class="alp-ptab-coa__info">
			<p class="alp-eyebrow">Laboranalyse</p>
			<h3 class="alp-h3">Die aktuelle Charge ist im Labor</h3>
			<p class="alp-ptab-coa__text">Sie wird von <?php echo esc_html( $b['lab'] ?: 'einem unabhängigen Labor' ); ?> per HPLC analysiert. Das Zertifikat erscheint <?php echo esc_html( $b['expected'] ?: 'in Kürze' ); ?> hier und auf der <a href="<?php echo esc_url( alp_link( '/coa/' ) ); ?>">COA-Seite</a>.</p>
		</div>
	<?php endif; ?>
</div>
