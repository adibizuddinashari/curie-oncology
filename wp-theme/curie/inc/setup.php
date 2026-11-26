<?php
/**
 * Theme setup, assets, and removal of WordPress front-end extras so the
 * rendered pages match the hand-built static site exactly.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'script', 'style' ) );

		// Sized for the largest place each photo appears (2x for retina).
		add_image_size( 'curie-card', 640, 800, false );
		add_image_size( 'curie-location', 1200, 1200, false );
		add_image_size( 'curie-article', 800, 800, false );
	}
);

/* Site stylesheet: the same compiled Tailwind file the static site uses. */
add_action(
	'wp_enqueue_scripts',
	function () {
		$css = get_template_directory() . '/assets/css/style.css';
		wp_enqueue_style( 'curie', CURIE_ASSETS . '/css/style.css', array(), file_exists( $css ) ? filemtime( $css ) : CURIE_VERSION );

		// Nothing on the front end uses WordPress's own block or theme styles.
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'core-block-supports' );
	},
	100
);

/* Strip head clutter and scripts the site doesn't use. */
add_action(
	'init',
	function () {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		remove_action( 'wp_head', 'rest_output_link_wp_head' );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'feed_links', 2 );
		remove_action( 'wp_head', 'feed_links_extra', 3 );
		remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
		remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
		add_filter( 'emoji_svg_url', '__return_false' );
	}
);

add_action(
	'wp_footer',
	function () {
		wp_dequeue_script( 'wp-embed' );
	}
);

/* WordPress 6.8+ prints a prefetch script; the static site doesn't use it. */
add_filter( 'wp_speculation_rules_configuration', '__return_null' );

/* Show titles exactly as typed (no automatic curly quotes or dashes). */
add_filter( 'run_wptexturize', '__return_false' );

/* No comments anywhere on this site. */
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 10 );

/* Keep the toolbar off the front end so logged-in previews match what patients see. */
add_filter( 'show_admin_bar', '__return_false' );
