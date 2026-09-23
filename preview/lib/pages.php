<?php
/**
 * Vorschau: Inhaltsseiten, Blogartikel, Warenkorb, Kasse, Mein Konto und Seitenübersicht.
 * Inhalte stammen aus aminolabspro.com (preview/data/pages, preview/data/posts).
 * Der Rahmen bildet Flatsomes Standard-Templates nach; das Aussehen kommt aus dem Child-Theme.
 */

/** Seiten mit eigener Überschrift im Inhalt bekommen keinen zusätzlichen Seitentitel. */
function pv_page_data( $dir ) {
	$out = array();
	foreach ( glob( ALP_PREVIEW_DATA . "/$dir/*.json" ) as $f ) {
		$d          = json_decode( file_get_contents( $f ), true );
		$d['title'] = html_entity_decode( $d['title'], ENT_QUOTES, 'UTF-8' );
		$out[ $d['slug'] ] = $d;
	}
	return $out;
}

/** Fehler im Seiteninhalt, die auch im Live-Shop sichtbar sind, als Hinweis anzeigen. */
function pv_content_warnings( $content ) {
	$notes = array();
	if ( substr_count( $content, '<style' ) > substr_count( $content, '</style>' ) ) {
		$notes[] = 'Im Seiteninhalt ist ein <code>&lt;style&gt;</code>-Block nicht geschlossen. Im Live-Shop verschwinden dadurch alles danach, also Footer, Navigation und Skripte. Die Vorschau schließt den Block, damit die Seite bedienbar bleibt.';
	}
	foreach ( (array) preg_match_all( '#<style[^>]*>(.*?)(?:</style>|$)#s', $content, $m ) ? $m[1] : array() as $css ) {
		if ( false !== strpos( $css, '<br' ) || false !== strpos( $css, '<p>' ) ) {
			$notes[] = 'WordPress hat <code>&lt;br&gt;</code>/<code>&lt;p&gt;</code>-Tags in das CSS dieser Seite eingefügt. Einige Gestaltungsregeln greifen deshalb nicht, auch im Live-Shop.';
			break;
		}
	}
	return $notes ? '<div class="pv-note"><strong>Hinweis zur Seite auf aminolabspro.com:</strong><ul><li>' . implode( '</li><li>', $notes ) . '</li></ul></div>' : '';
}

/** Offene <style>/<script>-Blöcke schließen, damit der Rest der Seite erhalten bleibt (nur Vorschau). */
function pv_close_raw_tags( $content ) {
	foreach ( array( 'style', 'script' ) as $tag ) {
		$missing = substr_count( $content, '<' . $tag ) - substr_count( $content, '</' . $tag . '>' );
		$content .= str_repeat( '</' . $tag . '>', max( 0, $missing ) );
	}
	return $content;
}

function pv_page_shell( $inner, $title = '' ) {
	$head = $title ? '<header class="entry-header"><h1 class="entry-title">' . esc_html( $title ) . '</h1></header>' : '';
	return '<div id="content" class="content-area page-wrapper" role="main"><div class="row row-main"><div class="large-12 col"><div class="col-inner">'
		. $head . $inner . '</div></div></div></div>';
}

function pv_render_content_pages() {
	foreach ( pv_page_data( 'pages' ) as $slug => $d ) {
		$content = $d['content'];
		$empty   = '' === trim( wp_strip_all_tags( $content ) );
		$inner   = '';
		if ( $empty ) {
			$inner .= '<div class="pv-note"><strong>Diese Seite ist auf aminolabspro.com derzeit leer.</strong> Im WordPress-Editor ist für „' . esc_html( $d['title'] ) . '“ kein Inhalt hinterlegt. Die Vorschau zeigt deshalb nur den Titel.</div>';
		}
		$inner  .= pv_content_warnings( $content );
		$content = pv_close_raw_tags( $content );
		$inner  .= '<div class="entry-content">' . $content . '</div>';
		$title  = preg_match( '/<h1[\s>]/i', $content ) ? '' : $d['title'];
		if ( preg_match( '/<h2[^>]*>\s*' . preg_quote( $d['title'], '/' ) . '\s*<\/h2>/iu', $content ) ) {
			$title = '';
		}
		alp_preview_ctx( 'page' );
		pv_page( $slug . '.html', 'page', $d['title'], array( 'page', 'page-id-' . $d['id'], 'alp-page-' . $slug ), pv_page_shell( $inner, $title ), null, $slug );
	}
}

function pv_render_posts() {
	$months = array( 1 => 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember' );
	foreach ( pv_page_data( 'posts' ) as $slug => $d ) {
		$t    = strtotime( $d['date'] );
		$date = date( 'j', $t ) . '. ' . $months[ (int) date( 'n', $t ) ] . ' ' . date( 'Y', $t );
		ob_start();
		?>
		<div id="content" class="blog-wrapper blog-single page-wrapper">
			<div class="row align-center">
				<div class="large-10 col">
					<article class="post type-post">
						<div class="article-inner">
							<header class="entry-header">
								<div class="entry-header-text text-center">
									<h6 class="entry-category is-xsmall"><a href="wissen.html" rel="category tag">Wissen</a></h6>
									<h1 class="entry-title"><?php echo esc_html( $d['title'] ); ?></h1>
									<div class="entry-divider is-divider small"></div>
									<div class="entry-meta uppercase is-xsmall">Veröffentlicht am <time datetime="<?php echo esc_attr( $d['date'] ); ?>"><?php echo esc_html( $date ); ?></time></div>
								</div>
							</header>
							<div class="entry-content single-page"><?php echo $d['content']; ?></div>
							<footer class="entry-meta"><a class="alp-link" href="wissen.html">← Zurück zur Wissensübersicht</a></footer>
						</div>
					</article>
				</div>
			</div>
		</div>
		<?php
		pv_page( 'artikel-' . $slug . '.html', 'post', $d['title'], array( 'single', 'single-post', 'postid-' . $d['id'] ), ob_get_clean(), null, 'artikel-' . $slug );
	}
}

/* ---------- Warenkorb & Kasse (Beispiel-Warenkorb aus lib/wp-stubs.php) ---------- */

function pv_shipping() {
	$threshold = (float) alp_config( 'free_shipping_threshold', 0 );
	return ( $threshold > 0 && WC()->cart->get_displayed_subtotal() >= $threshold ) ? 0.0 : 6.90;
}

function pv_render_cart() {
	$sub  = WC()->cart->get_displayed_subtotal();
	$ship = pv_shipping();
	ob_start();
	?>
	<div class="cart-container container page-wrapper page-checkout">
		<div class="woocommerce">
			<?php alp_steps_cart(); ?>
			<p class="pv-note pv-note--soft">Beispiel-Warenkorb für die Vorschau. Im Live-Shop steht hier, was der Kunde tatsächlich eingelegt hat.</p>
			<div class="woocommerce row row-large row-divided">
				<div class="col large-7 pb-0">
					<?php alp_free_shipping_progress(); ?>
					<form class="woocommerce-cart-form" action="warenkorb.html" method="post">
						<div class="cart-wrapper sm-touch-scroll">
							<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents">
								<thead><tr><th class="product-name" colspan="3">Produkt</th><th class="product-price">Preis</th><th class="product-quantity">Anzahl</th><th class="product-subtotal">Zwischensumme</th></tr></thead>
								<tbody>
									<?php foreach ( WC()->cart->lines() as $line ) : list( $p, $qty, $total ) = $line; ?>
										<tr class="woocommerce-cart-form__cart-item cart_item">
											<td class="product-remove"><a href="#" class="remove" aria-label="<?php echo esc_attr( $p->get_name() ); ?> entfernen" data-pv-cart>×</a></td>
											<td class="product-thumbnail"><a href="<?php echo esc_url( $p->get_permalink() ); ?>"><?php echo wp_get_attachment_image( $p->get_image_id(), 'thumbnail', false, array( 'alt' => '' ) ); ?></a></td>
											<td class="product-name" data-title="Produkt"><a href="<?php echo esc_url( $p->get_permalink() ); ?>"><?php echo esc_html( $p->get_name() ); ?></a></td>
											<td class="product-price" data-title="Preis"><?php echo wc_price( (float) $p->data['price'] ); ?></td>
											<td class="product-quantity" data-title="Anzahl">
												<div class="quantity buttons_added">
													<input type="button" value="-" class="minus button is-form" aria-label="Menge verringern" data-pv-cart>
													<input type="number" class="input-text qty text" value="<?php echo (int) $qty; ?>" min="1" aria-label="Menge" readonly>
													<input type="button" value="+" class="plus button is-form" aria-label="Menge erhöhen" data-pv-cart>
												</div>
											</td>
											<td class="product-subtotal" data-title="Zwischensumme"><?php echo wc_price( $total ); ?></td>
										</tr>
									<?php endforeach; ?>
									<tr>
										<td colspan="6" class="actions clear">
											<div class="continue-shopping pull-left text-left"><a class="button-continue-shopping button primary is-outline" href="shop.html">← Weiter einkaufen</a></div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</form>
				</div>
				<div class="cart-collaterals large-5 col pb-0">
					<div class="cart-sidebar col-inner">
						<div class="cart_totals">
							<h2>Warenkorb-Summe</h2>
							<table class="shop_table shop_table_responsive">
								<tr class="cart-subtotal"><th>Zwischensumme</th><td><?php echo wc_price( $sub ); ?></td></tr>
								<tr class="woocommerce-shipping-totals shipping"><th>Versand</th><td><?php echo $ship > 0 ? 'Standardversand: ' . wc_price( $ship ) : 'Kostenloser Versand'; ?><p class="woocommerce-shipping-destination">Versand nach <strong>Deutschland</strong>.</p></td></tr>
								<tr class="order-total"><th>Gesamtsumme</th><td><strong><?php echo wc_price( $sub + $ship ); ?></strong><small class="includes_tax"><?php echo esc_html( PV_TAX_NOTE ); ?></small></td></tr>
							</table>
							<div class="wc-proceed-to-checkout"><a href="kasse.html" class="checkout-button button alt wc-forward">Weiter zur Kasse</a></div>
						</div>
						<form class="checkout_coupon mb-0" method="post" action="warenkorb.html">
							<div class="coupon">
								<h3 class="widget-title">Gutschein</h3>
								<label for="coupon_code" class="screen-reader-text">Gutschein:</label>
								<input type="text" name="coupon_code" class="input-text" id="coupon_code" placeholder="Gutscheincode">
								<button type="submit" class="is-form expand button">Gutschein anwenden</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	pv_page( 'warenkorb.html', 'page', 'Warenkorb', array( 'page', 'woocommerce-cart', 'woocommerce-page', 'alp-page-warenkorb' ), ob_get_clean(), null, 'warenkorb' );
}

function pv_field( $id, $label, $type = 'text', $required = true, $value = '', $wide = true ) {
	return sprintf(
		'<p class="form-row %s%s" id="%s_field"><label for="%s">%s%s</label><span class="woocommerce-input-wrapper"><input type="%s" class="input-text" name="%s" id="%s" value="%s" autocomplete="off"></span></p>',
		$wide ? 'form-row-wide' : 'form-row-first',
		$required ? ' validate-required' : '',
		esc_attr( $id ), esc_attr( $id ), esc_html( $label ),
		$required ? '&nbsp;<abbr class="required" title="erforderlich">*</abbr>' : '&nbsp;<span class="optional">(optional)</span>',
		esc_attr( $type ), esc_attr( $id ), esc_attr( $id ), esc_attr( $value )
	);
}

function pv_render_checkout() {
	$sub  = WC()->cart->get_displayed_subtotal();
	$ship = pv_shipping();
	ob_start();
	?>
	<div class="cart-container container page-wrapper page-checkout">
		<div class="woocommerce">
			<?php alp_steps_checkout(); ?>
			<form name="checkout" method="post" class="checkout woocommerce-checkout" action="kasse.html" data-pv-checkout>
				<div class="row pt-0">
					<div class="large-7 col">
						<div id="customer_details">
							<div class="woocommerce-billing-fields">
								<h3>Rechnungsdetails</h3>
								<div class="woocommerce-billing-fields__field-wrapper">
									<?php
									echo pv_field( 'billing_first_name', 'Vorname' );
									echo pv_field( 'billing_last_name', 'Nachname' );
									echo pv_field( 'billing_company', 'Firmenname / Forschungseinrichtung', 'text', false );
									echo '<p class="form-row form-row-wide" id="billing_country_field"><label for="billing_country">Land / Region&nbsp;<abbr class="required" title="erforderlich">*</abbr></label><span class="woocommerce-input-wrapper"><strong>Deutschland</strong></span></p>';
									echo pv_field( 'billing_address_1', 'Straße und Hausnummer' );
									echo pv_field( 'billing_postcode', 'Postleitzahl' );
									echo pv_field( 'billing_city', 'Ort / Stadt' );
									echo pv_field( 'billing_email', 'E-Mail-Adresse', 'email' );
									echo pv_field( 'billing_phone', 'Telefon', 'tel', false );
									?>
								</div>
							</div>
							<div class="woocommerce-additional-fields">
								<h3>Zusätzliche Informationen</h3>
								<p class="form-row notes" id="order_comments_field"><label for="order_comments">Anmerkungen zur Bestellung&nbsp;<span class="optional">(optional)</span></label><span class="woocommerce-input-wrapper"><textarea name="order_comments" class="input-text" id="order_comments" placeholder="z. B. besondere Hinweise für die Lieferung" rows="2"></textarea></span></p>
							</div>
						</div>
					</div>
					<div class="large-5 col">
						<div class="col-inner has-border">
							<div class="checkout-sidebar sm-touch-scroll">
								<h3 id="order_review_heading">Deine Bestellung</h3>
								<div id="order_review" class="woocommerce-checkout-review-order">
									<table class="shop_table woocommerce-checkout-review-order-table">
										<thead><tr><th class="product-name">Produkt</th><th class="product-total">Zwischensumme</th></tr></thead>
										<tbody>
											<?php foreach ( WC()->cart->lines() as $line ) : list( $p, $qty, $total ) = $line; ?>
												<tr class="cart_item"><td class="product-name"><?php echo esc_html( $p->get_name() ); ?>&nbsp;<strong class="product-quantity">×&nbsp;<?php echo (int) $qty; ?></strong></td><td class="product-total"><?php echo wc_price( $total ); ?></td></tr>
											<?php endforeach; ?>
										</tbody>
										<tfoot>
											<tr class="cart-subtotal"><th>Zwischensumme</th><td><?php echo wc_price( $sub ); ?></td></tr>
											<tr class="woocommerce-shipping-totals shipping"><th>Versand</th><td><?php echo $ship > 0 ? 'Standardversand: ' . wc_price( $ship ) : 'Kostenloser Versand'; ?></td></tr>
											<tr class="order-total"><th>Gesamtsumme</th><td><strong><?php echo wc_price( $sub + $ship ); ?></strong><small class="includes_tax"><?php echo esc_html( PV_TAX_NOTE ); ?></small></td></tr>
										</tfoot>
									</table>
									<div id="payment" class="woocommerce-checkout-payment">
										<ul class="wc_payment_methods payment_methods methods">
											<li class="wc_payment_method payment_method_bacs">
												<input id="payment_method_bacs" type="radio" class="input-radio" name="payment_method" value="bacs" checked>
												<label for="payment_method_bacs">Vorkasse per Banküberweisung</label>
												<div class="payment_box payment_method_bacs"><p>Nach Eingang deiner Bestellung erhältst du eine Bestätigungs-E-Mail mit unseren Bankdaten. Bitte gib deine Bestellnummer als Verwendungszweck an. Der Versand erfolgt nach Zahlungseingang.</p></div>
											</li>
										</ul>
										<div class="form-row place-order">
											<div class="woocommerce-terms-and-conditions-wrapper">
												<p class="form-row legal wc-terms-and-conditions">
													<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox" for="legal">
														<input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="legal" id="legal">
														<span>Mit deiner Bestellung erklärst du dich mit unseren <a href="agb.html">Allgemeinen Geschäftsbedingungen</a>, <a href="widerrufsbelehrung.html">Widerrufsbestimmungen</a> und <a href="datenschutzerklaerung.html">Datenschutzbestimmungen</a> einverstanden.</span>&nbsp;<abbr class="required" title="erforderlich">*</abbr>
													</label>
												</p>
											</div>
											<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order">Zahlungspflichtig bestellen</button>
											<?php alp_checkout_trust(); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<?php
	pv_page( 'kasse.html', 'page', 'Kasse', array( 'page', 'woocommerce-checkout', 'woocommerce-page', 'alp-page-kasse' ), ob_get_clean(), null, 'kasse' );
}

function pv_render_account() {
	ob_start();
	?>
	<div class="page-wrapper my-account mb">
		<div class="container" role="main">
			<div class="woocommerce">
				<div class="account-container lightbox-inner">
					<div class="col2-set row row-divided row-large" id="customer_login">
						<div class="col-1 large-6 col pb-0">
							<div class="account-login-inner">
								<h3 class="uppercase">Anmelden</h3>
								<form class="woocommerce-form woocommerce-form-login login" method="post" action="mein-konto.html">
									<?php echo pv_field( 'username', 'Benutzername oder E-Mail-Adresse' ); ?>
									<?php echo pv_field( 'password', 'Passwort', 'password' ); ?>
									<p class="form-row">
										<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme"><input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme"> <span>Angemeldet bleiben</span></label>
										<button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login">Anmelden</button>
									</p>
									<p class="woocommerce-LostPassword lost_password"><a href="https://aminolabspro.com/mein-konto/lost-password/">Passwort vergessen?</a></p>
								</form>
							</div>
						</div>
						<div class="col-2 large-6 col pb-0">
							<div class="account-register-inner">
								<h3 class="uppercase">Registrieren</h3>
								<form method="post" class="woocommerce-form woocommerce-form-register register" action="mein-konto.html">
									<?php echo pv_field( 'reg_email', 'E-Mail-Adresse', 'email' ); ?>
									<p>Ein Link zum Festlegen eines neuen Passworts wird an deine E-Mail-Adresse gesendet.</p>
									<div class="woocommerce-privacy-policy-text"><p>Wir verwenden deine personenbezogenen Daten, um eine möglichst gute Benutzererfahrung auf dieser Website zu ermöglichen, den Zugriff auf dein Konto zu verwalten und für weitere Zwecke, die in unserer <a href="datenschutzerklaerung.html" class="woocommerce-privacy-policy-link">Datenschutzerklärung</a> beschrieben sind.</p></div>
									<p class="woocommerce-form-row form-row"><button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register">Registrieren</button></p>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	pv_page( 'mein-konto.html', 'page', 'Mein Konto', array( 'page', 'woocommerce-account', 'woocommerce-page', 'alp-page-mein-konto' ), ob_get_clean(), null, 'mein-konto' );
}

/* ---------- Seitenübersicht (nur Vorschau) ---------- */

function pv_render_sitemap() {
	$pages  = pv_page_data( 'pages' );
	$posts  = pv_page_data( 'posts' );
	$t      = fn( $slug ) => $pages[ $slug ]['title'] ?? $slug;
	$groups = array(
		'Shop'              => array( 'index.html' => 'Startseite', 'shop.html' => 'Shop (alle Produkte)', 'produkt-' . ALP_PREVIEW_FEATURED . '.html' => 'Produktseite (Beispiel BPC-157)', 'coa.html' => 'Laborergebnisse & COAs' ),
		'Warenkorb & Konto' => array( 'warenkorb.html' => 'Warenkorb', 'kasse.html' => 'Kasse', 'mein-konto.html' => 'Mein Konto' ),
		'Wissen & Tools'    => array( 'wissen.html' => $t( 'wissen' ), 'coas-richtig-lesen-verstehen.html' => $t( 'coas-richtig-lesen-verstehen' ), 'dosierungsrechner.html' => $t( 'dosierungsrechner' ), 'forschungsnotizen.html' => $t( 'forschungsnotizen' ) . ' (Forschungsnotizen)' ),
		'Artikel'           => array(),
		'Service'           => array( 'contakt.html' => $t( 'contakt' ), 'versandarten.html' => $t( 'versandarten' ), 'kundenwuensche.html' => $t( 'kundenwuensche' ), 'early-access.html' => $t( 'early-access' ), 'affiliate-dashboard.html' => $t( 'affiliate-dashboard' ), 'echtheit-von-bewertungen.html' => $t( 'echtheit-von-bewertungen' ) ),
		'Rechtliches'       => array( 'impressum.html' => $t( 'impressum' ), 'datenschutzerklaerung.html' => $t( 'datenschutzerklaerung' ), 'agb.html' => $t( 'agb' ), 'widerrufsbelehrung.html' => $t( 'widerrufsbelehrung' ) ),
	);
	foreach ( $posts as $slug => $d ) {
		$groups['Artikel'][ 'artikel-' . $slug . '.html' ] = $d['title'];
	}
	$empty = array();
	foreach ( $pages as $slug => $d ) {
		if ( '' === trim( wp_strip_all_tags( $d['content'] ) ) ) {
			$empty[ $slug . '.html' ] = true;
		}
	}
	ob_start();
	?>
	<section class="alp-section">
		<div class="alp-container">
			<p class="alp-eyebrow">Vorschau</p>
			<h1 class="alp-h1">Alle Seiten</h1>
			<p class="alp-lead">Jede Seite nutzt das neue Child-Theme. Die Inhalte stammen aus aminolabspro.com, Stand <?php echo esc_html( date( 'd.m.Y' ) ); ?>. Dazu kommen <?php echo count( alp_preview_products() ); ?> Produktseiten, erreichbar über den Shop.</p>
			<div class="pv-sitemap">
				<?php foreach ( $groups as $title => $links ) : ?>
					<nav aria-label="<?php echo esc_attr( $title ); ?>">
						<h2 class="alp-h4"><?php echo esc_html( $title ); ?></h2>
						<ul>
							<?php foreach ( $links as $href => $label ) : ?>
								<li><a href="<?php echo esc_url( $href ); ?>"><?php echo esc_html( $label ); ?></a><?php echo isset( $empty[ $href ] ) ? ' <span class="pv-tag">im Shop leer</span>' : ''; ?></li>
							<?php endforeach; ?>
						</ul>
					</nav>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
	pv_page( 'seiten.html', 'seiten', 'Alle Seiten', array( 'page', 'alp-page-seiten' ), ob_get_clean() );
}
