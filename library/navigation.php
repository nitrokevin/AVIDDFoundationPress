<?php

/**
 * Register Menus
 *
 * @link http://codex.wordpress.org/Function_Reference/register_nav_menus#Examples
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */
defined('ABSPATH') || exit;
register_nav_menus(
	array(
		'top-bar-r'  => esc_html__('Right Top Bar', 'foundationpress'),
		'top-bar-l'  => esc_html__('Left Top Bar', 'foundationpress'),
		'footer-nav-l'  => esc_html__('Footer Left', 'foundationpress'),
		'footer-nav-r'  => esc_html__('Footer Right', 'foundationpress'),
		'mobile-nav' => esc_html__('Mobile', 'foundationpress'),
	)
);


/**
 * Desktop navigation - left top bar
 *
 * @link http://codex.wordpress.org/Function_Reference/wp_nav_menu
 */
if (! function_exists('foundationpress_top_bar_l')) {
	function foundationpress_top_bar_l()
	{
		wp_nav_menu(
			array(
				'container'      => false,
				'menu_class'     => 'dropdown menu desktop-menu',
				'items_wrap'     => '<ul id="%1$s" class="%2$s" data-dropdown-menu>%3$s</ul>',
				'theme_location' => 'top-bar-l',
				'depth'          => 3,
				'fallback_cb'    => false,
				'walker'         => new Foundationpress_Top_Bar_Walker(),
			)
		);
	}
}
/**
 * Desktop navigation - right top bar
 *
 * @link http://codex.wordpress.org/Function_Reference/wp_nav_menu
 */
if (! function_exists('foundationpress_top_bar_r')) {
	function foundationpress_top_bar_r()
	{
		wp_nav_menu(
			array(
				'container'      => false,
				'menu_class'     => 'dropdown menu desktop-menu align-right ',
				'items_wrap'     => '<ul id="%1$s" class="%2$s" data-dropdown-menu>%3$s</ul>',
				'theme_location' => 'top-bar-r',
				'depth'          => 3,
				'fallback_cb'    => false,
				'walker'         => new Foundationpress_Top_Bar_Walker(),
			)
		);
	}
}
/**
 * Desktop navigation - left footer
 *
 * @link http://codex.wordpress.org/Function_Reference/wp_nav_menu
 */
if (! function_exists('foundationpress_footer_nav_l')) {
	function foundationpress_footer_nav_l()
	{
		wp_nav_menu(
			array(
				'container'      => false,
				'menu_class'     => 'menu',
				'items_wrap'     => '<ul id="%1$s" class="%2$s footer-left-menu" >%3$s</ul>',
				'theme_location' => 'footer-nav-l',
				'depth'          => 3,
				'fallback_cb'    => false,
				'walker'         => new Foundationpress_Top_Bar_Walker(),
			)
		);
	}
}
/**
 * Desktop navigation - right footer
 *
 * @link http://codex.wordpress.org/Function_Reference/wp_nav_menu
 */
if (! function_exists('foundationpress_footer_nav_r')) {
	function foundationpress_footer_nav_r()
	{
		wp_nav_menu(
			array(
				'container'      => false,
				'menu_class'     => 'menu align-right',
				'items_wrap'     => '<ul id="%1$s" class="%2$s footer-right-menu" >%3$s</ul>',
				'theme_location' => 'footer-nav-r',
				'depth'          => 3,
				'fallback_cb'    => false,
				'walker'         => new Foundationpress_Top_Bar_Walker(),
			)
		);
	}
}


/**
 * Mobile navigation - topbar (default) or offcanvas
 */
if (! function_exists('foundationpress_mobile_nav')) {
	function foundationpress_mobile_nav()
	{
		wp_nav_menu(
			array(
				'container'      => false,                         // Remove nav container
				'menu'           => __('mobile-nav', 'foundationpress'),
				'menu_class'     => 'vertical menu',
				'theme_location' => 'mobile-nav',
				'items_wrap'     => '<ul id="%1$s" class="%2$s" data-accordion-menu data-submenu-toggle="true">%3$s</ul>',
				'fallback_cb'    => false,
				'walker'         => new Foundationpress_Mobile_Walker(),
			)
		);
	}
}



function foundationpress_move_button_classes_to_link($atts, $item, $args)
{
	if (empty($item->classes) || ! is_array($item->classes)) {
		return $atts;
	}

	$button_classes = array(
		'button',
		'primary',
		'secondary',
		'success',
		'alert',
		'warning',
		'hollow',
		'expanded',
		'large',
		'small',
		'tiny',
		'disabled'
	);

	$link_classes = array_intersect($button_classes, $item->classes);

	if (! empty($link_classes)) {
		$existing = isset($atts['class']) ? explode(' ', $atts['class']) : array();
		$atts['class'] = implode(' ', array_unique(array_merge($existing, $link_classes)));
	}

	return $atts;
}
add_filter('nav_menu_link_attributes', 'foundationpress_move_button_classes_to_link', 10, 3);

function foundationpress_clean_menu_item_classes($classes, $item, $args, $depth)
{
	$remove = array(
		'button',
		'primary',
		'secondary',
		'success',
		'alert',
		'warning',
		'hollow',
		'expanded',
		'large',
		'small',
		'tiny',
		'disabled'
	);

	return array_diff($classes, $remove);
}
add_filter('nav_menu_css_class', 'foundationpress_clean_menu_item_classes', 10, 4);