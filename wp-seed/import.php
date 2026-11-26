<?php
/**
 * One-time content import for a fresh Curie Oncology WordPress install.
 *
 *   wp eval-file wp-seed/import.php
 *
 * Reads wp-seed/content.json (made by `node src/export-content.js`) and
 * creates the pages, Locations, Doctors, Topics and Articles, uploading
 * images from the theme's assets folder into the Media Library.
 * Safe to re-run: existing entries are matched by slug/title and updated.
 */

if ( ! defined( 'WP_CLI' ) ) {
	exit( "Run with: wp eval-file wp-seed/import.php\n" );
}
if ( ! function_exists( 'update_field' ) ) {
	WP_CLI::error( 'Activate the free Secure Custom Fields plugin before importing (wp plugin install secure-custom-fields --activate).' );
}
if ( 'curie' !== get_stylesheet() ) {
	WP_CLI::error( 'Activate the Curie Oncology theme before importing.' );
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$content = json_decode( file_get_contents( __DIR__ . '/content.json' ), true );
if ( ! $content ) {
	WP_CLI::error( 'wp-seed/content.json missing or invalid. Run `node src/export-content.js` first.' );
}

/** Upload a theme asset into the Media Library once; returns the attachment ID. */
function curie_seed_image( $relative ) {
	if ( ! $relative ) {
		return 0;
	}
	$existing = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'meta_key'    => '_curie_seed_source',
			'meta_value'  => $relative,
			'fields'      => 'ids',
			'numberposts' => 1,
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}
	$source = get_template_directory() . '/assets/' . $relative;
	if ( ! file_exists( $source ) ) {
		WP_CLI::warning( "Image not found: $relative" );
		return 0;
	}
	$tmp = wp_tempnam( basename( $source ) );
	copy( $source, $tmp );
	$id = media_handle_sideload( array( 'name' => basename( $source ), 'tmp_name' => $tmp ), 0 );
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( "Upload failed for $relative: " . $id->get_error_message() );
		return 0;
	}
	update_post_meta( $id, '_curie_seed_source', $relative );
	return (int) $id;
}

/** Create or update a post matched by slug. */
function curie_seed_post( $type, $title, $slug, $order, $status = 'publish' ) {
	$existing = get_page_by_path( $slug, OBJECT, $type );
	$data     = array(
		'post_type'   => $type,
		'post_title'  => $title,
		'post_name'   => $slug,
		'post_status' => $status,
		'menu_order'  => (int) $order,
	);
	if ( $existing ) {
		$data['ID'] = $existing->ID;
		return wp_update_post( $data );
	}
	return wp_insert_post( $data );
}

/* ----------------------------------------------------------------- Pages */
$pages = array(
	'home'              => 'Home',
	'about'             => 'About Us',
	'our-doctors'       => 'Our Doctors',
	'oncology-services' => 'Oncology Services',
	'resource-centre'   => 'Resource Centre',
	'contact-us'        => 'Contact Us',
);
$page_ids = array();
foreach ( $pages as $slug => $title ) {
	$page_ids[ $slug ] = curie_seed_post( 'page', $title, $slug, 0 );
}
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page_ids['home'] );
update_option( 'permalink_structure', '/%postname%/' );
update_option( 'blogdescription', 'Spring Will Come' );
update_option( 'timezone_string', 'Asia/Kuala_Lumpur' );
update_option( 'date_format', 'j F Y' );
flush_rewrite_rules();
WP_CLI::log( 'Pages ready.' );

/* ------------------------------------------------------------- Locations */
$location_ids = array();
foreach ( $content['locations'] as $loc ) {
	$id = curie_seed_post( 'location', $loc['title'], sanitize_title( $loc['key'] ), $loc['menu_order'] );
	$location_ids[ $loc['key'] ] = $id;
	$fields = array(
		'show_on_contact' => ! empty( $loc['show_on_contact'] ) ? 1 : 0,
		'eyebrow'         => $loc['eyebrow'] ?? '',
		'footer_label'    => $loc['footer_label'] ?? '',
		'address'         => $loc['address'] ?? '',
		'address_profile' => $loc['address_profile'] ?? '',
		'phone'           => $loc['phone'] ?? '',
		'email'           => $loc['email'] ?? '',
		'hours_heading'   => $loc['hours_heading'] ?? '',
		'hours'           => $loc['hours'] ?? array(),
		'closed_note'     => $loc['closed_note'] ?? '',
		'map_url'         => $loc['map_url'] ?? '',
		'website'         => $loc['website'] ?? '',
		'photo'           => curie_seed_image( $loc['photo'] ?? '' ),
		'photo_position'  => $loc['photo_position'] ?? '50% 50%',
		'photo_alt'       => $loc['photo_alt'] ?? '',
	);
	foreach ( $fields as $name => $value ) {
		update_field( "field_curie_location_$name", $value, $id );
	}
}
WP_CLI::log( count( $location_ids ) . ' locations imported.' );

/* --------------------------------------------------------------- Doctors */
foreach ( $content['doctors'] as $doc ) {
	$id     = curie_seed_post( 'doctor', $doc['title'], $doc['slug'], $doc['menu_order'] );
	$fields = array(
		'role'               => $doc['role'],
		'title_line'         => $doc['title_line'],
		'region'             => $doc['region'],
		'show_on_home'       => $doc['show_on_home'] ? 1 : 0,
		'summary'            => $doc['summary'],
		'highlights'         => array_map( fn( $t ) => array( 'text' => $t ), $doc['highlights'] ),
		'clinic'             => $location_ids[ $doc['clinic'] ] ?? 0,
		'hours_summary'      => $doc['hours_summary'],
		'languages'          => $doc['languages'],
		'schedule'           => $doc['schedule'],
		'schedule_note'      => $doc['schedule_note'],
		'visiting_locations' => array_values( array_filter( array_map( fn( $k ) => $location_ids[ $k ] ?? 0, $doc['visiting_locations'] ) ) ),
		'about'              => $doc['about'],
		'details'            => $doc['details'],
		'education'          => $doc['education'],
		'recognition_title'  => $doc['recognition_title'],
		'recognition'        => $doc['recognition'],
		'photo_portrait'     => curie_seed_image( $doc['photo_portrait'] ),
		'photo_card'         => curie_seed_image( $doc['photo_card'] ),
		'photo_cutout'       => curie_seed_image( $doc['photo_cutout'] ),
		'cutout_flip'        => $doc['cutout_flip'] ? 1 : 0,
		'crop_home'          => $doc['crop_home'],
		'crop_grid'          => $doc['crop_grid'],
		'crop_other'         => $doc['crop_other'],
		'seo_description'    => $doc['seo_description'],
	);
	foreach ( $fields as $name => $value ) {
		update_field( "field_curie_doctor_$name", $value, $id );
	}
	WP_CLI::log( "Doctor: {$doc['title']}" );
}

/* ------------------------------------------------------ Topics + Articles */
$topic_ids = array();
foreach ( $content['topics'] as $topic ) {
	$term = term_exists( $topic['name'], 'topic' );
	if ( ! $term ) {
		$term = wp_insert_term( $topic['name'], 'topic' );
	}
	$topic_ids[ $topic['name'] ] = (int) $term['term_id'];
	update_field( 'field_curie_topic_colour', $topic['colour'], 'term_' . $term['term_id'] );
}

foreach ( $content['articles'] as $article ) {
	$id = curie_seed_post( 'article', $article['title'], sanitize_title( $article['title'] ), $article['menu_order'] );
	wp_set_object_terms( $id, array_map( fn( $n ) => $topic_ids[ $n ], $article['topics'] ), 'topic' );
	update_field( 'field_curie_article_image', curie_seed_image( $article['image'] ), $id );
	update_field( 'field_curie_article_image_position', $article['image_position'] ?? '', $id );
	update_field( 'field_curie_article_excerpt', $article['excerpt'], $id );
	update_field( 'field_curie_article_url', $article['url'], $id );
	update_field( 'field_curie_article_link_label', $article['link_label'], $id );
}
WP_CLI::log( count( $content['articles'] ) . ' articles imported.' );

/* ------------------------------------------------------- Clinic Settings */
update_field( 'field_curie_settings_main_phone', '+60 12 822 3310', 'option' );
update_field( 'field_curie_settings_whatsapp', '60128223310', 'option' );
update_field( 'field_curie_settings_main_email', 'talk2us@curieoncology.com.my', 'option' );

WP_CLI::success( 'Import complete.' );
