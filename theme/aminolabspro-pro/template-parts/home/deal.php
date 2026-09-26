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

// Wochenangebot (inc/weekly-deal.php): wechselt automatisch jeden Montag 22 Uhr.
$week = function_exists( 'alp_wd_current' ) ? alp_wd_current() : null;

$products = array(); // [ [ product, qty, normal, promo ] ]
$ends     = 0;
$add_url  = '';
$title    = '';
$text     = $cfg['text'] ?? '';
if ( $week ) {
	foreach ( $week['deal']['items'] as $pid => $item ) {
		$p = wc_get_product( $pid );
		if ( ! $p || ! $p->is_purchasable() || ! $p->is_in_stock() ) {
			continue;
		}
		$normal     = (float) $p->get_price( 'edit' );
		$products[] = array( $p, $item['qty'], $normal, alp_wd_price( $week['deal'], $pid, $normal ) );
	}
	if ( count( $products ) !== count( $week['deal']['items'] ) ) {
		$products = array(); // Ein Produkt fehlt oder ist ausverkauft → Angebot nicht zeigen.
	}
	$ends    = $week['end'];
	$add_url = alp_wd_add_url( $week );
	$title   = (string) ( $week['deal']['title'] ?? '' );
	$text    = (string) ( $week['deal']['text'] ?? $text );
} else {
	// Ohne Wochenangebote: feste Artikelnummer oder das reduzierte Produkt mit dem höchsten Rabatt.
	$best = 0;
	$ids  = ! empty( $cfg['sku'] ) ? array( wc_get_product_id_by_sku( $cfg['sku'] ) ) : (array) wc_get_product_ids_on_sale();
	foreach ( $ids as $id ) {
		$p = $id ? wc_get_product( $id ) : null;
		if ( ! $p || ! $p->is_on_sale() || ! $p->is_in_stock() || ! (float) $p->get_regular_price() ) {
			continue;
		}
		$d = (int) round( ( 1 - (float) $p->get_sale_price() / (float) $p->get_regular_price() ) * 100 );
		if ( $d > $best ) {
			$best     = $d;
			$products = array( array( $p, 1, (float) $p->get_regular_price(), (float) $p->get_sale_price() ) );
		}
	}
	if ( $products ) {
		$to = $products[0][0]->get_date_on_sale_to();
		if ( $to ) {
			$ends = $to->getTimestamp();
		} elseif ( ! empty( $cfg['ends'] ) ) {
			$ends = strtotime( $cfg['ends'] . ' 23:59:59 Europe/Berlin' );
		}
	}
}
if ( ! $products ) {
	return;
}
if ( $ends && $ends < time() ) {
	$ends = 0;
}

$is_stack = count( $products ) > 1;
$product  = $products[0][0];
$regular  = 0.0;
$sale     = 0.0;
foreach ( $products as $row ) {
	// Beim Einzelprodukt „statt“-Preis = regulärer Preis (zeigt auch eine bestehende Reduzierung).
	$regular += ( $is_stack ? $row[2] : max( $row[2], (float) $row[0]->get_regular_price() ) ) * $row[1];
	$sale    += $row[3] * $row[1];
}
$discount = $regular > 0 ? (int) round( ( 1 - $sale / $regular ) * 100 ) : 0;
if ( $discount < 1 ) {
	return;
}
$title   = $title ? $title : $product->get_name();
$link    = $is_stack ? '#aktion' : $product->get_permalink();
$add_url = $add_url ? $add_url : $product->add_to_cart_url();
$lab     = $is_stack ? null : alp_coa_for_sku( $product->get_sku() );
?>
<section class="alp-deal" id="aktion" aria-labelledby="alp-deal-title">
	<div class="alp-container alp-deal__grid">
		<div class="alp-deal__visual">
			<span class="alp-deal__halo" aria-hidden="true"></span>
			<?php if ( $is_stack ) : ?>
				<div class="alp-deal__img alp-deal__img--stack alp-deal__img--n<?php echo (int) min( 4, count( $products ) ); ?>" aria-hidden="true">
					<?php foreach ( array_slice( $products, 0, 4 ) as $row ) : ?>
						<?php echo wp_get_attachment_image( $row[0]->get_image_id(), 'woocommerce_single', false, array( 'alt' => '' ) ); // phpcs:ignore ?>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<a class="alp-deal__img" href="<?php echo esc_url( $product->get_permalink() ); ?>" tabindex="-1" aria-hidden="true">
					<?php echo wp_get_attachment_image( $product->get_image_id(), 'large', false, array( 'alt' => '' ) ); // phpcs:ignore ?>
				</a>
			<?php endif; ?>
			<span class="alp-deal__burst"><small>Aktion</small>−<?php echo (int) $discount; ?> %</span>
		</div>

		<div class="alp-deal__copy">
			<p class="alp-deal__badge"><span class="alp-deal__pulse" aria-hidden="true"></span><?php echo esc_html( $cfg['eyebrow'] ?? 'Aktionsprodukt' ); ?> · <?php echo esc_html( rtrim( $cfg['title'] ?? '', '.' ) ); ?></p>
			<h2 class="alp-deal__title" id="alp-deal-title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></h2>
			<?php if ( $text ) : ?>
				<p class="alp-deal__text"><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
			<?php if ( $is_stack ) : ?>
				<ul class="alp-deal__items">
					<?php foreach ( $products as $row ) : ?>
						<li>
							<a href="<?php echo esc_url( $row[0]->get_permalink() ); ?>"><?php echo esc_html( ( $row[1] > 1 ? $row[1] . '× ' : '' ) . $row[0]->get_name() ); ?></a>
							<span><del><?php echo wc_price( $row[2] * $row[1] ); // phpcs:ignore ?></del> <?php echo wc_price( $row[3] * $row[1] ); // phpcs:ignore ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( $lab && $lab['done'] ) : ?>
				<p class="alp-card-lab"><?php echo alp_icon( 'check', 14 ); // phpcs:ignore ?><span>COA · <?php echo esc_html( alp_num( $lab['purity'], 0 ) ); ?> % HPLC · Charge <?php echo esc_html( $lab['batch'] ); ?></span></p>
			<?php endif; ?>

			<div class="alp-deal__price">
				<del><?php echo wc_price( $regular ); // phpcs:ignore ?></del>
				<strong><?php echo wc_price( $sale ); // phpcs:ignore ?></strong>
				<span class="alp-deal__save">Du sparst <?php echo wc_price( $regular - $sale ); // phpcs:ignore ?></span>
			</div>
			<p class="alp-deal__legal"><?php echo $is_stack ? 'Stack-Preis gilt im Warenkorb, wenn alle Produkte enthalten sind. ' : ''; ?><?php echo wp_kses_post( alp_price_legal_note( $product ) ); ?></p>

			<?php if ( $ends ) : ?>
				<div class="alp-deal__timer" data-alp-countdown="<?php echo (int) $ends; ?>"<?php echo $week ? ' data-alp-reload' : ''; ?>>
					<span class="alp-deal__timer-label"><?php echo $week ? 'Neues Angebot in' : 'Angebot endet in'; ?></span>
					<span class="alp-deal__timer-cells">
						<span><b data-unit="d">–</b><small>Tage</small></span>
						<span><b data-unit="h">–</b><small>Std</small></span>
						<span><b data-unit="m">–</b><small>Min</small></span>
						<span><b data-unit="s">–</b><small>Sek</small></span>
					</span>
				</div>
			<?php endif; ?>

			<div class="alp-deal__actions">
				<a class="alp-btn alp-btn--primary alp-btn--lg" href="<?php echo esc_url( $add_url ); ?>" rel="nofollow"><?php echo alp_icon( 'cart', 18 ); // phpcs:ignore ?> <?php echo $is_stack ? 'Stack in den Warenkorb' : 'Jetzt zum Aktionspreis sichern'; ?></a>
				<?php if ( ! $is_stack ) : ?>
					<a class="alp-btn alp-btn--ghost alp-btn--lg" href="<?php echo esc_url( $product->get_permalink() ); ?>">Details & COA</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
