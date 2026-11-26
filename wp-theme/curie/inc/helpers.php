<?php
/**
 * Small helpers shared by templates.
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
 * Routes
 * ------------------------------------------------------------------------- */

/** Fixed page routes. Slugs match the WP pages created on install. */
function curie_url( $key ) {
	$routes = array(
		'home'      => '/',
		'about'     => '/about/',
		'doctors'   => '/our-doctors/',
		'services'  => '/oncology-services/',
		'resources' => '/resource-centre/',
		'contact'   => '/contact-us/',
	);
	return home_url( isset( $routes[ $key ] ) ? $routes[ $key ] : '/' );
}

/** Permalink of a doctor by slug, or the doctors page if they've been removed. */
function curie_doctor_url( $slug ) {
	$doctor = get_page_by_path( $slug, OBJECT, 'doctor' );
	return ( $doctor && 'publish' === $doctor->post_status ) ? get_permalink( $doctor ) : curie_url( 'doctors' );
}

/* ---------------------------------------------------------------------------
 * Page meta (title, description, active nav item)
 * ------------------------------------------------------------------------- */

function curie_set_meta( array $meta ) {
	$GLOBALS['curie_meta'] = $meta;
}

function curie_meta( $key ) {
	$meta = isset( $GLOBALS['curie_meta'] ) ? $GLOBALS['curie_meta'] : array();
	if ( isset( $meta[ $key ] ) ) {
		return $meta[ $key ];
	}
	if ( 'title' === $key ) {
		return wp_get_document_title();
	}
	return '';
}

/** Echoes the active-link class used by the static build for the current nav key. */
function curie_nav_class( $key ) {
	if ( curie_meta( 'nav' ) === $key ) {
		echo '!text-forest';
	}
}

/* ---------------------------------------------------------------------------
 * Field access (works with Secure Custom Fields or ACF, and degrades to
 * plain post meta so the site never fatals if the plugin is deactivated).
 * ------------------------------------------------------------------------- */

function curie_field( $name, $post_id = null ) {
	if ( function_exists( 'get_field' ) ) {
		return get_field( $name, $post_id ? $post_id : false );
	}
	if ( 'option' === $post_id ) {
		return get_option( 'options_' . $name );
	}
	return get_post_meta( $post_id ? $post_id : get_the_ID(), $name, true );
}

function curie_setting( $name, $default = '' ) {
	$value = curie_field( $name, 'option' );
	return ( '' === $value || null === $value || false === $value ) ? $default : $value;
}

/** Image field (array or ID) → URL at a given size. */
function curie_image_url( $image, $size = 'full' ) {
	if ( is_array( $image ) ) {
		return isset( $image['sizes'][ $size ] ) ? $image['sizes'][ $size ] : $image['url'];
	}
	if ( is_numeric( $image ) ) {
		return wp_get_attachment_image_url( (int) $image, $size );
	}
	return is_string( $image ) ? $image : '';
}

/** Splits a textarea into non-empty trimmed lines. */
function curie_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/', (string) $text ) ), 'strlen' ) );
}

/** Splits a textarea into paragraphs on blank lines. */
function curie_paragraphs( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R\s*\R/', (string) $text ) ), 'strlen' ) );
}

/** Escapes profile text but keeps <em>/<strong> (book titles, emphasis). */
function curie_rich( $text ) {
	return wp_kses( (string) $text, array( 'em' => array(), 'strong' => array() ) );
}

/** "+604 371 0000 ext. 42617" → "tel:+6043710000". */
function curie_tel( $display ) {
	$number = preg_split( '/ext/i', (string) $display );
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $number[0] );
}

/* ---------------------------------------------------------------------------
 * Queries
 * ------------------------------------------------------------------------- */

/**
 * Published doctors in the order set in the admin (drag order, then name).
 *
 * @param array $args region (klang-valley|penang), home_only (bool), exclude (post ID).
 */
function curie_doctors( array $args = array() ) {
	$query = array(
		'post_type'      => 'doctor',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		'meta_query'     => array(),
	);
	if ( ! empty( $args['region'] ) ) {
		$query['meta_query'][] = array( 'key' => 'region', 'value' => $args['region'] );
	}
	if ( ! empty( $args['home_only'] ) ) {
		$query['meta_query'][] = array( 'key' => 'show_on_home', 'value' => '1' );
	}
	if ( ! empty( $args['exclude'] ) ) {
		$query['post__not_in'] = array( (int) $args['exclude'] );
	}
	return get_posts( $query );
}

function curie_locations( $contact_only = true ) {
	$query = array(
		'post_type'      => 'location',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
	);
	if ( $contact_only ) {
		$query['meta_query'] = array( array( 'key' => 'show_on_contact', 'value' => '1' ) );
	}
	return get_posts( $query );
}

/* ---------------------------------------------------------------------------
 * Doctors
 * ------------------------------------------------------------------------- */

function curie_region_label( $region ) {
	$labels = array(
		'klang-valley' => 'Klang Valley',
		'penang'       => 'Penang',
	);
	return isset( $labels[ $region ] ) ? $labels[ $region ] : '';
}

/**
 * Inline style for a cropped doctor photo, as used by .doctor-card__img.
 *
 * $context is home | grid | other. A developer crop (imported from the
 * hand-tuned static site) wins; otherwise the client's simple focus/zoom
 * controls are turned into the same left/top/width/height box.
 */
function curie_photo_style( $doctor_id, $context ) {
	$image = 'home' === $context ? null : curie_field( 'photo_card', $doctor_id );
	if ( ! $image ) {
		$image = curie_field( 'photo_portrait', $doctor_id );
	}
	$url      = curie_image_url( $image, 'curie-card' );
	$advanced = trim( (string) curie_field( 'crop_' . $context, $doctor_id ) );

	if ( '' !== $advanced ) {
		$box = $advanced;
	} else {
		$x    = (float) ( curie_field( 'photo_focus_x', $doctor_id ) ?: 50 );
		$y    = (float) ( curie_field( 'photo_focus_y', $doctor_id ) ?: 20 );
		$zoom = max( 100, (float) ( curie_field( 'photo_zoom', $doctor_id ) ?: 100 ) );
		$over = $zoom - 100;
		$box  = sprintf(
			'left:-%1$s%%;top:-%2$s%%;width:%3$s%%;height:%3$s%%;background-position:%4$s%% %5$s%%;background-size:cover;',
			round( $over * $x / 100, 3 ),
			round( $over * $y / 100, 3 ),
			$zoom,
			$x,
			$y
		);
	}

	// Developer crops carry their own background-* values; the URL is always ours.
	return $box . "background-image:url('" . esc_url( $url ) . "');";
}

/** data-* attributes for buttons that open the booking modal pre-filled with this doctor. */
function curie_booking_attrs( $doctor_id ) {
	$doctor = get_post( $doctor_id );
	$meta   = trim( curie_field( 'role', $doctor_id ) . ' · ' . curie_region_label( curie_field( 'region', $doctor_id ) ), ' ·' );
	printf(
		'data-book-doctor="%s" data-doctor-name="%s" data-doctor-meta="%s" data-doctor-photo="%s"',
		esc_attr( $doctor->post_name ),
		esc_attr( get_the_title( $doctor ) ),
		esc_attr( $meta ),
		esc_url( curie_image_url( curie_field( 'photo_portrait', $doctor_id ), 'medium' ) )
	);
}
