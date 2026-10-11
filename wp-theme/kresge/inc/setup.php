<?php
/**
 * Theme supports, assets, and plugin fallbacks.
 *
 * @package kresge
 */

const KRESGE_FONTS_URL = 'https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600;700&display=swap';

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'editor-styles' );
		// Editor styles load inside the editor iframe, so fonts go here too.
		add_editor_style( array( KRESGE_FONTS_URL, 'assets/css/theme.css' ) );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'kresge-fonts', KRESGE_FONTS_URL, array(), null );
		wp_enqueue_style( 'kresge-theme', get_theme_file_uri( 'assets/css/theme.css' ), array(), (string) filemtime( get_theme_file_path( 'assets/css/theme.css' ) ) );
	}
);
