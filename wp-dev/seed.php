<?php
/**
 * Seeds a fresh Playground site with the static site's content.
 * Runs from wp-dev/blueprint.json; not part of the shipped theme.
 */

update_option( 'blogname', 'Kresge Carpentry' );
update_option( 'blogdescription', 'Custom carpentry, built to last.' );

/**
 * Copies an image from the mounted static-site images into the media library.
 */
function kresge_seed_image( $file, $alt = '' ) {
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$source = WP_CONTENT_DIR . '/kresge-dev/images/' . $file;
	$upload = wp_upload_bits( $file, null, file_get_contents( $source ) );
	if ( ! empty( $upload['error'] ) ) {
		error_log( 'kresge seed: ' . $upload['error'] );
		return 0;
	}

	$id = wp_insert_attachment(
		array(
			'post_mime_type' => wp_check_filetype( $upload['file'] )['type'],
			'post_title'     => pathinfo( $file, PATHINFO_FILENAME ),
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	if ( $alt ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	}
	return $id;
}

update_option( 'site_logo', kresge_seed_image( 'kresge-logo.jpg', 'Kresge Carpentry' ) );
