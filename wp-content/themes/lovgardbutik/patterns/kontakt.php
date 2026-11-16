<?php
/**
 * Title: Kontakt – besøg gårdbutikken med kort
 * Slug: lovgardbutik/kontakt
 * Categories: lovgardbutik
 * Description: Adresse, knapper, Google-kort og åbningstider.
 * Inserter: true
 */
$maps_q = rawurlencode( 'Østre Kærvej 4, 4700 Næstved' );
?>
<!-- wp:group {"anchor":"besog","align":"full","style":{"spacing":{"padding":{"top":"96px","bottom":"104px","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"backgroundColor":"paper","layout":{"type":"constrained"}} -->
<div id="besog" class="wp-block-group alignfull has-paper-background-color has-background" style="padding-top:96px;padding-right:var(--wp--preset--spacing--50);padding-bottom:104px;padding-left:var(--wp--preset--spacing--50)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"64px","top":"40px"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"style":{"spacing":{"blockGap":"20px"}}} -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"lov-eyebrow","style":{"typography":{"fontWeight":"700","fontSize":"0.875rem"}},"textColor":"barn"} -->
<p class="lov-eyebrow has-barn-color has-text-color" style="font-size:0.875rem;font-weight:700">Besøg gårdbutikken</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"fontSize":"x-large"} -->
<h1 class="wp-block-heading has-x-large-font-size">Kom forbi og sig hej</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Gårdbutikken ligger i den hvide længe med det røde gavlbræt. Parkér på gårdspladsen, og betal med MobilePay. Vi tager desværre ikke imod kort eller kontanter.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">Lov Gårdbutik<br>Østre Kærvej 4, Lov<br>4700 Næstved</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="https://www.google.com/maps/dir/?api=1&amp;destination=<?php echo $maps_q; ?>">Find vej</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:html -->
<div class="lov-map"><iframe title="Kort: Lov Gårdbutik, Østre Kærvej 4, 4700 Næstved" src="https://maps.google.com/maps?q=<?php echo $maps_q; ?>&amp;z=14&amp;hl=da&amp;output=embed" width="600" height="360" style="border:0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe></div>
<!-- /wp:html --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"lov-hours","style":{"border":{"radius":"16px","color":"var:preset|color|line","width":"1px"},"spacing":{"padding":{"top":"32px","bottom":"32px","left":"32px","right":"32px"},"blockGap":"14px"}},"backgroundColor":"base","layout":{"type":"default"}} -->
<div class="wp-block-group lov-hours has-border-color has-base-background-color has-background" style="border-color:var(--wp--preset--color--line);border-width:1px;border-radius:16px;padding-top:32px;padding-right:32px;padding-bottom:32px;padding-left:32px"><!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"1.75rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.75rem">Åbningstider</h2>
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
