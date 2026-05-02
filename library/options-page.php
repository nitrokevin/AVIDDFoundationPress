<?php

/**
 * Site Settings — ACF Options Pages
 *
 * Registers a top-level "Site Settings" menu with sub-pages.
 * All fields use get_field( 'field_name', 'option' ) to retrieve values.
 *
 * Sub-pages:
 *  - Header & Branding
 *  - Footer Content
 *  - Company Information
 *  - Hero Settings
 *  - Product settings
 *  - Notifications
 */

if (! function_exists('acf_add_options_page')) {
	return;
}

// ============================================
// REGISTER OPTION PAGES
// ============================================

acf_add_options_page(array(
	'page_title' => __('Site Settings', 'avidd'),
	'menu_title' => __('Site Settings', 'avidd'),
	'menu_slug'  => 'site-settings',
	'capability' => 'manage_options',
	'icon_url'   => 'dashicons-admin-settings',
	'position'   => 60,
	'redirect'   => true,
));

acf_add_options_sub_page(array(
	'page_title'  => __('Header & Branding', 'avidd'),
	'menu_title'  => __('Header & Branding', 'avidd'),
	'menu_slug'   => 'site-settings-header',
	'parent_slug' => 'site-settings',
	'capability'  => 'manage_options',
));

acf_add_options_sub_page(array(
	'page_title'  => __('Footer Content', 'avidd'),
	'menu_title'  => __('Footer Content', 'avidd'),
	'menu_slug'   => 'site-settings-footer',
	'parent_slug' => 'site-settings',
	'capability'  => 'manage_options',
));

acf_add_options_sub_page(array(
	'page_title'  => __('Company Information', 'avidd'),
	'menu_title'  => __('Company Info', 'avidd'),
	'menu_slug'   => 'site-settings-company',
	'parent_slug' => 'site-settings',
	'capability'  => 'manage_options',
));

acf_add_options_sub_page(array(
	'page_title'  => __('Hero Settings', 'avidd'),
	'menu_title'  => __('Hero Settings', 'avidd'),
	'menu_slug'   => 'site-settings-hero',
	'parent_slug' => 'site-settings',
	'capability'  => 'manage_options',
));

acf_add_options_sub_page(array(
	'page_title'  => __('Product', 'avidd'),
	'menu_title'  => __('Product', 'avidd'),
	'menu_slug'   => 'site-settings-product',
	'parent_slug' => 'site-settings',
	'capability'  => 'manage_options',
));

acf_add_options_sub_page(array(
	'page_title'  => __('Notifications', 'avidd'),
	'menu_title'  => __('Notifications', 'avidd'),
	'menu_slug'   => 'site-settings-notifications',
	'parent_slug' => 'site-settings',
	'capability'  => 'manage_options',
));

acf_add_options_page([
	'page_title' => 'FAQs',
	'menu_title' => 'FAQs',
	'menu_slug' => 'faqs',
	'icon_url'   => 'dashicons-info',
	'capability' => 'manage_options',
	'position'   => 60,
	'redirect' => false,
]);
acf_add_options_page([
	'page_title' => 'People',
	'menu_title' => 'People',
	'menu_slug' => 'people',
	'icon_url'   => 'dashicons-groups',
	'capability' => 'manage_options',
	'position'   => 60,
	'redirect' => false,
]);


// ============================================
// REGISTER FIELD GROUPS
// ============================================

add_action('acf/init', 'avidd_register_options_field_groups');

function avidd_register_options_field_groups()


{
	if (!function_exists('acf_add_local_field_group') && (!is_admin()) && (wp_is_json_request())) return;

	// ----------------------------------------
	// HEADER & BRANDING
	// ----------------------------------------

	acf_add_local_field_group(array(
		'key'      => 'group_header_branding',
		'title'    => __('Header & Branding', 'avidd'),
		'fields'   => array(

			array(
				'key'           => 'field_header_logo',
				'label'         => __('Header Logo', 'avidd'),
				'name'          => 'header_logo',
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'instructions'  => __('Recommended format: SVG or PNG with transparency.', 'avidd'),
			),

			array(
				'key'           => 'field_contained_header',
				'label'         => __('Contained Header', 'avidd'),
				'name'          => 'contained_header',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __('On', 'avidd'),
				'ui_off_text'   => __('Off', 'avidd'),
				'instructions'  => __('Constrains the header content to the grid max-width.', 'avidd'),
			),

			array(
				'key'           => 'field_sticky_header',
				'label'         => __('Sticky Header', 'avidd'),
				'name'          => 'sticky_header',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __('On', 'avidd'),
				'ui_off_text'   => __('Off', 'avidd'),
			),

			array(
				'key'           => 'field_fixed_header',
				'label'         => __('Sticky header over featured image', 'avidd'),
				'name'          => 'fixed_header',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __('On', 'avidd'),
				'ui_off_text'   => __('Off', 'avidd'),
			),

			array(
				'key'           => 'field_transparent_header',
				'label'         => __('Transparent header over hero section', 'avidd'),
				'name'          => 'transparent_header',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __('On', 'avidd'),
				'ui_off_text'   => __('Off', 'avidd'),
			),

			array(
				'key'           => 'field_dark_mode',
				'label'         => __('Dark Mode', 'avidd'),
				'name'          => 'dark_mode',
				'type'          => 'true_false',
				'default_value' => 0,
				'ui'            => 1,
				'ui_on_text'    => __('On', 'avidd'),
				'ui_off_text'   => __('Off', 'avidd'),
			),

		),
		'location' => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'site-settings-header',
				),
			),
		),
	));

	// ----------------------------------------
	// FOOTER CONTENT
	// ----------------------------------------

	acf_add_local_field_group(array(
		'key'    => 'group_footer_content',
		'title'  => __('Footer Content', 'avidd'),
		'fields' => array(

			array(
				'key'   => 'field_footer_cta_tab',
				'label' => __('Call to Action', 'avidd'),
				'name'  => '',
				'type'  => 'tab',
			),

			array(
				'key'           => 'field_footer_cta_content',
				'label'         => __('CTA Content', 'avidd'),
				'name'          => 'footer_cta_content',
				'type'          => 'wysiwyg',
				'default_value' => '',
			),

			array(
				'key'           => 'field_footer_cta_button_text',
				'label'         => __('CTA Button Text', 'avidd'),
				'name'          => 'footer_cta_button_text',
				'type'          => 'text',
				'default_value' => '',
			),

			array(
				'key'           => 'field_footer_cta_link',
				'label'         => __('CTA Link', 'avidd'),
				'name'          => 'footer_cta_link',
				'type'          => 'url',
				'default_value' => '',
			),

			array(
				'key'   => 'field_footer_images_tab',
				'label' => __('Footer Images', 'avidd'),
				'name'  => '',
				'type'  => 'tab',
			),

			array(
				'key'          => 'field_footer_links',
				'label'        => __('Footer Images', 'avidd'),
				'name'         => 'footer_links',
				'type'         => 'repeater',
				'layout'       => 'block',
				'min'          => 0,
				'max'          => 0,
				'button_label' => __('Add Image', 'avidd'),
				'sub_fields'   => array(
					array(
						'key'             => 'field_footer_links_image',
						'label'           => __('Image', 'avidd'),
						'name'            => 'footer_image',
						'type'            => 'image',
						'return_format'   => 'id',
						'preview_size'    => 'thumbnail',
						'library'         => 'all',
						'parent_repeater' => 'field_footer_links',
					),
					array(
						'key'             => 'field_footer_links_url',
						'label'           => __('Link URL', 'avidd'),
						'name'            => 'link_url',
						'type'            => 'url',
						'parent_repeater' => 'field_footer_links',
					),
				),
			),

		),
		'location' => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'site-settings-footer',
				),
			),
		),
	));

	// ----------------------------------------
	// COMPANY INFORMATION
	// ----------------------------------------

	acf_add_local_field_group(array(
		'key'    => 'group_company_information',
		'title'  => __('Company Information', 'avidd'),
		'fields' => array(

			array(
				'key'   => 'field_contact_tab',
				'label' => __('Contact Details', 'avidd'),
				'name'  => '',
				'type'  => 'tab',
			),

			array(
				'key'           => 'field_contact_phone_number',
				'label'         => __('Phone Number', 'avidd'),
				'name'          => 'contact_phone_number',
				'type'          => 'text',
				'default_value' => '',
			),

			array(
				'key'           => 'field_contact_email',
				'label'         => __('Email Address', 'avidd'),
				'name'          => 'contact_email',
				'type'          => 'email',
				'default_value' => '',
			),

			array(
				'key'           => 'field_contact_address_1',
				'label'         => __('Address Line 1', 'avidd'),
				'name'          => 'contact_address_1',
				'type'          => 'text',
				'default_value' => '',
			),

			array(
				'key'           => 'field_contact_address_2',
				'label'         => __('Address Line 2', 'avidd'),
				'name'          => 'contact_address_2',
				'type'          => 'text',
				'default_value' => '',
			),

			array(
				'key'           => 'field_contact_address_3',
				'label'         => __('Address Line 3', 'avidd'),
				'name'          => 'contact_address_3',
				'type'          => 'text',
				'default_value' => '',
			),

			array(
				'key'           => 'field_contact_address_4',
				'label'         => __('Address Line 4', 'avidd'),
				'name'          => 'contact_address_4',
				'type'          => 'text',
				'default_value' => '',
			),

			array(
				'key'           => 'field_contact_address_5',
				'label'         => __('Address Line 5', 'avidd'),
				'name'          => 'contact_address_5',
				'type'          => 'text',
				'default_value' => '',
			),

			array(
				'key'           => 'field_contact_address_6',
				'label'         => __('Address Line 6', 'avidd'),
				'name'          => 'contact_address_6',
				'type'          => 'text',
				'default_value' => '',
			),

			array(
				'key'           => 'field_footer_company_number',
				'label'         => __('Company Number', 'avidd'),
				'name'          => 'footer_company_number',
				'type'          => 'text',
				'default_value' => '',
			),

			array(
				'key'           => 'field_footer_copyright',
				'label'         => __('Copyright Text', 'avidd'),
				'name'          => 'footer_copyright',
				'type'          => 'text',
				'default_value' => '',
				'instructions'  => __('Use {year} to output the current year dynamically.', 'avidd'),
			),

			array(
				'key'   => 'field_social_tab',
				'label' => __('Social Media', 'avidd'),
				'name'  => '',
				'type'  => 'tab',
			),

			array(
				'key'          => 'field_social_networks',
				'label'        => __('Social Networks', 'avidd'),
				'name'         => 'social_networks',
				'type'         => 'repeater',
				'layout'       => 'table',
				'min'          => 0,
				'max'          => 0,
				'button_label' => __('Add Network', 'avidd'),
				'instructions' => __('Add each social network you want to display, in order.', 'avidd'),
				'sub_fields'   => array(
					array(
						'key'             => 'field_social_network_name',
						'label'           => __('Network', 'avidd'),
						'name'            => 'network',
						'type'            => 'select',
						'choices'         => array(
							'instagram' => 'Instagram',
							'facebook'  => 'Facebook',
							'x'         => 'X (Twitter)',
							'linkedin'  => 'LinkedIn',
							'pinterest' => 'Pinterest',
							'youtube'   => 'YouTube',
							'tiktok'    => 'TikTok',
						),
						'default_value'   => 'instagram',
						'parent_repeater' => 'field_social_networks',
					),
					array(
						'key'             => 'field_social_network_url',
						'label'           => __('URL', 'avidd'),
						'name'            => 'url',
						'type'            => 'url',
						'parent_repeater' => 'field_social_networks',
					),
				),
			),

			array(
				'key'   => 'field_opening_times_tab',
				'label' => __('Opening Times', 'avidd'),
				'name'  => '',
				'type'  => 'tab',
			),

			array(
				'key'          => 'field_opening_times',
				'label'        => __('Opening Hours', 'avidd'),
				'name'         => 'opening_times',
				'type'         => 'repeater',
				'layout'       => 'table',
				'min'          => 0,
				'max'          => 0,
				'button_label' => __('Add Day', 'avidd'),
				'sub_fields'   => array(
					array(
						'key'             => 'field_opening_times_day',
						'label'           => __('Day', 'avidd'),
						'name'            => 'day',
						'type'            => 'text',
						'parent_repeater' => 'field_opening_times',
					),
					array(
						'key'             => 'field_opening_times_open',
						'label'           => __('Opening Time', 'avidd'),
						'name'            => 'opening_time',
						'type'            => 'time_picker',
						'display_format'  => 'g:i a',
						'return_format'   => 'H:i',
						'parent_repeater' => 'field_opening_times',
					),
					array(
						'key'             => 'field_opening_times_close',
						'label'           => __('Closing Time', 'avidd'),
						'name'            => 'closing_time',
						'type'            => 'time_picker',
						'display_format'  => 'g:i a',
						'return_format'   => 'H:i',
						'parent_repeater' => 'field_opening_times',
					),
					array(
						'key'             => 'field_opening_times_note',
						'label'           => __('Note', 'avidd'),
						'name'            => 'note',
						'type'            => 'text',
						'parent_repeater' => 'field_opening_times',
					),
				),
			),

			array(
				'key'          => 'field_special_opening_times',
				'label'        => __('Special Opening Hours', 'avidd'),
				'name'         => 'special_opening_times',
				'type'         => 'repeater',
				'layout'       => 'table',
				'min'          => 0,
				'max'          => 0,
				'button_label' => __('Add Entry', 'avidd'),
				'sub_fields'   => array(
					array(
						'key'             => 'field_special_opening_times_day',
						'label'           => __('Day / Date', 'avidd'),
						'name'            => 'day',
						'type'            => 'text',
						'parent_repeater' => 'field_special_opening_times',
					),
					array(
						'key'             => 'field_special_opening_times_open',
						'label'           => __('Opening Time', 'avidd'),
						'name'            => 'opening_time',
						'type'            => 'time_picker',
						'display_format'  => 'g:i a',
						'return_format'   => 'H:i',
						'parent_repeater' => 'field_special_opening_times',
					),
					array(
						'key'             => 'field_special_opening_times_close',
						'label'           => __('Closing Time', 'avidd'),
						'name'            => 'closing_time',
						'type'            => 'time_picker',
						'display_format'  => 'g:i a',
						'return_format'   => 'H:i',
						'parent_repeater' => 'field_special_opening_times',
					),
					array(
						'key'             => 'field_special_opening_times_note',
						'label'           => __('Note', 'avidd'),
						'name'            => 'note',
						'type'            => 'text',
						'parent_repeater' => 'field_special_opening_times',
					),
				),
			),

		),
		'location' => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'site-settings-company',
				),
			),
		),
	));

	// ----------------------------------------
	// HERO SETTINGS
	// ----------------------------------------

	acf_add_local_field_group(array(
		'key'    => 'group_hero_settings',
		'title'  => __('Hero Settings', 'avidd'),
		'fields' => array(

			array(
				'key'   => 'field_hero_front_tab',
				'label' => __('Front Page Hero', 'avidd'),
				'name'  => '',
				'type'  => 'tab',
			),

			array(
				'key'           => 'field_hero_full_height',
				'label'         => __('Full Height Hero', 'avidd'),
				'name'          => 'hero_full_height',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __('On', 'avidd'),
				'ui_off_text'   => __('Off', 'avidd'),
				'instructions'  => __('When disabled, the hero image is shorter.', 'avidd'),
			),

			array(
				'key'   => 'field_hero_inner_tab',
				'label' => __('Inner Page Hero', 'avidd'),
				'name'  => '',
				'type'  => 'tab',
			),

			array(
				'key'           => 'field_inner_hero_full_height',
				'label'         => __('Full Height Hero', 'avidd'),
				'name'          => 'inner_hero_full_height',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __('On', 'avidd'),
				'ui_off_text'   => __('Off', 'avidd'),
				'instructions'  => __('When disabled, the hero image is shorter.', 'avidd'),
			),

		),
		'location' => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'site-settings-hero',
				),
			),
		),
	));

	// ----------------------------------------
	// PRODUCT
	// ----------------------------------------

	acf_add_local_field_group(array(
		'key'    => 'group_product_filters',
		'title'  => 'Product',
		'fields' => array(
			array(
				'key'   => 'field_product_index_content',
				'label' => 'Product index page content',
				'name'  => 'product_index_content',
				'type'  => 'wysiwyg',
			),
			array(
				'key'           => 'field_product_range_filter',
				'label'         => 'Product Range Filter',
				'name'          => 'product_range_filter',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
			),
			array(
				'key'           => 'field_product_type_filter',
				'label'         => 'Product Type Filter',
				'name'          => 'product_type_filter',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
			),
			array(
				'key'           => 'field_product_application_filter',
				'label'         => 'Product Application Filter',
				'name'          => 'product_application_filter',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'site-settings-product',
				),
			),
		),
	));

	// ----------------------------------------
	// NOTIFICATIONS
	// ----------------------------------------

	acf_add_local_field_group(array(
		'key'    => 'group_notifications',
		'title'  => __('Notifications', 'avidd'),
		'fields' => array(

			array(
				'key'   => 'field_header_notifications_tab',
				'label' => __('Header Notifications', 'avidd'),
				'name'  => '',
				'type'  => 'tab',
			),

			array(
				'key'          => 'field_notifications',
				'label'        => __('Notification Messages', 'avidd'),
				'name'         => 'notifications',
				'type'         => 'repeater',
				'layout'       => 'block',
				'min'          => 0,
				'max'          => 0,
				'button_label' => __('Add Notification', 'avidd'),
				'sub_fields'   => array(
					array(
						'key'             => 'field_notification_header',
						'label'           => __('Header', 'avidd'),
						'name'            => 'notification_header',
						'type'            => 'text',
						'parent_repeater' => 'field_notifications',
					),
					array(
						'key'             => 'field_notification_text',
						'label'           => __('Text', 'avidd'),
						'name'            => 'notification_text',
						'type'            => 'text',
						'parent_repeater' => 'field_notifications',
					),
					array(
						'key'             => 'field_notification_link',
						'label'           => __('Link', 'avidd'),
						'name'            => 'notification_link',
						'type'            => 'url',
						'parent_repeater' => 'field_notifications',
					),
				),
			),

			array(
				'key'   => 'field_email_notifications_tab',
				'label' => __('Email Notifications', 'avidd'),
				'name'  => '',
				'type'  => 'tab',
			),

			array(
				'key'           => 'field_email_logo',
				'label'         => __('Email Logo', 'avidd'),
				'name'          => 'email_logo',
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'instructions'  => __('Used in transactional and notification emails.', 'avidd'),
			),

		),
		'location' => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'site-settings-notifications',
				),
			),
		),
	));



	// ----------------------------------------
	// FAQ
	// ----------------------------------------
	acf_add_local_field_group(array(
			'key' => 'group_options_page_faq',
			'title' => 'FAQ Options Page',
			'fields' => array(

				array(
					'key' => 'field_faq_repeater',
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
							'key' => 'field_faq_header',
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
							'key' => 'field_faq_categories',
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
							'key' => 'field_faq_content',
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


	// ----------------------------------------
	// People
	// ----------------------------------------

	acf_add_local_field_group(array(
			'key' => 'group_options_page_people',
			'title' => 'People',
			'fields' => array(


				array(
					'key' => 'field_people_repeater',
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
							'key' => 'field_people_image',
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
							'key' => 'field_people_name',
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
							'key' => 'field_people_job',
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
							'key' => 'field_people_email',
							'label' => 'Email',
							'name' => 'email',
							'type' => 'email',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),

						),


						array(
							'key' => 'field_people_biography',
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
	
}

// ============================================
// HELPER FUNCTIONS
// ============================================

/**
 * Get a site setting with an optional fallback.
 *
 * Uses get_option() to read the raw stored value directly from wp_options,
 * which lets us distinguish between two cases that get_field() cannot:
 *
 *   - Field never saved → get_option returns null → use $fallback
 *   - true_false saved as off → get_option returns '0' → return false
 *
 * Without this, get_field() returns false for both cases, causing toggles
 * like sticky_header to always appear on (fallback = true wins every time).
 *
 * get_field() is still used for the actual return value so image arrays,
 * formatted dates, select labels etc. all work correctly.
 *
 * @param  string $key      ACF field name.
 * @param  mixed  $fallback Value to return when field has never been saved.
 * @return mixed
 */
if (! function_exists('avidd_get_setting')) {
	function avidd_get_setting(string $key, $fallback = false)
	{
		// ACF stores options page fields as options_{field_name} in wp_options.
		// null = key doesn't exist (never saved). '0'/'1' = explicitly saved value.
		$raw = get_option('options_' . $key, null);

		if ($raw === null) {
			return $fallback;
		}

		// get_field handles formatting: image arrays, time picker strings, etc.
		return get_field($key, 'option');
	}
}

/**
 * Output the copyright text, replacing {year} with the current year.
 *
 * @return string
 */
if (! function_exists('avidd_get_copyright')) {
	function avidd_get_copyright(): string
	{
		$text = avidd_get_setting('footer_copyright', '');
		return esc_html(str_replace('{year}', date('Y'), $text));
	}
}

/**
 * Return an array of active social networks in order, each with:
 *   'network' => string  (slug e.g. 'instagram')
 *   'url'     => string
 *
 * @return array
 */
if (! function_exists('avidd_get_social_networks')) {
	function avidd_get_social_networks(): array
	{
		$rows = avidd_get_setting('social_networks', array());
		if (empty($rows) || ! is_array($rows)) {
			return array();
		}
		return array_filter($rows, fn($row) => ! empty($row['url']));
	}
}
