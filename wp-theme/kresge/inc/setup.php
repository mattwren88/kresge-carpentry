<?php
/**
 * Theme supports, assets, and plugin fallbacks.
 *
 * @package kresge
 */

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/theme.css' );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'kresge-fonts', 'https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600;700&display=swap', array(), null );
		wp_enqueue_style( 'kresge-theme', get_theme_file_uri( 'assets/css/theme.css' ), array(), (string) filemtime( get_theme_file_path( 'assets/css/theme.css' ) ) );
	}
);

add_action(
	'enqueue_block_editor_assets',
	function () {
		wp_enqueue_style( 'kresge-fonts', 'https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600;700&display=swap', array(), null );
	}
);

// Without Fluent Forms the contact pattern's shortcode would print as raw text.
add_action(
	'init',
	function () {
		if ( ! shortcode_exists( 'fluentform' ) ) {
			add_shortcode( 'fluentform', '__return_empty_string' );
		}
	},
	20
);
