<?php
/**
 * Pattern category for the homepage sections in patterns/.
 *
 * @package kresge
 */

add_action(
	'init',
	function () {
		register_block_pattern_category( 'kresge', array( 'label' => __( 'Kresge sections', 'kresge' ) ) );
	}
);
