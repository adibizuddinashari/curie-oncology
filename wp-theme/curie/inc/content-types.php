<?php
/**
 * Content the clinic edits: Doctors, Locations, Articles (+ Topics).
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'doctor',
			array(
				'labels'        => array(
					'name'               => 'Doctors',
					'singular_name'      => 'Doctor',
					'add_new'            => 'Add doctor',
					'add_new_item'       => 'Add a new doctor',
					'edit_item'          => 'Edit doctor',
					'new_item'           => 'New doctor',
					'view_item'          => 'View profile',
					'search_items'       => 'Search doctors',
					'not_found'          => 'No doctors found',
					'not_found_in_trash' => 'No doctors in Trash',
					'all_items'          => 'All doctors',
				),
				'public'        => true,
				'has_archive'   => false, // the listing is the "Our Doctors" page
				'rewrite'       => array( 'slug' => 'our-doctors', 'with_front' => false ),
				'menu_position' => 5,
				'menu_icon'     => 'dashicons-id',
				'supports'      => array( 'title', 'page-attributes', 'revisions' ),
				'show_in_rest'  => false, // classic edit screen: fields only, no block editor
			)
		);

		register_post_type(
			'location',
			array(
				'labels'             => array(
					'name'          => 'Locations',
					'singular_name' => 'Location',
					'add_new'       => 'Add location',
					'add_new_item'  => 'Add a new location',
					'edit_item'     => 'Edit location',
					'all_items'     => 'All locations',
				),
				'public'             => false, // shown inside other pages, never on its own URL
				'show_ui'            => true,
				'menu_position'      => 6,
				'menu_icon'          => 'dashicons-location',
				'supports'           => array( 'title', 'page-attributes', 'revisions' ),
				'show_in_rest'       => false,
			)
		);

		register_post_type(
			'article',
			array(
				'labels'        => array(
					'name'          => 'Articles',
					'singular_name' => 'Article',
					'add_new'       => 'Add article',
					'add_new_item'  => 'Add a new article',
					'edit_item'     => 'Edit article',
					'all_items'     => 'All articles',
				),
				'public'        => false, // cards link out (Curie SG) for now
				'show_ui'       => true,
				'menu_position' => 7,
				'menu_icon'     => 'dashicons-book-alt',
				'supports'      => array( 'title', 'page-attributes', 'revisions' ),
				'show_in_rest'  => false,
			)
		);

		register_taxonomy(
			'topic',
			'article',
			array(
				'labels'            => array(
					'name'          => 'Topics',
					'singular_name' => 'Topic',
					'add_new_item'  => 'Add topic',
				),
				'public'            => false,
				'show_ui'           => true,
				'show_admin_column' => true,
				'hierarchical'      => true, // checkbox list on the edit screen
				'show_in_rest'      => false,
			)
		);
	}
);

/* A removed doctor's old profile URL goes to the Our Doctors page, not a 404. */
add_action(
	'template_redirect',
	function () {
		if ( ! is_404() ) {
			return;
		}
		$path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
		if ( preg_match( '#^our-doctors/[^/]+$#', $path ) ) {
			wp_safe_redirect( curie_url( 'doctors' ), 301 );
			exit;
		}
	}
);
