<?php
/**
 * Strukturierte Daten. Rank Math liefert bereits Organisation, Website, Produkte
 * und Breadcrumbs – hier kommt nur das FAQ der Startseite dazu (Rich Snippet).
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_head', 'alp_faq_schema', 30 );
function alp_faq_schema() {
	if ( ! is_front_page() || ! alp_config( 'features.faq_schema' ) ) {
		return;
	}
	$faq = (array) alp_data( 'faq' );
	if ( ! $faq ) {
		return;
	}
	$entities = array();
	foreach ( $faq as $item ) {
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => wp_strip_all_tags( $item['q'] ),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_strip_all_tags( $item['a'] ),
			),
		);
	}
	$data = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $entities,
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
