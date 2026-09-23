<?php
/**
 * AminoLabs Pro 2 – Child Theme für Flatsome.
 *
 * Diese Datei lädt nur die Module aus /inc/. Bitte hier nichts direkt einbauen,
 * sondern im passenden Modul (siehe README.md).
 */

defined( 'ABSPATH' ) || exit;

define( 'ALP_VERSION', '2.2.6' );
define( 'ALP_DIR', get_stylesheet_directory() );
define( 'ALP_URI', get_stylesheet_directory_uri() );

$alp_modules = array(
	'helpers',      // Hilfsfunktionen: alp_config(), alp_icon(), alp_part() …
	'setup',        // Assets, Schriften, Body-Klassen, Übernahme der alten Flatsome-Einstellungen
	'flatsome',     // Header-Leiste, eigener Footer, Mobile-Navigation
	'coa',          // Chargen-/COA-Daten, Chargen-Prüfer, COA-Box am Produkt
	'description',  // Einheitliche Darstellung der Produktbeschreibungen
	'content',      // Einheitliche Darstellung von Seiten und Beiträgen
	'woocommerce',  // Shop, Produktseite, Warenkorb, Kasse
	'schema',       // Strukturierte Daten (FAQ) – ergänzt Rank Math
);

foreach ( $alp_modules as $alp_module ) {
	require_once ALP_DIR . '/inc/' . $alp_module . '.php';
}
