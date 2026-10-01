<?php
/**
 * Title: Wholesale call to action
 * Slug: kiln-crumb/wholesale-cta
 * Categories: kiln-crumb, call-to-action
 * Description: Full-width sage band inviting cafés and restaurants to order wholesale.
 * Viewport width: 1280
 *
 * @package kiln-crumb
 */

$biz = kc_business();
?>
<!-- wp:group {"tagName":"section","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}},"elements":{"link":{"color":{"text":"var:preset|color|cream"}}}},"backgroundColor":"sage","textColor":"cream","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull has-cream-color has-sage-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"style":{"spacing":{"blockGap":"0.5rem"}},"layout":{"type":"constrained","contentSize":"560px","justifyContent":"left"}} -->
<div class="wp-block-group"><!-- wp:heading -->
<h2 class="wp-block-heading">Bread for your café or restaurant</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We deliver sourdough, rye and focaccia to a handful of local kitchens, Tuesday through Saturday. Ask for the wholesale list.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"cream","textColor":"ink"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-ink-color has-cream-background-color has-text-color has-background wp-element-button" href="mailto:<?php echo esc_attr( $biz['email'] ); ?>?subject=Wholesale%20inquiry">Email about wholesale</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
