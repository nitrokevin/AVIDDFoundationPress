<?php 
add_action( 'init', 'register_project_cpt' );
add_action( 'init', 'register_project_taxonomies' );
function register_project_cpt() {

  $labels = [
    'name'               => 'Projects',
    'singular_name'      => 'Project',
    'menu_name'          => 'Projects',
    'name_admin_bar'     => 'Project',
    'add_new'            => 'Add New',
    'add_new_item'       => 'Add New Project',
    'edit_item'          => 'Edit Project',
    'new_item'           => 'New Project',
    'view_item'          => 'View Project',
    'view_items'         => 'View Projects',
    'search_items'       => 'Search Projects',
    'not_found'          => 'No products found',
    'not_found_in_trash' => 'No products found in Trash',
    'all_items'          => 'All Projects',
  ];

  $args = [
    'labels'             => $labels,
    'public'             => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'show_in_rest'       => true, // Gutenberg + ACF
    'has_archive'        => true,
    'rewrite'            => [
      'slug' => 'project'
    ],
    'menu_icon'          => 'dashicons-clipboard', 
    'supports'           => [
      'title',
      'editor',
      'thumbnail',
      'excerpt'
    ],
  ];

  register_post_type( 'project', $args );
}


function register_project_taxonomies() {

  $labels = [
    'name'              => 'Project Categories',
    'singular_name'     => 'Project Category',
    'search_items'      => 'Search Categories',
    'all_items'         => 'All Categories',
    'parent_item'       => 'Parent Category',
    'parent_item_colon' => 'Parent Category:',
    'edit_item'         => 'Edit Category',
    'update_item'       => 'Update Category',
    'add_new_item'      => 'Add New Category',
    'new_item_name'     => 'New Category Name',
    'menu_name'         => 'Categories',
  ];

  register_taxonomy( 'project_category', ['project'], [
    'hierarchical'      => true, // like categories
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'show_in_rest'      => true,
    'rewrite'           => [
      'slug' => 'project-category'
    ],
  ]);
}
?>