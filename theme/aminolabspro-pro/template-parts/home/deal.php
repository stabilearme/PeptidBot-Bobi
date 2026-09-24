<?php
/**
 * Startseite – Aktionsprodukt: großes Produkt mit Rabatt, Preisvergleich und Countdown.
 * Texte & Auswahl: config.php → sections.deal. Ohne reduziertes Produkt wird nichts angezeigt.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_get_product' ) ) {
	return;
}
$cfg = (array) alp_config( 'sections.deal', array() );

// Produkt wählen: feste Artikelnummer oder das reduzierte Produkt mit dem höchsten Rabatt.
$product  = null;
$discount = 0;
$ids      = ! empty( $cfg['sku'] ) ? array( wc_get_product_id_by_sku( $cfg['sku'] ) ) : (array) wc_get_product_ids_on_sale();
foreach ( $ids as $id ) {
	$p = $id ? wc_get_product( $id ) : null;
	if ( ! $p || ! $p->is_on_sale() || ! $p->is_in_stock() || ! (float) $p->get_regular_price() ) {
		continue;
	}
	$d = (int) round( ( 1 - (float) $p->get_sale_price() / (float) $p->get_regular_price() ) * 100 );
	if ( $d > $discount ) {
		$product  = $p;
		$discount = $d;
	}
}
if ( ! $product ) {
	return;
}

$regular = (float) $product->get_regular_price();
$sale    = (float) $product->get_sale_price();
$lab     = alp_coa_for_sku( $product->get_sku() );

// Ende der Aktion: „Angebot bis“ am Produkt, sonst Datum aus der Konfiguration.
$ends = 0;
$to   = $product->get_date_on_sale_to();
if ( $to ) {
	$ends = $to->getTimestamp();
} elseif ( ! empty( $cfg['ends'] ) ) {
	$ends = strtotime( $cfg['ends'] . ' 23:59:59 Europe/Berlin' );
} elseif ( defined( 'ALP_PREVIEW_THEME' ) ) {
	$ends = time() + 3 * DAY_IN_SECONDS + 5 * HOUR_IN_SECONDS; // nur Vorschau
}
if ( $ends && $ends < time() ) {
	$ends = 0;
}
?>
<section class="alp-deal" id="aktion" aria-labelledby="alp-deal-title">
	<div class="alp-container alp-deal__grid">
		<div class="alp-deal__visual">
			<span class="alp-deal__halo" aria-hidden="true"></span>
			<a class="alp-deal__img" href="<?php echo esc_url( $product->get_permalink() ); ?>" tabindex="-1" aria-hidden="true">
				<?php echo wp_get_attachment_image( $product->get_image_id(), 'large', false, array( 'alt' => '' ) ); // phpcs:ignore ?>
			</a>
			<span class="alp-deal__burst"><small>Aktion</small>−<?php echo (int) $discount; ?> %</span>
		</div>

		<div class="alp-deal__copy">
			<p class="alp-deal__badge"><span class="alp-deal__pulse" aria-hidden="true"></span><?php echo esc_html( $cfg['eyebrow'] ?? 'Aktionsprodukt' ); ?> · <?php echo esc_html( rtrim( $cfg['title'] ?? '', '.' ) ); ?></p>
			<h2 class="alp-deal__title" id="alp-deal-title"><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h2>
			<p class="alp-deal__text"><?php echo esc_html( $cfg['text'] ?? '' ); ?></p>
			<?php if ( $lab && $lab['done'] ) : ?>
				<p class="alp-card-lab"><?php echo alp_icon( 'check', 14 ); // phpcs:ignore ?><span>COA · <?php echo esc_html( alp_num( $lab['purity'], 0 ) ); ?> % HPLC · Charge <?php echo esc_html( $lab['batch'] ); ?></span></p>
			<?php endif; ?>

			<div class="alp-deal__price">
				<del><?php echo wc_price( $regular ); // phpcs:ignore ?></del>
				<strong><?php echo wc_price( $sale ); // phpcs:ignore ?></strong>
				<span class="alp-deal__save">Du sparst <?php echo wc_price( $regular - $sale ); // phpcs:ignore ?></span>
			</div>

			<?php if ( $ends ) : ?>
				<div class="alp-deal__timer" data-alp-countdown="<?php echo (int) $ends; ?>">
					<span class="alp-deal__timer-label">Angebot endet in</span>
					<span class="alp-deal__timer-cells">
						<span><b data-unit="d">–</b><small>Tage</small></span>
						<span><b data-unit="h">–</b><small>Std</small></span>
						<span><b data-unit="m">–</b><small>Min</small></span>
						<span><b data-unit="s">–</b><small>Sek</small></span>
					</span>
				</div>
			<?php endif; ?>

			<div class="alp-deal__actions">
				<a class="alp-btn alp-btn--primary alp-btn--lg" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" rel="nofollow"><?php echo alp_icon( 'cart', 18 ); // phpcs:ignore ?> Jetzt zum Aktionspreis sichern</a>
				<a class="alp-btn alp-btn--ghost alp-btn--lg" href="<?php echo esc_url( $product->get_permalink() ); ?>">Details & COA</a>
			</div>
		</div>
	</div>
</section>
