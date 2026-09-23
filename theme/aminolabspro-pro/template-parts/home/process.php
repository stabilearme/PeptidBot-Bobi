<?php
/**
 * Startseite – Ablauf in Schritten. Texte: config.php → sections.process
 */
defined( 'ABSPATH' ) || exit;

$cfg = (array) alp_config( 'sections.process', array() );
if ( empty( $cfg['steps'] ) ) {
	return;
}
?>
<section class="alp-section alp-section--tint" id="ablauf">
	<div class="alp-container">
		<header class="alp-section__head">
			<div>
				<p class="alp-eyebrow"><?php echo esc_html( $cfg['eyebrow'] ?? '' ); ?></p>
				<h2 class="alp-h2"><?php echo esc_html( $cfg['title'] ?? '' ); ?></h2>
			</div>
		</header>
		<ol class="alp-process">
			<?php foreach ( $cfg['steps'] as $i => $step ) : ?>
				<li class="alp-process__step">
					<span class="alp-process__n"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<h3 class="alp-h4"><?php echo esc_html( $step['title'] ); ?></h3>
					<p><?php echo esc_html( $step['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
