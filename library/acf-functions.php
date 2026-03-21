<?php add_action('acf/init', function() {
    if (!function_exists('acf_add_local_field_group')) return;


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
					'field' => 'field_slider_true_false',
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


}); //END ACF 
