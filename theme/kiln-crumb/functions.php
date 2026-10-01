<?php
/**
 * Kiln & Crumb theme bootstrap.
 *
 * @package kiln-crumb
 */

defined( 'ABSPATH' ) || exit;

require_once get_theme_file_path( 'inc/business.php' );
require_once get_theme_file_path( 'inc/seo.php' );

/**
 * Theme supports and editor styles.
 */
function kc_setup() {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
	remove_theme_support( 'core-block-patterns' ); // Keep the inserter focused on this theme's sections.
}
add_action( 'after_setup_theme', 'kc_setup' );

/**
 * Front-end stylesheet (small; almost everything is in theme.json).
 */
function kc_enqueue() {
	wp_enqueue_style( 'kiln-crumb', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'kc_enqueue' );

/**
 * Preload the three fonts used above the fold. The italic is in the hero h1;
 * without it Lighthouse measured CLS 0.218 on the home page (hero reflowed when
 * the italic arrived late). With it, CLS drops to ~0.
 */
function kc_preload_fonts() {
	foreach ( array( 'fraunces-latin-wght-normal.woff2', 'fraunces-latin-wght-italic.woff2', 'inter-latin-wght-normal.woff2' ) as $file ) {
		printf(
			"<link rel=\"preload\" href=\"%s\" as=\"font\" type=\"font/woff2\" crossorigin>\n",
			esc_url( get_theme_file_uri( 'assets/fonts/' . $file ) )
		);
	}
}
add_action( 'wp_head', 'kc_preload_fonts', 2 );

/**
 * Theme favicon, unless the client has set a Site Icon in the Customizer.
 */
function kc_favicon() {
	if ( has_site_icon() ) {
		return;
	}
	printf( "<link rel=\"icon\" href=\"%s\" type=\"image/svg+xml\">\n", esc_url( get_theme_file_uri( 'assets/img/favicon.svg' ) ) );
}
add_action( 'wp_head', 'kc_favicon', 3 );

/**
 * Remove front-end weight a bakery site never uses.
 */
function kc_trim_head() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'kc_trim_head' );

/**
 * Pattern category so the client finds this theme's sections first.
 */
function kc_pattern_categories() {
	register_block_pattern_category(
		'kiln-crumb',
		array(
			'label'       => __( 'Kiln & Crumb sections', 'kiln-crumb' ),
			'description' => __( 'Page sections built for this site.', 'kiln-crumb' ),
		)
	);
}
add_action( 'init', 'kc_pattern_categories' );

/**
 * Inline SVG illustration from assets/img, with an accessible name or hidden
 * when decorative. Inline (not <img>) so the art costs no extra request and
 * inherits theme colors.
 *
 * @param string $name  File name without extension.
 * @param string $label Accessible label; empty string marks it decorative.
 * @return string
 */
function kc_svg( $name, $label = '' ) {
	$path = get_theme_file_path( 'assets/img/' . sanitize_file_name( $name ) . '.svg' );
	if ( ! is_readable( $path ) ) {
		return '';
	}
	$svg  = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	$attr = $label
		? sprintf( 'role="img" aria-label="%s"', esc_attr( $label ) )
		: 'aria-hidden="true" focusable="false"';
	return preg_replace( '/<svg\b/', '<svg ' . $attr, $svg, 1 );
}
