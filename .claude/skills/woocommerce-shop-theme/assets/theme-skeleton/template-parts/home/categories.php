<?php
/**
 * Startseite – Kategorien (automatisch aus WooCommerce). Texte: config.php → sections.categories
 */
defined( 'ABSPATH' ) || exit;

$cfg   = (array) alp_config( 'sections.categories', array() );
$icons = (array) alp_config( 'category_icons', array() );
$terms = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
		'orderby'    => 'count',
		'order'      => 'DESC',
	)
);
if ( is_wp_error( $terms ) || ! $terms ) {
	return;
}
$terms = array_values( array_filter( $terms, fn( $t ) => ! in_array( $t->slug, (array) ( $cfg['exclude'] ?? array() ), true ) ) );
?>
<section class="alp-section" id="kategorien">
	<div class="alp-container">
		<header class="alp-section__head">
			<div>
				<p class="alp-eyebrow"><?php echo esc_html( $cfg['eyebrow'] ?? '' ); ?></p>
				<h2 class="alp-h2"><?php echo esc_html( $cfg['title'] ?? '' ); ?></h2>
			</div>
			<p class="alp-section__text"><?php echo esc_html( $cfg['text'] ?? '' ); ?></p>
		</header>
		<ul class="alp-cats">
			<?php foreach ( $terms as $term ) : ?>
				<li>
					<a class="alp-cat" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
						<span class="alp-cat__icon"><?php echo alp_icon( $icons[ $term->slug ] ?? 'flask', 24 ); // phpcs:ignore ?></span>
						<span class="alp-cat__name"><?php echo esc_html( html_entity_decode( $term->name ) ); ?></span>
						<span class="alp-cat__count"><?php echo (int) $term->count; ?> <?php echo 1 === (int) $term->count ? 'Produkt' : 'Produkte'; ?></span>
						<span class="alp-cat__arrow"><?php echo alp_icon( 'arrow', 18 ); // phpcs:ignore ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
