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

// Pretty permalinks so /projects/ and /#section links work.
update_option( 'permalink_structure', '/%postname%/' );
flush_rewrite_rules();

// Clear WordPress's sample content.
foreach ( get_posts( array( 'post_type' => array( 'post', 'page' ), 'numberposts' => -1, 'post_status' => 'any' ) ) as $kresge_post ) {
	wp_delete_post( $kresge_post->ID, true );
}

foreach ( array( 'Decks', 'Structural', 'Repairs', 'Outdoor living' ) as $kresge_term ) {
	wp_insert_term( $kresge_term, 'project_type' );
}

// Oldest first; the last one is newest and becomes the featured project.
$kresge_projects = array(
	array(
		'title'   => 'Deck post replacement',
		'type'    => 'Repairs',
		'image'   => 'kresge-facebook-1137549951504555.jpg',
		'alt'     => 'New pressure-treated support post installed under an existing second-story deck with iron railings.',
		'excerpt' => 'Swapped a failing support under a second-story deck for a new pressure-treated post, with the deck above left in use.',
	),
	array(
		'title'   => 'Covered porch decking',
		'type'    => 'Decks',
		'image'   => 'kresge-facebook-1137549974837886.jpg',
		'alt'     => 'Covered porch with gray composite decking, a contrasting border board, and white railings with black balusters.',
		'excerpt' => 'Composite boards with a contrasting picture-frame border, white rail with black balusters, and clean ceiling trim.',
	),
	array(
		'title'   => 'Porch roof shoring',
		'type'    => 'Structural',
		'image'   => 'kresge-facebook-1137550184837865.jpg',
		'alt'     => 'Porch roof held up by temporary timber shoring while the deck structure underneath is rebuilt.',
		'excerpt' => 'Temporary shoring carried the roof while we pulled the old supports and framed a new deck underneath.',
	),
	array(
		'title'   => 'Elevated deck & stair rebuild',
		'type'    => 'Outdoor living',
		'image'   => 'kresge-facebook-1088531793073038.jpg',
		'alt'     => 'Rebuilt second-story covered deck with gray composite decking, white railings with black balusters, and a new stair run down to a stamped concrete patio.',
		'excerpt' => 'Rebuilt a second-story covered deck from the posts up: new pressure-treated posts on fresh footings, low-maintenance composite decking with a picture-frame border, and a full stair run down to the patio.',
	),
);

foreach ( $kresge_projects as $kresge_i => $kresge_project ) {
	$kresge_id = wp_insert_post(
		array(
			'post_type'    => 'project',
			'post_status'  => 'publish',
			'post_title'   => $kresge_project['title'],
			'post_excerpt' => $kresge_project['excerpt'],
			'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $kresge_project['excerpt'] ) . '</p><!-- /wp:paragraph -->',
			'post_date'    => gmdate( 'Y-m-d H:i:s', time() - ( count( $kresge_projects ) - $kresge_i ) * WEEK_IN_SECONDS ),
		)
	);
	set_post_thumbnail( $kresge_id, kresge_seed_image( $kresge_project['image'], $kresge_project['alt'] ) );
	wp_set_object_terms( $kresge_id, $kresge_project['type'], 'project_type' );
}

update_option(
	'kresge_business',
	array(
		'phone'        => '(555) 123-4567',
		'email'        => 'hello@kresgecarpentry.com',
		'service_area' => 'Local towns within ~30 mi',
	)
);

// Homepage: the theme's section patterns, expanded to plain blocks. (A
// pattern reference would be tagged with its pattern name in the editor, and
// core then forces content-only editing on it, overriding the theme's locks.)
$kresge_home = wp_insert_post(
	array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => 'Home',
		'post_content' => implode(
			"\n\n",
			array_map(
				fn( $slug ) => WP_Block_Patterns_Registry::get_instance()->get_registered( 'kresge/' . $slug )['content'],
				array( 'hero', 'trust-strip', 'services', 'work', 'process', 'testimonial', 'faq', 'contact' )
			)
		),
	)
);
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $kresge_home );
