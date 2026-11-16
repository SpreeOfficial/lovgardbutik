<?php
/**
 * Title: Om gården – tidslinje
 * Slug: lovgardbutik/om-gaarden
 * Categories: lovgardbutik
 * Description: Historien om gården gennem generationer: billedbobler forbundet af en animeret, stiplet sti.
 * Inserter: true
 */

$img = function ( $file ) {
	$url = content_url( 'uploads/2026/11/' . $file );
	return array( 'id' => (int) attachment_url_to_postid( $url ), 'url' => esc_url( $url ) );
};

$chapters = array(
	array(
		'img'   => $img( 'tl-1-gaarden.jpg' ),
		'alt'   => 'Gammelt fotografi af et stuehus ved en grusvej',
		'year'  => '[ÅRSTAL]',
		'title' => 'Hvor det hele begyndte',
		'text'  => array(
			'Det startede med et lille stuehus i Lov, et par køer i stalden og røg fra skorstenen tidligt om morgenen. Gården var ikke stor, men jorden var god, og græsset groede tæt helt ned til vejen.',
			'Første generation byggede det fundament, vi stadig står på: Dyrene skal have det godt, og det, de giver os, skal behandles med respekt.',
		),
	),
	array(
		'img'   => $img( 'tl-2-haender.jpg' ),
		'alt'   => 'En gammel landmands hænder om en stok',
		'year'  => '[ÅRSTAL]',
		'title' => 'Hænder, der kendte jorden',
		'text'  => array(
			'Køerne blev malket i hånden, morgen og aften, året rundt. Mælkejungerne stod klar ved vejen, når mælkebilen kom forbi.',
			'Det var hårdt arbejde, men det lærte os at kende hvert dyr ved navn – og at et godt liv for dyrene smager igennem i mælken.',
		),
	),
	array(
		'img'   => $img( 'tl-3-traktor.jpg' ),
		'alt'   => 'Gammel traktor på en høstet mark',
		'year'  => '[ÅRSTAL]',
		'title' => 'Den første traktor',
		'text'  => array(
			'Med næste generation kom traktoren til gården, og hesten fik lov at gå på pension på marken bag laden.',
			'Meget blev nemmere, men én ting blev aldrig ændret: Køerne skulle stadig ud på græs, så snart foråret kom.',
		),
	),
	array(
		'img'   => $img( 'tl-4-hoens.jpg' ),
		'alt'   => 'Høns i græsset foran stuehuset i aftenlys',
		'year'  => '[ÅRSTAL]',
		'title' => 'Hønsene flytter ind',
		'text'  => array(
			'En dag kom de første høns til gården. De fik lov at gå frit rundt om stuehuset og i græsset, og snart var der flere æg, end familien kunne spise.',
			'Æggene kom i en kurv på et lille bord ved vejen – med en pengekasse ved siden af. Det var gårdbutikkens allerførste begyndelse.',
		),
	),
	array(
		'img'   => $img( 'tl-5-far-og-soen.jpg' ),
		'alt'   => 'Far og søn ser solen gå ned over marken',
		'year'  => '[ÅRSTAL]',
		'title' => 'Fra generation til generation',
		'text'  => array(
			'Gården gik videre fra forældre til børn. Mange steder blev landbrugene større og større, men vi valgte en anden vej.',
			'Vi holdt fast i det, vi tror på: færre dyr, mere plads, græs under klovene og god tid til det hele.',
		),
	),
	array(
		'img'   => $img( 'lov-koer.jpg' ),
		'alt'   => 'Køer græsser på en mark',
		'year'  => 'I dag',
		'title' => 'Lov Gårdbutik',
		'text'  => array(
			'Det lille bord ved vejen er blevet til en rigtig gårdbutik i den hvide længe med det røde gavlbræt. Her finder du mælk, kød og æg fra vores egne dyr, som går på græs.',
			'Historien fortsætter – og nu er du en del af den. Velkommen på gården.',
		),
	),
);
$shop = esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) );
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"96px","bottom":"64px","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"blockGap":"16px"}},"layout":{"type":"constrained","contentSize":"720px"}} -->
<div class="wp-block-group alignfull" style="padding-top:96px;padding-right:var(--wp--preset--spacing--50);padding-bottom:64px;padding-left:var(--wp--preset--spacing--50)"><!-- wp:paragraph {"align":"center","className":"lov-eyebrow","style":{"typography":{"fontWeight":"700","fontSize":"0.875rem"}},"textColor":"barn"} -->
<p class="has-text-align-center lov-eyebrow has-barn-color has-text-color" style="font-size:0.875rem;font-weight:700">Om gården</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","level":1,"fontSize":"xx-large"} -->
<h1 class="wp-block-heading has-text-align-center has-xx-large-font-size">En gård, mange generationer</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"muted","fontSize":"large"} -->
<p class="has-text-align-center has-muted-color has-text-color has-large-font-size">Følg stien ned gennem tiden – fra det første stuehus til gårdbutikken i dag.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","className":"lov-timeline","style":{"spacing":{"padding":{"top":"48px","bottom":"96px","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"blockGap":"120px"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide lov-timeline" style="padding-top:48px;padding-right:var(--wp--preset--spacing--50);padding-bottom:96px;padding-left:var(--wp--preset--spacing--50)"><?php
foreach ( $chapters as $i => $c ) :
	$right = $i % 2 ? ' is-right' : '';
	?><!-- wp:group {"className":"lov-tl-item<?php echo $right; ?>","style":{"spacing":{"blockGap":"56px"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center","verticalAlignment":"center"}} -->
<div class="wp-block-group lov-tl-item<?php echo $right; ?>"><!-- wp:image {"id":<?php echo $c['img']['id']; ?>,"sizeSlug":"medium_large","linkDestination":"none","className":"lov-tl-bubble"} -->
<figure class="wp-block-image size-medium_large lov-tl-bubble"><img src="<?php echo $c['img']['url']; ?>" alt="<?php echo esc_attr( $c['alt'] ); ?>" class="wp-image-<?php echo $c['img']['id']; ?>"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"lov-tl-text","style":{"spacing":{"blockGap":"12px"}},"layout":{"type":"default"}} -->
<div class="wp-block-group lov-tl-text"><!-- wp:paragraph {"className":"lov-tl-year"} -->
<p class="lov-tl-year"><?php echo esc_html( $c['year'] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size"><?php echo esc_html( $c['title'] ); ?></h2>
<!-- /wp:heading --><?php foreach ( $c['text'] as $p ) : ?>

<!-- wp:paragraph -->
<p><?php echo esc_html( $p ); ?></p>
<!-- /wp:paragraph --><?php endforeach; ?></div>
<!-- /wp:group --></div>
<!-- /wp:group --><?php endforeach; ?></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"96px","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"blockGap":"20px"},"elements":{"heading":{"color":{"text":"var:preset|color|paper"}}}},"backgroundColor":"grass","textColor":"oat","layout":{"type":"constrained","contentSize":"680px"}} -->
<div class="wp-block-group alignfull has-oat-color has-grass-background-color has-text-color has-background" style="padding-top:80px;padding-right:var(--wp--preset--spacing--50);padding-bottom:96px;padding-left:var(--wp--preset--spacing--50)"><!-- wp:heading {"textAlign":"center","fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size">Smag historien</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size">Mælk, kød og æg fra dyr, der går på græs – ligesom de altid har gjort.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo $shop; ?>">Se produkterne</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
