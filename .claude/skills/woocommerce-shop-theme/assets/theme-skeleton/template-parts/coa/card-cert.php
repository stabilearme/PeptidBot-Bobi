<?php
/**
 * Zertifikats-Karte im Urkunden-Stil (COA-Seite): Papierton, doppelter Rahmen,
 * Prüftabelle wie auf dem Laborbericht, runder Prüfstempel.
 * Umschalten: inc/config.php → 'coa_card_style'.
 */
defined( 'ABSPATH' ) || exit;

$b = $args['batch'] ?? null;
if ( ! $b ) {
	return;
}
$product_url = alp_coa_product_url( $b['sku'] );
$uid         = 'alp-stamp-' . sanitize_html_class( strtolower( $b['batch'] ) );
$declared    = preg_match( '/(\d+(?:[.,]\d+)?)\s*mg/i', $b['product'], $m ) ? (float) str_replace( ',', '.', $m[1] ) : 0;
$stamp_text  = $b['done'] ? 'VERIFIZIERT · DRITTLABOR · HPLC · ' : 'IN PRÜFUNG · DRITTLABOR · HPLC · ';
?>
<article class="alp-coa-card alp-cert-card<?php echo $b['done'] ? '' : ' is-pending'; ?>" id="charge-<?php echo esc_attr( strtolower( $b['batch'] ) ); ?>" data-product="<?php echo esc_attr( strtolower( $b['product'] ) ); ?>">
	<div class="alp-cert-card__frame">
		<header class="alp-cert-card__head">
			<div class="alp-cert-card__top">
				<span class="alp-cert-card__brand">amino<b>labs</b>pro</span>
				<span class="alp-cert-card__no"><?php echo $b['done'] && $b['cert'] ? 'Nr. ' . esc_html( $b['cert'] ) : 'Nr. ausstehend'; ?></span>
			</div>
			<p class="alp-cert-card__kicker">Certificate of Analysis</p>
			<h3 class="alp-cert-card__title"><?php echo esc_html( $b['product'] ); ?></h3>
			<p class="alp-cert-card__batch">Charge <span><?php echo esc_html( $b['batch'] ); ?></span></p>
			<span class="alp-cert-card__rule" aria-hidden="true"><i></i></span>
		</header>

		<?php if ( $b['done'] ) : ?>
			<table class="alp-cert-card__table">
				<thead><tr><th scope="col">Prüfung</th><th scope="col">Methode</th><th scope="col">Ergebnis</th></tr></thead>
				<tbody>
					<tr>
						<th scope="row">Reinheit</th>
						<td>HPLC</td>
						<td><strong class="alp-coa-card__v"><?php echo esc_html( alp_num( $b['purity'], 0 ) ); ?> %</strong><span class="alp-cert-card__ok" aria-label="bestanden"><?php echo alp_icon( 'check', 11 ); // phpcs:ignore ?></span></td>
					</tr>
					<tr>
						<th scope="row">Wirkstoffgehalt<?php if ( $declared ) : ?><small>Deklariert <?php echo esc_html( alp_num( $declared, floor( $declared ) == $declared ? 0 : 1 ) ); ?> mg</small><?php endif; ?></th>
						<td>HPLC</td>
						<td><strong class="alp-coa-card__v"><?php echo esc_html( alp_num( $b['content'] ) ); ?> mg</strong><span class="alp-cert-card__ok" aria-label="bestanden"><?php echo alp_icon( 'check', 11 ); // phpcs:ignore ?></span></td>
					</tr>
				</tbody>
			</table>
		<?php else : ?>
			<p class="alp-cert-card__pending">Diese Charge wird gerade im Labor analysiert. Das vollständige Zertifikat erscheint hier<?php echo $b['expected'] ? ' ' . esc_html( $b['expected'] ) : ''; ?>.</p>
		<?php endif; ?>

		<div class="alp-cert-card__sign">
			<dl class="alp-cert-card__meta">
				<div><dt>Prüflabor</dt><dd><?php echo esc_html( $b['lab'] ); ?></dd></div>
				<?php if ( $b['done'] ) : ?>
					<div><dt>Prüfzeitraum</dt><dd><?php echo esc_html( $b['tested'] ); ?></dd></div>
					<div><dt>Report-ID</dt><dd class="is-mono"><?php echo esc_html( $b['report'] ); ?></dd></div>
				<?php endif; ?>
			</dl>
			<svg class="alp-cert-stamp" viewBox="0 0 100 100" aria-hidden="true" focusable="false">
				<defs><path id="<?php echo esc_attr( $uid ); ?>" d="M50 50 m-36 0 a36 36 0 1 1 72 0 a36 36 0 1 1 -72 0"/></defs>
				<circle cx="50" cy="50" r="46" class="is-ring"/>
				<circle cx="50" cy="50" r="27" class="is-ring is-thin"/>
				<text class="is-text"><textPath href="#<?php echo esc_attr( $uid ); ?>" textLength="222"><?php echo esc_html( $stamp_text ); ?></textPath></text>
				<?php if ( $b['done'] ) : ?>
					<path class="is-mark" d="M38 51l8 8 16-17"/>
				<?php else : ?>
					<path class="is-mark" d="M50 38v13l8 5"/>
				<?php endif; ?>
			</svg>
		</div>

		<footer class="alp-cert-card__foot">
			<?php if ( $b['done'] && $b['file'] ) : ?>
				<a class="alp-coa-card__open lightbox" href="<?php echo esc_url( $b['file'] ); ?>" target="_blank" rel="noopener">
					<span class="alp-btn__chip"><?php echo alp_icon( 'doc', 16 ); // phpcs:ignore ?></span>
					<span>Original-Zertifikat</span>
				</a>
			<?php endif; ?>
			<?php if ( $product_url ) : ?>
				<a class="alp-coa-card__product" href="<?php echo esc_url( $product_url ); ?>">Zum Produkt <?php echo alp_icon( 'arrow', 15 ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</footer>
	</div>
</article>
