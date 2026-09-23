<?php
/**
 * Eine Zertifikats-Karte (COA-Seite) im Stil eines Laborbefunds:
 * Kopfzeile mit Zertifikatsnummer & Siegel, Messwerte mit Chromatogramm,
 * Soll/Ist-Vergleich des Wirkstoffgehalts, Prüfdaten, Aktionen.
 */
defined( 'ABSPATH' ) || exit;

$b = $args['batch'] ?? null;
if ( ! $b ) {
	return;
}
$product_url = alp_coa_product_url( $b['sku'] );

// Deklarierte Menge aus dem Produktnamen („BPC-157 10 mg“ → 10) für den Soll/Ist-Vergleich.
$declared = preg_match( '/(\d+(?:[.,]\d+)?)\s*mg/i', $b['product'], $m ) ? (float) str_replace( ',', '.', $m[1] ) : 0;
$delta    = ( $b['done'] && $declared > 0 && ! empty( $b['content'] ) ) ? ( $b['content'] / $declared - 1 ) * 100 : null;
?>
<article class="alp-coa-card<?php echo $b['done'] ? '' : ' is-pending'; ?>" id="charge-<?php echo esc_attr( strtolower( $b['batch'] ) ); ?>" data-product="<?php echo esc_attr( strtolower( $b['product'] ) ); ?>">
	<div class="alp-coa-card__strip">
		<span class="alp-coa-card__doc-no">
			<?php echo alp_icon( 'doc', 14 ); // phpcs:ignore ?>
			<?php echo $b['done'] && $b['cert'] ? 'COA Nr. ' . esc_html( $b['cert'] ) : 'COA in Arbeit'; ?>
		</span>
		<?php if ( $b['done'] ) : ?>
			<span class="alp-coa-seal alp-coa-seal--ok"><span class="alp-coa-seal__dot"><?php echo alp_icon( 'check', 11 ); // phpcs:ignore ?></span>Verifiziert</span>
		<?php else : ?>
			<span class="alp-coa-seal alp-coa-seal--pending"><span class="alp-coa-seal__dot"><?php echo alp_icon( 'clock', 11 ); // phpcs:ignore ?></span>Test läuft</span>
		<?php endif; ?>
	</div>

	<header class="alp-coa-card__head">
		<h3 class="alp-coa-card__title"><?php echo esc_html( $b['product'] ); ?></h3>
		<p class="alp-coa-card__batch"><span>Charge</span><?php echo esc_html( $b['batch'] ); ?></p>
	</header>

	<?php if ( $b['done'] ) : ?>
		<div class="alp-coa-card__figures">
			<div class="alp-coa-card__fig alp-coa-card__fig--purity">
				<span class="alp-coa-card__k">Reinheit · HPLC</span>
				<span class="alp-coa-card__v alp-coa-card__v--big"><?php echo esc_html( alp_num( $b['purity'], 0 ) ); ?><small>%</small></span>
				<svg class="alp-coa-card__peak" viewBox="0 0 120 34" preserveAspectRatio="none" aria-hidden="true" focusable="false">
					<path class="is-fill" d="M0 33 L30 33 C38 33 40 31 44 30 C48 29 50 3 56 3 C62 3 64 29 68 31 C74 33 80 32 86 31.5 C88 31 89 28 91 28 C93 28 94 31 96 32 L120 33 Z"/>
					<path class="is-line" d="M0 33 L30 33 C38 33 40 31 44 30 C48 29 50 3 56 3 C62 3 64 29 68 31 C74 33 80 32 86 31.5 C88 31 89 28 91 28 C93 28 94 31 96 32 L120 33"/>
				</svg>
			</div>
			<div class="alp-coa-card__fig">
				<span class="alp-coa-card__k">Wirkstoff</span>
				<span class="alp-coa-card__v"><?php echo esc_html( alp_num( $b['content'] ) ); ?><small> mg</small></span>
				<?php if ( null !== $delta ) : ?>
					<span class="alp-coa-card__delta<?php echo $delta >= 0 ? ' is-up' : ''; ?>">
						Deklariert <?php echo esc_html( alp_num( $declared, floor( $declared ) == $declared ? 0 : 1 ) ); ?> mg
						<b><?php echo esc_html( ( $delta >= 0 ? '+' : '−' ) . alp_num( abs( $delta ), 1 ) ); ?> %</b>
					</span>
				<?php endif; ?>
			</div>
		</div>

		<dl class="alp-coa-card__meta">
			<div><dt>Prüflabor</dt><dd><?php echo esc_html( $b['lab'] ); ?></dd></div>
			<div><dt>Getestet</dt><dd><?php echo esc_html( $b['tested'] ); ?></dd></div>
			<div><dt>Report-ID</dt><dd class="is-mono"><?php echo esc_html( $b['report'] ); ?></dd></div>
		</dl>

		<footer class="alp-coa-card__foot">
			<?php if ( $b['file'] ) : ?>
				<a class="alp-coa-card__open lightbox" href="<?php echo esc_url( $b['file'] ); ?>" target="_blank" rel="noopener">
					<span class="alp-btn__chip"><?php echo alp_icon( 'doc', 16 ); // phpcs:ignore ?></span>
					<span>Zertifikat</span>
				</a>
			<?php endif; ?>
			<?php if ( $product_url ) : ?>
				<a class="alp-coa-card__product" href="<?php echo esc_url( $product_url ); ?>">Zum Produkt <?php echo alp_icon( 'arrow', 15 ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</footer>
	<?php else : ?>
		<div class="alp-coa-card__progress" aria-hidden="true"><span></span></div>
		<p class="alp-coa-card__pending">Diese Charge wird gerade im Labor analysiert. Das vollständige Zertifikat erscheint hier<?php echo $b['expected'] ? ' ' . esc_html( $b['expected'] ) : ''; ?>.</p>
		<?php if ( $product_url ) : ?>
			<footer class="alp-coa-card__foot">
				<a class="alp-coa-card__product" href="<?php echo esc_url( $product_url ); ?>">Zum Produkt <?php echo alp_icon( 'arrow', 15 ); // phpcs:ignore ?></a>
			</footer>
		<?php endif; ?>
	<?php endif; ?>
</article>
