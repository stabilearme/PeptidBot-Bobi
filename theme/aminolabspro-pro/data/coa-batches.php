<?php
/**
 * ============================================================
 *  CHARGEN & LABORZERTIFIKATE (COA)
 * ============================================================
 *  Neue Charge = neuen Block kopieren und anpassen.
 *  Die NEUESTE Charge eines Produkts gehört nach OBEN.
 *
 *  batch    Chargennummer wie auf dem Etikett (Suche ist unabhängig von Groß-/Kleinschreibung)
 *  sku      Artikelnummer des Produkts in WooCommerce → verknüpft die Charge mit der Produktseite
 *  product  Anzeigename
 *  status   'done' = Zertifikat verfügbar | 'pending' = Test läuft
 *  content  Gemessener Wirkstoffgehalt in mg (Zahl mit Punkt, z. B. 10.44)
 *  purity   HPLC-Reinheit in % (Zahl)
 *  report   Report-ID des Labors
 *  cert     Zertifikatsnummer
 *  lab      Prüflabor
 *  tested   Testzeitraum (Text)
 *  photo    Bild vom Vial / Ergebnis (Dateiname in Mediathek-Ordner, siehe $uploads)
 *  file     Vollständiges Zertifikat (Dateiname)
 *  expected Nur bei 'pending': voraussichtliche Veröffentlichung
 * ============================================================
 */

defined( 'ABSPATH' ) || exit;

$uploads = '/wp-content/uploads/2026/08/';
$lab     = 'Analiza Białek';
$tested  = '21.–29.07.2026';

return array(
	array( 'batch' => 'BPC-0726-01', 'sku' => 'ALP-BPC157-10', 'product' => 'BPC-157 10 mg',     'status' => 'done', 'content' => 10.44,  'purity' => 99, 'report' => 'V19K6Z4Q', 'cert' => '100016423', 'lab' => $lab, 'tested' => $tested, 'photo' => $uploads . 'coa-result-V19K6Z4Q.webp', 'file' => $uploads . 'COA-V19K6Z4Q-scaled.webp' ),
	array( 'batch' => 'TB5-0726-01', 'sku' => 'ALP-TB500-10',  'product' => 'TB-500 10 mg',      'status' => 'done', 'content' => 11.18,  'purity' => 99, 'report' => 'W4JH17C5', 'cert' => '100016421', 'lab' => $lab, 'tested' => $tested, 'photo' => $uploads . 'coa-result-W4JH17C5.webp', 'file' => $uploads . 'COA-W4JH17C5-scaled.webp' ),
	array( 'batch' => 'R10-0726-01', 'sku' => 'ALP-RETA-10',   'product' => 'Retatrutide 10 mg', 'status' => 'done', 'content' => 10.78,  'purity' => 99, 'report' => '5T89J2NA', 'cert' => '100016428', 'lab' => $lab, 'tested' => $tested, 'photo' => $uploads . 'coa-result-5T89J2NA.webp', 'file' => $uploads . 'COA-5T89J2NA-scaled.webp' ),
	array( 'batch' => 'R30-0726-01', 'sku' => 'ALP-RETA-30',   'product' => 'Retatrutide 30 mg', 'status' => 'done', 'content' => 32.50,  'purity' => 99, 'report' => '1Q56Y3BJ', 'cert' => '100016430', 'lab' => $lab, 'tested' => $tested, 'photo' => $uploads . 'coa-result-1Q56Y3BJ.webp', 'file' => $uploads . 'COA-1Q56Y3BJ-scaled.webp' ),
	array( 'batch' => 'GH5-0726-01', 'sku' => 'ALP-GHKCU-50',  'product' => 'GHK-Cu 50 mg',      'status' => 'done', 'content' => 55.20,  'purity' => 99, 'report' => '1R78UC4M', 'cert' => '100016422', 'lab' => $lab, 'tested' => $tested, 'photo' => $uploads . 'coa-result-1R78UC4M.webp', 'file' => $uploads . 'COA-1R78UC4M-scaled.webp' ),
	array( 'batch' => 'GHK-0726-01', 'sku' => 'ALP-GHKCU-100', 'product' => 'GHK-Cu 100 mg',     'status' => 'done', 'content' => 113.33, 'purity' => 99, 'report' => 'D3Z18K4A', 'cert' => '100016420', 'lab' => $lab, 'tested' => $tested, 'photo' => $uploads . 'coa-result-D3Z18K4A.webp', 'file' => $uploads . 'COA-D3Z18K4A-scaled.webp' ),
	array( 'batch' => 'MOT-0726-01', 'sku' => 'ALP-MOTSC-40',  'product' => 'MOTS-c 40 mg',      'status' => 'done', 'content' => 40.16,  'purity' => 99, 'report' => 'F1Z7U16V', 'cert' => '100016426', 'lab' => $lab, 'tested' => $tested, 'photo' => $uploads . 'coa-result-F1Z7U16V.webp', 'file' => $uploads . 'COA-F1Z7U16V-scaled.webp' ),
	array( 'batch' => 'MOT-0726-02', 'sku' => 'ALP-MOTSC-10',  'product' => 'MOTS-c 10 mg',      'status' => 'done', 'content' => 10.20,  'purity' => 99, 'report' => 'D3UT16B8', 'cert' => '100016427', 'lab' => $lab, 'tested' => $tested, 'photo' => $uploads . 'coa-result-D3UT16B8.webp', 'file' => $uploads . 'COA-D3UT16B8-scaled.webp' ),
	array( 'batch' => 'SEM-0726-01', 'sku' => 'ALP-SEMAX-10',  'product' => 'Semax 10 mg',       'status' => 'done', 'content' => 11.02,  'purity' => 99, 'report' => 'Q1C64J8A', 'cert' => '100016424', 'lab' => $lab, 'tested' => $tested, 'photo' => $uploads . 'coa-result-Q1C64J8A.webp', 'file' => $uploads . 'COA-Q1C64J8A-scaled.webp' ),
	array( 'batch' => 'SEL-0726-01', 'sku' => 'ALP-SELANK-10', 'product' => 'Selank 10 mg',      'status' => 'done', 'content' => 10.62,  'purity' => 99, 'report' => '13Z7F4HK', 'cert' => '100016425', 'lab' => $lab, 'tested' => $tested, 'photo' => $uploads . 'coa-result-13Z7F4HK.webp', 'file' => $uploads . 'COA-13Z7F4HK-scaled.webp' ),
	array( 'batch' => 'MT2-0726-01', 'sku' => 'ALP-MT2-10',    'product' => 'Melanotan 2 10 mg', 'status' => 'done', 'content' => 10.74,  'purity' => 99, 'report' => 'Q9K16D7M', 'cert' => '100016429', 'lab' => $lab, 'tested' => $tested, 'photo' => $uploads . 'coa-result-Q9K16D7M.webp', 'file' => $uploads . 'COA-Q9K16D7M-scaled.webp' ),
	array( 'batch' => 'CAG-0726-01', 'sku' => 'ALP-CAGRI-5',   'product' => 'Cagrilintide 5 mg', 'status' => 'pending', 'expected' => 'in Kürze', 'lab' => $lab ),
);
