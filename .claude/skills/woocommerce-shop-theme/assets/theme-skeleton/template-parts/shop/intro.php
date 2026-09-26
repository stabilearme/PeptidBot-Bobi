<?php
/**
 * Einleitung über der Produktliste (Shop & Kategorien). Texte: config.php → 'shop'.
 * Auf Kategorieseiten erscheint die Kategoriebeschreibung aus WooCommerce, falls vorhanden.
 */
defined( 'ABSPATH' ) || exit;

$cfg  = (array) alp_config( 'shop', array() );
$text = $cfg['intro'] ?? '';
if ( is_product_category() ) {
	$desc = term_description();
	$text = $desc ? wp_strip_all_tags( $desc ) : $text;
}
$chips     = (array) ( $cfg['chips'] ?? array() );
$threshold = (float) alp_config( 'free_shipping_threshold', 0 );
if ( $threshold > 0 ) {
	$chips[] = array( 'icon' => 'box', 'text' => 'Versandkostenfrei ab ' . alp_num( $threshold, 0 ) . ' €' );
}
if ( ! $text && ! $chips ) {
	return;
}
?>
<div class="alp-shop-intro">
	<?php if ( $text ) : ?>
		<p class="alp-shop-intro__text"><?php echo esc_html( $text ); ?></p>
	<?php endif; ?>
	<?php if ( $chips ) : ?>
		<ul class="alp-shop-intro__usps">
			<?php foreach ( $chips as $chip ) : ?>
				<li><?php echo alp_icon( $chip['icon'] ?? 'check', 16 ); // phpcs:ignore ?><span><?php echo esc_html( $chip['text'] ); ?></span></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
