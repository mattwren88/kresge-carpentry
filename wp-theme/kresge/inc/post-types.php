<?php
/**
 * Projects portfolio: post type and "Project type" taxonomy.
 *
 * @package kresge
 */

add_action(
	'init',
	function () {
		register_post_type(
			'project',
			array(
				'labels'       => array(
					'name'          => __( 'Projects', 'kresge' ),
					'singular_name' => __( 'Project', 'kresge' ),
					'add_new_item'  => __( 'Add New Project', 'kresge' ),
					'edit_item'     => __( 'Edit Project', 'kresge' ),
					'all_items'     => __( 'All Projects', 'kresge' ),
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
					'name'          => __( 'Project types', 'kresge' ),
					'singular_name' => __( 'Project type', 'kresge' ),
				),
				'hierarchical'      => false,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => array( 'slug' => 'project-type' ),
			)
		);
	}
);
