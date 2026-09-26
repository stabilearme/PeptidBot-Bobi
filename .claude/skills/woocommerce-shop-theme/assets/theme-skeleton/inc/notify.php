<?php
/**
 * „Benachrichtigen lassen“ für kommende Produkte (z. B. Vial-Kühlschrank auf der Startseite).
 *
 * Besucher tragen ihre E-Mail direkt in der Karte ein. Die Adressen werden in WordPress
 * gespeichert (Option 'alp_notify_signups', getrennt nach Liste) – NICHT im Newsletter.
 * Admin: WooCommerce → Benachrichtigungen: Liste ansehen, als CSV herunterladen
 * (für den Import in Brevo), einzelne Einträge löschen.
 *
 * Einstellungen: config.php → sections.news.teaser.notify
 */

defined( 'ABSPATH' ) || exit;

define( 'ALP_NOTIFY_OPTION', 'alp_notify_signups' );

/** Alle Einträge: [ liste => [ email => [ 'date' => …, 'text' => Einwilligungstext ] ] ] */
function alp_notify_all() {
	$all = get_option( ALP_NOTIFY_OPTION, array() );
	return is_array( $all ) ? $all : array();
}

/**
 * Formular für die Karte. $notify: list, button, placeholder, consent, success.
 */
function alp_notify_form( $notify ) {
	$list = sanitize_key( $notify['list'] ?? '' );
	if ( ! $list ) {
		return '';
	}
	$status = sanitize_key( wp_unslash( $_GET['alp_notify'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification
	if ( 'ok' === $status && $list === sanitize_key( wp_unslash( $_GET['list'] ?? '' ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return '<p class="alp-notify__done" role="status">' . alp_icon( 'check', 18 ) . '<span>' . esc_html( $notify['success'] ?? 'Danke! Wir melden uns, sobald es verfügbar ist.' ) . '</span></p>';
	}
	$id = 'alp-notify-' . $list;
	ob_start();
	?>
	<form class="alp-notify" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
		<input type="hidden" name="action" value="alp_notify">
		<input type="hidden" name="list" value="<?php echo esc_attr( $list ); ?>">
		<input type="hidden" name="t" value="<?php echo esc_attr( (string) time() ); ?>">
		<input class="alp-nl-form__trap" type="text" name="website" value="" tabindex="-1" autocomplete="off" aria-hidden="true">
		<?php if ( 'error' === $status ) : ?>
			<p class="alp-notify__error" role="alert">Bitte gib eine gültige E-Mail-Adresse ein.</p>
		<?php endif; ?>
		<div class="alp-notify__row">
			<label class="screen-reader-text" for="<?php echo esc_attr( $id ); ?>">E-Mail-Adresse</label>
			<input class="alp-notify__input" id="<?php echo esc_attr( $id ); ?>" type="email" name="email" required autocomplete="email" inputmode="email" placeholder="<?php echo esc_attr( $notify['placeholder'] ?? 'deine@email.de' ); ?>">
			<button class="alp-btn alp-btn--primary alp-notify__btn" type="submit"><?php echo alp_icon( 'mail', 18 ); // phpcs:ignore ?> <span><?php echo esc_html( $notify['button'] ?? 'Benachrichtigen lassen' ); ?></span></button>
		</div>
		<?php if ( ! empty( $notify['consent'] ) ) : ?>
			<p class="alp-notify__note"><?php echo wp_kses( $notify['consent'], array( 'a' => array( 'href' => array() ) ) ); ?></p>
		<?php endif; ?>
	</form>
	<?php
	return ob_get_clean();
}

/* Eintragen (auch für nicht eingeloggte Besucher). Kein Nonce: die Startseite wird gecacht. */
add_action( 'admin_post_nopriv_alp_notify', 'alp_notify_submit' );
add_action( 'admin_post_alp_notify', 'alp_notify_submit' );
function alp_notify_submit() {
	// phpcs:disable WordPress.Security.NonceVerification
	$list   = sanitize_key( wp_unslash( $_POST['list'] ?? '' ) );
	$email  = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$trap   = (string) wp_unslash( $_POST['website'] ?? '' );
	$t      = (int) ( $_POST['t'] ?? 0 );
	// phpcs:enable
	$notify = (array) alp_config( 'sections.news.teaser.notify', array() );
	$back   = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$back   = remove_query_arg( array( 'alp_notify', 'list' ), strtok( $back, '#' ) );

	if ( ! $list || sanitize_key( $notify['list'] ?? '' ) !== $list ) {
		wp_safe_redirect( $back );
		exit;
	}
	// Bots: Falle ausgefüllt oder Formular in unter 2 Sekunden abgeschickt → still ignorieren.
	$is_bot = '' !== $trap || ( $t && time() - $t < 2 );
	// Höchstens 10 Einträge pro Stunde je IP.
	$ip_key = 'alp_ntf_' . md5( (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) );
	$count  = (int) get_transient( $ip_key );

	if ( ! $is_bot && $count < 10 ) {
		if ( ! is_email( $email ) ) {
			wp_safe_redirect( add_query_arg( array( 'alp_notify' => 'error', 'list' => $list ), $back ) . '#neu' );
			exit;
		}
		set_transient( $ip_key, $count + 1, HOUR_IN_SECONDS );
		$all = alp_notify_all();
		$key = strtolower( $email );
		if ( empty( $all[ $list ][ $key ] ) ) {
			$all[ $list ][ $key ] = array(
				'date' => current_time( 'mysql' ),
				'text' => wp_strip_all_tags( (string) ( $notify['consent'] ?? '' ) ),
			);
			update_option( ALP_NOTIFY_OPTION, $all, false );
		}
	}
	wp_safe_redirect( add_query_arg( array( 'alp_notify' => 'ok', 'list' => $list ), $back ) . '#neu' );
	exit;
}

/* ---------- Admin: WooCommerce → Benachrichtigungen ---------- */
add_action( 'admin_menu', 'alp_notify_admin_menu', 60 );
function alp_notify_admin_menu() {
	add_submenu_page( 'woocommerce', 'Benachrichtigungen', 'Benachrichtigungen', 'manage_woocommerce', 'alp-notify', 'alp_notify_admin_page' );
}

function alp_notify_admin_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}
	$all = alp_notify_all();
	echo '<div class="wrap"><h1>Benachrichtigungen („Benachrichtigen lassen“)</h1>';
	echo '<p>E-Mail-Adressen von Besuchern, die informiert werden möchten, sobald ein Produkt verfügbar ist. Für Brevo: CSV herunterladen und unter Kontakte → Kontakte importieren in eine eigene Liste laden.</p>';
	if ( ! $all ) {
		echo '<p><em>Noch keine Einträge.</em></p></div>';
		return;
	}
	foreach ( $all as $list => $rows ) {
		$csv = wp_nonce_url( admin_url( 'admin-post.php?action=alp_notify_csv&list=' . rawurlencode( $list ) ), 'alp_notify_csv' );
		echo '<h2 style="margin-top:2em">' . esc_html( $list ) . ' (' . count( $rows ) . ')</h2>';
		echo '<p><a class="button button-primary" href="' . esc_url( $csv ) . '">Als CSV herunterladen</a></p>';
		echo '<table class="widefat striped" style="max-width:760px"><thead><tr><th>E-Mail</th><th>Eingetragen am</th><th></th></tr></thead><tbody>';
		foreach ( array_reverse( $rows, true ) as $email => $row ) {
			$del = wp_nonce_url( admin_url( 'admin-post.php?action=alp_notify_delete&list=' . rawurlencode( $list ) . '&email=' . rawurlencode( $email ) ), 'alp_notify_delete' );
			echo '<tr><td>' . esc_html( $email ) . '</td><td>' . esc_html( $row['date'] ?? '' ) . '</td><td><a href="' . esc_url( $del ) . '" style="color:#b32d2e">Löschen</a></td></tr>';
		}
		echo '</tbody></table>';
	}
	echo '</div>';
}

add_action( 'admin_post_alp_notify_csv', 'alp_notify_csv' );
function alp_notify_csv() {
	if ( ! current_user_can( 'manage_woocommerce' ) || ! check_admin_referer( 'alp_notify_csv' ) ) {
		wp_die( 'Keine Berechtigung.' );
	}
	$list = sanitize_key( wp_unslash( $_GET['list'] ?? '' ) );
	$rows = alp_notify_all()[ $list ] ?? array();
	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="benachrichtigen-' . $list . '-' . gmdate( 'Y-m-d' ) . '.csv"' );
	$out = fopen( 'php://output', 'w' );
	fputcsv( $out, array( 'EMAIL', 'EINGETRAGEN_AM' ), ';' );
	foreach ( $rows as $email => $row ) {
		fputcsv( $out, array( $email, $row['date'] ?? '' ), ';' );
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	exit;
}

add_action( 'admin_post_alp_notify_delete', 'alp_notify_delete' );
function alp_notify_delete() {
	if ( ! current_user_can( 'manage_woocommerce' ) || ! check_admin_referer( 'alp_notify_delete' ) ) {
		wp_die( 'Keine Berechtigung.' );
	}
	$list  = sanitize_key( wp_unslash( $_GET['list'] ?? '' ) );
	$email = strtolower( sanitize_email( wp_unslash( $_GET['email'] ?? '' ) ) );
	$all   = alp_notify_all();
	unset( $all[ $list ][ $email ] );
	if ( isset( $all[ $list ] ) && ! $all[ $list ] ) {
		unset( $all[ $list ] );
	}
	update_option( ALP_NOTIFY_OPTION, $all, false );
	wp_safe_redirect( admin_url( 'admin.php?page=alp-notify' ) );
	exit;
}
