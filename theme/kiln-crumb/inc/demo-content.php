<?php
/**
 * Demo content for the concept site. Never runs on its own: it is called by
 * scripts/setup-content.sh (WP-CLI) and by blueprint.json (WordPress
 * Playground), so both routes produce the identical site.
 *
 * @package kiln-crumb
 */

defined( 'ABSPATH' ) || exit;

/**
 * Create demo pages and settings. Safe to run more than once.
 */
function kc_create_demo_content() {
	update_option( 'blogname', 'Kiln & Crumb' );
	update_option( 'blogdescription', 'Naturally leavened bread, baked before sunrise.' );

	// Remove the default post and page so they are not indexed.
	foreach ( array( 'hello-world' => 'post', 'sample-page' => 'page' ) as $slug => $type ) {
		$old = get_page_by_path( $slug, OBJECT, $type );
		if ( $old ) {
			wp_delete_post( $old->ID, true );
		}
	}

	if ( ! get_page_by_path( 'wholesale' ) ) {
		wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'Wholesale',
				'post_name'    => 'wholesale',
				'post_excerpt' => 'Sourdough, rye and focaccia delivered to local cafés and restaurants Tuesday through Saturday. Standing orders, 24-hour lead time.',
				'post_content' => <<<'HTML'
<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">We bake for a small number of local kitchens and deliver Tuesday through Saturday before 9 a.m.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">How it works</h2>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true} -->
<ol class="wp-block-list"><!-- wp:list-item -->
<li>Email us your weekly quantities. Standing orders get priority.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Change or pause an order with 24 hours notice.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>We invoice monthly. Net 15.</li>
<!-- /wp:list-item --></ol>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What we supply</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Country sourdough, seeded rye, focaccia by the sheet and burger buns. Pastry is available for cafés by arrangement.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="mailto:hello@kilnandcrumb.example?subject=Wholesale%20inquiry">Request the wholesale list</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
HTML
				,
			)
		);
	}
}
