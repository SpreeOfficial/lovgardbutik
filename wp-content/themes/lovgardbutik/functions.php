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
	return preg_replace( '#<svg[^>]*wc-block-mini-cart__icon[^>]*>.*?</svg>#s', $icon, $html, 1 );
} );

// Egen mønsterkategori, så sektionerne er nemme at finde i editoren.
add_action( 'init', function () {
	register_block_pattern_category( 'lovgardbutik', array( 'label' => 'Lov Gårdbutik' ) );
} );
