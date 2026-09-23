<?php
/**
 * Eine Zertifikats-Karte (COA-Seite).
 */
defined( 'ABSPATH' ) || exit;

$b = $args['batch'] ?? null;
if ( ! $b ) {
	return;
}
$product_url = alp_coa_product_url( $b['sku'] );
?>
<article class="alp-coa-card<?php echo $b['done'] ? '' : ' is-pending'; ?>" id="charge-<?php echo esc_attr( strtolower( $b['batch'] ) ); ?>" data-product="<?php echo esc_attr( strtolower( $b['product'] ) ); ?>">
	<header class="alp-coa-card__head">
		<div>
			<h3 class="alp-coa-card__title"><?php echo esc_html( $b['product'] ); ?></h3>
			<p class="alp-coa-card__batch"><?php echo esc_html( $b['batch'] ); ?></p>
		</div>
		<?php if ( $b['done'] ) : ?>
			<span class="alp-status alp-status--ok"><?php echo alp_icon( 'check', 14 ); // phpcs:ignore ?> Verifiziert</span>
		<?php else : ?>
			<span class="alp-status alp-status--pending"><?php echo alp_icon( 'clock', 14 ); // phpcs:ignore ?> Test läuft</span>
		<?php endif; ?>
	</header>

	<?php if ( $b['done'] ) : ?>
		<div class="alp-coa-card__figures">
			<div>
				<span class="alp-coa-card__k">Reinheit (HPLC)</span>
				<span class="alp-coa-card__v alp-coa-card__v--big"><?php echo esc_html( alp_num( $b['purity'], 0 ) ); ?><small> %</small></span>
			</div>
			<div>
				<span class="alp-coa-card__k">Wirkstoffgehalt</span>
				<span class="alp-coa-card__v"><?php echo esc_html( alp_num( $b['content'] ) ); ?> mg</span>
			</div>
		</div>
		<dl class="alp-specs alp-specs--compact">
			<div><dt>Prüflabor</dt><dd><?php echo esc_html( $b['lab'] ); ?></dd></div>
			<div><dt>Getestet</dt><dd><?php echo esc_html( $b['tested'] ); ?></dd></div>
			<div><dt>Report-ID</dt><dd class="is-mono"><?php echo esc_html( $b['report'] ); ?></dd></div>
		</dl>
		<footer class="alp-coa-card__foot">
			<?php if ( $b['file'] ) : ?>
				<a class="alp-btn alp-btn--primary alp-btn--sm lightbox" href="<?php echo esc_url( $b['file'] ); ?>" target="_blank" rel="noopener">
					<?php echo alp_icon( 'doc', 16 ); // phpcs:ignore ?> Zertifikat<?php echo $b['cert'] ? ' Nr. ' . esc_html( $b['cert'] ) : ''; ?>
				</a>
			<?php endif; ?>
			<?php if ( $product_url ) : ?>
				<a class="alp-link" href="<?php echo esc_url( $product_url ); ?>">Zum Produkt <?php echo alp_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</footer>
	<?php else : ?>
		<p class="alp-coa-card__pending">Diese Charge wird gerade im Labor analysiert. Das vollständige Zertifikat erscheint hier<?php echo $b['expected'] ? ' ' . esc_html( $b['expected'] ) : ''; ?>.</p>
		<?php if ( $product_url ) : ?>
			<footer class="alp-coa-card__foot">
				<a class="alp-link" href="<?php echo esc_url( $product_url ); ?>">Zum Produkt <?php echo alp_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
			</footer>
		<?php endif; ?>
	<?php endif; ?>
</article>
