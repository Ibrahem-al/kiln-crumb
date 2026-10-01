<?php
/**
 * Title: Footer
 * Slug: kiln-crumb/footer
 * Categories: kiln-crumb, footer
 * Block Types: core/template-part/footer
 * Inserter: no
 * Description: Site footer with address, hours summary and the concept-site notice.
 *
 * @package kiln-crumb
 */

$biz = kc_business();
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|50"}},"elements":{"link":{"color":{"text":"var:preset|color|cream"}}}},"backgroundColor":"ink","textColor":"cream","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-cream-color has-ink-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:paragraph {"style":{"typography":{"fontSize":"1.6rem","fontWeight":"600"}},"fontFamily":"display"} -->
<p class="has-display-font-family" style="font-size:1.6rem;font-weight:600"><?php echo esc_html( $biz['short_name'] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $biz['tagline'] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}}} -->
<p style="font-weight:600">Find us</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"kc-contact"} -->
<p class="kc-contact"><?php echo esc_html( $biz['address']['street'] ); ?><br><?php echo esc_html( $biz['address']['locality'] . ', ' . $biz['address']['region'] . ' ' . $biz['address']['postal'] ); ?><br><a href="tel:<?php echo esc_attr( $biz['phone'] ); ?>"><?php echo esc_html( $biz['phone_label'] ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}}} -->
<p style="font-weight:600">Hours</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php
$rows = array();
foreach ( $biz['hours'] as $row ) {
	$rows[] = esc_html( $row['label'] ) . ': ' . ( empty( $row['opens'] ) ? 'Closed' : esc_html( kc_format_time( $row['opens'] ) . '–' . kc_format_time( $row['closes'] ) ) );
}
echo implode( '<br>', $rows ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above.
?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:separator {"align":"wide","backgroundColor":"muted","className":"is-style-wide"} -->
<hr class="wp-block-separator alignwide has-text-color has-muted-color has-alpha-channel-opacity has-muted-background-color has-background is-style-wide"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"align":"wide","className":"kc-demo-note"} -->
<p class="alignwide kc-demo-note">Kiln &amp; Crumb is a fictional business. This is a concept site built by <a href="https://github.com/Ibrahem-al">Ibrahem Alkurdi</a> to demonstrate a WordPress block theme.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
