<?php
/**
 * Title: Dyrevelfærd – grøn bjælke med mærket
 * Slug: lovgardbutik/dyrevelfaerd
 * Categories: lovgardbutik
 * Description: Grøn bjælke med Bedre Dyrevelfærd-mærket i et hvidt felt og tekst om dyrevelfærd.
 * Inserter: true
 */
$logo = esc_url( get_theme_file_uri( 'assets/images/bedre-dyrevelfaerd.png' ) );
?>
<!-- wp:group {"align":"full","className":"lov-welfare","style":{"spacing":{"padding":{"top":"64px","bottom":"64px","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"margin":{"top":"72px"}},"elements":{"heading":{"color":{"text":"var:preset|color|paper"}}}},"backgroundColor":"grass","textColor":"oat","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull lov-welfare has-oat-color has-grass-background-color has-text-color has-background" style="margin-top:72px;padding-top:64px;padding-right:var(--wp--preset--spacing--50);padding-bottom:64px;padding-left:var(--wp--preset--spacing--50)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"56px","top":"32px"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"34%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:34%"><!-- wp:group {"className":"lov-welfare-box","style":{"border":{"radius":"20px"},"spacing":{"padding":{"top":"28px","bottom":"28px","left":"28px","right":"28px"}}},"backgroundColor":"paper","layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group lov-welfare-box has-paper-background-color has-background" style="border-radius:20px;padding-top:28px;padding-right:28px;padding-bottom:28px;padding-left:28px"><!-- wp:image {"width":"320px","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full is-resized"><img src="<?php echo $logo; ?>" alt="Bedre Dyrevelfærd – statens dyrevelfærdsmærke" width="480" height="323" style="width:320px" loading="lazy"/></figure>
<!-- /wp:image --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","style":{"spacing":{"blockGap":"16px"}}} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"className":"lov-eyebrow","style":{"typography":{"fontWeight":"700","fontSize":"0.875rem"}},"textColor":"hay"} -->
<p class="lov-eyebrow has-hay-color has-text-color" style="font-size:0.875rem;font-weight:700">Dyrevelfærd</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size">Vi går ind for dyr, der har det godt</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>For os starter god mad med glade dyr. Vores køer går på græs, så længe vejret tillader det, og hønsene har plads til at gå frit rundt på gården. Dyrene får tid til at vokse i deres eget tempo – uden hast og uden trængsel.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Bedre Dyrevelfærd er statens mærke for dyrevelfærd. Jo flere hjerter, jo bedre forhold har dyrene haft – fx mere plads, adgang til udeareal og mere tid hos moren.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
