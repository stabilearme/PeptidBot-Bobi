<?php
/**
 * Startseite – Neu & demnächst: Teaser für eine kommende Neuheit + Listen „Neu im Shop“ / „Angebote“.
 * Texte & Teaser: config.php → sections.news
 */
defined( 'ABSPATH' ) || exit;

$cfg    = (array) alp_config( 'sections.news', array() );
$teaser = (array) ( $cfg['teaser'] ?? array() );
$limit  = (int) ( $cfg['limit'] ?? 4 );
$tabs   = (array) ( $cfg['tabs'] ?? array() );

// Angebote nur zeigen, wenn es reduzierte Produkte gibt.
if ( isset( $tabs['sale'] ) && function_exists( 'wc_get_product_ids_on_sale' ) && ! wc_get_product_ids_on_sale() ) {
	unset( $tabs['sale'] );
}
$shortcodes = array(
	'new'  => sprintf( '[products limit="%d" columns="%d" orderby="date" order="DESC" visibility="visible"]', $limit, $limit ),
	'sale' => sprintf( '[products limit="%d" columns="%d" on_sale="true" orderby="popularity" visibility="visible"]', $limit, $limit ),
);
if ( ! $teaser && ! $tabs ) {
	return;
}
?>
<section class="alp-section" id="neu">
	<div class="alp-container">
		<header class="alp-section__head">
			<div>
				<p class="alp-eyebrow"><?php echo esc_html( $cfg['eyebrow'] ?? '' ); ?></p>
				<h2 class="alp-h2"><?php echo esc_html( $cfg['title'] ?? '' ); ?></h2>
			</div>
		</header>

		<div class="alp-news<?php echo $teaser ? '' : ' alp-news--no-teaser'; ?>">
			<?php if ( $teaser ) : ?>
				<?php
				$has_text = ! empty( $teaser['image_has_text'] ); // Bild zeigt die Produktangaben selbst
				$light    = $has_text && 'dark' !== ( $teaser['tone'] ?? 'light' );
				?>
				<article class="alp-soon<?php echo $has_text ? ' alp-soon--full' : ''; ?><?php echo $light ? ' alp-soon--light' : ''; ?>">
					<?php if ( ! empty( $teaser['image'] ) ) : ?>
						<div class="alp-soon__media">
							<img src="<?php echo esc_url( alp_link( $teaser['image'] ) ); ?>" alt="<?php echo esc_attr( $teaser['title'] ?? '' ); ?>" loading="lazy" width="1536" height="1024">
							<?php if ( ! $has_text && ! empty( $teaser['badge'] ) ) : ?>
								<span class="alp-soon__badge"><span class="alp-dot" aria-hidden="true"></span><?php echo esc_html( $teaser['badge'] ); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<div class="alp-soon__body">
						<?php if ( $has_text && ! empty( $teaser['badge'] ) ) : ?>
							<span class="alp-soon__badge"><span class="alp-dot" aria-hidden="true"></span><?php echo esc_html( $teaser['badge'] ); ?></span>
						<?php endif; ?>
						<h3 class="alp-h3"><?php echo esc_html( $teaser['title'] ?? '' ); ?></h3>
						<p><?php echo esc_html( $teaser['text'] ?? '' ); ?></p>
						<?php if ( ! $has_text && ! empty( $teaser['features'] ) ) : ?>
							<ul class="alp-soon__features">
								<?php foreach ( (array) $teaser['features'] as $feature ) : ?>
									<li><?php echo alp_icon( 'check', 16 ); // phpcs:ignore ?><?php echo esc_html( $feature ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
						<?php if ( ! empty( $teaser['cta'] ) ) : ?>
							<a class="alp-btn alp-btn--primary" href="<?php echo esc_url( alp_link( $teaser['cta']['url'] ) ); ?>"><?php echo alp_icon( 'mail', 18 ); // phpcs:ignore ?> <?php echo esc_html( $teaser['cta']['label'] ); ?></a>
						<?php endif; ?>
						<?php if ( ! empty( $teaser['note'] ) ) : ?>
							<p class="alp-soon__note"><?php echo esc_html( $teaser['note'] ); ?></p>
						<?php endif; ?>
					</div>
				</article>
			<?php endif; ?>

			<?php if ( $tabs ) : ?>
				<div class="alp-news__lists">
					<?php if ( count( $tabs ) > 1 ) : ?>
						<div class="alp-news__tabs" role="tablist" aria-label="Produktauswahl">
							<?php $first = true; foreach ( $tabs as $key => $label ) : ?>
								<button type="button" class="alp-pill<?php echo $first ? ' is-active' : ''; ?>" role="tab" aria-selected="<?php echo $first ? 'true' : 'false'; ?>" aria-controls="alp-news-<?php echo esc_attr( $key ); ?>" data-alp-tab="alp-news-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></button>
							<?php $first = false; endforeach; ?>
						</div>
					<?php endif; ?>
					<?php $first = true; foreach ( $tabs as $key => $label ) : ?>
						<div class="alp-news__panel" id="alp-news-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-label="<?php echo esc_attr( $label ); ?>"<?php echo $first ? '' : ' hidden'; ?>>
							<div class="alp-products" data-alp-list="<?php echo esc_attr( $key ); ?>"><?php echo do_shortcode( $shortcodes[ $key ] ?? '' ); // phpcs:ignore ?></div>
						</div>
					<?php $first = false; endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
