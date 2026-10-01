<?php
/**
 * Business facts: the single source of truth.
 *
 * Hours, address, menu and FAQ are used in three places: the visible block
 * patterns, the schema.org JSON-LD, and /llms.txt. Keeping them here means
 * the page, the search-engine data and the AI-readable summary can never
 * disagree. Swapping this file (plus theme.json colors/fonts) is most of
 * the work of re-skinning the theme for the next client.
 *
 * Kiln & Crumb is a FICTIONAL business. The phone number uses the 555-01xx
 * range reserved for fiction.
 *
 * @package kiln-crumb
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return every business fact the theme renders or publishes.
 *
 * Filterable so a child theme or site plugin can override facts without
 * editing this file.
 *
 * @return array<string, mixed>
 */
function kc_business() {
	static $data = null;
	if ( null !== $data ) {
		return $data;
	}

	$data = array(
		'name'        => 'Kiln & Crumb Bakery',
		'short_name'  => 'Kiln & Crumb',
		'tagline'     => 'Naturally leavened bread, baked before sunrise.',
		'description' => 'Neighborhood bakery in Riverton, VA: long-fermented sourdough, laminated pastry and seasonal galettes. Walk in for the morning bake or preorder for pickup.',
		'phone'       => '+1-703-555-0142',
		'phone_label' => '(703) 555-0142',
		'email'       => 'hello@kilnandcrumb.example',
		'price_range' => '$',
		'address'     => array(
			'street'   => '214 Mill Street',
			'locality' => 'Riverton',
			'region'   => 'VA',
			'postal'   => '22651',
			'country'  => 'US',
		),
		'geo'         => array( 'lat' => 38.9551, 'lng' => -78.2092 ),
		'map_url'     => 'https://www.openstreetmap.org/?mlat=38.9551&mlon=-78.2092#map=17/38.9551/-78.2092',

		// Days use schema.org names so the same data feeds openingHoursSpecification.
		'hours'       => array(
			array( 'label' => 'Tuesday – Friday', 'days' => array( 'Tuesday', 'Wednesday', 'Thursday', 'Friday' ), 'opens' => '07:00', 'closes' => '15:00' ),
			array( 'label' => 'Saturday – Sunday', 'days' => array( 'Saturday', 'Sunday' ), 'opens' => '08:00', 'closes' => '14:00' ),
			array( 'label' => 'Monday', 'days' => array( 'Monday' ), 'opens' => null, 'closes' => null ),
		),

		'menu'        => array(
			array( 'name' => 'Country Sourdough', 'note' => '36-hour ferment, crackling crust, open crumb.', 'price' => '9.00', 'tags' => array( 'Vegan' ) ),
			array( 'name' => 'Seeded Rye', 'note' => 'Dense Nordic-style loaf with sunflower, flax and caraway.', 'price' => '10.00', 'tags' => array( 'Vegan' ) ),
			array( 'name' => 'Butter Croissant', 'note' => 'Three days of lamination with cultured butter.', 'price' => '4.25', 'tags' => array() ),
			array( 'name' => 'Cardamom Knot', 'note' => 'Swedish-style, brushed with syrup and pearl sugar.', 'price' => '4.75', 'tags' => array() ),
			array( 'name' => 'Olive & Rosemary Focaccia', 'note' => 'Sold by the slab; dimpled, oily, very good.', 'price' => '6.00', 'tags' => array( 'Vegan' ) ),
			array( 'name' => 'Seasonal Fruit Galette', 'note' => 'Whatever the orchard sent this week.', 'price' => '5.50', 'tags' => array() ),
		),

		'faq'         => array(
			array(
				'q' => 'Can I preorder bread?',
				'a' => 'Yes. Call or email by 2 p.m. the day before and we will hold your order at the counter until noon.',
			),
			array(
				'q' => 'What time does bread sell out?',
				'a' => 'On weekends the sourdough is usually gone by 11 a.m. Weekday mornings are calmer; preordering guarantees a loaf.',
			),
			array(
				'q' => 'Do you have gluten-free options?',
				'a' => 'No. Everything is baked in one room full of wheat flour, so we cannot promise anything is safe for celiac guests.',
			),
			array(
				'q' => 'Do you supply restaurants and cafés?',
				'a' => 'We bake wholesale bread for a small number of local kitchens, delivered Tuesday to Saturday. Email us for the wholesale list.',
			),
			array(
				'q' => 'Is there parking?',
				'a' => 'Free street parking on Mill Street and a public lot one block east on Second Avenue.',
			),
		),
	);

	/**
	 * Filter the business facts.
	 *
	 * @param array $data Business facts.
	 */
	$data = apply_filters( 'kc_business', $data );
	return $data;
}

/**
 * Format "07:00" as "7 a.m." (AP style, which reads well aloud to screen readers).
 *
 * @param string $time 24-hour HH:MM.
 * @return string
 */
function kc_format_time( $time ) {
	list( $h, $m ) = array_map( 'intval', explode( ':', $time ) );
	$suffix        = $h >= 12 ? 'p.m.' : 'a.m.';
	$h12           = $h % 12 ? $h % 12 : 12;
	return $m ? sprintf( '%d:%02d %s', $h12, $m, $suffix ) : sprintf( '%d %s', $h12, $suffix );
}

/**
 * One-line postal address.
 *
 * @return string
 */
function kc_address_line() {
	$a = kc_business()['address'];
	return sprintf( '%s, %s, %s %s', $a['street'], $a['locality'], $a['region'], $a['postal'] );
}
