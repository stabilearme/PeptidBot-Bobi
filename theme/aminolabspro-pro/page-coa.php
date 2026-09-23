<?php
/**
 * Seite „Laborergebnisse & COAs“ (Slug: coa) – wird automatisch verwendet.
 * Daten: /data/coa-batches.php · Aussehen: assets/css/coa.css
 *
 * Eigener Text aus dem WordPress-Editor dieser Seite erscheint unter der Übersicht,
 * wenn er KEINE alten COA-Karten enthält (die werden jetzt automatisch erzeugt).
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div id="alp-coa" class="alp-coa-page">
	<section class="alp-section alp-section--dark alp-coa-hero">
		<div class="alp-container alp-coa-hero__grid">
			<div>
				<p class="alp-eyebrow alp-eyebrow--light">Laborberichte</p>
				<h1 class="alp-h1"><?php echo esc_html( get_the_title() ); ?></h1>
				<p class="alp-lead">Jede Charge wird von einem unabhängigen Labor per HPLC auf Reinheit und Wirkstoffgehalt analysiert. Hier findest du alle Zertifikate – oder prüfst direkt die Nummer auf deinem Vial.</p>
			</div>
			<?php alp_part( 'coa/lookup', array( 'tone' => 'dark' ) ); ?>
		</div>
	</section>

	<section class="alp-section">
		<div class="alp-container">
			<?php alp_part( 'coa/grid' ); ?>
		</div>
	</section>

	<?php
	while ( have_posts() ) :
		the_post();
		$alp_content = get_the_content();
		if ( trim( wp_strip_all_tags( $alp_content ) ) && false === strpos( $alp_content, 'coa-card' ) ) :
			?>
			<section class="alp-section alp-section--tint">
				<div class="alp-container alp-prose">
					<?php the_content(); ?>
				</div>
			</section>
			<?php
		endif;
	endwhile;
	?>

	<section class="alp-section alp-section--tint">
		<div class="alp-container alp-coa-explain">
			<h2 class="alp-h3">So liest du ein Zertifikat</h2>
			<dl class="alp-coa-explain__list">
				<div><dt>Reinheit (HPLC)</dt><dd>Anteil des Zielpeptids an allen detektierten Substanzen. Research-Grade beginnt bei ≥ 98 %.</dd></div>
				<div><dt>Wirkstoffgehalt</dt><dd>Tatsächlich gemessene Menge im Vial. Leichte Abweichungen nach oben sind üblich.</dd></div>
				<div><dt>Report-ID</dt><dd>Eindeutige Kennung des Laborberichts – damit lässt sich das Zertifikat beim Labor verifizieren.</dd></div>
			</dl>
			<a class="alp-link" href="<?php echo esc_url( alp_link( '/coas-richtig-lesen-verstehen/' ) ); ?>">Ausführlicher Leitfaden <?php echo alp_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
		</div>
	</section>
</div>
<?php
get_footer();
