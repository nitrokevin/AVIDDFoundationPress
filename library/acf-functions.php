<?php if (function_exists('acf_add_options_page')) {
    acf_add_options_page([
        'page_title' => 'FAQs',
        'menu_title' => 'FAQs',
        'menu_slug' => 'faqs',
        'capability' => 'edit_posts',
        'redirect' => false,
    ]);
    acf_add_options_page([
        'page_title' => 'People',
        'menu_title' => 'People',
        'menu_slug' => 'people',
        'capability' => 'edit_posts',
        'redirect' => false,
    ]);
}


if (function_exists('acf_add_local_field_group')) {

//Page Options
acf_add_local_field_group(array(
	'key' => 'group_5c756aae12c9d',
	'title' => 'Page Options',
	'fields' => array(

			
		array(
			'key' => 'field_slider_true_false',
			'label' => 'Header Slider',
			'name' => 'slider',
			'type' => 'true_false',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'message' => '',
			'ui' => 1,
			'ui_on_text' => '',
			'ui_off_text' => '',
		),
	
		
		
		array(
			'key' => 'field_62540c6661c0f',
			'label' => 'Featured Slider',
			'name' => 'repeater_featured_slider',
			'type' => 'repeater',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(	
				array(
					'field' => 'field_5c812c928y139c1',
					'operator' => '==',
					'value' => '1',
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'collapsed' => '',
			'min' => 1,
			'max' => 0,
			'layout' => 'block',
			'button_label' => 'Add slide',
			'sub_fields' => array(
				array(
					'key' => 'field_media_type',
					'label' => 'Media type',
					'name' => 'media_type',
					'type' => 'radio',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'choices' => array(
						1 => 'Image',
						2 => 'Video',
	
					),
					'allow_null' => 0,
					'other_choice' => 0,
					'default_value' => 1,
					'layout' => 'horizontal',
					'return_format' => 'value',
					'save_other_choice' => 0,
				),
				
				array(
					'key' => 'field_slider_image',
					'label' => 'Slider image',
					'name' => 'slider_image',
					'type' => 'image',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(	
						array(
							'field' => 'field_media_type',
							'operator' => '==',
							'value' => '1',
						),
					),
					'wrapper' => array(
						'width' => '100',
						'class' => '',
						'id' => '',
					),
					'return_format' => 'array',
					'preview_size' => 'thumbnail',
					'library' => 'all',
				),
				array(
					'key' => 'field_slider_video_teaser',
					'label' => 'Slider video Teaser',
					'name' => 'slider_video_teaser',
					'type' => 'file',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(	
						array(
							'field' => 'field_media_type',
							'operator' => '==',
							'value' => '2',
						),
					),
					'wrapper' => array(
						'width' => '100',
						'class' => '',
						'id' => '',
					),
					'return_format' => 'url',
					'library' => 'all',
					'min_size' => '',
					'max_size' => '',
					'mime_types' => '',
				),
				array(
					'key' => 'field_slider_video_full',
					'label' => 'Slider video full',
					'name' => 'slider_video_full',
					'type' => 'file',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(	
						array(
							'field' => 'field_media_type',
							'operator' => '==',
							'value' => '2',
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'return_format' => 'url',
					'library' => 'all',
					'min_size' => '',
					'max_size' => '',
					'mime_types' => '',
				),
				array(
					'key' => 'field_slider_content',
					'label' => 'Slider Content',
					'name' => 'slider_content',
					'type' => 'wysiwyg',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'tabs' => 'visual',
					'toolbar' => 'full',
					'media_upload' => 1,
					'delay' => 0,
				),
			)
		),
		
	),

	'location' => array(	
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'page',
			),
		),
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'post',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'side',
	'style' => 'seamless',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => false,
));


//FAQ OPTIONS PAGE
acf_add_local_field_group(array(
	'key' => 'group_5d54589f208266',
	'title' => 'Options Page',
	'fields' => array(
		
		array(
			'key' => 'field_5c34ede232af66',
			'label' => 'FAQ',
			'name' => 'faq_repeater',
			'type' => 'repeater',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'collapsed' => '',
			'min' => 0,
			'max' => 0,
			'layout' => 'block',
			'button_label' => 'Add Accordion',
			'sub_fields' => array(
				array(
					'key' => 'field_5c34ee0032af76',
					'label' => 'Header',
					'name' => 'header',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '100',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),

				array(
					'key' => 'field_614b0df41e61b6',
					'label' => 'Categories',
					'name' => 'categories',
					'type' => 'taxonomy',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '50',
						'class' => '',
						'id' => '',
					),
					'taxonomy' => 'category',
					'field_type' => 'checkbox',
					'add_term' => 0,
					'save_terms' => 0,
					'load_terms' => 0,
					'return_format' => 'object',
					'multiple' => 0,
					'allow_null' => 0,
				),
				array(
					'key' => 'field_5c34ee0932af86',
					'label' => 'Content',
					'name' => 'content',
					'type' => 'wysiwyg',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'tabs' => 'all',
					'toolbar' => 'full',
					'media_upload' => 1,
					'delay' => 0,
				),
			),
		),
	

),

	'location' => array(
		array(
			array(
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'faqs',
			),
		),
	),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
));

acf_add_local_field_group(array(
		'key' => 'group_5d54589f208269',
		'title' => 'People',
		'fields' => array(
		
	
			array(
				'key' => 'field_5c34ede232af66f1',
				'label' => 'People',
				'name' => 'people_repeater',
				'type' => 'repeater',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'collapsed' => '',
				'min' => 0,
				'max' => 0,
				'layout' => 'block',
				'button_label' => 'Add Person',
				'sub_fields' => array(
				
					array(
						'key' => 'field_5c812c9h28a19c2',
						'label' => 'Image',
						'name' => 'image',
						'type' => 'image',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '50',
							'class' => '',
							'id' => '',
						),
						'return_format' => 'array',
						'preview_size' => 'thumbnail',
						'library' => 'all',
	
					),
					
					array(
						'key' => 'field_5c34ee003g2af746',
						'label' => 'Name',
						'name' => 'name',
						'type' => 'text',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '50',
							'class' => '',
							'id' => '',
						),
					
					),
					array(
						'key' => 'field_5c34ee0032arf746',
						'label' => 'Job Title',
						'name' => 'job',
						'type' => 'text',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '50',
							'class' => '',
							'id' => '',
						),
					
					),
				
					array(
						'key' => 'field_524ee20032arf746',
						'label' => 'Email',
						'name' => 'email',
						'type' => 'text',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '50',
							'class' => '',
							'id' => '',
						),
					
					),
				
				
					array(
						'key' => 'field_5c34ere0932af86',
						'label' => 'Biography',
						'name' => 'biography',
						'type' => 'wysiwyg',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'tabs' => 'all',
						'toolbar' => 'full',
						'media_upload' => 0,
						'delay' => 0,
					),
				),
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'people',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
	));


} //END ACF 


add_action('admin_head', 'gutenberg_sidebar');

function gutenberg_sidebar() {
  echo 
  '<style>
    .edit-post-sidebar {width: 400px;}
  </style>';
}
add_action('acf/input/admin_head', 'my_acf_admin_head5');
function my_acf_admin_head5()
{

	?>
<style type="text/css">
	.acf-editor-wrap iframe {
		min-height: 100px;
		height: 150px !important;
	}

	ul.acf-swatch-list.acf-hl>li {
		margin-right: .1rem;

	}

	ul.acf-swatch-list label {
		font-size: 0;
	}

	ul.acf-swatch-list .swatch-toggle {
		border-radius: 50%;
		border: 1px solid #aaaaaa;
	}
</style>
<?php

}
