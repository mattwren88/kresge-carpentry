<?php
/**
 * Without Fluent Forms the contact section's [fluentform] shortcode would
 * print as raw text; render nothing instead.
 *
 * @package kresge-core
 */

add_action(
	'init',
	function () {
		if ( ! shortcode_exists( 'fluentform' ) ) {
			add_shortcode( 'fluentform', '__return_empty_string' );
		}
	},
	20
);
