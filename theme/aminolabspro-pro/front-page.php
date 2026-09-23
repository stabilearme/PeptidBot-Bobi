<?php
/**
 * Startseite.
 *
 * Reihenfolge der Abschnitte hier ändern, einzelne Abschnitte mit // auskommentieren.
 * Texte: inc/config.php → 'hero', 'trust', 'sections'. Aussehen: assets/css/home.css
 *
 * Hinweis: Der Inhalt der WordPress-Seite „Home“ wird hier nicht mehr angezeigt.
 * Titel & Beschreibung für Google kommen weiterhin aus Rank Math (Seite „Home“).
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div id="alp-home" class="alp-home">
	<?php
	$alp_home_sections = array(
		'home/hero',
		'home/trust',
		'home/categories',
		'home/products',
		'home/news',
		'home/coa',
		'home/process',
		'home/knowledge',
		'home/faq',
		'home/newsletter',
	);
	foreach ( $alp_home_sections as $alp_section ) {
		alp_part( $alp_section );
	}
	?>
</div>
<?php
get_footer();
