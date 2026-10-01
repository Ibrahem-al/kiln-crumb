<?php
/**
 * Search and answer-engine output: meta description, Open Graph,
 * schema.org JSON-LD and /llms.txt.
 *
 * If the client later installs Yoast or Rank Math, the meta/OG output here
 * steps aside automatically (see kc_seo_plugin_active) so tags are never
 * printed twice. The JSON-LD stays, because those plugins do not model
 * bakery hours, menus or FAQs from this theme's data.
 *
 * @package kiln-crumb
 */

defined( 'ABSPATH' ) || exit;

/**
 * True when a full SEO plugin is handling meta tags.
 *
 * @return bool
 */
function kc_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

/**
 * Best description for the current view.
 *
 * @return string
 */
function kc_meta_description() {
	if ( is_front_page() ) {
		return kc_business()['description'];
	}
	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post && has_excerpt( $post ) ) {
			return wp_strip_all_tags( get_the_excerpt( $post ) );
		}
		if ( $post ) {
			$text = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
			$text = trim( preg_replace( '/\s+/', ' ', $text ) );
			if ( $text ) {
				return wp_html_excerpt( $text, 155, '…' );
			}
		}
	}
	return kc_business()['description'];
}

/**
 * Print meta description and Open Graph / Twitter tags.
 */
function kc_print_meta_tags() {
	if ( kc_seo_plugin_active() || is_404() ) {
		return;
	}

	$biz   = kc_business();
	$desc  = kc_meta_description();
	$title = wp_get_document_title();
	$url   = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
	$image = get_theme_file_uri( 'assets/img/og-image.png' );

	printf( "<meta name=\"description\" content=\"%s\">\n", esc_attr( $desc ) );
	printf( "<meta property=\"og:type\" content=\"%s\">\n", is_front_page() ? 'website' : 'article' );
	printf( "<meta property=\"og:site_name\" content=\"%s\">\n", esc_attr( $biz['name'] ) );
	printf( "<meta property=\"og:title\" content=\"%s\">\n", esc_attr( $title ) );
	printf( "<meta property=\"og:description\" content=\"%s\">\n", esc_attr( $desc ) );
	printf( "<meta property=\"og:url\" content=\"%s\">\n", esc_url( $url ) );
	printf( "<meta property=\"og:image\" content=\"%s\">\n", esc_url( $image ) );
	echo "<meta property=\"og:image:width\" content=\"1200\">\n<meta property=\"og:image:height\" content=\"630\">\n";
	echo "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";

	// WordPress core only prints rel=canonical on singular views; cover the front page when it lists posts.
	if ( is_front_page() && ! is_singular() ) {
		printf( "<link rel=\"canonical\" href=\"%s\">\n", esc_url( home_url( '/' ) ) );
	}
}
add_action( 'wp_head', 'kc_print_meta_tags', 1 );

/**
 * Build the schema.org graph for the front page.
 *
 * @return array
 */
function kc_schema_graph() {
	$biz  = kc_business();
	$home = home_url( '/' );

	$hours = array();
	foreach ( $biz['hours'] as $row ) {
		if ( empty( $row['opens'] ) ) {
			continue; // Closed days are simply omitted, per schema.org guidance.
		}
		$hours[] = array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => array_map(
				static function ( $d ) {
					return 'https://schema.org/' . $d;
				},
				$row['days']
			),
			'opens'     => $row['opens'],
			'closes'    => $row['closes'],
		);
	}

	$menu_items = array();
	foreach ( $biz['menu'] as $item ) {
		$menu_items[] = array(
			'@type'       => 'MenuItem',
			'name'        => $item['name'],
			'description' => $item['note'],
			'offers'      => array(
				'@type'         => 'Offer',
				'price'         => $item['price'],
				'priceCurrency' => 'USD',
			),
		);
	}

	$faq = array();
	foreach ( $biz['faq'] as $pair ) {
		$faq[] = array(
			'@type'          => 'Question',
			'name'           => $pair['q'],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $pair['a'],
			),
		);
	}

	return array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			array(
				'@type'     => 'WebSite',
				'@id'       => $home . '#website',
				'url'       => $home,
				'name'      => $biz['name'],
				'publisher' => array( '@id' => $home . '#bakery' ),
			),
			array(
				'@type'                     => 'Bakery',
				'@id'                       => $home . '#bakery',
				'name'                      => $biz['name'],
				'description'               => $biz['description'],
				'url'                       => $home,
				'telephone'                 => $biz['phone'],
				'email'                     => $biz['email'],
				'priceRange'                => $biz['price_range'],
				'image'                     => get_theme_file_uri( 'assets/img/og-image.png' ),
				'address'                   => array(
					'@type'           => 'PostalAddress',
					'streetAddress'   => $biz['address']['street'],
					'addressLocality' => $biz['address']['locality'],
					'addressRegion'   => $biz['address']['region'],
					'postalCode'      => $biz['address']['postal'],
					'addressCountry'  => $biz['address']['country'],
				),
				'geo'                       => array(
					'@type'     => 'GeoCoordinates',
					'latitude'  => $biz['geo']['lat'],
					'longitude' => $biz['geo']['lng'],
				),
				'openingHoursSpecification' => $hours,
				'hasMenu'                   => array(
					'@type'          => 'Menu',
					'name'           => 'Daily bake',
					'hasMenuSection' => array(
						'@type'       => 'MenuSection',
						'name'        => 'Bread and pastry',
						'hasMenuItem' => $menu_items,
					),
				),
			),
			array(
				'@type'      => 'FAQPage',
				'@id'        => $home . '#faq',
				'mainEntity' => $faq,
			),
		),
	);
}

/**
 * Print JSON-LD on the front page.
 */
function kc_print_schema() {
	if ( ! is_front_page() ) {
		return;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( kc_schema_graph(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
}
add_action( 'wp_head', 'kc_print_schema', 20 );

/**
 * Plain-text summary for AI answer engines at /llms.txt (llmstxt.org format).
 *
 * Generated from the same data as the page and the JSON-LD, so hours or
 * prices can never be stale in one place and right in another.
 *
 * @return string
 */
function kc_llms_txt() {
	$biz   = kc_business();
	$lines = array();

	$lines[] = '# ' . $biz['name'];
	$lines[] = '';
	$lines[] = '> ' . $biz['description'];
	$lines[] = '';
	$lines[] = 'Address: ' . kc_address_line();
	$lines[] = 'Phone: ' . $biz['phone_label'];
	$lines[] = 'Email: ' . $biz['email'];
	$lines[] = '';
	$lines[] = '## Hours';
	foreach ( $biz['hours'] as $row ) {
		$lines[] = empty( $row['opens'] )
			? sprintf( '- %s: closed', $row['label'] )
			: sprintf( '- %s: %s to %s', $row['label'], kc_format_time( $row['opens'] ), kc_format_time( $row['closes'] ) );
	}
	$lines[] = '';
	$lines[] = '## Daily bake';
	foreach ( $biz['menu'] as $item ) {
		$tags    = $item['tags'] ? ' (' . implode( ', ', $item['tags'] ) . ')' : '';
		$lines[] = sprintf( '- %s, $%s%s: %s', $item['name'], $item['price'], $tags, $item['note'] );
	}
	$lines[] = '';
	$lines[] = '## Frequently asked questions';
	foreach ( $biz['faq'] as $pair ) {
		$lines[] = '- ' . $pair['q'] . ' ' . $pair['a'];
	}

	$pages = get_pages( array( 'sort_column' => 'menu_order,post_title' ) );
	if ( $pages ) {
		$lines[] = '';
		$lines[] = '## Pages';
		$lines[] = sprintf( '- [Home](%s)', home_url( '/' ) );
		foreach ( $pages as $page ) {
			if ( (int) get_option( 'page_on_front' ) === $page->ID ) {
				continue;
			}
			$lines[] = sprintf( '- [%s](%s)', $page->post_title, get_permalink( $page ) );
		}
	}

	return implode( "\n", $lines ) . "\n";
}

/**
 * Serve /llms.txt before WordPress decides the request is a 404.
 *
 * @param WP $wp Current request.
 */
function kc_maybe_serve_llms_txt( $wp ) {
	$path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$home = trim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
	if ( $home && 0 === strpos( $path, $home . '/' ) ) {
		$path = substr( $path, strlen( $home ) + 1 );
	}
	if ( 'llms.txt' !== $path ) {
		return;
	}
	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'X-Robots-Tag: noindex' );
	echo kc_llms_txt(); // phpcs:ignore WordPress.Security.EscapeOutput -- plain text response.
	exit;
}
add_action( 'parse_request', 'kc_maybe_serve_llms_txt', 0 );
