<?php
/**
 * Startseite – FAQ. Fragen: /data/faq.php · Überschrift: config.php → sections.faq
 */
defined( 'ABSPATH' ) || exit;

$cfg = (array) alp_config( 'sections.faq', array() );
$faq = (array) alp_data( 'faq' );
if ( ! $faq ) {
	return;
}
$allowed = array(
	'a'      => array( 'href' => array() ),
	'strong' => array(),
	'em'     => array(),
	'br'     => array(),
);
?>
<section class="alp-section" id="faq">
	<div class="alp-container alp-faq">
		<header class="alp-faq__head">
			<p class="alp-eyebrow"><?php echo esc_html( $cfg['eyebrow'] ?? '' ); ?></p>
			<h2 class="alp-h2"><?php echo esc_html( $cfg['title'] ?? '' ); ?></h2>
			<p class="alp-section__text">Deine Frage ist nicht dabei? <a href="<?php echo esc_url( alp_link( '/contakt/' ) ); ?>">Schreib uns</a> – wir antworten in der Regel innerhalb eines Werktags.</p>
		</header>
		<div class="alp-faq__list">
			<?php foreach ( $faq as $i => $item ) : ?>
				<details class="alp-faq__item"<?php echo 0 === $i ? ' open' : ''; ?>>
					<summary><span><?php echo esc_html( $item['q'] ); ?></span><?php echo alp_icon( 'plus', 20, 'alp-faq__icon' ); // phpcs:ignore ?></summary>
					<div class="alp-faq__a"><p><?php echo wp_kses( $item['a'], $allowed ); ?></p></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>
