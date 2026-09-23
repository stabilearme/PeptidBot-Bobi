<?php
/**
 * Startseite – Kennzahlen als schwebende „Labor-Anzeige“ zwischen Hero und Chargen-Prüfer.
 * Jede Kennzahl mit Icon und kleiner Grafik, die sich beim Einblenden aufbaut.
 * Werte: config.php → hero.stats ({coa} = Zahl der veröffentlichten Zertifikate).
 */
defined( 'ABSPATH' ) || exit;

$stats = (array) alp_config( 'hero.stats', array() );
if ( ! $stats || empty( alp_config( 'hero.image' ) ) ) {
	return; // ohne Hero-Bild stehen die Kennzahlen im Hero selbst
}
$done      = function_exists( 'alp_coa_batches' ) ? array_values( array_filter( alp_coa_batches(), fn( $b ) => $b['done'] ) ) : array();
$coa_count = count( $done );
$latest    = $done[0] ?? null; // neueste Charge steht in coa-batches.php oben
?>
<section class="alp-stats" aria-label="Kennzahlen">
	<div class="alp-container">
		<ul class="alp-stats__panel">
			<?php foreach ( $stats as $stat ) : ?>
				<?php
				$visual = $stat['visual'] ?? '';
				$fill   = max( 0, min( 100, (int) ( $stat['fill'] ?? 100 ) ) );
				?>
				<li class="alp-stats__item alp-stats__item--<?php echo esc_attr( $visual ?: 'plain' ); ?>" style="--alp-fill: <?php echo (int) $fill; ?>%;">
					<div class="alp-stats__top">
						<?php if ( ! empty( $stat['icon'] ) ) : ?>
							<span class="alp-stats__icon"><?php echo alp_icon( $stat['icon'], 20 ); // phpcs:ignore ?></span>
						<?php endif; ?>
						<div class="alp-stats__text">
							<strong class="alp-stats__value"><?php echo esc_html( alp_stat_value( $stat['value'] ) ); ?></strong>
							<span class="alp-stats__label"><?php echo esc_html( $stat['label'] ); ?></span>
						</div>
						<?php if ( 'ring' === $visual ) : ?>
							<svg class="alp-stats__ring" viewBox="0 0 44 44" aria-hidden="true" focusable="false"><circle cx="22" cy="22" r="18" pathLength="100"></circle><circle class="is-bar" cx="22" cy="22" r="18" pathLength="100"></circle><path d="m15.5 22.5 4.5 4.5 8.5-9"></path></svg>
						<?php endif; ?>
					</div>

					<?php if ( 'ring' === $visual && $latest ) : ?>
						<p class="alp-stats__note"><span class="alp-dot" aria-hidden="true"></span>Zuletzt geprüft: <span class="alp-mono"><?php echo esc_html( $latest['tested'] ); ?></span></p>
					<?php elseif ( 'meter' === $visual ) : ?>
						<div class="alp-stats__meter" aria-hidden="true"><span class="alp-stats__meter-bar"></span><span class="alp-stats__meter-mark"></span></div>
						<div class="alp-stats__scale" aria-hidden="true"><span>90</span><span>95</span><span>100 %</span></div>
					<?php elseif ( 'docs' === $visual && $coa_count ) : ?>
						<div class="alp-stats__docs" aria-hidden="true">
							<?php for ( $i = 0; $i < min( $coa_count, 16 ); $i++ ) : ?>
								<span style="--i: <?php echo (int) $i; ?>;"></span>
							<?php endfor; ?>
						</div>
					<?php elseif ( 'timeline' === $visual && ! empty( $stat['steps'] ) ) : ?>
						<ol class="alp-stats__timeline" aria-hidden="true">
							<?php foreach ( (array) $stat['steps'] as $i => $step ) : ?>
								<li style="--i: <?php echo (int) $i; ?>;"><?php echo esc_html( $step ); ?></li>
							<?php endforeach; ?>
						</ol>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
