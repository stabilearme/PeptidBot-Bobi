<?php
/**
 * Such-Overlay (öffnet über die Mobile-Navigation oder Links mit data-alp-open-search).
 */
defined( 'ABSPATH' ) || exit;

$cats = function_exists( 'get_terms' ) ? get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'number'     => 8,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
	)
) : array();
?>
<div class="alp-search" id="alp-search" role="dialog" aria-modal="true" aria-label="Produktsuche" hidden>
	<div class="alp-search__backdrop" data-alp-close-search></div>
	<div class="alp-search__panel">
		<form class="alp-search__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="alp-search-input">Produkte durchsuchen</label>
			<?php echo alp_icon( 'search', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<input id="alp-search-input" type="search" name="s" placeholder="Peptid suchen, z. B. BPC-157" autocomplete="off">
			<input type="hidden" name="post_type" value="product">
			<button type="button" class="alp-search__close" data-alp-close-search aria-label="Suche schließen"><?php echo alp_icon( 'close', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		</form>
		<?php if ( ! is_wp_error( $cats ) && $cats ) : ?>
			<p class="alp-search__label">Kategorien</p>
			<div class="alp-pills">
				<?php foreach ( $cats as $cat ) : ?>
					<a class="alp-pill" href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
