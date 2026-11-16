<?php
/**
 * Title: Lov Gårdbutik – forside
 * Slug: lovgardbutik/forside
 * Categories: lovgardbutik
 * Description: Hele forsiden: velkomst, historien, varer, en dag på gården, høns og besøg.
 * Inserter: true
 */

$lov_img = function ( $file ) {
	$id = attachment_url_to_postid( content_url( 'uploads/2026/11/' . $file ) );
	return array(
		'id'  => (int) $id,
		'url' => esc_url( content_url( 'uploads/2026/11/' . $file ) ),
	);
};
$koer     = $lov_img( 'lov-koer.jpg' );
$sol      = $lov_img( 'lov-solnedgang.jpg' );
$maelk    = $lov_img( 'lov-maelk.jpg' );
$hegn     = $lov_img( 'lov-hegn.jpg' );
$koed     = $lov_img( 'lov-koed.jpg' );
$aeg      = $lov_img( 'lov-aeg.jpg' );
$hoene    = $lov_img( 'lov-hoene.jpg' );
$cat      = function ( $slug ) {
	$t = get_term_by( 'slug', $slug, 'product_cat' );
	return esc_url( $t ? get_term_link( $t ) : home_url( '/butik/' ) );
};
$shop     = esc_url( function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/butik/' ) );
?>
<!-- wp:cover {"url":"<?php echo $koer['url']; ?>","id":<?php echo $koer['id']; ?>,"dimRatio":0,"isUserOverlayColor":true,"minHeight":640,"contentPosition":"center left","className":"lov-front-hero","align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull has-custom-content-position is-position-center-left lov-front-hero" style="min-height:640px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><img class="wp-block-cover__image-background wp-image-<?php echo $koer['id']; ?>" alt="" src="<?php echo $koer['url']; ?>" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:group {"className":"lov-front-hero-text","style":{"spacing":{"blockGap":"22px"}},"layout":{"type":"default"}} -->
<div class="wp-block-group lov-front-hero-text"><!-- wp:paragraph {"className":"lov-eyebrow","style":{"typography":{"fontWeight":"700","fontSize":"0.875rem"}},"textColor":"hay"} -->
<p class="lov-eyebrow has-hay-color has-text-color" style="font-size:0.875rem;font-weight:700">Lov Gårdbutik · Østre Kærvej 4, Lov</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"textColor":"paper","fontSize":"xx-large"} -->
<h1 class="wp-block-heading has-paper-color has-text-color has-xx-large-font-size">Mælk, kød og æg fra dyr, der går på græs.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"paper","fontSize":"large"} -->
<p class="has-paper-color has-text-color has-large-font-size">Fra marken bag laden til dit køkkenbord. Uden omveje, og med god tid til det hele.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo $shop; ?>">Se ugens varer</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline lov-btn-light"} -->
<div class="wp-block-button is-style-outline lov-btn-light"><a class="wp-block-button__link wp-element-button" href="#besog">Find vej til gården</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"lov-hero-note","style":{"border":{"radius":"14px"},"spacing":{"padding":{"top":"16px","bottom":"16px","left":"22px","right":"22px"},"blockGap":"2px"}},"backgroundColor":"paper","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group lov-hero-note has-paper-background-color has-background" style="border-radius:14px;padding-top:16px;padding-right:22px;padding-bottom:16px;padding-left:22px"><!-- wp:paragraph {"style":{"typography":{"fontWeight":"700","fontSize":"1.25rem"}},"fontFamily":"source-serif"} -->
<p class="has-source-serif-font-family" style="font-size:1.25rem;font-weight:700">Ude fra forår til frost</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"textColor":"muted","fontSize":"small"} -->
<p class="has-muted-color has-text-color has-small-font-size">Køerne går på græs, så længe vejret tillader det.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->

<!-- wp:group {"anchor":"historien","align":"full","style":{"spacing":{"padding":{"top":"104px","bottom":"104px","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"top":{"color":"var:preset|color|line","width":"1px"},"bottom":{"color":"var:preset|color|line","width":"1px"}}},"backgroundColor":"paper","layout":{"type":"constrained"}} -->
<div id="historien" class="wp-block-group alignfull has-paper-background-color has-background" style="border-top-color:var(--wp--preset--color--line);border-top-width:1px;border-bottom-color:var(--wp--preset--color--line);border-bottom-width:1px;padding-top:104px;padding-right:var(--wp--preset--spacing--50);padding-bottom:104px;padding-left:var(--wp--preset--spacing--50)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"64px"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"id":<?php echo $hegn['id']; ?>,"aspectRatio":"4/5","scale":"cover","sizeSlug":"large","linkDestination":"none","style":{"border":{"radius":"16px"}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="<?php echo $hegn['url']; ?>" alt="Rød lade på en bakke med høballer i solnedgang" class="wp-image-<?php echo $hegn['id']; ?>" style="border-radius:16px;aspect-ratio:4/5;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","style":{"spacing":{"blockGap":"22px"}}} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"className":"lov-eyebrow","style":{"typography":{"fontWeight":"700","fontSize":"0.875rem"}},"textColor":"barn"} -->
<p class="lov-eyebrow has-barn-color has-text-color" style="font-size:0.875rem;font-weight:700">Historien om Lov</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size">En lille gård, hvor tiden går lidt langsommere</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Hver morgen begynder på samme måde i Lov. Duggen ligger stadig i græsset, når staldlågen går op, og køerne går roligt ud på marken, som om de har gjort det hele livet. Det har de også. Kalvene følger med deres mødre, og hønsene er for længst på vej rundt om laden for at finde dagens første orm.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Vi tror på, at god mad kommer af tålmodighed. Dyr, der får lov at gå ude, æde græs og leve i deres eget tempo, giver mælk med smag af sommer, kød med karakter og æg med blommer så gule som smørblomster.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Den lille hvide gårdbutik med det røde gavlbræt er vores måde at dele det med jer på. Her står køleskabet med dagens mælk, frysere med kød fra egne dyr og kurve med æg, der blev lagt i morges. Kig forbi, sig hej til dyrene over hegnet, og tag en bid af landet med hjem.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"italic","fontWeight":"500"}},"textColor":"barn","fontSize":"large","fontFamily":"source-serif"} -->
<p class="has-barn-color has-text-color has-source-serif-font-family has-large-font-size" style="font-style:italic;font-weight:500">Velkommen på gården. [JERES NAVNE]</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"anchor":"varer","align":"full","style":{"spacing":{"padding":{"top":"104px","bottom":"104px","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"blockGap":"48px"}},"layout":{"type":"constrained"}} -->
<div id="varer" class="wp-block-group alignfull" style="padding-top:104px;padding-right:var(--wp--preset--spacing--50);padding-bottom:104px;padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"12px"}},"layout":{"type":"constrained","contentSize":"640px","justifyContent":"left"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"className":"lov-eyebrow","style":{"typography":{"fontWeight":"700","fontSize":"0.875rem"}},"textColor":"barn"} -->
<p class="lov-eyebrow has-barn-color has-text-color" style="font-size:0.875rem;font-weight:700">Fra gården</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size">Tre ting, vi gør rigtig godt</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted"} -->
<p class="has-muted-color has-text-color">Alt i butikken kommer fra vores egne dyr. Bestil på nettet og hent på gården, eller kom forbi og vælg selv.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"32px","top":"32px"}}}} -->
<div class="wp-block-columns alignwide"><?php
foreach (
	array(
		array( $maelk, 'Frisk mælk hældes fra en glaskande i et glas', 'Mælk', 'Frisk mælk fra køer, der har gået på græs. Fed, cremet og med den runde smag, man husker fra bedstemors køkken.', $cat( 'maelk' ), 'Se mælk' ),
		array( $koed, 'Stykke råt oksekød på et skærebræt', 'Kød', 'Kød fra dyr, der har haft et roligt liv på marken. Hængemørt, pakket i portioner og klar til fryseren. Spørg efter kødpakker.', $cat( 'koed' ), 'Se kød' ),
		array( $aeg, 'Friske brune og hvide æg i en bakke', 'Æg', 'Æg fra høns, der går frit rundt på gårdspladsen og i græsset. Samlet ind hver dag, så de er så friske, som de kan blive.', $cat( 'aeg' ), 'Se æg' ),
	) as $card
) :
	list( $img, $alt, $title, $text, $link, $btn ) = $card;
	?><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"lov-card","style":{"border":{"radius":"16px","color":"var:preset|color|line","width":"1px"},"spacing":{"blockGap":"0"}},"backgroundColor":"paper","layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group lov-card has-border-color has-paper-background-color has-background" style="border-color:var(--wp--preset--color--line);border-width:1px;border-radius:16px"><!-- wp:image {"id":<?php echo $img['id']; ?>,"aspectRatio":"3/2","scale":"cover","sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo $img['url']; ?>" alt="<?php echo esc_attr( $alt ); ?>" class="wp-image-<?php echo $img['id']; ?>" style="aspect-ratio:3/2;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"lov-card-body","style":{"spacing":{"padding":{"top":"26px","bottom":"30px","left":"26px","right":"26px"},"blockGap":"12px"},"layout":{"selfStretch":"fill"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group lov-card-body" style="padding-top:26px;padding-right:26px;padding-bottom:30px;padding-left:26px"><!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.75rem"}}} -->
<h3 class="wp-block-heading" style="font-size:1.75rem"><?php echo esc_html( $title ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted"} -->
<p class="has-muted-color has-text-color"><?php echo esc_html( $text ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"contrast","textColor":"paper","style":{"elements":{"link":{"color":{"text":"var:preset|color|paper"}}}}} -->
<div class="wp-block-button"><a class="wp-block-button__link has-paper-color has-contrast-background-color has-text-color has-background has-link-color wp-element-button" href="<?php echo $link; ?>"><?php echo esc_html( $btn ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:column --><?php endforeach; ?></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"104px","bottom":"104px","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"blockGap":"48px"},"elements":{"heading":{"color":{"text":"var:preset|color|paper"}}}},"backgroundColor":"grass","textColor":"oat","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-oat-color has-grass-background-color has-text-color has-background" style="padding-top:104px;padding-right:var(--wp--preset--spacing--50);padding-bottom:104px;padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"12px"}},"layout":{"type":"constrained","contentSize":"680px","justifyContent":"left"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"className":"lov-eyebrow","style":{"typography":{"fontWeight":"700","fontSize":"0.875rem"}},"textColor":"hay"} -->
<p class="lov-eyebrow has-hay-color has-text-color" style="font-size:0.875rem;font-weight:700">En dag i Lov</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size">Fra hanegal til aftensol</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"24px","top":"32px"}}}} -->
<div class="wp-block-columns alignwide"><?php
foreach (
	array(
		array( '05.30', 'Morgenmalkning', 'Køerne bliver malket i ro og mag, inden de går ud på marken.' ),
		array( '08.00', 'Æggene samles', 'Vi går en runde i hønsehuset, og dagens æg kommer i kurven.' ),
		array( '10.00', 'Butikken fyldes op', 'Køleskab og frysere bliver fyldt med friske varer til dagens kunder.' ),
		array( '20.00', 'Aftenstille', 'Hønsene går selv i hus, og køerne græsser videre i aftensolen.' ),
	) as $step
) :
	?><!-- wp:column {"className":"lov-time","style":{"spacing":{"blockGap":"8px"}}} -->
<div class="wp-block-column lov-time"><!-- wp:paragraph {"style":{"typography":{"fontSize":"2.1rem","fontWeight":"700","lineHeight":"1.1"}},"textColor":"hay","fontFamily":"source-serif"} -->
<p class="has-hay-color has-text-color has-source-serif-font-family" style="font-size:2.1rem;font-weight:700;line-height:1.1"><?php echo esc_html( $step[0] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontWeight":"700","fontSize":"1.25rem"}},"textColor":"paper"} -->
<p class="has-paper-color has-text-color" style="font-size:1.25rem;font-weight:700"><?php echo esc_html( $step[1] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $step[2] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --><?php endforeach; ?></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"104px","bottom":"104px","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:104px;padding-right:var(--wp--preset--spacing--50);padding-bottom:104px;padding-left:var(--wp--preset--spacing--50)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"64px"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","style":{"spacing":{"blockGap":"20px"}}} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"className":"lov-eyebrow","style":{"typography":{"fontWeight":"700","fontSize":"0.875rem"}},"textColor":"barn"} -->
<p class="lov-eyebrow has-barn-color has-text-color" style="font-size:0.875rem;font-weight:700">Gårdens høns</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size">Høns med udsigt og masser af plads</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Vores høns har det meste af gården for sig selv. De sprætter i græsset, tager støvbad i solen og holder øje med alt, hvad der sker. Det kan man smage på æggene.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Når du henter æg i gårdbutikken, er der god chance for, at en nysgerrig høne kommer forbi og hilser på.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"id":<?php echo $hoene['id']; ?>,"aspectRatio":"4/3","scale":"cover","sizeSlug":"large","linkDestination":"none","className":"lov-leaf"} -->
<figure class="wp-block-image size-large lov-leaf"><img src="<?php echo $hoene['url']; ?>" alt="Brune høns går rundt på gården" class="wp-image-<?php echo $hoene['id']; ?>" style="aspect-ratio:4/3;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"anchor":"besog","align":"full","style":{"spacing":{"padding":{"top":"104px","bottom":"104px","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"top":{"color":"var:preset|color|line","width":"1px"}}},"backgroundColor":"paper","layout":{"type":"constrained"}} -->
<div id="besog" class="wp-block-group alignfull has-paper-background-color has-background" style="border-top-color:var(--wp--preset--color--line);border-top-width:1px;padding-top:104px;padding-right:var(--wp--preset--spacing--50);padding-bottom:104px;padding-left:var(--wp--preset--spacing--50)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"64px","top":"40px"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"style":{"spacing":{"blockGap":"20px"}}} -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"lov-eyebrow","style":{"typography":{"fontWeight":"700","fontSize":"0.875rem"}},"textColor":"barn"} -->
<p class="lov-eyebrow has-barn-color has-text-color" style="font-size:0.875rem;font-weight:700">Besøg gårdbutikken</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size">Kom forbi og sig hej</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Gårdbutikken ligger i den hvide længe med det røde gavlbræt. Parkér på gårdspladsen, og betal med MobilePay. Vi tager desværre ikke imod kort eller kontanter.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><strong>Lov Gårdbutik</strong><br>Østre Kærvej 4, Lov<br>4700 Næstved</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="https://www.google.com/maps/dir/?api=1&amp;destination=%C3%98stre%20K%C3%A6rvej%204%2C%204700%20N%C3%A6stved">Find vej</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"lov-hours","style":{"border":{"radius":"16px","color":"var:preset|color|line","width":"1px"},"spacing":{"padding":{"top":"32px","bottom":"32px","left":"32px","right":"32px"},"blockGap":"14px"}},"backgroundColor":"base","layout":{"type":"default"}} -->
<div class="wp-block-group lov-hours has-border-color has-base-background-color has-background" style="border-color:var(--wp--preset--color--line);border-width:1px;border-radius:16px;padding-top:32px;padding-right:32px;padding-bottom:32px;padding-left:32px"><!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.75rem"}}} -->
<h3 class="wp-block-heading" style="font-size:1.75rem">Åbningstider</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><span>Mandag – fredag</span><strong>[TID]</strong></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><span>Lørdag – søndag</span><strong>[TID]</strong></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><span>Afhentning af webordrer</span><strong>[TID]</strong></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"textColor":"muted","fontSize":"small"} -->
<p class="has-muted-color has-text-color has-small-font-size">Butikken er selvbetjent. Er du i tvivl om noget, så ring eller skriv. Vi er som regel ude ved dyrene.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
