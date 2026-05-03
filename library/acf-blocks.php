<?php

/**
 * ACF Block Registration & Field Groups
 *
 * @package FoundationPress
 */

// =============================================================================
// BLOCK CATEGORY
// =============================================================================

add_filter('block_categories_all', 'avidd_block_categories', 99);
/**
 * Prepend a custom AVIDD block category to the editor block inserter.
 *
 * @param  array $categories Existing block categories.
 * @return array
 */
function avidd_block_categories($categories)
{
	return array_merge(
		array(
			array(
				'slug'  => 'avidd-blocks',
				'title' => 'AVIDD',
			),
		),
		$categories
	);
}

// =============================================================================
// BLOCK TYPE REGISTRATION
// =============================================================================

add_action('init', 'avidd_register_blocks');
/**
 * Register ACF block types from block.json files.
 */
function avidd_register_blocks()
{
	if (! function_exists('register_block_type')) {
		return;
	}

	$blocks = array(
		'acf-accordion',
		'acf-carousel',
		'acf-tab',
		'acf-global-content-selector',
	);

	foreach ($blocks as $block) {
		$path = __DIR__ . '/' . $block . '/block.json';
		if (file_exists($path)) {
			register_block_type($path);
		}
	}
}

// =============================================================================
// ACF FIELD GROUPS
// =============================================================================

add_action('acf/init', 'avidd_register_field_groups');
/**
 * Register all ACF local field groups.
 */
function avidd_register_field_groups()
{
	// Bail if ACF not present
	if (!function_exists('acf_add_local_field_group')) return;

	avidd_field_group_accordion();
	avidd_field_group_tab();
	avidd_field_group_carousel();
	avidd_field_group_global_content_selector();
}

// -----------------------------------------------------------------------------
// Accordion
// -----------------------------------------------------------------------------

function avidd_field_group_accordion()
{
	acf_add_local_field_group(array(
		'key'                   => 'group_accordion',
		'title'                 => 'Block: Accordion',
		'fields'                => array(
			array(
				'key'               => 'field_accordion_type',
				'label'             => 'Accordion type',
				'name'              => 'accordion_type',
				'type'              => 'select',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array('width' => '', 'class' => '', 'id' => ''),
				'choices'           => array(
					'custom' => 'Custom',
					'faq'    => 'FAQs',
				),
				'default_value'     => false,
				'allow_null'        => 0,
				'multiple'          => 0,
				'ui'                => 0,
				'return_format'     => 'value',
				'ajax'              => 0,
				'placeholder'       => '',
			),
			array(
				'key'               => 'field_repeater_content_accordion',
				'label'             => 'Accordion Content',
				'name'              => 'repeater_content_accordion',
				'type'              => 'repeater',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_accordion_type',
							'operator' => '==',
							'value'    => 'custom',
						),
					),
				),
				'wrapper'           => array('width' => '', 'class' => '', 'id' => ''),
				'collapsed'         => 'field_accordion_heading',
				'min'               => 1,
				'max'               => 0,
				'layout'            => 'block',
				'button_label'      => 'Add Accordion Row',
				'sub_fields'        => array(
					array(
						'key'               => 'field_accordion_heading',
						'label'             => 'Accordion Heading',
						'name'              => 'accordion_heading',
						'type'              => 'text',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array('width' => '', 'class' => '', 'id' => ''),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
					),
					array(
						'key'               => 'field_accordion_heading_background_color',
						'label'             => 'Accordion Heading Background Colour',
						'name'              => 'accordion_heading_background_color',
						'type'              => 'theme_swatch',
						'allow_null'        => 1,
						'include_colors'    => true,
						'include_gradients' => true,
						'gutenberg_classes' => true,
						'default_value'     => '',
						'layout'            => 'horizontal',
						'other_choice'      => 0,
						'save_other_choice' => 0,
					),
					array(
						'key'               => 'field_accordion_content',
						'label'             => 'Accordion Content',
						'name'              => 'accordion_content',
						'type'              => 'wysiwyg',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array('width' => '', 'class' => '', 'id' => ''),
						'default_value'     => '',
						'tabs'              => 'all',
						'toolbar'           => 'full',
						'media_upload'      => 1,
						'delay'             => 0,
					),
				),
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/accordion',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'seamless',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => '',
		'show_in_rest'          => 0,
	));
}

// -----------------------------------------------------------------------------
// Tab
// -----------------------------------------------------------------------------

function avidd_field_group_tab()
{
	acf_add_local_field_group(array(
		'key'                   => 'group_tab',
		'title'                 => 'Block: Tab',
		'fields'                => array(
			array(
				'key'               => 'field_tab_bar_background_color',
				'label'             => 'Tab Bar Background Colour',
				'name'              => 'tab_bar_background_color',
				'type'              => 'theme_swatch',
				'allow_null'        => 1,
				'include_colors'    => true,
				'include_gradients' => true,
				'gutenberg_classes' => true,
				'default_value'     => '',
				'layout'            => 'horizontal',
				'other_choice'      => 0,
				'save_other_choice' => 0,
			),
			array(
				'key'               => 'field_repeater_content_tab',
				'label'             => 'Tab Content',
				'name'              => 'repeater_content_tab',
				'type'              => 'repeater',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array('width' => '', 'class' => '', 'id' => ''),
				'collapsed'         => 'field_tab_heading',
				'min'               => 1,
				'max'               => 0,
				'layout'            => 'block',
				'button_label'      => 'Add Tab',
				'sub_fields'        => array(
					array(
						'key'               => 'field_tab_heading',
						'label'             => 'Tab Heading',
						'name'              => 'tab_heading',
						'type'              => 'text',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array('width' => '', 'class' => '', 'id' => ''),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
					),
					array(
						'key'               => 'field_tab_background_color',
						'label'             => 'Tab Background Colour',
						'name'              => 'tab_background_color',
						'type'              => 'theme_swatch',
						'allow_null'        => 1,
						'include_colors'    => true,
						'include_gradients' => true,
						'gutenberg_classes' => true,
						'default_value'     => '',
						'layout'            => 'horizontal',
						'other_choice'      => 0,
						'save_other_choice' => 0,
					),
					array(
						'key'               => 'field_tab_content',
						'label'             => 'Tab Content',
						'name'              => 'tab_content',
						'type'              => 'wysiwyg',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array('width' => '', 'class' => '', 'id' => ''),
						'default_value'     => '',
						'tabs'              => 'all',
						'toolbar'           => 'full',
						'media_upload'      => 1,
						'delay'             => 0,
					),
				),
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/tab',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'seamless',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => '',
		'show_in_rest'          => 0,
	));
}

// -----------------------------------------------------------------------------
// Carousel
// -----------------------------------------------------------------------------

function avidd_field_group_carousel()
{
	acf_add_local_field_group(array(
		'key'                   => 'group_carosel',
		'title'                 => 'Block: Carousel',
		'fields'                => array(
			array(
				'key'               => 'field_carousel_type',
				'label'             => 'Carousel Type',
				'name'              => 'carousel_type',
				'type'              => 'select',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array('width' => '100', 'class' => '', 'id' => ''),
				'choices'           => array(
					'slide-carousel'  => 'Slide Carousel',
					'gallery-carousel' => 'Gallery Carousel',
				),
				'default_value'     => 'slide-carousel',
				'allow_null'        => 0,
				'multiple'          => 0,
				'ui'                => 1,
				'ajax'              => 0,
				'return_format'     => 'value',
				'placeholder'       => '',
			),
		
			array(
				'key'               => 'field_carousel_gallery',
				'label'             => 'Gallery Carousel',
				'name'              => 'carousel_gallery',
				'type'              => 'gallery',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_carousel_type',
							'operator' => '==',
							'value'    => 'gallery-carousel',
						),
					),
				),
				'wrapper'           => array('width' => '', 'class' => '', 'id' => ''),
				'return_format'     => 'array',
				'preview_size'      => 'thumbnail',
				'insert'            => 'append',
				'library'           => 'all',
			),
			array(
				'key'               => 'field_repeater_content_carousel',
				'label'             => 'Carousel Content',
				'name'              => 'repeater_content_carousel',
				'type'              => 'repeater',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_carousel_type',
							'operator' => '==',
							'value'    => 'slide-carousel',
						),
					),
				),
				'wrapper'           => array('width' => '', 'class' => '', 'id' => ''),
				'collapsed'         => 'field_carousel_heading',
				'min'               => 1,
				'max'               => 0,
				'layout'            => 'block',
				'button_label'      => 'Add Slide',
				'sub_fields'        => array(
					array(
						'key'               => 'field_carousel_image',
						'label'             => 'Carousel Image',
						'name'              => 'carousel_image',
						'type'              => 'image',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array('width' => '', 'class' => '', 'id' => ''),
						'return_format'     => 'array',
						'preview_size'      => 'thumbnail',
						'library'           => 'all',
						'min_width'         => '',
						'min_height'        => '',
						'min_size'          => '',
						'max_width'         => '',
						'max_height'        => '',
						'max_size'          => '',
						'mime_types' => 'jpg, webp, avif',
					),
					array(
						'key'               => 'field_background_image',
						'label'             => 'Background Image',
						'name'              => 'background_image',
						'type'              => 'true_false',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array('width' => '', 'class' => '', 'id' => ''),
						'message'           => '',
						'ui'                => 1,
						'ui_on_text'        => '',
						'ui_off_text'       => '',
					),
					array(
						'key'               => 'field_carousel_heading',
						'label'             => 'Carousel Heading',
						'name'              => 'carousel_heading',
						'type'              => 'text',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array('width' => '', 'class' => '', 'id' => ''),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
					),
					array(
						'key'               => 'field_carousel_background_color',
						'label'             => 'Carousel Background Colour',
						'name'              => 'carousel_background_color',
						'type'              => 'theme_swatch',
						'allow_null'        => 1,
						'include_colors'    => true,
						'include_gradients' => true,
						'gutenberg_classes' => true,
						'default_value'     => '',
						'layout'            => 'horizontal',
						'other_choice'      => 0,
						'save_other_choice' => 0,
					),
					array(
						'key'               => 'field_carousel_content',
						'label'             => 'Carousel Content',
						'name'              => 'carousel_content',
						'type'              => 'wysiwyg',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array('width' => '', 'class' => '', 'id' => ''),
						'default_value'     => '',
						'tabs'              => 'all',
						'toolbar'           => 'full',
						'media_upload'      => 1,
						'delay'             => 0,
					),
				),
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/carousel',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'seamless',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => '',
		'show_in_rest'          => 0,
	));
}

// -----------------------------------------------------------------------------
// Global Content Selector
// -----------------------------------------------------------------------------

function avidd_field_group_global_content_selector()
{
	acf_add_local_field_group(array(
		'key'                   => 'group_global_content_selector',
		'title'                 => 'Block: Global Content Selector',
		'fields'                => array(
			array(
				'key'               => 'field_global_content_source',
				'label'             => 'Options Page Source',
				'name'              => 'options_page_selector',
				'type'              => 'select',
				'instructions'      => 'Select which global options page to pull data from.',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array('width' => '', 'class' => '', 'id' => ''),
				'choices'           => array(),
				'allow_null'        => 0,
				'multiple'          => 0,
				'ui'                => 1,
				'ajax'              => 0,
				'return_format'     => 'value',
				'placeholder'       => 'Select an options page',
			),
			array(
				'key'               => 'field_layout_selector',
				'label'             => 'Layout Style',
				'name'              => 'layout_style',
				'type'              => 'select',
				'instructions'      => 'Choose how the content should be displayed.',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array('width' => '', 'class' => '', 'id' => ''),
				'choices'           => array(
					'accordion'    => 'Accordion',
					'list'         => 'List',
					'columns'      => 'Columns',
					'vertical-tab' => 'Vertical Tabs',
				),
				'allow_null'        => 0,
				'multiple'          => 0,
				'ui'                => 1,
				'ajax'              => 0,
				'return_format'     => 'value',
				'placeholder'       => 'Select layout style',
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/global-content-selector',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'seamless',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => '',
		'show_in_rest'          => 0,
	));
}
