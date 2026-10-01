<?php
/**
 * Title: Visit us
 * Slug: kiln-crumb/visit
 * Categories: kiln-crumb, contact
 * Description: Address, phone, map link and an opening-hours table built from inc/business.php.
 * Viewport width: 1280
 *
 * @package kiln-crumb
 */

$biz = kc_business();
?>
<!-- wp:group {"tagName":"section","anchor":"visit","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"paper","layout":{"type":"constrained"}} -->
<section id="visit" class="wp-block-group alignfull has-paper-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2 class="wp-block-heading">Visit the bakery</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php echo esc_html( $biz['address']['street'] ); ?><br><?php echo esc_html( $biz['address']['locality'] . ', ' . $biz['address']['region'] . ' ' . $biz['address']['postal'] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"kc-contact"} -->
<p class="kc-contact"><a href="tel:<?php echo esc_attr( $biz['phone'] ); ?>"><?php echo esc_html( $biz['phone_label'] ); ?></a><br><a href="mailto:<?php echo esc_attr( $biz['email'] ); ?>"><?php echo esc_html( $biz['email'] ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $biz['map_url'] ); ?>">Get directions<span class="screen-reader-text"> (opens OpenStreetMap)</span></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Hours</h3>
<!-- /wp:heading -->

<!-- wp:table {"className":"kc-hours"} -->
<figure class="wp-block-table kc-hours"><table><tbody><?php foreach ( $biz['hours'] as $row ) : ?><tr><td><?php echo esc_html( $row['label'] ); ?></td><td><?php echo empty( $row['opens'] ) ? 'Closed' : esc_html( kc_format_time( $row['opens'] ) . ' – ' . kc_format_time( $row['closes'] ) ); ?></td></tr><?php endforeach; ?></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:paragraph {"textColor":"muted","fontSize":"small"} -->
<p class="has-muted-color has-text-color has-small-font-size">We close early if we sell out. Holiday hours are posted on the door and on this page.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
