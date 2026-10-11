<?php
/**
 * Plugin Name: Kresge Core
 * Description: Projects portfolio and business details for the Kresge Carpentry site. Keeps your content when the theme changes.
 * Version: 0.1.0
 * Requires at least: 6.7
 * Requires PHP: 8.0
 * Author: Matt Wren
 * License: GPLv2 or later
 * Text Domain: kresge-core
 *
 * @package kresge-core
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/inc/post-types.php';
require_once __DIR__ . '/inc/business-details.php';
require_once __DIR__ . '/inc/fluent-forms-fallback.php';

// /projects/ URLs need fresh rewrite rules whenever the post type appears or goes.
register_activation_hook(
	__FILE__,
	function () {
		kresge_register_post_types();
		flush_rewrite_rules();
	}
);
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
