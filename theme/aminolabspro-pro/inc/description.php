<?php
/**
 * Einheitliche Produktbeschreibungen.
 *
 * Die Beschreibungen in WooCommerce enthalten viele Inline-Styles (Farben, Abstände).
 * Dieses Modul entfernt sie beim Anzeigen und vergibt stattdessen Klassen, damit alle
 * Beschreibungen im Theme-Design erscheinen. In der Datenbank wird NICHTS verändert –
 * Schalter aus (config.php → features.clean_descriptions) = alter Zustand.
 *
 * Styling: assets/css/product.css → Abschnitt „Beschreibung“.
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'the_content', 'alp_clean_product_description', 20 );
function alp_clean_product_description( $html ) {
	if ( ! alp_config( 'features.clean_descriptions' ) || ! function_exists( 'is_product' ) || ! is_product() || ! in_the_loop() ) {
		return $html;
	}
	if ( ! class_exists( 'DOMDocument' ) || false === strpos( $html, 'style=' ) ) {
		return '<div class="alp-desc">' . $html . '</div>';
	}
	return '<div class="alp-desc">' . alp_normalize_description_html( $html ) . '</div>';
}

/**
 * Wandelt Inline-Styles in semantische Klassen um.
 */
function alp_normalize_description_html( $html ) {
	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML( '<?xml encoding="utf-8"?><div id="alp-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
	libxml_clear_errors();

	$xpath = new DOMXPath( $doc );

	// Leere Absätze (entstehen durch HTML-Kommentare im Editor) und <br> zwischen Chips entfernen.
	foreach ( $xpath->query( '//comment()' ) as $comment ) {
		$comment->parentNode->removeChild( $comment );
	}
	foreach ( $xpath->query( '//p[not(normalize-space()) and not(*)]' ) as $p ) {
		$p->parentNode->removeChild( $p );
	}

	foreach ( $xpath->query( '//*[@style]' ) as $el ) {
		$style = strtolower( preg_replace( '/\s+/', '', $el->getAttribute( 'style' ) ) );
		$tag   = strtolower( $el->nodeName );
		$class = '';

		if ( 'span' === $tag && false !== strpos( $style, 'border-radius:20px' ) ) {
			$class = 'alp-chip';
			alp_desc_add_class( $el->parentNode, 'alp-chips' );
		} elseif ( in_array( $tag, array( 'h2', 'h3', 'div' ), true ) && false !== strpos( $style, 'text-transform:uppercase' ) ) {
			$class = 'alp-desc__eyebrow';
		} elseif ( 'div' === $tag && false !== strpos( $style, 'border-left:3px' ) ) {
			$class = 'alp-desc__legal';
		} elseif ( 'div' === $tag && false !== strpos( $style, 'grid-template-columns' ) ) {
			$class = 'alp-desc__tiles';
		} elseif ( 'div' === $tag && false !== strpos( $style, 'text-align:center' ) && $el->parentNode instanceof DOMElement && false !== strpos( $el->parentNode->getAttribute( 'class' ), 'alp-desc__tiles' ) ) {
			$class = 'alp-desc__tile';
		} elseif ( 'div' === $tag && preg_match( '/background:#(f7fafb|f0fbf7|fff)/', $style ) ) {
			$class = 'alp-desc__panel';
		} elseif ( 'table' === $tag ) {
			$class = 'alp-desc__table';
		}

		if ( $class ) {
			alp_desc_add_class( $el, $class );
		}
		$el->removeAttribute( 'style' );
	}

	foreach ( $xpath->query( '//*[contains(concat(" ",@class," ")," alp-chips ")]/br' ) as $br ) {
		$br->parentNode->removeChild( $br );
	}

	// Tabellen scrollbar machen (schmale Displays).
	foreach ( $xpath->query( '//table' ) as $table ) {
		$wrap = $doc->createElement( 'div' );
		$wrap->setAttribute( 'class', 'alp-table-scroll' );
		$table->parentNode->replaceChild( $wrap, $table );
		$wrap->appendChild( $table );
	}

	$root = $doc->getElementById( 'alp-root' );
	$out  = '';
	if ( $root ) {
		foreach ( $root->childNodes as $child ) {
			$out .= $doc->saveHTML( $child );
		}
	}
	return $out ?: $html;
}

function alp_desc_add_class( $el, $class ) {
	if ( ! $el instanceof DOMElement ) {
		return;
	}
	$classes = preg_split( '/\s+/', trim( $el->getAttribute( 'class' ) ) );
	if ( ! in_array( $class, $classes, true ) ) {
		$classes[] = $class;
	}
	$el->setAttribute( 'class', trim( implode( ' ', $classes ) ) );
}
