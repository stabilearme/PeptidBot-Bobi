<?php
/**
 * Chargen-Box auf der Produktseite (unter dem Warenkorb-Button).
 * Verknüpfung über die Artikelnummer (SKU) in /data/coa-batches.php.
 */
defined( 'ABSPATH' ) || exit;

$b = $args['batch'] ?? null;
?>
<section class="alp-pcoa<?php echo ( $b && $b['done'] ) ? '' : ' is-pending'; ?>" aria-label="Laboranalyse dieser Charge">
	<?php if ( $b && $b['done'] ) : ?>
		<header class="alp-pcoa__head">
			<span class="alp-pcoa__eyebrow"><?php echo alp_icon( 'flask', 16 ); // phpcs:ignore ?> Aktuelle Charge</span>
			<span class="alp-status alp-status--ok"><?php echo alp_icon( 'check', 14 ); // phpcs:ignore ?> Verifiziert</span>
		</header>
		<div class="alp-pcoa__body">
			<div class="alp-pcoa__figure">
				<span class="alp-pcoa__num"><?php echo esc_html( alp_num( $b['purity'], 0 ) ); ?><small>%</small></span>
				<span class="alp-pcoa__cap">Reinheit (HPLC)</span>
			</div>
			<dl class="alp-specs alp-specs--compact">
				<div><dt>Charge</dt><dd class="is-mono"><?php echo esc_html( $b['batch'] ); ?></dd></div>
				<div><dt>Wirkstoffgehalt</dt><dd><?php echo esc_html( alp_num( $b['content'] ) ); ?> mg</dd></div>
				<div><dt>Prüflabor</dt><dd><?php echo esc_html( $b['lab'] ); ?></dd></div>
				<div><dt>Getestet</dt><dd><?php echo esc_html( $b['tested'] ); ?></dd></div>
			</dl>
		</div>
		<footer class="alp-pcoa__foot">
			<?php if ( $b['file'] ) : ?>
				<a class="alp-btn alp-btn--ghost alp-btn--sm lightbox" href="<?php echo esc_url( $b['file'] ); ?>" target="_blank" rel="noopener"><?php echo alp_icon( 'doc', 16 ); // phpcs:ignore ?> Zertifikat ansehen</a>
			<?php endif; ?>
			<a class="alp-link" href="<?php echo esc_url( alp_link( '/coa/' ) . '#charge-' . strtolower( $b['batch'] ) ); ?>">Alle COAs</a>
		</footer>
	<?php else : ?>
		<header class="alp-pcoa__head">
			<span class="alp-pcoa__eyebrow"><?php echo alp_icon( 'flask', 16 ); // phpcs:ignore ?> Laboranalyse</span>
			<span class="alp-status alp-status--pending"><?php echo alp_icon( 'clock', 14 ); // phpcs:ignore ?> In Prüfung</span>
		</header>
		<p class="alp-pcoa__text">Die aktuelle Charge wird von einem unabhängigen Labor per HPLC analysiert. Das Zertifikat wird nach Abschluss auf der <a href="<?php echo esc_url( alp_link( '/coa/' ) ); ?>">COA-Seite</a> veröffentlicht.</p>
	<?php endif; ?>
</section>
