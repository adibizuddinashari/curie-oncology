<?php
/**
 * Fallback template. Every real page has its own template (front-page.php,
 * page-*.php, single-doctor.php), so anything landing here is a stray URL:
 * send it to the homepage rather than render an unstyled WordPress loop.
 */
wp_safe_redirect( home_url( '/' ), 301 );
exit;
