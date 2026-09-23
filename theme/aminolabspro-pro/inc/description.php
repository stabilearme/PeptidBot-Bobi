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
	foreach ( $xpath->query( '//p[not(normalize-space()) and not(*) and not(@id) and not(@class)]' ) as $p ) {
		$p->parentNode->removeChild( $p );
	}

	foreach ( $xpath->query( '//*[@style]' ) as $el ) {
		$style = strtolower( preg_replace( '/\s+/', '', $el->getAttribute( 'style' ) ) );
		$class = alp_desc_class_for( strtolower( $el->nodeName ), $style, $el->parentNode instanceof DOMElement ? $el->parentNode->getAttribute( 'class' ) : '' );

		if ( 'alp-chip' === $class ) {
			alp_desc_add_class( $el->parentNode, 'alp-chips' );
		}
		if ( $class ) {
			alp_desc_add_class( $el, $class );
		}
		// Ausgeblendete Elemente (z. B. Rechner-Ergebnis) bleiben ausgeblendet – Skripte blenden sie ein.
		if ( preg_match( '/(^|;)display:none/', $style ) ) {
			$el->setAttribute( 'style', 'display:none' );
		} else {
			$el->removeAttribute( 'style' );
		}
	}

	// Leere Boxen des alten Designs (nur Abstandshalter/Trennlinien) entfernen.
	foreach ( $xpath->query( '//div[not(@id) and ( not(@class) or starts-with(@class, "alp-desc__") ) and not(normalize-space()) and not(.//img or .//svg or .//input or .//select or .//textarea or .//button or .//iframe or .//video or .//canvas)]' ) as $div ) {
		$div->parentNode->removeChild( $div );
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

	// Ausgabe: Inhalt des Hilfs-Containers – plus alles, was durch ein überzähliges </div> im
	// Seiteninhalt dahinter gelandet ist (sonst ginge dieser Teil verloren).
	$out = '';
	foreach ( $doc->childNodes as $node ) {
		if ( $node instanceof DOMElement && 'alp-root' === $node->getAttribute( 'id' ) ) {
			foreach ( $node->childNodes as $child ) {
				$out .= $doc->saveHTML( $child );
			}
		} elseif ( XML_PI_NODE !== $node->nodeType ) {
			$out .= $doc->saveHTML( $node );
		}
	}
	return $out ?: $html;
}

/**
 * Ordnet einem Element anhand seines alten Inline-Styles eine Theme-Klasse zu.
 * Die Muster stammen aus den Produktbeschreibungen, Seiten und Wissensartikeln.
 */
function alp_desc_class_for( $tag, $style, $parent_class = '' ) {
	$has  = static fn( $needle ) => false !== strpos( $style, $needle );
	$dark = (bool) preg_match( '/background:(#(1a1f2e|0e1a22|0b151c|122029|111|000)\b|linear-gradient\([^;]*#(1a1f2e|0e1a22|0b151c))/', $style );
	$box  = (bool) preg_match( '/(^|;)(min-)?width:\d{2}px/', $style ) && $has( 'border-radius' );

	if ( 'span' === $tag ) {
		if ( $has( 'border-radius:20px' ) ) {
			return 'alp-chip';
		}
		if ( $has( 'position:absolute' ) ) {
			return 'alp-desc__deco';
		}
		if ( $has( 'text-transform:uppercase' ) ) {
			return 'alp-desc__eyebrow';
		}
		return $box ? 'alp-desc__badge' : '';
	}
	if ( in_array( $tag, array( 'h2', 'h3' ), true ) ) {
		if ( $has( 'text-transform:uppercase' ) ) {
			return 'alp-desc__eyebrow';
		}
		return $has( 'display:flex' ) ? 'alp-desc__numhead' : '';
	}
	if ( 'table' === $tag ) {
		return 'alp-desc__table';
	}
	if ( 'div' !== $tag ) {
		return '';
	}
	if ( $has( 'text-transform:uppercase' ) ) {
		return 'alp-desc__eyebrow';
	}
	if ( preg_match( '/border-left:[34]px/', $style ) ) {
		return 'alp-desc__legal';
	}
	if ( $has( 'grid-template-columns' ) ) {
		return 'alp-desc__tiles';
	}
	if ( $dark ) {
		return 'alp-desc__dark';
	}
	if ( $box && $has( 'display:flex' ) ) {
		return 'alp-desc__icon';
	}
	if ( $has( 'text-align:center' ) && false !== strpos( $parent_class, 'alp-desc__tiles' ) ) {
		return 'alp-desc__tile';
	}
	if ( $has( 'border-radius' ) && $has( 'background' ) ) {
		return $has( 'text-align:center' ) ? 'alp-desc__tile' : 'alp-desc__panel';
	}
	if ( $has( 'display:flex' ) ) {
		return 'alp-desc__row';
	}
	return '';
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
