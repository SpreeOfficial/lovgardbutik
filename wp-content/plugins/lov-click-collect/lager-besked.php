<?php
/**
 * "Giv mig besked, når varen er på lager"
 *
 * - På produktsiden for en udsolgt vare vises en lille formular (e-mail + samtykke).
 * - Tilmeldinger gemmes på produktet (kun e-mail og tidspunkt).
 * - Når varen sættes "På lager", får alle tilmeldte én e-mail, og listen slettes.
 * - I admin kan man se, hvor mange der venter, på produktets redigeringsside.
 */

defined( 'ABSPATH' ) || exit;

const LOV_NOTIFY_META = '_lov_lager_besked';

/** Formularen (bruges i stedet for "Tilføj til liste" på udsolgte varer). */
function lov_notify_form( WC_Product $product ): string {
	$id     = $product->get_id();
	$status = isset( $_GET['lov_besked'] ) ? sanitize_key( wp_unslash( $_GET['lov_besked'] ) ) : '';
	$msg    = '';
	if ( 'ok' === $status ) {
		$msg = '<p class="lov-notify-msg is-ok" role="status">Tak! Vi sender dig en e-mail, så snart varen er på lager igen.</p>';
	} elseif ( 'fejl' === $status ) {
		$msg = '<p class="lov-notify-msg is-error" role="alert">Skriv en gyldig e-mail, og sæt hak i feltet nedenfor.</p>';
	}
	if ( 'ok' === $status ) {
		return '<div class="lov-notify">' . $msg . '</div>';
	}
	ob_start();
	?>
	<form class="lov-notify" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<p class="lov-notify-title">Få besked, når varen er på lager igen</p>
		<?php echo $msg; // phpcs:ignore -- statisk tekst ovenfor ?>
		<input type="hidden" name="action" value="lov_lager_besked">
		<input type="hidden" name="product_id" value="<?php echo (int) $id; ?>">
		<?php wp_nonce_field( 'lov_lager_besked_' . $id, 'lov_nonce' ); ?>
		<p class="lov-notify-hp" aria-hidden="true"><label>Lad dette felt være tomt <input type="text" name="lov_hp" tabindex="-1" autocomplete="off"></label></p>
		<div class="lov-notify-row">
			<label class="screen-reader-text" for="lov-notify-email-<?php echo (int) $id; ?>">Din e-mail</label>
			<input type="email" id="lov-notify-email-<?php echo (int) $id; ?>" name="email" required autocomplete="email" placeholder="din@email.dk">
			<button type="submit" class="wp-element-button">Giv mig besked</button>
		</div>
		<p class="lov-notify-consent">
			<input type="checkbox" id="lov-notify-ok-<?php echo (int) $id; ?>" name="samtykke" value="1" required>
			<label for="lov-notify-ok-<?php echo (int) $id; ?>">Ja tak, send mig én e-mail, når varen er på lager. Din e-mail bruges kun til dette og slettes, når beskeden er sendt.</label>
		</p>
	</form>
	<?php
	return ob_get_clean();
}

/** Modtag tilmelding (både for besøgende og indloggede). */
function lov_notify_handle() {
	$id   = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$back = $id ? get_permalink( $id ) : home_url( '/' );
	if ( ! $id || ! isset( $_POST['lov_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lov_nonce'] ) ), 'lov_lager_besked_' . $id ) ) {
		wp_safe_redirect( add_query_arg( 'lov_besked', 'fejl', $back ) . '#lov-besked' );
		exit;
	}
	// Spam-fælde: rigtige mennesker udfylder ikke det skjulte felt.
	if ( ! empty( $_POST['lov_hp'] ) ) {
		wp_safe_redirect( add_query_arg( 'lov_besked', 'ok', $back ) . '#lov-besked' );
		exit;
	}
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( ! is_email( $email ) || empty( $_POST['samtykke'] ) || ! wc_get_product( $id ) ) {
		wp_safe_redirect( add_query_arg( 'lov_besked', 'fejl', $back ) . '#lov-besked' );
		exit;
	}
	$list = (array) get_post_meta( $id, LOV_NOTIFY_META, true );
	$key  = strtolower( $email );
	if ( ! isset( $list[ $key ] ) ) {
		$list[ $key ] = array( 'email' => $email, 'tid' => time() );
		update_post_meta( $id, LOV_NOTIFY_META, $list );
	}
	wp_safe_redirect( add_query_arg( 'lov_besked', 'ok', $back ) . '#lov-besked' );
	exit;
}
add_action( 'admin_post_nopriv_lov_lager_besked', 'lov_notify_handle' );
add_action( 'admin_post_lov_lager_besked', 'lov_notify_handle' );

/** Varen er på lager igen: send beskeder og slet listen. */
function lov_notify_send( $product_id ) {
	$product = wc_get_product( $product_id );
	if ( ! $product || ! $product->is_in_stock() ) {
		return;
	}
	$list = (array) get_post_meta( $product_id, LOV_NOTIFY_META, true );
	$list = array_filter( $list, 'is_array' );
	if ( ! $list ) {
		return;
	}
	$name    = $product->get_name();
	$url     = get_permalink( $product_id );
	$subject = sprintf( '%s er på lager igen hos Lov Gårdbutik', $name );
	$body    = '<p>Hej,</p>'
		. '<p>Du bad os give besked, når <strong>' . esc_html( $name ) . '</strong> var på lager igen. Nu er den klar!</p>'
		. '<p><a href="' . esc_url( $url ) . '">Se varen og læg den på din klik og hent liste</a></p>'
		. '<p>Venlige hilsner<br>Lov Gårdbutik<br>Østre Kærvej 4, Lov · 4700 Næstved</p>'
		. '<p style="color:#777;font-size:12px">Du får kun denne ene e-mail. Din e-mail er nu slettet fra vores liste.</p>';
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	foreach ( $list as $row ) {
		if ( ! empty( $row['email'] ) && is_email( $row['email'] ) ) {
			wp_mail( $row['email'], $subject, $body, $headers );
		}
	}
	delete_post_meta( $product_id, LOV_NOTIFY_META );
}
add_action( 'woocommerce_product_set_stock_status', function ( $product_id, $status ) {
	if ( 'instock' === $status ) {
		lov_notify_send( $product_id );
	}
}, 10, 2 );
add_action( 'woocommerce_variation_set_stock_status', function ( $product_id, $status ) {
	if ( 'instock' === $status ) {
		lov_notify_send( $product_id );
	}
}, 10, 2 );

/** Admin: vis hvor mange der venter på besked. */
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'lov-lager-besked', 'Venter på besked', function ( $post ) {
		$list = array_filter( (array) get_post_meta( $post->ID, LOV_NOTIFY_META, true ), 'is_array' );
		if ( ! $list ) {
			echo '<p>Ingen venter på besked om denne vare.</p>';
			return;
		}
		echo '<p><strong>' . count( $list ) . '</strong> ' . ( 1 === count( $list ) ? 'person venter' : 'personer venter' ) . '. De får automatisk en e-mail, når varen sættes "På lager".</p><ul style="margin:0">';
		foreach ( $list as $row ) {
			echo '<li>' . esc_html( $row['email'] ) . ' <span style="color:#777">(' . esc_html( wp_date( 'j. M Y', (int) $row['tid'] ) ) . ')</span></li>';
		}
		echo '</ul>';
	}, 'product', 'side', 'default' );
} );

/** Styling */
add_action( 'wp_enqueue_scripts', function () {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}
	wp_add_inline_style( 'wp-block-library', '
		.lov-notify{margin:8px 0 24px;padding:20px 22px;border:1px solid var(--wp--preset--color--line,#E4D8C3);border-radius:16px;background:var(--wp--preset--color--base,#F7F1E6);scroll-margin-top:140px}
		.lov-notify-title{margin:0 0 12px;font-family:var(--wp--preset--font-family--source-serif);font-weight:700;font-size:1.2rem}
		.lov-notify-row{display:flex;gap:10px;flex-wrap:wrap}
		.lov-notify-row input[type=email]{flex:1 1 200px;min-height:46px;padding:10px 14px;border:1px solid var(--wp--preset--color--line,#E4D8C3);border-radius:999px;font:inherit;background:#fff}
		.lov-notify-row input[type=email]:focus-visible{outline:3px solid var(--wp--preset--color--hay,#E9D9A8);outline-offset:1px}
		.lov-notify-row button{min-height:46px;border:0;border-radius:999px;padding:0 22px;font-weight:700;cursor:pointer;background:var(--wp--preset--color--barn,#8E2F22);color:var(--wp--preset--color--paper,#FBF6EC)}
		.lov-notify-row button:hover{background:var(--wp--preset--color--barn-dark,#5E1D15)}
		.lov-notify-consent{display:flex;gap:10px;align-items:flex-start;margin:12px 0 0;font-size:.9rem;color:var(--wp--preset--color--muted,#4A4136)}
		.lov-notify-consent input{margin-top:4px;width:18px;height:18px;flex:0 0 auto}
		.lov-notify-msg{margin:0 0 10px;padding:10px 14px;border-radius:10px}
		.lov-notify-msg.is-ok{margin:0;background:#E7F0E2;color:#2C5520}
		.lov-notify-msg.is-error{background:#F7E1DD;color:#7A2418}
		.lov-notify-hp{position:absolute!important;left:-9999px!important;width:1px;height:1px;overflow:hidden}
	' );
} );
