<?php
/**
 * Startseite – Produktauswahl (nutzt die Flatsome/WooCommerce-Produktkacheln).
 * Einstellungen: config.php → sections.products
 */
defined( 'ABSPATH' ) || exit;

$cfg = (array) alp_config( 'sections.products', array() );

// Kategorie-Filter über der Liste: die größten Kategorien als Pills.
$pills = array();
if ( ! empty( $cfg['pills'] ) && function_exists( 'get_terms' ) ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => 12,
		)
	);
	$skip  = (array) alp_config( 'sections.categories.exclude', array() );
	foreach ( is_array( $terms ) ? $terms : array() as $term ) {
		if ( ! in_array( $term->slug, $skip, true ) && count( $pills ) < (int) $cfg['pills'] ) {
			$pills[] = $term;
		}
	}
}
$sc  = sprintf(
	'[products limit="%d" columns="4" orderby="%s" order="DESC" visibility="visible"]',
	(int) ( $cfg['limit'] ?? 8 ),
	esc_attr( $cfg['orderby'] ?? 'popularity' )
);
?>
<section class="alp-section" id="produkte">
	<div class="alp-container">
		<header class="alp-section__head">
			<div>
				<p class="alp-eyebrow"><?php echo esc_html( $cfg['eyebrow'] ?? '' ); ?></p>
				<h2 class="alp-h2"><?php echo esc_html( $cfg['title'] ?? '' ); ?></h2>
			</div>
			<?php if ( $pills ) : ?>
				<nav class="alp-pills alp-section__pills" aria-label="Nach Forschungsgebiet">
					<a class="alp-pill is-active" href="<?php echo esc_url( alp_link( 'shop' ) ); ?>">Alle</a>
					<?php foreach ( $pills as $term ) : ?>
						<a class="alp-pill" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( html_entity_decode( $term->name ) ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php elseif ( ! empty( $cfg['cta'] ) ) : ?>
				<a class="alp-link" href="<?php echo esc_url( alp_link( $cfg['cta']['url'] ) ); ?>"><?php echo esc_html( $cfg['cta']['label'] ); ?> <?php echo alp_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</header>
		<div class="alp-products">
			<?php echo do_shortcode( $sc ); // phpcs:ignore ?>
		</div>
		<?php if ( $pills && ! empty( $cfg['cta'] ) ) : ?>
			<p class="alp-section__more"><a class="alp-btn alp-btn--ghost" href="<?php echo esc_url( alp_link( $cfg['cta']['url'] ) ); ?>"><?php echo esc_html( $cfg['cta']['label'] ); ?> <?php echo alp_icon( 'arrow', 16 ); // phpcs:ignore ?></a></p>
		<?php endif; ?>
	</div>
</section>
