<?php
add_action('init', 'register_project_cpt');
add_action('init', 'register_project_taxonomies');
function register_project_cpt()
{

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
    'not_found'          => __('No projects found', 'foundationpress'),
    'not_found_in_trash' => __('No projects found in Trash', 'foundationpress'),
    'all_items'          => 'All Projects',
  ];

  $args = [
    'labels'             => $labels,
    'public'             => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'show_in_rest'       => true,
    'has_archive'        => true,
    'rewrite'            => [
      'slug' => 'project'
    ],
    'menu_icon'          => 'dashicons-clipboard',
    'supports'           => [
      'title',
      'thumbnail',
      'excerpt',
      'page-attributes'
    ],

    'hierarchical' => true, // enables parent/child
  ];

  register_post_type('project', $args);
}


function register_project_taxonomies()
{

  $taxonomies = [

    'project_type' => [
      'plural'       => 'Project Types',
      'singular'     => 'Project Type',
      'menu_name'    => 'Project Type',
      'slug'         => 'project-type',
      'hierarchical' => true,
    ],



  ];

  foreach ($taxonomies as $taxonomy => $settings) {

    $labels = [
      'name'              => $settings['plural'],
      'singular_name'     => $settings['singular'],
      'search_items'      => 'Search ' . $settings['plural'],
      'all_items'         => 'All ' . $settings['plural'],
      'parent_item'       => 'Parent ' . $settings['singular'],
      'parent_item_colon' => 'Parent ' . $settings['singular'] . ':',
      'edit_item'         => 'Edit ' . $settings['singular'],
      'update_item'       => 'Update ' . $settings['singular'],
      'add_new_item'      => 'Add New ' . $settings['singular'],
      'new_item_name'     => 'New ' . $settings['singular'] . ' Name',
      'menu_name'         => $settings['menu_name'],
    ];

    register_taxonomy($taxonomy, ['project'], [
      'hierarchical'      => $settings['hierarchical'],
      'labels'            => $labels,
      'show_ui'           => true,
      'show_admin_column' => true,
      'show_in_rest'      => true,
      'rewrite'           => [
        'slug' => $settings['slug'],
      ],
    ]);
  }
}
