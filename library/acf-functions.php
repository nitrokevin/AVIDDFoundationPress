<?php if (function_exists('acf_add_local_field_group')) {

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

.acf-theme-swatches {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 4px 0;
}

.acf-theme-swatches .swatch-item {
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}

.acf-theme-swatches .swatch-item input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.acf-theme-swatches .swatch {
    display: block;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid #ddd;
    transition: border-color 0.15s ease;
}

.acf-theme-swatches .swatch--none {
    background: #fff;
    position: relative;
}

.acf-theme-swatches .swatch__cross::before,
.acf-theme-swatches .swatch__cross::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 60%;
    height: 2px;
    background: #cc0000;
    transform: translate(-50%, -50%) rotate(45deg);
}

.acf-theme-swatches .swatch__cross::after {
    transform: translate(-50%, -50%) rotate(-45deg);
}

.acf-theme-swatches input[type="radio"]:checked + .swatch {
    border-color: #640FA1;
    box-shadow: 0 0 0 2px #640FA1;
}

.acf-theme-swatches .swatch-item:hover .swatch {
    border-color: #999;
}
</style>
<?php

}
