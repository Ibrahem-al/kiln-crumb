<?php
/**
 * Title: FAQ
 * Slug: kiln-crumb/faq
 * Categories: kiln-crumb, text
 * Description: Native details/summary disclosures (keyboard and screen-reader friendly with zero JavaScript), mirrored as FAQPage JSON-LD.
 * Viewport width: 1280
 *
 * @package kiln-crumb
 */

$biz = kc_business();
?>
<!-- wp:group {"tagName":"section","anchor":"faq","align":"full","className":"kc-faq","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<section id="faq" class="wp-block-group alignfull kc-faq" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:heading -->
<h2 class="wp-block-heading">Questions we hear a lot</h2>
<!-- /wp:heading -->

<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group">
<?php foreach ( $biz['faq'] as $pair ) : ?>
<!-- wp:details -->
<details class="wp-block-details"><summary><?php echo esc_html( $pair['q'] ); ?></summary><!-- wp:paragraph {"textColor":"muted"} -->
<p class="has-muted-color has-text-color"><?php echo esc_html( $pair['a'] ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
<?php endforeach; ?>
</div>
<!-- /wp:group --></section>
<!-- /wp:group -->
