<?php
/**
 * Title: Menu board
 * Slug: kiln-crumb/menu-board
 * Categories: kiln-crumb
 * Description: The daily bake with prices, generated from inc/business.php so the page and structured data always match.
 * Viewport width: 1280
 *
 * @package kiln-crumb
 */

$biz = kc_business();
?>
<!-- wp:group {"tagName":"section","anchor":"menu","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"paper","layout":{"type":"constrained"}} -->
<section id="menu" class="wp-block-group alignfull has-paper-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
<div class="wp-block-group alignwide"><!-- wp:heading -->
<h2 class="wp-block-heading">Today’s bake</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted"} -->
<p class="has-muted-color has-text-color">Out of the oven by 7 a.m. Prices per loaf or piece.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":{"top":"0","left":"var:preset|spacing|60"},"margin":{"top":"var:preset|spacing|40"}}},"layout":{"type":"grid","minimumColumnWidth":"20rem"}} -->
<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--40)">
<?php foreach ( $biz['menu'] as $item ) : ?>
<!-- wp:group {"className":"kc-menu-item","style":{"spacing":{"blockGap":"0.35rem"}},"layout":{"type":"default"}} -->
<div class="wp-block-group kc-menu-item"><!-- wp:group {"style":{"spacing":{"blockGap":"1rem"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"top"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html( $item['name'] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"kc-price","style":{"typography":{"fontWeight":"600"}},"textColor":"crust"} -->
<p class="kc-price has-crust-color has-text-color" style="font-weight:600">$<?php echo esc_html( $item['price'] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"textColor":"muted"} -->
<p class="has-muted-color has-text-color"><?php echo esc_html( $item['note'] ); ?><?php if ( $item['tags'] ) : ?> <strong class="has-sage-color"><?php echo esc_html( implode( ' · ', $item['tags'] ) ); ?></strong><?php endif; ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
<?php endforeach; ?>
</div>
<!-- /wp:group --></section>
<!-- /wp:group -->
