<?php
/**
 * Plugin Name: Lov Gårdbutik – Click & Collect
 * Description: Gør WooCommerce til en ren click & collect-bestilling uden login: kurven er en oversigt, kassen er en kort formular med ønsket afhentningsdato og -tidspunkt, og bestillingen sendes som e-mail til butikken og kunden.
 * Version: 1.0.0
 * Requires Plugins: woocommerce
 * Requires PHP: 8.1
 * Author: Lov Gårdbutik
 * Text Domain: lov-click-collect
 */

defined( 'ABSPATH' ) || exit;

/* -----------------------------------------------------------------
 * Indstillinger – ret her, når åbningstiderne ligger fast.
 * ----------------------------------------------------------------- */
const LOV_CC_FIRST_DAY_OFFSET = 1;     // 1 = tidligst i morgen, 0 = samme dag
const LOV_CC_DAYS_AHEAD       = 30;    // hvor langt frem man kan vælge
const LOV_CC_OPEN             = '08:00';
const LOV_CC_CLOSE            = '18:00';
const LOV_CC_SLOT_MINUTES     = 30;
const LOV_CC_CLOSED_WEEKDAYS  = array(); // fx array( 7 ) = lukket søndag (1 = mandag … 7 = søndag)

/** Tilladte datoer (Y-m-d) i butikkens tidszone. */
function lov_cc_allowed_dates(): array {
	$tz    = wp_timezone();
	$day   = new DateTimeImmutable( 'today', $tz );
	$dates = array();
	for ( $i = LOV_CC_FIRST_DAY_OFFSET; $i <= LOV_CC_DAYS_AHEAD; $i++ ) {
		$d = $day->modify( "+$i day" );
		if ( ! in_array( (int) $d->format( 'N' ), LOV_CC_CLOSED_WEEKDAYS, true ) ) {
			$dates[] = $d->format( 'Y-m-d' );
		}
	}
	return $dates;
}

/** Tidsrum som "08:00–08:30". */
function lov_cc_slots(): array {
	$slots = array();
	$t     = strtotime( '2000-01-01 ' . LOV_CC_OPEN );
	$end   = strtotime( '2000-01-01 ' . LOV_CC_CLOSE );
	while ( $t + LOV_CC_SLOT_MINUTES * 60 <= $end ) {
		$from           = gmdate( 'H:i', $t );
		$to             = gmdate( 'H:i', $t + LOV_CC_SLOT_MINUTES * 60 );
		$slots[ $from ] = str_replace( ':', '.', $from ) . '–' . str_replace( ':', '.', $to );
		$t             += LOV_CC_SLOT_MINUTES * 60;
	}
	return $slots;
}

function lov_cc_format_date( string $ymd ): string {
	$ts = strtotime( $ymd . ' 12:00' );
	return $ts ? wp_date( 'l \d. j. F Y', $ts ) : $ymd;
}

/* -----------------------------------------------------------------
 * 1. Intet login og ingen kundekonti
 * ----------------------------------------------------------------- */
add_filter( 'pre_option_woocommerce_enable_guest_checkout', fn() => 'yes' );
add_filter( 'pre_option_woocommerce_enable_checkout_login_reminder', fn() => 'no' );
add_filter( 'pre_option_woocommerce_enable_signup_and_login_from_checkout', fn() => 'no' );
add_filter( 'pre_option_woocommerce_enable_myaccount_registration', fn() => 'no' );
add_filter( 'pre_option_woocommerce_enable_signup_from_checkout_for_subscriptions', fn() => 'no' );
add_filter( 'pre_option_woocommerce_enable_delayed_account_creation', fn() => 'no' );
add_filter( 'woocommerce_checkout_registration_enabled', '__return_false' );
add_filter( 'woocommerce_checkout_registration_required', '__return_false' );

// Fjern konto-ikonet, som WooCommerce selv sætter ind i sidehovedet.
add_filter( 'hooked_block_types', function ( $types ) {
	return array_values( array_diff( (array) $types, array( 'woocommerce/customer-account' ) ) );
}, 99 );
add_filter( 'render_block_woocommerce/customer-account', '__return_empty_string' );

// "Min konto" findes ikke for kunder – send dem til butikken.
add_action( 'template_redirect', function () {
	if ( function_exists( 'is_account_page' ) && is_account_page() && ! current_user_can( 'edit_posts' ) ) {
		wp_safe_redirect( wc_get_page_permalink( 'shop' ) );
		exit;
	}
} );

/* -----------------------------------------------------------------
 * 2. Ingen fragt – kun afhentning
 * ----------------------------------------------------------------- */
add_filter( 'woocommerce_cart_needs_shipping', '__return_false' );
add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );
add_filter( 'woocommerce_ship_to_different_address_checked', '__return_false' );
add_filter( 'woocommerce_coupons_enabled', '__return_false' );

/* -----------------------------------------------------------------
 * 3. Kassen = kort formular
 * ----------------------------------------------------------------- */
add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
	$keep = array(
		'billing_first_name' => array( 'label' => 'Fornavn', 'priority' => 10, 'class' => array( 'form-row-first' ) ),
		'billing_last_name'  => array( 'label' => 'Efternavn', 'priority' => 20, 'class' => array( 'form-row-last' ) ),
		'billing_phone'      => array( 'label' => 'Telefon', 'priority' => 30, 'required' => true, 'class' => array( 'form-row-first' ) ),
		'billing_email'      => array( 'label' => 'E-mail', 'priority' => 40, 'class' => array( 'form-row-last' ) ),
	);
	$billing = array();
	foreach ( $keep as $key => $over ) {
		if ( isset( $fields['billing'][ $key ] ) ) {
			$billing[ $key ] = array_merge( $fields['billing'][ $key ], $over );
		}
	}
	$fields['billing']  = $billing;
	$fields['shipping'] = array();
	$fields['account']  = array();
	if ( isset( $fields['order']['order_comments'] ) ) {
		$fields['order']['order_comments']['label']       = 'Bemærkninger';
		$fields['order']['order_comments']['placeholder'] = 'Fx særlige ønsker til udskæring eller antal';
	}
	return $fields;
} );

// Dato og tidspunkt – vises øverst i formularen.
add_action( 'woocommerce_checkout_before_customer_details', function () {
	$dates   = lov_cc_allowed_dates();
	$slots   = lov_cc_slots();
	$picked  = isset( $_POST['lov_pickup_date'] ) ? sanitize_text_field( wp_unslash( $_POST['lov_pickup_date'] ) ) : '';
	$pickedT = isset( $_POST['lov_pickup_time'] ) ? sanitize_text_field( wp_unslash( $_POST['lov_pickup_time'] ) ) : '';
	?>
	<fieldset class="lov-cc-pickup">
		<legend>Hvornår vil du hente?</legend>
		<p class="lov-cc-help">Vælg den dag og det tidspunkt, du gerne vil hente din bestilling på gården, Østre Kærvej 4, Lov.</p>
		<p class="form-row form-row-first validate-required" id="lov_pickup_date_field">
			<label for="lov_pickup_date">Afhentningsdato&nbsp;<abbr class="required" title="påkrævet">*</abbr></label>
			<span class="woocommerce-input-wrapper">
				<input type="date" class="input-text" name="lov_pickup_date" id="lov_pickup_date" required
					min="<?php echo esc_attr( reset( $dates ) ); ?>" max="<?php echo esc_attr( end( $dates ) ); ?>"
					value="<?php echo esc_attr( $picked ); ?>">
			</span>
		</p>
		<p class="form-row form-row-last validate-required" id="lov_pickup_time_field">
			<label for="lov_pickup_time">Tidspunkt&nbsp;<abbr class="required" title="påkrævet">*</abbr></label>
			<span class="woocommerce-input-wrapper">
				<select name="lov_pickup_time" id="lov_pickup_time" required>
					<option value="">Vælg tidspunkt</option>
					<?php foreach ( $slots as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $pickedT, $value ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</span>
		</p>
		<div class="clear"></div>
	</fieldset>
	<?php
} );

add_action( 'woocommerce_after_checkout_validation', function ( $data, $errors ) {
	$date = isset( $_POST['lov_pickup_date'] ) ? sanitize_text_field( wp_unslash( $_POST['lov_pickup_date'] ) ) : '';
	$time = isset( $_POST['lov_pickup_time'] ) ? sanitize_text_field( wp_unslash( $_POST['lov_pickup_time'] ) ) : '';
	if ( '' === $date ) {
		$errors->add( 'lov_pickup_date', '<strong>Afhentningsdato</strong> skal udfyldes.' );
	} elseif ( ! in_array( $date, lov_cc_allowed_dates(), true ) ) {
		$errors->add( 'lov_pickup_date', '<strong>Afhentningsdato</strong> kan ikke vælges. Vælg en dato fra ' . esc_html( lov_cc_format_date( lov_cc_allowed_dates()[0] ) ) . ' og ' . LOV_CC_DAYS_AHEAD . ' dage frem.' );
	}
	if ( '' === $time || ! array_key_exists( $time, lov_cc_slots() ) ) {
		$errors->add( 'lov_pickup_time', '<strong>Tidspunkt</strong> skal vælges.' );
	}
}, 10, 2 );

add_action( 'woocommerce_checkout_create_order', function ( $order ) {
	$date = sanitize_text_field( wp_unslash( $_POST['lov_pickup_date'] ?? '' ) );
	$time = sanitize_text_field( wp_unslash( $_POST['lov_pickup_time'] ?? '' ) );
	$order->update_meta_data( '_lov_pickup_date', $date );
	$order->update_meta_data( '_lov_pickup_time', $time );
} );

function lov_cc_pickup_text( $order ): string {
	$date = $order->get_meta( '_lov_pickup_date' );
	$time = $order->get_meta( '_lov_pickup_time' );
	if ( ! $date ) {
		return '';
	}
	$slots = lov_cc_slots();
	return lov_cc_format_date( $date ) . ( $time ? ', kl. ' . ( $slots[ $time ] ?? $time ) : '' );
}

/* -----------------------------------------------------------------
 * 4. Vis afhentning i e-mails, på kvitteringen og i admin
 * ----------------------------------------------------------------- */
add_filter( 'woocommerce_email_order_meta_fields', function ( $fields, $sent_to_admin, $order ) {
	$text = lov_cc_pickup_text( $order );
	if ( $text ) {
		$fields['lov_pickup'] = array( 'label' => 'Afhentning', 'value' => $text . ' – Østre Kærvej 4, Lov, 4700 Næstved' );
	}
	return $fields;
}, 10, 3 );

add_action( 'woocommerce_thankyou', function ( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( $order && ( $text = lov_cc_pickup_text( $order ) ) ) {
		echo '<section class="lov-cc-thanks"><h2>Afhentning</h2><p><strong>' . esc_html( $text ) . '</strong><br>Lov Gårdbutik, Østre Kærvej 4, Lov, 4700 Næstved</p><p>Du betaler, når du henter varerne. Du har også fået en kvittering på e-mail.</p></section>';
	}
}, 5 );

add_action( 'woocommerce_admin_order_data_after_billing_address', function ( $order ) {
	if ( $text = lov_cc_pickup_text( $order ) ) {
		echo '<p><strong>Ønsket afhentning:</strong><br>' . esc_html( $text ) . '</p>';
	}
} );

// Kolonne i ordreoversigten (både HPOS og klassisk visning).
foreach ( array( 'manage_woocommerce_page_wc-orders_columns', 'manage_edit-shop_order_columns' ) as $hook ) {
	add_filter( $hook, function ( $cols ) {
		$new = array();
		foreach ( $cols as $k => $v ) {
			$new[ $k ] = $v;
			if ( 'order_date' === $k ) {
				$new['lov_pickup'] = 'Afhentning';
			}
		}
		return $new;
	} );
}
add_action( 'manage_woocommerce_page_wc-orders_custom_column', function ( $col, $order ) {
	if ( 'lov_pickup' === $col ) {
		echo esc_html( lov_cc_pickup_text( $order ) );
	}
}, 10, 2 );
add_action( 'manage_shop_order_posts_custom_column', function ( $col, $post_id ) {
	if ( 'lov_pickup' === $col && ( $o = wc_get_order( $post_id ) ) ) {
		echo esc_html( lov_cc_pickup_text( $o ) );
	}
}, 10, 2 );

/* -----------------------------------------------------------------
 * 5. Tekster: kurv = oversigt, kasse = bestilling
 * ----------------------------------------------------------------- */
add_filter( 'woocommerce_order_button_text', fn() => 'Send bestilling' );
add_filter( 'woocommerce_get_privacy_policy_text', function ( $text, $type ) {
	return 'checkout' === $type
		? 'Vi bruger dine oplysninger til at behandle din bestilling og kontakte dig om afhentningen. Læs mere i vores [privacy_policy].'
		: $text;
}, 10, 2 );
add_filter( 'gettext', function ( $translated, $text, $domain ) {
	if ( 'woocommerce' !== $domain ) {
		return $translated;
	}
	$map = array(
		'Proceed to checkout' => 'Gå til bestilling',
		'Cart totals'         => 'Din bestilling',
		'Billing details'     => 'Dine oplysninger',
		'Your order'          => 'Det har du bestilt',
		'Additional information' => 'Bemærkninger',
		'Place order'         => 'Send bestilling',
		'Your cart is currently empty.' => 'Du har ikke lagt noget i kurven endnu.',
		'Return to shop'      => 'Se varerne',
		'Thank you. Your order has been received.' => 'Tak! Vi har modtaget din bestilling.',
		'%s in cart'          => 'Tilføj til liste', // antallet vises i stedet i cirklen ved siden af knappen
		'Added to cart'       => 'Tilføjet til listen',
	);
	return $map[ $text ] ?? $translated;
}, 20, 3 );

/* -----------------------------------------------------------------
 * Produktkort: "Tilføj til liste" + cirkel med antal på listen
 * ----------------------------------------------------------------- */
add_filter( 'woocommerce_product_add_to_cart_text', function ( $text, $product ) {
	return $product->is_purchasable() && $product->is_in_stock() ? 'Tilføj til liste' : $text;
}, 10, 2 );

function lov_cc_qty_in_list( int $product_id ): int {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return 0;
	}
	$qty = 0;
	foreach ( WC()->cart->get_cart() as $item ) {
		if ( (int) $item['product_id'] === $product_id ) {
			$qty += (int) $item['quantity'];
		}
	}
	return $qty;
}

/*
 * Tæller på produktkortet:
 *   0 på listen:  [Tilføj til liste] (0)
 *   1+ på listen:      [ − ] (2) [ + ]
 */
add_filter( 'woocommerce_loop_add_to_cart_link', function ( $html, $product ) {
	if ( ! $product->is_purchasable() || ! $product->is_in_stock() || ! $product->is_type( 'simple' ) ) {
		return $html;
	}
	$id      = $product->get_id();
	$qty     = lov_cc_qty_in_list( $id );
	$name    = esc_attr( $product->get_name() );
	$stepper = sprintf(
		'<div class="lov-stepper" data-product-id="%1$d">'
			. '<button type="button" class="lov-step-btn lov-minus" aria-label="Tag én %2$s fra listen">−</button>'
			. '<span class="lov-qty%3$s" role="status" aria-live="polite"><span class="screen-reader-text">Antal %2$s på listen: </span><span class="lov-qty-n">%4$d</span></span>'
			. '<button type="button" class="lov-step-btn lov-plus" aria-label="Læg én %2$s mere på listen">+</button>'
		. '</div>',
		$id,
		$name,
		$qty ? ' has-items' : '',
		$qty
	);
	// Markér kassen, når varen er på listen (så knappen skjules og tælleren vises).
	$html = preg_replace( '/class="([^"]*wc-block-components-product-button[^"]*)"/', 'class="$1 lov-qty-box' . ( $qty ? ' lov-in-list' : '' ) . '"', $html, 1 );
	$pos  = strrpos( $html, '</div>' );
	return false === $pos ? $html . $stepper : substr_replace( $html, $stepper . '</div>', $pos, 6 );
}, 20, 2 );

/*
 * Samme tæller på selve produktsiden: WooCommerce' formular (antalsfelt + knap)
 * skiftes ud med "Tilføj til liste" / [−] (antal) [+]. Formularen ligger stadig
 * i <noscript>, så man kan bestille uden JavaScript.
 */
add_filter( 'render_block_woocommerce/add-to-cart-form', function ( $html ) {
	global $product;
	if ( ! $product instanceof WC_Product || ! $product->is_type( 'simple' ) || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return $html;
	}
	$id   = $product->get_id();
	$qty  = lov_cc_qty_in_list( $id );
	$name = esc_attr( $product->get_name() );
	return sprintf(
		'<div class="lov-qty-box lov-single%1$s">'
			. '<button type="button" class="lov-add wp-element-button" data-product-id="%2$d">Tilføj til liste</button>'
			. '<div class="lov-stepper" data-product-id="%2$d">'
				. '<button type="button" class="lov-step-btn lov-minus" aria-label="Tag én %3$s fra listen">−</button>'
				. '<span class="lov-qty%4$s" role="status" aria-live="polite"><span class="screen-reader-text">Antal %3$s på listen: </span><span class="lov-qty-n">%5$d</span></span>'
				. '<button type="button" class="lov-step-btn lov-plus" aria-label="Læg én %3$s mere på listen">+</button>'
			. '</div>'
		. '</div><noscript>%6$s</noscript>',
		$qty ? ' lov-in-list' : '',
		$id,
		$name,
		$qty ? ' has-items' : '',
		$qty,
		$html
	);
} );

// Ingen faner (Beskrivelse / Anmeldelser) på produktsiden – vis beskrivelsen direkte.
add_filter( 'render_block_woocommerce/product-details', function ( $html ) {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return $html;
	}
	$desc = $product->get_description();
	if ( '' === trim( wp_strip_all_tags( $desc ) ) ) {
		return '';
	}
	return '<section class="lov-product-desc alignwide"><h2>Om ' . esc_html( $product->get_name() ) . '</h2>'
		. wpautop( wp_kses_post( $desc ) ) . '</section>';
} );

// Danske ord i produktsidens metadata.
add_filter( 'render_block_core/post-terms', function ( $html ) {
	return str_replace( array( '>Category: <', '>Categories: <', '>Tags: <' ), array( '>Kategori: <', '>Kategorier: <', '>Mærker: <' ), $html );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_register_script( 'lov-cc-qty', plugins_url( 'qty.js', __FILE__ ), array(), filemtime( __DIR__ . '/qty.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_add_inline_script(
		'lov-cc-qty',
		'window.lovCc=' . wp_json_encode(
			array(
				'api'   => rest_url( 'wc/store/v1/cart' ),
				'nonce' => wp_create_nonce( 'wc_store_api' ),
			)
		) . ';',
		'before'
	);
	wp_enqueue_script( 'lov-cc-qty' );
	wp_add_inline_style( 'wp-block-library', '
		.lov-qty-box{display:flex !important;flex-direction:row !important;align-items:center;justify-content:flex-start;gap:10px}
		.lov-qty-box .added_to_cart{display:none !important}
		.lov-stepper{display:inline-flex;align-items:center;gap:8px}
		.lov-qty{flex:0 0 auto;width:44px;height:44px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;border:2px solid var(--wp--preset--color--line,#E4D8C3);color:var(--wp--preset--color--muted,#4A4136);background:var(--wp--preset--color--base,#F7F1E6);transition:background .25s,color .25s,border-color .25s,transform .25s}
		.lov-qty.has-items{background:var(--wp--preset--color--barn,#8E2F22);border-color:var(--wp--preset--color--barn,#8E2F22);color:var(--wp--preset--color--paper,#FBF6EC)}
		.lov-qty.bump{transform:scale(1.18)}
		.lov-step-btn{display:none;width:44px;height:44px;border-radius:50%;border:2px solid var(--wp--preset--color--barn,#8E2F22);background:var(--wp--preset--color--paper,#FBF6EC);color:var(--wp--preset--color--barn,#8E2F22);font-size:1.5rem;font-weight:700;line-height:1;cursor:pointer;padding:0;align-items:center;justify-content:center;transition:background .2s,color .2s}
		.lov-step-btn:hover,.lov-step-btn:focus-visible{background:var(--wp--preset--color--barn,#8E2F22);color:var(--wp--preset--color--paper,#FBF6EC)}
		.lov-step-btn:focus-visible{outline:3px solid var(--wp--preset--color--hay,#E9D9A8);outline-offset:2px}
		.lov-qty-box.lov-in-list > :not(.lov-stepper){display:none !important}
		.lov-qty-box.lov-in-list .lov-step-btn{display:inline-flex}
		.lov-qty-box.is-busy .lov-stepper,.lov-qty-box.is-busy .lov-add{opacity:.6;pointer-events:none}
		.lov-single{margin:8px 0 24px}
		.lov-product-desc{max-width:760px;margin:48px auto 0;padding-top:32px;border-top:1px solid var(--wp--preset--color--line,#E4D8C3)}
		.lov-product-desc h2{font-size:1.75rem;margin:0 0 16px}
		.lov-product-desc p{margin:0 0 1em;line-height:1.7}
		.lov-single .lov-add{border:0;border-radius:999px;padding:.85rem 1.6rem;min-height:44px;cursor:pointer;font-weight:700;background:var(--wp--preset--color--barn,#8E2F22);color:var(--wp--preset--color--paper,#FBF6EC)}
		.lov-single .lov-add:hover{background:var(--wp--preset--color--barn-dark,#5E1D15)}
		@media (prefers-reduced-motion:reduce){.lov-qty,.lov-step-btn{transition:none}.lov-qty.bump{transform:none}}
	' );
} );

add_action( 'woocommerce_before_cart', function () {
	echo '<div class="lov-cc-intro"><p><strong>Click &amp; collect:</strong> Her er din bestilling. Ret antal, og gå videre for at vælge, hvornår du vil hente den på gården. Du betaler ved afhentning.</p></div>';
} );

add_action( 'wp_enqueue_scripts', function () {
	if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() ) ) {
		wp_add_inline_style( 'woocommerce-general', '
			.lov-cc-pickup{border:1px solid var(--wp--preset--color--line,#E4D8C3);border-radius:12px;padding:20px 24px;margin:0 0 32px;background:var(--wp--preset--color--paper,#FBF6EC)}
			.lov-cc-pickup legend{font-family:var(--wp--preset--font-family--source-serif,serif);font-weight:700;font-size:1.35rem;padding:0 8px}
			.lov-cc-pickup input,.lov-cc-pickup select{width:100%;min-height:44px;padding:8px 12px;font-size:1rem;box-sizing:border-box}
			.lov-cc-intro,.lov-cc-thanks{background:var(--wp--preset--color--paper,#FBF6EC);border:1px solid var(--wp--preset--color--line,#E4D8C3);border-radius:12px;padding:16px 20px;margin:0 0 24px}
			.woocommerce-shipping-totals,.woocommerce-shipping-calculator{display:none}
		' );
	}
} );

/* -----------------------------------------------------------------
 * 6. Lokalt (XAMPP) kan PHP ikke sende mails: gem dem i en logfil i stedet,
 *    så formularen kan testes. Gælder kun på localhost.
 * ----------------------------------------------------------------- */
add_filter( 'pre_wp_mail', function ( $null, $atts ) {
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	if ( ! in_array( $host, array( 'localhost', '127.0.0.1' ), true ) ) {
		return $null;
	}
	$dir = WP_CONTENT_DIR . '/lov-mail-log';
	wp_mkdir_p( $dir );
	file_put_contents( $dir . '/.htaccess', "Require all denied\n" );
	$to   = is_array( $atts['to'] ) ? implode( ', ', $atts['to'] ) : $atts['to'];
	$name = gmdate( 'Ymd-His' ) . '-' . substr( md5( $to . $atts['subject'] . microtime() ), 0, 6 ) . '.html';
	file_put_contents( $dir . '/' . $name, '<!-- TO: ' . esc_html( $to ) . ' | SUBJECT: ' . esc_html( $atts['subject'] ) . " -->\n" . $atts['message'] );
	return true;
}, 10, 2 );
