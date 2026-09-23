<?php
/**
 * Startseite – Kennzahlen als Mint-Leiste unter dem Hero. Werte: config.php → hero.stats
 * ({coa} wird durch die Zahl der veröffentlichten Zertifikate ersetzt).
 */
defined( 'ABSPATH' ) || exit;

$stats = (array) alp_config( 'hero.stats', array() );
if ( ! $stats || empty( alp_config( 'hero.image' ) ) ) {
	return; // ohne Hero-Bild stehen die Kennzahlen im Hero selbst
}
?>
<section class="alp-stats" aria-label="Kennzahlen">
	<dl class="alp-container alp-stats__list">
		<?php foreach ( $stats as $stat ) : ?>
			<div class="alp-stats__item">
				<dt><?php echo esc_html( $stat['label'] ); ?></dt>
				<dd><?php echo esc_html( alp_stat_value( $stat['value'] ) ); ?></dd>
			</div>
		<?php endforeach; ?>
	</dl>
</section>
