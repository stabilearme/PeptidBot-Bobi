<?php
/**
 * Kategorie-Leiste über Shop und Kategorieseiten.
 */
defined( 'ABSPATH' ) || exit;

$exclude = (array) alp_config( 'sections.categories.exclude', array() );
$terms   = get_terms(
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
$current = is_product_category() ? get_queried_object_id() : 0;
?>
<nav class="alp-cat-pills" aria-label="Kategorien">
	<a class="alp-pill<?php echo $current ? '' : ' is-active'; ?>" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"<?php echo $current ? '' : ' aria-current="page"'; ?>>Alle</a>
	<?php
	foreach ( $terms as $term ) :
		if ( in_array( $term->slug, $exclude, true ) ) {
			continue;
		}
		$active = $current === $term->term_id;
		?>
		<a class="alp-pill<?php echo $active ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $term ) ); ?>"<?php echo $active ? ' aria-current="page"' : ''; ?>>
			<?php echo esc_html( $term->name ); ?> <span class="alp-pill__count"><?php echo (int) $term->count; ?></span>
		</a>
	<?php endforeach; ?>
</nav>
