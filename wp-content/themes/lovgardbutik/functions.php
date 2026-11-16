<?php
/**
 * Lov Gårdbutik – child-tema til Twenty Twenty-Five.
 */

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'lovgardbutik',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' ) . '-' . filemtime( get_stylesheet_directory() . '/style.css' )
	);
} );

// Tidslinjens animation – kun på sider, der indeholder en tidslinje.
add_action( 'wp_enqueue_scripts', function () {
	if ( is_singular() && false !== strpos( (string) get_post_field( 'post_content', get_queried_object_id() ), 'lov-timeline' ) ) {
		wp_enqueue_script(
			'lovgardbutik-tidslinje',
			get_theme_file_uri( 'assets/js/tidslinje.js' ),
			array(),
			wp_get_theme()->get( 'Version' ) . '-' . filemtime( get_theme_file_path( 'assets/js/tidslinje.js' ) ),
			array( 'in_footer' => true, 'strategy' => 'defer' )
		);
	}
} );

// Bestillingsguide som pop-up på mobil og kategoriknapper som sidescroller – kun på Produkter- og kategorisiderne.
add_action( 'wp_enqueue_scripts', function () {
	if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_category() ) ) {
		wp_enqueue_script(
			'lovgardbutik-pills',
			get_theme_file_uri( 'assets/js/pills.js' ),
			array(),
			filemtime( get_theme_file_path( 'assets/js/pills.js' ) ),
			array( 'in_footer' => true, 'strategy' => 'defer' )
		);
		wp_enqueue_script(
			'lovgardbutik-guide',
			get_theme_file_uri( 'assets/js/guide.js' ),
			array(),
			filemtime( get_theme_file_path( 'assets/js/guide.js' ) ),
			array( 'in_footer' => true, 'strategy' => 'defer' )
		);
	}
} );

// Sidehovedet bliver mindre ved scroll – på alle sider.
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_script(
		'lovgardbutik-header',
		get_theme_file_uri( 'assets/js/header.js' ),
		array(),
		filemtime( get_theme_file_path( 'assets/js/header.js' ) ),
		array( 'in_footer' => true, 'strategy' => 'defer' )
	);
} );

// Samme styling i editoren, så siden ser ens ud mens man redigerer.
add_action( 'after_setup_theme', function () {
	add_editor_style( 'style.css' );
} );

/**
 * Hero på Produkter-siden: indsæt kategoriens billede (eller butikkens standardbillede)
 * i cover-blokken med klassen "lov-product-hero".
 */
add_filter( 'render_block_core/cover', function ( $html, $block ) {
	if ( false === strpos( $block['attrs']['className'] ?? '', 'lov-product-hero' ) ) {
		return $html;
	}
	$image_id = 0;
	if ( is_product_category() ) {
		$image_id = (int) get_term_meta( get_queried_object_id(), 'thumbnail_id', true );
	}
	if ( ! $image_id ) {
		$image_id = (int) get_theme_mod( 'lov_products_hero', 0 );
	}
	if ( ! $image_id ) {
		return $html;
	}
	$img = wp_get_attachment_image(
		$image_id,
		'full',
		false,
		array(
			'class'         => 'wp-block-cover__image-background',
			'alt'           => '',
			'data-object-fit' => 'cover',
			'loading'       => 'eager',
			'fetchpriority' => 'high',
			'sizes'         => '100vw',
		)
	);
	// Billedet skal ligge før overlay-laget.
	return preg_replace( '/(<div[^>]*class="[^"]*wp-block-cover[^"]*"[^>]*>)/', '$1' . $img, $html, 1 );
}, 10, 2 );

/**
 * Menulinks i temaets skabeloner peger på en side/kategori via ID (så de virker på alle domæner).
 * Udfyld adressen ud fra ID'et, når linket ikke har en URL.
 */
add_filter( 'render_block_data', function ( $block ) {
	if ( 'core/navigation-link' !== ( $block['blockName'] ?? '' ) || ! empty( $block['attrs']['url'] ) || empty( $block['attrs']['id'] ) ) {
		return $block;
	}
	$id  = (int) $block['attrs']['id'];
	$url = '';
	if ( 'taxonomy' === ( $block['attrs']['kind'] ?? '' ) ) {
		$link = get_term_link( $id, $block['attrs']['type'] ?? 'category' );
		$url  = is_wp_error( $link ) ? '' : $link;
	} else {
		$url = get_permalink( $id ) ?: '';
	}
	if ( $url ) {
		$block['attrs']['url'] = $url;
	}
	return $block;
} );

/**
 * Kurv-ikonet i sidehovedet: en "klik og hent"-kurv (flettet kurv med hank og pil op)
 * i stedet for WooCommerce' indkøbsvogn.
 */
add_filter( 'render_block_woocommerce/mini-cart', function ( $html ) {
	$icon = '<svg class="wc-block-mini-cart__icon lov-cc-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
		. '<path d="M9 13 C9 6.5 23 6.5 23 13"/>'                       // hank
		. '<path d="M4 13 H28 L25.5 27 H6.5 Z"/>'                       // kurv
		. '<path d="M10 17.5 L11 23.5 M16 17.5 V23.5 M22 17.5 L21 23.5"/>' // fletning
		. '<path d="M16 2.5 V8.5 M13.5 5 L16 2.5 L18.5 5"/>'            // pil: "hent"
		. '</svg>';
	$html = preg_replace( '#<svg[^>]*wc-block-mini-cart__icon[^>]*>.*?</svg>#s', $icon, $html, 1 );

	// På kurv- og bestillingssiden skjuler WooCommerce ikonet. Vis det i stedet som et link
	// til "Din bestilling" med antallet, så sidehovedet ser ens ud overalt.
	if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() ) ) {
		$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
		$badge = $count ? '<span class="wc-block-mini-cart__badge lov-cc-badge">' . (int) $count . '</span>' : '';
		$label = sprintf( 'Din klik og hent liste: %d %s', $count, 1 === $count ? 'vare' : 'varer' );
		$html  = str_replace( ' style="visibility:hidden" aria-hidden="true"', '', $html );
		$html  = preg_replace(
			'#<button class="wc-block-mini-cart__button"[^>]*>(.*?)</button>#s',
			'<a class="wc-block-mini-cart__button lov-cc-link" href="' . esc_url( wc_get_cart_url() ) . '" aria-label="' . esc_attr( $label ) . '"><span class="wc-block-mini-cart__quantity-badge">' . $icon . $badge . '</span></a>',
			$html,
			1
		);
	}
	return $html;
} );

/**
 * Produktsiden (split-layout):
 *  - "Tilbage" peger på produktets kategori (eller Produkter-siden)
 *  - Faktaboksen viser produktets synlige egenskaber (fx Kilopris, Generel vægt),
 *    prisen og om varen er på lager. Egenskaberne rettes under Produktdata → Egenskaber.
 */
add_filter( 'render_block_core/paragraph', function ( $html, $block ) {
	if ( false === strpos( $block['attrs']['className'] ?? '', 'lov-back' ) || ! is_product() ) {
		return $html;
	}
	$url   = wc_get_page_permalink( 'shop' );
	$terms = get_the_terms( get_the_ID(), 'product_cat' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$url = get_term_link( $terms[0] );
	}
	$arrow = '<svg aria-hidden="true" focusable="false" width="28" height="20" viewBox="0 0 28 20" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M26 10H3M10 2 2 10l8 8"/></svg>';
	return str_replace( array( 'href="#tilbage">', '>Tilbage<' ), array( 'href="' . esc_url( $url ) . '">' . $arrow, '><span>Tilbage</span><' ), $html );
}, 10, 2 );

add_filter( 'render_block_core/group', function ( $html, $block ) {
	if ( false === strpos( $block['attrs']['className'] ?? '', 'lov-pdp-facts' ) || ! is_product() ) {
		return $html;
	}
	$product = wc_get_product( get_the_ID() );
	if ( ! $product ) {
		return $html;
	}
	$facts = array();
	foreach ( $product->get_attributes() as $attr ) {
		if ( ! $attr->get_visible() ) {
			continue;
		}
		$value = $attr->is_taxonomy()
			? implode( ', ', wc_get_product_terms( $product->get_id(), $attr->get_name(), array( 'fields' => 'names' ) ) )
			: implode( ', ', $attr->get_options() );
		$facts[] = array( wc_attribute_label( $attr->get_name(), $product ), esc_html( $value ) );
	}
	$facts[] = array( 'Pris', $product->get_price_html() );
	$in      = $product->is_in_stock();
	$facts[] = array( 'Lager', '<span class="lov-stock ' . ( $in ? 'is-in' : 'is-out' ) . '">' . ( $in ? 'På lager' : 'Ikke på lager' ) . '</span>' );

	$out = '<dl class="wp-block-group lov-pdp-facts">';
	foreach ( $facts as $f ) {
		$out .= '<div><dt>' . esc_html( $f[0] ) . '</dt><dd>' . $f[1] . '</dd></div>';
	}
	return $out . '</dl>';
}, 10, 2 );

// Burger-ikon med tre streger (WordPress' standard har kun to).
add_filter( 'render_block_core/navigation', function ( $html, $block ) {
	if ( false === strpos( $block['attrs']['className'] ?? '', 'lov-header-nav' ) ) {
		return $html;
	}
	$burger = '<svg class="lov-burger" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M4 6.5h16M4 12h16M4 17.5h16"/></svg>';
	return preg_replace(
		'#(<button[^>]*wp-block-navigation__responsive-container-open[^>]*>)\s*<svg.*?</svg>#s',
		'$1' . $burger,
		$html,
		1
	);
}, 10, 2 );

/**
 * Produkter-siden har ingen kategoribeskrivelse – vis i stedet sidens uddrag
 * (Sider → Produkter → Uddrag) eller en standardtekst under overskriften i hero.
 */
add_post_type_support( 'page', 'excerpt' );
add_filter( 'render_block_core/term-description', function ( $html, $block ) {
	if ( '' !== trim( wp_strip_all_tags( $html ) ) || ! function_exists( 'is_shop' ) || ! is_shop() ) {
		return $html;
	}
	$text = trim( (string) get_post_field( 'post_excerpt', wc_get_page_id( 'shop' ) ) );
	if ( '' === $text ) {
		$text = 'Mælk, kød og æg fra vores egne dyr på gården i Lov.';
	}
	$classes = 'wp-block-term-description';
	if ( ! empty( $block['attrs']['textColor'] ) ) {
		$classes .= ' has-text-color has-' . sanitize_html_class( $block['attrs']['textColor'] ) . '-color';
	}
	if ( ! empty( $block['attrs']['fontSize'] ) ) {
		$classes .= ' has-' . sanitize_html_class( $block['attrs']['fontSize'] ) . '-font-size';
	}
	return '<div class="' . esc_attr( $classes ) . '"><p>' . esc_html( $text ) . '</p></div>';
}, 10, 2 );

/**
 * Lagerstatus på produktkortene: samme grønne/røde prik som på produktsiden,
 * lige under prisen.
 */
add_filter( 'render_block_woocommerce/product-price', function ( $html, $block, $instance ) {
	$id = $instance instanceof WP_Block ? (int) ( $instance->context['postId'] ?? 0 ) : 0;
	if ( ! $id || ( function_exists( 'is_product' ) && is_product() && get_queried_object_id() === $id ) ) {
		return $html;
	}
	$product = wc_get_product( $id );
	if ( ! $product ) {
		return $html;
	}
	$in = $product->is_in_stock();
	return $html . '<p class="lov-card-stock"><span class="lov-stock ' . ( $in ? 'is-in' : 'is-out' ) . '">' . ( $in ? 'På lager' : 'Ikke på lager' ) . '</span></p>';
}, 10, 3 );

// Egen mønsterkategori, så sektionerne er nemme at finde i editoren.
add_action( 'init', function () {
	register_block_pattern_category( 'lovgardbutik', array( 'label' => 'Lov Gårdbutik' ) );
} );

/**
 * Relative links i indholdet.
 * Links og billeder, der er gemt med fuld adresse (lokalt, lovgardbutik.dk eller one.com's
 * forhåndsadresse), skrives om til stier ud fra sidens aktuelle adresse – så de virker overalt.
 */
function lov_relative_urls( $html ) {
	if ( ! is_string( $html ) || '' === $html || false === strpos( $html, '//' ) ) {
		return $html;
	}
	static $from = null, $to = null;
	if ( null === $from ) {
		$path = rtrim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
		$from = array();
		foreach ( array( 'lovgardbutik.dk', 'www.lovgardbutik.dk', 'localhost/lovgardbutik', '0d5bd027.website.ca63ltfwk.service.one' ) as $host ) {
			foreach ( array( 'https://', 'http://', '//' ) as $scheme ) {
				$from[] = '="' . $scheme . $host . '/';
				$from[] = '="' . $scheme . $host . '"';
				$from[] = "url(" . $scheme . $host . '/';
			}
		}
		$to = array();
		foreach ( $from as $f ) {
			$to[] = str_starts_with( $f, 'url(' ) ? 'url(' . $path . '/' : ( str_ends_with( $f, '"' ) ? '="' . $path . '/"' : '="' . $path . '/' );
		}
	}
	return str_replace( $from, $to, $html );
}
add_filter( 'render_block', function ( $html, $block ) {
	return lov_relative_urls( $html );
}, 99, 2 );
add_filter( 'the_content', 'lov_relative_urls', 99 );
