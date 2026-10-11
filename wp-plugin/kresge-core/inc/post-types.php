<?php
/**
 * Projects portfolio: post type and "Project type" taxonomy.
 *
 * @package kresge-core
 */

/**
 * Registers the project post type and project_type taxonomy.
 */
function kresge_register_post_types() {
	register_post_type(
		'project',
		array(
			'labels'       => array(
				'name'          => __( 'Projects', 'kresge-core' ),
				'singular_name' => __( 'Project', 'kresge-core' ),
				'add_new_item'  => __( 'Add New Project', 'kresge-core' ),
				'edit_item'     => __( 'Edit Project', 'kresge-core' ),
				'all_items'     => __( 'All Projects', 'kresge-core' ),
			),
			'public'       => true,
			'has_archive'  => 'projects',
			'rewrite'      => array( 'slug' => 'projects' ),
			'menu_icon'    => 'dashicons-hammer',
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
		)
	);

	register_taxonomy(
		'project_type',
		'project',
		array(
			'labels'            => array(
				'name'          => __( 'Project types', 'kresge-core' ),
				'singular_name' => __( 'Project type', 'kresge-core' ),
			),
			'hierarchical'      => false,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'project-type' ),
		)
	);
}
add_action( 'init', 'kresge_register_post_types' );
