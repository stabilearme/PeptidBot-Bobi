<?php
/**
 * Chargen-Prüfer (Eingabe + Ergebnis). Logik: assets/js/coa.js
 */
defined( 'ABSPATH' ) || exit;

$tone     = ( $args['tone'] ?? 'light' ) === 'dark' ? 'dark' : 'light';
$examples = array_slice( wp_list_pluck( array_filter( alp_coa_batches(), fn( $b ) => $b['done'] ), 'batch' ), 0, 3 );
$uid      = wp_unique_id( 'alp-lookup-' );
?>
<div class="alp-lookup alp-lookup--<?php echo esc_attr( $tone ); ?>" data-alp-lookup>
	<form class="alp-lookup__form" novalidate>
		<label class="alp-lookup__label" for="<?php echo esc_attr( $uid ); ?>">Chargennummer vom Etikett</label>
		<div class="alp-lookup__row">
			<input id="<?php echo esc_attr( $uid ); ?>" class="alp-lookup__input" type="text" inputmode="text" autocomplete="off" spellcheck="false" placeholder="z. B. <?php echo esc_attr( $examples[0] ?? 'BPC-0726-01' ); ?>" aria-describedby="<?php echo esc_attr( $uid ); ?>-hint">
			<button class="alp-btn alp-btn--primary" type="submit">Prüfen</button>
		</div>
		<?php if ( $examples ) : ?>
			<p class="alp-lookup__hint" id="<?php echo esc_attr( $uid ); ?>-hint">
				Beispiele:
				<?php foreach ( $examples as $ex ) : ?>
					<button type="button" class="alp-lookup__example" data-batch="<?php echo esc_attr( $ex ); ?>"><?php echo esc_html( $ex ); ?></button>
				<?php endforeach; ?>
			</p>
		<?php endif; ?>
	</form>
	<div class="alp-lookup__result" aria-live="polite"></div>
</div>
