<?php
/**
 * Title: Our story
 * Slug: kiln-crumb/story
 * Categories: kiln-crumb, about
 * Description: Two-column story section with the oven illustration.
 * Viewport width: 1280
 *
 * @package kiln-crumb
 */
?>
<!-- wp:group {"tagName":"section","anchor":"story","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<section id="story" class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"42%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:42%"><!-- wp:html -->
<div class="kc-illustration"><?php echo kc_svg( 'oven', '' ); // phpcs:ignore WordPress.Security.EscapeOutput -- trusted theme asset. ?></div>
<!-- /wp:html --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"58%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:58%"><!-- wp:heading -->
<h2 class="wp-block-heading">Slow dough, small batches, one oven.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Every loaf starts two days before you buy it. We mix with flour milled a few counties away, let the dough ferment slowly overnight in the cold, and bake it in a single brick-lined deck oven before sunrise.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>That schedule means we make a fixed amount each day, and when it is gone we close the case. If you want to be sure of a loaf, call ahead and we will set one aside with your name on it.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-default"} -->
<ul class="wp-block-list is-style-default"><!-- wp:list-item -->
<li>Regional, stone-milled flour</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>No commercial yeast in our sourdough</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Day-old bread donated to the Riverton food pantry</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
