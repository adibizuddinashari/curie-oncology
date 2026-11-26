<?php
/**
 * Curie Oncology theme bootstrap.
 *
 * Markup for pages comes from src/ in the project repo (see src/build-theme.js);
 * the files in inc/ and template-parts/ are hand-written and own the dynamic
 * content: Doctors, Locations, Articles and Clinic Settings.
 */

defined( 'ABSPATH' ) || exit;

define( 'CURIE_VERSION', '1.0.0' );
define( 'CURIE_ASSETS', get_template_directory_uri() . '/assets' );

require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/setup.php';
require_once __DIR__ . '/inc/content-types.php';
require_once __DIR__ . '/inc/fields.php';
require_once __DIR__ . '/inc/admin.php';
require_once __DIR__ . '/inc/forms.php';
