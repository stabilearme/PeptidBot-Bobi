<?php
/**
 * Übersicht aller Zertifikate mit Filterfeld.
 */
defined( 'ABSPATH' ) || exit;

$batches = alp_coa_batches();
$done    = count( array_filter( $batches, fn( $b ) => $b['done'] ) );
?>
<div class="alp-coa-grid-wrap" data-alp-coa-filter>
	<div class="alp-coa-grid-bar">
		<p class="alp-coa-grid-count"><strong><?php echo (int) $done; ?></strong> Zertifikate veröffentlicht · <?php echo (int) ( count( $batches ) - $done ); ?> in Prüfung</p>
		<label class="alp-coa-grid-filter">
			<span class="screen-reader-text">Zertifikate nach Produkt filtern</span>
			<?php echo alp_icon( 'search', 18 ); // phpcs:ignore ?>
			<input type="search" placeholder="Produkt filtern …" data-alp-filter-input>
		</label>
	</div>
	<div class="alp-coa-grid">
		<?php
		foreach ( $batches as $batch ) {
			alp_part( 'coa/card', array( 'batch' => $batch ) );
		}
		?>
	</div>
	<p class="alp-coa-grid-empty" hidden>Kein Zertifikat zu diesem Suchbegriff gefunden.</p>
</div>
