<?php

if( function_exists('acf_register_block_type') ) {
add_action('acf/init', 'my_acf_init_block_types');
function my_acf_init_block_types() {

    // Check function exists.
function checkCategoryOrder($categories)
{
    //custom category array
	$temp = array(
        'slug'  => 'avidd-blocks',
        'title' => 'AVIDD'
    );
    //new categories array and adding new custom category at first location
    $newCategories = array();
    $newCategories[0] = $temp;

    //appending original categories in the new array
    foreach ($categories as $category) {
        $newCategories[] = $category;
    }

    //return new categories
    return $newCategories;
}
add_filter( 'block_categories', 'checkCategoryOrder', 99, 1);
  
add_action( 'init', 'register_acf_blocks' );
function register_acf_blocks() {
    register_block_type(  __DIR__ . '/acf-accordion/block.json' );
    register_block_type(  __DIR__ . '/acf-carousel/block.json' );
    register_block_type(  __DIR__ . '/acf-tab/block.json' );
     register_block_type(  __DIR__ . '/acf-global-content-selector/block.json' );


    }
}


//Accordion
acf_add_local_field_group(array(
	'key' => 'group_622b3632877721',
	'title' => 'Block: Accordion',
	'fields' => array(
        array(
            'key' => 'field_626db345738d5',
            'label' => 'Accordion type',
            'name' => 'accordion_type',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'custom' => 'Custom',
                'faq' => 'FAQs',
            ),
            'default_value' => false,
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 0,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => '',
        ),
  
       
        array(
            'key' => 'field_626db2f7738c7',
            'label' => 'Accordion Content',
            'name' => 'repeater_content_accordion',
            'type' => 'repeater',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                array(
                    array(
                        'field' => 'field_626db345738d5',
                        'operator' => '==',
                        'value' => 'custom',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'collapsed' => 'field_626db2f7738cc',
            'min' => 1,
            'max' => 0,
            'layout' => 'block',
            'button_label' => 'Add Accordion Row',
            'sub_fields' => array(
                array(
                    'key' => 'field_626db2f7738cc',
                    'label' => 'Accordion Heading',
                    'name' => 'accordion_heading',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
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
                    'key' => 'field_626db2f7738ca',
                    'label' => 'Accordion Heading Background Colour',
                    'name' => 'accordion_heading_background_color',
                    'type' => 'swatch',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                   'choices' => get_theme_design_choices([
    'include_colors'   => true,
    'include_gradients' => false,
    'key' => 'color', // return HEX as key for editor
]),
                    'allow_null' => 1,
                    'default_value' => '',
                    'layout' => 'horizontal',
                    'return_format' => 'value',
                    'other_choice' => 0,
                    'save_other_choice' => 0,
                ),
                array(
                    'key' => 'field_626db2f7738cd',
                    'label' => 'Accordion Content',
                    'name' => 'accordion_content',
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
				'param' => 'block',
				'operator' => '==',
				'value' => 'acf/accordion',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'seemless',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
));
//Tab
acf_add_local_field_group(array(
	'key' => 'group_622b3632877723',
	'title' => 'Block: Tab',
	'fields' => array(
      
        array(
                'key' => 'field_626da7410655fg',
                'label' => 'Tab Bar Background Colour',
                'name' => 'tab_bar_background_color',
                'type' => 'swatch',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
               'choices' => get_theme_design_choices([
    'include_colors'   => true,
    'include_gradients' => false,
    'key' => 'color', // return HEX as key for editor
]),
                'allow_null' => 1,
                'default_value' => '',
                'layout' => 'horizontal',
                'return_format' => 'value',
                'other_choice' => 0,
                'save_other_choice' => 0,
            ),
            array(
                'key' => 'field_626dcf6a205da',
                'label' => 'Tab Content',
                'name' => 'repeater_content_tab',
                'type' => 'repeater',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'collapsed' => 'field_626dcf6a205db',
                'min' => 1,
                'max' => 0,
                'layout' => 'block',
                'button_label' => 'Add Tab',
                'sub_fields' => array(
                    array(
                        'key' => 'field_626dcf6a205db',
                        'label' => 'Tab Heading',
                        'name' => 'tab_heading',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
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
                        'key' => 'field_626dcf6a205dc',
                        'label' => 'Tab Background Colour',
                        'name' => 'tab_background_color',
                        'type' => 'swatch',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                       'choices' => get_theme_design_choices([
    'include_colors'   => true,
    'include_gradients' => false,
    'key' => 'color', // return HEX as key for editor
]),
                        'allow_null' => 1,
                        'default_value' => '',
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                        'other_choice' => 0,
                        'save_other_choice' => 0,
                    ),
                    array(
                        'key' => 'field_626dd11e205e3',
                        'label' => 'Tab Content',
                        'name' => 'tab_content',
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
				'param' => 'block',
				'operator' => '==',
				'value' => 'acf/tab',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'seemless',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
));
//Carousel
acf_add_local_field_group(array(    
	'key' => 'group_622b36328777a24',
	'title' => 'Block: Carousel',
	'fields' => array(
        array(
    'key' => 'field_5c3812a7a819bf1',
    'label' => 'Carousel Type',
    'name' => 'carousel_type',
    'type' => 'select',
    'instructions' => '',
    'required' => 0,
    'conditional_logic' => 0,
    'wrapper' => array(
        'width' => '100',
        'class' => '',
        'id' => '',
    ),
    'choices' => array(
        'people-carousel' => 'People carousel',
        'slide-carousel' => 'Slide Carousel',
        
   

    ),
    'default_value' => array(
        0 => 'gallery-carousel',
    ),
    'allow_null' => 0,
    'multiple' => 0,
    'ui' => 1,
    'ajax' => 0,
    'return_format' => 'value',
    'placeholder' => '',
    ),

    
      
         array(
            'key' => 'field_5c812a7a8hgh19bf',
            'label' => 'People Group',
            'name' => 'person_group_select',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
        array(
            'field' => 'field_5c3812a7a819bf1',
            'operator' => '==',
            'value' => 'people-carousel',
        ),
    ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'team' => 'Team',
                'board' => 'Board',
                'other' => 'Other',
            ),
            
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 1,
            'ajax' => 0,
            'return_format' => 'value',
            'placeholder' => '',
        ),
       
    array(
    'key' => 'field_625407c6661c0f',
    'label' => 'Gallery Carousel',
    'name' => 'carousel_gallery',
    'type' => 'gallery',
    'instructions' => '',
    'required' => 0,
    'conditional_logic' => array(
        array(
            'field' => 'field_5c3812a7a819bf1',
            'operator' => '==',
            'value' => 'gallery-carousel',
        ),
    ),
    'wrapper' => array(
        'width' => '',
        'class' => '',
        'id' => '',
    ),
    'return_format' => 'array',
    'preview_size' => 'thumbnail',
    'insert' => 'append',
    'library' => 'all',
    ),
    array(
    'key' => 'field_626dd3503e215h',
    'label' => 'Carousel Content',
    'name' => 'repeater_content_carousel',
    'type' => 'repeater',
    'instructions' => '',
    'required' => 0,
    'conditional_logic' => array(
        array(
            array(
                'field' => 'field_5c3812a7a819bf1',
                'operator' => '==',
                'value' => 'slide-carousel',
            ),
        ),
    ),
    'wrapper' => array(
        'width' => '',
        'class' => '',
        'id' => '',
    ),
    'collapsed' => 'field_626dd3503e216',
    'min' => 1,
    'max' => 0,
    'layout' => 'block',
    'button_label' => 'Add Slide',
    'sub_fields' => array(
        array(
            'key' => 'field_626ddp4413e2k19',
            'label' => 'Carousel Image',
            'name' => 'carousel_image',
            'type' => 'image',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'return_format' => 'array',
            'preview_size' => 'thumbnail',
            'library' => 'all',
            'min_width' => '',
            'min_height' => '',
            'min_size' => '',
            'max_width' => '',
            'max_height' => '',
            'max_size' => '',
            'mime_types' => '',
        ),
          array(
            'key' => 'field_background_image',
            'label' => 'Background Image',
            'name' => 'background_image',
            'type' => 'true_false',
            'instructions' => '',
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
            'key' => 'field_626dd35jf03e216',
            'label' => 'Carousel Heading',
            'name' => 'carousel_heading',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
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
            'key' => 'field_626dd3503ell217',
            'label' => 'Carousel Background Colour',
            'name' => 'carousel_background_color',
            'type' => 'swatch',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            // Compose swatch choices with gradients and colors, fallback to black if none found
            'choices' => get_theme_design_choices([
    'include_colors'   => true,
    'include_gradients' => false,
    'for_acf' => true,
    'use_css_value' => true,
]),
            'allow_null' => 1,
            'default_value' => '',
            'layout' => 'horizontal',
            'return_format' => 'value',
            'other_choice' => 0,
            'save_other_choice' => 0,
        ),
        array(
            'key' => 'field_626hedd3503e218',
            'label' => 'Carousel Content',
            'name' => 'carousel_content',
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
                'param' => 'block',
                'operator' => '==',
                'value' => 'acf/carousel',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'seemless',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
    )
);   


// Global Content Selector
acf_add_local_field_group(array(
    'key' => 'group_global_content_selector',
    'title' => 'Block: Global Content Selector',
    'fields' => array(
        array(
            'key' => 'field_global_content_source',
            'label' => 'Options Page Source',
            'name' => 'options_page_selector',
            'type' => 'select',
            'instructions' => 'Select which global options page to pull data from.',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(),
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 1,
            'ajax' => 0,
            'return_format' => 'value',
            'placeholder' => 'Select an options page',
        ),
        array(
    'key' => 'field_layout_selector',
    'label' => 'Layout Style',
    'name' => 'layout_style',
    'type' => 'select',
    'instructions' => 'Choose how the content should be displayed.',
    'required' => 0,
    'conditional_logic' => 0,
    'wrapper' => array(
        'width' => '',
        'class' => '',
        'id' => '',
    ),
    'choices' => array(
        'accordion' => 'Accordion',
        'list'      => 'List',
        'columns'      => 'Columns',
        'vertical-tab' => 'Vertical Tabs',
    ),
    'allow_null' => 0,
    'multiple' => 0,
    'ui' => 1,
    'ajax' => 0,
    'return_format' => 'value',
    'placeholder' => 'Select layout style',
),
    ),
    'location' => array(
        array(
            array(
                'param' => 'block',
                'operator' => '==',
                'value' => 'acf/global-content-selector',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'seamless',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
    'show_in_rest' => 0,
    'description' => '',
));

} ?>