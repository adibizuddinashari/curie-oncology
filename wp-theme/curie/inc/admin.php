<?php
/**
 * Admin experience: a "Clinic Editor" role for clinic staff, a trimmed menu,
 * and helpful list columns. Developers keep the normal Administrator role.
 */

defined( 'ABSPATH' ) || exit;

// No editing theme/plugin code from the dashboard.
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

/*
 * Clinic Editor: manages Doctors, Locations, Articles, Topics, media and
 * Clinic Settings. Cannot touch pages, plugins, themes, users or settings.
 * (The three content types use the standard "post" capabilities; the blog
 * "Posts" menu itself is hidden below because the site has no blog.)
 */
define( 'CURIE_ROLE_VERSION', 1 );

add_action(
	'init',
	function () {
		if ( (int) get_option( 'curie_role_version' ) === CURIE_ROLE_VERSION ) {
			return;
		}
		remove_role( 'clinic_editor' );
		add_role(
			'clinic_editor',
			'Clinic Editor',
			array(
				'read'                   => true,
				'upload_files'           => true,
				'edit_posts'             => true,
				'edit_others_posts'      => true,
				'edit_published_posts'   => true,
				'publish_posts'          => true,
				'delete_posts'           => true,
				'delete_others_posts'    => true,
				'delete_published_posts' => true,
				'read_private_posts'     => true,
				'manage_categories'      => true, // Topics
			)
		);
		update_option( 'curie_role_version', CURIE_ROLE_VERSION );
	}
);

/* The site has no blog or comments: hide those menus for everyone. */
add_action(
	'admin_menu',
	function () {
		remove_menu_page( 'edit.php' );
		remove_menu_page( 'edit-comments.php' );
		if ( ! current_user_can( 'manage_options' ) ) {
			remove_menu_page( 'tools.php' );
		}
	},
	999
);

/* No comments counter in the top bar. */
add_action(
	'admin_bar_menu',
	function ( $bar ) {
		$bar->remove_node( 'comments' );
	},
	999
);

/* Land clinic staff on the Doctors list instead of the generic dashboard. */
add_filter(
	'login_redirect',
	function ( $redirect, $requested, $user ) {
		if ( $user instanceof WP_User && in_array( 'clinic_editor', (array) $user->roles, true ) ) {
			return admin_url( 'edit.php?post_type=doctor' );
		}
		return $redirect;
	},
	10,
	3
);

/* Doctors list: photo, title and region columns; sorted by the display order. */
add_filter(
	'manage_doctor_posts_columns',
	function ( $columns ) {
		return array(
			'cb'           => $columns['cb'],
			'curie_photo'  => '',
			'title'        => 'Name',
			'curie_role'   => 'Title',
			'curie_region' => 'Region',
			'menu_order'   => 'Order',
		);
	}
);

add_action(
	'manage_doctor_posts_custom_column',
	function ( $column, $post_id ) {
		switch ( $column ) {
			case 'curie_photo':
				$url = curie_image_url( curie_field( 'photo_portrait', $post_id ), 'thumbnail' );
				if ( $url ) {
					printf( '<img src="%s" alt="" style="width:44px;height:44px;border-radius:50%%;object-fit:cover;object-position:top">', esc_url( $url ) );
				}
				break;
			case 'curie_role':
				echo esc_html( curie_field( 'role', $post_id ) );
				break;
			case 'curie_region':
				echo esc_html( curie_region_label( curie_field( 'region', $post_id ) ) );
				break;
			case 'menu_order':
				echo (int) get_post_field( 'menu_order', $post_id );
				break;
		}
	},
	10,
	2
);

add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( is_admin() && $query->is_main_query() && in_array( $query->get( 'post_type' ), array( 'doctor', 'location', 'article' ), true ) && ! $query->get( 'orderby' ) ) {
			$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
		}
	}
);

/* Plain-language placeholder in the title box. */
add_filter(
	'enter_title_here',
	function ( $text, $post ) {
		$placeholders = array(
			'doctor'   => 'Full name, e.g. Dr Tan Ai Lian',
			'location' => 'Location name, e.g. Curie Oncology, Kuala Lumpur',
			'article'  => 'Article title',
		);
		return isset( $placeholders[ $post->post_type ] ) ? $placeholders[ $post->post_type ] : $text;
	},
	10,
	2
);
