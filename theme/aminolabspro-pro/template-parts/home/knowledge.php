<?php
/**
 * Startseite – Wissen: die neuesten Artikel mit Titelbild.
 * Ohne Artikel mit Bild: die Kacheln aus config.php → sections.knowledge.items.
 */
defined( 'ABSPATH' ) || exit;

$cfg   = (array) alp_config( 'sections.knowledge', array() );
$posts = array();
if ( ! empty( $cfg['posts'] ) && function_exists( 'get_posts' ) ) {
	foreach ( get_posts( array( 'numberposts' => (int) $cfg['posts'] * 2, 'post_status' => 'publish' ) ) as $post ) {
		$img = get_the_post_thumbnail_url( $post, 'medium_large' );
		if ( $img && count( $posts ) < (int) $cfg['posts'] ) {
			$cats    = get_the_category( $post->ID );
			$posts[] = array(
				'url'   => get_permalink( $post ),
				'title' => get_the_title( $post ),
				'img'   => $img,
				'cat'   => $cats ? $cats[0]->name : '',
			);
		}
	}
}
if ( ! $posts && empty( $cfg['items'] ) ) {
	return;
}
?>
<section class="alp-section" id="wissen">
	<div class="alp-container">
		<header class="alp-section__head">
			<div>
				<p class="alp-eyebrow"><?php echo esc_html( $cfg['eyebrow'] ?? '' ); ?></p>
				<h2 class="alp-h2"><?php echo esc_html( $cfg['title'] ?? '' ); ?></h2>
			</div>
			<?php if ( ! empty( $cfg['cta'] ) ) : ?>
				<a class="alp-link" href="<?php echo esc_url( alp_link( $cfg['cta']['url'] ) ); ?>"><?php echo esc_html( $cfg['cta']['label'] ); ?> <?php echo alp_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</header>
		<?php if ( $posts ) : ?>
			<ul class="alp-articles">
				<?php foreach ( $posts as $item ) : ?>
					<li>
						<a class="alp-article" href="<?php echo esc_url( $item['url'] ); ?>">
							<span class="alp-article__img"><img src="<?php echo esc_url( $item['img'] ); ?>" alt="" loading="lazy" width="768" height="430"></span>
							<span class="alp-article__body">
								<?php if ( $item['cat'] ) : ?><span class="alp-article__cat"><?php echo esc_html( $item['cat'] ); ?></span><?php endif; ?>
								<span class="alp-article__title"><?php echo esc_html( $item['title'] ); ?></span>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<ul class="alp-know">
				<?php foreach ( $cfg['items'] as $item ) : ?>
					<li>
						<a class="alp-know__card" href="<?php echo esc_url( alp_link( $item['url'] ) ); ?>">
							<span class="alp-know__icon"><?php echo alp_icon( $item['icon'], 24 ); // phpcs:ignore ?></span>
							<h3 class="alp-h4"><?php echo esc_html( $item['title'] ); ?></h3>
							<p><?php echo esc_html( $item['text'] ); ?></p>
							<span class="alp-link">Öffnen <?php echo alp_icon( 'arrow', 16 ); // phpcs:ignore ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
